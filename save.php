<?php

declare(strict_types=1);
require __DIR__ . '/functions.php';
$user = require_user();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('edit.php');
}
if (!verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
    $_SESSION['form_errors'] = ['Token keamanan tidak valid. Silakan muat ulang halaman edit.'];
    $_SESSION['form_data'] = find_profile_by_user_id((int) $user['id']);
    redirect('edit.php');
}
$result = save_profile_from_request((int) $user['id'], $_POST, $_FILES);
if (!$result['success']) {
    $_SESSION['form_errors'] = $result['errors'];
    $_SESSION['form_data'] = $result['data'];
    redirect('edit.php');
}
flash('success', 'Data CV berhasil diperbarui.');
redirect('dashboard.php');
