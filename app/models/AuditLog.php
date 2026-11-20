<?php
declare(strict_types=1);

class AuditLog
{
    public static function add(?int $userId, string $action, string $entity, ?string $entityId = null, array $meta = []): void
    {
        $stmt = db()->prepare(
            'INSERT INTO audit_logs (user_id, action, entity, entity_id, meta) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $userId,
            $action,
            $entity,
            $entityId,
            $meta === [] ? null : json_encode($meta, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public static function recent(int $limit = 100): array
    {
        $limit = max(1, min(500, $limit));
        return db()->query(
            'SELECT a.*, u.name AS user_name
             FROM audit_logs a
             LEFT JOIN users u ON u.id = a.user_id
             ORDER BY a.id DESC
             LIMIT ' . $limit
        )->fetchAll();
    }
}
