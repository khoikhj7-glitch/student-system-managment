<?php
// api/detai.php — CRUD Đề tài
require_once __DIR__ . '/../auth.php';
requireLogin();

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDB();
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Helper functions
function getGvRecordId(PDO $db): int {
    $userId = (int)$_SESSION['user_id'];
    $stmt = $db->prepare("SELECT id FROM nguoi_dungs WHERE vai_tro = 'Giang_vien' ORDER BY id ASC");
    $stmt->execute();
    $gvUsers = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $rank = array_search($userId, $gvUsers);
    if ($rank === false) return 0;

    $stmt2 = $db->prepare("SELECT id FROM giang_viens ORDER BY id ASC LIMIT 1 OFFSET ?");
    $stmt2->execute([$rank]);
    $row = $stmt2->fetch();
    return $row ? (int)$row['id'] : 0;
}

function getSvRecordId(PDO $db): int {
    $userId = (int)$_SESSION['user_id'];
    $stmt = $db->prepare("SELECT id FROM nguoi_dungs WHERE vai_tro = 'Sinh_vien' ORDER BY id ASC");
    $stmt->execute();
    $svUsers = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $rank = array_search($userId, $svUsers);
    if ($rank === false) return 0;

    $stmt2 = $db->prepare("SELECT id FROM sinh_viens ORDER BY id ASC LIMIT 1 OFFSET ?");
    $stmt2->execute([$rank]);
    $row = $stmt2->fetch();
    return $row ? (int)$row['id'] : 0;
}

