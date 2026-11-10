<?php
// api/dangkydetai.php — Quản lý Đăng ký, Tiến độ và Chấm điểm
// Đảm bảo đường dẫn require_once chính xác tới auth.php
require_once __DIR__ . '/../auth.php'; 

// Thiết lập phản hồi JSON ngay lập tức
header('Content-Type: application/json; charset=utf-8');

// Bắt buộc phải đăng nhập
if (!isLoggedIn()) {
    jsonResponse(false, 'Vui lòng đăng nhập để thực hiện.', [], 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDB();
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/**
 * Lấy ID thực tế trong bảng sinh_viens/giang_viens dựa trên user_id session
 * (Giữ nguyên logic tính rank/offset của bạn nhưng viết gọn lại)
 */
function getRecordIdByRole(PDO $db, string $role): int {
    $userId = (int)$_SESSION['user_id'];
    $table  = ($role === 'Giang_vien') ? 'giang_viens' : 'sinh_viens';

    $stmt = $db->prepare("SELECT id FROM nguoi_dungs WHERE vai_tro = ? ORDER BY id ASC");
    $stmt->execute([$role]);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $rank = array_search($userId, $ids);

    if ($rank === false) return 0;

    $stmt2 = $db->prepare("SELECT id FROM $table ORDER BY id ASC LIMIT 1 OFFSET ?");
    $stmt2->execute([$rank]);
    $row = $stmt2->fetch();
    return $row ? (int)$row['id'] : 0;
}

// ── [GET] XEM CHI TIẾT ĐĂNG KÝ ─────────────────────────────
if ($method === 'GET') {
    if (!$id) jsonResponse(false, 'Thiếu ID đăng ký.', [], 400);

    $stmt = $db->prepare("
        SELECT dkdt.*, 
               sv.ho_ten AS ten_sv, sv.ma_sv,
               dt.ten_de_tai, dt.giang_vien_id,
               gv.ho_ten AS ten_giang_vien
        FROM dang_ky_de_tais dkdt
        JOIN sinh_viens sv  ON sv.id = dkdt.sinh_vien_id
        JOIN de_tais dt     ON dt.id = dkdt.de_tai_id
        JOIN giang_viens gv ON gv.id = dt.giang_vien_id
        WHERE dkdt.id = ?
    ");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!$row) jsonResponse(false, 'Không tìm thấy thông tin đăng ký.', [], 404);
    jsonResponse(true, 'Thành công', $row);
}

// ── [POST] SINH VIÊN ĐĂNG KÝ ĐỀ TÀI ────────────────────────
if ($method === 'POST') {
    requireSinhVien();
    $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $deTaiId = (int)($body['de_tai_id'] ?? 0);

    if (!$deTaiId) jsonResponse(false, 'Vui lòng chọn đề tài.', [], 400);

    $svId = getRecordIdByRole($db, 'Sinh_vien');
    if (!$svId) jsonResponse(false, 'Lỗi hệ thống: Không xác định được SV.', [], 500);

    // Kiểm tra đề tài
    $dt = $db->prepare("SELECT trang_thai FROM de_tais WHERE id = ?");
    $dt->execute([$deTaiId]);
    $deTai = $dt->fetch();

    if (!$deTai) jsonResponse(false, 'Đề tài không tồn tại.', [], 404);
    if ($deTai['trang_thai'] !== 'Mo_dang_ky') jsonResponse(false, 'Đề tài này đang đóng đăng ký.', [], 400);

    // Kiểm tra SV đã đăng ký chưa
    $ck = $db->prepare("SELECT id FROM dang_ky_de_tais WHERE sinh_vien_id = ?");
    $ck->execute([$svId]);
    if ($ck->fetch()) jsonResponse(false, 'Bạn đã đăng ký một đề tài khác rồi.', [], 400);

    $stmt = $db->prepare("INSERT INTO dang_ky_de_tais (de_tai_id, sinh_vien_id, trang_thai) VALUES (?, ?, 'Cho_duyet')");
    $stmt->execute([$deTaiId, $svId]);
    
    jsonResponse(true, 'Đăng ký thành công! Vui lòng chờ duyệt.', ['id' => $db->lastInsertId()]);
}

// ── [PUT] CẬP NHẬT ĐIỂM (GV) HOẶC TIẾN ĐỘ (SV) ──────────────
// ── [PUT] CẬP NHẬT ĐIỂM (GV) HOẶC TIẾN ĐỘ (SV) ──────────────
if ($method === 'PUT') {
    try {
        // 1. Kiểm tra ID đầu vào
        if (!$id) {
            jsonResponse(false, 'Thiếu ID bản ghi.', [], 400);
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];

        // 2. Lấy thông tin bản ghi đăng ký hiện tại từ database
        $stmt = $db->prepare("SELECT dk.*, dt.giang_vien_id FROM dang_ky_de_tais dk JOIN de_tais dt ON dt.id = dk.de_tai_id WHERE dk.id = ?");
        $stmt->execute([$id]);
        $rec = $stmt->fetch();

        if (!$rec) {
            jsonResponse(false, 'Không tìm thấy dữ liệu đăng ký.', [], 404);
        }

        $fields = [];
        $params = [];

        // 3. Xử lý logic theo vai trò
        if (isGiangVien()) {
            $gvId = getRecordIdByRole($db, 'Giang_vien');
            
            // Kiểm tra xem giảng viên này có phải là người hướng dẫn đề tài này không
            if ($gvId === 0 || (int)$rec['giang_vien_id'] !== $gvId) {
                jsonResponse(false, 'Bạn không có quyền quản lý hoặc chấm điểm đề tài này.', [], 403);
            }

            // Xử lý Chấm điểm
            if (isset($body['diem_so'])) {
                $ds = (float)$body['diem_so'];
                if ($ds < 0 || $ds > 10) {
                    jsonResponse(false, 'Điểm số phải nằm trong khoảng từ 0 đến 10.', [], 400);
                }
                
                $fields[] = 'diem_so = ?';
                $params[] = $ds;
                $fields[] = 'trang_thai = ?';
                $params[] = 'Da_cham';
            }

            // Xử lý Nhận xét
            if (isset($body['nhan_xet'])) {
                $fields[] = 'nhan_xet = ?';
                $params[] = trim($body['nhan_xet']);
            }
        } 
        elseif (isSinhVien()) {
            $svId = getRecordIdByRole($db, 'Sinh_vien');
            
            // Kiểm tra xem đây có đúng là đề tài sinh viên này đã đăng ký không
            if ($svId === 0 || (int)$rec['sinh_vien_id'] !== $svId) {
                jsonResponse(false, 'Quyền truy cập bị từ chối: Đây không phải đề tài của bạn.', [], 403);
            }

            // Cập nhật tiến độ %
            if (isset($body['tien_do'])) {
                $fields[] = 'tien_do = ?';
                $params[] = (int)$body['tien_do'];
            }

            // Cập nhật tên file/link báo cáo
            if (isset($body['file_bao_cao'])) {
                $fields[] = 'file_bao_cao = ?';
                $params[] = trim($body['file_bao_cao']);
            }

            // Đánh dấu nộp bài
            if (!empty($body['nop_bao_cao'])) {
                $fields[] = 'trang_thai = ?';
                $params[] = 'Da_nop';
            }
        }

        // 4. Kiểm tra xem có dữ liệu nào để cập nhật không
        if (empty($fields)) {
            jsonResponse(false, 'Không có thông tin mới để cập nhật.', [], 400);
        }

        // 5. Thực thi câu lệnh UPDATE
        $params[] = $id;
        $sql = "UPDATE dang_ky_de_tais SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $db->prepare($sql)->execute($params);
        
        // Trả về phản hồi thành công
        jsonResponse(true, 'Cập nhật dữ liệu thành công!');

    } catch (Exception $e) {
        // Bắt mọi lỗi hệ thống phát sinh trong khối try và trả về JSON lỗi
        jsonResponse(false, 'Lỗi hệ thống: ' . $e->getMessage(), [], 500);
    }
}
// ── [DELETE] SINH VIÊN HỦY ĐĂNG KÝ ─────────────────────────
if ($method === 'DELETE') {
    requireSinhVien();
    $svId = getRecordIdByRole($db, 'Sinh_vien');
    
    $stmt = $db->prepare("SELECT trang_thai FROM dang_ky_de_tais WHERE id = ? AND sinh_vien_id = ?");
    $stmt->execute([$id, $svId]);
    $rec = $stmt->fetch();

    if (!$rec) jsonResponse(false, 'Không tìm thấy hoặc không có quyền xóa.', [], 404);
    if ($rec['trang_thai'] !== 'Cho_duyet') jsonResponse(false, 'Đề tài đã duyệt không thể tự hủy.', [], 400);

    $db->prepare("DELETE FROM dang_ky_de_tais WHERE id = ?")->execute([$id]);
    jsonResponse(true, 'Đã hủy đăng ký thành công.');
}

// Nếu không khớp method nào
jsonResponse(false, 'Phương thức không được hỗ trợ.', [], 405);