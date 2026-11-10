<?php
// Session was already started in main index.php
$isGV = ($_SESSION['vai_tro'] ?? '') === 'Giang_vien';
?>
<style>
/* CSS dành riêng cho module Đề Tài */
:root {
  --ink:      #0d0d14;
  --ink-soft: #3a3a4a;
  --paper:    #f4f2ed;
  --cream:    #ede9df;
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

.dt-container {
    font-family: 'DM Sans', sans-serif;
    color: var(--ink);
}

.dt-container .page { display: none; }
.dt-container .page.active { display: block; animation: fadeIn .25s ease; }
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}

.dt-container .page-header {
  display: flex; align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 28px; flex-wrap: wrap; gap: 16px;
}
.dt-container .page-header h1 {
  font-family: 'Syne', sans-serif;
  font-size: 1.6rem; font-weight: 700;
  color: var(--ink); line-height: 1.2;
}
.dt-container .page-header p { color: var(--muted); font-size: .875rem; margin-top: 4px; }

/* ── THESIS GRID ── */
.thesis-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 20px;
}
.thesis-card {
  background: #fff;
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

/* ── DETAIL PANEL ── */
.detail-panel {
  position: fixed; top: 0; right: 0; bottom: 0;
  width: 520px; background: #fff;
  border-left: 1.5px solid var(--border);
  box-shadow: -8px 0 40px rgba(0,0,0,.1);
  z-index: 1050; display: flex; flex-direction: column;
  transform: translateX(100%);
  transition: transform .3s cubic-bezier(.4,0,.2,1);
  overflow-y: auto;
}
.detail-panel.open { transform: translateX(0); }
.detail-panel-head {
  padding: 20px 24px; border-bottom: 1.5px solid var(--border);
  display: flex; align-items: center; gap: 12px;
  background: var(--paper); position: sticky; top: 0; z-index: 10;
}
.detail-panel-head h3 {
  flex: 1; font-family: 'Syne', sans-serif;
  font-size: 1rem; font-weight: 700; margin: 0;
}
.detail-panel-body { padding: 24px; flex: 1; }

.dt-badge {
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

.dt-form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 15px;}
.dt-form-group label {
  font-size: .78rem; font-weight: 600;
  letter-spacing: .05em; text-transform: uppercase; color: var(--muted);
}
.dt-form-group input[type=text], .dt-form-group input[type=number], .dt-form-group select, .dt-form-group textarea {
  width: 100%; padding: 10px 13px;
  border: 1.5px solid var(--border); border-radius: 8px;
  font-family: 'DM Sans', sans-serif; font-size: .9rem;
  color: var(--ink); background: var(--paper);
  outline: none; transition: border-color .2s, box-shadow .2s;
}
.dt-form-group input:focus, .dt-form-group select:focus, .dt-form-group textarea:focus {
  border-color: var(--teal);
  box-shadow: 0 0 0 3px rgba(46,107,92,.12); background: #fff;
}
.dt-form-group textarea { resize: vertical; min-height: 80px; }

/* Modal overlay fix */
.dt-modal-overlay {
  position: fixed; inset: 0; z-index: 1060;
  background: rgba(0,0,0,.45); backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center;
  padding: 24px; opacity: 0; pointer-events: none; transition: opacity .2s;
}
.dt-modal-overlay.open { opacity: 1; pointer-events: all; }
.dt-modal {
  background: #fff; border-radius: 14px;
  width: 100%; max-width: 560px;
  box-shadow: 0 24px 80px rgba(0,0,0,.2);
  transform: scale(.95) translateY(16px);
  transition: transform .2s;
  max-height: 90vh; overflow-y: auto;
}
.dt-modal-overlay.open .dt-modal { transform: scale(1) translateY(0); }
.dt-modal-head {
  padding: 22px 24px 18px;
  border-bottom: 1.5px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
}
.dt-modal-head h2 { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 700; margin: 0; }
.dt-modal-body { padding: 24px; display: flex; flex-direction: column; }
.dt-modal-foot {
  padding: 16px 24px; border-top: 1.5px solid var(--border);
  display: flex; gap: 10px; justify-content: flex-end;
}
.modal-close-btn {
    background: none; border: none; font-size: 1.3rem;
  cursor: pointer; color: var(--muted); padding: 4px 8px;
  border-radius: 6px; transition: background .15s;
}
.modal-close-btn:hover { background: var(--paper); color: var(--ink); }

#dt-toast {
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

/* Spinner & empty state */
.spinner {
  width: 28px; height: 28px;
  border: 3px solid var(--border); border-top-color: var(--teal);
  border-radius: 50%;
  animation: spin .8s linear infinite; margin: 40px auto;
}
@keyframes spin { to { transform: rotate(360deg); } }
.empty-state { text-align: center; padding: 64px 24px; color: var(--muted); }
.empty-state .icon { font-size: 3rem; margin-bottom: 12px; }

</style>

<div class="dt-container">
    
    <!-- MENU CON CỦA ĐỀ TÀI -->
    <div class="mb-4 d-flex gap-2 border-bottom pb-3">
        <?php if ($isGV): ?>
        <button class="btn btn-primary dt-nav-item active" data-page="gv-list" onclick="showPage('gv-list')">📋 Đề tài của tôi</button>
        <button class="btn btn-outline-primary dt-nav-item" data-page="gv-students" onclick="showPage('gv-students'); loadGvStudents()">👥 Chấm điểm & Theo dõi</button>
        <?php else: ?>
        <button class="btn btn-primary dt-nav-item active" data-page="sv-browse" onclick="showPage('sv-browse')">🔍 Danh sách đề tài</button>
        <button class="btn btn-outline-primary dt-nav-item" data-page="sv-my" onclick="showPage('sv-my'); loadMyRegistration()">📌 Đề tài của tôi</button>
        <?php endif; ?>
    </div>

    <!-- NỘI DUNG CHÍNH -->
    <main class="main-content">
        <?php if ($isGV): ?>
        <!-- ═══ GIẢNG VIÊN: Danh sách đề tài + nút thêm ═══ -->
        <div id="page-gv-list" class="page active">
            <div class="page-header">
                <div>
                <h1>📋 Quản lý Đề tài</h1>
                <p>Danh sách đề tài bạn đã tạo</p>
                </div>
                <button class="btn btn-success" onclick="showPage('gv-create')">➕ Thêm đề tài mới</button>
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
                <button class="btn btn-secondary" onclick="showPage('gv-list')">← Quay lại</button>
            </div>
            <div class="card" style="max-width:640px">
                <div class="card-header"><h3>Thông tin đề tài</h3></div>
                <div class="card-body">
                <form id="form-create-dt">
                    <div class="dt-form-group">
                    <label>Tên đề tài *</label>
                    <input type="text" name="ten_de_tai" placeholder="Ví dụ: Hệ thống nhận diện khuôn mặt điểm danh..." required>
                    </div>
                    <div class="dt-form-group">
                    <label>Mô tả</label>
                    <textarea name="mo_ta" placeholder="Mô tả chi tiết về đề tài, công nghệ sử dụng, yêu cầu sinh viên..."></textarea>
                    </div>
                    <div class="dt-form-group">
                    <label>Trạng thái</label>
                    <select name="trang_thai">
                        <option value="Mo_dang_ky">🟢 Mở đăng ký</option>
                        <option value="Dong_dang_ky">🟡 Đóng đăng ký</option>
                    </select>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:10px">
                    <button type="submit" class="btn btn-primary">✓ Tạo đề tài</button>
                    <button type="button" class="btn btn-secondary" onclick="showPage('gv-list')">Hủy</button>
                    </div>
                    <div id="form-create-error" style="display:none;color:var(--red);font-size:.875rem;background:var(--red-l);padding:10px 14px;border-radius:8px; margin-top:10px"></div>
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
                <input type="text" id="filter-search" class="form-control"
                    placeholder="🔍 Tìm theo tên đề tài hoặc giảng viên..."
                    oninput="filterThesis()"
                    style="width:320px;">
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
    <button class="modal-close-btn" onclick="closePanel()">✕</button>
  </div>
  <div class="detail-panel-body" id="panel-body"></div>
</div>

<!-- MODAL: Chỉnh sửa đề tài -->
<div class="dt-modal-overlay" id="modal-edit-dt">
  <div class="dt-modal">
    <div class="dt-modal-head">
      <h2>✏️ Chỉnh sửa đề tài</h2>
      <button class="modal-close-btn" onclick="closeModal('modal-edit-dt')">✕</button>
    </div>
    <div class="dt-modal-body">
      <input type="hidden" id="edit-dt-id">
      <div class="dt-form-group">
        <label>Tên đề tài</label>
        <input type="text" id="edit-dt-ten">
      </div>
      <div class="dt-form-group">
        <label>Mô tả</label>
        <textarea id="edit-dt-mota"></textarea>
      </div>
      <div class="dt-form-group">
        <label>Trạng thái</label>
        <select id="edit-dt-tt">
          <option value="Mo_dang_ky">🟢 Mở đăng ký</option>
          <option value="Dong_dang_ky">🟡 Đóng đăng ký</option>
          <option value="Hoan_thanh">✅ Hoàn thành</option>
        </select>
      </div>
    </div>
    <div class="dt-modal-foot">
      <button class="btn btn-secondary" onclick="closeModal('modal-edit-dt')">Hủy</button>
      <button class="btn btn-primary" onclick="submitEditDeTai()">💾 Lưu thay đổi</button>
    </div>
  </div>
</div>

<!-- MODAL CHỈ NHẬP ĐIỂM -->
<div class="dt-modal-overlay" id="modal-grade">
  <div class="dt-modal" style="max-width:420px;">
    <div class="dt-modal-head">
      <h2>📝 Nhập điểm</h2>
      <button class="modal-close-btn" onclick="closeModal('modal-grade')">✕</button>
    </div>
    <div class="dt-modal-body">
      <input type="hidden" id="grade-dk-id">
      <div id="grade-sv-info" style="background:var(--paper);border-radius:8px;padding:12px 14px;font-size:.875rem;color:var(--ink-soft); margin-bottom: 10px;"></div>
      <div class="dt-form-group">
        <label>Điểm số (0 – 10)</label>
        <input type="number" id="grade-diemSo" min="0" max="10" step="0.1" placeholder="Ví dụ: 8.5">
      </div>
    </div>
    <div class="dt-modal-foot">
      <button class="btn btn-secondary" onclick="closeModal('modal-grade')">Hủy</button>
      <button class="btn btn-primary" onclick="submitGrade()">💾 Lưu điểm</button>
    </div>
  </div>
</div>

<!-- MODAL: Cập nhật tiến độ (SV) -->
<div class="dt-modal-overlay" id="modal-progress">
  <div class="dt-modal">
    <div class="dt-modal-head">
      <h2>📊 Cập nhật tiến độ</h2>
      <button class="modal-close-btn" onclick="closeModal('modal-progress')">✕</button>
    </div>
    <div class="dt-modal-body">
      <input type="hidden" id="prog-dk-id">
      <div class="dt-form-group">
        <label>Tiến độ: <strong id="prog-val-label">0%</strong></label>
        <input type="range" id="prog-slider" min="0" max="100" step="5" value="0"
               oninput="document.getElementById('prog-val-label').textContent=this.value+'%'">
      </div>
      <div class="dt-form-group">
        <label>Link / Tên file báo cáo</label>
        <input type="text" id="prog-file"
               placeholder="vd: bao_cao_tuan3.pdf hoặc link Google Drive...">
      </div>
      <div class="dt-form-group">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;text-transform:none">
          <input type="checkbox" id="prog-nop-bao-cao"
                 style="width:auto;border:none;padding:0;accent-color:var(--teal)">
          Đánh dấu là đã nộp báo cáo hoàn thành
        </label>
      </div>
    </div>
    <div class="dt-modal-foot">
      <button class="btn btn-secondary" onclick="closeModal('modal-progress')">Hủy</button>
      <button class="btn btn-success" onclick="submitProgress()">📤 Cập nhật</button>
    </div>
  </div>
</div>

<div id="dt-toast"></div>

<script>
const IS_GV = <?= $isGV ? 'true' : 'false' ?>;
let allThesisList = [];

// API calls with relative path from index.php
async function api(endpoint, method = 'GET', body = null) {
  let urlStr = endpoint;
  if (urlStr.startsWith('api/dangkydetai.php')) {
      urlStr = urlStr.replace('api/dangkydetai.php?', 'api.php?action=dangky&').replace('api/dangkydetai.php', 'api.php?action=dangky');
  } else if (urlStr.startsWith('api/detai.php')) {
      urlStr = urlStr.replace('api/detai.php?', 'api.php?action=detai&').replace('api/detai.php', 'api.php?action=detai');
  }
  const url = 'DeTai/' + urlStr; // since we are at index.php

  const opts = { method, headers: { 'Content-Type': 'application/json' } };
  if (body) opts.body = JSON.stringify(body);

  try {
    const res = await fetch(url, opts);
    const text = await res.text();

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
    return { success: false, message: 'Lỗi kết nối máy chủ.' };
  }
}

function showPage(name) {
  document.querySelectorAll('.dt-container .page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.dt-nav-item').forEach(n => {
      n.classList.remove('active', 'btn-primary');
      n.classList.add('btn-outline-primary');
  });

  const page = document.getElementById('page-' + name);
  if (page) page.classList.add('active');

  const nav = document.querySelector(`[data-page="${name}"]`);
  if (nav) {
      nav.classList.add('active', 'btn-primary');
      nav.classList.remove('btn-outline-primary');
  }

  closePanel();
  if (name === 'gv-list') loadGvList();
  if (name === 'sv-browse') loadSvBrowse();
}

function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.dt-modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});

