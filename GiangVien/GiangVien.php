<?php

class GiangVien {
    private $conn;
    private $table_name = "giang_viens";

    public $id;
    public $ma_gv;
    public $ho_ten;
    public $trinh_do;
    public $khoa_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllKhoas() {
        return $this->conn->query("SELECT * FROM khoas")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDanhSach($search_name = "") {
        $sql = "SELECT gv.*, k.ten_khoa 
                FROM " . $this->table_name . " gv 
                LEFT JOIN khoas k ON gv.khoa_id = k.id 
                WHERE gv.ho_ten LIKE :ten OR gv.ma_gv LIKE :ten
                ORDER BY gv.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':ten' => "%$search_name%"]);
        return $stmt;
    }

    public function save() {
        if (!empty($this->id)) {
            $sql = "UPDATE " . $this->table_name . " SET ma_gv=?, ho_ten=?, trinh_do=?, khoa_id=? WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$this->ma_gv, $this->ho_ten, $this->trinh_do, $this->khoa_id, $this->id]);
        } else {
            $sql = "INSERT INTO " . $this->table_name . " (ma_gv, ho_ten, trinh_do, khoa_id) VALUES (?,?,?,?)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$this->ma_gv, $this->ho_ten, $this->trinh_do, $this->khoa_id]);
        }
    }

    public function delete() {
        $sql = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$this->id]);
    }
}
?>
