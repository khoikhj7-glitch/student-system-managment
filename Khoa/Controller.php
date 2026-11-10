<?php
require_once "config/database.php";
require_once "Khoa/Khoa.php";

class Controller {
    private $db;
    private $model;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new Khoa($this->db);
    }

    public function index() {
        $rows = $this->model->getAll()->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_khoa'])) {
            $this->model->id = $_POST['id'] ?? null;
            $this->model->ma_khoa = $_POST['ma_khoa'];
            $this->model->ten_khoa = $_POST['ten_khoa'];
            
            if ($this->model->save()) {
                header("Location: index.php?f=Khoa&action=index&msg=saved");
                exit;
            }
        }

        $view = "Khoa/index.php";
        require_once "core/layout.php";
    }

    public function delete() {
        $this->model->id = $_GET['id'] ?? null;
        if ($this->model->id) {
            $check = $this->model->checkDeleteConstraints();
            if ($check === true) {
                $this->model->delete();
                header("Location: index.php?f=Khoa&action=index&msg=deleted");
                exit;
            } else {
                header("Location: index.php?f=Khoa&action=index&error=" . urlencode($check));
                exit;
            }
        }
    }

    public function apiList() {
        // Micro API trả HTML Table dạng text cho Fetch JS
        $this->model->id = $_GET['id'] ?? 0;
        $data = $this->model->getGiangViens();

        if (count($data) > 0) {
            echo '<div class="table-responsive">
                    <table class="table table-hover align-middle shadow-sm border-0 mb-0 text-center">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th class="py-3 px-4">Mã GV</th>
                                <th class="py-3">Họ và Tên</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data as $r) {
                echo "<tr>
                        <td class='px-4 fw-bold text-primary'>" . htmlspecialchars($r['ma_gv']) . "</td>
                        <td class='text-dark left'>" . htmlspecialchars($r['ho_ten']) . "</td>
                      </tr>";
            }
            echo '</tbody></table></div>';
            echo '<div class="mt-3 text-end"><small class="text-muted pe-3">Tổng cộng: ' . count($data) . '</small></div>';
        } else {
            echo '<div class="text-center py-5">
                    <h6 class="text-secondary mt-2">Khoa này hiện chưa có Giảng viên trực thuộc</h6>
                  </div>';
        }
        exit; // End request
    }
}
?>
