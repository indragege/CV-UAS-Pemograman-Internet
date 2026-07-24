<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$config = require __DIR__ . '/config.php';
date_default_timezone_set((string) ($config['timezone'] ?? 'Asia/Jakarta'));

function app_config(): array
{
    static $config;
    if ($config === null) {
        $config = require __DIR__ . '/config.php';
    }
    return $config;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_base_path(): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $adminPos = stripos($script, '/Admin/');
    if ($adminPos !== false) {
        return rtrim(substr($script, 0, $adminPos), '/');
    }

    $dir = str_replace('\\', '/', dirname($script));
    if ($dir === '/' || $dir === '.' || $dir === '\\') {
        return '';
    }
    return rtrim($dir, '/');
}

function app_url(string $path = ''): string
{
    $base = app_base_path();
    $path = ltrim($path, '/');
    return ($base === '' ? '' : $base) . '/' . $path;
}

function profile_url(string $username): string
{
    return app_url(rawurlencode($username));
}

function redirect(string $path): never
{
    header('Location: ' . (preg_match('~^https?://~i', $path) ? $path : app_url($path)));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['csrf_token'];
}

function verify_csrf(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals((string) $_SESSION['csrf_token'], $token);
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return is_string($value) ? $value : null;
}

function storage_path(): string
{
    return (string) app_config()['storage_path'];
}

function default_profile(): array
{
    return require __DIR__ . '/data/default.php';
}

function blank_profile(string $name = ''): array
{
    return [
        'name' => $name,
        'title' => 'Mahasiswa Teknik Informatika',
        'headline' => 'Profil Pengguna',
        'nim' => '',
        'email' => '',
        'phone' => '',
        'github' => '',
        'location' => 'Bandung, Indonesia',
        'study_program' => 'Teknik Informatika',
        'cohort' => '',
        'summary' => 'Tuliskan profil singkat Anda melalui halaman edit CV.',
        'education' => [[
            'title' => 'Universitas Bale Bandung',
            'meta' => 'Program Studi Teknik Informatika',
            'description' => 'Lengkapi informasi pendidikan Anda melalui dashboard pengguna.',
        ]],
        'experience' => [],
        'skills' => ['HTML', 'CSS', 'PHP'],
        'technical' => [['name' => 'Pengembangan Web', 'percentage' => 60]],
        'languages' => ['Bahasa Indonesia'],
        'portfolio_title' => 'Portofolio Saya',
        'portfolio_description' => 'Lengkapi deskripsi portofolio melalui halaman edit CV.',
        'footer_text' => 'Aplikasi CV Multi User',
        'photo_path' => 'assets/img/avatar-default.svg',
    ];
}

function seed_store(): array
{
    $now = date('Y-m-d H:i:s');
    $indra = default_profile();
    $cecep = blank_profile('Cecep Suwanda');
    $cecep['title'] = 'Dosen Pemrograman Internet';
    $cecep['headline'] = 'Dosen dan Praktisi Teknologi Informasi';
    $cecep['email'] = 'cecep.suwanda@example.com';
    $cecep['summary'] = 'Profil contoh untuk mendemonstrasikan URL publik CV pada aplikasi multi-user. Data ini dapat diubah oleh pemilik akun melalui dashboard pengguna.';
    $cecep['portfolio_title'] = 'Pembelajaran Pemrograman Internet';
    $cecep['portfolio_description'] = 'Contoh halaman CV pengguna kedua yang membuktikan bahwa aplikasi dapat menyimpan dan menampilkan lebih dari satu profil.';

    return [
        'settings' => [
            'default_user_id' => 2,
            'next_user_id' => 4,
            'created_at' => $now,
        ],
        'users' => [
            [
                'id' => 1,
                'username' => 'admin',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'username' => 'indra_mulyadi',
                'password_hash' => password_hash('user123', PASSWORD_DEFAULT),
                'role' => 'user',
                'name' => 'Indra Mulyadi',
                'email' => (string) $indra['email'],
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'username' => 'cecep_suwanda',
                'password_hash' => password_hash('user123', PASSWORD_DEFAULT),
                'role' => 'user',
                'name' => 'Cecep Suwanda',
                'email' => (string) $cecep['email'],
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ],
        'profiles' => [
            array_merge(['user_id' => 2, 'updated_at' => $now], $indra),
            array_merge(['user_id' => 3, 'updated_at' => $now], $cecep),
        ],
    ];
}

function ensure_storage(): void
{
    $path = storage_path();
    $directory = dirname($path);
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
    if (!file_exists($path) || filesize($path) === 0) {
        write_store(seed_store());
    }
}

function read_store(): array
{
    ensure_storage();
    $content = file_get_contents(storage_path());
    $data = json_decode((string) $content, true);
    if (!is_array($data) || !isset($data['users'], $data['profiles'], $data['settings'])) {
        $data = seed_store();
        write_store($data);
    }
    return $data;
}

function write_store(array $data): void
{
    $path = storage_path();
    $directory = dirname($path);
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        throw new RuntimeException('Data tidak dapat dikonversi ke JSON.');
    }

    $temp = $path . '.tmp';
    if (file_put_contents($temp, $json, LOCK_EX) === false || !rename($temp, $path)) {
        @unlink($temp);
        throw new RuntimeException('Basis data file tidak dapat disimpan. Pastikan folder data dapat ditulis.');
    }
}

