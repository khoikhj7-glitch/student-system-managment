<?php
session_start();
// Report mọi lỗi ẩn để dễ debug trong môi trường dev
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bắt Param (Lấy tên Folder Class)
$folder = isset($_GET['f']) ? $_GET['f'] : 'Dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Auth Guard
if (!isset($_SESSION['user_id']) && $folder !== 'Auth') {
    header("Location: index.php?f=Auth");
    exit;
}
if (isset($_SESSION['user_id']) && $folder === 'Auth' && $action !== 'logout') {
    header("Location: index.php?f=Dashboard");
    exit;
}

// Danh sách các Module hợp lệ theo phân quyền (RBAC)
if (!isset($_SESSION['vai_tro'])) {
    $allowedFolders = ['Auth'];
} elseif ($_SESSION['vai_tro'] === 'Giang_vien') {
    // Giảng viên full quyền (Thêm Module 'Auth' dể có logout)
    $allowedFolders = ['Auth', 'Dashboard', 'DeTai', 'BaiViet', 'SinhVien', 'GiangVien', 'Khoa', 'Lop', 'MonHoc', 'Diem', 'DiemDanh']; 
} else {
    // Sinh viên quyền hạn chế
    $allowedFolders = ['Auth', 'Dashboard', 'BaiViet', 'DeTai', 'Diem', 'DiemDanh', 'MonHoc']; // MonHoc để coi môn
}

if (in_array($folder, $allowedFolders)) {
    $controllerPath = $folder . "/Controller.php";
    
    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        $controller = new Controller();
        
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            die("ERROR: Action '$action' không tồn tại trong Controller của '$folder'!");
        }
    } else {
        die("ERROR: Controller cho Class '$folder' chưa được tạo!");
    }
} else {
    die("ERROR: Truy cập bị từ chối hoặc Class '$folder' chưa khai báo.");
}
?>
