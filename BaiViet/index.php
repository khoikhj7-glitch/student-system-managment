<?php
/**
 * BaiViet/index.php — View duy nhất, render bằng JS qua API
 * - Giảng viên: bảng quản lý (thêm/sửa/xóa)
 * - Sinh viên:  news feed + tìm kiếm + lọc chuyên mục + xem chi tiết
 */
$isGV = ($_SESSION['vai_tro'] ?? '') === 'Giang_vien';
?>

<!-- ═══════════════════════ CSS module ═══════════════════════ -->
<style>
/* --- Grid thẻ tin tức --- */
.news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
}
.news-card {
    background: var(--surface);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-200);
    display: flex; flex-direction: column;
    text-decoration: none; color: inherit;
    transition: transform .25s, box-shadow .25s;
    cursor: pointer;
}
.news-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }

.news-cover {
    width: 100%; height: 170px;
    background: center/cover var(--gray-200);
    position: relative;
}
.news-cover .badge-cat {
    position: absolute; top: 12px; left: 12px;
    background: var(--primary); color: #fff;
    padding: 3px 10px; border-radius: 20px;
    font-size: .75rem; font-weight: 700; text-transform: uppercase;
}
.news-cover .badge-hot {
    position: absolute; top: 12px; left: 12px;
    background: #dc3545; color: #fff;
    padding: 3px 10px; border-radius: 20px;
    font-size: .75rem; font-weight: 700;
}
.news-body  { padding: 18px; flex: 1; display: flex; flex-direction: column; }
.news-title { font-size: 1rem; font-weight: 700; margin-bottom: 8px; line-height: 1.4; color: var(--gray-900); }
.news-excerpt { font-size: .875rem; color: var(--gray-500); flex: 1; line-height: 1.5; }
.news-footer { padding: 12px 18px; border-top: 1px solid var(--gray-200); display: flex; justify-content: space-between; font-size: .8rem; color: var(--gray-500); }

