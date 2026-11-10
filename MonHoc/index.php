<h2> QUẢN LÝ MÔN HỌC</h2>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">Thao tác thành công!</div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
    <h3>Thêm môn học</h3>
    <form method="POST" action="index.php?f=MonHoc&action=index">
        <label>Mã môn:</label>
        <input type="text" name="ma_mh" required style="padding: 5px; margin-right: 10px;">

        <label>Tên môn:</label>
        <input type="text" name="ten_mh" required style="padding: 5px; margin-right: 10px; width: 250px;">

        <button type="submit" name="add" class="btn btn-primary" style="padding: 6px 15px;">Thêm</button>
    </form>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Mã môn</th>
            <th>Tên môn</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($monhocs as $row): ?>
        <tr>
            <form method="POST" action="index.php?f=MonHoc&action=index">
                <td><?= $row['id'] ?></td>
                <td>
                    <input type="text" name="ma_mh" value="<?= htmlspecialchars($row['ma_mh']) ?>" required style="padding: 5px;">
                </td>
                <td>
                    <input type="text" name="ten_mh" value="<?= htmlspecialchars($row['ten_mh']) ?>" required style="padding: 5px; width: 250px;">
                </td>
                <td>
                    <div class="action-group">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" name="update" class="btn btn-secondary btn-sm">💾 Lưu</button>
                        <a href="index.php?f=MonHoc&action=delete&id=<?= $row['id'] ?>" onclick="return confirmDelete();" class="btn btn-danger btn-sm">🗑️ Xóa</a>
                    </div>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($monhocs)): ?>
            <tr><td colspan="4" class="text-center">Chưa có môn học nào!</td></tr>
        <?php endif; ?>
    </tbody>
</table>