function openPanel(title, html) {
  document.getElementById('panel-title').textContent = title;
  document.getElementById('panel-body').innerHTML = html;
  document.getElementById('detail-panel').classList.add('open');
}
function closePanel() { document.getElementById('detail-panel').classList.remove('open'); }

function toast(msg, type = 'success') {
  const el = document.createElement('div');
  el.className = `toast-item ${type}`;
  el.innerHTML = (type === 'success' ? '✓ ' : '✕ ') + msg;
  document.getElementById('dt-toast').appendChild(el);
  setTimeout(() => el.remove(), 3800);
}

const statusDT = {
  Mo_dang_ky:   '<span class="dt-badge badge-open">🟢 Mở đăng ký</span>',
  Dong_dang_ky: '<span class="dt-badge badge-closed">🟡 Đóng đăng ký</span>',
  Hoan_thanh:   '<span class="dt-badge badge-done">✅ Hoàn thành</span>',
};
const statusDK = {
  Cho_duyet:      '<span class="dt-badge badge-waiting">⏳ Chờ duyệt</span>',
  Dang_thuc_hien: '<span class="dt-badge badge-wip">🔵 Đang thực hiện</span>',
  Da_nop:         '<span class="dt-badge badge-submitted">📤 Đã nộp</span>',
  Da_cham:        '<span class="dt-badge badge-graded">✅ Đã chấm</span>',
};

