<?php
declare(strict_types=1);

function safe_return(string $url, string $fallback = '/dashboard'): string
{
    if ($url !== '' && str_starts_with($url, '/') && !str_starts_with($url, '//')) {
        return $url;
    }
    return $fallback;
}

function dashboard_index(): void
{
    require_login();

    $q = trim((string) ($_GET['q'] ?? ''));
    $from = valid_date($_GET['from'] ?? null) ? (string) $_GET['from'] : today();
    $to = valid_date($_GET['to'] ?? null) ? (string) $_GET['to'] : today();
    $status = in_array($_GET['status'] ?? '', ['aktif', 'selesai'], true) ? (string) $_GET['status'] : '';

    if ($from > $to) {
        [$from, $to] = [$to, $from];
    }

    $visits = Visit::search([
        'q' => $q,
        'from' => $from,
        'to' => $to,
        'status' => $status,
    ]);

    view('dashboard/index', [
        'title' => 'Tamu',
        'visits' => $visits,
        'stats' => Visit::stats(today()),
        'filters' => ['q' => $q, 'from' => $from, 'to' => $to, 'status' => $status],
        'canManage' => can_manage_visits(),
    ]);
}

function dashboard_create_form(): void
{
    require_role('admin', 'petugas');

    view('dashboard/create', [
        'title' => 'Tambah Tamu Manual',
        'errors' => [],
        'old' => [],
    ]);
}

function dashboard_create(): void
{
    require_role('admin', 'petugas');
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
        view('dashboard/create', [
            'title' => 'Tambah Tamu Manual',
            'errors' => $errors,
            'old' => $old,
        ]);
        return;
    }

    $user = current_user();
    $visit = Visit::create([
        'name' => $old['name'],
        'institution' => $old['institution'],
        'phone' => normalize_phone($old['phone']),
        'notes' => $old['notes'],
        'source' => 'manual',
        'created_by' => (int) $user['id'],
    ]);

    AuditLog::add((int) $user['id'], 'visit.create', 'visit', (string) $visit['id'], ['source' => 'manual']);

    flash('success', 'Tamu manual berhasil dicatat dengan nomor ' . $visit['visit_no'] . '.');
    redirect('/dashboard');
}

function dashboard_checkout(): void
{
    require_role('admin', 'petugas');
    verify_csrf();

    $id = (int) ($_POST['id'] ?? 0);
    $return = safe_return((string) ($_POST['return'] ?? '/dashboard'));

    $visit = Visit::find($id);
    if (!$visit) {
        flash('error', 'Data kunjungan tidak ditemukan.');
        redirect($return);
    }

    if (!Visit::checkout($id)) {
        flash('error', 'Tamu ini sudah tercatat keluar.');
        redirect($return);
    }

    $user = current_user();
    AuditLog::add((int) $user['id'], 'visit.checkout', 'visit', (string) $id);

    flash('success', 'Check-out ' . $visit['name'] . ' berhasil dicatat.');
    redirect($return);
}
