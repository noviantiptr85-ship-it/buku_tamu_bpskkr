<div class="auth-card">
    <div class="auth-brand">
        <img src="/assets/img/logo-bps.svg" alt="Logo BPS">
        <div>
            <strong><?= e($siteName) ?></strong>
            <small>Buku Tamu Digital</small>
        </div>
    </div>

    <h1>Masuk</h1>
    <p class="muted">Gunakan akun petugas atau admin untuk mengelola kunjungan.</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/login">
        <?= csrf_field() ?>

        <div class="field">
            <label for="email">Email</label>
            <input class="input" type="email" id="email" name="email" value="<?= e($email ?? '') ?>"
                   autocomplete="username" required autofocus>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input class="input" type="password" id="password" name="password"
                   autocomplete="current-password" required>
        </div>

        <button class="btn btn-primary btn-block btn-lg" type="submit">Masuk</button>
    </form>

    <?php if (APP_ENV === 'local'): ?>
        <div class="demo-box">
            <strong>Akun demo:</strong><br>
            Admin: admin@bpskuburaya.local / Admin#BPS2026<br>
            Petugas: petugas@bpskuburaya.local / Petugas#BPS2026<br>
            Pimpinan: pimpinan@bpskuburaya.local / Pimpinan#BPS2026
        </div>
    <?php endif; ?>

    <p class="hint" style="text-align:center;margin:16px 0 0;">
        <a href="/">← Kembali ke tampilan kiosk</a>
    </p>
</div>
