<div class="card">
    <div class="card-head">
        <div>
            <h2>Audit Log</h2>
            <p>150 aktivitas terakhir. Berguna untuk menelusuri perubahan data.</p>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Waktu</th>
                <th>Pengguna</th>
                <th>Aksi</th>
                <th>Entitas</th>
                <th>ID</th>
                <th>Detail</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($logs)): ?>
                <tr><td colspan="6" class="empty">Belum ada aktivitas tercatat.</td></tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="nowrap small"><?= e(tanggal_id($log['created_at'])) ?></td>
                        <td><?= e($log['user_name'] ?? 'Kiosk / Sistem') ?></td>
                        <td><span class="badge badge-muted"><?= e($log['action']) ?></span></td>
                        <td class="small"><?= e($log['entity']) ?></td>
                        <td class="mono small"><?= e((string) ($log['entity_id'] ?? '-')) ?></td>
                        <td class="small muted">
                            <?php
                            $meta = (string) ($log['meta'] ?? '');
                            echo e(mb_strlen($meta) > 90 ? mb_substr($meta, 0, 90) . '…' : $meta);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
