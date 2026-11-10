<?php

class Dashboard {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getKPIs() {
        $query = "SELECT 
            (SELECT COUNT(*) FROM khoas) as khoa,
            (SELECT COUNT(*) FROM lops) as lop,
            (SELECT COUNT(*) FROM sinh_viens) as sv,
            (SELECT COUNT(*) FROM mon_hocs) as mon,
            (SELECT COUNT(*) FROM giang_viens) as gv";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getGenderStats() {
        $query = "SELECT gioi_tinh, COUNT(*) as total FROM sinh_viens GROUP BY gioi_tinh";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCountryStats() {
        $query = "SELECT sv_nuoc_ngoai, COUNT(*) as total FROM sinh_viens GROUP BY sv_nuoc_ngoai";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getScoreStats() {
        // tong = (diem_tp*10 + diem_gk*30 + diem_ck*60)/100
        $query = "SELECT 
            SUM(CASE WHEN (diem_tp*10 + diem_gk*30 + diem_ck*60)/100 < 5.0 THEN 1 ELSE 0 END) as duoi5,
            SUM(CASE WHEN (diem_tp*10 + diem_gk*30 + diem_ck*60)/100 >= 5.0 AND (diem_tp*10 + diem_gk*30 + diem_ck*60)/100 <= 8.0 THEN 1 ELSE 0 END) as tu5den8,
            SUM(CASE WHEN (diem_tp*10 + diem_gk*30 + diem_ck*60)/100 > 8.0 THEN 1 ELSE 0 END) as tren8
        FROM diems";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAttendanceStats() {
        $query = "SELECT
            SUM(CASE WHEN so_vang = 1 THEN 1 ELSE 0 END) as buoi1,
            SUM(CASE WHEN so_vang = 2 THEN 1 ELSE 0 END) as buoi2,
            SUM(CASE WHEN so_vang = 3 THEN 1 ELSE 0 END) as buoi3,
            SUM(CASE WHEN so_vang >= 4 THEN 1 ELSE 0 END) as buoi4
        FROM (
            SELECT sinh_vien_id, mon_hoc_id, COUNT(*) as so_vang 
            FROM diem_danhs 
            WHERE trang_thai_vang = 1 
            GROUP BY sinh_vien_id, mon_hoc_id
        ) sub";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTeacherStats() {
        $query = "SELECT trinh_do, COUNT(*) as total FROM giang_viens GROUP BY trinh_do";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
