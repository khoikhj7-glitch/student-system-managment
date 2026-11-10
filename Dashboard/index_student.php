<?php if (!$sv): ?>
    <div class="alert alert-danger">
        Không tìm thấy thông tin sinh viên phù hợp. Hệ thống có thế chưa được thiết lập dữ liệu cá nhân cho bạn.
    </div>
<?php else: ?>

    <div class="mb-4 d-flex justify-content-between align-items-center" style="border-bottom: 2px solid #eee; padding-bottom: 15px;">
        <div>
            <h2 style="margin-bottom: 5px;">Hồ Sơ & Tổng Quan Học Tập</h2>
            <p class="text-muted">Chào mừng trở lại, <strong><?= htmlspecialchars($sv['ho_ten']) ?></strong></p>
        </div>
        <span class="badge bg-primary" style="padding: 10px 15px; font-size: 1rem;">MSSV: <?= htmlspecialchars($sv['ma_sv']) ?></span>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div>
                    <small class="text-muted d-block">Ngày sinh:</small>
                    <strong><?= !empty($sv['ngay_sinh']) ? date('d/m/Y', strtotime($sv['ngay_sinh'])) : 'Chưa cập nhật' ?></strong>
                </div>
                <div>
                    <small class="text-muted d-block">Giới tính:</small>
                    <strong><?= htmlspecialchars($sv['gioi_tinh']) ?></strong>
                </div>
                <div>
                    <small class="text-muted d-block">Nguồn gốc:</small>
                    <span class="badge <?= $sv['sv_nuoc_ngoai'] ? 'bg-info' : 'bg-secondary' ?>">
                        <?= $sv['sv_nuoc_ngoai'] ? 'Sinh viên Quốc tế' : 'Sinh viên trong nước' ?>
                    </span>
                </div>
                <div>
                    <small class="text-muted d-block">Lớp:</small>
                    <strong><?= htmlspecialchars($sv['ten_lop']) ?></strong>
                </div>
            </div>
        </div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-title">Điểm Trung Bình (GPA)</div>
            <div class="kpi-value text-warning"><?= number_format($sv['gpa'], 2) ?></div>
        </div>
        <div class="kpi-card" style="background:#10b981;">
            <div class="kpi-title">Môn Học Tích Lũy</div>
            <div class="kpi-value"><?= $sv['tong_mon'] ?></div>
        </div>
        <div class="kpi-card" style="background:#8b5cf6;">
            <div class="kpi-title">Khoa / Ngành</div>
            <div class="kpi-value" style="font-size:1.2rem;"><?= htmlspecialchars($sv['ten_khoa']) ?></div>
        </div>
    </div>

    <div style="display:flex; gap: 24px; flex-wrap: wrap; margin-top: 24px;">
        
        <div class="card" style="flex: 2; min-width: 400px;">
            <div class="card-header">
                <h3>Kết Quả Học Tập (Bảng Điểm)</h3>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Môn Học</th>
                                <th>Quá Trình</th>
                                <th>Giữa Kỳ</th>
                                <th>Cuối Kỳ</th>
                                <th>Trung Bình</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($diems)): ?>
                                <tr><td colspan="5" class="text-center text-muted">Chưa có dữ liệu điểm.</td></tr>
                            <?php else: ?>
                                <?php foreach($diems as $d): 
                                    $tb = round(($d['diem_tp'] + $d['diem_gk'] + $d['diem_ck']) / 3, 2);
                                ?>
                                <tr>
                                    <td style="font-weight: 500;"><?= htmlspecialchars($d['ten_mon']) ?></td>
                                    <td><?= $d['diem_tp'] ?></td>
                                    <td><?= $d['diem_gk'] ?></td>
                                    <td><?= $d['diem_ck'] ?></td>
                                    <td><span class="badge <?= $tb >= 5 ? 'bg-success' : 'bg-danger' ?>"><?= $tb ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card" style="flex: 1; min-width: 300px;">
            <div class="card-header">
                <h3>Lịch Sử Điểm Danh</h3>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Môn Học</th>
                                <th>Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($diemdanh)): ?>
                                <tr><td colspan="3" class="text-center text-muted">Không có lịch sử.</td></tr>
                            <?php else: ?>
                                <?php foreach($diemdanh as $dd): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($dd['ngay_diem_danh'])) ?></td>
                                    <td style="font-size:0.9rem;"><?= htmlspecialchars($dd['ten_mon']) ?></td>
                                    <td>
                                        <?php if($dd['trang_thai_vang']): ?>
                                            <span class="badge bg-danger">Vắng</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Có mặt</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>