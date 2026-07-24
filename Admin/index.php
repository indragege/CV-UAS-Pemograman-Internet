<?php

declare(strict_types=1);
require dirname(__DIR__) . '/functions.php';
$admin = require_admin();
$users = all_users(true);
$defaultId = default_user_id();
$success = flash('success');
$error = flash('error');
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Panel Administrator</title><link rel="stylesheet" href="<?= e(app_url('styles.css')) ?>"></head>
<body class="admin-body">
<aside class="admin-sidebar">
    <a class="brand brand-light" href="<?= e(app_url()) ?>"><span>CV</span> Admin</a>
    <nav><a class="active" href="<?= e(app_url('Admin/')) ?>">Dashboard</a><a href="<?= e(app_url('Admin/user_form.php')) ?>">Tambah Pengguna</a><a href="<?= e(app_url()) ?>">Lihat Front End</a></nav>
    <div class="admin-profile"><span>A</span><div><strong><?= e($admin['name']) ?></strong><small>Administrator</small></div></div>
    <a class="sidebar-logout" href="<?= e(app_url('logout.php')) ?>">Keluar</a>
</aside>
<main class="admin-main">
    <header class="admin-header"><div><span class="eyebrow dark">BACK END ADMINISTRATOR</span><h1>Manajemen CV Multi User</h1><p>Kelola akun pengguna dan tentukan CV yang tampil pada halaman utama.</p></div><a class="btn btn-primary" href="<?= e(app_url('Admin/user_form.php')) ?>">+ Tambah Pengguna</a></header>
    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <section class="admin-stats">
        <article><span>Total Pengguna</span><strong><?= count($users) ?></strong><small>Akun CV terdaftar</small></article>
        <article><span>Pengguna Aktif</span><strong><?= count(array_filter($users, static fn(array $u): bool => (bool) $u['active'])) ?></strong><small>Dapat diakses publik</small></article>
        <article><span>CV Default</span><strong><?php $defaultUser = find_user_by_id($defaultId); echo e($defaultUser['username'] ?? '-'); ?></strong><small>Tampil di halaman utama</small></article>
    </section>
    <section class="admin-panel">
        <div class="panel-heading"><div><h2>Daftar Pengguna</h2><p>Setiap username mempunyai URL CV publik sendiri.</p></div><span class="db-badge"><?= e(storage_label()) ?></span></div>
        <div class="table-wrap"><table class="admin-table"><thead><tr><th>Pengguna</th><th>Username / URL</th><th>Status</th><th>CV Default</th><th>Aksi</th></tr></thead><tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><div class="user-cell"><span><?= e(strtoupper(substr((string) $user['name'], 0, 1))) ?></span><div><strong><?= e($user['name']) ?></strong><small><?= e($user['email']) ?></small></div></div></td>
                <td><code>/<?= e($user['username']) ?></code><a class="tiny-link" href="<?= e(profile_url($user['username'])) ?>" target="_blank">Buka CV</a></td>
                <td><span class="status-pill <?= $user['active'] ? 'active' : 'inactive' ?>"><?= $user['active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                <td><?php if ((int) $user['id'] === $defaultId): ?><span class="default-pill">Default</span><?php elseif ($user['active']): ?><form method="post" action="<?= e(app_url('Admin/set_default.php')) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>"><button class="link-button" type="submit">Jadikan Default</button></form><?php else: ?><span class="muted">-</span><?php endif; ?></td>
                <td><div class="row-actions"><a href="<?= e(app_url('Admin/user_form.php?id=' . (int) $user['id'])) ?>">Edit</a><form method="post" action="<?= e(app_url('Admin/delete_user.php')) ?>" onsubmit="return confirm('Hapus akun dan seluruh data CV ini?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>"><button type="submit">Hapus</button></form></div></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$users): ?><tr><td colspan="5" class="empty-state">Belum ada pengguna.</td></tr><?php endif; ?>
        </tbody></table></div>
    </section>
    <section class="admin-notes"><h2>Cara Akses Aplikasi</h2><div class="access-grid"><div><strong>Administrator</strong><code><?= e(app_url('Admin/')) ?></code></div><div><strong>Login Pengguna</strong><code><?= e(app_url('login.php')) ?></code></div><div><strong>CV Berdasarkan Username</strong><code><?= e(profile_url('cecep_suwanda')) ?></code></div><div><strong>CV Default</strong><code><?= e(app_url()) ?></code></div></div></section>
</main>
</body>
</html>
