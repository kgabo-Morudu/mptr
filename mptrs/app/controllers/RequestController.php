<?php
class RequestController extends Controller {
    private Model $db;
    public function __construct() {
        parent::__construct();
        Auth::requireLogin();
        $this->db = new class extends Model {};
    }

    private function generateNumber(string $prefix): string {
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    public function index(): void {
        $role = Auth::role();
        $sql = "SELECT mr.*, a.name AS asset_name, a.asset_code, u.full_name AS requester_name,
                (SELECT COUNT(*) FROM attachments WHERE entity_type='request' AND entity_id=mr.id) AS attach_count
                FROM maintenance_requests mr
                JOIN assets a ON a.id = mr.asset_id
                JOIN users u ON u.id = mr.requester_id";
        $params = [];
        if ($role === 'requester') {
            $sql .= " WHERE mr.requester_id = ?";
            $params[] = Auth::id();
        }
        $sql .= " ORDER BY mr.created_at DESC LIMIT 200";
        $items = $this->db->fetchAll($sql, $params);
        $this->view('requests/index', compact('items','role'));
    }

    public function createForm(): void {
        $assets = $this->db->fetchAll("SELECT id, name, asset_code FROM assets WHERE status='Active' ORDER BY name");
        $this->view('requests/form', compact('assets'));
    }

    public function store(): void {
        $assetId = (int)$this->input('asset_id');
        $title = trim((string)$this->input('title',''));
        $description = trim((string)$this->input('description',''));
        $priority = $this->input('priority','Medium');

        if (!$assetId || !$title || !$description) {
            Session::flash('error','All fields are required');
            $this->redirect('requests-create');
        }

        $reqNum = $this->generateNumber('REQ');
        $this->db->execute(
            "INSERT INTO maintenance_requests (request_number,asset_id,requester_id,title,description,priority)
             VALUES (?,?,?,?,?,?)",
            [$reqNum, $assetId, Auth::id(), $title, $description, $priority]
        );
        $reqId = $this->db->lastInsertId();

        // Handle photo uploads
        if (!empty($_FILES['photos']['name'][0])) {
            foreach ($_FILES['photos']['name'] as $i => $name) {
                if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $file = [
                    'name'     => $name,
                    'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                    'size'     => $_FILES['photos']['size'][$i],
                    'error'    => $_FILES['photos']['error'][$i],
                ];
                try {
                    $path = Uploader::handle($file, 'requests');
                    if ($path) {
                        $this->db->execute(
                            "INSERT INTO attachments (entity_type,entity_id,file_name,file_path,file_type,file_size,uploaded_by)
                             VALUES ('request',?,?,?,?,?,?)",
                            [$reqId, $name, $path, $file['name'] ? mime_content_type(UPLOAD_PATH.$path) : null,
                             $file['size'], Auth::id()]
                        );
                    }
                } catch (Throwable $e) { /* skip bad file */ }
            }
        }

        // Notify managers
        $managers = $this->db->fetchAll(
            "SELECT u.id FROM users u JOIN roles r ON r.id=u.role_id WHERE r.name IN ('manager','admin') AND u.is_active=1"
        );
        foreach ($managers as $m) {
            $this->db->execute(
                "INSERT INTO notifications (user_id,title,message,link) VALUES (?,?,?,?)",
                [$m['id'],'New Maintenance Request',
                 "Request {$reqNum} requires approval",
                 "requests-view&id={$reqId}"]
            );
        }

        Audit::log('request_create','request',$reqId);
        Session::flash('success','Request submitted: '.$reqNum);
        $this->redirect('requests');
    }

    public function viewRequest(): void {
        $id = (int)$this->input('id');
        $req = $this->db->fetchOne(
            "SELECT mr.*, a.name AS asset_name, a.asset_code, a.criticality,
                    u.full_name AS requester_name,
                    au.full_name AS approver_name
             FROM maintenance_requests mr
             JOIN assets a ON a.id = mr.asset_id
             JOIN users u ON u.id = mr.requester_id
             LEFT JOIN users au ON au.id = mr.approved_by
             WHERE mr.id=?", [$id]
        );
        if (!$req) $this->redirect('requests');
        $attachments = $this->db->fetchAll(
            "SELECT * FROM attachments WHERE entity_type='request' AND entity_id=?", [$id]
        );
        $workOrders = $this->db->fetchAll(
            "SELECT wo.*, u.full_name AS technician_name FROM work_orders wo
             LEFT JOIN users u ON u.id = wo.technician_id
             WHERE wo.request_id=?", [$id]
        );
        $this->view('requests/view', compact('req','attachments','workOrders'));
    }

    public function approve(): void {
        Auth::requireRole(['manager','admin']);
        $id = (int)$this->input('id');
        $this->db->execute(
            "UPDATE maintenance_requests SET status='Approved', approved_by=?, approved_at=NOW() WHERE id=?",
            [Auth::id(), $id]
        );
        Audit::log('request_approve','request',$id);
        Session::flash('success','Request approved');
        $this->redirect('requests-view', ['id'=>$id]);
    }
    Auth::verifyCsrf();

    public function reject(): void {
        Auth::requireRole(['manager','admin']);
        $id = (int)$this->input('id');
        $reason = trim((string)$this->input('reason',''));
        $this->db->execute(
            "UPDATE maintenance_requests SET status='Rejected', approved_by=?, rejection_reason=? WHERE id=?",
            [Auth::id(), $reason, $id]
        );
        Audit::log('request_reject','request',$id);
        Session::flash('success','Request rejected');
        $this->redirect('requests-view', ['id'=>$id]);
    }

    public function createWorkOrder(): void {
        Auth::requireRole(['manager','admin','supervisor']);
        $requestId = (int)$this->input('request_id');
        $req = $this->db->fetchOne("SELECT * FROM maintenance_requests WHERE id=?", [$requestId]);
        if (!$req) $this->redirect('requests');

        $woNum = $this->generateNumber('WO');
        $this->db->execute(
            "INSERT INTO work_orders (wo_number,request_id,asset_id,title,description,priority,created_by)
             VALUES (?,?,?,?,?,?,?)",
            [$woNum, $requestId, $req['asset_id'], $req['title'], $req['description'],
             $req['priority'], Auth::id()]
        );
        $woId = $this->db->lastInsertId();
        $this->db->execute("UPDATE maintenance_requests SET status='Assigned' WHERE id=?", [$requestId]);
        Audit::log('wo_create','work_order',$woId);
        Session::flash('success','Work Order created: '.$woNum);
        $this->redirect('workorders-view', ['id'=>$woId]);
    }
}