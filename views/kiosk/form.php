<?php
$value = static function (string $key) use ($old): string {
    return e((string) ($old[$key] ?? ''));
};
$error = static function (string $key) use ($errors): string {
    return isset($errors[$key]) ? (string) $errors[$key] : '';
};
?>
<div class="kiosk-page">
    <header class="kiosk-header">
        <div class="kiosk-brand">
            <img src="/assets/img/logo-bps.svg" alt="Logo BPS">
            <div>
                <strong><?= e($siteName) ?></strong>
                <span>Buku Tamu Digital</span>
            </div>
        </div>
        <a class="kiosk-back" href="/">← Beranda</a>
    </header>

    <main class="form-main">
        <div class="form-card">
            <h1>Isi Buku Tamu</h1>
            <p class="lead">Silakan lengkapi data kunjungan Anda. Semua kolom wajib diisi.</p>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-error"><?= e($errors['general']) ?></div>
            <?php endif; ?>

            <form class="visit-form" method="post" action="/isi" novalidate>
                <?= csrf_field() ?>

                <div class="field">
                    <label for="name">Nama Lengkap</label>
                    <input class="input<?= $error('name') !== '' ? ' is-invalid' : '' ?>"
                           type="text" id="name" name="name" value="<?= $value('name') ?>"
                           maxlength="100" autocomplete="name" autofocus
                           placeholder="Contoh: Ahmad Fauzi">
                    <?php if ($error('name') !== ''): ?>
                        <span class="error-text"><?= e($error('name')) ?></span>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="institution">Instansi / Asal</label>
                    <input class="input<?= $error('institution') !== '' ? ' is-invalid' : '' ?>"
                           type="text" id="institution" name="institution" value="<?= $value('institution') ?>"
                           maxlength="100" autocomplete="organization"
                           placeholder="Contoh: Dinas Pendidikan Kubu Raya">
                    <?php if ($error('institution') !== ''): ?>
                        <span class="error-text"><?= e($error('institution')) ?></span>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="phone">Nomor HP</label>
                    <input class="input<?= $error('phone') !== '' ? ' is-invalid' : '' ?>"
                           type="tel" id="phone" name="phone" value="<?= $value('phone') ?>"
                           maxlength="18" inputmode="tel" autocomplete="tel"
                           placeholder="Contoh: 081234567890">
                    <?php if ($error('phone') !== ''): ?>
                        <span class="error-text"><?= e($error('phone')) ?></span>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="notes">Keterangan / Keperluan</label>
                    <textarea class="textarea<?= $error('notes') !== '' ? ' is-invalid' : '' ?>"
                              id="notes" name="notes" maxlength="300"
                              placeholder="Contoh: Konsultasi data statistik kecamatan"><?= $value('notes') ?></textarea>
                    <?php if ($error('notes') !== ''): ?>
                        <span class="error-text"><?= e($error('notes')) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <a class="btn btn-ghost btn-lg" href="/">Batal</a>
                    <button class="btn btn-primary btn-lg" type="submit">Kirim</button>
                </div>
            </form>

            <p class="form-note">
                Data yang Anda isi digunakan hanya untuk keperluan buku tamu
                <?= e($siteName) ?>.
            </p>
        </div>
    </main>
</div>
