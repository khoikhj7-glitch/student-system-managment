<style>
.article-detail { max-width: 900px; margin: 0 auto; background: var(--surface); padding: 40px; border-radius: 16px; box-shadow: var(--shadow-sm); }
.article-title { font-size: 2.5rem; font-weight: 800; color: var(--gray-900); margin-bottom: 20px; line-height: 1.3; }
.article-meta { display: flex; align-items: center; gap: 20px; color: var(--gray-500); font-size: 0.95rem; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--gray-200); }
.article-category { background: var(--primary); color: white; padding: 4px 12px; border-radius: 20px; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; }
.article-cover { width: 100%; height: auto; max-height: 500px; object-fit: cover; border-radius: 12px; margin-bottom: 30px; }
.article-content { font-size: 1.1rem; line-height: 1.8; color: var(--gray-800); }
.article-content h2, .article-content h3 { color: var(--gray-900); margin-top: 30px; margin-bottom: 15px; }
.article-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 20px 0; }
.back-btn { display: inline-flex; align-items: center; gap: 8px; color: var(--primary); font-weight: 600; margin-bottom: 20px; text-decoration: none; }
.back-btn:hover { color: var(--primary-hover); text-decoration: underline; }
</style>

<div style="max-width: 900px; margin: 0 auto;">
    <a href="index.php?f=BaiViet" class="back-btn">⬅ Quay lại bảng tin</a>
</div>

<div class="article-detail">
    <?php if(!$article): ?>
        <div class="alert alert-danger text-center">Không tìm thấy bài viết hoặc bài viết đã bị xóa.</div>
    <?php else: ?>
        <div class="article-meta">
            <span class="article-category"><?= htmlspecialchars($article['chuyen_muc'] ?? 'Tin tức') ?></span>
            <span>📅 <?= date('d/m/Y H:i', strtotime($article['created_at'])) ?></span>
            <span>👁️ <?= $article['luot_xem'] ?? 0 ?> lượt xem</span>
        </div>

        <h1 class="article-title"><?= htmlspecialchars($article['tieu_de']) ?></h1>
        
        <?php if(!empty($article['tom_tat'])): ?>
            <p style="font-size: 1.2rem; color: var(--gray-600); font-style: italic; margin-bottom: 30px; border-left: 4px solid var(--primary); padding-left: 16px;">
                <?= htmlspecialchars($article['tom_tat']) ?>
            </p>
        <?php endif; ?>

        <?php if(!empty($article['thumbnail'])): ?>
            <img src="public/uploads/<?= htmlspecialchars($article['thumbnail']) ?>" class="article-cover" alt="Thumbnail">
        <?php endif; ?>

        <div class="article-content">
            <?= $article['noi_dung'] ?>
        </div>
        
        <?php if(!empty($related_articles)): ?>
            <div style="margin-top: 60px; padding-top: 30px; border-top: 1px solid var(--gray-200);">
                <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; color: var(--gray-900);">Bài Viết Cùng Chuyên Mục</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <?php foreach($related_articles as $rel): ?>
                        <a href="index.php?f=BaiViet&action=detail&id=<?= $rel['id'] ?>" style="display: block; text-decoration: none; background: white; border: 1px solid var(--gray-200); border-radius: 12px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; box-shadow: var(--shadow-sm);">
                            <?php $relCover = $rel['thumbnail'] ? "public/uploads/".$rel['thumbnail'] : "https://images.unsplash.com/photo-1546410531-bb4caa6b424d?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80"; ?>
                            <div style="width: 100%; height: 140px; background: url('<?= htmlspecialchars($relCover) ?>') center/cover;"></div>
                            <div style="padding: 15px;">
                                <div style="font-size: 0.8rem; color: var(--gray-500); margin-bottom: 8px;">📅 <?= date('d/m/Y', strtotime($rel['created_at'])) ?></div>
                                <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--gray-900); margin: 0; line-height: 1.4;"><?= htmlspecialchars($rel['tieu_de']) ?></h4>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