/* --- Bộ lọc --- */
.filter-bar {
    display: flex; flex-wrap: wrap; align-items: center; gap: 10px;
    background: var(--surface); padding: 16px 20px;
    border-radius: 12px; margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
}
.cat-btn { padding: 5px 14px; border-radius: 20px; border: 1px solid var(--gray-300); background: #fff; cursor: pointer; font-size: .85rem; transition: all .15s; }
.cat-btn:hover, .cat-btn.active { background: var(--primary); color: #fff; border-color: var(--primary); }

/* --- Trang chi tiết --- */
.article-wrap { max-width: 860px; margin: 0 auto; }
.article-title { font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: 800; line-height: 1.3; margin-bottom: 16px; }
.article-meta  { display: flex; gap: 16px; flex-wrap: wrap; font-size: .875rem; color: var(--gray-500); padding-bottom: 20px; border-bottom: 1px solid var(--gray-200); margin-bottom: 28px; }
.article-cover { width: 100%; border-radius: 12px; max-height: 460px; object-fit: cover; margin-bottom: 28px; }
.article-body  { font-size: 1.05rem; line-height: 1.85; color: var(--gray-800); }
.article-body img { max-width: 100%; border-radius: 8px; margin: 16px 0; }

/* --- Loading & empty --- */
.bv-spinner { display: flex; justify-content: center; padding: 60px 0; }
.bv-spinner:after { content:''; width:36px; height:36px; border:4px solid var(--gray-200); border-top-color:var(--primary); border-radius:50%; animation: bv-spin .8s linear infinite; }
@keyframes bv-spin { to { transform: rotate(360deg); } }
.bv-empty { text-align: center; padding: 60px 20px; color: var(--gray-500); }
.bv-empty .icon { font-size: 3rem; margin-bottom: 12px; }

/* --- Modal (form thêm/sửa GV) --- */
.bv-modal-bg {
    display: none; position: fixed; inset: 0; z-index: 1050;
    background: rgba(0,0,0,.5); backdrop-filter: blur(3px);
    align-items: center; justify-content: center; padding: 20px;
}
.bv-modal-bg.open { display: flex; }
.bv-modal {
    background: #fff; border-radius: 14px; width: 100%; max-width: 720px;
    max-height: 90vh; display: flex; flex-direction: column;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
}
.bv-modal-head { padding: 20px 24px; border-bottom: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; }
.bv-modal-head h2 { margin: 0; font-size: 1.15rem; }
.bv-modal-body { padding: 24px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 16px; }
.bv-modal-foot { padding: 16px 24px; border-top: 1px solid var(--gray-200); display: flex; gap: 10px; justify-content: flex-end; }
.bv-form-row { display: flex; gap: 16px; flex-wrap: wrap; }
.bv-form-row .form-group { flex: 1; min-width: 180px; }
</style>

<!-- ═══ HEADER SINH VIÊN (chỉ hiện với SV) ═══ -->
<?php if (!$isGV): ?>
<div style="background: linear-gradient(135deg,var(--primary),var(--secondary)); color:#fff; padding:30px 24px; border-radius:14px; margin-bottom:28px; text-align:center; box-shadow:var(--shadow-md);">
    <h1 style="font-size:2rem;font-weight:800;margin-bottom:8px;">Bảng Tin Sinh Viên</h1>
    <p style="opacity:.9;">Cập nhật thông tin, thông báo và tin tức mới nhất</p>
</div>
<?php endif; ?>

<!-- ═══ THANH ĐIỀU HƯỚNG / NÚT THAO TÁC ═══ -->
<div id="bv-toolbar" style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <!-- trái: nút quay lại (ẩn lúc đầu) -->
    <button id="btn-back" class="btn btn-secondary" style="display:none;" onclick="goBack()">← Quay lại</button>
    <div id="bv-breadcrumb" style="font-size:.875rem;color:var(--gray-500);"></div>

    <!-- phải -->
    <div style="display:flex;gap:10px;margin-left:auto;">
        <?php if ($isGV): ?>
        <button class="btn btn-primary" onclick="openForm(null)">➕ Bài Viết Mới</button>
        <?php endif; ?>
    </div>
</div>

<!-- ═══ BỘ LỌC (SV) ═══ -->
<?php if (!$isGV): ?>
<div id="filter-bar" class="filter-bar" style="display:none;">
    <button class="cat-btn active" data-cat="" onclick="filterCat(this)">Tất Cả</button>
    <!-- danh mục load qua JS -->
    <div style="flex:1;min-width:200px;display:flex;gap:8px;">
        <input id="bv-search" type="text" class="form-control" placeholder="🔍 Tìm bài viết..." style="border-radius:20px;" oninput="debounceSearch()" />
    </div>
</div>
<?php endif; ?>

<!-- ═══ KHU VỰC NỘI DUNG CHÍNH ═══ -->
<div id="bv-content">
    <div class="bv-spinner"></div>
</div>

<!-- ═══════════ MODAL FORM THÊM/SỬA (GV) ═══════════ -->
<?php if ($isGV): ?>
<div id="bv-modal-bg" class="bv-modal-bg">
    <div class="bv-modal">
        <div class="bv-modal-head">
            <h2 id="modal-title">Bài Viết Mới</h2>
            <button class="btn btn-sm btn-secondary" onclick="closeForm()">✕</button>
        </div>
        <div class="bv-modal-body">
            <input type="hidden" id="edit-id">

            <div class="form-group">
                <label class="form-label">Tiêu đề <span style="color:red">*</span></label>
                <input type="text" id="f-tieu_de" class="form-control" placeholder="Nhập tiêu đề..." required>
            </div>

            <div class="form-group">
                <label class="form-label">Tóm tắt</label>
                <textarea id="f-tom_tat" class="form-control" rows="2" placeholder="Mô tả ngắn..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Nội dung chi tiết <span style="color:red">*</span></label>
                <textarea id="f-noi_dung" class="form-control" rows="6" placeholder="Nội dung..."></textarea>
            </div>

            <div class="bv-form-row">
                <div class="form-group">
                    <label class="form-label">Chuyên mục</label>
                    <select id="f-chuyen_muc" class="form-control">
                        <option value="Tin tuc">Tin Tức Chung</option>
                        <option value="Thong bao">Thông Báo Quy Chế</option>
                        <option value="Da ngoai">Hoạt động Dã Ngoại</option>
                        <option value="Tuyen sinh">Chỉ Tiêu Tuyển Sinh</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Trạng thái</label>
                    <select id="f-trang_thai" class="form-control">
                        <option value="Draft">Bản Nháp</option>
                        <option value="Published">Xuất Bản</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Ảnh Thumbnail</label>
                <input type="file" id="f-thumbnail" class="form-control" accept="image/*">
                <div id="thumb-preview" style="margin-top:8px;"></div>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" id="f-is_hot" style="width:18px;height:18px;">
                    Đánh dấu là Tin Nổi Bật 🔥
                </label>
            </div>

            <div id="form-error" style="display:none;padding:10px 14px;background:#fde8e6;color:#c0392b;border-radius:8px;font-size:.875rem;"></div>
        </div>
        <div class="bv-modal-foot">
            <button class="btn btn-secondary" onclick="closeForm()">Hủy</button>
            <button class="btn btn-primary" id="btn-save" onclick="saveArticle()">💾 Lưu bài viết</button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ═══════════════ JAVASCRIPT ═══════════════ -->
<script>
const IS_GV = <?= $isGV ? 'true' : 'false' ?>;
const API    = 'BaiViet/api.php';
let   currentCat    = '';
let   searchTimer   = null;
let   currentScreen = 'list'; // 'list' | 'detail'

// ─── Khởi động ───────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (IS_GV) loadGvList();
    else       loadNewsFeed();
});

