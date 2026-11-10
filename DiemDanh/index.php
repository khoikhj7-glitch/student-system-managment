<div class="mb-3">
    <h2>📋 QUẢN LÝ ĐIỂM DANH</h2>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success">✅ <?= $success ?></div>
<?php endif; ?>

<style>
.section-header {
    background: var(--primary);
    color: white; 
    padding: 12px 15px;
    cursor: pointer;
    border-radius: 4px;
    margin-bottom: 10px;
    font-weight: 600;
}
.section-header:hover {
    opacity: 0.9;
}
.section-content {
    display: none; 
    padding: 15px;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 4px;
    margin-bottom: 20px;
}
.red {color: #dc3545; font-weight: bold;}
.green {color: #198754; font-weight: bold;}
</style>

<script>
function toggle(id){
    let el = document.getElementById(id);
    el.style.display = (el.style.display == "none" || el.style.display == "") ? "block" : "none";
}

function updateVang(id, base){
    let radios = document.getElementsByName('vang['+id+']');
    let vang = 0;
    for (let i = 0; i < radios.length; i++) {
        if (radios[i].checked) {
            vang = parseInt(radios[i].value);
            break;
        }
    }
    
    let count = parseInt(base);

    if(vang === 1){
        count = count + 1;
    }

    document.getElementById("vang_"+id).innerText = count;

    if(count > 3){
        document.getElementById("status_"+id).innerHTML = "<span class='red'>CẤM THI</span>";
    } else {
        document.getElementById("status_"+id).innerHTML = "<span class='green'>Đủ điều kiện</span>";
    }
}
</script>

<?php $i=0; foreach($monhocs as $mh): $i++; ?>

    <div class="section-header" onclick="toggle('content<?= $i ?>')">
        📘 <?= htmlspecialchars($mh['ten_mh']) ?> (Nhấn để điểm danh)
    </div>

    <div class="section-content" id="content<?= $i ?>">
        <form method="POST" action="index.php?f=DiemDanh&action=index">
            <input type="hidden" name="mh" value="<?= $mh['id'] ?>">

            <div class="form-group" style="max-width: 300px;">
                <label>Ngày điểm danh:</label>
                <input type="date" name="ngay" required class="form-control" value="<?= date('Y-m-d') ?>">
            </div>

            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã SV</th>
                            <th>Tên SV</th>
                            <th class="text-center">Có mặt</th>
                            <th class="text-center" style="color:red;">Vắng</th>
                            <th class="text-center">Tích lũy vắng</th>
                            <th class="text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $students = $attendanceBySubject[$mh['id']];
                        foreach($students as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ma_sv']) ?></td>
                            <td><?= htmlspecialchars($row['ho_ten']) ?></td>
                            <td class="text-center">
                                <input type="radio" name="vang[<?= $row['id'] ?>]" value="0" checked onchange="updateVang(<?= $row['id'] ?>, <?= $row['so_vang'] ?>)">
                            </td>
                            <td class="text-center">
                                <input type="radio" name="vang[<?= $row['id'] ?>]" value="1" onchange="updateVang(<?= $row['id'] ?>, <?= $row['so_vang'] ?>)">
                            </td>
                            <td class="text-center" id="vang_<?= $row['id'] ?>">
                                <b><?= $row['so_vang'] ?></b>
                            </td>
                            <td class="text-center" id="status_<?= $row['id'] ?>">
                                <?php if($row['so_vang'] > 3): ?>
                                    <span class="red">CẤM THI</span>
                                <?php else: ?>
                                    <span class="green">Đủ điều kiện</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 text-right">
                <button type="submit" name="save_attendance" class="btn btn-primary">💾 Lưu Dữ Liệu</button>
            </div>
        </form>
    </div>

<?php endforeach; ?>
