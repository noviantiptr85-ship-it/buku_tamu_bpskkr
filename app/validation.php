<?php
declare(strict_types=1);

/**
 * Validasi form buku tamu (dipakai kiosk dan input manual petugas).
 * Mengembalikan array error per field; kosong berarti valid.
 */
function validate_visit_input(array $data): array
{
    $errors = [];

    $name = trim((string) ($data['name'] ?? ''));
    if ($name === '') {
        $errors['name'] = 'Nama wajib diisi.';
    } elseif (mb_strlen($name) < 3) {
        $errors['name'] = 'Nama minimal 3 karakter.';
    } elseif (mb_strlen($name) > 100) {
        $errors['name'] = 'Nama maksimal 100 karakter.';
    }

    $institution = trim((string) ($data['institution'] ?? ''));
    if ($institution === '') {
        $errors['institution'] = 'Instansi/asal wajib diisi.';
    } elseif (mb_strlen($institution) < 2) {
        $errors['institution'] = 'Instansi/asal minimal 2 karakter.';
    } elseif (mb_strlen($institution) > 100) {
        $errors['institution'] = 'Instansi/asal maksimal 100 karakter.';
    }

    $phoneRaw = trim((string) ($data['phone'] ?? ''));
    if ($phoneRaw === '') {
        $errors['phone'] = 'Nomor HP wajib diisi.';
    } else {
        $phone = normalize_phone($phoneRaw);
        if (!valid_phone($phone)) {
            $errors['phone'] = 'Nomor HP tidak valid. Contoh: 081234567890.';
        }
    }

    $notes = trim((string) ($data['notes'] ?? ''));
    if ($notes === '') {
        $errors['notes'] = 'Keterangan wajib diisi.';
    } elseif (mb_strlen($notes) < 3) {
        $errors['notes'] = 'Keterangan minimal 3 karakter.';
    } elseif (mb_strlen($notes) > 300) {
        $errors['notes'] = 'Keterangan maksimal 300 karakter.';
    }

    return $errors;
}
