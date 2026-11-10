<?php
// login.php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function basePath(): string {
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir    = dirname($script);
    return rtrim($dir === '/' || $dir === '\\' ? '' : $dir, '/\\');
}

if (isset($_SESSION['user_id'])) {
    header('Location: ' . basePath() . '/detai.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM nguoi_dungs WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && $user['password'] === $password) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['vai_tro']  = $user['vai_tro'];
            header('Location: ' . basePath() . '/detai.php');
            exit;
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
        }
    } else {
        $error = 'Vui lòng điền đầy đủ thông tin.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #0d0d14;
            --paper: #f4f2ed;
            --white: #ffffff;
            --gold: #c8a96e;
            --border: #d8d3c8;
            --muted: #7c7a72;
            --shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--paper);
            color: var(--ink);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: var(--white);
            width: 100%;
            max-width: 400px;
            padding: 50px 40px;
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            font-family: 'Syne', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 8px;
            text-align: center;
            letter-spacing: -0.5px;
        }

        p.subtitle {
            color: var(--muted);
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 32px;
        }

        .error-msg {
            background: #fff5f5;
            color: #c53030;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            border: 1px solid #feb2b2;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 10px;
            border: 1.5px solid var(--border);
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.2s;
            background: var(--paper);
        }

        input:focus {
            outline: none;
            border-color: var(--ink);
            background: var(--white);
        }

        .options {
            margin-bottom: 30px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            color: var(--ink-soft);
            font-size: 0.9rem;
        }

        .remember input {
            width: 18px; height: 18px;
            accent-color: var(--ink);
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: var(--ink);
            color: var(--gold);
            border: none;
            border-radius: 12px;
            font-family: 'Syne', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: #1a1a25;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h1>Đăng nhập</h1>
        <p class="subtitle">Hệ thống quản lý sinh viên</p>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="username" placeholder="Username" 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="options">
                <label class="remember">
                    <input type="checkbox" name="remember"> Duy trì đăng nhập
                </label>
            </div>

            <button type="submit" class="btn-login">VÀO HỆ THỐNG</button>
        </form>
    </div>

</body>
</html>