<?php
require_once "config/database.php";
require_once "Lop/Lop.php";

class Controller {
    private $db;
    private $model;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new Lop($this->db);
    }

    public function index() {
        $rows = $this->model->getAll()->fetchAll(PDO::FETCH_ASSOC);
        $khoas = $this->model->getKhoas();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_lop'])) {
            $this->model->id = !empty($_POST['id']) ? $_POST['id'] : null;
            $this->model->ma_lop = trim($_POST['ma_lop']);
            $this->model->ten_lop = trim($_POST['ten_lop']);
            $this->model->khoa_id = $_POST['khoa_id'];
            
            if ($this->model->save()) {
                header("Location: index.php?f=Lop&action=index&msg=saved");
                exit;
            }
        }

        $view = "Lop/index.php";
        require_once "core/layout.php";
    }

    public function delete() {
        $this->model->id = $_GET['id'] ?? null;
        if ($this->model->id) {
            $check = $this->model->checkDeleteConstraints();
            if ($check === true) {
                if ($this->model->delete()) {
                    header("Location: index.php?f=Lop&action=index&msg=deleted");
                    exit;
                }
            } else {
                header("Location: index.php?f=Lop&action=index&error=" . urlencode($check));
                exit;
            }
        }
    }
    public function apiList() {
        $this->model->id = $_GET['id'] ?? 0;
        $data = $this->model->getSinhViens();

        if (count($data) > 0) {
            echo '<div class="table-responsive">
                    <table class="table table-hover align-middle shadow-sm border-0 mb-0 text-center">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th class="py-3 px-4">Mã SV</th>
                                <th class="py-3 text-start">Họ và Tên</th>
                                <th class="py-3">Ngày sinh</th>
                                <th class="py-3">Giới tính</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data as $r) {
                $ngay_sinh = !empty($r['ngay_sinh']) ? date("d/m/Y", strtotime($r['ngay_sinh'])) : "---";
                
                $gt_class = ($r['gioi_tinh'] == 'Nam') ? 'text-primary' : 'text-danger';
                $gt_bg = ($r['gioi_tinh'] == 'Nam') ? 'bg-primary' : 'bg-danger';

                echo "<tr>
                        <td class='px-4 fw-bold text-success'>" . htmlspecialchars($r['ma_sv']) . "</td>
                        <td class='text-dark text-start fw-semibold'>" . htmlspecialchars($r['ho_ten']) . "</td>
                        <td class='text-muted'>" . $ngay_sinh . "</td>
                        <td>
                            <span class='badge " . $gt_bg . " bg-opacity-10 " . $gt_class . " px-3 py-2' style='min-width: 60px;'>
                                " . htmlspecialchars($r['gioi_tinh']) . "
                            </span>
                        </td>
                      </tr>";
            }
            echo '</tbody></table></div>';
            echo '<div class="mt-3 text-end"><small class="text-muted pe-3">Tổng cộng: ' . count($data) . ' sinh viên</small></div>';
        } else {
            echo '<div class="text-center py-5">
                    <h6 class="text-secondary mt-2">Lớp này hiện chưa có sinh viên nào</h6>
                  </div>';
        }
        exit;
    }
}
?>