<div class="mb-3 d-flex justify-between align-items-center">
    <h2><?php echo $isEdit ? "Cập Nhật Bài Viết" : "Thêm Bài Viết Mới"; ?></h2>
    <a href="index.php?f=BaiViet&action=index" class="btn btn-secondary">← Trở về Danh sách</a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Lưu ý: enctype="multipart/form-data" để cho phép upload file ảnh -->
<form action="index.php?f=BaiViet&action=form<?php echo $isEdit ? '&id='.$this->model->id : ''; ?>" method="POST" enctype="multipart/form-data">
    
    <div class="form-row" style="display: flex; gap: 20px;">
        <!-- Cột Trái -->
        <div style="flex: 2;">
            <div class="form-group">
                <label for="tieu_de" class="form-label">Tiêu đề bài viết <span style="color:red">*</span></label>
                <input type="text" id="tieu_de" name="tieu_de" class="form-control" required placeholder="Nhập tiêu đề..." value="<?php echo $isEdit ? htmlspecialchars($this->model->tieu_de) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="tom_tat" class="form-label">Tóm tắt ngắn (Description)</label>
                <textarea id="tom_tat" name="tom_tat" class="form-control" rows="3" placeholder="Mô tả nội dung bài viết trong khoảng 150 chữ..."><?php echo $isEdit ? htmlspecialchars($this->model->tom_tat) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="noi_dung" class="form-label">Nội dung chi tiết <span style="color:red">*</span></label>
                <textarea id="noi_dung" name="noi_dung" class="form-control" required><?php echo $isEdit ? htmlspecialchars($this->model->noi_dung) : ''; ?></textarea>
            </div>
        </div>

        <!-- Cột Phải (Sidebar cài đặt) -->
        <div style="flex: 1; background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #dee2e6;">
            <h4>Thông tin hiển thị</h4>
            <hr style="margin: 15px 0; border: none; border-top: 1px solid #dee2e6;" />

            <div class="form-group">
                <label class="form-label">Chọn Trạng Thái</label>
                <select name="trang_thai" class="form-control">
                    <option value="Draft" <?php echo ($isEdit && $this->model->trang_thai == 'Draft') ? 'selected' : ''; ?>>Lưu Nháp (Draft)</option>
                    <option value="Published" <?php echo ($isEdit && $this->model->trang_thai == 'Published') ? 'selected' : ''; ?>>Xuất Bản Ngay</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Chuyên Mục</label>
                <select name="chuyen_muc" class="form-control" required>
                    <?php
                        $cates = ['Tin tuc' => 'Tin Tức Chung', 'Thong bao' => 'Thông Báo Quy Chế', 'Da ngoai' => 'Hoạt động Dã Ngoại', 'Tuyen sinh' => 'Chỉ Tiêu Tuyển Sinh'];
                        foreach ($cates as $val => $text):
                            $selected = ($isEdit && $this->model->chuyen_muc == $val) ? 'selected' : '';
                            echo "<option value='{$val}' {$selected}>{$text}</option>";
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Tác Giả <span style="font-size:12px; font-weight:normal">(Giả lập)</span></label>
                <select name="tac_gia_id" class="form-control" required>
                    <!-- Trong thực tế danh sách này load từ DB, nhưng để test MVC, ta cố định 1 vài data -->
                    <option value="1" <?php echo ($isEdit && $this->model->tac_gia_id == 1) ? 'selected' : ''; ?>>Giảng Viên: Thanh</option>
                    <option value="2" <?php echo ($isEdit && $this->model->tac_gia_id == 2) ? 'selected' : ''; ?>>Giảng Viên: Minh Hà</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_hot" value="1" <?php echo ($isEdit && $this->model->is_hot == 1) ? 'checked' : ''; ?> style="width: 20px; height: 20px;">
                    Đánh dấu là "Tin Nổi Bật / HOT"
                </label>
            </div>

            <hr style="margin: 15px 0; border: none; border-top: 1px solid #dee2e6;" />

            <div class="form-group">
                <label class="form-label">Ảnh Thumbnail (Bìa đại diện)</label>
                <?php if ($isEdit && !empty($this->model->thumbnail)): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="public/uploads/<?php echo htmlspecialchars($this->model->thumbnail); ?>" alt="Preview" style="max-width: 100%; border-radius:4px;">
                        <span style="font-size:12px; color:#6c757d;">(Tải ảnh mới nếu muốn ghi đè ảnh cũ)</span>
                    </div>
                <?php endif; ?>
                <input type="file" name="thumbnail" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size:1.1rem; margin-top:20px;">
                💾 <?php echo $isEdit ? "Cập Nhật" : "Lưu Bài Viết"; ?>
            </button>
        </div>
    </div>
</form>

<!-- Cấu hình CKEditor -->
<script>
    CKEDITOR.replace('noi_dung', {
        height: 400,
        toolbar: [
            { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
            '/',
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
            '/',
            { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
            { name: 'about', items: [ 'About' ] }
        ]
    });
</script>
