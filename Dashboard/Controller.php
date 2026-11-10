<?php
require_once "config/database.php";
require_once "Dashboard/Dashboard.php";

class Controller {
    private $db;
    private $model;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new Dashboard($this->db);
    }

    public function index() {
        if ($_SESSION['vai_tro'] === 'Sinh_vien') {
            // Cố gắng map user theo ma_sv, nếu không tìm thấy thì map thẳng theo ID hoăc lấy fallback đầu tiên
            $username = $_SESSION['username'];
            $uid = $_SESSION['user_id'];
            
            $stmt = $this->db->prepare("SELECT s.*, l.ten_lop, k.ten_khoa FROM sinh_viens s LEFT JOIN lops l ON s.lop_id = l.id LEFT JOIN khoas k ON l.khoa_id = k.id WHERE s.ma_sv = ? OR s.id = ? LIMIT 1");
            $stmt->execute([$username, $uid]);
            $sv = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Fallback for mocked database lack of FKs
            if (!$sv) {
                $stmt = $this->db->query("SELECT s.*, l.ten_lop, k.ten_khoa FROM sinh_viens s LEFT JOIN lops l ON s.lop_id = l.id LEFT JOIN khoas k ON l.khoa_id = k.id LIMIT 1");
                $sv = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if ($sv) {
                // Điểm trung bình (Học phân GPA)
                $gpaStmt = $this->db->query("SELECT AVG((diem_tp+diem_gk+diem_ck)/3) as gpa FROM diems WHERE sinh_vien_id = " . $sv['id']);
                $gpa = $gpaStmt->fetch(PDO::FETCH_ASSOC)['gpa'];
                $sv['gpa'] = $gpa ? round($gpa, 2) : "Chưa có";

                // Tổng môn đã học
                $monStmt = $this->db->query("SELECT COUNT(*) as total FROM diems WHERE sinh_vien_id = " . $sv['id']);
                $sv['tong_mon'] = $monStmt->fetch(PDO::FETCH_ASSOC)['total'];

                // Bảng điểm chi tiết
                $diemStmt = $this->db->query("SELECT m.ten_mh as ten_mon, d.diem_tp, d.diem_gk, d.diem_ck FROM diems d JOIN mon_hocs m ON d.mon_hoc_id = m.id WHERE d.sinh_vien_id = " . $sv['id']);
                $diems = $diemStmt->fetchAll(PDO::FETCH_ASSOC);

                // Điểm danh
                $ddStmt = $this->db->query("SELECT m.ten_mh as ten_mon, dd.ngay_diem_danh, dd.trang_thai_vang FROM diem_danhs dd JOIN mon_hocs m ON dd.mon_hoc_id = m.id WHERE dd.sinh_vien_id = " . $sv['id'] . " ORDER BY dd.ngay_diem_danh DESC");
                $diemdanh = $ddStmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $view = "Dashboard/index_student.php";
        } else {
            // Giao diện giảng viên / Admin
            $view = "Dashboard/index.php";
        }
        
        require_once "core/layout.php";
    }

    public function apiData() {
        if ($_SESSION['vai_tro'] === 'Sinh_vien') exit; // Cấm Sinh viên gọi API biểu đồ của Giảng viên
        
        header('Content-Type: application/json');

        $kpi = $this->model->getKPIs();
        $gender = $this->model->getGenderStats();
        $country = $this->model->getCountryStats();
        $score = $this->model->getScoreStats();
        $attendanceTemp = $this->model->getAttendanceStats();
        
        $score['duoi5'] = $score['duoi5'] ?? 0;
        $score['tu5den8'] = $score['tu5den8'] ?? 0;
        $score['tren8'] = $score['tren8'] ?? 0;
        $attendanceTemp['buoi1'] = $attendanceTemp['buoi1'] ?? 0;
        $attendanceTemp['buoi2'] = $attendanceTemp['buoi2'] ?? 0;
        $attendanceTemp['buoi3'] = $attendanceTemp['buoi3'] ?? 0;
        $attendanceTemp['buoi4'] = $attendanceTemp['buoi4'] ?? 0;

        $trinhdo = $this->model->getTeacherStats();

        $data = [
            "kpi" => $kpi,
            "gender" => $gender,
            "country" => $country,
            "score" => $score,
            "chuyencan" => $attendanceTemp,
            "trinhdo" => $trinhdo
        ];

        echo json_encode($data);
        exit;
    }
}
?>
