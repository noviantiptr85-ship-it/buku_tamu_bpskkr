<?php
declare(strict_types=1);

class Infographic
{
    public static function active(): array
    {
        return db()->query('SELECT * FROM infographics WHERE active = 1 ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public static function all(): array
    {
        return db()->query('SELECT * FROM infographics ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM infographics WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(string $title, string $filePath, int $sortOrder = 0): int
    {
        $stmt = db()->prepare('INSERT INTO infographics (title, file_path, sort_order, active) VALUES (?, ?, ?, 1)');
        $stmt->execute([$title, $filePath, $sortOrder]);
        return (int) db()->lastInsertId();
    }

    public static function toggle(int $id): void
    {
        db()->prepare('UPDATE infographics SET active = 1 - active WHERE id = ?')->execute([$id]);
    }

    public static function delete(int $id): ?array
    {
        $row = self::find($id);
        if ($row) {
            db()->prepare('DELETE FROM infographics WHERE id = ?')->execute([$id]);
        }
        return $row;
    }

    public static function updateOrders(array $orders): void
    {
        $stmt = db()->prepare('UPDATE infographics SET sort_order = ? WHERE id = ?');
        foreach ($orders as $id => $order) {
            $stmt->execute([(int) $order, (int) $id]);
        }
    }

    public static function nextSortOrder(): int
    {
        $value = db()->query('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM infographics')->fetchColumn();
        return (int) $value;
    }
}
