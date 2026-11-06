<?php
class AssetController extends Controller {
    private Model $db;
    public function __construct() {
        parent::__construct();
        Auth::requireRole(['admin','manager','supervisor','technician']);
        $this->db = new class extends Model {};
    }
    public function index(): void {
        $search = trim((string)$this->input('q',''));
        $sql = "SELECT a.*, c.name AS category_name, f.name AS facility_name, b.name AS building_name
                FROM assets a
                JOIN asset_categories c ON c.id = a.category_id
                JOIN facilities f ON f.id = a.facility_id
                JOIN buildings b ON b.id = a.building_id";
        $params = [];
        if ($search) {
            $sql .= " WHERE a.name LIKE ? OR a.asset_code LIKE ? OR a.serial_number LIKE ?";
            $params = ["%$search%","%$search%","%$search%"];
        }
        $sql .= " ORDER BY a.created_at DESC LIMIT 500";
        $items = $this->db->fetchAll($sql, $params);
        $this->view('assets/index', compact('items','search'));
    }
    public function form(): void {
        $id = (int)$this->input('id', 0);
        $item = $id ? $this->db->fetchOne("SELECT * FROM assets WHERE id=?", [$id]) : null;
        $categories = $this->db->fetchAll("SELECT * FROM asset_categories ORDER BY name");
        $facilities = $this->db->fetchAll("SELECT * FROM facilities ORDER BY name");
        $buildings  = $this->db->fetchAll("SELECT * FROM buildings ORDER BY name");
        $this->view('assets/form', compact('item','categories','facilities','buildings'));
    }
    public function save(): void {
        $id = (int)$this->input('id', 0);
        $data = [
            'asset_code'        => trim($this->input('asset_code','')),
            'name'              => trim($this->input('name','')),
            'category_id'       => (int)$this->input('category_id'),
            'facility_id'       => (int)$this->input('facility_id'),
            'building_id'       => (int)$this->input('building_id'),
            'manufacturer'      => trim($this->input('manufacturer','')),
            'model'             => trim($this->input('model','')),
            'serial_number'     => trim($this->input('serial_number','')),
            'installation_date' => $this->input('installation_date') ?: null,
            'condition_status'  => $this->input('condition_status','Good'),
            'criticality'       => $this->input('criticality','Medium'),
            'status'            => $this->input('status','Active'),
            'warranty_expiry'   => $this->input('warranty_expiry') ?: null,
            'notes'             => trim($this->input('notes','')),
        ];
        if (!$data['asset_code'] || !$data['name'] || !$data['category_id'] || !$data['facility_id'] || !$data['building_id']) {
            Session::flash('error','Required fields missing');
            $this->redirect('assets-form', ['id'=>$id]);
        }
        try {
            if ($id) {
                $this->db->execute(
                  "UPDATE assets SET asset_code=?,name=?,category_id=?,facility_id=?,building_id=?,manufacturer=?,model=?,serial_number=?,installation_date=?,condition_status=?,criticality=?,status=?,warranty_expiry=?,notes=? WHERE id=?",
                  [$data['asset_code'],$data['name'],$data['category_id'],$data['facility_id'],$data['building_id'],
                   $data['manufacturer'],$data['model'],$data['serial_number'],$data['installation_date'],
                   $data['condition_status'],$data['criticality'],$data['status'],$data['warranty_expiry'],$data['notes'],$id]);
                Audit::log('asset_update','asset',$id);
            } else {
                $this->db->execute(
                  "INSERT INTO assets (asset_code,name,category_id,facility_id,building_id,manufacturer,model,serial_number,installation_date,condition_status,criticality,status,warranty_expiry,notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                  array_values($data));
                Audit::log('asset_create','asset',$this->db->lastInsertId());
            }
            Session::flash('success','Asset saved');
        } catch (PDOException $e) { Session::flash('error',$e->getMessage()); }
        $this->redirect('assets');
    }
    public function delete(): void {
        $id = (int)$this->input('id');
        try { $this->db->execute("DELETE FROM assets WHERE id=?", [$id]);
              Session::flash('success','Asset deleted');
        } catch (PDOException $e) { Session::flash('error','Cannot delete: '.$e->getMessage()); }
        $this->redirect('assets');
    }
}