<?php
class AuthController extends Controller {
    public function loginForm(): void {
        if (Auth::check()) $this->redirect('dashboard');
        $this->view('auth/login', [], 'auth');
    }

    public function login(): void {
        $username = trim((string)$this->input('username', ''));
        $password = (string)$this->input('password', '');

        if ($username === '' || $password === '') {
            Session::flash('error', 'Please fill in all fields');
            $this->redirect('login');
        }

        if (!Auth::attempt($username, $password)) {
            Audit::log('login_failed');
            Session::flash('error', 'Invalid credentials');
            $this->redirect('login');
        }

        Audit::log('login_success');
        $this->redirect('dashboard');
    }

    public function logout(): void {
        Audit::log('logout');
        Auth::logout();
        $this->redirect('login');
    }

    public function forgotForm(): void {
        $this->view('auth/forgot', [], 'auth');
    }

    public function forgot(): void {
        $email = trim((string)$this->input('email', ''));
        $token = Auth::createResetToken($email);
        // In production, send email here. For now, log the link.
        if ($token) {
            $link = BASE_URL . "/index.php?route=reset-password&token={$token}";
            error_log("Password reset for {$email}: {$link}");
            Session::flash('success', 'Reset link has been generated (check server log in dev).');
        } else {
            Session::flash('success', 'If the email exists, a reset link has been sent.');
        }
        $this->redirect('forgot-password');
    }

    public function resetForm(): void {
        $token = (string)$this->input('token', '');
        if ($token === '') { $this->redirect('login'); }
        $this->view('auth/reset', ['token' => $token], 'auth');
    }

    public function reset(): void {
        $token = (string)$this->input('token', '');
        $password = (string)$this->input('password', '');
        $confirm = (string)$this->input('password_confirm', '');

        if (strlen($password) < 8 || $password !== $confirm) {
            Session::flash('error', 'Passwords must match and be at least 8 characters');
            $this->redirect('reset-password', ['token' => $token]);
        }
        if (!Auth::resetPassword($token, $password)) {
            Session::flash('error', 'Invalid or expired reset token');
            $this->redirect('login');
        }
        Audit::log('password_reset');
        Session::flash('success', 'Password updated. Please log in.');
        $this->redirect('login');
    }
}