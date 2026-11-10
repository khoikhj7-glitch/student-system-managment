<?php
// detai.php — Trang chính Quản lý Đề tài
require_once __DIR__ . '/auth.php';
requireLogin();

$user      = currentUser();
$isGV      = isGiangVien();
$roleLabel = $isGV ? 'Giảng viên' : 'Sinh viên';
$roleIcon  = $isGV ? '👨‍🏫' : '🎓';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý Đề tài — SMS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,400&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --ink:      #0d0d14;
  --ink-soft: #3a3a4a;
  --paper:    #f4f2ed;
  --cream:    #ede9df;
  --white:    #ffffff;
  --gold:     #c8a96e;
  --teal:     #2e6b5c;
  --teal-l:   #d4ede7;
  --red:      #c0392b;
  --red-l:    #fde8e6;
  --blue:     #2563eb;
  --blue-l:   #dbeafe;
  --amber:    #d97706;
  --amber-l:  #fef3c7;
  --muted:    #7c7a72;
  --border:   #d8d3c8;
  --shadow:   0 2px 16px rgba(0,0,0,.08);
  --radius:   10px;
}

html, body { height: 100%; }
body {
  font-family: 'DM Sans', sans-serif;
  background: var(--paper);
  color: var(--ink);
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  font-size: 15px;
}

/* ── TOPBAR ── */
.topbar {
  position: sticky; top: 0; z-index: 100;
  background: var(--ink);
  height: 56px;
  display: flex;
  align-items: center;
  padding: 0 24px;
  gap: 16px;
  box-shadow: 0 2px 12px rgba(0,0,0,.3);
}
.topbar-brand {
  font-family: 'Syne', sans-serif;
  font-weight: 800; font-size: 1.15rem;
  color: var(--gold); letter-spacing: .04em;
  text-decoration: none;
}
.topbar-title {
  flex: 1; color: rgba(255,255,255,.5);
  font-size: .85rem; letter-spacing: .04em;
  text-transform: uppercase;
  padding-left: 16px;
  border-left: 1px solid rgba(255,255,255,.15);
}
.topbar-user {
  display: flex; align-items: center;
  gap: 8px; color: rgba(255,255,255,.85);
  font-size: .875rem;
}
.badge-role {
  background: rgba(255,255,255,.1);
  border: 1px solid rgba(255,255,255,.2);
  border-radius: 20px; padding: 3px 10px;
  font-size: .75rem; color: var(--gold); font-weight: 500;
}
.btn-logout {
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.15);
  color: rgba(255,255,255,.7);
  padding: 6px 14px; border-radius: 6px;
  font-size: .8rem; cursor: pointer;
  text-decoration: none; transition: background .2s;
}
.btn-logout:hover { background: rgba(255,255,255,.2); color: #fff; }

/* ── LAYOUT ── */
.layout { flex: 1; display: flex; }

/* ── SIDEBAR ── */
.sidebar {
  width: 240px; min-width: 240px;
  background: var(--white);
  border-right: 1.5px solid var(--border);
  padding: 24px 0;
  display: flex; flex-direction: column; gap: 4px;
}
.sidebar-section {
  padding: 0 16px 8px;
  font-size: .7rem; font-weight: 600;
  letter-spacing: .1em; text-transform: uppercase;
  color: var(--muted); margin-top: 8px;
}
.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 20px; color: var(--ink-soft);
  cursor: pointer; border-left: 3px solid transparent;
  transition: all .15s; font-size: .9rem; user-select: none;
}
.nav-item:hover { background: var(--paper); color: var(--ink); }
.nav-item.active {
  background: var(--teal-l); color: var(--teal);
  border-left-color: var(--teal); font-weight: 500;
}
.nav-icon { font-size: 1rem; width: 20px; text-align: center; }

/* ── MAIN ── */
.main { flex: 1; padding: 32px; overflow-y: auto; }
.page { display: none; }
.page.active { display: block; animation: fadeIn .25s ease; }
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ── PAGE HEADER ── */
.page-header {
  display: flex; align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 28px; flex-wrap: wrap; gap: 16px;
}
.page-header h1 {
  font-family: 'Syne', sans-serif;
  font-size: 1.6rem; font-weight: 700;
  color: var(--ink); line-height: 1.2;
}
.page-header p { color: var(--muted); font-size: .875rem; margin-top: 4px; }

