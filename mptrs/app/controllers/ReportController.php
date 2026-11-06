<?php
class ReportController extends Controller {
    private Model $db;
    public function __construct() {
        parent::__construct();
        Auth::requireRole(['admin','manager','supervisor']);
        $this->db = new class extends Model {};
    }
    public function index(): void {
        $month = (int)($this->input('month') ?: date('n'));
        $year  = (int)($this->input('year')  ?: date('Y'));

        // Monthly maintenance summary
        $monthly = $this->db->fetchOne(
            "SELECT
                (SELECT COUNT(*) FROM maintenance_requests WHERE MONTH(created_at)=? AND YEAR(created_at)=?) AS requests_logged,
                (SELECT COUNT(*) FROM maintenance_requests WHERE status='Closed' AND MONTH(updated_at)=? AND YEAR(updated_at)=?) AS requests_closed,
                (SELECT COUNT(*) FROM maintenance_requests WHERE status NOT IN ('Closed','Rejected')) AS open_requests,
                (SELECT COUNT(*) FROM work_orders WHERE MONTH(created_at)=? AND YEAR(created_at)=?) AS wo_created,
                (SELECT COUNT(*) FROM work_orders WHERE status='Closed' AND MONTH(closed_at)=? AND YEAR(closed_at)=?) AS wo_completed,
                (SELECT COALESCE(SUM(amount),0) FROM costs WHERE MONTH(incurred_date)=? AND YEAR(incurred_date)=?) AS total_cost",
            [$month,$year,$month,$year,$month,$year,$month,$year,$month,$year]
        );

        // Cost by building
        $costByBuilding = $this->db->fetchAll(
            "SELECT b.name, SUM(c.amount) total
             FROM costs c
             JOIN work_orders wo ON wo.id=c.work_order_id
             JOIN assets a ON a.id=wo.asset_id
             JOIN buildings b ON b.id=a.building_id
             WHERE MONTH(c.incurred_date)=? AND YEAR(c.incurred_date)=?
             GROUP BY b.id ORDER BY total DESC", [$month,$year]);

        // Cost by facility
        $costByFacility = $this->db->fetchAll(
            "SELECT f.name, SUM(c.amount) total
             FROM costs c
             JOIN work_orders wo ON wo.id=c.work_order_id
             JOIN assets a ON a.id=wo.asset_id
             JOIN facilities f ON f.id=a.facility_id
             WHERE MONTH(c.incurred_date)=? AND YEAR(c.incurred_date)=?
             GROUP BY f.id ORDER BY total DESC", [$month,$year]);

        // Most faulty assets
        $faulty = $this->db->fetchAll(
            "SELECT a.asset_code, a.name, COUNT(mr.id) fault_count
             FROM assets a LEFT JOIN maintenance_requests mr ON mr.asset_id=a.id
             GROUP BY a.id HAVING fault_count > 0
             ORDER BY fault_count DESC LIMIT 10");

        // Highest cost assets
        $costAssets = $this->db->fetchAll(
            "SELECT a.asset_code, a.name, SUM(c.amount) total
             FROM costs c
             JOIN work_orders wo ON wo.id=c.work_order_id
             JOIN assets a ON a.id=wo.asset_id
             WHERE MONTH(c.incurred_date)=? AND YEAR(c.incurred_date)=?
             GROUP BY a.id ORDER BY total DESC LIMIT 10", [$month,$year]);

        // Monthly trend (12 months)
        $trend = $this->db->fetchAll(
            "SELECT DATE_FORMAT(incurred_date, '%Y-%m') m, SUM(amount) total
             FROM costs
             WHERE incurred_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY m ORDER BY m");

        // Assets by condition
        $cond = $this->db->fetchAll("SELECT condition_status, COUNT(*) c FROM assets GROUP BY condition_status");
        $crit = $this->db->fetchAll("SELECT criticality, COUNT(*) c FROM assets GROUP BY criticality");

        $this->view('reports/index',
            compact('month','year','monthly','costByBuilding','costByFacility',
                    'faulty','costAssets','trend','cond','crit'));
    }
        public function exportCsv(): void {
        Auth::requireRole(['admin','manager','supervisor']);
        $month = (int)($this->input('month') ?: date('n'));
        $year  = (int)($this->input('year')  ?: date('Y'));

        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT a.asset_code, a.name AS asset_name, c.cost_type, c.description, c.amount, c.incurred_date
             FROM costs c
             JOIN work_orders wo ON wo.id = c.work_order_id
             JOIN assets a ON a.id = wo.asset_id
             WHERE MONTH(c.incurred_date) = ? AND YEAR(c.incurred_date) = ?"
        );
        $stmt->execute([$month, $year]);
        $data = $stmt->fetchAll();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="costs_report_' . $year . '_' . $month . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Asset Code', 'Asset Name', 'Cost Type', 'Description', 'Amount', 'Date']);
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    public function printPdf(): void {
        Auth::requireRole(['admin','manager','supervisor']);
        $month = (int)($this->input('month') ?: date('n'));
        $year  = (int)($this->input('year')  ?: date('Y'));
        
        // You can pass data to the print view here (similar to the index method)
        // For brevity, we'll just render the view. 
        $this->view('reports/print', compact('month', 'year'), null); // 'null' means no layout
    }
}