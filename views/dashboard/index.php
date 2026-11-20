<?php
$query = http_build_query($filters);
$returnUrl = '/dashboard?' . $query;
?>
<div class="stats">
    <div class="stat accent">
        <div class="num"><?= (int) $stats['total'] ?></div>
        <div class="label">Tamu hari ini</div>
    </div>
    <div class="stat">
        <div class="num"><?= (int) $stats['aktif'] ?></div>
        <div class="label">Masih di kantor</div>
    </div>
    <div class="stat">
        <div class="num"><?= (int) $stats['selesai'] ?></div>
        <div class="label">Sudah check-out</div>
    </div>
    <div class="stat">
        <div class="num"><?= (int) $stats['rata_menit'] ?><span style="font-size:14px;font-weight:600;color:var(--muted);"> mnt</span></div>
        <div class="label">Rata-rata kunjungan</div>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <div>
            <h2>Daftar Tamu</h2>
            <p>Menampilkan data sesuai filter. Maksimal 500 baris terbaru.</p>
        </div>
        <?php if ($canManage): ?>
            <a class="btn btn-primary" href="/dashboard/tambah">+ Tambah Tamu Manual</a>
        <?php endif; ?>
    </div>

    <form class="toolbar" method="get" action="/dashboard">
        <div class="field grow">
            <label for="q">Cari</label>
            <input class="input" type="search" id="q" name="q" value="<?= e($filters['q']) ?>"
                   placeholder="Nama, instansi, atau nomor HP">
        </div>
        <div class="field">
            <label for="from">Dari</label>
            <input class="input" type="date" id="from" name="from" value="<?= e($filters['from']) ?>">
        </div>
        <div class="field">
            <label for="to">Sampai</label>
            <input class="input" type="date" id="to" name="to" value="<?= e($filters['to']) ?>">
        </div>
        <div class="field">
            <label for="status">Status</label>
            <select class="select" id="status" name="status">
                <option value="">Semua</option>
                <option value="aktif"<?= $filters['status'] === 'aktif' ? ' selected' : '' ?>>Aktif</option>
                <option value="selesai"<?= $filters['status'] === 'selesai' ? ' selected' : '' ?>>Selesai</option>
            </select>
        </div>
        <button class="btn btn-ghost" type="submit">Terapkan</button>
        <a class="btn btn-ghost" href="/dashboard">Hari Ini</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th style="width:44px;">No</th>
                <th>Masuk</th>
                <th>Nama</th>
                <th>Instansi / Asal</th>
                <th>No HP</th>
                <th>Keterangan</th>
                <th>Status</th>
                <th>Durasi</th>
                <?php if ($canManage): ?><th>Aksi</th><?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($visits)): ?>
                <tr>
                    <td colspan="<?= $canManage ? 9 : 8 ?>" class="empty">
                        Belum ada data kunjungan pada rentang tanggal ini.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($visits as $i => $visit): ?>
                    <tr>
                        <td class="mono muted"><?= $i + 1 ?></td>
                        <td class="nowrap mono">
                            <?= e(jam($visit['check_in_at'])) ?>
                            <div class="small muted"><?= e(date('d/m/Y', strtotime($visit['check_in_at']))) ?></div>
                        </td>
                        <td>
                            <strong><?= e($visit['name']) ?></strong>
                            <div class="small muted mono"><?= e($visit['visit_no']) ?></div>
                        </td>
                        <td><?= e($visit['institution']) ?></td>
                        <td class="mono nowrap"><?= e(phone_for_view($visit['phone'])) ?></td>
                        <td>
                            <?php $notes = (string) $visit['notes']; ?>
                            <span title="<?= e($notes) ?>"><?= e(mb_strlen($notes) > 70 ? mb_substr($notes, 0, 70) . '…' : $notes) ?></span>
                        </td>
                        <td>
                            <?php if ($visit['status'] === 'selesai'): ?>
                                <span class="badge badge-selesai">Selesai</span>
                            <?php else: ?>
                                <span class="badge badge-aktif">Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="nowrap muted"><?= durasi($visit['check_in_at'], $visit['check_out_at']) ?></td>
                        <?php if ($canManage): ?>
                            <td>
                                <?php if ($visit['status'] === 'aktif'): ?>
                                    <form method="post" action="/visits/checkout"
                                          data-confirm="Catat check-out untuk <?= e($visit['name']) ?>?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $visit['id'] ?>">
                                        <input type="hidden" name="return" value="<?= e($returnUrl) ?>">
                                        <button class="btn btn-sm btn-primary" type="submit">Check-out</button>
                                    </form>
                                <?php else: ?>
                                    <span class="small muted nowrap"><?= e(jam($visit['check_out_at'])) ?> WIB</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
