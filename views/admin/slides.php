<div class="card">
    <div class="card-head">
        <div>
            <h2>Tambah Infografis</h2>
            <p>Format JPG, PNG, atau WebP. Maksimal 5 MB. Gambar portrait lebih cocok untuk tablet.</p>
        </div>
    </div>

    <form method="post" action="/admin/slides/upload" enctype="multipart/form-data" class="toolbar" style="margin-bottom:0;">
        <?= csrf_field() ?>
        <div class="field grow">
            <label for="title">Judul</label>
            <input class="input" type="text" id="title" name="title" maxlength="150"
                   placeholder="Contoh: Pertumbuhan Ekonomi 2025">
        </div>
        <div class="field grow">
            <label for="image">File Gambar</label>
            <input class="input" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" required>
        </div>
        <button class="btn btn-primary" type="submit">Unggah</button>
    </form>
</div>

<div class="card">
    <div class="card-head">
        <div>
            <h2>Daftar Infografis</h2>
            <p>Total <?= count($slides) ?> gambar · durasi tiap slide <?= (int) $slideDuration ?> detik · urutan kecil tampil lebih dulu.</p>
        </div>
        <a class="btn btn-ghost" href="/admin/pengaturan">Atur Durasi</a>
    </div>

    <form id="orderForm" method="post" action="/admin/slides/order">
        <?= csrf_field() ?>
    </form>

    <?php if (empty($slides)): ?>
        <p class="muted">Belum ada infografis. Unggah gambar pertama di atas.</p>
    <?php else: ?>
        <div class="slide-grid">
            <?php foreach ($slides as $slide): ?>
                <div class="slide-card"<?= !(int) $slide['active'] ? ' style="opacity:.62;"' : '' ?>>
                    <div class="slide-thumb">
                        <img src="<?= e($slide['file_path']) ?>" alt="<?= e($slide['title']) ?>" loading="lazy">
                    </div>
                    <div class="slide-body">
                        <strong><?= e($slide['title']) ?></strong>
                        <div class="slide-meta">
                            <span>
                                <?php if ((int) $slide['active']): ?>
                                    <span class="badge badge-selesai">Tampil</span>
                                <?php else: ?>
                                    <span class="badge badge-muted">Nonaktif</span>
                                <?php endif; ?>
                            </span>
                            <label style="display:flex;align-items:center;gap:6px;">
                                Urutan
                                <input class="order-input" type="number" min="1" max="999"
                                       name="orders[<?= (int) $slide['id'] ?>]"
                                       value="<?= (int) $slide['sort_order'] ?>"
                                       form="orderForm">
                            </label>
                        </div>
                        <div class="slide-actions">
                            <form method="post" action="/admin/slides/toggle">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
                                <button class="btn btn-sm btn-ghost" type="submit">
                                    <?= (int) $slide['active'] ? 'Sembunyikan' : 'Tampilkan' ?>
                                </button>
                            </form>
                            <form method="post" action="/admin/slides/delete"
                                  data-confirm="Hapus infografis &quot;<?= e($slide['title']) ?>&quot;? File gambar juga akan dihapus.">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
                                <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="form-actions" style="justify-content:flex-start;">
            <button class="btn btn-primary" type="submit" form="orderForm">Simpan Urutan</button>
        </div>
    <?php endif; ?>
</div>
