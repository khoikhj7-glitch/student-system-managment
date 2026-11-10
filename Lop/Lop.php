<?php
class Lop {
    private $conn;
    private $table_name = "lops";

    public $id;
    public $ma_lop;
    public $ten_lop;
    public $khoa_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $sql = "SELECT l.*, k.ten_khoa, 
                (SELECT COUNT(*) FROM sinh_viens WHERE lop_id = l.id) as tong_sv 
                FROM " . $this->table_name . " l 
                JOIN khoas k ON l.khoa_id = k.id 
                ORDER BY l.id DESC";
        return $this->conn->query($sql);
    }

    public function getKhoas() {
        return $this->conn->query("SELECT id, ten_khoa FROM khoas")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save() {
        if (!empty($this->id)) {
            $sql = "UPDATE " . $this->table_name . " SET ma_lop=?, ten_lop=?, khoa_id=? WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                htmlspecialchars(strip_tags($this->ma_lop)), 
                htmlspecialchars(strip_tags($this->ten_lop)), 
                $this->khoa_id, 
                $this->id
            ]);
        } else {
            $sql = "INSERT INTO " . $this->table_name . " (ma_lop, ten_lop, khoa_id) VALUES (?,?,?)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                htmlspecialchars(strip_tags($this->ma_lop)), 
                htmlspecialchars(strip_tags($this->ten_lop)), 
                $this->khoa_id
            ]);
        }
    }

    public function checkDeleteConstraints() {
        $checkSV = $this->conn->prepare("SELECT COUNT(*) FROM sinh_viens WHERE lop_id = ?");
        $checkSV->execute([$this->id]);
        if ($checkSV->fetchColumn() > 0) {
            return "Không thể xóa! Lớp này đang có Sinh Viên theo học.";
        }
        return true; 
    }

    public function delete() {
        $sql = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$this->id]);
    }

    public function getSinhViens() {
        $sql = "SELECT ma_sv, ho_ten, ngay_sinh, gioi_tinh FROM sinh_viens WHERE lop_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>