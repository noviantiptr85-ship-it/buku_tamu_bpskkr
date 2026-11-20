<?php
declare(strict_types=1);

function kiosk_index(): void
{
    view('kiosk/index', [
        'title' => 'Buku Tamu',
        'slides' => Infographic::active(),
        'slideDuration' => max(3, min(15, setting_int('slide_duration', 5))),
        'welcomeText' => setting('welcome_text', 'Selamat datang di BPS Kabupaten Kubu Raya'),
        'runningText' => setting('running_text', ''),
        'siteName' => setting('site_name', 'BPS Kabupaten Kubu Raya'),
    ], 'layout_kiosk');
}

function kiosk_form(): void
{
    view('kiosk/form', [
        'title' => 'Isi Buku Tamu',
        'errors' => [],
        'old' => [],
        'siteName' => setting('site_name', 'BPS Kabupaten Kubu Raya'),
    ], 'layout_kiosk');
}

function kiosk_submit(): void
{
    verify_csrf();

    $old = [
        'name' => trim((string) ($_POST['name'] ?? '')),
        'institution' => trim((string) ($_POST['institution'] ?? '')),
        'phone' => trim((string) ($_POST['phone'] ?? '')),
        'notes' => trim((string) ($_POST['notes'] ?? '')),
    ];

    $errors = validate_visit_input($old);

    if ($errors !== []) {
        http_response_code(422);
        view('kiosk/form', [
            'title' => 'Isi Buku Tamu',
            'errors' => $errors,
            'old' => $old,
            'siteName' => setting('site_name', 'BPS Kabupaten Kubu Raya'),
        ], 'layout_kiosk');
        return;
    }

    try {
        $visit = Visit::create([
            'name' => $old['name'],
            'institution' => $old['institution'],
            'phone' => normalize_phone($old['phone']),
            'notes' => $old['notes'],
            'source' => 'kiosk',
            'created_by' => null,
        ]);
    } catch (Throwable $e) {
        http_response_code(500);
        view('kiosk/form', [
            'title' => 'Isi Buku Tamu',
            'errors' => ['general' => 'Maaf, data gagal disimpan. Silakan coba lagi atau hubungi petugas.'],
            'old' => $old,
            'siteName' => setting('site_name', 'BPS Kabupaten Kubu Raya'),
        ], 'layout_kiosk');
        return;
    }

    AuditLog::add(null, 'visit.create', 'visit', (string) $visit['id'], ['source' => 'kiosk']);

    $_SESSION['last_visit'] = [
        'visit_no' => $visit['visit_no'],
        'name' => $visit['name'],
        'check_in_at' => $visit['check_in_at'],
    ];

    redirect('/sukses');
}

function kiosk_success(): void
{
    $visit = $_SESSION['last_visit'] ?? null;
    unset($_SESSION['last_visit']);

    if (!$visit) {
        redirect('/');
    }

    view('kiosk/success', [
        'title' => 'Terima Kasih',
        'visit' => $visit,
        'successDuration' => max(3, min(15, setting_int('success_duration', 5))),
        'siteName' => setting('site_name', 'BPS Kabupaten Kubu Raya'),
    ], 'layout_kiosk');
}
