<?php
// Tự nạp models
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../BaiViet/BaiViet.php';
require_once __DIR__ . '/../Khoa/Khoa.php';
require_once __DIR__ . '/../Lop/Lop.php';
require_once __DIR__ . '/../MonHoc/MonHoc.php';
require_once __DIR__ . '/../GiangVien/GiangVien.php';
// SinhVien is also required but we can only run simple tests due to FK references.

class SMLTestFramework {
    public static $passed = 0;
    public static $failed = 0;
    public static $results = [];

    public static function assert($condition, $testName) {
        if ($condition) {
            self::$passed++;
            self::$results[] = "🟢 [PASS] $testName";
        } else {
            self::$failed++;
            self::$results[] = "🔴 [FAIL] $testName";
        }
    }

    public static function assertEquals($expected, $actual, $testName) {
        self::assert($expected == $actual, "$testName (Expected: $expected | Actual: $actual)");
    }
}

// Bắt đầu quy trình kiểm thử
$database = new Database();
$db = $database->getConnection();
$db->beginTransaction();

try {
    SMLTestFramework::$results[] = "🚀 Khởi chạy SML Unit Tests (Database Transaction Mode)...";
    
    // --- 1. Test Module BaiViet ---
    $baiViet = new BaiViet($db);
    $baiViet->tieu_de = "Test bài viết " . time();
    $baiViet->noi_dung = "Nội dung abcxyz";
    $baiViet->chuyen_muc = "Tin tuc";
    $baiViet->tac_gia_id = 1;
    $baiViet->tom_tat = "Tóm tắt";
    $baiViet->thumbnail = "img.png";
    $baiViet->is_hot = 0;
    $baiViet->trang_thai = "Draft";
    
    // Test Insert
    $isInserted = $baiViet->create();
    SMLTestFramework::assert($isInserted, "BaiViet - Create mới bài viết thành công");
    
    // Test Select Last Insert
    $stmt = $db->query("SELECT * FROM bai_viets ORDER BY id DESC LIMIT 1");
    $lastBaiViet = $stmt->fetch(PDO::FETCH_ASSOC);
    SMLTestFramework::assertEquals("Nội dung abcxyz", $lastBaiViet['noi_dung'] ?? '', "BaiViet - Dữ liệu khớp Content Db");

    // --- 2. Test Module Khoa ---
    $khoa = new Khoa($db);
    $khoa->ma_khoa = "TEST-KHOA";
    $khoa->ten_khoa = "Khoa Kiểm Thử GKO";
    $isKhoaInserted = $khoa->save();
    SMLTestFramework::assert($isKhoaInserted, "Khoa - Lưu thành công Khoa mới");
    $khoaId = $db->lastInsertId();

    // Test Constraint (Tạo 1 giảng viên thuộc Khoa này)
    $gv = new GiangVien($db);
    $gv->ma_gv = "GV-TEST";
    $gv->ho_ten = "Giảng Viên Kiểm Thử";
    $gv->trinh_do = "Giao su";
    $gv->khoa_id = $khoaId;
    $gv->save();

    // Cố tình xóa khoa đang có Giảng Viên trực thuộc
    $khoaCheck = new Khoa($db);
    $khoaCheck->id = $khoaId;
    $deleteResult = $khoaCheck->checkDeleteConstraints();
    SMLTestFramework::assert(is_string($deleteResult), "Khoa Constraint - Ngăn cản xóa Khoa đang chứa GV thành công");

    // --- 3. Test Module Lop ---
    $lop = new Lop($db);
    $lop->ma_lop = "TEST-LOP";
    $lop->ten_lop = "Lớp Kiểm Thử";
    $lop->khoa_id = $khoaId;
    $lop->save();
    $lopId = $db->lastInsertId();

    // Kiểm tra Khoa Constraint (Đang chứa lớp)
    $deleteResult2 = $khoaCheck->checkDeleteConstraints();
    SMLTestFramework::assert(is_string($deleteResult2), "Khoa Constraint - Ngăn cản xóa Khoa đang chứa Lớp học thành công");

    // --- 4. Test MonHoc ---
    $mh = new MonHoc($db);
    $mh->ma_mh = "MH-TEST";
    $mh->ten_mh = "Môn Học Bổ Trợ";
    $mhInsert = $mh->create();
    SMLTestFramework::assert($mhInsert, "MonHoc - Thêm mới Môn Học thành công");

    // --- 5. Test Thuật Toán Xếp Loại Của Module Điểm (Diem/Diem.php) ---
    require_once __DIR__ . '/../Diem/Diem.php';
    list($chuF, $gpaF) = Diem::xepLoai(3.9);
    SMLTestFramework::assertEquals("F", $chuF, "Diem Algorithm - <= 3.9 là điểm F");
    SMLTestFramework::assertEquals(0.0, $gpaF, "Diem Algorithm - F có GPA là 0.0");
    
    list($chuB, $gpaB) = Diem::xepLoai(7.5);
    SMLTestFramework::assertEquals("B", $chuB, "Diem Algorithm - 7.5 xếp chữ B");
    
    list($chuA, $gpaA) = Diem::xepLoai(9.2);
    SMLTestFramework::assertEquals("A", $chuA, "Diem Algorithm - 9.2 xếp chữ A (4.0)");

} catch (Exception $e) {
    SMLTestFramework::$results[] = "🔴 Lỗi Logic nghiêm trọng: " . $e->getMessage();
}

// Rollback để xóa toàn bộ rác test khỏi Database!
$db->rollBack();

// In nội dung output
SMLTestFramework::$results[] = "--------------------------------------------------";
SMLTestFramework::$results[] = "Kết quả: Passed " . SMLTestFramework::$passed . " - Failed " . SMLTestFramework::$failed;

echo implode("\n", SMLTestFramework::$results);
?>
