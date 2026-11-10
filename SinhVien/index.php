<style>
    .btn-add { background-color: #00a65a; color: white; border: none; }
    .action-group-sv { display: flex; gap: 4px; justify-content: center; min-width: 110px; }
    .action-btn { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; color: white !important; border-radius: 4px; text-decoration: none; border: none; cursor: pointer;}
    .bg-view { background-color: #00c0ef; } 
    .bg-edit { background-color: #3c8dbc; } 
    .bg-delete { background-color: #dc3545; }
    .badge-nn { background-color: #e67e22; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; }
    .filter-box { background: white; padding: 15px; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 20px;}
    .form-control, .form-select { border: 1px solid #ced4da; border-radius: 4px; padding: 6px; }
</style>

<div class="d-flex justify-between align-items-center mb-3">
    <h2 class="m-0 text-uppercase">Danh sách Sinh viên</h2>
    <button class="btn btn-primary" onclick="openAddModal()">➕ Thêm mới</button>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php 
            if($_GET['msg'] == 'saved') echo "✅ Đã lưu sinh viên thành công!";
            if($_GET['msg'] == 'deleted') echo "🗑️ Đã xóa sinh viên!";
            if($_GET['msg'] == 'imported') echo "✅ Đã nạp thành công dữ liệu từ file CSV!";
        ?>
    </div>
<?php endif; ?>

<!-- TÌM KIẾM -->
<div class="filter-box">
    <form method="GET" action="index.php">
        <input type="hidden" name="f" value="SinhVien">
        <input type="hidden" name="action" value="index">
        
        <div style="display:flex; justify-content: space-between; margin-bottom: 15px;">
            <div style="display:flex; gap:10px;">
                <input type="date" name="ngay_sinh" class="form-control" value="<?= htmlspecialchars($filters['ngay_sinh']) ?>">
                <select name="khoa_id" class="form-select">
                    <option value="">Chọn khoa</option>
                    <?php foreach($list_khoas as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $filters['khoa_id'] == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['ten_khoa']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex; gap:10px;">
                <a href="index.php?f=SinhVien&action=export" class="btn btn-success" style="background:#198754; color:white; border:none;">📥 Export Excel</a>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('file_csv').click()">
                    📤 Import CSV
                </button>
            </div>
        </div>
        
        <div style="display:flex; gap:10px; align-items:center;">
            <input type="text" name="ma_sv" class="form-control" placeholder="Mã NV/SV..." value="<?= htmlspecialchars($filters['ma_sv']) ?>">
            <input type="text" name="ho_ten" class="form-control" placeholder="Tên sinh viên..." value="<?= htmlspecialchars($filters['ho_ten']) ?>" style="flex:1;">
            <select name="lop_id" class="form-select">
                <option value="">Chọn lớp</option>
                <?php foreach($list_lops as $l): ?>
                    <option value="<?= $l['id'] ?>" <?= $filters['lop_id'] == $l['id'] ? 'selected' : '' ?>><?= htmlspecialchars($l['ten_lop']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 6.5px 15px;">Tìm kiếm</button>
            <a href="index.php?f=SinhVien&action=index" class="btn btn-secondary" style="padding: 6.5px 15px;">Reset</a>
        </div>
    </form>
    <form action="index.php?f=SinhVien&action=import" method="POST" enctype="multipart/form-data" id="importForm" style="display:none;">
        <input type="file" name="file_csv" id="file_csv" accept=".csv" onchange="document.getElementById('importForm').submit()">
    </form>
</div>

<!-- BẢNG BIỂU -->
<div style="overflow-x: auto;">
    <table class="data-table" style="text-align: center;">
        <thead>
            <tr>
                <th>STT</th><th>Mã SV</th><th style="text-align: left;">Họ tên</th><th>Ngày sinh</th><th>Giới tính</th><th>Lớp</th><th>Khoa</th><th>Nguồn gốc</th><th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php $stt = 1; foreach($rows as $r): ?>
            <tr>
                <td><?= $stt++ ?></td>
                <td><strong><?= htmlspecialchars($r['ma_sv']) ?></strong></td>
                <td style="text-align: left;"><?= htmlspecialchars($r['ho_ten']) ?></td>
                <td><?= date('d-m-Y', strtotime($r['ngay_sinh'])) ?></td>
                <td><?= ($r['gioi_tinh'] == 'Nu') ? 'Nữ' : 'Nam' ?></td>
                <td><?= htmlspecialchars($r['ten_lop']) ?></td>
                <td><?= htmlspecialchars($r['ten_khoa']) ?></td>
                <td>
                    <?= (isset($r['sv_nuoc_ngoai']) && $r['sv_nuoc_ngoai']) ? '<span class="badge-nn">Nước ngoài</span>' : 'Trong nước' ?>
                </td>
                <td>
                    <div class="action-group-sv">
                        <!-- Nút kích hoạt trigger Bootstrap Modal via JS obj -->
                        <button type="button" onclick='openViewModal(<?= json_encode($r) ?>)' class="action-btn bg-view">Xem</button>
                        <button type="button" onclick='openEditModal(<?= json_encode($r) ?>)' class="action-btn bg-edit">Sửa</button>
                        <a href="index.php?f=SinhVien&action=delete&id=<?= $r['id'] ?>" class="action-btn bg-delete" onclick="return confirmDelete()">Xóa</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; if(empty($rows)) echo "<tr><td colspan='9'>Không có dữ liệu!</td></tr>"; ?>
        </tbody>
    </table>
</div>

<!-- MODAL THÊM / SỬA -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="studentForm" action="index.php?f=SinhVien&action=index" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="modalTitle">Thông tin sinh viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="stu_id">
                
                <div style="display:flex; gap:15px; margin-bottom:15px;">
                    <div style="flex:1;">
                        <label>Mã sinh viên</label>
                        <input type="text" name="ma_sv" id="ma_sv" class="form-control" required style="width:100%">
                    </div>
                    <div style="flex:1;">
                        <label>Họ tên sinh viên</label>
                        <input type="text" name="ho_ten" id="ho_ten" class="form-control" required style="width:100%">
                    </div>
                </div>

                <div style="display:flex; gap:15px; margin-bottom:15px;">
                    <div style="flex:1;">
                        <label>Ngày sinh</label>
                        <input type="date" name="ngay_sinh" id="ngay_sinh" class="form-control" required style="width:100%">
                    </div>
                    <div style="flex:1;">
                        <label>Lớp học</label>
                        <select name="lop_id" id="lop_id" class="form-select" style="width:100%">
                            <?php foreach($list_lops as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['ten_lop']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="display:flex; gap:15px;">
                    <div style="flex:1;">
                        <label>Giới tính</label>
                        <select name="gioi_tinh" id="gioi_tinh" class="form-select" style="width:100%">
                            <option value="Nam">Nam</option>
                            <option value="Nu">Nữ</option>
                        </select>
                    </div>
                    <div style="flex:1; display:flex; align-items:flex-end;">
                        <label style="display:flex; align-items:center; gap:8px;">
                            <input type="checkbox" name="sv_nuoc_ngoai" id="nn" style="width:20px; height:20px;">
                            Sinh viên người nước ngoài
                        </label>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" name="save_student" id="btnSubmit" class="btn btn-primary">Lưu lại</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </form>
    </div>
</div>

<script>
    var studentModal = new bootstrap.Modal(document.getElementById('studentModal'));
    const formSv = document.getElementById('studentForm');

    function openAddModal() {
        formSv.reset();
        document.getElementById('stu_id').value = '';
        document.getElementById('modalTitle').innerText = 'Thêm mới sinh viên';
        enableForm(true);
        studentModal.show();
    }

    function openEditModal(data) {
        fillForm(data);
        document.getElementById('modalTitle').innerText = 'Cập nhật Sinh Viên';
        enableForm(true);
        studentModal.show();
    }

    function openViewModal(data) {
        fillForm(data);
        document.getElementById('modalTitle').innerText = 'Chi Tiết (Chỉ Xem)';
        enableForm(false); 
        studentModal.show();
    }

    function fillForm(data) {
        document.getElementById('stu_id').value = data.id;
        document.getElementById('ma_sv').value = data.ma_sv;
        document.getElementById('ho_ten').value = data.ho_ten;
        document.getElementById('ngay_sinh').value = data.ngay_sinh;
        document.getElementById('lop_id').value = data.lop_id;
        document.getElementById('gioi_tinh').value = data.gioi_tinh;
        document.getElementById('nn').checked = (parseInt(data.sv_nuoc_ngoai) === 1);
    }

    function enableForm(status) {
        const inputs = formSv.querySelectorAll('input, select');
        inputs.forEach(input => { if (input.id !== 'stu_id') input.disabled = !status; });
        document.getElementById('btnSubmit').style.display = status ? 'block' : 'none';
    }
</script>
