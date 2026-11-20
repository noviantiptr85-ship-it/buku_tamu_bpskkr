<header class="kiosk-header">
    <div class="kiosk-brand">
        <img src="/assets/img/logo-bps.svg" alt="Logo BPS">
        <div>
            <strong><?= e($siteName) ?></strong>
            <span>Buku Tamu Digital</span>
        </div>
    </div>
    <div class="kiosk-clock">
        <span id="clock">--:--</span>
        <small id="date">&nbsp;</small>
    </div>
</header>

<main class="kiosk-main">
    <?php if (!empty($slides)): ?>
        <div class="slideshow" id="slideshow" data-interval="<?= (int) $slideDuration ?>">
            <div class="slides-stage">
                <?php foreach ($slides as $i => $slide): ?>
                    <figure class="slide<?= $i === 0 ? ' is-active' : '' ?>" data-index="<?= (int) $i ?>">
                        <div class="slide-bg" style="background-image:url('<?= e($slide['file_path']) ?>')"></div>
                        <img src="<?= e($slide['file_path']) ?>" alt="<?= e($slide['title']) ?>">
                    </figure>
                <?php endforeach; ?>
            </div>

            <?php if (count($slides) > 1): ?>
                <div class="slides-dots">
                    <?php foreach ($slides as $i => $slide): ?>
                        <button type="button" class="dot<?= $i === 0 ? ' is-active' : '' ?>"
                                data-index="<?= (int) $i ?>"
                                aria-label="Tampilkan infografis <?= (int) ($i + 1) ?>"></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="slideshow-empty">
            <div>
                <h2>Selamat datang</h2>
                <p>Belum ada infografis yang ditampilkan. Silakan hubungi petugas.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="kiosk-cta-wrap">
        <a class="kiosk-cta" href="/isi">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            <span>Isi Buku Tamu</span>
        </a>
        <p class="kiosk-welcome"><?= e($welcomeText) ?></p>
    </div>
</main>

<footer class="kiosk-footer">
    <?php if (!empty($runningText)): ?>
        <div class="ticker"><span><?= e($runningText) ?></span></div>
    <?php else: ?>
        <span><?= e($siteName) ?> · Kalimantan Barat</span>
    <?php endif; ?>
</footer>
