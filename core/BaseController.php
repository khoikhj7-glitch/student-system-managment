<?php
/**
 * BaseController — Lớp cha cho tất cả Controller
 * Cung cấp: kết nối DB, JSON response, kiểm tra quyền.
 */

require_once __DIR__ . '/../config/database.php';

abstract class BaseController {
    protected PDO $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Trả JSON response chuẩn
     */
    protected function json(bool $success, string $message, $data = [], int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Đọc JSON body từ request
     */
    protected function getJsonBody(): array {
        $body = json_decode(file_get_contents('php://input'), true);
        return is_array($body) ? $body : [];
    }

    /**
     * Kiểm tra vai trò
     */
    protected function requireRole(string $role): void {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== $role) {
            $this->json(false, 'Bạn không có quyền thực hiện thao tác này.', [], 403);
        }
    }

    /**
     * Kiểm tra đã đăng nhập
     */
    protected function requireLogin(): void {
        if (!isset($_SESSION['user_id'])) {
            $this->json(false, 'Vui lòng đăng nhập.', [], 401);
        }
    }

    /**
     * Lấy user ID hiện tại
     */
    protected function userId(): int {
        return (int)($_SESSION['user_id'] ?? 0);
    }

    /**
     * Lấy vai trò hiện tại
     */
    protected function userRole(): string {
        return $_SESSION['vai_tro'] ?? '';
    }

    /**
     * Kiểm tra request method
     */
    protected function isMethod(string $method): bool {
        return $_SERVER['REQUEST_METHOD'] === strtoupper($method);
    }

    /**
     * Lấy param từ GET
     */
    protected function param(string $key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    /**
     * Mapping GV user_id -> giang_viens.id
     */
    protected function getGvRecordId(): int {
        $userId = $this->userId();
        $stmt = $this->db->prepare("SELECT id FROM nguoi_dungs WHERE vai_tro = 'Giang_vien' ORDER BY id ASC");
        $stmt->execute();
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $rank = array_search($userId, $ids);
        if ($rank === false) return 0;

        $stmt2 = $this->db->prepare("SELECT id FROM giang_viens ORDER BY id ASC LIMIT 1 OFFSET ?");
        $stmt2->execute([$rank]);
        $row = $stmt2->fetch();
        return $row ? (int)$row['id'] : 0;
    }

    /**
     * Mapping SV user_id -> sinh_viens.id
     */
    protected function getSvRecordId(): int {
        $userId = $this->userId();
        $stmt = $this->db->prepare("SELECT id FROM nguoi_dungs WHERE vai_tro = 'Sinh_vien' ORDER BY id ASC");
        $stmt->execute();
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $rank = array_search($userId, $ids);
        if ($rank === false) return 0;

        $stmt2 = $this->db->prepare("SELECT id FROM sinh_viens ORDER BY id ASC LIMIT 1 OFFSET ?");
        $stmt2->execute([$rank]);
        $row = $stmt2->fetch();
        return $row ? (int)$row['id'] : 0;
    }
}
