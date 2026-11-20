<?php
$user = current_user();
$current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$is = static function (string $prefix) use ($current): string {
    return str_starts_with($current, $prefix) ? ' class="active"' : '';
};
$siteName = setting('site_name', 'BPS Kabupaten Kubu Raya');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($title ?? 'Beranda') . ' · ' . $siteName) ?></title>
    <link rel="icon" href="/assets/img/logo-bps.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <a class="brand" href="/dashboard">
            <img src="/assets/img/logo-bps.svg" alt="Logo BPS">
            <span>
                <strong>Buku Tamu</strong>
                <small><?= e($siteName) ?></small>
            </span>
        </a>

        <nav>
            <a href="/dashboard"<?= $is('/dashboard') ?>>Tamu Hari Ini</a>
            <a href="/laporan"<?= $is('/laporan') ?>>Laporan</a>

            <?php if (is_admin()): ?>
                <div class="nav-label">Administrasi</div>
                <a href="/admin/slides"<?= $is('/admin/slides') ?>>Infografis</a>
                <a href="/admin/pengaturan"<?= $is('/admin/pengaturan') ?>>Pengaturan</a>
                <a href="/admin/users"<?= $is('/admin/users') ?>>Pengguna</a>
                <a href="/admin/audit"<?= $is('/admin/audit') ?>>Audit Log</a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-foot">
            <a href="/" target="_blank" rel="noopener">Buka tampilan kiosk →</a>
            <small>Versi 1.0 · <?= e(date('Y')) ?></small>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div>
                <h1><?= e($title ?? 'Beranda') ?></h1>
                <p class="muted"><?= e(tanggal_id(date('Y-m-d H:i:s'))) ?> WIB</p>
            </div>
            <div class="topbar-actions">
                <div class="user-chip">
                    <span class="avatar"><?= e(mb_strtoupper(mb_substr((string) ($user['name'] ?? '?'), 0, 1))) ?></span>
                    <span>
                        <strong><?= e((string) ($user['name'] ?? '')) ?></strong>
                        <small><?= e(ucfirst((string) ($user['role'] ?? ''))) ?></small>
                    </span>
                </div>
                <form method="post" action="/logout">
                    <?= csrf_field() ?>
                    <button class="btn btn-ghost btn-sm" type="submit">Keluar</button>
                </form>
            </div>
        </header>

        <main class="content">
            <?php if ($message = flash('success')): ?>
                <div class="alert alert-success"><?= e($message) ?></div>
            <?php endif; ?>
            <?php if ($message = flash('error')): ?>
                <div class="alert alert-error"><?= e($message) ?></div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>
</div>
<script src="/assets/js/app.js"></script>
</body>
</html>
