<?php
require_once "config/database.php";
require_once "MonHoc/MonHoc.php";

class Controller {
    private $db;
    private $model;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new MonHoc($this->db);
    }

    public function index() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['add'])) {
                $this->model->ma_mh = $_POST['ma_mh'];
                $this->model->ten_mh = $_POST['ten_mh'];
                try {
                    if ($this->model->create()) {
                        header("Location: index.php?f=MonHoc&action=index&msg=added");
                        exit;
                    }
                } catch(PDOException $e) {
                    $error = "Mã môn học có thể đã tồn tại!";
                }
            } elseif (isset($_POST['update'])) {
                $this->model->id = $_POST['id'];
                $this->model->ma_mh = $_POST['ma_mh'];
                $this->model->ten_mh = $_POST['ten_mh'];
                try {
                    if ($this->model->update()) {
                        header("Location: index.php?f=MonHoc&action=index&msg=updated");
                        exit;
                    }
                } catch(PDOException $e) {
                    $error = "Lỗi cập nhật hoặc mã môn học bị trùng!";
                }
            }
        }

        $stmt = $this->model->readAll();
        $monhocs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $view = "MonHoc/index.php";
        require_once "core/layout.php";
    }

    public function delete() {
        $this->model->id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($this->model->id && $this->model->delete()) {
            header("Location: index.php?f=MonHoc&action=index&msg=deleted");
            exit;
        }
        die("Xóa thất bại!");
    }
}
?>
