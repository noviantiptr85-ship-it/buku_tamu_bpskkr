<?php
declare(strict_types=1);

class User
{
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array
    {
        return db()->query('SELECT * FROM users ORDER BY FIELD(role, "admin", "petugas", "pimpinan"), name')->fetchAll();
    }

    public static function create(string $name, string $email, string $password, string $role): int
    {
        $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, role, active) VALUES (?, ?, ?, ?, 1)');
        $stmt->execute([
            trim($name),
            strtolower(trim($email)),
            password_hash($password, PASSWORD_DEFAULT),
            $role,
        ]);
        return (int) db()->lastInsertId();
    }

    public static function setActive(int $id, bool $active): void
    {
        $stmt = db()->prepare('UPDATE users SET active = ? WHERE id = ?');
        $stmt->execute([$active ? 1 : 0, $id]);
    }

    public static function updatePassword(int $id, string $password): void
    {
        $stmt = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public static function emailExists(string $email, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE email = ?';
        $params = [strtolower(trim($email))];
        if ($exceptId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $exceptId;
        }
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
