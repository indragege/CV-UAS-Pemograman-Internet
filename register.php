<?php

declare(strict_types=1);
require __DIR__ . '/functions.php';

$errors = [];
$input = ['name' => '', 'username' => '', 'email' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;
    if (!verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
        $errors[] = 'Token keamanan tidak valid.';
    } else {
        try {
            $result = create_user_account($_POST);
            if ($result['success']) {
                flash('success', 'Pendaftaran berhasil. Silakan login dan lengkapi CV Anda.');
                redirect('login.php');
            }
            $errors = $result['errors'];
        } catch (Throwable $exception) {
            $errors[] = $exception->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Daftar Pengguna</title><link rel="stylesheet" href="<?= e(app_url('styles.css')) ?>"></head>
<body class="auth-body">
<main class="single-auth-card">
    <a class="brand" href="<?= e(app_url()) ?>"><span>CV</span> Multi User</a>
    <div class="auth-heading"><span class="eyebrow dark">AKUN BARU</span><h1>Daftar Pengguna</h1><p>Username akan menjadi alamat publik CV, misalnya <b>/nama_pengguna</b>.</p></div>
    <?php if ($errors): ?><div class="alert alert-error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label>Nama lengkap<input name="name" required value="<?= e($input['name'] ?? '') ?>"></label>
        <label>Username<input name="username" required pattern="[a-z0-9_]{3,30}" value="<?= e($input['username'] ?? '') ?>"><small>Huruf kecil, angka, dan garis bawah.</small></label>
        <label>Email<input type="email" name="email" required value="<?= e($input['email'] ?? '') ?>"></label>
        <label>Password<input type="password" name="password" required minlength="6"></label>
        <button class="btn btn-primary btn-block" type="submit">Buat Akun dan CV</button>
    </form>
    <p class="auth-footer">Sudah mempunyai akun? <a href="<?= e(app_url('login.php')) ?>">Kembali ke login</a></p>
</main>
</body>
</html>
