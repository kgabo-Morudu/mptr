<?php
class NotificationController extends Controller {
    private Model $db;
    
    public function __construct() {
        parent::__construct();
        Auth::requireLogin();
        $this->db = new class extends Model {};
    }

    public function index(): void {
        $items = $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 100",
            [Auth::id()]
        );
        $this->view('notifications/index', compact('items'));
    }

    public function markRead(): void {
        $id = (int)$this->input('id');
        $this->db->execute("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?", [$id, Auth::id()]);
        
        $notif = $this->db->fetchOne("SELECT link FROM notifications WHERE id = ?", [$id]);
        if ($notif && $notif['link']) {
            header("Location: " . BASE_URL . "/index.php?route=" . $notif['link']);
            exit;
        }
        $this->redirect('notifications');
    }

    public function markAllRead(): void {
        $this->db->execute("UPDATE notifications SET is_read = 1 WHERE user_id = ?", [Auth::id()]);
        Session::flash('success', 'All notifications marked as read.');
        $this->redirect('notifications');
    }
}