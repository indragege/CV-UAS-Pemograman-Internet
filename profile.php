<?php

declare(strict_types=1);
require __DIR__ . '/functions.php';

$username = strtolower(trim((string) ($_GET['username'] ?? '')));
$cv = find_profile_by_username($username);
if (!$cv) {
    http_response_code(404);
    require __DIR__ . '/partials/not_found.php';
    exit;
}
$isDefault = default_user_id() === (int) $cv['user_id'];
require __DIR__ . '/partials/cv_view.php';
