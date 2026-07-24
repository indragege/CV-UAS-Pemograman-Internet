<?php

declare(strict_types=1);
require __DIR__ . '/functions.php';
$user = require_user();
$cv = $_SESSION['form_data'] ?? find_profile_by_user_id((int) $user['id']) ?? blank_profile((string) $user['name']);
$errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_data'], $_SESSION['form_errors']);
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Edit CV - <?= e($cv['name']) ?></title><link rel="stylesheet" href="<?= e(app_url('styles.css')) ?>"></head>
<body class="editor-body">
<main class="editor-shell">
    <header class="editor-header"><div><span class="eyebrow">BACK END PENGGUNA</span><h1>Edit Data Curriculum Vitae</h1><p>Perubahan hanya diterapkan pada CV milik akun <?= e($user['username']) ?>.</p></div><div class="header-actions"><span class="db-badge"><?= e(storage_label()) ?></span><a class="btn btn-light" href="<?= e(app_url('dashboard.php')) ?>">Kembali ke Dashboard</a></div></header>
    <?php if ($errors): ?><section class="alert alert-error"><strong>Data belum dapat disimpan:</strong><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></section><?php endif; ?>
    <form class="edit-form" action="<?= e(app_url('save.php')) ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <section class="form-section"><h2>Identitas dan Kontak</h2><div class="form-grid">
            <label>Nama lengkap<input name="name" required value="<?= e($cv['name']) ?>"></label>
            <label>Identitas/Jabatan<input name="title" required value="<?= e($cv['title']) ?>"></label>
            <label>Status singkat<input name="headline" required value="<?= e($cv['headline']) ?>"></label>
            <label>NIM<input name="nim" required value="<?= e($cv['nim']) ?>"></label>
            <label>Email<input type="email" name="email" required value="<?= e($cv['email']) ?>"></label>
            <label>Telepon<input name="phone" required value="<?= e($cv['phone']) ?>"></label>
            <label>GitHub<input name="github" value="<?= e($cv['github']) ?>"></label>
            <label>Lokasi<input name="location" value="<?= e($cv['location']) ?>"></label>
            <label>Program studi<input name="study_program" value="<?= e($cv['study_program']) ?>"></label>
            <label>Angkatan<input name="cohort" value="<?= e($cv['cohort']) ?>"></label>
            <label class="full">Foto profil (JPG/PNG/WEBP, maks. 2 MB)<input type="file" name="photo" accept="image/jpeg,image/png,image/webp"><small>Biarkan kosong jika foto tidak diubah.</small></label>
        </div></section>
        <section class="form-section"><h2>Profil</h2><label>Profil singkat<textarea name="summary" rows="6" required><?= e($cv['summary']) ?></textarea></label></section>
        <section class="form-section"><h2>Pendidikan</h2><p class="help">Satu baris untuk satu riwayat. Format: Nama institusi | Periode/jurusan | Deskripsi</p><textarea name="education" rows="6" required><?= e(records_to_text($cv['education'])) ?></textarea></section>
        <section class="form-section"><h2>Pengalaman</h2><p class="help">Satu baris untuk satu pengalaman. Format: Judul | Periode | Deskripsi</p><textarea name="experience" rows="6"><?= e(records_to_text($cv['experience'])) ?></textarea></section>
        <section class="form-section"><h2>Keahlian dan Bahasa</h2><div class="form-grid">
            <label class="full">Keahlian (pisahkan dengan koma)<textarea name="skills" rows="3"><?= e(implode(', ', $cv['skills'])) ?></textarea></label>
            <label class="full">Kemampuan teknis - format Nama | Persentase<textarea name="technical" rows="6"><?= e(technical_to_text($cv['technical'])) ?></textarea></label>
            <label class="full">Bahasa - satu baris per item<textarea name="languages" rows="4"><?= e(implode("\n", $cv['languages'])) ?></textarea></label>
        </div></section>
        <section class="form-section"><h2>Portofolio dan Footer</h2><div class="form-grid">
            <label class="full">Judul portofolio<input name="portfolio_title" value="<?= e($cv['portfolio_title']) ?>"></label>
            <label class="full">Deskripsi portofolio<textarea name="portfolio_description" rows="4"><?= e($cv['portfolio_description']) ?></textarea></label>
            <label class="full">Teks footer<input name="footer_text" value="<?= e($cv['footer_text']) ?>"></label>
        </div></section>
        <div class="sticky-actions"><a class="btn btn-secondary" href="<?= e(app_url('dashboard.php')) ?>">Batal</a><button class="btn btn-primary" type="submit">Simpan Perubahan</button></div>
    </form>
</main>
</body>
</html>