// GIẢNG VIÊN
async function loadGvList() {
  const el = document.getElementById('gv-list-content');
  if (!el) return;
  
  el.innerHTML = '<div class="spinner"></div>';
  const r = await api('api/detai.php');
  
  if (!r.success) { el.innerHTML = `<p class="text-danger p-3">${r.message}</p>`; return; }

  const list = r.data;
  if (!list || !list.length) {
    el.innerHTML = `<div class="empty-state"><div class="icon">📭</div><h3>Chưa có đề tài nào</h3><p>Hãy tạo đề tài đầu tiên của bạn!</p><button class="btn btn-success mt-3" onclick="showPage('gv-create')">➕ Thêm đề tài mới</button></div>`;
    return;
  }

  el.innerHTML = `<div class="thesis-grid">${list.map(dt => `
    <div class="thesis-card">
      <div class="tc-head">
        <h4>${esc(dt.ten_de_tai)}</h4>
        <p>${esc(dt.mo_ta || 'Chưa có mô tả.')}</p>
      </div>
      <div class="tc-meta">
        <div class="tc-info"><span>👥 ${dt.so_dang_ky} SV đăng ký</span></div>
        ${statusDT[dt.trang_thai] || ''}
      </div>
      <div class="tc-actions">
        <button class="btn btn-outline-secondary btn-sm" onclick="openDetailGv(${dt.id}, '${esc(dt.ten_de_tai)}')">👁 Xem</button>
        <button class="btn btn-info btn-sm text-white" onclick="showPage('gv-students'); loadGvStudents()">👨‍🎓 Sinh viên</button>
        <button class="btn btn-outline-primary btn-sm" onclick="openEditDeTai(${dt.id}, ${JSON.stringify(dt).replace(/"/g,'&quot;')})">✏️ Sửa</button>
        <button class="btn btn-danger btn-sm" onclick="deleteDeTai(${dt.id})">🗑 Xóa</button>
      </div>
    </div>
  `).join('')}</div>`;
}

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
        } else toast(r.message, 'error');
      }
    };
  }
}