function mutate_store(callable $callback): mixed
{
    ensure_storage();
    $lockPath = storage_path() . '.lock';
    $handle = fopen($lockPath, 'c+');
    if ($handle === false) {
        throw new RuntimeException('Kunci basis data tidak dapat dibuat.');
    }

    try {
        if (!flock($handle, LOCK_EX)) {
            throw new RuntimeException('Basis data sedang digunakan. Silakan coba lagi.');
        }
        $data = read_store();
        $result = $callback($data);
        write_store($data);
        flock($handle, LOCK_UN);
        return $result;
    } finally {
        fclose($handle);
    }
}

function all_users(bool $onlyUsers = false): array
{
    $users = read_store()['users'];
    if ($onlyUsers) {
        $users = array_values(array_filter($users, static fn(array $user): bool => $user['role'] === 'user'));
    }
    usort($users, static fn(array $a, array $b): int => ((int) $a['id']) <=> ((int) $b['id']));
    return $users;
}

function find_user_by_id(int $id): ?array
{
    foreach (read_store()['users'] as $user) {
        if ((int) $user['id'] === $id) {
            return $user;
        }
    }
    return null;
}

function find_user_by_username(string $username): ?array
{
    foreach (read_store()['users'] as $user) {
        if (strtolower((string) $user['username']) === strtolower(trim($username))) {
            return $user;
        }
    }
    return null;
}

function find_profile_by_user_id(int $userId): ?array
{
    foreach (read_store()['profiles'] as $profile) {
        if ((int) $profile['user_id'] === $userId) {
            return $profile;
        }
    }
    return null;
}

function find_profile_by_username(string $username): ?array
{
    $user = find_user_by_username($username);
    if (!$user || $user['role'] !== 'user' || !(bool) $user['active']) {
        return null;
    }
    $profile = find_profile_by_user_id((int) $user['id']);
    if (!$profile) {
        return null;
    }
    $profile['username'] = $user['username'];
    $profile['user_active'] = $user['active'];
    return $profile;
}

function default_user_id(): int
{
    return (int) (read_store()['settings']['default_user_id'] ?? 0);
}

function default_public_profile(): ?array
{
    $data = read_store();
    $id = (int) ($data['settings']['default_user_id'] ?? 0);
    $user = null;
    foreach ($data['users'] as $candidate) {
        if ((int) $candidate['id'] === $id && $candidate['role'] === 'user' && (bool) $candidate['active']) {
            $user = $candidate;
            break;
        }
    }
    if (!$user) {
        foreach ($data['users'] as $candidate) {
            if ($candidate['role'] === 'user' && (bool) $candidate['active']) {
                $user = $candidate;
                break;
            }
        }
    }
    if (!$user) {
        return null;
    }
    foreach ($data['profiles'] as $profile) {
        if ((int) $profile['user_id'] === (int) $user['id']) {
            $profile['username'] = $user['username'];
            return $profile;
        }
    }
    return null;
}

