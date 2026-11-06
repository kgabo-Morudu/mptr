<?php
class BuildingController extends Controller {
    private Model $db;
    public function __construct() {
        parent::__construct();
        Auth::requireRole(['admin','manager']);
        $this->db = new class extends Model {};
    }
    public function index(): void {
        $facilityModel = new Facility();
        $items = $this->db->fetchAll(
            "SELECT b.*, f.name AS facility_name FROM buildings b
             JOIN facilities f ON f.id = b.facility_id ORDER BY f.name, b.name"
        );
        $facilities = $facilityModel->all();
        $this->view('buildings/index', compact('items','facilities'));
    }
    public function form(): void {
        $id = (int)$this->input('id', 0);
        $item = $id ? $this->db->fetchOne("SELECT * FROM buildings WHERE id=?", [$id]) : null;
        $facilities = (new Facility())->all();
        $this->view('buildings/form', compact('item','facilities'));
    }
    public function save(): void {
        $id = (int)$this->input('id', 0);
        $data = [
            'facility_id' => (int)$this->input('facility_id'),
            'code'        => trim($this->input('code','')),
            'name'        => trim($this->input('name','')),
            'type'        => $this->input('type','Ward'),
            'floors'      => (int)$this->input('floors', 1),
            'description' => trim($this->input('description','')),
            'is_active'   => (int)$this->input('is_active', 1),
        ];
        if (!$data['facility_id'] || !$data['code'] || !$data['name']) {
            Session::flash('error','Facility, code and name required');
            $this->redirect('buildings');
        }
        try {
            if ($id) {
                $this->db->execute("UPDATE buildings SET facility_id=?,code=?,name=?,type=?,floors=?,description=?,is_active=? WHERE id=?",
                    [$data['facility_id'],$data['code'],$data['name'],$data['type'],$data['floors'],$data['description'],$data['is_active'],$id]);
            } else {
                $this->db->execute("INSERT INTO buildings (facility_id,code,name,type,floors,description,is_active) VALUES (?,?,?,?,?,?,?)",
                    [$data['facility_id'],$data['code'],$data['name'],$data['type'],$data['floors'],$data['description'],$data['is_active']]);
            }
            Audit::log($id?'building_update':'building_create','building',$id ?: 0);
            Session::flash('success','Building saved');
        } catch (PDOException $e) { Session::flash('error',$e->getMessage()); }
        $this->redirect('buildings');
    }
    public function delete(): void {
        $id = (int)$this->input('id');
        try { $this->db->execute("DELETE FROM buildings WHERE id=?", [$id]);
              Session::flash('success','Building deleted');
        } catch (PDOException $e) { Session::flash('error','Cannot delete: '.$e->getMessage()); }
        $this->redirect('buildings');
    }
}