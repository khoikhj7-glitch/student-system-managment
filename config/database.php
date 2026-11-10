<?php

class Database {
    private $host = "localhost";
    private $db_name = "sms_db";
    private $username = "root";
    private $password = "";
    public $conn;

    // Thiết lập kết nối
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Lỗi kết nối cơ sở dữ liệu: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
