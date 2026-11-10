<?php
/**
 * BaiViet/Controller.php
 * Chỉ cần load view — mọi dữ liệu đều qua API bằng JS
 */
class Controller {
    public function index() {
        $view = 'BaiViet/index.php';
        require_once 'core/layout.php';
    }
}
