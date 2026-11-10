<?php
// logout.php — Đăng xuất (đã fix basePath)
require_once __DIR__ . '/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', 
        time() - 42000,
        $params['path'], $params['domain'], 
        $params['secure'], $params['httponly']
    );
}

session_destroy();

// Redirect dùng basePath() để đúng dù ở root hay sub-folder
header('Location: ' . basePath() . '/login.php');
exit;