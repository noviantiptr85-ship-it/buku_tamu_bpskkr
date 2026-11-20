<div class="success-screen" data-seconds="<?= (int) $successDuration ?>" data-redirect="/">
    <div class="success-card">
        <div class="success-icon">✓</div>
        <h1>Terima kasih<?= $visit['name'] !== '' ? ', ' . e($visit['name']) : '' ?>!</h1>
        <p class="muted">Kunjungan Anda telah tercatat.</p>

        <div class="success-meta">
            <div>
                <span>Nomor kunjungan</span>
                <strong><?= e($visit['visit_no']) ?></strong>
            </div>
            <div>
                <span>Jam masuk</span>
                <strong><?= e(jam($visit['check_in_at'])) ?> WIB</strong>
            </div>
        </div>

        <p class="countdown-text">
            Kembali ke halaman utama dalam <strong id="countdown"><?= (int) $successDuration ?></strong> detik
        </p>
        <div class="progress"><div class="progress-bar" id="countdown-bar"></div></div>

        <a class="btn btn-primary btn-lg" href="/">Selesai</a>
    </div>
</div>
