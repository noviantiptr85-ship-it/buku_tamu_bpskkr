<?php
declare(strict_types=1);

function auth_login_form(): void
{
    if (is_logged_in()) {
        redirect(home_for_role((string) current_user()['role']));
    }

    view('auth/login', [
        'title' => 'Masuk',
        'error' => null,
        'email' => '',
        'siteName' => setting('site_name', 'BPS Kabupaten Kubu Raya'),
    ], 'layout_auth');
}

function auth_login(): void
{
    verify_csrf();

    if (is_logged_in()) {
        redirect(home_for_role((string) current_user()['role']));
    }

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $error = null;
    if ($email === '' || $password === '') {
        $error = 'Email dan password wajib diisi.';
    } else {
        $error = attempt_login($email, $password);
    }

    if ($error !== null) {
        http_response_code(422);
        view('auth/login', [
            'title' => 'Masuk',
            'error' => $error,
            'email' => $email,
            'siteName' => setting('site_name', 'BPS Kabupaten Kubu Raya'),
        ], 'layout_auth');
        return;
    }

    redirect(home_for_role((string) current_user(true)['role']));
}

function auth_logout(): void
{
    verify_csrf();
    logout_user();
    session_start();
    flash('success', 'Anda telah keluar.');
    redirect('/login');
}
