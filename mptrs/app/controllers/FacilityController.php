<?php
class FacilityController extends Controller {
    private Facility $model;
    public function __construct() {
        parent::__construct();
        Auth::requireRole(['admin','manager']);
        $this->model = new Facility();
    }
    public function index(): void {
        $this->view('facilities/index', ['items' => $this->model->all()]);
    }
    public function createForm(): void {
        $this->view('facilities/form', ['item' => null]);
    }
    public function editForm(): void {
        $id = (int)$this->input('id');
        $item = $this->model->find($id);
        if (!$item) $this->redirect('facilities');
        $this->view('facilities/form', ['item' => $item]);
    }
    public function save(): void {
        $id = (int)$this->input('id', 0);
        $data = [
            'code'           => trim($this->input('code','')),
            'name'           => trim($this->input('name','')),
            'type'           => $this->input('type','Hospital'),
            'location'       => trim($this->input('location','')),
            'contact_person' => trim($this->input('contact_person','')),
            'phone'          => trim($this->input('phone','')),
            'is_active'      => (int)$this->input('is_active', 1),
        ];
        if (!$data['code'] || !$data['name']) {
            Session::flash('error','Code and name are required');
            $this->redirect($id ? 'facilities-edit' : 'facilities-create', ['id'=>$id]);
        }
        try {
            if ($id) {
                $this->model->update($id, $data);
                Audit::log('facility_update','facility',$id);
                Session::flash('success','Facility updated');
            } else {
                $newId = $this->model->create($data);
                Audit::log('facility_create','facility',$newId);
                Session::flash('success','Facility created');
            }
        } catch (PDOException $e) {
            Session::flash('error','Save failed: '.$e->getMessage());
        }
        $this->redirect('facilities');
    }
    Auth::verifyCsrf();
    public function delete(): void {
        $id = (int)$this->input('id');
        try {
            $this->model->delete($id);
            Audit::log('facility_delete','facility',$id);
            Session::flash('success','Facility deleted');
        } catch (PDOException $e) {
            Session::flash('error','Cannot delete: '.$e->getMessage());
        }
        $this->redirect('facilities');
    }
}