/* ── BUTTONS ── */
.btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 18px; border-radius: 8px;
  font-family: 'DM Sans', sans-serif;
  font-size: .875rem; font-weight: 500;
  cursor: pointer; border: none; transition: all .15s;
  text-decoration: none;
}
.btn-primary { background: var(--ink); color: var(--gold); }
.btn-primary:hover { background: #1a1a2e; transform: translateY(-1px); }
.btn-teal { background: var(--teal); color: #fff; }
.btn-teal:hover { background: #245748; transform: translateY(-1px); }
.btn-outline {
  background: transparent;
  border: 1.5px solid var(--border); color: var(--ink-soft);
}
.btn-outline:hover { border-color: var(--ink); color: var(--ink); }
.btn-danger { background: var(--red-l); color: var(--red); border: 1px solid #f0b0aa; }
.btn-danger:hover { background: #f8d7d5; }
.btn-sm { padding: 5px 12px; font-size: .8rem; }
.btn-xs { padding: 3px 9px; font-size: .75rem; }

/* ── CARDS ── */
.card {
  background: var(--white);
  border: 1.5px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  overflow: hidden;
}
.card-header {
  padding: 16px 20px;
  border-bottom: 1.5px solid var(--border);
  display: flex; align-items: center;
  justify-content: space-between; gap: 12px;
}
.card-header h3 {
  font-family: 'Syne', sans-serif;
  font-weight: 700; font-size: 1rem;
}
.card-body { padding: 20px; }

/* ── THESIS GRID ── */
.thesis-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 20px;
}
.thesis-card {
  background: var(--white);
  border: 1.5px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  display: flex; flex-direction: column;
  transition: transform .2s, box-shadow .2s;
}
.thesis-card:hover { transform: translateY(-3px); box-shadow: 0 8px 32px rgba(0,0,0,.12); }
.tc-head {
  padding: 18px 20px 14px;
  border-bottom: 1.5px solid var(--border); flex: 1;
}
.tc-head h4 {
  font-family: 'Syne', sans-serif;
  font-size: .95rem; font-weight: 700;
  color: var(--ink); margin-bottom: 6px; line-height: 1.4;
}
.tc-head p {
  font-size: .83rem; color: var(--muted); line-height: 1.5;
  display: -webkit-box; -webkit-line-clamp: 2;
  -webkit-box-orient: vertical; overflow: hidden;
}
.tc-meta {
  padding: 12px 20px;
  display: flex; align-items: center;
  justify-content: space-between; flex-wrap: wrap; gap: 8px;
  background: var(--paper);
  border-bottom: 1px solid var(--border);
}
.tc-info {
  font-size: .8rem; color: var(--muted);
  display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
}
.tc-info span { display: flex; align-items: center; gap: 4px; }
.tc-actions {
  padding: 12px 20px;
  display: flex; gap: 8px; flex-wrap: wrap; align-items: center;
}

/* ── BADGES ── */
.badge {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 3px 10px; border-radius: 20px;
  font-size: .75rem; font-weight: 600;
}
.badge-open     { background: var(--teal-l); color: var(--teal); }
.badge-closed   { background: var(--amber-l); color: var(--amber); }
.badge-done     { background: #e8f4fd; color: #1565c0; }
.badge-waiting  { background: var(--amber-l); color: var(--amber); }
.badge-wip      { background: var(--blue-l); color: var(--blue); }
.badge-submitted{ background: var(--teal-l); color: var(--teal); }
.badge-graded   { background: #f3e8ff; color: #6d28d9; }

/* ── PROGRESS ── */
.progress-wrap {
  background: var(--cream); border-radius: 20px;
  height: 8px; overflow: hidden; flex: 1;
}
.progress-bar {
  height: 100%; border-radius: 20px;
  background: linear-gradient(90deg, var(--teal), #4caf90);
  transition: width .4s ease;
}
.progress-label {
  font-size: .8rem; font-weight: 600;
  color: var(--teal); min-width: 36px; text-align: right;
}

/* ── TABLE ── */
.table-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
thead th {
  background: var(--paper); padding: 10px 14px;
  text-align: left; font-size: .78rem; font-weight: 600;
  letter-spacing: .05em; text-transform: uppercase;
  color: var(--muted); border-bottom: 1.5px solid var(--border);
  white-space: nowrap;
}
tbody td {
  padding: 12px 14px; border-bottom: 1px solid var(--border);
  font-size: .875rem; vertical-align: middle;
}
tbody tr:last-child td { border-bottom: none; }
tbody tr:hover { background: var(--paper); }

/* ── MODAL ── */
.modal-overlay {
  position: fixed; inset: 0; z-index: 500;
  background: rgba(0,0,0,.45); backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center;
  padding: 24px; opacity: 0; pointer-events: none; transition: opacity .2s;
}
.modal-overlay.open { opacity: 1; pointer-events: all; }
.modal {
  background: var(--white); border-radius: 14px;
  width: 100%; max-width: 560px;
  box-shadow: 0 24px 80px rgba(0,0,0,.2);
  transform: scale(.95) translateY(16px);
  transition: transform .2s;
  max-height: 90vh; overflow-y: auto;
}
.modal-overlay.open .modal { transform: scale(1) translateY(0); }
.modal-head {
  padding: 22px 24px 18px;
  border-bottom: 1.5px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
}
.modal-head h2 { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 700; }
.modal-close {
  background: none; border: none; font-size: 1.3rem;
  cursor: pointer; color: var(--muted); padding: 4px 8px;
  border-radius: 6px; transition: background .15s;
}
.modal-close:hover { background: var(--paper); color: var(--ink); }
.modal-body { padding: 24px; display: flex; flex-direction: column; gap: 18px; }
.modal-foot {
  padding: 16px 24px; border-top: 1.5px solid var(--border);
  display: flex; gap: 10px; justify-content: flex-end;
}

/* ── FORM ── */
.form-group { display: flex; flex-direction: column; gap: 6px; }
label {
  font-size: .78rem; font-weight: 600;
  letter-spacing: .05em; text-transform: uppercase; color: var(--muted);
}
input[type=text], input[type=number], select, textarea {
  width: 100%; padding: 10px 13px;
  border: 1.5px solid var(--border); border-radius: 8px;
  font-family: 'DM Sans', sans-serif; font-size: .9rem;
  color: var(--ink); background: var(--paper);
  outline: none; transition: border-color .2s, box-shadow .2s;
}
input:focus, select:focus, textarea:focus {
  border-color: var(--teal);
  box-shadow: 0 0 0 3px rgba(46,107,92,.12); background: #fff;
}
textarea { resize: vertical; min-height: 80px; }
input[type=range] {
  width: 100%; height: 6px; padding: 0;
  accent-color: var(--teal); border: none; background: none;
}

/* ── TOAST ── */
#toast {
  position: fixed; bottom: 24px; right: 24px;
  z-index: 9999; display: flex; flex-direction: column; gap: 8px;
}
.toast-item {
  background: var(--ink); color: #fff;
  padding: 12px 18px; border-radius: 8px;
  font-size: .875rem; box-shadow: 0 4px 20px rgba(0,0,0,.2);
  display: flex; align-items: center; gap: 10px;
  animation: slideIn .3s ease; max-width: 340px;
}
.toast-item.success { border-left: 4px solid #4caf50; }
.toast-item.error   { border-left: 4px solid var(--red); }
@keyframes slideIn {
  from { opacity: 0; transform: translateX(40px); }
  to   { opacity: 1; transform: translateX(0); }
}

/* ── DETAIL PANEL ── */
.detail-panel {
  position: fixed; top: 56px; right: 0; bottom: 0;
  width: 520px; background: var(--white);
  border-left: 1.5px solid var(--border);
  box-shadow: -8px 0 40px rgba(0,0,0,.1);
  z-index: 200; display: flex; flex-direction: column;
  transform: translateX(100%);
  transition: transform .3s cubic-bezier(.4,0,.2,1);
  overflow-y: auto;
}
.detail-panel.open { transform: translateX(0); }
.detail-panel-head {
  padding: 20px 24px; border-bottom: 1.5px solid var(--border);
  display: flex; align-items: center; gap: 12px;
  background: var(--paper); position: sticky; top: 0;
}
.detail-panel-head h3 {
  flex: 1; font-family: 'Syne', sans-serif;
  font-size: 1rem; font-weight: 700;
}
.detail-panel-body { padding: 24px; flex: 1; }

.info-grid {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 16px; margin-bottom: 20px;
}
.info-item label { margin-bottom: 2px; }
.info-item p { font-size: .9rem; color: var(--ink); }

.section-title {
  font-family: 'Syne', sans-serif;
  font-size: .9rem; font-weight: 700; color: var(--ink);
  padding-bottom: 8px;
  border-bottom: 1.5px solid var(--border);
  margin-bottom: 14px;
}

/* ── EMPTY STATE ── */
.empty-state {
  text-align: center; padding: 64px 24px; color: var(--muted);
}
.empty-state .icon { font-size: 3rem; margin-bottom: 12px; }
.empty-state h3 {
  font-family: 'Syne', sans-serif; font-size: 1.1rem;
  color: var(--ink-soft); margin-bottom: 6px;
}
.empty-state p { font-size: .875rem; }

/* ── SPINNER ── */
.spinner {
  width: 28px; height: 28px;
  border: 3px solid var(--border); border-top-color: var(--teal);
  border-radius: 50%;
  animation: spin .8s linear infinite; margin: 40px auto;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── SCORE ── */
.score-big { display: inline-flex; align-items: baseline; gap: 3px; }
.score-big .num {
  font-family: 'Syne', sans-serif; font-size: 2rem;
  font-weight: 800; color: var(--teal);
}
.score-big .denom { color: var(--muted); font-size: .9rem; }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .main { padding: 16px; }
  .detail-panel { width: 100%; }
  .thesis-grid { grid-template-columns: 1fr; }
  .info-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- TOPBAR -->
<nav class="topbar">
  <a class="topbar-brand" href="detai.php">⬡ SMS</a>
  <span class="topbar-title">Quản lý Đề tài & Khóa Luận</span>
  <div class="topbar-user">
    <span><?= $roleIcon ?> <?= htmlspecialchars($user['username']) ?></span>
    <span class="badge-role"><?= $roleLabel ?></span>
    <a href="<?= basePath() ?>/logout.php" class="btn-logout">Đăng xuất</a>
  </div>
</nav>

<!-- LAYOUT -->
<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <?php if ($isGV): ?>
    <div class="sidebar-section">Quản lý</div>
    <div class="nav-item active" data-page="gv-list" onclick="showPage('gv-list')">
      <span class="nav-icon">📋</span> Đề tài của tôi
    </div>
    <div class="sidebar-section">Sinh viên</div>
    <div class="nav-item" data-page="gv-students" onclick="showPage('gv-students'); loadGvStudents()">
      <span class="nav-icon">👥</span> Chấm điểm & Theo dõi
    </div>
    <?php else: ?>
    <div class="sidebar-section">Đề tài</div>
    <div class="nav-item active" data-page="sv-browse" onclick="showPage('sv-browse')">
      <span class="nav-icon">🔍</span> Danh sách đề tài
    </div>
    <div class="nav-item" data-page="sv-my" onclick="showPage('sv-my'); loadMyRegistration()">
      <span class="nav-icon">📌</span> Đề tài của tôi
    </div>
    <?php endif; ?>
  </aside>

  <!-- MAIN -->
  <main class="main">

  <?php if ($isGV): ?>
  <!-- ═══ GIẢNG VIÊN: Danh sách đề tài + nút thêm ═══ -->
  <div id="page-gv-list" class="page active">
    <div class="page-header">
      <div>
        <h1>📋 Quản lý Đề tài</h1>
        <p>Danh sách đề tài bạn đã tạo</p>
      </div>
      <button class="btn btn-primary" onclick="showPage('gv-create')">➕ Thêm đề tài mới</button>
    </div>
    <div id="gv-list-content"><div class="spinner"></div></div>
  </div>

  <!-- ═══ GIẢNG VIÊN: Form tạo đề tài mới ═══ -->
  <div id="page-gv-create" class="page">
    <div class="page-header">
      <div>
        <h1>➕ Tạo đề tài mới</h1>
        <p>Điền thông tin để tạo đề tài cho sinh viên đăng ký</p>
      </div>
      <button class="btn btn-outline" onclick="showPage('gv-list')">← Quay lại</button>
    </div>
    <div class="card" style="max-width:640px">
      <div class="card-header"><h3>Thông tin đề tài</h3></div>
      <div class="card-body">
        <form id="form-create-dt" style="display:flex;flex-direction:column;gap:18px">
          <div class="form-group">
            <label>Tên đề tài *</label>
            <input type="text" name="ten_de_tai" placeholder="Ví dụ: Hệ thống nhận diện khuôn mặt điểm danh..." required>
          </div>
          <div class="form-group">
            <label>Mô tả</label>
            <textarea name="mo_ta" placeholder="Mô tả chi tiết về đề tài, công nghệ sử dụng, yêu cầu sinh viên..."></textarea>
          </div>
          <div class="form-group">
            <label>Trạng thái</label>
            <select name="trang_thai">
              <option value="Mo_dang_ky">🟢 Mở đăng ký</option>
              <option value="Dong_dang_ky">🟡 Đóng đăng ký</option>
            </select>
          </div>
          <div style="display:flex;gap:10px;margin-top:4px">
            <button type="submit" class="btn btn-primary">✓ Tạo đề tài</button>
            <button type="button" class="btn btn-outline" onclick="showPage('gv-list')">Hủy</button>
          </div>
          <div id="form-create-error" style="display:none;color:var(--red);font-size:.875rem;background:var(--red-l);padding:10px 14px;border-radius:8px"></div>
        </form>
      </div>
    </div>
  </div>

  <!-- ═══ GIẢNG VIÊN: Chấm điểm & Theo dõi SV ═══ -->
  <div id="page-gv-students" class="page">
    <div class="page-header">
      <div>
        <h1>👥 Chấm điểm & Theo dõi</h1>
        <p>Xem tiến độ, nhận xét và chấm điểm cho sinh viên</p>
      </div>
    </div>
    <div id="gv-students-content"><div class="spinner"></div></div>
  </div>

  <?php else: ?>
  <!-- ═══ SINH VIÊN: Danh sách đề tài mở đăng ký ═══ -->
  <div id="page-sv-browse" class="page active">
    <div class="page-header">
      <div>
        <h1>🔍 Đề tài đang mở đăng ký</h1>
        <p>Chọn và đăng ký đề tài phù hợp với bạn</p>
      </div>
    </div>
    <div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap">
      <input type="text" id="filter-search"
             placeholder="🔍 Tìm theo tên đề tài hoặc giảng viên..."
             oninput="filterThesis()"
             style="width:320px;padding:8px 12px">
    </div>
    <div id="sv-browse-content"><div class="spinner"></div></div>
  </div>

  <!-- ═══ SINH VIÊN: Đề tài của tôi ═══ -->
  <div id="page-sv-my" class="page">
    <div class="page-header">
      <div>
        <h1>📌 Đề tài của tôi</h1>
        <p>Theo dõi tiến độ và nộp báo cáo</p>
      </div>
    </div>
    <div id="sv-my-content"><div class="spinner"></div></div>
  </div>
  <?php endif; ?>

  </main>
</div>

<!-- DETAIL PANEL -->
<div class="detail-panel" id="detail-panel">
  <div class="detail-panel-head">
    <h3 id="panel-title">Chi tiết</h3>
    <button class="modal-close" onclick="closePanel()">✕</button>
  </div>
  <div class="detail-panel-body" id="panel-body"></div>
</div>

<!-- MODAL: Chỉnh sửa đề tài -->
<div class="modal-overlay" id="modal-edit-dt">
  <div class="modal">
    <div class="modal-head">
      <h2>✏️ Chỉnh sửa đề tài</h2>
      <button class="modal-close" onclick="closeModal('modal-edit-dt')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="edit-dt-id">
      <div class="form-group">
        <label>Tên đề tài</label>
        <input type="text" id="edit-dt-ten">
      </div>
      <div class="form-group">
        <label>Mô tả</label>
        <textarea id="edit-dt-mota"></textarea>
      </div>
      <div class="form-group">
        <label>Trạng thái</label>
        <select id="edit-dt-tt">
          <option value="Mo_dang_ky">🟢 Mở đăng ký</option>
          <option value="Dong_dang_ky">🟡 Đóng đăng ký</option>
          <option value="Hoan_thanh">✅ Hoàn thành</option>
        </select>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-outline" onclick="closeModal('modal-edit-dt')">Hủy</button>
      <button class="btn btn-primary" onclick="submitEditDeTai()">💾 Lưu thay đổi</button>
    </div>
  </div>
</div>

<!-- MODAL CHỈ NHẬP ĐIỂM (đã bỏ nhận xét theo yêu cầu) -->
<div class="modal-overlay" id="modal-grade">
  <div class="modal" style="max-width:420px;">
    <div class="modal-head">
      <h2>📝 Nhập điểm</h2>
      <button class="modal-close" onclick="closeModal('modal-grade')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="grade-dk-id">
      <div id="grade-sv-info" style="background:var(--paper);border-radius:8px;padding:12px 14px;font-size:.875rem;color:var(--ink-soft)"></div>
      <div class="form-group">
        <label>Điểm số (0 – 10)</label>
        <input type="number" id="grade-diemSo" min="0" max="10" step="0.1" placeholder="Ví dụ: 8.5">
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-outline" onclick="closeModal('modal-grade')">Hủy</button>
      <button class="btn btn-primary" onclick="submitGrade()">💾 Lưu điểm</button>
    </div>
  </div>
</div>

<!-- MODAL: Cập nhật tiến độ (SV) -->
<div class="modal-overlay" id="modal-progress">
  <div class="modal">
    <div class="modal-head">
      <h2>📊 Cập nhật tiến độ</h2>
      <button class="modal-close" onclick="closeModal('modal-progress')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="prog-dk-id">
      <div class="form-group">
        <label>Tiến độ: <strong id="prog-val-label">0%</strong></label>
        <input type="range" id="prog-slider" min="0" max="100" step="5" value="0"
               oninput="document.getElementById('prog-val-label').textContent=this.value+'%'">
      </div>
      <div class="form-group">
        <label>Link / Tên file báo cáo</label>
        <input type="text" id="prog-file"
               placeholder="vd: bao_cao_tuan3.pdf hoặc link Google Drive...">
      </div>
      <div class="form-group">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;text-transform:none">
          <input type="checkbox" id="prog-nop-bao-cao"
                 style="width:auto;border:none;padding:0;accent-color:var(--teal)">
          Đánh dấu là đã nộp báo cáo hoàn thành
        </label>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-outline" onclick="closeModal('modal-progress')">Hủy</button>
      <button class="btn btn-teal" onclick="submitProgress()">📤 Cập nhật</button>
    </div>
  </div>
</div>

<div id="toast"></div>

<script>
const IS_GV = <?= $isGV ? 'true' : 'false' ?>;
let allThesisList = [];

// ── API SỬ DỤNG basePath() ĐỂ KHÔNG BỊ 404 ──
async function api(endpoint, method = 'GET', body = null) {
  const base = '<?= basePath() ?>';
  const url = (base ? base + '/' : '') + endpoint;

  console.log('[API DEBUG] Gọi:', url);

  const opts = { method, headers: { 'Content-Type': 'application/json' } };
  if (body) opts.body = JSON.stringify(body);

  try {
    const res = await fetch(url, opts);
    console.log(`[API DEBUG] Status: ${res.status}`);

    const text = await res.text();
    console.log('[API DEBUG] Response (300 ký tự):', text.substring(0, 300));

    let data;
    try {
      data = JSON.parse(text);
    } catch(e) {
      console.error("Không parse được JSON");
      return { success: false, message: 'Server không trả về JSON.' };
    }
    return data;
  } catch(e) {
    console.error('Fetch error:', e);
    return { success: false, message: 'Lỗi kết nối máy chủ. Xem console F12.' };
  }
}

// ── NAVIGATION ──
function showPage(name) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));

  const page = document.getElementById('page-' + name);
  if (page) page.classList.add('active');

  const nav = document.querySelector(`[data-page="${name}"]`);
  if (nav) nav.classList.add('active');

  closePanel();
  if (name === 'gv-list') loadGvList();
  if (name === 'sv-browse') loadSvBrowse();
}

// ── MODAL ──
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});

// ── PANEL ──
function openPanel(title, html) {
  document.getElementById('panel-title').textContent = title;
  document.getElementById('panel-body').innerHTML = html;
  document.getElementById('detail-panel').classList.add('open');
}
function closePanel() { document.getElementById('detail-panel').classList.remove('open'); }

// ── TOAST ──
function toast(msg, type = 'success') {
  const el = document.createElement('div');
  el.className = `toast-item ${type}`;
  el.innerHTML = (type === 'success' ? '✓ ' : '✕ ') + msg;
  document.getElementById('toast').appendChild(el);
  setTimeout(() => el.remove(), 3800);
}

// ── STATUS LABELS ──
const statusDT = {
  Mo_dang_ky:   '<span class="badge badge-open">🟢 Mở đăng ký</span>',
  Dong_dang_ky: '<span class="badge badge-closed">🟡 Đóng đăng ký</span>',
  Hoan_thanh:   '<span class="badge badge-done">✅ Hoàn thành</span>',
};
const statusDK = {
  Cho_duyet:      '<span class="badge badge-waiting">⏳ Chờ duyệt</span>',
  Dang_thuc_hien: '<span class="badge badge-wip">🔵 Đang thực hiện</span>',
  Da_nop:         '<span class="badge badge-submitted">📤 Đã nộp</span>',
  Da_cham:        '<span class="badge badge-graded">✅ Đã chấm</span>',
};

// ════════════════════════════════
// ════════════════════════════════
//  GIẢNG VIÊN - QUẢN LÝ ĐỀ TÀI & ĐIỂM
// ════════════════════════════════

// 1. Tải danh sách đề tài của giảng viên
// ════════════════════════════════════════════════════════════
//  GIẢNG VIÊN - QUẢN LÝ ĐỀ TÀI & ĐIỂM
// ════════════════════════════════════════════════════════════

// 1. Tải danh sách đề tài của giảng viên
async function loadGvList() {
  const el = document.getElementById('gv-list-content');
  if (!el) return;
  
  el.innerHTML = '<div class="spinner"></div>';
  const r = await api('api/detai.php');
  
  if (!r.success) { 
    el.innerHTML = `<p style="color:var(--red);padding:16px">${r.message}</p>`; 
    return; 
  }

  const list = r.data;
  if (!list || !list.length) {
    el.innerHTML = `<div class="empty-state">
      <div class="icon">📭</div>
      <h3>Chưa có đề tài nào</h3>
      <p>Hãy tạo đề tài đầu tiên của bạn!</p>
      <button class="btn btn-primary" style="margin-top:16px" onclick="showPage('gv-create')">➕ Thêm đề tài mới</button>
    </div>`;
    return;
  }

  el.innerHTML = `<div class="thesis-grid">${list.map(dt => `
    <div class="thesis-card">
      <div class="tc-head">
        <h4>${esc(dt.ten_de_tai)}</h4>
        <p>${esc(dt.mo_ta || 'Chưa có mô tả.')}</p>
      </div>
      <div class="tc-meta">
        <div class="tc-info">
          <span>👥 ${dt.so_dang_ky} SV đăng ký</span>
          <span>📅 ${fmtDate(dt.created_at)}</span>
        </div>
        ${statusDT[dt.trang_thai] || ''}
      </div>
      <div class="tc-actions">
        <button class="btn btn-outline btn-sm" onclick="openDetailGv(${dt.id}, '${esc(dt.ten_de_tai)}')">👁 Xem</button>
        <button class="btn btn-teal btn-sm" onclick="showPage('gv-students'); loadGvStudents()">👨‍🎓 Sinh viên</button>
        <button class="btn btn-outline btn-sm" onclick="openEditDeTai(${dt.id}, ${JSON.stringify(dt).replace(/"/g,'&quot;')})">✏️ Sửa</button>
        <button class="btn btn-danger btn-sm" onclick="deleteDeTai(${dt.id})">🗑 Xóa</button>
      </div>
    </div>
  `).join('')}</div>`;
}

