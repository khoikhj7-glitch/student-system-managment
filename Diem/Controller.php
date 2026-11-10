<?php
require_once "config/database.php";
require_once "Diem/Diem.php";

class Controller {
    private $db;
    private $model;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new Diem($this->db);
    }

    public function index() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
            $this->model->id = $_POST['id'];
            $this->model->diem_tp = floatval($_POST['tp']);
            $this->model->diem_gk = floatval($_POST['gk']);
            $this->model->diem_ck = floatval($_POST['ck']);
            
            if ($this->model->update()) {
                header("Location: index.php?f=Diem&action=index&msg=updated");
                exit;
            }
        }

        $monhocs = $this->model->getAllMonHocs()->fetchAll(PDO::FETCH_ASSOC);

        // Fetch scores grouped by subject
        $scoresBySubject = [];
        foreach ($monhocs as $mh) {
            $scoresBySubject[$mh['id']] = $this->model->getDiemsByMonHoc($mh['id'])->fetchAll(PDO::FETCH_ASSOC);
        }

        $view = "Diem/index.php";
        require_once "core/layout.php";
    }

    public function form() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
            $this->model->sinh_vien_id = $_POST['sv'];
            $this->model->mon_hoc_id = $_POST['mh'];
            $this->model->diem_tp = floatval($_POST['tp']);
            $this->model->diem_gk = floatval($_POST['gk']);
            $this->model->diem_ck = floatval($_POST['ck']);

            try {
                if ($this->model->create()) {
                    header("Location: index.php?f=Diem&action=index&msg=success");
                    exit;
                }
            } catch(PDOException $e) {
                $error = "Sinh viên đã có điểm môn học này hoặc lỗi lưu trử!";
            }
        }

        $sinhviens = $this->model->getAllSinhViens()->fetchAll(PDO::FETCH_ASSOC);
        $monhocs = $this->model->getAllMonHocs()->fetchAll(PDO::FETCH_ASSOC);

        $view = "Diem/form.php";
        require_once "core/layout.php";
    }

    public function delete() {
    $this->model->id = isset($_GET['id']) ? $_GET['id'] : null;
    if ($this->model->id && $this->model->delete()) {
        header("Location: index.php?f=Diem&action=index&msg=deleted");
        exit;
    }
    die("Xóa dữ liệu thất bại!");
}
}
?>
