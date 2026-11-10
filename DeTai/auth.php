<?php
// includes/auth.php — Xác thực phiên đăng nhập
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Lấy base path (vd: /detai hoặc '' nếu ở root)
 */
function basePath(): string {
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    // Lấy thư mục chứa script hiện tại (không phải tên file)
    $dir = dirname($script);
    // Nếu là root thì trả về ''
    return rtrim($dir === '/' ? '' : $dir, '/');
}

/**
 * Kiểm tra đã đăng nhập chưa
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Lấy thông tin user hiện tại
 */
function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    return [
        'id'       => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'vai_tro'  => $_SESSION['vai_tro'],
    ];
}

/**
 * Bắt buộc đăng nhập — redirect về login nếu chưa
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . basePath() . '/login.php');
        exit;
    }
}

/**
 * Kiểm tra vai trò Giảng viên
 */
function isGiangVien(): bool {
    return ($_SESSION['vai_tro'] ?? '') === 'Giang_vien';
}

/**
 * Kiểm tra vai trò Sinh viên
 */
function isSinhVien(): bool {
    return ($_SESSION['vai_tro'] ?? '') === 'Sinh_vien';
}

/**
 * Bắt buộc là Giảng viên
 */
function requireGiangVien(): void {
    requireLogin();
    if (!isGiangVien()) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Chỉ Giảng viên mới có quyền thực hiện thao tác này.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * Bắt buộc là Sinh viên
 */
function requireSinhVien(): void {
    requireLogin();
    if (!isSinhVien()) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Chỉ Sinh viên mới có quyền thực hiện thao tác này.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * Response JSON chuẩn
 */
function jsonResponse(bool $success, string $message, $data = [], int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
