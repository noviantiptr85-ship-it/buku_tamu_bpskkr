<?php
declare(strict_types=1);

/**
 * Koneksi PDO tunggal (singleton) ke MariaDB/MySQL.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        env('DB_HOST', '127.0.0.1'),
        env('DB_PORT', '3306'),
        env('DB_NAME', 'buku_tamu')
    );

    try {
        $pdo = new PDO($dsn, env('DB_USER', 'root'), env('DB_PASS', ''), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        if (APP_DEBUG) {
            exit('Koneksi database gagal: ' . $e->getMessage());
        }
        exit('Koneksi database gagal. Hubungi administrator.');
    }

    return $pdo;
}
