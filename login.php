<?php

declare(strict_types=1);
require __DIR__ . '/functions.php';

$current = current_user();
if ($current) {
    redirect($current['role'] === 'admin' ? 'Admin/' : 'dashboard.php');
}

$error = flash('error');
$success = flash('success');
$isAdminHint = ($_GET['role'] ?? '') === 'admin';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
        $error = 'Token keamanan tidak valid. Silakan muat ulang halaman.';
    } else {
        $user = authenticate_user((string) ($_POST['username'] ?? ''), (string) ($_POST['password'] ?? ''));
        if ($user) {
            login_user($user);
            flash('success', 'Login berhasil. Selamat datang, ' . $user['name'] . '.');
            redirect($user['role'] === 'admin' ? 'Admin/' : 'dashboard.php');
        }
        $error = 'Username atau password salah, atau akun sedang dinonaktifkan.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login - Multi User CV</title><link rel="stylesheet" href="<?= e(app_url('styles.css')) ?>"></head>
<body class="auth-body">
    <main class="auth-shell">
        <section class="auth-visual">
            <a class="brand brand-light" href="<?= e(app_url()) ?>"><span>CV</span> Multi User</a>
            <div><span class="eyebrow">PEMROGRAMAN INTERNET</span><h1>Satu aplikasi untuk banyak Curriculum Vitae.</h1><p>Pengguna dapat mengelola CV masing-masing, sedangkan administrator mengatur akun dan memilih CV default pada halaman utama.</p></div>
            <ul class="feature-checks"><li>Front end CV responsif</li><li>Back end pengguna dan admin</li><li>URL publik berdasarkan username</li></ul>
        </section>
        <section class="auth-card">
            <div class="auth-heading"><span class="eyebrow dark"><?= $isAdminHint ? 'AKSES ADMINISTRATOR' : 'AKSES PENGGUNA' ?></span><h2>Masuk ke Aplikasi</h2><p>Gunakan username dan password yang telah terdaftar.</p></div>
            <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
            <form method="post" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <label>Username<input name="username" autocomplete="username" required placeholder="contoh: indra_mulyadi"></label>
                <label>Password<input type="password" name="password" autocomplete="current-password" required placeholder="Masukkan password"></label>
                <button class="btn btn-primary btn-block" type="submit">Masuk</button>
            </form>
            <div class="demo-box"><strong>Akun demonstrasi</strong><span>Admin: admin / admin123</span><span>User: indra_mulyadi / user123</span></div>
            <p class="auth-footer">Belum memiliki akun? <a href="<?= e(app_url('register.php')) ?>">Daftar sebagai pengguna</a></p>
        </section>
    </main>
</body>
</html>