function current_user(): ?array
{
    $id = (int) ($_SESSION['user_id'] ?? 0);
    if ($id < 1) {
        return null;
    }
    $user = find_user_by_id($id);
    if (!$user || !(bool) $user['active']) {
        unset($_SESSION['user_id']);
        return null;
    }
    return $user;
}

function authenticate_user(string $username, string $password): ?array
{
    $user = find_user_by_username($username);
    if (!$user || !(bool) $user['active'] || !password_verify($password, (string) $user['password_hash'])) {
        return null;
    }
    return $user;
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function require_user(): array
{
    $user = current_user();
    if (!$user || $user['role'] !== 'user') {
        flash('error', 'Silakan masuk sebagai pengguna terlebih dahulu.');
        redirect('login.php');
    }
    return $user;
}

function require_admin(): array
{
    $user = current_user();
    if (!$user || $user['role'] !== 'admin') {
        flash('error', 'Silakan masuk sebagai administrator.');
        redirect('login.php?role=admin');
    }
    return $user;
}

function create_user_account(array $input): array
{
    $username = strtolower(trim((string) ($input['username'] ?? '')));
    $name = clean_text($input['name'] ?? '');
    $email = clean_text($input['email'] ?? '');
    $password = (string) ($input['password'] ?? '');
    $errors = [];

    if (!preg_match('/^[a-z0-9_]{3,30}$/', $username)) {
        $errors[] = 'Username harus 3-30 karakter dan hanya menggunakan huruf kecil, angka, atau garis bawah.';
    }
    if ($name === '') {
        $errors[] = 'Nama wajib diisi.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }
    if (find_user_by_username($username)) {
        $errors[] = 'Username sudah digunakan.';
    }

    if ($errors !== []) {
        return ['success' => false, 'errors' => $errors];
    }

    mutate_store(function (array &$data) use ($username, $name, $email, $password): void {
        foreach ($data['users'] as $existing) {
            if (strtolower((string) $existing['username']) === $username) {
                throw new RuntimeException('Username sudah digunakan.');
            }
        }
        $id = (int) ($data['settings']['next_user_id'] ?? 1);
        $data['settings']['next_user_id'] = $id + 1;
        $now = date('Y-m-d H:i:s');
        $data['users'][] = [
            'id' => $id,
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user',
            'name' => $name,
            'email' => $email,
            'active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $profile = blank_profile($name);
        $profile['email'] = $email;
        $data['profiles'][] = array_merge(['user_id' => $id, 'updated_at' => $now], $profile);
        if (empty($data['settings']['default_user_id'])) {
            $data['settings']['default_user_id'] = $id;
        }
    });

    return ['success' => true, 'errors' => []];
}

function admin_update_user(int $id, array $input): array
{
    $username = strtolower(trim((string) ($input['username'] ?? '')));
    $name = clean_text($input['name'] ?? '');
    $email = clean_text($input['email'] ?? '');
    $password = (string) ($input['password'] ?? '');
    $active = isset($input['active']);
    $errors = [];

    if (!preg_match('/^[a-z0-9_]{3,30}$/', $username)) {
        $errors[] = 'Username tidak valid.';
    }
    if ($name === '') {
        $errors[] = 'Nama wajib diisi.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }
    if ($password !== '' && strlen($password) < 6) {
        $errors[] = 'Password baru minimal 6 karakter.';
    }

    $existingUser = find_user_by_id($id);
    if (!$existingUser || $existingUser['role'] !== 'user') {
        $errors[] = 'Pengguna tidak ditemukan.';
    }
    $sameUsername = find_user_by_username($username);
    if ($sameUsername && (int) $sameUsername['id'] !== $id) {
        $errors[] = 'Username sudah digunakan.';
    }

    if ($errors !== []) {
        return ['success' => false, 'errors' => $errors];
    }

    mutate_store(function (array &$data) use ($id, $username, $name, $email, $password, $active): void {
        foreach ($data['users'] as &$user) {
            if ((int) $user['id'] === $id && $user['role'] === 'user') {
                $user['username'] = $username;
                $user['name'] = $name;
                $user['email'] = $email;
                $user['active'] = $active;
                $user['updated_at'] = date('Y-m-d H:i:s');
                if ($password !== '') {
                    $user['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
                }
            }
        }
        unset($user);
        foreach ($data['profiles'] as &$profile) {
            if ((int) $profile['user_id'] === $id) {
                $profile['name'] = $name;
                $profile['email'] = $email;
                $profile['updated_at'] = date('Y-m-d H:i:s');
            }
        }
        unset($profile);
        if (!$active && (int) $data['settings']['default_user_id'] === $id) {
            $data['settings']['default_user_id'] = first_active_user_id($data, $id);
        }
    });

    return ['success' => true, 'errors' => []];
}

function first_active_user_id(array $data, int $excludeId = 0): int
{
    foreach ($data['users'] as $user) {
        if ($user['role'] === 'user' && (bool) $user['active'] && (int) $user['id'] !== $excludeId) {
            return (int) $user['id'];
        }
    }
    return 0;
}

function set_default_user(int $id): bool
{
    $user = find_user_by_id($id);
    if (!$user || $user['role'] !== 'user' || !(bool) $user['active']) {
        return false;
    }
    mutate_store(function (array &$data) use ($id): void {
        $data['settings']['default_user_id'] = $id;
    });
    return true;
}

function delete_user_account(int $id): bool
{
    $user = find_user_by_id($id);
    if (!$user || $user['role'] !== 'user') {
        return false;
    }
    mutate_store(function (array &$data) use ($id): void {
        $data['users'] = array_values(array_filter($data['users'], static fn(array $user): bool => (int) $user['id'] !== $id));
        $data['profiles'] = array_values(array_filter($data['profiles'], static fn(array $profile): bool => (int) $profile['user_id'] !== $id));
        if ((int) $data['settings']['default_user_id'] === $id) {
            $data['settings']['default_user_id'] = first_active_user_id($data, $id);
        }
    });
    return true;
}

function clean_text(mixed $value): string
{
    return trim(preg_replace('/\s+/', ' ', strip_tags((string) $value)) ?? '');
}

function clean_multiline(mixed $value): string
{
    $text = str_replace(["\r\n", "\r"], "\n", strip_tags((string) $value));
    return trim(preg_replace('/[ \t]+/', ' ', $text) ?? '');
}

function parse_line_list(string $value): array
{
    $items = preg_split('/\R/', $value) ?: [];
    return array_values(array_filter(array_map('clean_text', $items), static fn(string $item): bool => $item !== ''));
}

function parse_comma_list(string $value): array
{
    $items = preg_split('/[,\r\n]+/', $value) ?: [];
    return array_values(array_unique(array_filter(array_map('clean_text', $items), static fn(string $item): bool => $item !== '')));
}

function parse_records(string $value): array
{
    $records = [];
    foreach (preg_split('/\R/', $value) ?: [] as $line) {
        if (trim($line) === '') {
            continue;
        }
        $parts = array_map('clean_text', explode('|', $line, 3));
        $records[] = [
            'title' => $parts[0] ?? '',
            'meta' => $parts[1] ?? '',
            'description' => $parts[2] ?? '',
        ];
    }
    return $records;
}

function parse_technical(string $value): array
{
    $records = [];
    foreach (preg_split('/\R/', $value) ?: [] as $line) {
        if (trim($line) === '') {
            continue;
        }
        $parts = array_map('clean_text', explode('|', $line, 2));
        $records[] = [
            'name' => $parts[0] ?? '',
            'percentage' => max(0, min(100, (int) ($parts[1] ?? 0))),
        ];
    }
    return $records;
}

function records_to_text(array $records): string
{
    return implode("\n", array_map(static fn(array $record): string => implode(' | ', [
        $record['title'] ?? '',
        $record['meta'] ?? '',
        $record['description'] ?? '',
    ]), $records));
}

function technical_to_text(array $records): string
{
    return implode("\n", array_map(static fn(array $record): string => ($record['name'] ?? '') . ' | ' . ($record['percentage'] ?? 0), $records));
}

function save_uploaded_photo(array $file, int $userId, string $currentPath): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return $currentPath;
    }
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Foto gagal diunggah.');
    }
    if ((int) ($file['size'] ?? 0) > (int) app_config()['max_upload_size']) {
        throw new RuntimeException('Ukuran foto maksimal 2 MB.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime])) {
        throw new RuntimeException('Format foto harus JPG, PNG, atau WEBP.');
    }
    $relativeDir = 'assets/uploads/user_' . $userId;
    $absoluteDir = __DIR__ . '/' . $relativeDir;
    if (!is_dir($absoluteDir) && !mkdir($absoluteDir, 0775, true) && !is_dir($absoluteDir)) {
        throw new RuntimeException('Folder unggahan tidak dapat dibuat.');
    }
    $filename = 'profile_' . date('YmdHis') . '_' . bin2hex(random_bytes(3)) . '.' . $extensions[$mime];
    if (!move_uploaded_file((string) $file['tmp_name'], $absoluteDir . '/' . $filename)) {
        throw new RuntimeException('Foto tidak dapat disimpan.');
    }
    return $relativeDir . '/' . $filename;
}

function save_profile_from_request(int $userId, array $input, array $files): array
{
    $current = find_profile_by_user_id($userId) ?? blank_profile();
    $errors = [];
    $required = [
        'name' => 'Nama',
        'title' => 'Identitas/Jabatan',
        'headline' => 'Status',
        'nim' => 'NIM',
        'email' => 'Email',
        'phone' => 'Telepon',
        'summary' => 'Profil singkat',
    ];
    foreach ($required as $field => $label) {
        if (trim((string) ($input[$field] ?? '')) === '') {
            $errors[] = $label . ' wajib diisi.';
        }
    }
    if (!filter_var((string) ($input['email'] ?? ''), FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    $photoPath = (string) ($current['photo_path'] ?? 'assets/img/avatar-default.svg');
    try {
        if (isset($files['photo'])) {
            $photoPath = save_uploaded_photo($files['photo'], $userId, $photoPath);
        }
    } catch (RuntimeException $exception) {
        $errors[] = $exception->getMessage();
    }

    $profile = [
        'user_id' => $userId,
        'name' => clean_text($input['name'] ?? ''),
        'title' => clean_text($input['title'] ?? ''),
        'headline' => clean_text($input['headline'] ?? ''),
        'nim' => clean_text($input['nim'] ?? ''),
        'email' => clean_text($input['email'] ?? ''),
        'phone' => clean_text($input['phone'] ?? ''),
        'github' => clean_text($input['github'] ?? ''),
        'location' => clean_text($input['location'] ?? ''),
        'study_program' => clean_text($input['study_program'] ?? ''),
        'cohort' => clean_text($input['cohort'] ?? ''),
        'summary' => clean_multiline($input['summary'] ?? ''),
        'education' => parse_records((string) ($input['education'] ?? '')),
        'experience' => parse_records((string) ($input['experience'] ?? '')),
        'skills' => parse_comma_list((string) ($input['skills'] ?? '')),
        'technical' => parse_technical((string) ($input['technical'] ?? '')),
        'languages' => parse_line_list((string) ($input['languages'] ?? '')),
        'portfolio_title' => clean_text($input['portfolio_title'] ?? ''),
        'portfolio_description' => clean_multiline($input['portfolio_description'] ?? ''),
        'footer_text' => clean_text($input['footer_text'] ?? ''),
        'photo_path' => $photoPath,
        'updated_at' => date('Y-m-d H:i:s'),
    ];

    if ($profile['education'] === []) {
        $errors[] = 'Minimal satu data pendidikan harus diisi.';
    }
    if ($errors !== []) {
        return ['success' => false, 'errors' => $errors, 'data' => $profile];
    }

    mutate_store(function (array &$data) use ($userId, $profile): void {
        $found = false;
        foreach ($data['profiles'] as $index => $item) {
            if ((int) $item['user_id'] === $userId) {
                $data['profiles'][$index] = $profile;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $data['profiles'][] = $profile;
        }
        foreach ($data['users'] as &$user) {
            if ((int) $user['id'] === $userId) {
                $user['name'] = $profile['name'];
                $user['email'] = $profile['email'];
                $user['updated_at'] = $profile['updated_at'];
                break;
            }
        }
        unset($user);
    });

    return ['success' => true, 'errors' => [], 'data' => $profile];
}

function storage_label(): string
{
    return 'Basis data JSON aktif';
}

ensure_storage();
