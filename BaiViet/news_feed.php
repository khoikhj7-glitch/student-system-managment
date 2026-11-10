<style>
.news-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 30px 20px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    border-radius: 16px;
    box-shadow: var(--shadow-md);
}
.news-header h1 { font-size: 2.5rem; font-weight: 800; margin-bottom: 10px; }
.news-header p { font-size: 1.1rem; opacity: 0.9; }

.news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
}
.news-card {
    background: var(--surface);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid var(--gray-200);
    display: flex; flex-direction: column;
}
.news-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}
.news-cover {
    width: 100%;
    height: 180px;
    background-size: cover;
    background-position: center;
    background-color: var(--gray-200);
    position: relative;
}
.news-badge {
    position: absolute;
    top: 15px; left: 15px;
    background: var(--primary);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
}
.news-content {
    padding: 24px;
    flex: 1;
    display: flex; flex-direction: column;
}
.news-meta {
    font-size: 0.85rem;
    color: var(--gray-500);
    margin-bottom: 12px;
    display: flex; align-items: center; gap: 10px;
}
.news-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 12px;
    line-height: 1.4;
}
.news-excerpt {
    color: var(--gray-600);
    font-size: 0.95rem;
    margin-bottom: 20px;
    line-height: 1.5;
    flex: 1;
}
.news-footer {
    display: flex; justify-content: space-between; align-items: center;
    border-top: 1px solid var(--gray-200);
    padding-top: 16px;
}
.news-author { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 500; }
.news-views { font-size: 0.85rem; color: var(--gray-500); }
</style>

<div class="news-header">
    <h1>Bảng Tin Sinh Viên</h1>
    <p>Cập nhật thông tin, thông báo và tin tức mới nhất từ hệ thống</p>
</div>

<!-- Bộ lọc (Filter Toolbar) -->
<div style="background: var(--surface); padding: 20px; border-radius: 12px; margin-bottom: 30px; box-shadow: var(--shadow-sm); display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px;">
    <!-- Tabs Danh Mục -->
    <div style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px;">
        <a href="index.php?f=BaiViet" class="btn btn-sm <?= empty($_GET['cat']) ? 'btn-primary' : 'btn-secondary' ?>" style="border-radius: 20px;">Tất Cả</a>
        <?php if(!empty($categories)): foreach($categories as $c): ?>
            <a href="index.php?f=BaiViet&cat=<?= urlencode($c) ?>" class="btn btn-sm <?= (isset($_GET['cat']) && $_GET['cat'] == $c) ? 'btn-primary' : 'btn-secondary' ?>" style="border-radius: 20px;">
                <?= htmlspecialchars($c) ?>
            </a>
        <?php endforeach; endif; ?>
    </div>
    
    <!-- Tìm Kiếm -->
    <form action="index.php" method="GET" style="display: flex; gap: 10px; flex: 1; min-width: 250px;">
        <input type="hidden" name="f" value="BaiViet">
        <?php if(!empty($_GET['cat'])): ?>
            <input type="hidden" name="cat" value="<?= htmlspecialchars($_GET['cat']) ?>">
        <?php endif; ?>
        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Tìm kiếm bài viết..." class="form-control" style="border-radius: 20px; border-color: var(--gray-300);">
        <button type="submit" class="btn btn-primary" style="border-radius: 20px;">🔍 Tìm</button>
    </form>
</div>

<?php if(empty($bai_viets)): ?>
    <div class="alert alert-info text-center">
        Hiện tại chưa có bài viết nào được xuất bản.
    </div>
<?php else: ?>
    <div class="news-grid">
        <?php foreach($bai_viets as $bv): ?>
            <a href="index.php?f=BaiViet&action=detail&id=<?= $bv['id'] ?>" class="news-card" style="text-decoration:none;">
                <?php $cover = $bv['thumbnail'] ? "public/uploads/".$bv['thumbnail'] : "https://images.unsplash.com/photo-1546410531-bb4caa6b424d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"; ?>
                <div class="news-cover" style="background-image: url('<?= htmlspecialchars($cover) ?>');">
                    <?php if($bv['is_hot']): ?>
                        <div class="news-badge" style="background:var(--danger-color);">🔥 HOT</div>
                    <?php else: ?>
                        <div class="news-badge"><?= htmlspecialchars($bv['chuyen_muc']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="news-content">
                    <div class="news-meta">
                        <span>📅 <?= date('d/m/Y', strtotime($bv['created_at'])) ?></span>
                    </div>
                    <h3 class="news-title"><?= htmlspecialchars($bv['tieu_de']) ?></h3>
                    <div class="news-excerpt">
                        <?= htmlspecialchars($bv['tom_tat'] ?? strip_tags(substr($bv['noi_dung'], 0, 150)).'...') ?>
                    </div>
                </div>
                
                <div class="news-footer" style="padding: 0 24px 20px 24px; border:none;">
                    <div class="news-author">
                        <div style="width:24px; height:24px; background:var(--gray-300); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px;">
                            ✍️
                        </div>
                        <?= htmlspecialchars($bv['tac_gia_ten']) ?>
                    </div>
                    <div class="news-views">
                        👁️ <?= $bv['luot_xem'] ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
