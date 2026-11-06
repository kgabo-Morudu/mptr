<?php
class Uploader {
    private const ALLOWED = ['jpg','jpeg','png','gif','pdf','doc','docx','xls','xlsx'];
    private const MAX_SIZE = 10 * 1024 * 1024; // 10 MB

    public static function handle(array $file, string $subdir): ?string {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) return null;
        if ($file['size'] > self::MAX_SIZE) throw new RuntimeException('File too large (max 10MB)');
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED, true)) throw new RuntimeException('File type not allowed');

        $dir = UPLOAD_PATH . $subdir . '/';
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $name = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
            throw new RuntimeException('Upload failed');
        }
        return $subdir . '/' . $name;
    }
}