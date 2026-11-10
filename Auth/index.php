<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SML - Quick Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body {
            background-color: var(--bg);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-container {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            width: 100%; max-width: 800px;
            padding: 40px;
            text-align: center;
        }
        .login-header h1 { color: var(--primary); font-size: 2rem; font-weight: 800; margin-bottom: 10px; }
        .login-header p { color: var(--text-muted); margin-bottom: 30px; }
        
        .role-section {
            margin-bottom: 30px;
            text-align: left;
        }
        .role-section h3 {
            font-size: 1.1rem;
            color: var(--text-main);
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }
        .user-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            color: var(--text-main);
            display: flex; flex-direction: column; align-items: center;
        }
        .user-card:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1);
            transform: translateY(-2px);
        }
        .user-avatar {
            width: 50px; height: 50px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; font-weight: 700;
            margin-bottom: 10px;
        }
        .gv-avatar { background: #059669; }
        .user-name { font-weight: 600; font-size: 0.95rem; }
        .user-role { font-size: 0.8rem; color: var(--text-muted); margin-top: 5px; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <h1>SML Quick Login</h1>
        <p>Chọn tài khoản để đăng nhập nhanh (Dành cho nhà phát triển)</p>
    </div>

    <!-- Giảng viên -->
    <div class="role-section">
        <h3>👨‍🏫 Giảng Viên (Full Access)</h3>
        <div class="user-grid">
            <?php foreach ($users as $u): if ($u['vai_tro'] == 'Giang_vien'): ?>
                <a href="index.php?f=Auth&action=login&id=<?= $u['id'] ?>" class="user-card">
                    <div class="user-avatar gv-avatar"><?= strtoupper(substr($u['username'], 0, 1)) ?></div>
                    <div class="user-name"><?= htmlspecialchars($u['username']) ?></div>
                    <div class="user-role">Giảng viên</div>
                </a>
            <?php endif; endforeach; ?>
        </div>
    </div>

    <!-- Sinh viên -->
    <div class="role-section">
        <h3>🎓 Sinh Viên (Limited Access)</h3>
        <div class="user-grid">
            <?php foreach ($users as $u): if ($u['vai_tro'] == 'Sinh_vien'): ?>
                <a href="index.php?f=Auth&action=login&id=<?= $u['id'] ?>" class="user-card">
                    <div class="user-avatar"><?= strtoupper(substr($u['username'], 0, 1)) ?></div>
                    <div class="user-name"><?= htmlspecialchars($u['username']) ?></div>
                    <div class="user-role">Sinh viên</div>
                </a>
            <?php endif; endforeach; ?>
        </div>
    </div>
</div>

</body>
</html>
