<div class="mb-3 d-flex justify-between align-items-center">
    <h2>➕ THÊM ĐIỂM SINH VIÊN</h2>
    <a href="index.php?f=Diem&action=index" class="btn btn-secondary">← Quay lại</a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div style="background: white; padding: 20px; border-radius: 8px; max-width: 600px; border: 1px solid var(--border-color);">
    <form method="POST" action="index.php?f=Diem&action=form">
        <div class="form-group">
            <label class="form-label">Chọn Sinh viên:</label>
            <select name="sv" required class="form-control">
                <?php foreach($sinhviens as $sv): ?>
                    <option value="<?= $sv['id'] ?>">
                        <?= htmlspecialchars($sv['ma_sv']) ?> - <?= htmlspecialchars($sv['ho_ten']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Chọn Môn học:</label>
            <select name="mh" required class="form-control">
                <?php foreach($monhocs as $mh): ?>
                    <option value="<?= $mh['id'] ?>">
                        <?= htmlspecialchars($mh['ten_mh']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row" style="display: flex; gap: 15px;">
            <div class="form-group" style="flex:1;">
                <label class="form-label">Điểm TP (10%):</label>
                <input type="number" step="0.1" name="tp" required class="form-control">
            </div>

            <div class="form-group" style="flex:1;">
                <label class="form-label">Điểm GK (30%):</label>
                <input type="number" step="0.1" name="gk" required class="form-control">
            </div>

            <div class="form-group" style="flex:1;">
                <label class="form-label">Điểm CK (60%):</label>
                <input type="number" step="0.1" name="ck" required class="form-control">
            </div>
        </div>

        <button type="submit" name="save" class="btn btn-primary" style="width: 100%;">💾 Lưu Điểm</button>
    </form>
</div>
