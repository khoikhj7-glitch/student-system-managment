<div class="d-flex justify-between align-items-center mb-3">
    <h2 class="m-0 text-uppercase">Danh sách giảng viên</h2>
    <button class="btn btn-primary" onclick="resetFormGV()">➕ Thêm mới</button>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php 
            if($_GET['msg'] == 'saved') echo "✅ Đã lưu Giảng viên thành công!";
            if($_GET['msg'] == 'deleted') echo "🗑️ Đã xóa thông tin Giảng viên!";
        ?>
    </div>
<?php endif; ?>

<!-- TÌM KIẾM -->
<div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 20px;">
    <form method="GET" action="index.php" style="display:flex; gap:10px; align-items:center;">
        <input type="hidden" name="f" value="GiangVien">
        <input type="hidden" name="action" value="index">
        <input type="text" name="search_name" class="form-control" style="width: 300px; padding: 6px;" placeholder="Tìm tên hoặc mã GV..." value="<?= htmlspecialchars($search_name) ?>">
        <button type="submit" class="btn btn-primary" style="padding: 6.5px 15px;">Tìm kiếm</button>
        <a href="index.php?f=GiangVien&action=index" class="btn btn-secondary" style="padding: 6.5px 15px;">Reset</a>
    </form>
</div>

<!-- DATA TABLE -->
<div style="overflow-x: auto;">
    <table class="data-table" style="text-align: center;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Mã giảng viên</th>
                <th style="text-align: left;">Họ tên</th>
                <th>Trình độ</th>
                <th>Khoa</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($rows as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><strong><?= htmlspecialchars($r['ma_gv']) ?></strong></td>
                <td style="text-align: left;"><?= htmlspecialchars($r['ho_ten']) ?></td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($r['trinh_do']) ?></span></td>
                <td><?= htmlspecialchars($r['ten_khoa']) ?></td>
                <td>
                    <button type="button" class="btn btn-secondary btn-sm" onclick='editTeacher(<?= json_encode($r) ?>)'>Sửa</button>
                    <a href="index.php?f=GiangVien&action=delete&id=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete()">Xóa</a>
                </td>
            </tr>
            <?php endforeach; if(empty($rows)) echo "<tr><td colspan='6'>Không có dữ liệu!</td></tr>"; ?>
        </tbody>
    </table>
</div>

<!-- GIANG VIEN MODAL -->
<div class="modal fade" id="teacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="index.php?f=GiangVien&action=index" class="modal-content" id="teacherForm">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="modalTitleGV">THÊM MỚI GIẢNG VIÊN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="teacher_id">
                
                <div class="form-group mb-3">
                    <label>Mã giảng viên</label>
                    <input type="text" name="ma_gv" id="ma_gv" class="form-control mt-1" style="width:100%; padding: 8px;" required>
                </div>
                
                <div class="form-group mb-3">
                    <label>Họ tên</label>
                    <input type="text" name="ho_ten" id="ho_ten_gv" class="form-control mt-1" style="width:100%; padding: 8px;" required>
                </div>

                <div class="form-group mb-3">
                    <label>Trình độ</label>
                    <select name="trinh_do" id="trinh_do" class="form-select mt-1" style="width:100%; padding: 8px;">
                        <option value="Cu nhan">Cử nhân</option>
                        <option value="Thac si">Thạc sĩ</option>
                        <option value="Tien si">Tiến sĩ</option>
                        <option value="Giao su">Giáo sư</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>Khoa trực thuộc</label>
                    <select name="khoa_id" id="khoa_id_gv" class="form-select mt-1" style="width:100%; padding: 8px;" required>
                        <?php foreach($list_khoas as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['ten_khoa']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="save_teacher" class="btn btn-primary" style="padding: 8px 20px;">Lưu dữ liệu</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 8px 20px;">Đóng</button>
            </div>
        </form>
    </div>
</div>

<script>
    var teacherModalObj = new bootstrap.Modal(document.getElementById('teacherModal'));

    function resetFormGV() {
        document.getElementById('teacherForm').reset();
        document.getElementById('teacher_id').value = '';
        document.getElementById('modalTitleGV').innerText = 'THÊM MỚI GIẢNG VIÊN';
        teacherModalObj.show();
    }

    function editTeacher(data) {
        document.getElementById('modalTitleGV').innerText = 'CẬP NHẬT GIẢNG VIÊN';
        document.getElementById('teacher_id').value = data.id;
        document.getElementById('ma_gv').value = data.ma_gv;
        document.getElementById('ho_ten_gv').value = data.ho_ten;
        document.getElementById('trinh_do').value = data.trinh_do;
        document.getElementById('khoa_id_gv').value = data.khoa_id;
        teacherModalObj.show();
    }
</script>
