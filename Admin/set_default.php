<?php

declare(strict_types=1);
require dirname(__DIR__) . '/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
    flash('error', 'Permintaan tidak valid.');
    redirect('Admin/');
}
$id = (int) ($_POST['user_id'] ?? 0);
if (set_default_user($id)) {
    flash('success', 'CV default berhasil diubah.');
} else {
    flash('error', 'CV default hanya dapat dipilih dari pengguna aktif.');
}
redirect('Admin/');
