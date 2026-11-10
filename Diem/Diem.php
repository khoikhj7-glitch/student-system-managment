<?php
class Diem {
    private $conn;
    private $table_name = "diems";

    public $id;
    public $sinh_vien_id;
    public $mon_hoc_id;
    public $diem_tp;
    public $diem_gk;
    public $diem_ck;

    public function __construct($db) {
        $this->conn = $db;
    }

    public static function xepLoai($diem) {
        if($diem >= 8.5) return ['A', 4.0];
        if($diem >= 7.8) return ['B+', 3.5];
        if($diem >= 7.0) return ['B', 3.0];
        if($diem >= 6.3) return ['C+', 2.5];
        if($diem >= 5.5) return ['C', 2.0];
        if($diem >= 4.8) return ['D+', 1.5];
        if($diem >= 4.0) return ['D', 1.0];
        return ['F', 0.0];
    }

    public function getAllMonHocs() {
        return $this->conn->query("SELECT * FROM mon_hocs");
    }

    public function getAllSinhViens() {
        return $this->conn->query("SELECT * FROM sinh_viens ORDER BY ma_sv ASC");
    }

    public function getDiemsByMonHoc($mon_hoc_id) {
        $query = "SELECT d.*, sv.ma_sv, sv.ho_ten 
                  FROM diems d 
                  JOIN sinh_viens sv ON d.sinh_vien_id = sv.id 
                  WHERE d.mon_hoc_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$mon_hoc_id]);
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (sinh_vien_id, mon_hoc_id, diem_tp, diem_gk, diem_ck) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->sinh_vien_id, $this->mon_hoc_id, $this->diem_tp, $this->diem_gk, $this->diem_ck]);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET diem_tp=?, diem_gk=?, diem_ck=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->diem_tp, $this->diem_gk, $this->diem_ck, $this->id]);
    }

   public function delete() {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    return $stmt->execute([$this->id]);
}
}
?>
