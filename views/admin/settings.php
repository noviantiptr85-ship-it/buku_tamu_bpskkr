<?php
$current = static function (string $key, string $default = '') use ($settings): string {
    return e((string) ($settings[$key] ?? $default));
};
?>
<div class="card" style="max-width:820px;">
    <div class="card-head">
        <div>
            <h2>Pengaturan Aplikasi</h2>
            <p>Perubahan langsung berlaku setelah disimpan.</p>
        </div>
    </div>

    <form method="post" action="/admin/pengaturan" novalidate>
        <?= csrf_field() ?>

        <div class="form-grid">
            <div class="field span-2">
                <label for="site_name">Nama Instansi</label>
                <input class="input" type="text" id="site_name" name="site_name"
                       value="<?= $current('site_name', 'BPS Kabupaten Kubu Raya') ?>" maxlength="100" required>
            </div>

            <div class="field span-2">
                <label for="welcome_text">Teks Sapaan (bawah tombol kiosk)</label>
                <input class="input" type="text" id="welcome_text" name="welcome_text"
                       value="<?= $current('welcome_text', 'Selamat datang di BPS Kabupaten Kubu Raya') ?>" maxlength="200">
            </div>

            <div class="field span-2">
                <label for="running_text">Running Text (opsional, kosongkan jika tidak perlu)</label>
                <input class="input" type="text" id="running_text" name="running_text"
                       value="<?= $current('running_text', '') ?>" maxlength="300"
                       placeholder="Contoh: Selamat datang, jam layanan Senin–Jumat 08.00–15.30 WIB">
            </div>

            <div class="field">
                <label for="slide_duration">Durasi Slide (detik)</label>
                <input class="input" type="number" id="slide_duration" name="slide_duration"
                       value="<?= $current('slide_duration', '5') ?>" min="3" max="15" required>
                <span class="hint">3–15 detik per gambar infografis.</span>
            </div>

            <div class="field">
                <label for="success_duration">Durasi Layar Sukses (detik)</label>
                <input class="input" type="number" id="success_duration" name="success_duration"
                       value="<?= $current('success_duration', '5') ?>" min="3" max="15" required>
                <span class="hint">3–15 detik sebelum kembali ke slideshow.</span>
            </div>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Simpan Pengaturan</button>
        </div>
    </form>
</div>

<div class="card" style="max-width:820px;">
    <div class="card-head">
        <div>
            <h2>Catatan Keamanan</h2>
            <p>Sebelum aplikasi dipakai produksi:</p>
        </div>
    </div>
    <ul class="muted small" style="margin:0;padding-left:18px;line-height:1.9;">
        <li>Ganti password akun demo (admin/petugas/pimpinan).</li>
        <li>Isi <code>DB_PASS</code> dan buat user database khusus, jangan memakai root.</li>
        <li>Set <code>APP_ENV=production</code> agar detail error tidak tampil.</li>
        <li>Aktifkan HTTPS dan backup database harian.</li>
    </ul>
</div>