async function openDetailGv(dtId, title) {
  openPanel(title, '<div class="spinner"></div>');
  const r = await api(`api/detai.php?id=${dtId}`);
  if (!r.success) {
    document.getElementById('panel-body').innerHTML = `<p class="text-danger">${r.message}</p>`;
    return;
  }
  
  const dt = r.data;
  const dkList = dt.dang_ky || [];

  const svRows = dkList.length
    ? dkList.map(dk => `
      <tr>
        <td><strong>${esc(dk.ma_sv)}</strong><br><small class="text-muted">${esc(dk.ho_ten)}</small></td>
        <td>${statusDK[dk.trang_thai] || dk.trang_thai}</td>
        <td>${dk.diem_so !== null ? parseFloat(dk.diem_so).toFixed(1) : '—'}</td>
        <td>
          <button class="btn btn-primary btn-sm"
            onclick="openGradeModal(${dk.id},'${esc(dk.ho_ten)}',${dk.diem_so ?? 'null'})">
            📝 Nhập điểm
          </button>
        </td>
      </tr>`).join('')
    : `<tr><td colspan="4" class="text-center text-muted p-4">Chưa có sinh viên đăng ký</td></tr>`;

  document.getElementById('panel-body').innerHTML = `
    <div class="row mb-3">
      <div class="col-6"><strong>Trạng thái</strong><br>${statusDT[dt.trang_thai] || dt.trang_thai}</div>
      <div class="col-6"><strong>Số SV đăng ký</strong><br>${dkList.length} người</div>
      <div class="col-12 mt-2"><strong>Mô tả</strong><br>${esc(dt.mo_ta || '—')}</div>
    </div>
    <h5 class="border-bottom pb-2 mb-3">Danh sách sinh viên (${dkList.length})</h5>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead class="table-light"><tr><th>Sinh viên</th><th>Trạng thái</th><th>Điểm</th><th>Thao tác</th></tr></thead>
        <tbody>${svRows}</tbody>
      </table>
    </div>`;
}

