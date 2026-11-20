<?php
declare(strict_types=1);

/**
 * Escape output HTML.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return APP_URL . ($path === '' ? '' : '/' . ltrim($path, '/'));
}

function asset(string $path): string
{
    return '/assets/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function back(string $fallback = '/'): never
{
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    $host = parse_url($ref, PHP_URL_HOST);
    if ($ref !== '' && ($host === null || $host === ($_SERVER['HTTP_HOST'] ?? ''))) {
        redirect($ref);
    }
    redirect($fallback);
}

/* ---------------------------------------------------------------------------
 * CSRF
 * ------------------------------------------------------------------------- */

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['_token'] ?? '';
    if (!is_string($token) || $token === '' || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.');
    }
}

/* ---------------------------------------------------------------------------
 * Flash message
 * ------------------------------------------------------------------------- */

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $value;
}

/* ---------------------------------------------------------------------------
 * Pengaturan aplikasi
 * ------------------------------------------------------------------------- */

function setting(string $key, ?string $default = null): ?string
{
    $all = Setting::all();
    return array_key_exists($key, $all) ? (string) $all[$key] : $default;
}

function setting_int(string $key, int $default): int
{
    $value = setting($key);
    return $value === null || $value === '' ? $default : (int) $value;
}

/* ---------------------------------------------------------------------------
 * Format tampilan
 * ------------------------------------------------------------------------- */

function bulan_id(int $month): string
{
    $names = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];
    return $names[$month] ?? '';
}

function tanggal_id(?string $datetime, bool $withTime = true): string
{
    if ($datetime === null || $datetime === '' || $datetime === '0000-00-00 00:00:00') {
        return '-';
    }

    $ts = strtotime($datetime);
    if ($ts === false) {
        return '-';
    }

    $text = date('j', $ts) . ' ' . bulan_id((int) date('n', $ts)) . ' ' . date('Y', $ts);
    return $withTime ? $text . ', ' . date('H:i', $ts) : $text;
}

function jam(?string $datetime): string
{
    if ($datetime === null || $datetime === '') {
        return '-';
    }
    $ts = strtotime($datetime);
    return $ts === false ? '-' : date('H:i', $ts);
}

function durasi(?string $start, ?string $end): string
{
    if ($start === null || $end === null) {
        return '-';
    }

    $minutes = (int) round((strtotime($end) - strtotime($start)) / 60);
    if ($minutes < 1) {
        return '< 1 menit';
    }
    if ($minutes < 60) {
        return $minutes . ' menit';
    }

    $hours = intdiv($minutes, 60);
    $rest = $minutes % 60;
    return $rest === 0 ? $hours . ' jam' : $hours . ' jam ' . $rest . ' menit';
}

function mask_phone(string $phone): string
{
    $len = strlen($phone);
    if ($len <= 6) {
        return $phone;
    }
    return substr($phone, 0, 4) . str_repeat('*', max(2, $len - 8)) . substr($phone, -4);
}

/**
 * Nomor HP penuh hanya untuk admin/petugas. Peran lain melihat versi ter-mask.
 */
function phone_for_view(string $phone): string
{
    return can_manage_visits() ? $phone : mask_phone($phone);
}

function normalize_phone(string $raw): string
{
    $phone = preg_replace('/[^0-9+]/', '', $raw) ?? '';
    if (str_starts_with($phone, '+62')) {
        $phone = '0' . substr($phone, 3);
    } elseif (str_starts_with($phone, '62')) {
        $phone = '0' . substr($phone, 2);
    }
    return $phone;
}

function valid_phone(string $phone): bool
{
    return (bool) preg_match('/^08[1-9][0-9]{7,11}$/', $phone);
}

/* ---------------------------------------------------------------------------
 * Render view
 * ------------------------------------------------------------------------- */

function view(string $name, array $data = [], ?string $layout = 'layout'): void
{
    $file = VIEW_PATH . '/' . $name . '.php';
    if (!is_file($file)) {
        throw new RuntimeException('View tidak ditemukan: ' . $name);
    }

    extract($data, EXTR_SKIP);

    ob_start();
    require $file;
    $content = (string) ob_get_clean();

    if ($layout === null) {
        echo $content;
        return;
    }

    require VIEW_PATH . '/' . $layout . '.php';
}

function abort(int $code, string $message = ''): never
{
    http_response_code($code);
    $title = $code === 404 ? 'Halaman Tidak Ditemukan' : 'Terjadi Kesalahan';
    $default = $code === 404
        ? 'Halaman yang Anda cari tidak tersedia.'
        : 'Permintaan tidak dapat diproses.';
    $message = $message !== '' ? $message : $default;
    require VIEW_PATH . '/errors/error.php';
    exit;
}

/* ---------------------------------------------------------------------------
 * Util tanggal filter
 * ------------------------------------------------------------------------- */

function valid_date(?string $date): bool
{
    if ($date === null || $date === '') {
        return false;
    }
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    return $dt !== false && $dt->format('Y-m-d') === $date;
}

function today(): string
{
    return date('Y-m-d');
}
