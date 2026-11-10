<?php
class MonHoc {
    private $conn;
    private $table_name = "mon_hocs";

    public $id;
    public $ma_mh;
    public $ten_mh;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (ma_mh, ten_mh) VALUES (:ma_mh, :ten_mh)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":ma_mh", $this->ma_mh);
        $stmt->bindParam(":ten_mh", $this->ten_mh);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET ma_mh = :ma_mh, ten_mh = :ten_mh WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":ma_mh", $this->ma_mh);
        $stmt->bindParam(":ten_mh", $this->ten_mh);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
}
?>
