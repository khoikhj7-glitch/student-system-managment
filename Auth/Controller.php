<?php
require_once "config/database.php";

class Controller {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index() {
        // Fetch users to display in the fast login screen
        $query = "SELECT * FROM nguoi_dungs";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Render explicit login view without the main layout
        require_once "Auth/index.php";
    }

    public function login() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $query = "SELECT * FROM nguoi_dungs WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['vai_tro'] = $user['vai_tro'];
                header("Location: index.php?f=Dashboard");
                exit;
            }
        }
        header("Location: index.php?f=Auth");
        exit;
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?f=Auth");
        exit;
    }
}
?>
