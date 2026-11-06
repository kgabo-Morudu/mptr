<?php
class Auth {
    public static function attempt(string $username, string $password): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT u.*, r.name AS role_name 
             FROM users u JOIN roles r ON r.id = u.role_id 
             WHERE (u.username = ? OR u.email = ?) AND u.is_active = 1 LIMIT 1"
        );
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            unset($user['password_hash'], $user['reset_token']);
            Session::set('user', $user);
            Session::set('last_activity', time());
            $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")
               ->execute([$user['id']]);
            return true;
        }
        return false;
    }

    public static function user(): ?array {
        return Session::get('user');
    }

    public static function check(): bool {
        return Session::get('user') !== null;
    }

    public static function id(): ?int {
        $u = self::user();
        return $u ? (int)$u['id'] : null;
    }

    public static function role(): ?string {
        $u = self::user();
        return $u['role_name'] ?? null;
    }

    public static function hasRole(array $roles): bool {
        return in_array(self::role(), $roles, true);
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            header('Location: ' . BASE_URL . '/index.php?route=login');
            exit;
        }
    }

    public static function requireRole(array $roles): void {
        self::requireLogin();
        if (!self::hasRole($roles)) {
            http_response_code(403);
            die('403 - Access Denied');
        }
    }

    public static function logout(): void {
        Session::destroy();
    }

    // Password reset
    public static function createResetToken(string $email): ?string {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        if (!$stmt->fetch()) return null;
        $token = bin2hex(random_bytes(32));
        $db->prepare("UPDATE users SET reset_token=?, reset_expires=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email=?")
           ->execute([hash('sha256', $token), $email]);
        return $token;
    }

    public static function resetPassword(string $token, string $newPassword): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM users WHERE reset_token=? AND reset_expires > NOW()");
        $stmt->execute([hash('sha256', $token)]);
        $row = $stmt->fetch();
        if (!$row) return false;
        $db->prepare("UPDATE users SET password_hash=?, reset_token=NULL, reset_expires=NULL WHERE id=?")
           ->execute([password_hash($newPassword, PASSWORD_DEFAULT), $row['id']]);
        return true;
    }
        public static function csrfToken(): string {
        if (!Session::get('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function verifyCsrf(): void {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals(self::csrfToken(), $token)) {
            http_response_code(403);
            die('403 - CSRF Token Mismatch. Please refresh and try again.');
        }
    }
}