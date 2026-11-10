<?php
require_once "config/database.php";
require_once "DiemDanh/DiemDanh.php";

class Controller {
    private $db;
    private $model;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new DiemDanh($this->db);
    }

    public function index() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_attendance'])) {
            $mh = $_POST['mh'];
            $ngay = $_POST['ngay'];
            $vangs = isset($_POST['vang']) ? $_POST['vang'] : [];

            foreach ($vangs as $sv_id => $vang) {
                if (!$this->model->checkExists($sv_id, $mh, $ngay)) {
                    $this->model->saveRecord($sv_id, $mh, $ngay, $vang);
                }
            }
            $success = "Đã lưu điểm danh ngày " . htmlspecialchars($ngay);
        }

        $monhocs = $this->model->getAllMonHocs()->fetchAll(PDO::FETCH_ASSOC);

        $attendanceBySubject = [];
        foreach ($monhocs as $mh) {
            $attendanceBySubject[$mh['id']] = $this->model->getStudentAttendance($mh['id'])->fetchAll(PDO::FETCH_ASSOC);
        }

        $view = "DiemDanh/index.php";
        require_once "core/layout.php";
    }
}
?>
