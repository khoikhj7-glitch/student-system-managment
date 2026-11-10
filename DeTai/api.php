<?php
/**
 * DeTai/api.php — REST API cho module Đề Tài
 * Hỗ trợ hai action: 'detai' và 'dangky'
 */
require_once __DIR__ . '/../config/database.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

function db(): PDO {
    static $pdo;
    if (!$pdo) {
        $d = new Database();
        $pdo = $d->getConnection();
    }
    return $pdo;
}

function json(bool $ok, string $msg, $data = null, int $code = 200): void {
    http_response_code($code);
    echo json_encode(['success' => $ok, 'message' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function isGV(): bool { return ($_SESSION['vai_tro'] ?? '') === 'Giang_vien'; }
function isSV(): bool { return ($_SESSION['vai_tro'] ?? '') === 'Sinh_vien'; }
function requireGV(): void { if (!isGV()) json(false, 'Chỉ Giảng viên thao tác', null, 403); }
function requireSV(): void { if (!isSV()) json(false, 'Chỉ Sinh viên thao tác', null, 403); }

// Mapping ID từ bảng nguoi_dungs sang giang_viens / sinh_viens
function getGvId() {
    $userId = $_SESSION['user_id'] ?? 0;
    $db = db();
    $stmt = $db->query("SELECT id FROM nguoi_dungs WHERE vai_tro='Giang_vien' ORDER BY id ASC");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $idx = array_search($userId, $users);
    if ($idx === false) return 0;
    $idx = (int)$idx;
    $gvStmt = $db->query("SELECT id FROM giang_viens ORDER BY id ASC LIMIT 1 OFFSET $idx");
    return (int)$gvStmt->fetchColumn() ?: 0;
}

function getSvId() {
    $userId = $_SESSION['user_id'] ?? 0;
    $db = db();
    $stmt = $db->query("SELECT id FROM nguoi_dungs WHERE vai_tro='Sinh_vien' ORDER BY id ASC");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $idx = array_search($userId, $users);
    if ($idx === false) return 0;
    $idx = (int)$idx;
    $svStmt = $db->query("SELECT id FROM sinh_viens ORDER BY id ASC LIMIT 1 OFFSET $idx");
    return (int)$svStmt->fetchColumn() ?: 0;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'detai'; // 'detai', 'dangky'
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$body = json_decode(file_get_contents('php://input'), true) ?? $_POST;

// ====================== ACTION: detai ======================
if ($action === 'detai') {
    if ($method === 'GET') {
        if ($id) {
            // Chi tiết 1 đề tài
            $stmt = db()->prepare("
                SELECT dt.*, gv.ho_ten AS ten_giang_vien,
                       (SELECT COUNT(*) FROM dang_ky_de_tais WHERE de_tai_id = dt.id) AS so_dang_ky
                FROM de_tais dt
                JOIN giang_viens gv ON gv.id = dt.giang_vien_id
                WHERE dt.id = ?");
            $stmt->execute([$id]);
            $dt = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$dt) json(false, 'Không tìm thấy.', null, 404);

            // Lấy danh sách SV đăng ký
            $sv = db()->prepare("
                SELECT dk.*, sv.ho_ten, sv.ma_sv 
                FROM dang_ky_de_tais dk
                JOIN sinh_viens sv ON sv.id = dk.sinh_vien_id
                WHERE dk.de_tai_id = ?");
            $sv->execute([$id]);
            $dt['dang_ky'] = $sv->fetchAll(PDO::FETCH_ASSOC);

            json(true, 'OK', $dt);
        } else {
            // Danh sách đề tài
            if (isGV()) {
                $gvId = getGvId();
                if (!$gvId) json(false, 'Không tìm thấy GV.', null, 404);
                $stmt = db()->prepare("
                    SELECT dt.*, gv.ho_ten AS ten_giang_vien,
                           (SELECT COUNT(*) FROM dang_ky_de_tais WHERE de_tai_id = dt.id) AS so_dang_ky
                    FROM de_tais dt
                    JOIN giang_viens gv ON gv.id = dt.giang_vien_id
                    WHERE dt.giang_vien_id = ?
                    ORDER BY dt.created_at DESC");
                $stmt->execute([$gvId]);
                json(true, 'OK', $stmt->fetchAll(PDO::FETCH_ASSOC));
            } else {
                $svId = getSvId();
                $stmt = db()->prepare("
                    SELECT dt.*, gv.ho_ten AS ten_giang_vien,
                           (SELECT COUNT(*) FROM dang_ky_de_tais WHERE de_tai_id = dt.id) AS so_dang_ky,
                           (SELECT id FROM dang_ky_de_tais WHERE de_tai_id = dt.id AND sinh_vien_id = ?) AS da_dang_ky_id,
                           (SELECT trang_thai FROM dang_ky_de_tais WHERE de_tai_id = dt.id AND sinh_vien_id = ?) AS trang_thai_dk
                    FROM de_tais dt
                    JOIN giang_viens gv ON gv.id = dt.giang_vien_id
                    WHERE dt.trang_thai = 'Mo_dang_ky' OR (SELECT id FROM dang_ky_de_tais WHERE de_tai_id = dt.id AND sinh_vien_id = ?) IS NOT NULL
                    ORDER BY dt.created_at DESC");
                $stmt->execute([$svId, $svId, $svId]);
                json(true, 'OK', ['list' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            }
        }
    }

    if ($method === 'POST') {
        requireGV();
        $gvId = getGvId();
        $ten = trim($body['ten_de_tai'] ?? '');
        if (!$ten) json(false, 'Tên không được rỗng.', null, 400);

        $stmt = db()->prepare("INSERT INTO de_tais (giang_vien_id, ten_de_tai, mo_ta, so_luong_sv, trang_thai) VALUES (?,?,?,?,?)");
        $stmt->execute([$gvId, $ten, trim($body['mo_ta'] ?? ''), 1, $body['trang_thai'] ?? 'Mo_dang_ky']);
        json(true, 'Tạo thành công!', ['id' => db()->lastInsertId()]);
    }

    if ($method === 'PUT') {
        requireGV();
        $gvId = getGvId();
        if (!$id) json(false, 'Thiếu ID.', null, 400);
        $stmt = db()->prepare("UPDATE de_tais SET ten_de_tai=?, mo_ta=?, trang_thai=? WHERE id=? AND giang_vien_id=?");
        $stmt->execute([trim($body['ten_de_tai'] ?? ''), trim($body['mo_ta'] ?? ''), $body['trang_thai'] ?? 'Mo_dang_ky', $id, $gvId]);
        json(true, 'Cập nhật thành công!');
    }

    if ($method === 'DELETE') {
        requireGV();
        $gvId = getGvId();
        
        $chk = db()->prepare("SELECT COUNT(*) FROM dang_ky_de_tais WHERE de_tai_id=?");
        $chk->execute([$id]);
        if ($chk->fetchColumn() > 0) json(false, 'Đã có SV đăng ký, không thể xóa.', null, 400);

        $stmt = db()->prepare("DELETE FROM de_tais WHERE id=? AND giang_vien_id=?");
        $stmt->execute([$id, $gvId]);
        json(true, 'Đã xóa!');
    }
}

// ====================== ACTION: dangky ======================
if ($action === 'dangky') {
    if ($method === 'GET') {
        if ($id) {
            // Lấy chi tiết đăng ký
            $stmt = db()->prepare("
                SELECT dk.*, dt.ten_de_tai, gv.ho_ten AS ten_giang_vien 
                FROM dang_ky_de_tais dk 
                JOIN de_tais dt ON dk.de_tai_id = dt.id 
                JOIN giang_viens gv ON dt.giang_vien_id = gv.id 
                WHERE dk.id = ?");
            $stmt->execute([$id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($res) json(true, 'OK', $res);
            json(false, 'Not found', null, 404);
        }
    }

    if ($method === 'POST') {
        requireSV();
        $svId = getSvId();
        $dtId = (int)($body['de_tai_id'] ?? 0);
        if (!$dtId) json(false, 'Thiếu de_tai_id', null, 400);

        $chk = db()->prepare("SELECT id FROM dang_ky_de_tais WHERE sinh_vien_id = ?");
        $chk->execute([$svId]);
        if ($chk->fetch()) json(false, 'Bạn đã đăng ký 1 đề tài rồi!', null, 400);

        $stmt = db()->prepare("INSERT INTO dang_ky_de_tais (sinh_vien_id, de_tai_id, trang_thai) VALUES (?, ?, 'Cho_duyet')");
        $stmt->execute([$svId, $dtId]);
        json(true, 'Đăng ký thành công!');
    }

    if ($method === 'PUT') {
        if (!$id) json(false, 'Thiếu ID.', null, 400);
        $fields = []; $params = [];

        if (isGV()) {
            if (isset($body['diem_so'])) { $fields[] = "diem_so=?"; $params[] = $body['diem_so']; }
            if (isset($body['trang_thai'])) { $fields[] = "trang_thai=?"; $params[] = $body['trang_thai']; }
            if (isset($body['nhan_xet'])) { $fields[] = "nhan_xet=?"; $params[] = $body['nhan_xet']; }
        } else {
            // SV cập nhật
            if (isset($body['tien_do'])) { $fields[] = "tien_do=?"; $params[] = $body['tien_do']; }
            if (isset($body['file_bao_cao'])) { $fields[] = "file_bao_cao=?"; $params[] = $body['file_bao_cao']; }
            // SV có thể nộp
            if (!empty($body['nop_bao_cao'])) { $fields[] = "trang_thai=?"; $params[] = 'Da_nop'; }
        }

        if (empty($fields)) json(false, 'Không có data', null, 400);
        $params[] = $id;
        
        $sql = "UPDATE dang_ky_de_tais SET " . implode(',', $fields) . " WHERE id=?";
        db()->prepare($sql)->execute($params);
        json(true, 'Cập nhật thành công!');
    }

    if ($method === 'DELETE') {
        requireSV();
        $svId = getSvId();
        db()->prepare("DELETE FROM dang_ky_de_tais WHERE id=? AND sinh_vien_id=? AND trang_thai='Cho_duyet'")->execute([$id, $svId]);
        json(true, 'Đã hủy đăng ký!');
    }
}

json(false, 'Method / Action not allowed', null, 405);
