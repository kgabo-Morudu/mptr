<?php
class DashboardController extends Controller {
    public function index(): void {
        Auth::requireLogin();
        $db = Database::getInstance();
        $role = Auth::role();
        $userId = Auth::id();

        // KPI Queries
        $kpi = [
            'open_requests'    => (int)$db->query("SELECT COUNT(*) FROM maintenance_requests WHERE status='Open'")->fetchColumn(),
            'active_wo'        => (int)$db->query("SELECT COUNT(*) FROM work_orders WHERE status IN ('Created','Assigned','In Progress')")->fetchColumn(),
            'overdue_wo'       => (int)$db->query("SELECT COUNT(*) FROM work_orders WHERE status NOT IN ('Closed','Cancelled','Completed') AND due_date < NOW()")->fetchColumn(),
            'completed_wo'     => (int)$db->query("SELECT COUNT(*) FROM work_orders WHERE status IN ('Completed','Verified','Closed')")->fetchColumn(),
            'total_assets'     => (int)$db->query("SELECT COUNT(*) FROM assets WHERE status='Active'")->fetchColumn(),
            'critical_assets'  => (int)$db->query("SELECT COUNT(*) FROM assets WHERE criticality='Critical'")->fetchColumn(),
            'total_cost'       => (float)$db->query("SELECT COALESCE(SUM(amount),0) FROM costs")->fetchColumn(),
        ];

        // Charts
        $reqByStatus = $db->query("SELECT status, COUNT(*) c FROM maintenance_requests GROUP BY status")->fetchAll();
        $woByStatus  = $db->query("SELECT status, COUNT(*) c FROM work_orders GROUP BY status")->fetchAll();
        $costByType  = $db->query("SELECT cost_type, SUM(amount) t FROM costs GROUP BY cost_type")->fetchAll();

        // Recent activity
        $recentRequests = $db->query(
            "SELECT mr.*, a.name AS asset_name, u.full_name requester
             FROM maintenance_requests mr
             JOIN assets a ON a.id = mr.asset_id
             JOIN users u ON u.id = mr.requester_id
             ORDER BY mr.created_at DESC LIMIT 5"
        )->fetchAll();

        // Technician-specific
        if ($role === 'technician') {
            $kpi['my_assigned'] = (int)$db->query("SELECT COUNT(*) FROM work_orders WHERE technician_id={$userId} AND status='Assigned'")->fetchColumn();
            $kpi['my_active']   = (int)$db->query("SELECT COUNT(*) FROM work_orders WHERE technician_id={$userId} AND status='In Progress'")->fetchColumn();
        }
        if ($role === 'requester') {
            $kpi['my_requests'] = (int)$db->query("SELECT COUNT(*) FROM maintenance_requests WHERE requester_id={$userId}")->fetchColumn();
        }

        $this->view('dashboard/index', compact('kpi','reqByStatus','woByStatus','costByType','recentRequests','role'));
    }
}