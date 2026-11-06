<?php
class Audit {
    public static function log(string $action, ?string $entityType = null, ?int $entityId = null,
                               ?array $oldValues = null, ?array $newValues = null): void {
        try {
            $db = Database::getInstance();
            $db->prepare("INSERT INTO audit_log (user_id, action, entity_type, entity_id, old_values, new_values, ip_address, user_agent)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
               ->execute([
                   Auth::id(),
                   $action,
                   $entityType,
                   $entityId,
                   $oldValues ? json_encode($oldValues) : null,
                   $newValues ? json_encode($newValues) : null,
                   $_SERVER['REMOTE_ADDR'] ?? null,
                   substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
               ]);
        } catch (Throwable $e) {
            error_log('Audit failed: ' . $e->getMessage());
        }
    }
}