-- Skema Buku Tamu BPS Kabupaten Kubu Raya
-- Database: buku_tamu (MariaDB 10.11 / MySQL 8)
-- Jalankan file ini jika ingin membuat ulang tabel dari nol.

CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('admin', 'petugas', 'pimpinan') NOT NULL DEFAULT 'petugas',
    active        TINYINT(1) NOT NULL DEFAULT 1,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS visits (
    id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    visit_no     VARCHAR(20) NOT NULL UNIQUE,
    name         VARCHAR(100) NOT NULL,
    institution  VARCHAR(100) NOT NULL,
    phone        VARCHAR(20) NOT NULL,
    notes        VARCHAR(300) NOT NULL,
    check_in_at  DATETIME NOT NULL,
    check_out_at DATETIME NULL,
    status       ENUM('aktif', 'selesai') NOT NULL DEFAULT 'aktif',
    source       ENUM('kiosk', 'manual') NOT NULL DEFAULT 'kiosk',
    created_by   INT UNSIGNED NULL,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_checkin (check_in_at),
    INDEX idx_status (status),
    CONSTRAINT fk_visits_user FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS infographics (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(150) NOT NULL,
    file_path  VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    active     TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    `key` VARCHAR(50) PRIMARY KEY,
    `value` TEXT NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NULL,
    action     VARCHAR(50) NOT NULL,
    entity     VARCHAR(50) NOT NULL,
    entity_id  VARCHAR(50) NULL,
    meta       JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created (created_at)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
