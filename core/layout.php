<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SML - Mô Hình Module Driven</title>
    <!-- Import phông chữ Inter để giao diện nhìn hiện đại -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Cho Modal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Link file css thuần (Ưu tiên) -->
    <link rel="stylesheet" href="public/css/style.css">
    
    <!-- Tích hợp CKEditor -->
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
</head>
<body>

<div class="app-wrapper">
    <!-- SIDEBAR -->
    <aside class="app-sidebar">
        <div class="sidebar-brand">
            📊 SML
        </div>
        <nav class="sidebar-nav">
            <?php $f = $_GET['f'] ?? 'Dashboard'; ?>
            
            <a href="index.php?f=Dashboard&action=index" class="nav-link <?= $f == 'Dashboard' ? 'active' : '' ?>">
                Trang Chủ
            </a>
            
            <a href="index.php?f=BaiViet&action=index" class="nav-link <?= $f == 'BaiViet' ? 'active' : '' ?>">
                Tin Tức
            </a>

            <a href="index.php?f=DeTai&action=index" class="nav-link <?= $f == 'DeTai' ? 'active' : '' ?>">
                Đề Tài
            </a>

            <?php if (isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] === 'Giang_vien'): ?>
                <div style="padding: 15px 16px 5px; font-size: 0.75rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">
                    Quản Lý (Giảng Viên)
                </div>
                <a href="index.php?f=Khoa&action=index" class="nav-link <?= $f == 'Khoa' ? 'active' : '' ?>">Khoa</a>
                <a href="index.php?f=Lop&action=index" class="nav-link <?= $f == 'Lop' ? 'active' : '' ?>">Lớp Học</a>
                <a href="index.php?f=SinhVien&action=index" class="nav-link <?= $f == 'SinhVien' ? 'active' : '' ?>">Sinh Viên</a>
                <a href="index.php?f=GiangVien&action=index" class="nav-link <?= $f == 'GiangVien' ? 'active' : '' ?>">Giảng Viên</a>
                <a href="index.php?f=MonHoc&action=index" class="nav-link <?= $f == 'MonHoc' ? 'active' : '' ?>">Môn Học</a>
                <a href="index.php?f=Diem&action=index" class="nav-link <?= $f == 'Diem' ? 'active' : '' ?>">Điểm Số</a>
                <a href="index.php?f=DiemDanh&action=index" class="nav-link <?= $f == 'DiemDanh' ? 'active' : '' ?>">Điểm Danh</a>
            <?php endif; ?>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="app-main">
        <header class="app-topbar">
            <div class="topbar-title">Hệ Thống Quản Lý Học Vụ</div>
            <div class="topbar-actions">
                <div class="user-profile">
                    <div class="user-avatar"><?= isset($_SESSION['username']) ? strtoupper(substr($_SESSION['username'], 0, 1)) : 'U' ?></div>
                    <span><?= $_SESSION['username'] ?? 'User' ?></span>
                </div>
                <a href="index.php?f=Auth&action=logout" class="btn btn-secondary btn-sm">Đăng xuất</a>
            </div>
        </header>

        <div class="app-content">
            <!-- Render Nội dung Module vào đây -->
            <?php require_once $view; ?>
        </div>
    </main>
</div>

<script>
    function confirmDelete() {
        return confirm("Bạn có chắc chắn muốn xóa bản ghi này không?");
    }
</script>

</body>
</html>
