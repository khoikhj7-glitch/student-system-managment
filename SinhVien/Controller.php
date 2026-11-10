<?php
require_once "config/database.php";
require_once "SinhVien/SinhVien.php";

class Controller {
    private $db;
    private $model;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new SinhVien($this->db);
    }

    public function index() {
        // Collect filters from $_GET
        $filters = [
            'ma_sv' => $_GET['ma_sv'] ?? '',
            'ho_ten' => $_GET['ho_ten'] ?? '',
            'khoa_id' => $_GET['khoa_id'] ?? '',
            'lop_id' => $_GET['lop_id'] ?? '',
            'ngay_sinh' => $_GET['ngay_sinh'] ?? ''
        ];

        // Fetch data
        $list_khoas = $this->model->getAllKhoas();
        $list_lops = $this->model->getAllLops();
        $rows = $this->model->getDanhSach($filters)->fetchAll(PDO::FETCH_ASSOC);

        // Process Save Form
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_student'])) {
            $this->model->id = $_POST['id'] ?? null;
            $this->model->ma_sv = $_POST['ma_sv'];
            $this->model->ho_ten = $_POST['ho_ten'];
            $this->model->ngay_sinh = $_POST['ngay_sinh'];
            $this->model->gioi_tinh = $_POST['gioi_tinh'];
            $this->model->sv_nuoc_ngoai = isset($_POST['sv_nuoc_ngoai']) ? 1 : 0;
            $this->model->lop_id = $_POST['lop_id'];

            if ($this->model->save()) {
                header("Location: index.php?f=SinhVien&action=index&msg=saved");
                exit;
            } else {
                $error = "Đã xảy ra lỗi!";
            }
        }

        // View context
        $view = "SinhVien/index.php";
        require_once "core/layout.php";
    }

    public function delete() {
        $this->model->id = $_GET['id'] ?? null;
        if ($this->model->id) {
            $this->model->delete();
        }
        header("Location: index.php?f=SinhVien&action=index&msg=deleted");
        exit;
    }

    public function import() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_csv'])) {
            $file = $_FILES['file_csv']['tmp_name'];
            if ($_FILES['file_csv']['size'] > 0) {
                $this->model->importCSV($file);
                header("Location: index.php?f=SinhVien&action=index&msg=imported");
                exit;
            }
        }
        die("Import failed!");
    }

    public function export() {
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=DanhSachSinhVien.xls");
        
        $ten = $_GET['ten'] ?? '';
        $sql = "SELECT * FROM sinh_viens WHERE ho_ten LIKE ?";
        $stmt = $this->db->prepare($sql); 
        $stmt->execute(["%$ten%"]);

        echo "<table border='1'>
                <tr>
                    <th style='background:#0d6efd; color:white;'>Mã SV</th>
                    <th style='background:#0d6efd; color:white;'>Họ Tên</th>
                    <th style='background:#0d6efd; color:white;'>Ngày Sinh</th>
                    <th style='background:#0d6efd; color:white;'>Giới tính</th>
                </tr>";
        
        while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>{$r['ma_sv']}</td>
                    <td>{$r['ho_ten']}</td>
                    <td>{$r['ngay_sinh']}</td>
                    <td>{$r['gioi_tinh']}</td>
                  </tr>";
        }
        echo "</table>";
        exit;
    }
}
?>
