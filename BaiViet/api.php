<?php
/**
 * BaiViet/api.php — REST API cho module Bài Viết
 */

require_once __DIR__ . '/../config/database.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

// --- Helpers ---
function db(): PDO {
    static $pdo;
    if (!$pdo) {
        $d = new Database();
        $pdo = $d->getConnection();
    }
    return $pdo;
}

function json(bool $ok, string $msg, $data = null): void {
    echo json_encode(['success' => $ok, 'message' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function isGV(): bool {
    return ($_SESSION['vai_tro'] ?? '') === 'Giang_vien';
}

function requireGV(): void {
    if (!isGV()) json(false, 'Không có quyền thực hiện thao tác này.', null);
}

// --- Router ---
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_GET['_method']) && $_GET['_method'] === 'PUT') {
    $method = 'PUT';
}
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// GET danh sách chuyên mục
if ($method === 'GET' && isset($_GET['categories'])) {
    $rows = db()->query("SELECT DISTINCT chuyen_muc FROM bai_viets WHERE trang_thai='Published' AND chuyen_muc IS NOT NULL AND chuyen_muc!='' ORDER BY chuyen_muc")
                ->fetchAll(PDO::FETCH_COLUMN);
    json(true, 'OK', $rows);
}

// GET chi tiết 1 bài viết
if ($method === 'GET' && $id) {
    db()->query("UPDATE bai_viets SET luot_xem = luot_xem + 1 WHERE id = $id");

    $stmt = db()->prepare("SELECT b.*, n.username AS tac_gia_ten FROM bai_viets b LEFT JOIN nguoi_dungs n ON b.tac_gia_id = n.id WHERE b.id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) json(false, 'Không tìm thấy bài viết.');

    $rel = db()->prepare("SELECT id, tieu_de, thumbnail, chuyen_muc, created_at FROM bai_viets WHERE chuyen_muc = ? AND id != ? AND trang_thai = 'Published' ORDER BY created_at DESC LIMIT 3");
    $rel->execute([$article['chuyen_muc'], $id]);
    $article['lien_quan'] = $rel->fetchAll(PDO::FETCH_ASSOC);

    json(true, 'OK', $article);
}

// GET danh sách
if ($method === 'GET') {
    $where  = isGV() ? '1=1' : "b.trang_thai = 'Published'";
    $params = [];

    if (!empty($_GET['search'])) {
        $where   .= " AND (b.tieu_de LIKE ? OR b.tom_tat LIKE ?)";
        $params[] = "%{$_GET['search']}%";
        $params[] = "%{$_GET['search']}%";
    }
    if (!empty($_GET['cat'])) {
        $where   .= " AND b.chuyen_muc = ?";
        $params[] = $_GET['cat'];
    }

    $stmt = db()->prepare("SELECT b.*, n.username AS tac_gia_ten FROM bai_viets b LEFT JOIN nguoi_dungs n ON b.tac_gia_id = n.id WHERE $where ORDER BY b.created_at DESC");
    $stmt->execute($params);
    json(true, 'OK', $stmt->fetchAll(PDO::FETCH_ASSOC));
}

// POST thêm mới
if ($method === 'POST') {
    requireGV();
    $thumbnail = null;
    if (!empty($_FILES['thumbnail']['tmp_name'])) {
        $filename  = time() . '_' . basename($_FILES['thumbnail']['name']);
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], __DIR__ . '/../public/uploads/' . $filename);
        $thumbnail = $filename;
    }
    $body = $_POST;

    $stmt = db()->prepare("INSERT INTO bai_viets (tac_gia_id, chuyen_muc, tieu_de, tom_tat, thumbnail, noi_dung, is_hot, trang_thai) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->execute([
        $body['tac_gia_id'] ?? $_SESSION['user_id'] ?? 1,
        $body['chuyen_muc'] ?? 'Tin tuc',
        $body['tieu_de'] ?? '',
        $body['tom_tat'] ?? '',
        $thumbnail,
        $body['noi_dung'] ?? '',
        isset($body['is_hot']) ? 1 : 0,
        $body['trang_thai'] ?? 'Draft',
    ]);
    json(true, 'Thêm bài viết thành công!', ['id' => db()->lastInsertId()]);
}

// PUT cập nhật
if ($method === 'PUT') {
    requireGV();
    if (!$id) json(false, 'Thiếu ID.');

    $thumbSQL = '';
    $params   = [];
    $body     = $_POST;
    
    if (!empty($_FILES['thumbnail']['tmp_name'])) {
        $filename  = time() . '_' . basename($_FILES['thumbnail']['name']);
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], __DIR__ . '/../public/uploads/' . $filename);
        $thumbSQL  = 'thumbnail=?,';
        $params[]  = $filename;
    }

    array_push($params,
        $body['tac_gia_id'] ?? $_SESSION['user_id'] ?? 1,
        $body['chuyen_muc'] ?? 'Tin tuc',
        $body['tieu_de'] ?? '',
        $body['tom_tat'] ?? '',
        $body['noi_dung'] ?? '',
        isset($body['is_hot']) ? 1 : 0,
        $body['trang_thai'] ?? 'Draft',
        $id
    );

    $stmt = db()->prepare("UPDATE bai_viets SET {$thumbSQL}tac_gia_id=?,chuyen_muc=?,tieu_de=?,tom_tat=?,noi_dung=?,is_hot=?,trang_thai=? WHERE id=?");
    $stmt->execute($params);
    json(true, 'Cập nhật thành công!');
}

// DELETE xóa
if ($method === 'DELETE') {
    requireGV();
    if (!$id) json(false, 'Thiếu ID.');

    $row = db()->prepare("SELECT thumbnail FROM bai_viets WHERE id=?");
    $row->execute([$id]);
    $r = $row->fetch(PDO::FETCH_ASSOC);
    if ($r && $r['thumbnail'] && file_exists(__DIR__ . '/../public/uploads/' . $r['thumbnail'])) {
        unlink(__DIR__ . '/../public/uploads/' . $r['thumbnail']);
    }

    db()->prepare("DELETE FROM bai_viets WHERE id=?")->execute([$id]);
    json(true, 'Đã xóa bài viết!');
}

json(false, 'Method not allowed.', null);