// 2. Xử lý Tạo đề tài mới (Đây là phần bạn đang bị thiếu)
async function initGvHandlers() {
  const formCreate = document.getElementById('form-create-dt');
  if (formCreate) {
    formCreate.onsubmit = async (e) => {
      e.preventDefault();
      const formData = new FormData(formCreate);
      const body = Object.fromEntries(formData.entries());

      const r = await api('api/detai.php', 'POST', body);
      if (r.success) {
        toast('Tạo đề tài thành công!');
        formCreate.reset();
        showPage('gv-list');
        loadGvList();
      } else {
        const errEl = document.getElementById('form-create-error');
        if (errEl) {
          errEl.textContent = r.message;
          errEl.style.display = 'block';
        } else {
          toast(r.message, 'error');
        }
      }
    };
  }
}

// 3. Xem chi tiết đề tài và danh sách SV đăng ký
async function openDetailGv(dtId, title) {
  openPanel(title, '<div class="spinner"></div>');
  const r = await api(`api/detai.php?id=${dtId}`);
  if (!r.success) {
    document.getElementById('panel-body').innerHTML = `<p style="color:var(--red)">${r.message}</p>`;
    return;
  }
  
  const dt = r.data;
  const dkList = dt.dang_ky || [];

  const svRows = dkList.length
    ? dkList.map(dk => `
      <tr>
        <td><strong>${esc(dk.ma_sv)}</strong><br><small style="color:var(--muted)">${esc(dk.ho_ten)}</small></td>
        <td>${statusDK[dk.trang_thai] || dk.trang_thai}</td>
        <td>${dk.diem_so !== null ? parseFloat(dk.diem_so).toFixed(1) : '—'}</td>
        <td>
          <button class="btn btn-teal btn-xs"
            onclick="openGradeModal(${dk.id},'${esc(dk.ho_ten)}',${dk.diem_so ?? 'null'})">
            📝 Nhập điểm
          </button>
        </td>
      </tr>`).join('')
    : `<tr><td colspan="4" style="text-align:center;color:var(--muted);padding:24px">Chưa có sinh viên đăng ký</td></tr>`;

  document.getElementById('panel-body').innerHTML = `
    <div class="info-grid">
      <div class="info-item"><label>Trạng thái</label><p>${statusDT[dt.trang_thai] || dt.trang_thai}</p></div>
      <div class="info-item"><label>Số SV đăng ký</label><p>${dkList.length} người</p></div>
      <div class="info-item" style="grid-column:1/-1"><label>Mô tả</label><p>${esc(dt.mo_ta || '—')}</p></div>
    </div>
    <div class="section-title">Danh sách sinh viên (${dkList.length})</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Sinh viên</th><th>Trạng thái</th><th>Điểm</th><th>Thao tác</th></tr></thead>
        <tbody>${svRows}</tbody>
      </table>
    </div>`;
}

