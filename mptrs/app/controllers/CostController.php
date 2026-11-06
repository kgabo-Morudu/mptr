<?php
class CostController extends Controller {
    private Model $db;
    public function __construct() {
        parent::__construct();
        Auth::requireRole(['admin','manager','supervisor']);
        $this->db = new class extends Model {};
    }
    public function index(): void {
        $items = $this->db->fetchAll(
            "SELECT c.*, wo.wo_number, a.name AS asset_name
             FROM costs c
             JOIN work_orders wo ON wo.id = c.work_order_id
             JOIN assets a ON a.id = wo.asset_id
             ORDER BY c.incurred_date DESC LIMIT 300");
        $summary = $this->db->fetchAll(
            "SELECT cost_type, COUNT(*) cnt, SUM(amount) total
             FROM costs GROUP BY cost_type");
        $this->view('costs/index', compact('items','summary'));
    }
    public function store(): void {
        Auth::requireRole(['admin','manager','supervisor']);
        $woId = (int)$this->input('work_order_id');
        $type = $this->input('cost_type');
        $amount = (float)$this->input('amount');
        $desc = trim((string)$this->input('description',''));
        $date = $this->input('incurred_date') ?: date('Y-m-d');
        if (!$woId || $amount <= 0) {
            Session::flash('error','Invalid data');
            $this->redirect('workorders-view', ['id'=>$woId]);
        }
        $this->db->execute(
            "INSERT INTO costs (work_order_id,cost_type,description,amount,incurred_date,recorded_by)
             VALUES (?,?,?,?,?,?)",
            [$woId, $type, $desc, $amount, $date, Auth::id()]
        );
        Audit::log('cost_create','cost',$this->db->lastInsertId());
        Session::flash('success','Cost recorded');
        $this->redirect('workorders-view', ['id'=>$woId]);
    }
}