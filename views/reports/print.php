<?php
$siteName = setting('site_name', 'BPS Kabupaten Kubu Raya');
?>
<div class="print-toolbar">
    <a class="btn btn-ghost" href="/laporan">← Kembali</a>
    <button class="btn btn-primary" type="button" onclick="window.print()">Cetak / Simpan PDF</button>
</div>

<div class="print-page">
    <div style="display:flex;align-items:center;gap:16px;border-bottom:3px double #1F2937;padding-bottom:14px;margin-bottom:18px;">
        <img src="/assets/img/logo-bps.svg" alt="Logo BPS" style="width:64px;height:64px;">
        <div style="flex:1;">
            <div style="font-size:16px;font-weight:800;letter-spacing:.02em;">BADAN PUSAT STATISTIK</div>
            <div style="font-size:20px;font-weight:800;text-transform:uppercase;"><?= e($siteName) ?></div>
            <div style="font-size:12px;color:#475569;">Kabupaten Kubu Raya, Kalimantan Barat</div>
        </div>
    </div>

    <h1 style="font-size:18px;text-align:center;margin:18px 0 4px;">LAPORAN BUKU TAMU</h1>
    <p style="text-align:center;margin:0 0 18px;font-size:13px;">
        Periode <?= e(tanggal_id($filters['from'], false)) ?> s.d. <?= e(tanggal_id($filters['to'], false)) ?>
    </p>

    <div style="display:flex;gap:18px;flex-wrap:wrap;font-size:13px;margin-bottom:14px;">
        <div><strong>Total kunjungan:</strong> <?= (int) $summary['total'] ?></div>
        <div><strong>Selesai:</strong> <?= (int) $summary['selesai'] ?></div>
        <div><strong>Belum check-out:</strong> <?= (int) $summary['aktif'] ?></div>
        <div><strong>Rata-rata durasi:</strong> <?= (int) $summary['rata_menit'] ?> menit</div>
    </div>

    <table>
        <thead>
        <tr>
            <th style="width:38px;">No</th>
            <th>Nomor</th>
            <th>Nama</th>
            <th>Instansi / Asal</th>
            <th>No HP</th>
            <th>Keterangan</th>
            <th>Masuk</th>
            <th>Keluar</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($visits)): ?>
            <tr><td colspan="8" class="empty">Tidak ada data pada periode ini.</td></tr>
        <?php else: ?>
            <?php foreach ($visits as $i => $visit): ?>
                <tr>
                    <td class="mono"><?= $i + 1 ?></td>
                    <td class="mono"><?= e($visit['visit_no']) ?></td>
                    <td><?= e($visit['name']) ?></td>
                    <td><?= e($visit['institution']) ?></td>
                    <td class="mono"><?= e(phone_for_view($visit['phone'])) ?></td>
                    <td><?= e($visit['notes']) ?></td>
                    <td class="mono nowrap"><?= e(date('d/m/Y H:i', strtotime($visit['check_in_at']))) ?></td>
                    <td class="mono nowrap"><?= $visit['check_out_at'] ? e(date('d/m/Y H:i', strtotime($visit['check_out_at']))) : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="display:flex;justify-content:flex-end;margin-top:36px;font-size:13px;">
        <div style="text-align:center;min-width:260px;">
            <div>Kubu Raya, <?= e(tanggal_id(date('Y-m-d'), false)) ?></div>
            <div style="margin-top:6px;">Petugas Buku Tamu</div>
            <div style="height:70px;"></div>
            <div style="border-top:1px solid #1F2937;padding-top:4px;">(...................................)</div>
        </div>
    </div>
</div>
