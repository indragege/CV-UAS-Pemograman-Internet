<?php

declare(strict_types=1);
require dirname(__DIR__) . '/functions.php';
require_admin();
$id = (int) ($_GET['id'] ?? 0);
$isEdit = $id > 0;
$user = $isEdit ? find_user_by_id($id) : null;
if ($isEdit && (!$user || $user['role'] !== 'user')) {
    flash('error', 'Pengguna tidak ditemukan.');
    redirect('Admin/');
}
$errors = $_SESSION['admin_form_errors'] ?? [];
$formData = $_SESSION['admin_form_data'] ?? $user ?? ['name' => '', 'username' => '', 'email' => '', 'active' => true];
unset($_SESSION['admin_form_errors'], $_SESSION['admin_form_data']);
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $isEdit ? 'Edit' : 'Tambah' ?> Pengguna</title><link rel="stylesheet" href="<?= e(app_url('styles.css')) ?>"></head>
<body class="dashboard-body">
<main class="editor-shell compact-editor">
    <header class="editor-header"><div><span class="eyebrow">ADMINISTRATOR</span><h1><?= $isEdit ? 'Edit Akun Pengguna' : 'Tambah Pengguna Baru' ?></h1><p><?= $isEdit ? 'Perbarui username, identitas, status, atau password pengguna.' : 'Akun baru otomatis memperoleh halaman CV dan URL publik.' ?></p></div><a class="btn btn-light" href="<?= e(app_url('Admin/')) ?>">Kembali</a></header>
    <?php if ($errors): ?><section class="alert alert-error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></section><?php endif; ?>
    <form class="edit-form" action="<?= e(app_url('Admin/user_save.php')) ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="user_id" value="<?= $id ?>">
        <section class="form-section"><h2>Data Akun</h2><div class="form-grid">
            <label>Nama lengkap<input name="name" required value="<?= e($formData['name'] ?? '') ?>"></label>
            <label>Email<input type="email" name="email" required value="<?= e($formData['email'] ?? '') ?>"></label>
            <label>Username<input name="username" required pattern="[a-z0-9_]{3,30}" value="<?= e($formData['username'] ?? '') ?>"><small>Digunakan pada URL publik.</small></label>
            <label><?= $isEdit ? 'Password baru' : 'Password' ?><input type="password" name="password" <?= $isEdit ? '' : 'required' ?> minlength="6"><small><?= $isEdit ? 'Kosongkan jika tidak diubah.' : 'Minimal 6 karakter.' ?></small></label>
            <?php if ($isEdit): ?><label class="checkbox-label full"><input type="checkbox" name="active" value="1" <?= !empty($formData['active']) ? 'checked' : '' ?>><span>Akun aktif dan CV dapat diakses publik</span></label><?php endif; ?>
        </div></section>
        <div class="sticky-actions"><a class="btn btn-secondary" href="<?= e(app_url('Admin/')) ?>">Batal</a><button class="btn btn-primary" type="submit"><?= $isEdit ? 'Simpan Perubahan' : 'Buat Pengguna' ?></button></div>
    </form>
</main>
</body>
</html>
