<?php
$value = static function (string $key) use ($old): string {
    return e((string) ($old[$key] ?? ''));
};
$error = static function (string $key) use ($errors): string {
    return isset($errors[$key]) ? (string) $errors[$key] : '';
};
?>
<div class="card" style="max-width:760px;">
    <div class="card-head">
        <div>
            <h2>Tambah Tamu Manual</h2>
            <p>Gunakan form ini jika tamu tidak dapat mengisi sendiri di kiosk.</p>
        </div>
        <a class="btn btn-ghost" href="/dashboard">← Kembali</a>
    </div>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error"><?= e($errors['general']) ?></div>
    <?php endif; ?>

    <form method="post" action="/dashboard/tambah" novalidate>
        <?= csrf_field() ?>

        <div class="form-grid">
            <div class="field span-2">
                <label for="name">Nama Lengkap</label>
                <input class="input<?= $error('name') !== '' ? ' is-invalid' : '' ?>"
                       type="text" id="name" name="name" value="<?= $value('name') ?>"
                       maxlength="100" required>
                <?php if ($error('name') !== ''): ?><span class="error-text"><?= e($error('name')) ?></span><?php endif; ?>
            </div>

            <div class="field">
                <label for="institution">Instansi / Asal</label>
                <input class="input<?= $error('institution') !== '' ? ' is-invalid' : '' ?>"
                       type="text" id="institution" name="institution" value="<?= $value('institution') ?>"
                       maxlength="100" required>
                <?php if ($error('institution') !== ''): ?><span class="error-text"><?= e($error('institution')) ?></span><?php endif; ?>
            </div>

            <div class="field">
                <label for="phone">Nomor HP</label>
                <input class="input<?= $error('phone') !== '' ? ' is-invalid' : '' ?>"
                       type="tel" id="phone" name="phone" value="<?= $value('phone') ?>"
                       maxlength="18" inputmode="tel" required>
                <?php if ($error('phone') !== ''): ?><span class="error-text"><?= e($error('phone')) ?></span><?php endif; ?>
            </div>

            <div class="field span-2">
                <label for="notes">Keterangan / Keperluan</label>
                <textarea class="textarea<?= $error('notes') !== '' ? ' is-invalid' : '' ?>"
                          id="notes" name="notes" maxlength="300" required><?= $value('notes') ?></textarea>
                <?php if ($error('notes') !== ''): ?><span class="error-text"><?= e($error('notes')) ?></span><?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <a class="btn btn-ghost" href="/dashboard">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
    </form>
</div>
