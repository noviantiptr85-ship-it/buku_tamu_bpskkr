<?php
declare(strict_types=1);

function current_user(bool $refresh = false): ?array
{
    static $resolved = false;
    static $user = null;

    if ($refresh) {
        $resolved = false;
        $user = null;
    }

    if ($resolved) {
        return $user;
    }
    $resolved = true;

    $id = $_SESSION['user_id'] ?? null;
    if (!$id) {
        return null;
    }

    $user = User::find((int) $id);
    if (!$user || !(int) $user['active']) {
        unset($_SESSION['user_id']);
        $user = null;
    }

    return $user;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    return (current_user()['role'] ?? null) === 'admin';
}

function is_petugas(): bool
{
    return (current_user()['role'] ?? null) === 'petugas';
}

function can_manage_visits(): bool
{
    return in_array(current_user()['role'] ?? '', ['admin', 'petugas'], true);
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Silakan masuk terlebih dahulu.');
        redirect('/login');
    }
}

function require_role(string ...$roles): void
{
    require_login();
    if (!in_array(current_user()['role'] ?? '', $roles, true)) {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}

/**
 * Coba login. Mengembalikan pesan error atau null jika berhasil.
 */
function attempt_login(string $email, string $password): ?string
{
    $lockedUntil = (int) ($_SESSION['_login_locked_until'] ?? 0);
    if ($lockedUntil > time()) {
        $wait = $lockedUntil - time();
        return 'Terlalu banyak percobaan. Coba lagi dalam ' . $wait . ' detik.';
    }

    $user = User::findByEmail(strtolower(trim($email)));
    if (!$user || !password_verify($password, $user['password_hash'])) {
        $_SESSION['_login_attempts'] = (int) ($_SESSION['_login_attempts'] ?? 0) + 1;
        if ($_SESSION['_login_attempts'] >= 5) {
            $_SESSION['_login_attempts'] = 0;
            $_SESSION['_login_locked_until'] = time() + 60;
        }
        return 'Email atau password salah.';
    }

    if (!(int) $user['active']) {
        return 'Akun ini sedang dinonaktifkan. Hubungi admin.';
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    unset($_SESSION['_login_attempts'], $_SESSION['_login_locked_until']);

    AuditLog::add((int) $user['id'], 'login', 'user', (string) $user['id']);

    return null;
}

function logout_user(): void
{
    $id = $_SESSION['user_id'] ?? null;
    if ($id) {
        AuditLog::add((int) $id, 'logout', 'user', (string) $id);
    }

    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }
    session_destroy();
}

function home_for_role(string $role): string
{
    return match ($role) {
        'admin' => '/admin/slides',
        'pimpinan' => '/laporan',
        default => '/dashboard',
    };
}