// ====================== GET ======================
if ($method === 'GET') {
    if ($id) {
        // Chi tiết 1 đề tài
        $stmt = $db->prepare("
            SELECT dt.*, gv.ho_ten AS ten_giang_vien,
                   (SELECT COUNT(*) FROM dang_ky_de_tais WHERE de_tai_id = dt.id) AS so_dang_ky
            FROM de_tais dt
            JOIN giang_viens gv ON gv.id = dt.giang_vien_id
            WHERE dt.id = ?
        ");
        $stmt->execute([$id]);
        $dt = $stmt->fetch();
        if (!$dt) jsonResponse(false, 'Không tìm thấy đề tài.', [], 404);

        if (isGiangVien()) {
            $gvId = getGvRecordId($db);
            if ((int)$dt['giang_vien_id'] !== $gvId) {
                jsonResponse(false, 'Bạn không có quyền xem đề tài này.', [], 403);
            }
        }

        $svStmt = $db->prepare("
            SELECT dkdt.*, sv.ho_ten, sv.ma_sv 
            FROM dang_ky_de_tais dkdt
            JOIN sinh_viens sv ON sv.id = dkdt.sinh_vien_id
            WHERE dkdt.de_tai_id = ?
        ");
        $svStmt->execute([$id]);
        $dt['dang_ky'] = $svStmt->fetchAll();

        jsonResponse(true, 'OK', $dt);

    } else {
        // Danh sách đề tài
        if (isGiangVien()) {
            $gvId = getGvRecordId($db);
            if (!$gvId) jsonResponse(false, 'Không tìm thấy giảng viên.', [], 404);

            $stmt = $db->prepare("
                SELECT dt.*, gv.ho_ten AS ten_giang_vien,
                       (SELECT COUNT(*) FROM dang_ky_de_tais WHERE de_tai_id = dt.id) AS so_dang_ky
                FROM de_tais dt
                JOIN giang_viens gv ON gv.id = dt.giang_vien_id
                WHERE dt.giang_vien_id = ?
                ORDER BY dt.created_at DESC
            ");
            $stmt->execute([$gvId]);
            jsonResponse(true, 'OK', $stmt->fetchAll());
        } else {
            // Sinh viên xem đề tài mở đăng ký
            $svId = getSvRecordId($db);
            $stmt = $db->prepare("
                SELECT dt.*,
                       gv.ho_ten AS ten_giang_vien,
                       (SELECT COUNT(*) FROM dang_ky_de_tais WHERE de_tai_id = dt.id) AS so_dang_ky,
                       (SELECT id FROM dang_ky_de_tais WHERE de_tai_id = dt.id AND sinh_vien_id = ?) AS da_dang_ky_id,
                       (SELECT trang_thai FROM dang_ky_de_tais WHERE de_tai_id = dt.id AND sinh_vien_id = ?) AS trang_thai_dk
                FROM de_tais dt
                JOIN giang_viens gv ON gv.id = dt.giang_vien_id
                WHERE dt.trang_thai = 'Mo_dang_ky'
                ORDER BY dt.created_at DESC
            ");
            $stmt->execute([$svId, $svId]);
            jsonResponse(true, 'OK', ['list' => $stmt->fetchAll()]);
        }
    }
}

// ====================== POST ======================
if ($method === 'POST') {
    requireGiangVien();

    $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $tenDeTai = trim($body['ten_de_tai'] ?? '');
    if (!$tenDeTai) jsonResponse(false, 'Tên đề tài không được để trống.', [], 400);

    $gvId = getGvRecordId($db);
    if (!$gvId) jsonResponse(false, 'Không tìm thấy thông tin giảng viên.', [], 404);

    $stmt = $db->prepare("
        INSERT INTO de_tais (giang_vien_id, ten_de_tai, mo_ta, so_luong_sv, trang_thai)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $gvId,
        $tenDeTai,
        trim($body['mo_ta'] ?? ''),
        (int)($body['so_luong_sv'] ?? 1),
        $body['trang_thai'] ?? 'Mo_dang_ky'
    ]);

    jsonResponse(true, 'Tạo đề tài thành công!', ['id' => $db->lastInsertId()]);
}

// PUT và DELETE giữ nguyên (bạn đã viết khá tốt)

// --- TRONG FILE api/dangkydetai.php ---

if ($method === 'PUT') {
    // Chỉ giảng viên mới có quyền chấm điểm
    requireGiangVien();

    $body = json_decode(file_get_contents('php://input'), true);
    if (!$id) {
        jsonResponse(false, 'Thiếu ID bản ghi.', [], 400);
    }

    $fields = [];
    $params = [];

    // Xử lý cập nhật điểm số
    if (isset($body['diem_so'])) {
        $fields[] = "diem_so = ?";
        $params[] = $body['diem_so'];
    }

    // Tự động chuyển trạng thái khi có điểm (nếu FE gửi lên)
    if (isset($body['trang_thai'])) {
        $fields[] = "trang_thai = ?";
        $params[] = $body['trang_thai'];
    }

    if (empty($fields)) {
        jsonResponse(false, 'Không có dữ liệu thay đổi.', [], 400);
    }

    $params[] = $id;
    $sql = "UPDATE dang_ky_de_tais SET " . implode(', ', $fields) . " WHERE id = ?";
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse(true, 'Đã cập nhật điểm số thành công.');
    } catch (Exception $e) {
        jsonResponse(false, 'Lỗi CSDL: ' . $e->getMessage(), [], 500);
    }
}
if ($method === 'DELETE') {
    try {
        requireGiangVien(); // Chỉ giảng viên mới có quyền xóa đề tài

        if (!$id) {
            jsonResponse(false, 'Thiếu ID đề tài cần xóa.', [], 400);
        }

        $gvId = getGvRecordId($db); // Sử dụng hàm helper của bạn (đảm bảo đã sửa lỗi OFFSET)

        // 1. Kiểm tra xem đề tài có tồn tại và thuộc về giảng viên này không
        $stmt = $db->prepare("SELECT id FROM de_tais WHERE id = ? AND giang_vien_id = ?");
        $stmt->execute([$id, $gvId]);
        if (!$stmt->fetch()) {
            jsonResponse(false, 'Đề tài không tồn tại hoặc bạn không có quyền xóa.', [], 403);
        }

        // 2. Kiểm tra xem đã có sinh viên nào đăng ký chưa
        // (Ràng buộc: Thường không nên xóa đề tài đã có người thực hiện)
        $ck = $db->prepare("SELECT COUNT(*) FROM dang_ky_de_tais WHERE de_tai_id = ?");
        $ck->execute([$id]);
        if ($ck->fetchColumn() > 0) {
            jsonResponse(false, 'Không thể xóa đề tài đã có sinh viên đăng ký.', [], 400);
        }

        // 3. Tiến hành xóa
        $db->prepare("DELETE FROM de_tais WHERE id = ?")->execute([$id]);
        
        jsonResponse(true, 'Đã xóa đề tài thành công!');

    } catch (Exception $e) {
        jsonResponse(false, 'Lỗi hệ thống: ' . $e->getMessage(), [], 500);
    }
}

jsonResponse(false, 'Method not allowed', [], 405);