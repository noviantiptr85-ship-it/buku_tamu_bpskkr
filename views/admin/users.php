<?php $me = (int) (current_user()['id'] ?? 0); ?>
<div class="card">
    <div class="card-head">
        <div>
            <h2>Tambah Pengguna</h2>
            <p>Admin dapat mengelola semua data. Petugas mencatat kunjungan. Pimpinan hanya melihat laporan.</p>
        </div>
    </div>

    <form method="post" action="/admin/users/create" class="toolbar" style="margin-bottom:0;">
        <?= csrf_field() ?>
        <div class="field grow">
            <label for="name">Nama</label>
            <input class="input" type="text" id="name" name="name" maxlength="100" required>
        </div>
        <div class="field grow">
            <label for="email">Email</label>
            <input class="input" type="email" id="email" name="email" maxlength="150" required>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input class="input" type="password" id="password" name="password" minlength="8" required>
        </div>
        <div class="field">
            <label for="role">Peran</label>
            <select class="select" id="role" name="role">
                <option value="petugas">Petugas</option>
                <option value="admin">Admin</option>
                <option value="pimpinan">Pimpinan</option>
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Tambah</button>
    </form>
</div>

<div class="card">
    <div class="card-head">
        <div>
            <h2>Daftar Pengguna</h2>
            <p>Total <?= count($users) ?> akun.</p>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Peran</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th style="min-width:260px;">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><strong><?= e($user['name']) ?></strong><?= (int) $user['id'] === $me ? ' <span class="badge badge-role">Anda</span>' : '' ?></td>
                    <td class="mono"><?= e($user['email']) ?></td>
                    <td><span class="badge badge-role"><?= e(ucfirst($user['role'])) ?></span></td>
                    <td>
                        <?php if ((int) $user['active']): ?>
                            <span class="badge badge-selesai">Aktif</span>
                        <?php else: ?>
                            <span class="badge badge-muted">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="small muted nowrap"><?= e(tanggal_id($user['created_at'], false)) ?></td>
                    <td>
                        <div class="actions">
                            <?php if ((int) $user['id'] !== $me): ?>
                                <form method="post" action="/admin/users/toggle">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                    <button class="btn btn-sm btn-ghost" type="submit">
                                        <?= (int) $user['active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <form method="post" action="/admin/users/password" class="row" style="gap:6px;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                <input class="input" type="password" name="password" minlength="8"
                                       placeholder="Password baru" style="width:150px;padding:6px 9px;" required>
                                <button class="btn btn-sm btn-ghost" type="submit">Reset</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