async function loadGvStudents() {
  const el = document.getElementById('gv-students-content');
  if (!el) return;
  
  el.innerHTML = '<div class="spinner"></div>';
  const r = await api('api/detai.php');
  if (!r.success) { el.innerHTML = `<p class="text-danger p-3">${r.message}</p>`; return; }

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
    <div class="card shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light"><tr>
            <th>Sinh viên</th><th>Đề tài</th><th>Trạng thái</th>
            <th>File báo cáo</th><th>Điểm</th><th>Thao tác</th>
          </tr></thead>
          <tbody>${allRows.map(dk => `
            <tr>
              <td><strong>${esc(dk.ma_sv)}</strong><br><small class="text-muted">${esc(dk.ho_ten)}</small></td>
              <td style="max-width:180px;font-size:.83rem">${esc(dk.ten_de_tai)}</td>
              <td>${statusDK[dk.trang_thai] || dk.trang_thai}</td>
              <td style="font-size:.82rem">${dk.file_bao_cao ? `📎 ${esc(dk.file_bao_cao)}` : '<span class="text-muted">—</span>'}</td>
              <td>${dk.diem_so !== null ? `<b class="text-success">${parseFloat(dk.diem_so).toFixed(1)}</b>` : '—'}</td>
              <td>
                <button class="btn btn-primary btn-sm"
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
  } else toast(r.message, 'error');
}

async function deleteDeTai(id) {
  if (!confirm('Bạn có chắc muốn xóa đề tài này?')) return;
  const r = await api(`api/detai.php?id=${id}`, 'DELETE');
  if (r.success) { 
    toast('Đã xóa đề tài!'); 
    loadGvList(); 
    if (typeof closePanel === 'function') closePanel(); 
  } else toast(r.message, 'error');
}

function openGradeModal(dkId, tenSv, diemSo) {
  document.getElementById('grade-dk-id').value = dkId;
  document.getElementById('grade-sv-info').innerHTML = `Sinh viên: <strong>${esc(tenSv)}</strong>`;
  document.getElementById('grade-diemSo').value = (diemSo !== null && diemSo !== undefined) ? diemSo : '';
  openModal('modal-grade');
}

async function submitGrade() {
  const id = document.getElementById('grade-dk-id').value;
  const ds = document.getElementById('grade-diemSo').value.trim();

  if (ds === "" || isNaN(ds)) { toast("Vui lòng nhập điểm số hợp lệ!", "error"); return; }
  const score = parseFloat(ds);
  if (score < 0 || score > 10) { toast("Điểm phải từ 0 đến 10!", "error"); return; }

  const body = { diem_so: score, trang_thai: 'Da_cham' };
  const r = await api(`api/dangkydetai.php?id=${id}`, 'PUT', body);
  if (r.success) {
    toast('✅ Đã lưu điểm thành công!');
    closeModal('modal-grade');
    loadGvList();
    loadGvStudents();
    if (typeof closePanel === 'function') closePanel();
  } else toast(r.message, 'error');
}


// SINH VIÊN
async function loadSvBrowse() {
  const el = document.getElementById('sv-browse-content');
  el.innerHTML = '<div class="spinner"></div>';
  const r = await api('api/detai.php');
  if (!r.success) { el.innerHTML = `<p class="text-danger p-3">${r.message}</p>`; return; }
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
    el.innerHTML = `<div class="empty-state"><div class="icon">🔍</div><h3>Không tìm thấy đề tài nào</h3><p>Hiện tại chưa có đề tài mở đăng ký hoặc không khớp từ khóa tìm kiếm.</p></div>`;
    return;
  }

  el.innerHTML = `<div class="thesis-grid">${list.map(dt => {
    const daDangKy = dt.da_dang_ky_id;
    let actionBtn = '';

    if (daDangKy) {
      const canCancel = dt.trang_thai_dk === 'Cho_duyet';
      actionBtn = `
        <span class="dt-badge badge-submitted" style="padding:6px 12px">✓ Đã đăng ký</span>
        ${canCancel ? `<button class="btn btn-danger btn-sm" onclick="huyDangKy(${daDangKy})">Hủy đăng ký</button>` : ''}`;
    } else {
      actionBtn = `<button class="btn btn-info text-white btn-sm" onclick="dangKy(${dt.id})">📌 Nhận đề tài này</button>`;
    }

    return `
      <div class="thesis-card">
        <div class="tc-head">
          <h4>${esc(dt.ten_de_tai)}</h4>
          <p>${esc(dt.mo_ta || 'Chưa có mô tả.')}</p>
        </div>
        <div class="tc-meta">
          <div class="tc-info"><span>👨‍🏫 ${esc(dt.ten_giang_vien)}</span><span>👥 ${dt.so_dang_ky} SV đã đăng ký</span></div>
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
  } else toast(r.message, 'error');
}

async function huyDangKy(dkId) {
  if (!confirm('Bạn có chắc muốn hủy đăng ký đề tài này?')) return;
  const r = await api(`api/dangkydetai.php?id=${dkId}`, 'DELETE');
  if (r.success) {
    toast('Đã hủy đăng ký!');
    loadSvBrowse();
    loadMyRegistration();
  } else toast(r.message, 'error');
}

async function loadMyRegistration() {
  const el = document.getElementById('sv-my-content');
  if (!el) return;
  el.innerHTML = '<div class="spinner"></div>';

  const r = await api('api/detai.php');
  if (!r.success) { el.innerHTML = `<p class="text-danger p-3">${r.message}</p>`; return; }
  const list = r.data.list || [];
  const myDt = list.find(dt => dt.da_dang_ky_id);

  if (!myDt) {
    el.innerHTML = `<div class="empty-state"><div class="icon">📭</div><h3>Bạn chưa đăng ký đề tài nào</h3><p>Hãy khám phá danh sách đề tài và nhận một đề tài phù hợp!</p><button class="btn btn-info text-white mt-3" onclick="showPage('sv-browse')">🔍 Xem danh sách đề tài</button></div>`;
    return;
  }

  const r2 = await api(`api/dangkydetai.php?id=${myDt.da_dang_ky_id}`);
  if (!r2.success) { el.innerHTML = `<p class="text-danger p-3">${r2.message}</p>`; return; }
  const dk = r2.data;

  el.innerHTML = `
    <div style="max-width:720px;display:flex;flex-direction:column;gap:20px">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <h4 class="m-0">📋 ${esc(dk.ten_de_tai)}</h4>
          ${statusDK[dk.trang_thai] || ''}
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-6"><strong>Giảng viên hướng dẫn</strong><br>👨‍🏫 ${esc(dk.ten_giang_vien)}</div>
            <div class="col-6"><strong>Ngày đăng ký</strong><br>📅 ${fmtDate(dk.created_at)}</div>
          </div>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <h5 class="m-0">📊 Tiến độ thực hiện</h5>
          <button class="btn btn-info text-white btn-sm"
            onclick="openProgressModal(${dk.id}, ${dk.tien_do}, '${esc(dk.file_bao_cao || '')}')">
            📤 Cập nhật tiến độ
          </button>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <span class="text-muted">Tiến độ hoàn thành</span>
              <strong class="text-success">${dk.tien_do}%</strong>
            </div>
            <div class="progress" style="height: 12px;">
              <div class="progress-bar bg-success" role="progressbar" style="width: ${dk.tien_do}%" aria-valuenow="${dk.tien_do}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
          <div>
            <strong>File báo cáo: </strong>
            ${dk.file_bao_cao ? `📎 ${esc(dk.file_bao_cao)}` : '<span class="text-muted">Chưa nộp</span>'}
          </div>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-header bg-white"><h5 class="m-0">💬 Nhận xét & Kết quả</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <strong>Nhận xét của giảng viên</strong>
            <div class="p-3 border rounded mt-1 bg-light">
              ${dk.nhan_xet ? esc(dk.nhan_xet) : '<span class="text-muted">Chưa có nhận xét</span>'}
            </div>
          </div>
          <div>
            <strong>Điểm số: </strong>
            ${dk.diem_so !== null && dk.diem_so !== undefined
              ? `<span class="h4 text-success">${parseFloat(dk.diem_so).toFixed(1)}</span><span class="text-muted">/10</span>`
              : '<span class="text-muted">Chưa chấm điểm</span>'
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
  } else toast(r.message, 'error');
}

// UTILS
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

// INIT
document.addEventListener('DOMContentLoaded', () => {
  if (IS_GV) {
    loadGvList();
    initGvHandlers();
  } else {
    loadSvBrowse();
  }
});
</script>