// 4. Tải tất cả sinh viên (Trang Chấm điểm & Theo dõi)
async function loadGvStudents() {
  const el = document.getElementById('gv-students-content');
  if (!el) return;
  
  el.innerHTML = '<div class="spinner"></div>';
  const r = await api('api/detai.php');
  if (!r.success) { el.innerHTML = `<p style="color:var(--red);padding:16px">${r.message}</p>`; return; }

  const dtList = r.data;
  let allRows = [];
  
  for (const dt of dtList) {
    const r2 = await api(`api/detai.php?id=${dt.id}`);
    if (r2.success && r2.data.dang_ky) {
      r2.data.dang_ky.forEach(dk => {
        allRows.push({ ...dk, ten_de_tai: dt.ten_de_tai });
      });
    }
  }

  if (!allRows.length) {
    el.innerHTML = '<div class="empty-state"><div class="icon">🎓</div><h3>Chưa có sinh viên nào đăng ký</h3></div>';
    return;
  }

  el.innerHTML = `
    <div class="card">
      <div class="table-wrap">
        <table>
          <thead><tr>
            <th>Sinh viên</th><th>Đề tài</th><th>Trạng thái</th>
            <th>File báo cáo</th><th>Điểm</th><th>Thao tác</th>
          </tr></thead>
          <tbody>${allRows.map(dk => `
            <tr>
              <td><strong>${esc(dk.ma_sv)}</strong><br><small style="color:var(--muted)">${esc(dk.ho_ten)}</small></td>
              <td style="max-width:180px;font-size:.83rem">${esc(dk.ten_de_tai)}</td>
              <td>${statusDK[dk.trang_thai] || dk.trang_thai}</td>
              <td style="font-size:.82rem">${dk.file_bao_cao ? `📎 ${esc(dk.file_bao_cao)}` : '<span style="color:var(--muted)">—</span>'}</td>
              <td>${dk.diem_so !== null ? `<b style="color:var(--teal)">${parseFloat(dk.diem_so).toFixed(1)}</b>` : '—'}</td>
              <td>
                <button class="btn btn-teal btn-xs"
                  onclick="openGradeModal(${dk.id},'${esc(dk.ho_ten)}',${dk.diem_so ?? 'null'})">
                  📝 Nhập điểm
                </button>
              </td>
            </tr>`).join('')}
          </tbody>
        </table>
      </div>
    </div>`;
}

