<style>
    .lop-row td { vertical-align: middle; }
    .text-link { 
        color: #198754; 
        cursor: pointer; 
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .text-link:hover { 
        color: #146c43;
        text-decoration: underline !important;
    }
    #tableLop thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        font-weight: 700;
        border-bottom: 2px solid #dee2e6;
    }
    .btn-group .btn {
        padding: 0.25rem 0.75rem;
    }
    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.2);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold m-0 text-uppercase text-primary">Danh mục Lớp</h2>
        <small class="text-muted"></small>
    </div>
    
    <div class="d-flex gap-3 align-items-center">
        <div class="input-group" style="width: 300px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="searchLop" class="form-control border-start-0 ps-0" 
                   placeholder="Tìm mã hoặc tên lớp..." onkeyup="filterLopRealtime()">
        </div>
        <button class="btn btn-primary shadow-sm px-4" onclick="resetFormLop()">
            <i class="bi bi-plus-lg me-1"></i> Thêm Lớp
        </button>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?php 
            if($_GET['msg'] == 'saved') echo "Đã lưu thông tin lớp thành công!";
            if($_GET['msg'] == 'deleted') echo "Đã xóa lớp khỏi hệ thống an toàn!";
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger shadow-sm border-0 alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="tableLop" style="text-align: center;">
            <thead>
                <tr>
                    <th class="py-3">Mã Lớp</th>
                    <th class="py-3" style="text-align: left;">Tên Lớp</th>
                    <th class="py-3">Sĩ số</th> 
                    <th class="py-3">Thuộc Khoa</th>
                    <th class="py-3">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                <tr class="lop-row">
                    <td>
                        <span class="badge bg-light text-primary border fw-bold px-3 py-2">
                            <?php echo htmlspecialchars($r['ma_lop']); ?>
                        </span>
                    </td>
                    <td style="text-align: left;">
                        <div class="text-link" onclick="viewSinhVien(<?php echo $r['id']; ?>, '<?php echo htmlspecialchars($r['ten_lop'], ENT_QUOTES); ?>')">
                            <?php echo htmlspecialchars($r['ten_lop']); ?>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-soft-success px-3 py-2">
                            <?php echo $r['tong_sv']; ?> Sinh viên
                        </span>
                    </td>
                    <td class="text-secondary fw-medium"><?php echo htmlspecialchars($r['ten_khoa']); ?></td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary" 
                                    onclick='editLop(<?php echo json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                <i class="bi bi-pencil-square"></i> Sửa
                            </button>
                            <a href="index.php?f=Lop&action=delete&id=<?php echo $r['id']; ?>" 
                               class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa lớp này? Thao tác không thể hoàn tác.')">
                                <i class="bi bi-trash"></i> Xóa
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalLop" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?f=Lop&action=index" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark text-uppercase" id="titleLop">Thêm Lớp Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id" id="l_id">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Mã Lớp</label>
                    <input type="text" name="ma_lop" id="l_ma" class="form-control form-control-lg" placeholder="Ví dụ: D21CNTT01" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tên Lớp</label>
                    <input type="text" name="ten_lop" id="l_ten" class="form-control form-control-lg" placeholder="Ví dụ: Công nghệ thông tin 1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Khoa trực thuộc</label>
                    <select name="khoa_id" id="l_khoa" class="form-select form-select-lg" required>
                        <option value="">-- Chọn Khoa --</option>
                        <?php foreach($khoas as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['ten_khoa']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="submit" name="save_lop" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                    <i class="bi bi-save me-2"></i> LƯU THÔNG TIN
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalViewSV" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow:hidden;">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold" id="titleSV">Danh sách sinh viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="bodySV">
                </div>
        </div>
    </div>
</div>

<script>
function filterLopRealtime() {
    let input = document.getElementById("searchLop").value.toLowerCase();
    let rows = document.querySelectorAll(".lop-row");
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}

// 2. Xem danh sách sinh viên 
function viewSinhVien(id, tenLop) {
    document.getElementById('titleSV').innerHTML = `<i class="bi bi-people-fill me-2"></i> Lớp: ${tenLop}`;
    document.getElementById('bodySV').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted small">Đang tải danh sách sinh viên...</p>
        </div>`;
    
    new bootstrap.Modal(document.getElementById('modalViewSV')).show();

    fetch('index.php?f=Lop&action=apiList&id=' + id)
        .then(response => response.text())
        .then(html => {
            document.getElementById('bodySV').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('bodySV').innerHTML = '<div class="alert alert-danger m-3 text-center">Lỗi tải dữ liệu sinh viên!</div>';
        });
}

function editLop(d) {
    document.getElementById('titleLop').innerText = "Cập nhật thông tin Lớp";
    document.getElementById('l_id').value = d.id;
    document.getElementById('l_ma').value = d.ma_lop;
    document.getElementById('l_ten').value = d.ten_lop;
    document.getElementById('l_khoa').value = d.khoa_id;
    new bootstrap.Modal(document.getElementById('modalLop')).show();
}

function resetFormLop() {
    document.getElementById('titleLop').innerText = "Thêm Lớp Mới";
    document.getElementById('l_id').value = "";
    document.getElementById('l_ma').value = "";
    document.getElementById('l_ten').value = "";
    document.getElementById('l_khoa').value = "";
    new bootstrap.Modal(document.getElementById('modalLop')).show();
}
</script>