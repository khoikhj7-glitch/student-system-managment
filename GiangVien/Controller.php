<?php
require_once "config/database.php";
require_once "GiangVien/GiangVien.php";

class Controller {
    private $db;
    private $model;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new GiangVien($this->db);
    }

    public function index() {
        // Collect search filter
        $search_name = $_GET['search_name'] ?? '';

        $list_khoas = $this->model->getAllKhoas();
        $rows = $this->model->getDanhSach($search_name)->fetchAll(PDO::FETCH_ASSOC);

        // Process save
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_teacher'])) {
            $this->model->id = $_POST['id'] ?? null;
            $this->model->ma_gv = $_POST['ma_gv'];
            $this->model->ho_ten = $_POST['ho_ten'];
            $this->model->trinh_do = $_POST['trinh_do'];
            $this->model->khoa_id = $_POST['khoa_id'];

            if ($this->model->save()) {
                header("Location: index.php?f=GiangVien&action=index&msg=saved");
                exit;
            } else {
                $error = "Lỗi kỹ thuật khi cập nhật giảng viên!";
            }
        }

        $view = "GiangVien/index.php";
        require_once "core/layout.php";
    }

    public function delete() {
        $this->model->id = $_GET['id'] ?? null;
        if ($this->model->id) {
            $this->model->delete();
        }
        header("Location: index.php?f=GiangVien&action=index&msg=deleted");
        exit;
    }
}
?>