// 5. Chỉnh sửa và Xóa đề tài
function openEditDeTai(id, dt) {
  if (typeof dt === 'string') dt = JSON.parse(dt.replace(/&quot;/g, '"'));
  document.getElementById('edit-dt-id').value = id;
  document.getElementById('edit-dt-ten').value = dt.ten_de_tai;
  document.getElementById('edit-dt-mota').value = dt.mo_ta || '';
  document.getElementById('edit-dt-tt').value = dt.trang_thai;
  openModal('modal-edit-dt');
}

async function submitEditDeTai() {
  const id = document.getElementById('edit-dt-id').value;
  const body = {
    ten_de_tai: document.getElementById('edit-dt-ten').value,
    mo_ta:      document.getElementById('edit-dt-mota').value,
    trang_thai: document.getElementById('edit-dt-tt').value,
  };
  const r = await api(`api/detai.php?id=${id}`, 'PUT', body);
  if (r.success) {
    toast('Đã cập nhật đề tài!');
    closeModal('modal-edit-dt');
    loadGvList();
    if (typeof closePanel === 'function') closePanel();
  } else {
    toast(r.message, 'error');
  }
}

async function deleteDeTai(id) {
  if (!confirm('Bạn có chắc muốn xóa đề tài này?')) return;
  const r = await api(`api/detai.php?id=${id}`, 'DELETE');
  if (r.success) { 
    toast('Đã xóa đề tài!'); 
    loadGvList(); 
    if (typeof closePanel === 'function') closePanel(); 
  } else {
    toast(r.message, 'error');
  }
}

