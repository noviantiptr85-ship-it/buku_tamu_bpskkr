<?php
declare(strict_types=1);

function admin_slides(): void
{
    require_role('admin');

    view('admin/slides', [
        'title' => 'Kelola Infografis',
        'slides' => Infographic::all(),
        'slideDuration' => setting_int('slide_duration', 5),
    ]);
}

function admin_slide_upload(): void
{
    require_role('admin');
    verify_csrf();

    $title = trim((string) ($_POST['title'] ?? ''));

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        flash('error', 'Pilih file gambar terlebih dahulu.');
        redirect('/admin/slides');
    }

    $file = $_FILES['image'];

    if ($file['size'] > 5 * 1024 * 1024) {
        flash('error', 'Ukuran gambar maksimal 5 MB.');
        redirect('/admin/slides');
    }

    $info = @getimagesize($file['tmp_name']);
    $allowed = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp',
    ];

    if ($info === false || !isset($allowed[$info[2]])) {
        flash('error', 'Format gambar harus JPG, PNG, atau WebP.');
        redirect('/admin/slides');
    }

    $extension = $allowed[$info[2]];
    $filename = 'slide-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.' . $extension;
    $directory = PUBLIC_PATH . '/assets/infografis';

    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        flash('error', 'Folder gambar tidak dapat dibuat.');
        redirect('/admin/slides');
    }

    $destination = $directory . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        flash('error', 'Gagal menyimpan gambar yang diunggah.');
        redirect('/admin/slides');
    }

    if ($title === '') {
        $title = 'Infografis ' . date('d/m/Y H:i');
    }

    $id = Infographic::create($title, '/assets/infografis/' . $filename, Infographic::nextSortOrder());
    AuditLog::add((int) current_user()['id'], 'slide.create', 'infographic', (string) $id, ['title' => $title]);

    flash('success', 'Infografis baru berhasil ditambahkan.');
    redirect('/admin/slides');
}

function admin_slide_toggle(): void
{
    require_role('admin');
    verify_csrf();

    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0 && Infographic::find($id)) {
        Infographic::toggle($id);
        AuditLog::add((int) current_user()['id'], 'slide.toggle', 'infographic', (string) $id);
        flash('success', 'Status tampil infografis diperbarui.');
    } else {
        flash('error', 'Infografis tidak ditemukan.');
    }

    redirect('/admin/slides');
}

function admin_slide_delete(): void
{
    require_role('admin');
    verify_csrf();

    $id = (int) ($_POST['id'] ?? 0);
    $row = $id > 0 ? Infographic::delete($id) : null;

    if ($row === null) {
        flash('error', 'Infografis tidak ditemukan.');
        redirect('/admin/slides');
    }

    $path = (string) $row['file_path'];
    if (str_starts_with($path, '/assets/infografis/')) {
        $file = PUBLIC_PATH . $path;
        if (is_file($file)) {
            @unlink($file);
        }
    }

    AuditLog::add((int) current_user()['id'], 'slide.delete', 'infographic', (string) $id, ['title' => $row['title']]);

    flash('success', 'Infografis berhasil dihapus.');
    redirect('/admin/slides');
}

function admin_slide_order(): void
{
    require_role('admin');
    verify_csrf();

    $orders = $_POST['orders'] ?? [];
    if (is_array($orders)) {
        $clean = [];
        foreach ($orders as $id => $order) {
            $clean[(int) $id] = (int) $order;
        }
        Infographic::updateOrders($clean);
        AuditLog::add((int) current_user()['id'], 'slide.order', 'infographic', null, $clean);
        flash('success', 'Urutan infografis disimpan.');
    }

    redirect('/admin/slides');
}

function admin_settings(): void
{
    require_role('admin');

    view('admin/settings', [
        'title' => 'Pengaturan',
        'settings' => Setting::all(),
    ]);
}