// ─── Fetch wrapper ────────────────────────────
async function api(path, opts = {}) {
    try {
        const res  = await fetch(path, opts);
        const text = await res.text();
        return JSON.parse(text);
    } catch (e) {
        return { success: false, message: 'Lỗi kết nối máy chủ.' };
    }
}

// ─── Loading / Error helpers ──────────────────
const content = () => document.getElementById('bv-content');
function setLoading()       { content().innerHTML = '<div class="bv-spinner"></div>'; }
function setError(msg)      { content().innerHTML = `<div class="alert alert-danger">${msg}</div>`; }
function esc(s)             { return s == null ? '' : String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function fmtDate(s)         { return s ? new Date(s).toLocaleDateString('vi-VN') : '—'; }
function thumb(t)           { return t ? `public/uploads/${t}` : 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=600&q=80'; }

// ─── GIẢNG VIÊN — bảng quản lý ───────────────
async function loadGvList() {
    setLoading();
    const r = await api(API);
    if (!r.success) return setError(r.message);

    const rows = r.data;
    if (!rows.length) {
        content().innerHTML = `<div class="bv-empty"><div class="icon">📭</div><p>Chưa có bài viết nào.</p></div>`;
        return;
    }

    content().innerHTML = `
    <div style="overflow-x:auto;">
    <table class="data-table">
      <thead><tr>
        <th width="7%">Ảnh</th>
        <th width="35%">Tiêu đề</th>
        <th width="14%">Chuyên mục</th>
        <th width="12%">Trạng thái</th>
        <th width="10%">Tác giả</th>
        <th width="10%">Ngày tạo</th>
        <th width="12%">Thao tác</th>
      </tr></thead>
      <tbody>
        ${rows.map(b => `
        <tr>
          <td><img src="${thumb(b.thumbnail)}" style="width:48px;height:48px;object-fit:cover;border-radius:6px;"></td>
          <td>
            <strong>${esc(b.tieu_de)}</strong>
            ${b.is_hot == 1 ? '<span class="badge bg-danger ms-1">🔥 HOT</span>' : ''}
            <div style="font-size:.78rem;color:var(--gray-500);margin-top:2px;">👁️ ${b.luot_xem} lượt xem</div>
          </td>
          <td><span class="badge bg-secondary">${esc(b.chuyen_muc)}</span></td>
          <td>${b.trang_thai === 'Published'
                ? '<span class="badge bg-success">Đã xuất bản</span>'
                : '<span class="badge bg-warning">Bản nháp</span>'}</td>
          <td>${esc(b.tac_gia_ten)}</td>
          <td>${fmtDate(b.created_at)}</td>
          <td>
            <div class="action-group">
              <button class="btn btn-secondary btn-sm" onclick="openForm(${JSON.stringify(b).replace(/"/g,'&quot;')})">✏️ Sửa</button>
              <button class="btn btn-danger btn-sm" onclick="deleteArticle(${b.id})">🗑️</button>
            </div>
          </td>
        </tr>`).join('')}
      </tbody>
    </table></div>`;
}