// 6. XỬ LÝ NHẬP ĐIỂM
function openGradeModal(dkId, tenSv, diemSo) {
  document.getElementById('grade-dk-id').value = dkId;
  document.getElementById('grade-sv-info').innerHTML = `Sinh viên: <strong>${esc(tenSv)}</strong>`;
  document.getElementById('grade-diemSo').value = (diemSo !== null && diemSo !== undefined) ? diemSo : '';
  openModal('modal-grade');
}

async function submitGrade() {
  const id = document.getElementById('grade-dk-id').value;
  const ds = document.getElementById('grade-diemSo').value.trim();

  if (ds === "" || isNaN(ds)) {
    toast("Vui lòng nhập điểm số hợp lệ!", "error");
    return;
  }

  const score = parseFloat(ds);
  if (score < 0 || score > 10) {
    toast("Điểm phải từ 0 đến 10!", "error");
    return;
  }

  const body = { 
    diem_so: score,
    trang_thai: 'Da_cham' 
  };

  const r = await api(`api/dangkydetai.php?id=${id}`, 'PUT', body);
  if (r.success) {
    toast('✅ Đã lưu điểm thành công!');
    closeModal('modal-grade');
    loadGvList();
    loadGvStudents();
    if (typeof closePanel === 'function') closePanel();
  } else {
    toast(r.message, 'error');
  }
}

