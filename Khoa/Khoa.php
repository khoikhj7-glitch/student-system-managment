<?php
class Khoa {
    private $conn;
    private $table_name = "khoas";

    public $id;
    public $ma_khoa;
    public $ten_khoa;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $sql = "SELECT k.*, 
                (SELECT COUNT(*) FROM giang_viens WHERE khoa_id = k.id) as tong_gv 
                FROM " . $this->table_name . " k 
                ORDER BY k.id DESC";
        return $this->conn->query($sql);
    }

    public function save() {
        if (!empty($this->id)) {
            $sql = "UPDATE " . $this->table_name . " SET ma_khoa=?, ten_khoa=? WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$this->ma_khoa, $this->ten_khoa, $this->id]);
        } else {
            $sql = "INSERT INTO " . $this->table_name . " (ma_khoa, ten_khoa) VALUES (?,?)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$this->ma_khoa, $this->ten_khoa]);
        }
    }

    public function checkDeleteConstraints() {
        // Trả về true nếu an toàn để xóa. False nếu vi phạm.
        
        // 1. Kiểm tra Giảng Viên
        $checkGV = $this->conn->prepare("SELECT COUNT(*) FROM giang_viens WHERE khoa_id = ?");
        $checkGV->execute([$this->id]);
        if ($checkGV->fetchColumn() > 0) return "Không thể xóa! Khoa này vẫn còn giảng viên đang trực thuộc.";

        // 2. Kiểm tra Lớp học
        $checkLop = $this->conn->prepare("SELECT COUNT(*) FROM lops WHERE khoa_id = ?");
        $checkLop->execute([$this->id]);
        if ($checkLop->fetchColumn() > 0) return "Không thể xóa! Khoa này đang có lớp học trực thuộc.";

        return true; 
    }

    public function delete() {
        $sql = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$this->id]);
    }

    public function getGiangViens() {
        $sql = "SELECT * FROM giang_viens WHERE khoa_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
