<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Seeder hanya dapat dijalankan dari command line.');
}

require __DIR__ . '/../app/bootstrap.php';

$pdo = db();

echo "== Seed Buku Tamu BPS Kubu Raya ==\n\n";

/* ---------------------------------------------------------------------- */
/* 1. Pengguna                                                            */
/* ---------------------------------------------------------------------- */

$users = [
    ['Administrator', 'admin@bpskuburaya.local', 'Admin#BPS2026', 'admin'],
    ['Petugas Front Office', 'petugas@bpskuburaya.local', 'Petugas#BPS2026', 'petugas'],
    ['Pimpinan', 'pimpinan@bpskuburaya.local', 'Pimpinan#BPS2026', 'pimpinan'],
];

foreach ($users as [$name, $email, $password, $role]) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);

    if ($stmt->fetchColumn()) {
        echo "[skip] user {$email} sudah ada\n";
        continue;
    }

    $insert = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, active) VALUES (?, ?, ?, ?, 1)');
    $insert->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
    echo "[ok]   user {$email} ({$role})\n";
}

/* ---------------------------------------------------------------------- */
/* 2. Infografis                                                          */
/* ---------------------------------------------------------------------- */

$slides = [
    [
        'source' => 'Infografis/Statistik Ekonomi/image-proxy (1).jpg',
        'target' => '01-ekonomi-pertumbuhan-2025.jpg',
        'title' => 'Pertumbuhan Ekonomi Kabupaten Kubu Raya Tahun 2025',
        'order' => 1,
    ],
    [
        'source' => 'Infografis/Statistk Demografi dan Sosial/image-proxy.jpg',
        'target' => '02-demografi-ipm-2026.jpg',
        'title' => 'Indeks Pembangunan Manusia Kabupaten Kubu Raya 2026',
        'order' => 2,
    ],
    [
        'source' => 'Infografis/Statistk Demografi dan Sosial/image-proxy (1).jpg',
        'target' => '03-demografi-kemiskinan-2026.jpg',
        'title' => 'Profil Kemiskinan Kabupaten Kubu Raya, Maret 2026',
        'order' => 3,
    ],
    [
        'source' => 'Infografis/Statistk Demografi dan Sosial/image-proxy (2).jpg',
        'target' => '04-demografi-ketenagakerjaan-2023.jpg',
        'title' => 'Keadaan Ketenagakerjaan Kubu Raya Agustus 2023',
        'order' => 4,
    ],
    [
        'source' => 'Infografis/Statistk Demografi dan Sosial/image-proxy (3).jpg',
        'target' => '05-demografi-kemiskinan-2023.jpg',
        'title' => 'Profil Kemiskinan di Kubu Raya Maret 2023',
        'order' => 5,
    ],
    [
        'source' => 'Infografis/Statistk Demografi dan Sosial/image-proxy (4).jpg',
        'target' => '06-demografi-kependudukan-sp2020.jpg',
        'title' => 'Estimasi Parameter Kependudukan Kabupaten Kubu Raya (SP2020)',
        'order' => 6,
    ],
    [
        'source' => 'Infografis/Statistk Demografi dan Sosial/image-proxy (5).jpg',
        'target' => '07-demografi-kemiskinan-2010-2020.jpg',
        'title' => 'Profil Kemiskinan Kabupaten Kubu Raya 2010–2020',
        'order' => 7,
    ],
    [
        'source' => 'Infografis/Statistk Demografi dan Sosial/image-proxy (6).jpg',
        'target' => '08-demografi-ketenagakerjaan-2020.jpg',
        'title' => 'Keadaan Ketenagakerjaan Kabupaten Kubu Raya 2020',
        'order' => 8,
    ],
    [
        'source' => 'Infografis/Statistik Lingkungan Hidup dan Multidomain/image-proxy.jpg',
        'target' => '09-lingkungan-sensus-pertanian-2023.jpg',
        'title' => 'Hasil Pencacahan Lengkap Sensus Pertanian 2023 – Tahap I',
        'order' => 9,
    ],
];

$destinationDir = PUBLIC_PATH . '/assets/infografis';
if (!is_dir($destinationDir) && !mkdir($destinationDir, 0775, true) && !is_dir($destinationDir)) {
    exit("[error] Tidak dapat membuat folder {$destinationDir}\n");
}

foreach ($slides as $slide) {
    $source = ROOT_PATH . '/' . $slide['source'];
    $target = $destinationDir . '/' . $slide['target'];
    $relativePath = '/assets/infografis/' . $slide['target'];

    if (!is_file($target)) {
        if (!is_file($source)) {
            echo "[warn] sumber tidak ditemukan: {$slide['source']}\n";
            continue;
        }
        if (!copy($source, $target)) {
            echo "[warn] gagal menyalin: {$slide['source']}\n";
            continue;
        }
        echo "[copy] {$slide['target']}\n";
    }

    $stmt = $pdo->prepare('SELECT id FROM infographics WHERE file_path = ? LIMIT 1');
    $stmt->execute([$relativePath]);

    if ($stmt->fetchColumn()) {
        echo "[skip] slide {$slide['title']}\n";
        continue;
    }

    $insert = $pdo->prepare('INSERT INTO infographics (title, file_path, sort_order, active) VALUES (?, ?, ?, 1)');
    $insert->execute([$slide['title'], $relativePath, $slide['order']]);
    echo "[ok]   slide {$slide['title']}\n";
}

/* ---------------------------------------------------------------------- */
/* 3. Pengaturan                                                          */
/* ---------------------------------------------------------------------- */

$settings = [
    'site_name' => 'BPS Kabupaten Kubu Raya',
    'welcome_text' => 'Selamat datang di BPS Kabupaten Kubu Raya',
    'running_text' => '',
    'slide_duration' => '5',
    'success_duration' => '5',
];

foreach ($settings as $key => $value) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM settings WHERE `key` = ?');
    $stmt->execute([$key]);

    if ((int) $stmt->fetchColumn() > 0) {
        echo "[skip] setting {$key}\n";
        continue;
    }

    Setting::set($key, $value);
    echo "[ok]   setting {$key} = {$value}\n";
}

echo "\nSelesai. Akun demo:\n";
echo "  admin@bpskuburaya.local / Admin#BPS2026\n";
echo "  petugas@bpskuburaya.local / Petugas#BPS2026\n";
echo "  pimpinan@bpskuburaya.local / Pimpinan#BPS2026\n\n";
echo "Jalankan: C:\\xampp\\php\\php.exe -S localhost:8000 -t public public/index.php\n";
