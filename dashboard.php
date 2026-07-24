<?php

declare(strict_types=1);
require __DIR__ . '/functions.php';
$user = require_user();
$cv = find_profile_by_user_id((int) $user['id']) ?? blank_profile((string) $user['name']);
$success = flash('success');
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dashboard Pengguna</title><link rel="stylesheet" href="<?= e(app_url('styles.css')) ?>"></head>
<body class="dashboard-body">
<nav class="dashboard-nav"><a class="brand brand-light" href="<?= e(app_url()) ?>"><span>CV</span> Multi User</a><div><a href="<?= e(profile_url($user['username'])) ?>">Lihat CV</a><a href="<?= e(app_url('logout.php')) ?>">Keluar</a></div></nav>
<main class="dashboard-shell">
    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <header class="dashboard-hero"><div><span class="eyebrow">DASHBOARD PENGGUNA</span><h1>Halo, <?= e($user['name']) ?></h1><p>Kelola data CV dan bagikan halaman publik Anda.</p></div><a class="btn btn-light" href="<?= e(app_url('edit.php')) ?>">Edit Data CV</a></header>
    <section class="metric-grid">
        <article class="metric-card"><span>Username</span><strong><?= e($user['username']) ?></strong><small>Alamat publik unik</small></article>
        <article class="metric-card"><span>Status Akun</span><strong class="status-active">Aktif</strong><small>Dapat diakses publik</small></article>
        <article class="metric-card"><span>Terakhir Diubah</span><strong><?= e(date('d M Y', strtotime((string) $cv['updated_at']))) ?></strong><small><?= e(date('H:i', strtotime((string) $cv['updated_at']))) ?> WIB</small></article>
    </section>
    <section class="dashboard-grid">
        <article class="panel public-link-panel"><div><span class="panel-kicker">URL CV PUBLIK</span><h2><?= e(profile_url($user['username'])) ?></h2><p>Alamat ini dapat dibagikan dan dibuka tanpa login.</p></div><button class="btn btn-secondary" type="button" data-copy="<?= e(profile_url($user['username'])) ?>" onclick="navigator.clipboard.writeText(location.origin + this.dataset.copy);this.textContent='Tersalin'">Salin Link</button></article>
        <article class="panel profile-preview"><img src="<?= e(app_url($cv['photo_path'])) ?>" alt="Foto profil"><div><span class="panel-kicker">RINGKASAN CV</span><h2><?= e($cv['name']) ?></h2><p><?= e($cv['title']) ?></p><div class="preview-actions"><a class="btn btn-primary" href="<?= e(app_url('edit.php')) ?>">Ubah CV</a><a class="btn btn-secondary" href="<?= e(profile_url($user['username'])) ?>">Buka Halaman</a></div></div></article>
        <article class="panel info-panel"><h2>Fitur Akun Pengguna</h2><ul><li>Mengubah identitas, pendidikan, pengalaman, keahlian, dan portofolio.</li><li>Mengunggah foto profil dengan validasi format dan ukuran.</li><li>Melihat CV melalui URL berdasarkan username.</li><li>Mencetak halaman CV langsung dari browser.</li></ul></article>
    </section>
</main>
</body>
</html>