// Đảm bảo chạy init khi trang load
document.addEventListener('DOMContentLoaded', () => {
  if (IS_GV) {
    loadGvList();
    initGvHandlers(); // Kích hoạt bộ lắng nghe form tạo đề tài
  } else {
    loadSvBrowse();
  }
});

// ════════════════════════════════
//  SINH VIÊN (giữ nguyên toàn bộ)
// ════════════════════════════════

async function loadSvBrowse() {
  const el = document.getElementById('sv-browse-content');
  el.innerHTML = '<div class="spinner"></div>';
  const r = await api('api/detai.php');
  if (!r.success) { el.innerHTML = `<p style="color:var(--red);padding:16px">${r.message}</p>`; return; }
  allThesisList = r.data.list || [];
  renderSvBrowse(allThesisList);
}

function filterThesis() {
  const q = document.getElementById('filter-search').value.toLowerCase();
  const filtered = allThesisList.filter(dt =>
    !q || dt.ten_de_tai.toLowerCase().includes(q) ||
    (dt.ten_giang_vien || '').toLowerCase().includes(q)
  );
  renderSvBrowse(filtered);
}

function renderSvBrowse(list) {
  const el = document.getElementById('sv-browse-content');
  if (!list.length) {
    el.innerHTML = `<div class="empty-state">
      <div class="icon">🔍</div>
      <h3>Không tìm thấy đề tài nào</h3>
      <p>Hiện tại chưa có đề tài mở đăng ký hoặc không khớp từ khóa tìm kiếm.</p>
    </div>`;
    return;
  }

  el.innerHTML = `<div class="thesis-grid">${list.map(dt => {
    const daDangKy = dt.da_dang_ky_id;
    let actionBtn = '';

    if (daDangKy) {
      const canCancel = dt.trang_thai_dk === 'Cho_duyet';
      actionBtn = `
        <span class="badge badge-submitted" style="padding:6px 12px">✓ Đã đăng ký</span>
        ${canCancel ? `<button class="btn btn-danger btn-sm" onclick="huyDangKy(${daDangKy})">Hủy đăng ký</button>` : ''}`;
    } else {
      actionBtn = `<button class="btn btn-teal btn-sm" onclick="dangKy(${dt.id})">📌 Nhận đề tài này</button>`;
    }

    return `
      <div class="thesis-card">
        <div class="tc-head">
          <h4>${esc(dt.ten_de_tai)}</h4>
          <p>${esc(dt.mo_ta || 'Chưa có mô tả.')}</p>
        </div>
        <div class="tc-meta">
          <div class="tc-info">
            <span>👨‍🏫 ${esc(dt.ten_giang_vien)}</span>
            <span>👥 ${dt.so_dang_ky} SV đã đăng ký</span>
          </div>
          ${statusDT[dt.trang_thai] || ''}
        </div>
        <div class="tc-actions">${actionBtn}</div>
      </div>`;
  }).join('')}</div>`;
}

async function dangKy(deTaiId) {
  if (!confirm('Bạn có chắc muốn nhận đề tài này?\nMỗi sinh viên chỉ được đăng ký 1 đề tài.')) return;
  const r = await api('api/dangkydetai.php', 'POST', { de_tai_id: deTaiId });
  if (r.success) {
    toast(r.message);
    loadSvBrowse();
    loadMyRegistration();
  } else {
    toast(r.message, 'error');
  }
}