// ─── SINH VIÊN — news feed ────────────────────
async function loadNewsFeed() {
    setLoading();
    const search = document.getElementById('bv-search')?.value.trim() ?? '';
    const params = new URLSearchParams();
    if (search)      params.set('search', search);
    if (currentCat)  params.set('cat', currentCat);

    const r = await api(`${API}?${params}`);
    if (!r.success) return setError(r.message);

    document.getElementById('filter-bar').style.display = '';
    loadCategories();
    showScreen('list');

    const articles = r.data;
    if (!articles.length) {
        content().innerHTML = `<div class="bv-empty"><div class="icon">🔍</div><p>Không tìm thấy bài viết nào.</p></div>`;
        return;
    }

    content().innerHTML = `<div class="news-grid">${articles.map(renderCard).join('')}</div>`;
}

function renderCard(b) {
    return `
    <div class="news-card" onclick="loadDetail(${b.id})">
      <div class="news-cover" style="background-image:url('${thumb(b.thumbnail)}')">
        ${b.is_hot == 1
            ? '<span class="badge-hot">🔥 HOT</span>'
            : `<span class="badge-cat">${esc(b.chuyen_muc)}</span>`}
      </div>
      <div class="news-body">
        <div class="news-title">${esc(b.tieu_de)}</div>
        <div class="news-excerpt">${esc(b.tom_tat || '')}</div>
      </div>
      <div class="news-footer">
        <span>✍️ ${esc(b.tac_gia_ten)}</span>
        <span>📅 ${fmtDate(b.created_at)} · 👁️ ${b.luot_xem}</span>
      </div>
    </div>`;
}

// ─── Chi tiết bài viết (SV) ──────────────────
async function loadDetail(id) {
    setLoading();
    showScreen('detail');

    const r = await api(`${API}?id=${id}`);
    if (!r.success) return setError(r.message);

    const b    = r.data;
    const rels = b.lien_quan ?? [];

    content().innerHTML = `
    <div class="article-wrap">
      <div class="article-meta">
        <span style="background:var(--primary);color:#fff;padding:3px 10px;border-radius:20px;font-weight:700;font-size:.78rem;text-transform:uppercase">${esc(b.chuyen_muc)}</span>
        <span>📅 ${fmtDate(b.created_at)}</span>
        <span>👁️ ${b.luot_xem} lượt xem</span>
        <span>✍️ ${esc(b.tac_gia_ten)}</span>
      </div>

      <h1 class="article-title">${esc(b.tieu_de)}</h1>

      ${b.tom_tat ? `<p style="font-size:1.1rem;color:var(--gray-600);font-style:italic;border-left:4px solid var(--primary);padding-left:14px;margin-bottom:24px;">${esc(b.tom_tat)}</p>` : ''}

      ${b.thumbnail ? `<img src="public/uploads/${esc(b.thumbnail)}" class="article-cover" alt="">` : ''}

      <div class="article-body">${b.noi_dung}</div>

      ${rels.length ? `
      <div style="margin-top:48px;padding-top:24px;border-top:1px solid var(--gray-200);">
        <h3 style="font-size:1.2rem;font-weight:700;margin-bottom:16px;">Bài Viết Liên Quan</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
          ${rels.map(rel => `
          <div class="news-card" onclick="loadDetail(${rel.id})" style="cursor:pointer;">
            <div class="news-cover" style="background-image:url('${thumb(rel.thumbnail)}');height:130px;"></div>
            <div class="news-body" style="padding:12px;">
              <div class="news-title" style="font-size:.9rem;">${esc(rel.tieu_de)}</div>
              <div style="font-size:.78rem;color:var(--gray-500);margin-top:4px;">${fmtDate(rel.created_at)}</div>
            </div>
          </div>`).join('')}
        </div>
      </div>` : ''}
    </div>`;
}

// ─── Điều hướng màn hình (SV) ────────────────
function showScreen(screen) {
    currentScreen = screen;
    const btnBack   = document.getElementById('btn-back');
    const filterBar = document.getElementById('filter-bar');
    if (screen === 'detail') {
        btnBack.style.display   = '';
        filterBar && (filterBar.style.display = 'none');
    } else {
        btnBack.style.display   = 'none';
        filterBar && (filterBar.style.display = '');
    }
}

function goBack() {
    loadNewsFeed();
}

// ─── Lọc chuyên mục ──────────────────────────
function filterCat(btn) {
    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentCat = btn.dataset.cat;
    loadNewsFeed();
}

function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(loadNewsFeed, 350);
}

