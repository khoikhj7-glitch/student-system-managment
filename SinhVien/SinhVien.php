<?php

class SinhVien {
    private $conn;
    private $table_name = "sinh_viens";

    public $id;
    public $ma_sv;
    public $ho_ten;
    public $ngay_sinh;
    public $gioi_tinh;
    public $sv_nuoc_ngoai;
    public $lop_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get filter data helpers
    public function getAllKhoas() {
        return $this->conn->query("SELECT * FROM khoas")->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllLops() {
        return $this->conn->query("SELECT * FROM lops")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDanhSach($filters = []) {
        $sql = "SELECT sv.*, l.ten_lop, k.ten_khoa 
                FROM " . $this->table_name . " sv 
                LEFT JOIN lops l ON sv.lop_id = l.id 
                LEFT JOIN khoas k ON l.khoa_id = k.id
                WHERE sv.ma_sv LIKE :ma AND sv.ho_ten LIKE :ten";

        if (!empty($filters['khoa_id'])) $sql .= " AND k.id = :kid";
        if (!empty($filters['lop_id']))  $sql .= " AND l.id = :lid";
        if (!empty($filters['ngay_sinh'])) $sql .= " AND sv.ngay_sinh = :ns";

        $sql .= " ORDER BY sv.id DESC";

        $stmt = $this->conn->prepare($sql);

        // Bind params safely
        $stmt->bindValue(':ma', "%" . ($filters['ma_sv'] ?? '') . "%");
        $stmt->bindValue(':ten', "%" . ($filters['ho_ten'] ?? '') . "%");

        if (!empty($filters['khoa_id'])) $stmt->bindValue(':kid', $filters['khoa_id']);
        if (!empty($filters['lop_id']))  $stmt->bindValue(':lid', $filters['lop_id']);
        if (!empty($filters['ngay_sinh'])) $stmt->bindValue(':ns', $filters['ngay_sinh']);

        $stmt->execute();
        return $stmt;
    }

    public function save() {
        if (!empty($this->id)) {
            // Update
            $sql = "UPDATE " . $this->table_name . " SET ma_sv=?, ho_ten=?, ngay_sinh=?, gioi_tinh=?, sv_nuoc_ngoai=?, lop_id=? WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$this->ma_sv, $this->ho_ten, $this->ngay_sinh, $this->gioi_tinh, $this->sv_nuoc_ngoai, $this->lop_id, $this->id]);
        } else {
            // Insert
            $sql = "INSERT INTO " . $this->table_name . " (ma_sv, ho_ten, ngay_sinh, gioi_tinh, sv_nuoc_ngoai, lop_id) VALUES (?,?,?,?,?,?)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$this->ma_sv, $this->ho_ten, $this->ngay_sinh, $this->gioi_tinh, $this->sv_nuoc_ngoai, $this->lop_id]);
        }
    }

    public function delete() {
        $sql = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$this->id]);
    }

    public function importCSV($filePath) {
        if (file_exists($filePath)) {
            $handle = fopen($filePath, "r");
            fgetcsv($handle); // Skip header
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) >= 6) {
                    $sql = "INSERT INTO " . $this->table_name . " (ma_sv, ho_ten, ngay_sinh, gioi_tinh, sv_nuoc_ngoai, lop_id) 
                            VALUES (?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE ho_ten=VALUES(ho_ten), ngay_sinh=VALUES(ngay_sinh)";
                    $this->conn->prepare($sql)->execute([$data[0], $data[1], $data[2], $data[3], $data[4], $data[5]]);
                }
            }
            fclose($handle);
            return true;
        }
        return false;
    }
}
?>
