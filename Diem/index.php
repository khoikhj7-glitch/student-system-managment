<div class="d-flex justify-between align-items-center mb-3">
    <h2>📊 QUẢN LÝ ĐIỂM THEO MÔN</h2>
    <a href="index.php?f=Diem&action=form" class="btn btn-primary">➕ Thêm điểm</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php 
            if($_GET['msg']=='success') echo "Thêm điểm thành công!";
            elseif($_GET['msg']=='updated') echo " Cập nhật điểm thành công!";
            elseif($_GET['msg']=='delete') echo " Đã xóa thành công";
        ?>
    </div>
<?php endif; ?>

<?php foreach($monhocs as $mh): ?>
    <h3 style="background:#e9ecef; padding:10px; border-radius: 4px; border-left: 4px solid var(--primary-color);">
    <?= htmlspecialchars($mh['ten_mh']) ?>
    </h3>

    <div style="overflow-x: auto; margin-bottom: 30px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mã SV</th>
                    <th>Tên SV</th>
                    <th>TP (10%)</th>
                    <th>GK (30%)</th>
                    <th>CK (60%)</th>
                    <th>Tổng</th>
                    <th>Chữ</th>
                    <th>GPA</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $diems = $scoresBySubject[$mh['id']];
                if (empty($diems)): ?>
                    <tr><td colspan="9" class="text-center">Chưa có bảng điểm</td></tr>
                <?php else:
                    foreach($diems as $row): 
                        $tp = $row['diem_tp'] ?? 0;
                        $gk = $row['diem_gk'] ?? 0;
                        $ck = $row['diem_ck'] ?? 0;

                        $tong = ($tp*10 + $gk*30 + $ck*60)/100;
                        list($chu, $gpa) = Diem::xepLoai($tong);
                ?>
                <tr>
                    <form method="POST" action="index.php?f=Diem&action=index">
                        <td><?= htmlspecialchars($row['ma_sv']) ?></td>
                        <td><?= htmlspecialchars($row['ho_ten']) ?></td>
                        <td><input type="number" step="0.1" name="tp" value="<?= $tp ?>" class="form-control" style="width:70px; padding:4px;"></td>
                        <td><input type="number" step="0.1" name="gk" value="<?= $gk ?>" class="form-control" style="width:70px; padding:4px;"></td>
                        <td><input type="number" step="0.1" name="ck" value="<?= $ck ?>" class="form-control" style="width:70px; padding:4px;"></td>
                        <td><b><?= number_format($tong,1) ?></b></td>
                        <td style="color:<?= ($chu=='F') ? 'red' : ($chu=='A' ? 'green' : 'black') ?>"><b><?= $chu ?></b></td>
                        <td><b><?= $gpa ?></b></td>
                        <td>
                            <div class="action-group">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" name="update" class="btn btn-secondary btn-sm">Sửa</button>
                                <a href="index.php?f=Diem&action=delete&id=<?= $row['id'] ?>" 
                                onclick="return confirm('Bạn có chắc chắn muốn XÓA VĨNH VIỄN điểm của học sinh này không?');" 
                                class="btn btn-danger btn-sm">Xóa</a>                            
                            </div>
                        </td>
                    </form>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
<?php endforeach; ?>
