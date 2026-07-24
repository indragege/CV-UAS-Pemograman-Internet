<?php

declare(strict_types=1);
require dirname(__DIR__) . '/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('Admin/');
}
$id = (int) ($_POST['user_id'] ?? 0);
if (!verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
    $_SESSION['admin_form_errors'] = ['Token keamanan tidak valid.'];
    $_SESSION['admin_form_data'] = $_POST;
    redirect('Admin/user_form.php' . ($id ? '?id=' . $id : ''));
}
try {
    $result = $id > 0 ? admin_update_user($id, $_POST) : create_user_account($_POST);
    if (!$result['success']) {
        $_SESSION['admin_form_errors'] = $result['errors'];
        $_SESSION['admin_form_data'] = $_POST;
        redirect('Admin/user_form.php' . ($id ? '?id=' . $id : ''));
    }
    flash('success', $id > 0 ? 'Data pengguna berhasil diperbarui.' : 'Pengguna baru berhasil dibuat.');
} catch (Throwable $exception) {
    flash('error', $exception->getMessage());
}
redirect('Admin/');