function admin_settings_save(): void
{
    require_role('admin');
    verify_csrf();

    $siteName = trim((string) ($_POST['site_name'] ?? ''));
    $welcome = trim((string) ($_POST['welcome_text'] ?? ''));
    $running = trim((string) ($_POST['running_text'] ?? ''));
    $slideDuration = (int) ($_POST['slide_duration'] ?? 5);
    $successDuration = (int) ($_POST['success_duration'] ?? 5);

    if (mb_strlen($siteName) < 2 || mb_strlen($siteName) > 100) {
        flash('error', 'Nama instansi harus 2–100 karakter.');
        redirect('/admin/pengaturan');
    }
    if (mb_strlen($welcome) > 200) {
        flash('error', 'Teks sapaan maksimal 200 karakter.');
        redirect('/admin/pengaturan');
    }
    if (mb_strlen($running) > 300) {
        flash('error', 'Running text maksimal 300 karakter.');
        redirect('/admin/pengaturan');
    }
    if ($slideDuration < 3 || $slideDuration > 15 || $successDuration < 3 || $successDuration > 15) {
        flash('error', 'Durasi slide dan layar sukses harus antara 3–15 detik.');
        redirect('/admin/pengaturan');
    }

    Setting::setMany([
        'site_name' => $siteName,
        'welcome_text' => $welcome,
        'running_text' => $running,
        'slide_duration' => (string) $slideDuration,
        'success_duration' => (string) $successDuration,
    ]);

    AuditLog::add((int) current_user()['id'], 'settings.update', 'settings');

    flash('success', 'Pengaturan berhasil disimpan.');
    redirect('/admin/pengaturan');
}

function admin_users(): void
{
    require_role('admin');

    view('admin/users', [
        'title' => 'Pengguna',
        'users' => User::all(),
    ]);
}

function admin_user_create(): void
{
    require_role('admin');
    verify_csrf();

    $name = trim((string) ($_POST['name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $role = (string) ($_POST['role'] ?? 'petugas');

    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        flash('error', 'Nama pengguna harus 2–100 karakter.');
        redirect('/admin/users');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Format email tidak valid.');
        redirect('/admin/users');
    }
    if (strlen($password) < 8) {
        flash('error', 'Password minimal 8 karakter.');
        redirect('/admin/users');
    }
    if (!in_array($role, ['admin', 'petugas', 'pimpinan'], true)) {
        flash('error', 'Peran tidak valid.');
        redirect('/admin/users');
    }
    if (User::emailExists($email)) {
        flash('error', 'Email sudah digunakan.');
        redirect('/admin/users');
    }

    $id = User::create($name, $email, $password, $role);
    AuditLog::add((int) current_user()['id'], 'user.create', 'user', (string) $id, ['email' => $email, 'role' => $role]);

    flash('success', 'Pengguna baru berhasil ditambahkan.');
    redirect('/admin/users');
}

function admin_user_toggle(): void
{
    require_role('admin');
    verify_csrf();

    $id = (int) ($_POST['id'] ?? 0);
    $me = (int) current_user()['id'];

    if ($id === $me) {
        flash('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        redirect('/admin/users');
    }

    $user = User::find($id);
    if (!$user) {
        flash('error', 'Pengguna tidak ditemukan.');
        redirect('/admin/users');
    }

    $newState = !(int) $user['active'];
    User::setActive($id, $newState);
    AuditLog::add($me, $newState ? 'user.activate' : 'user.deactivate', 'user', (string) $id);

    flash('success', 'Status pengguna diperbarui.');
    redirect('/admin/users');
}

function admin_user_password(): void
{
    require_role('admin');
    verify_csrf();

    $id = (int) ($_POST['id'] ?? 0);
    $password = (string) ($_POST['password'] ?? '');

    if (strlen($password) < 8) {
        flash('error', 'Password minimal 8 karakter.');
        redirect('/admin/users');
    }

    if (!User::find($id)) {
        flash('error', 'Pengguna tidak ditemukan.');
        redirect('/admin/users');
    }

    User::updatePassword($id, $password);
    AuditLog::add((int) current_user()['id'], 'user.password', 'user', (string) $id);

    flash('success', 'Password pengguna berhasil diubah.');
    redirect('/admin/users');
}

function admin_audit(): void
{
    require_role('admin');

    view('admin/audit', [
        'title' => 'Audit Log',
        'logs' => AuditLog::recent(150),
    ]);
}
