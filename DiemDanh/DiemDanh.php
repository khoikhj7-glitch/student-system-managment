<?php
class DiemDanh {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllMonHocs() {
        return $this->conn->query("SELECT * FROM mon_hocs");
    }

    public function getStudentAttendance($mon_hoc_id) {
        $query = "SELECT sv.id, sv.ma_sv, sv.ho_ten, 
                         COUNT(CASE WHEN d.trang_thai_vang=1 THEN 1 END) as so_vang
                  FROM sinh_viens sv
                  LEFT JOIN diem_danhs d ON sv.id = d.sinh_vien_id AND d.mon_hoc_id = ?
                  GROUP BY sv.id
                  ORDER BY sv.ma_sv ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$mon_hoc_id]);
        return $stmt;
    }

    public function checkExists($sinh_vien_id, $mon_hoc_id, $ngay) {
        $check = $this->conn->prepare("SELECT * FROM diem_danhs WHERE sinh_vien_id=? AND mon_hoc_id=? AND ngay_diem_danh=?");
        $check->execute([$sinh_vien_id, $mon_hoc_id, $ngay]);
        return $check->rowCount() > 0;
    }

    public function saveRecord($sinh_vien_id, $mon_hoc_id, $ngay, $vang) {
        $sql = "INSERT INTO diem_danhs(sinh_vien_id, mon_hoc_id, ngay_diem_danh, trang_thai_vang) VALUES (?, ?, ?, ?)";
        return $this->conn->prepare($sql)->execute([$sinh_vien_id, $mon_hoc_id, $ngay, $vang]);
    }
}
?>
