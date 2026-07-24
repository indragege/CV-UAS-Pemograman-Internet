<?php
// Router untuk pengujian dengan: php -S localhost:8000 router.php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$file = __DIR__ . $path;
if ($path !== '/' && is_file($file)) {
    return false;
}
if (is_dir($file) && is_file(rtrim($file, '/') . '/index.php')) {
    require rtrim($file, '/') . '/index.php';
    return true;
}
if ($path === '/' || $path === '') {
    require __DIR__ . '/index.php';
    return true;
}
$username = trim($path, '/');
if (preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $_GET['username'] = $username;
    require __DIR__ . '/profile.php';
    return true;
}
http_response_code(404);
echo '404 Not Found';
