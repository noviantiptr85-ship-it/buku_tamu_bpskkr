<?php
declare(strict_types=1);

class Setting
{
    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $rows = db()->query('SELECT `key`, `value` FROM settings')->fetchAll();
        $data = [];
        foreach ($rows as $row) {
            $data[$row['key']] = $row['value'];
        }

        return self::$cache = $data;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $all = self::all();
        return array_key_exists($key, $all) ? (string) $all[$key] : $default;
    }

    public static function set(string $key, string $value): void
    {
        $stmt = db()->prepare(
            'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)'
        );
        $stmt->execute([$key, $value]);
        self::$cache = null;
    }

    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            self::set((string) $key, (string) $value);
        }
    }
}
