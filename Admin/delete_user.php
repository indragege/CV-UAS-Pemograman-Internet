<?php

declare(strict_types=1);
require dirname(__DIR__) . '/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
    flash('error', 'Permintaan tidak valid.');
    redirect('Admin/');
}
$id = (int) ($_POST['user_id'] ?? 0);
if (delete_user_account($id)) {
    flash('success', 'Pengguna dan data CV berhasil dihapus.');
} else {
    flash('error', 'Pengguna tidak dapat dihapus.');
}
redirect('Admin/');
