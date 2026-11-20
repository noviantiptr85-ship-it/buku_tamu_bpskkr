<?php
$queryFilters = array_filter($filters, static fn ($value) => $value !== '' && $value !== null);
$baseQuery = http_build_query($queryFilters);
$exportCsv = '/laporan/export?format=csv' . ($baseQuery !== '' ? '&' . $baseQuery : '');
$exportXls = '/laporan/export?format=xls' . ($baseQuery !== '' ? '&' . $baseQuery : '');
$printUrl = '/laporan/print' . ($baseQuery !== '' ? '?' . $baseQuery : '');
?>
<div class="stats">
    <div class="stat accent">
        <div class="num"><?= (int) $summary['total'] ?></div>
        <div class="label">Total kunjungan</div>
    </div>
    <div class="stat">
        <div class="num"><?= (int) $summary['selesai'] ?></div>
        <div class="label">Selesai</div>
    </div>
    <div class="stat">
        <div class="num"><?= (int) $summary['aktif'] ?></div>
        <div class="label">Belum check-out</div>
    </div>
    <div class="stat">
        <div class="num"><?= (int) $summary['rata_menit'] ?><span style="font-size:14px;font-weight:600;color:var(--muted);"> mnt</span></div>
        <div class="label">Rata-rata kunjungan</div>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <div>
            <h2>Filter Laporan</h2>
            <p>Periode: <?= e(tanggal_id($filters['from'], false)) ?> – <?= e(tanggal_id($filters['to'], false)) ?></p>
        </div>
        <div class="actions">
            <a class="btn btn-ghost" href="<?= e($exportCsv) ?>">Export CSV</a>
            <a class="btn btn-ghost" href="<?= e($exportXls) ?>">Export Excel</a>
            <a class="btn btn-primary" href="<?= e($printUrl) ?>" target="_blank" rel="noopener">Cetak PDF</a>
        </div>
    </div>

    <form class="toolbar" method="get" action="/laporan">
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
        <button class="btn btn-primary" type="submit">Terapkan</button>
        <a class="btn btn-ghost" href="/laporan">Bulan Ini</a>
    </form>

    <div class="report-grid">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th style="width:44px;">No</th>
                    <th>Masuk</th>
                    <th>Nama</th>
                    <th>Instansi</th>
                    <th>No HP</th>
                    <th>Durasi</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($visits)): ?>
                    <tr><td colspan="7" class="empty">Tidak ada kunjungan pada periode ini.</td></tr>
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
                            <td class="nowrap muted"><?= durasi($visit['check_in_at'], $visit['check_out_at']) ?></td>
                            <td>
                                <?php if ($visit['status'] === 'selesai'): ?>
                                    <span class="badge badge-selesai">Selesai</span>
                                <?php else: ?>
                                    <span class="badge badge-aktif">Aktif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card" style="margin-top:0;">
            <div class="card-head">
                <div>
                    <h2>Instansi Terbanyak</h2>
                    <p>5 besar pada periode ini</p>
                </div>
            </div>
            <?php if (empty($top)): ?>
                <p class="muted small">Belum ada data.</p>
            <?php else: ?>
                <ol class="rank-list">
                    <?php foreach ($top as $row): ?>
                        <li>
                            <span><?= e($row['institution']) ?></span>
                            <span class="count"><?= (int) $row['total'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </div>
    </div>
</div>
