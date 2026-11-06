<?php
class WorkOrderController extends Controller {
    private Model $db;
    public function __construct() {
        parent::__construct();
        Auth::requireLogin();
        $this->db = new class extends Model {};
    }
    public function index(): void {
        $role = Auth::role();
        $sql = "SELECT wo.*, a.name AS asset_name, a.asset_code,
                       t.full_name AS technician_name
                FROM work_orders wo
                JOIN assets a ON a.id = wo.asset_id
                LEFT JOIN users t ON t.id = wo.technician_id";
        $params = [];
        if ($role === 'technician') {
            $sql .= " WHERE wo.technician_id = ?";
            $params[] = Auth::id();
        } elseif ($role === 'requester') {
            $sql .= " WHERE 1=0"; // requesters see via requests only
        }
        $sql .= " ORDER BY wo.created_at DESC LIMIT 300";
        $items = $this->db->fetchAll($sql, $params);
        $this->view('workorders/index', compact('items','role'));
    }
    public function viewWO(): void {
        $id = (int)$this->input('id');
        $wo = $this->db->fetchOne(
            "SELECT wo.*, a.name AS asset_name, a.asset_code, a.criticality,
                    t.full_name AS technician_name, s.full_name AS supervisor_name,
                    u.full_name AS creator_name
             FROM work_orders wo
             JOIN assets a ON a.id=wo.asset_id
             LEFT JOIN users t ON t.id=wo.technician_id
             LEFT JOIN users s ON s.id=wo.supervisor_id
             JOIN users u ON u.id=wo.created_by
             WHERE wo.id=?", [$id]
        );
        if (!$wo) $this->redirect('workorders');
        $progress = $this->db->fetchAll(
            "SELECT wp.*, u.full_name FROM work_order_progress wp JOIN users u ON u.id=wp.user_id
             WHERE wp.work_order_id=? ORDER BY wp.created_at DESC", [$id]);
        $costs = $this->db->fetchAll(
            "SELECT * FROM costs WHERE work_order_id=? ORDER BY incurred_date DESC", [$id]);
        $attachments = $this->db->fetchAll(
            "SELECT * FROM attachments WHERE entity_type='work_order' AND entity_id=?", [$id]);
        $technicians = $this->db->fetchAll(
            "SELECT u.id, u.full_name FROM users u JOIN roles r ON r.id=u.role_id
             WHERE r.name='technician' AND u.is_active=1 ORDER BY u.full_name");
        $totals = $this->db->fetchOne(
            "SELECT SUM(amount) total FROM costs WHERE work_order_id=?", [$id]);
        $this->view('workorders/view',
            compact('wo','progress','costs','attachments','technicians','totals'));
    }
    public function assign(): void {
        Auth::requireRole(['admin','manager','supervisor']);
        $id = (int)$this->input('id');
        $techId = (int)$this->input('technician_id');
        $dueDate = $this->input('due_date') ?: null;
        $this->db->execute(
            "UPDATE work_orders SET technician_id=?, supervisor_id=?, assigned_date=NOW(),
                                    due_date=?, status='Assigned' WHERE id=?",
            [$techId, Auth::id(), $dueDate, $id]
        );
        $this->db->execute(
            "INSERT INTO work_order_progress (work_order_id,user_id,status_from,status_to,notes)
             VALUES (?,?,?,?,?)",
            [$id, Auth::id(), 'Created','Assigned',"Assigned to technician"])
        ;
        // Notify technician
        $this->db->execute(
            "INSERT INTO notifications (user_id,title,message,link) VALUES (?,?,?,?)",
            [$techId, 'New Work Order Assigned', 'You have a new job assignment',
             'workorders-view&id='.$id]
        );
        Audit::log('wo_assign','work_order',$id);
        Session::flash('success','Work order assigned');
        $this->redirect('workorders-view', ['id'=>$id]);
    }
    public function updateStatus(): void {
        Auth::requireLogin();
        $id = (int)$this->input('id');
        $newStatus = $this->input('status');
        $notes = trim((string)$this->input('notes',''));
        $hours = (float)$this->input('hours', 0);

        $wo = $this->db->fetchOne("SELECT * FROM work_orders WHERE id=?", [$id]);
        if (!$wo) $this->redirect('workorders');

        // RBAC: technician can only update own
        if (Auth::role()==='technician' && (int)$wo['technician_id'] !== Auth::id()) {
            $this->redirect('workorders');
        }

        $updates = ['status = ?'];
        $params = [$newStatus];
        if ($newStatus==='In Progress' && !$wo['started_at']) { $updates[]='started_at=NOW()'; }
        if ($newStatus==='Completed') { $updates[]='completion_date=NOW()'; }
        if ($newStatus==='Closed') { $updates[]='closed_at=NOW()'; }
        if ($hours > 0) {
            $updates[]='labour_hours = labour_hours + ?';
            $params[] = $hours;
        }
        $params[] = $id;
        $this->db->execute("UPDATE work_orders SET ".implode(',', $updates)." WHERE id=?", $params);

        $this->db->execute(
            "INSERT INTO work_order_progress (work_order_id,user_id,status_from,status_to,notes,hours_logged)
             VALUES (?,?,?,?,?,?)",
            [$id, Auth::id(), $wo['status'], $newStatus, $notes, $hours]
        );

        // Sync request status
        if ($wo['request_id']) {
            $reqStatus = match($newStatus) {
                'In Progress' => 'In Progress',
                'Completed'   => 'Completed',
                'Closed'      => 'Closed',
                default       => null,
            };
            if ($reqStatus) {
                $this->db->execute("UPDATE maintenance_requests SET status=? WHERE id=?",
                    [$reqStatus, $wo['request_id']]);
            }
        }

        Audit::log('wo_status_change','work_order',$id, ['status'=>$wo['status']], ['status'=>$newStatus]);
        Session::flash('success',"Status updated to {$newStatus}");
        $this->redirect('workorders-view', ['id'=>$id]);
    }
    public function uploadPhoto(): void {
        Auth::requireLogin();
        $id = (int)$this->input('id');
        if (!empty($_FILES['photo']['tmp_name'])) {
            try {
                $path = Uploader::handle($_FILES['photo'], 'workorders');
                if ($path) {
                    $this->db->execute(
                        "INSERT INTO attachments (entity_type,entity_id,file_name,file_path,file_type,file_size,uploaded_by)
                         VALUES ('work_order',?,?,?,?,?,?)",
                        [$id, $_FILES['photo']['name'], $path,
                         $_FILES['photo']['type'], $_FILES['photo']['size'], Auth::id()]
                    );
                    Session::flash('success','Photo uploaded');
                }
            } catch (Throwable $e) { Session::flash('error',$e->getMessage()); }
        }
        $this->redirect('workorders-view', ['id'=>$id]);
    }
}