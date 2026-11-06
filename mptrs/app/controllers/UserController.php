<?php
class UserController extends Controller {
    private Model $db;
    public function __construct() {
        parent::__construct();
        Auth::requireRole(['admin']);
        $this->db = new class extends Model {};
    }
    public function index(): void {
        $items = $this->db->fetchAll(
            "SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id=u.role_id ORDER BY u.full_name");
        $this->view('users/index', compact('items'));
    }
    public function form(): void {
        $id = (int)$this->input('id', 0);
        $item = $id ? $this->db->fetchOne("SELECT * FROM users WHERE id=?", [$id]) : null;
        $roles = $this->db->fetchAll("SELECT * FROM roles");
        $this->view('users/form', compact('item','roles'));
    }
    public function save(): void {
        $id = (int)$this->input('id', 0);
        $data = [
            'full_name'  => trim($this->input('full_name','')),
            'email'      => trim($this->input('email','')),
            'username'   => trim($this->input('username','')),
            'phone'      => trim($this->input('phone','')),
            'role_id'    => (int)$this->input('role_id'),
            'department' => trim($this->input('department','')),
            'is_active'  => (int)$this->input('is_active',1),
        ];
        $password = (string)$this->input('password','');
        if (!$data['full_name'] || !$data['email'] || !$data['username'] || !$data['role_id']) {
            Session::flash('error','Required fields missing');
            $this->redirect('users-form', ['id'=>$id]);
        }
        try {
            if ($id) {
                $this->db->execute(
                    "UPDATE users SET full_name=?,email=?,username=?,phone=?,role_id=?,department=?,is_active=? WHERE id=?",
                    [...array_values($data), $id]);
                if ($password) {
                    $this->db->execute("UPDATE users SET password_hash=? WHERE id=?",
                        [password_hash($password, PASSWORD_DEFAULT), $id]);
                }
            } else {
                if (strlen($password) < 8) {
                    Session::flash('error','Password must be at least 8 characters');
                    $this->redirect('users-form');
                }
                $this->db->execute(
                    "INSERT INTO users (full_name,email,username,password_hash,phone,role_id,department,is_active)
                     VALUES (?,?,?,?,?,?,?,?)",
                    [$data['full_name'],$data['email'],$data['username'],
                     password_hash($password, PASSWORD_DEFAULT),
                     $data['phone'],$data['role_id'],$data['department'],$data['is_active']]);
            }
            Audit::log($id?'user_update':'user_create','user',$id ?: $this->db->lastInsertId());
            Session::flash('success','User saved');
        } catch (PDOException $e) { Session::flash('error',$e->getMessage()); }
        $this->redirect('users');
    }
    public function delete(): void {
        $id = (int)$this->input('id');
        if ($id === Auth::id()) { Session::flash('error','Cannot delete yourself'); $this->redirect('users'); }
        try { $this->db->execute("DELETE FROM users WHERE id=?", [$id]);
              Session::flash('success','User deleted');
        } catch (PDOException $e) { Session::flash('error','Cannot delete: '.$e->getMessage()); }
        $this->redirect('users');
    }
}