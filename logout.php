<?php

declare(strict_types=1);
require __DIR__ . '/functions.php';
logout_user();
session_start();
flash('success', 'Anda telah keluar dari aplikasi.');
redirect('login.php');