async function loadCategories() {
    const r = await api(`${API}?categories=1`);
    if (!r.success) return;
    const bar   = document.getElementById('filter-bar');
    const exist = [...bar.querySelectorAll('.cat-btn')].map(b => b.dataset.cat);
    r.data.forEach(cat => {
        if (exist.includes(cat)) return;
        const btn    = document.createElement('button');
        btn.className   = 'cat-btn';
        btn.dataset.cat = cat;
        btn.textContent = cat;
        btn.onclick     = () => filterCat(btn);
        bar.insertBefore(btn, bar.querySelector('div')); // trước ô tìm kiếm
    });
}

// ─── FORM THÊM/SỬA (GV) ─────────────────────
function openForm(article) {
    const isEdit = article !== null;
    document.getElementById('modal-title').textContent = isEdit ? 'Sửa Bài Viết' : 'Bài Viết Mới';
    document.getElementById('edit-id').value           = isEdit ? article.id : '';
    document.getElementById('f-tieu_de').value         = isEdit ? article.tieu_de     : '';
    document.getElementById('f-tom_tat').value         = isEdit ? article.tom_tat     : '';
    document.getElementById('f-noi_dung').value        = isEdit ? article.noi_dung    : '';
    document.getElementById('f-chuyen_muc').value      = isEdit ? article.chuyen_muc  : 'Tin tuc';
    document.getElementById('f-trang_thai').value      = isEdit ? article.trang_thai  : 'Draft';
    document.getElementById('f-is_hot').checked        = isEdit && article.is_hot == 1;
    document.getElementById('f-thumbnail').value       = '';
    document.getElementById('form-error').style.display = 'none';
    document.getElementById('thumb-preview').innerHTML =
        (isEdit && article.thumbnail)
            ? `<img src="public/uploads/${esc(article.thumbnail)}" style="max-width:100%;max-height:120px;border-radius:8px;">`
            : '';
    document.getElementById('bv-modal-bg').classList.add('open');
}

function closeForm() {
    document.getElementById('bv-modal-bg').classList.remove('open');
}

async function saveArticle() {
    const id     = document.getElementById('edit-id').value;
    const isEdit = Boolean(id);
    const errEl  = document.getElementById('form-error');
    errEl.style.display = 'none';

    const tieu_de = document.getElementById('f-tieu_de').value.trim();
    const noi_dung = document.getElementById('f-noi_dung').value.trim();
    if (!tieu_de || !noi_dung) {
        errEl.textContent    = 'Tiêu đề và Nội dung không được để trống.';
        errEl.style.display  = '';
        return;
    }

    // Dùng FormData để upload được ảnh
    const fd = new FormData();
    fd.append('tieu_de',    tieu_de);
    fd.append('tom_tat',    document.getElementById('f-tom_tat').value.trim());
    fd.append('noi_dung',   noi_dung);
    fd.append('chuyen_muc', document.getElementById('f-chuyen_muc').value);
    fd.append('trang_thai', document.getElementById('f-trang_thai').value);
    fd.append('tac_gia_id', <?= $_SESSION['user_id'] ?? 1 ?>);
    if (document.getElementById('f-is_hot').checked) fd.append('is_hot', '1');
    const file = document.getElementById('f-thumbnail').files[0];
    if (file) fd.append('thumbnail', file);

    const url    = isEdit ? `${API}?id=${id}&_method=PUT` : API;
    const method = 'POST'; // Bắt buộc dùng POST vì FormData upload file không hoạt động qua PUT trong PHP

    document.getElementById('btn-save').disabled = true;
    const r = await fetch(url, { method, body: fd })
                        .then(res => res.json())
                        .catch(() => ({ success: false, message: 'Lỗi kết nối.' }));
    document.getElementById('btn-save').disabled = false;

    if (!r.success) {
        errEl.textContent   = r.message;
        errEl.style.display = '';
        return;
    }
    closeForm();
    loadGvList();
}

async function deleteArticle(id) {
    if (!confirm('Bạn có chắc muốn xóa bài viết này?')) return;
    const r = await api(`${API}?id=${id}`, { method: 'DELETE' });
    if (r.success) loadGvList();
    else alert(r.message);
}

// Đóng modal khi click nền
document.getElementById('bv-modal-bg')?.addEventListener('click', function(e) {
    if (e.target === this) closeForm();
});
</script>
