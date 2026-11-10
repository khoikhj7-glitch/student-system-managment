<?php
/**
 * api.php — API Router tập trung (Monolith API)
 * Mọi request API đều đi qua đây.
 * URL: api.php?module=<Module>&action=<action>&id=<id>
 */

session_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// Modules hợp lệ
$validModules = [
    'Auth', 'Dashboard', 'DeTai', 'BaiViet',
    'SinhVien', 'GiangVien', 'Khoa', 'Lop',
    'MonHoc', 'Diem', 'DiemDanh'
];

$module = $_GET['module'] ?? '';
$action = $_GET['action'] ?? 'index';

// Validate module
if (!in_array($module, $validModules)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => "Module '$module' không hợp lệ.",
        'data'    => []
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Auth check (trừ Auth module)
if ($module !== 'Auth' && !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng đăng nhập.',
        'data'    => []
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// RBAC — danh sách module được phép theo vai trò
$allowedByRole = [
    'Giang_vien' => $validModules,
    'Sinh_vien'  => ['Auth', 'Dashboard', 'BaiViet', 'DeTai', 'Diem', 'DiemDanh', 'MonHoc'],
];

if ($module !== 'Auth') {
    $role = $_SESSION['vai_tro'] ?? '';
    $allowed = $allowedByRole[$role] ?? [];
    if (!in_array($module, $allowed)) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Truy cập bị từ chối.',
            'data'    => []
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Load & dispatch
require_once __DIR__ . '/core/BaseController.php';
$controllerPath = __DIR__ . "/$module/Controller.php";

if (!file_exists($controllerPath)) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => "Controller cho '$module' không tồn tại.",
        'data'    => []
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once $controllerPath;
$controller = new Controller();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => "Action '$action' không tồn tại trong '$module'.",
        'data'    => []
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$controller->$action();
