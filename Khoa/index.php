<style>
    .khoa-row td { vertical-align: middle; }
    .text-link { color: #0d6efd; cursor: pointer; text-decoration: underline; text-decoration-color: #0d6efd;}
    .text-link:hover { opacity: 0.8;}
</style>

<div class="d-flex justify-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold m-0 text-uppercase">Danh mục Khoa</h2>
        <small class="text-muted"></small>
    </div>
    
    <div class="d-flex gap-3 align-items-center">
        <input type="text" id="searchKhoa" class="form-control" style="width: 250px;" placeholder="🔍 Tìm mã hoặc tên khoa..." onkeyup="filterKhoaRealtime()">
        <button class="btn btn-primary" onclick="resetFormKhoa()">➕ Thêm Khoa</button>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success mt-2 mb-3">
        <?php 
            if($_GET['msg'] == 'saved') echo "✅ Đã lưu Khoa thành công!";
            if($_GET['msg'] == 'deleted') echo "🗑️ Đã xóa Khoa an toàn!";
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger mt-2 mb-3">
        ⚠️ <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<div class="table-responsive bg-white shadow-sm border" style="border-radius: 8px;">
    <table class="data-table" id="tableKhoa" style="text-align: center;">
        <thead class="bg-light">
            <tr>
                <th>Mã Khoa</th>
                <th style="text-align: left;">Tên Khoa</th>
                <th>Nhân sự</th> 
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
            <tr class="khoa-row">
                <td><span class="fw-bold text-primary"><?php echo htmlspecialchars($r['ma_khoa']); ?></span></td>
                <td style="text-align: left;" class="text-link" onclick="viewGiangVien(<?php echo $r['id']; ?>, '<?php echo htmlspecialchars($r['ten_khoa'], ENT_QUOTES); ?>')">
                    <?php echo htmlspecialchars($r['ten_khoa']); ?>
                </td>
                <td>
                    <span class="badge bg-secondary"><?php echo $r['tong_gv']; ?> Giảng viên</span>
                </td>
                <td>
                    <button class="btn btn-secondary btn-sm" onclick='editKhoa(<?php echo json_encode($r); ?>)'>Sửa</button>
                    <a href="index.php?f=Khoa&action=delete&id=<?php echo $r['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xác nhận xóa khoa này?')">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Thêm Khoa -->
<div class="modal fade" id="modalKhoa" tabindex="-1">
    <div class="modal-dialog">
        <form action="index.php?f=Khoa&action=index" method="POST" class="modal-content" style="border-radius: 12px; overflow:hidden;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="titleKhoa">Thêm Khoa Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id" id="k_id">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase">Mã Khoa</label>
                    <input type="text" name="ma_khoa" id="k_ma" class="form-control" placeholder="VD: CNTT" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase">Tên Khoa</label>
                    <input type="text" name="ten_khoa" id="k_ten" class="form-control" placeholder="VD: Công nghệ thông tin" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="save_khoa" class="btn btn-primary w-100">Lưu Nội Dung</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Xem Chi Tiết API HTML -->
<div class="modal fade" id="modalViewDetails" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; overflow:hidden;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="detailTitle">Chi tiết giảng viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detailBody"></div>
        </div>
    </div>
</div>

<script>
function filterKhoaRealtime() {
    let input = document.getElementById("searchKhoa").value.toLowerCase();
    let rows = document.querySelectorAll(".khoa-row");
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}

function viewGiangVien(id, tenKhoa) {
    document.getElementById('detailTitle').innerText = "Danh sách Giảng viên - Khoa " + tenKhoa;
    document.getElementById('detailBody').innerHTML = `<div class="text-center py-5">Đang nạp dữ liệu...</div>`;
    
    new bootstrap.Modal(document.getElementById('modalViewDetails')).show();

    fetch('index.php?f=Khoa&action=apiList&id=' + id)
        .then(response => response.text())
        .then(html => document.getElementById('detailBody').innerHTML = html)
        .catch(err => document.getElementById('detailBody').innerHTML = '<div class="alert alert-danger">Lỗi kết nối CSDL</div>');
}

function editKhoa(d) {
    document.getElementById('titleKhoa').innerText = "Cập nhật Khoa";
    document.getElementById('k_id').value = d.id;
    document.getElementById('k_ma').value = d.ma_khoa;
    document.getElementById('k_ten').value = d.ten_khoa;
    new bootstrap.Modal(document.getElementById('modalKhoa')).show();
}

function resetFormKhoa() {
    document.getElementById('titleKhoa').innerText = "Thêm Khoa Mới";
    document.getElementById('k_id').value = "";
    document.getElementById('k_ma').value = "";
    document.getElementById('k_ten').value = "";
    new bootstrap.Modal(document.getElementById('modalKhoa')).show();
}
</script>
