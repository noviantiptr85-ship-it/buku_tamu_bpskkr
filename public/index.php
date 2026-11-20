<?php
declare(strict_types=1);

// Layani file statis saat memakai server bawaan PHP (tanpa memuat sesi/aplikasi).
if (PHP_SAPI === 'cli-server') {
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    if (str_contains($requestPath, '..')) {
        http_response_code(404);
        exit('Not Found');
    }

    $file = __DIR__ . $requestPath;
    if ($requestPath !== '/' && is_file($file)) {
        return false;
    }
}

require_once __DIR__ . '/../app/bootstrap.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: same-origin');

require APP_PATH . '/controllers/kiosk.php';
require APP_PATH . '/controllers/auth.php';
require APP_PATH . '/controllers/dashboard.php';
require APP_PATH . '/controllers/reports.php';
require APP_PATH . '/controllers/admin.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

switch (true) {
    // Kiosk
    case $path === '/' && $method === 'GET':
        kiosk_index();
        break;
    case $path === '/isi' && $method === 'GET':
        kiosk_form();
        break;
    case $path === '/isi' && $method === 'POST':
        kiosk_submit();
        break;
    case $path === '/sukses' && $method === 'GET':
        kiosk_success();
        break;

    // Auth
    case $path === '/login' && $method === 'GET':
        auth_login_form();
        break;
    case $path === '/login' && $method === 'POST':
        auth_login();
        break;
    case $path === '/logout' && $method === 'POST':
        auth_logout();
        break;

    // Dashboard petugas
    case $path === '/dashboard' && $method === 'GET':
        dashboard_index();
        break;
    case $path === '/dashboard/tambah' && $method === 'GET':
        dashboard_create_form();
        break;
    case $path === '/dashboard/tambah' && $method === 'POST':
        dashboard_create();
        break;
    case $path === '/visits/checkout' && $method === 'POST':
        dashboard_checkout();
        break;

    // Laporan
    case $path === '/laporan' && $method === 'GET':
        report_index();
        break;
    case $path === '/laporan/export' && $method === 'GET':
        report_export();
        break;
    case $path === '/laporan/print' && $method === 'GET':
        report_print();
        break;

    // Admin
    case $path === '/admin' && $method === 'GET':
        redirect('/admin/slides');
        break;
    case $path === '/admin/slides' && $method === 'GET':
        admin_slides();
        break;
    case $path === '/admin/slides/upload' && $method === 'POST':
        admin_slide_upload();
        break;
    case $path === '/admin/slides/toggle' && $method === 'POST':
        admin_slide_toggle();
        break;
    case $path === '/admin/slides/delete' && $method === 'POST':
        admin_slide_delete();
        break;
    case $path === '/admin/slides/order' && $method === 'POST':
        admin_slide_order();
        break;
    case $path === '/admin/pengaturan' && $method === 'GET':
        admin_settings();
        break;
    case $path === '/admin/pengaturan' && $method === 'POST':
        admin_settings_save();
        break;
    case $path === '/admin/users' && $method === 'GET':
        admin_users();
        break;
    case $path === '/admin/users/create' && $method === 'POST':
        admin_user_create();
        break;
    case $path === '/admin/users/toggle' && $method === 'POST':
        admin_user_toggle();
        break;
    case $path === '/admin/users/password' && $method === 'POST':
        admin_user_password();
        break;
    case $path === '/admin/audit' && $method === 'GET':
        admin_audit();
        break;

    default:
        abort(404);
}
