<?php

class BaiViet {
    private $conn;
    private $table_name = "bai_viets";

    // DB Columns
    public $id;
    public $tac_gia_id;
    public $chuyen_muc;
    public $tieu_de;
    public $tom_tat;
    public $thumbnail;
    public $noi_dung;
    public $is_hot;
    public $trang_thai;
    public $luot_xem;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả bài viết kèm tên tác giả (sử dụng JOIN)
    public function readAll() {
        $query = "SELECT b.*, n.username AS tac_gia_ten 
                  FROM " . $this->table_name . " b
                  LEFT JOIN nguoi_dungs n ON b.tac_gia_id = n.id
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Đọc 1 dòng
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->tac_gia_id = $row['tac_gia_id'];
            $this->chuyen_muc = $row['chuyen_muc'];
            $this->tieu_de = $row['tieu_de'];
            $this->tom_tat = $row['tom_tat'];
            $this->thumbnail = $row['thumbnail'];
            $this->noi_dung = $row['noi_dung'];
            $this->is_hot = $row['is_hot'];
            $this->trang_thai = $row['trang_thai'];
            $this->luot_xem = $row['luot_xem'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // Thêm bản tin
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET tac_gia_id=:tac_gia_id, chuyen_muc=:chuyen_muc, tieu_de=:tieu_de, 
                      tom_tat=:tom_tat, thumbnail=:thumbnail, noi_dung=:noi_dung, 
                      is_hot=:is_hot, trang_thai=:trang_thai";
        
        $stmt = $this->conn->prepare($query);

        // Bind
        $stmt->bindParam(":tac_gia_id", $this->tac_gia_id);
        $stmt->bindParam(":chuyen_muc", $this->chuyen_muc);
        $stmt->bindParam(":tieu_de", $this->tieu_de);
        $stmt->bindParam(":tom_tat", $this->tom_tat);
        $stmt->bindParam(":thumbnail", $this->thumbnail);
        $stmt->bindParam(":noi_dung", $this->noi_dung);
        $stmt->bindParam(":is_hot", $this->is_hot);
        $stmt->bindParam(":trang_thai", $this->trang_thai);

        return $stmt->execute();
    }

    // Cập nhật
    public function update() {
        // Cú pháp động xử lý nếu thumbnail không dc upload lên mới thì không cập nhật cột này
        $thumb_query = "";
        if ($this->thumbnail != null) {
            $thumb_query = "thumbnail=:thumbnail, ";
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET tac_gia_id=:tac_gia_id, chuyen_muc=:chuyen_muc, tieu_de=:tieu_de, 
                      tom_tat=:tom_tat, " . $thumb_query . " noi_dung=:noi_dung, 
                      is_hot=:is_hot, trang_thai=:trang_thai 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        // Bind
        $stmt->bindParam(":tac_gia_id", $this->tac_gia_id);
        $stmt->bindParam(":chuyen_muc", $this->chuyen_muc);
        $stmt->bindParam(":tieu_de", $this->tieu_de);
        $stmt->bindParam(":tom_tat", $this->tom_tat);
        if ($this->thumbnail != null) {
            $stmt->bindParam(":thumbnail", $this->thumbnail);
        }
        $stmt->bindParam(":noi_dung", $this->noi_dung);
        $stmt->bindParam(":is_hot", $this->is_hot);
        $stmt->bindParam(":trang_thai", $this->trang_thai);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Xóa
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        // Có thể lấy thumbnail URL để unlink file ổ cứng thủ công nếu cần kỹ càng hơn
        return $stmt->execute();
    }
}
?>