async function huyDangKy(dkId) {
  if (!confirm('Bạn có chắc muốn hủy đăng ký đề tài này?')) return;
  const r = await api(`api/dangkydetai.php?id=${dkId}`, 'DELETE');
  if (r.success) {
    toast('Đã hủy đăng ký!');
    loadSvBrowse();
    loadMyRegistration();
  } else {
    toast(r.message, 'error');
  }
}

async function loadMyRegistration() {
  const el = document.getElementById('sv-my-content');
  if (!el) return;
  el.innerHTML = '<div class="spinner"></div>';

  const r = await api('api/detai.php');
  if (!r.success) { el.innerHTML = `<p style="color:var(--red);padding:16px">${r.message}</p>`; return; }
  const list = r.data.list || [];
  const myDt = list.find(dt => dt.da_dang_ky_id);

  if (!myDt) {
    el.innerHTML = `<div class="empty-state">
      <div class="icon">📭</div>
      <h3>Bạn chưa đăng ký đề tài nào</h3>
      <p>Hãy khám phá danh sách đề tài và nhận một đề tài phù hợp!</p>
      <button class="btn btn-teal" style="margin-top:16px" onclick="showPage('sv-browse')">🔍 Xem danh sách đề tài</button>
    </div>`;
    return;
  }

  const r2 = await api(`api/dangkydetai.php?id=${myDt.da_dang_ky_id}`);
  if (!r2.success) { el.innerHTML = `<p style="color:var(--red);padding:16px">${r2.message}</p>`; return; }
  const dk = r2.data;

  el.innerHTML = `
    <div style="max-width:720px;display:flex;flex-direction:column;gap:20px">
      <div class="card">
        <div class="card-header">
          <h3>📋 ${esc(dk.ten_de_tai)}</h3>
          ${statusDK[dk.trang_thai] || ''}
        </div>
        <div class="card-body">
          <div class="info-grid">
            <div class="info-item"><label>Giảng viên hướng dẫn</label><p>👨‍🏫 ${esc(dk.ten_giang_vien)}</p></div>
            <div class="info-item"><label>Ngày đăng ký</label><p>📅 ${fmtDate(dk.created_at)}</p></div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3>📊 Tiến độ thực hiện</h3>
          <button class="btn btn-teal btn-sm"
            onclick="openProgressModal(${dk.id}, ${dk.tien_do}, '${esc(dk.file_bao_cao || '')}')">
            📤 Cập nhật tiến độ
          </button>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:14px">
          <div>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px">
              <span style="font-size:.875rem;color:var(--muted)">Tiến độ hoàn thành</span>
              <strong style="color:var(--teal)">${dk.tien_do}%</strong>
            </div>
            <div class="progress-wrap" style="height:12px">
              <div class="progress-bar" style="width:${dk.tien_do}%"></div>
            </div>
          </div>
          <div class="info-grid">
            <div class="info-item">
              <label>File báo cáo</label>
              <p>${dk.file_bao_cao ? `📎 ${esc(dk.file_bao_cao)}` : '<span style="color:var(--muted)">Chưa nộp</span>'}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><h3>💬 Nhận xét & Kết quả</h3></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:14px">
          <div class="form-group">
            <label>Nhận xét của giảng viên</label>
            <div style="background:var(--paper);border:1.5px solid var(--border);border-radius:8px;padding:12px 14px;font-size:.9rem;color:var(--ink-soft);min-height:60px">
              ${dk.nhan_xet ? esc(dk.nhan_xet) : '<span style="color:var(--muted)">Chưa có nhận xét</span>'}
            </div>
          </div>
          <div class="info-item">
            <label>Điểm số</label>
            ${dk.diem_so !== null && dk.diem_so !== undefined
              ? `<div class="score-big" style="margin-top:6px"><span class="num">${parseFloat(dk.diem_so).toFixed(1)}</span><span class="denom">/10</span></div>`
              : '<p style="color:var(--muted)">Chưa chấm điểm</p>'
            }
          </div>
        </div>
      </div>
    </div>`;
}

function openProgressModal(dkId, tienDo, fileBaoCao) {
  document.getElementById('prog-dk-id').value = dkId;
  document.getElementById('prog-slider').value = tienDo;
  document.getElementById('prog-val-label').textContent = tienDo + '%';
  document.getElementById('prog-file').value = fileBaoCao;
  document.getElementById('prog-nop-bao-cao').checked = false;
  openModal('modal-progress');
}

async function submitProgress() {
  const id     = document.getElementById('prog-dk-id').value;
  const tienDo = +document.getElementById('prog-slider').value;
  const file   = document.getElementById('prog-file').value.trim();
  const nop    = document.getElementById('prog-nop-bao-cao').checked;

  const body = { tien_do: tienDo };
  if (file) body.file_bao_cao = file;
  if (nop)  body.nop_bao_cao  = true;

  const r = await api(`api/dangkydetai.php?id=${id}`, 'PUT', body);
  if (r.success) {
    toast('Cập nhật tiến độ thành công!');
    closeModal('modal-progress');
    loadMyRegistration();
  } else {
    toast(r.message, 'error');
  }
}

// ── UTILS ──
function esc(str) {
  if (str == null) return '';
  return String(str)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

function fmtDate(str) {
  if (!str) return '—';
  const d = new Date(str);
  return isNaN(d) ? str : d.toLocaleDateString('vi-VN', { day:'2-digit', month:'2-digit', year:'numeric' });
}

// ── INIT ──
document.addEventListener('DOMContentLoaded', () => {
  if (IS_GV) {
    loadGvList();
  } else {
    loadSvBrowse();
  }
});
</script>

</div>
</body>
</html>