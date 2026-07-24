<?php

declare(strict_types=1);
require __DIR__ . '/functions.php';

$cv = default_public_profile();
if (!$cv) {
    http_response_code(503);
    echo 'Belum ada CV aktif. Silakan hubungi administrator.';
    exit;
}
$isDefault = true;
require __DIR__ . '/partials/cv_view.php';
