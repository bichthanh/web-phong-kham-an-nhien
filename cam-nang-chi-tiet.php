<?php
$postId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
require_once __DIR__ . '/includes/db.php';

if (!$postId) {
    header("Location: cam-nang.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    die("Không tìm thấy bài viết!");
}

// Tăng lượt xem
$pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$postId]);

$page_title = $post['title'];
require_once __DIR__ . '/includes/header.php';
?>

<div style="background:linear-gradient(135deg, #042F2E 0%, #0F172A 100%);color:#FFF;padding:45px 0;">
    <div class="container" style="max-width:850px;">
        <span class="badge badge-info" style="margin-bottom:12px;"><?= htmlspecialchars($post['category']) ?></span>
        <h1 style="font-size:32px;font-weight:800;line-height:1.3;margin-bottom:14px;"><?= htmlspecialchars($post['title']) ?></h1>
        <div style="display:flex;gap:20px;font-size:13.5px;color:#94A3B8;">
            <span><i class="fa-solid fa-user-pen"></i> Tác giả: <strong><?= htmlspecialchars($post['author_name']) ?></strong></span>
            <span><i class="fa-regular fa-calendar"></i> Ngày đăng: <?= formatDate($post['created_at']) ?></span>
            <span><i class="fa-regular fa-eye"></i> <?= $post['views'] + 1 ?> lượt xem</span>
        </div>
    </div>
</div>

<section class="section">
    <div class="container" style="max-width:850px;">
        <div class="card" style="padding:35px;">
            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($post['image_url']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" style="width:100%;height:380px;object-fit:cover;border-radius:12px;margin-bottom:28px;">
            
            <div style="font-size:16px;line-height:1.8;color:#1E293B;" class="post-content">
                <?= $post['content'] ?>
            </div>

            <div style="margin-top:40px;padding-top:24px;border-top:1px solid #E2E8F0;display:flex;justify-content:space-between;align-items:center;">
                <a href="<?= BASE_URL ?>/cam-nang.php" class="btn btn-outline">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách cẩm nang
                </a>
                <a href="<?= BASE_URL ?>/dat-lich.php" class="btn btn-primary">
                    <i class="fa-solid fa-calendar-check"></i> Đặt lịch khám ngay
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
