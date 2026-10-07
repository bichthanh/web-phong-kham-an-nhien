<?php
$page_title = "Cẩm nang Y tế & Sức khỏe";
require_once __DIR__ . '/includes/header.php';

$posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC")->fetchAll();
?>

<div style="background:linear-gradient(135deg, #042F2E 0%, #0F172A 100%);color:#FFF;padding:50px 0;">
    <div class="container">
        <h1 style="font-size:32px;font-weight:800;margin-bottom:8px;">Cẩm Nang Sức Khỏe & Tin Tức Y Khoa</h1>
        <p style="color:#94A3B8;font-size:16px;">Kiến thức y khoa chuẩn xác, dễ hiểu từ các chuyên gia bác sĩ Phòng khám Đa khoa An Nhiên.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:28px;">
            <?php foreach ($posts as $p): ?>
                <div class="card" style="overflow:hidden;display:flex;flex-direction:column;">
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" style="height:220px;width:100%;object-fit:cover;">
                    <div class="card-body" style="display:flex;flex-direction:column;flex:1;">
                        <span class="badge badge-info" style="align-self:flex-start;margin-bottom:12px;"><?= htmlspecialchars($p['category']) ?></span>
                        <h3 style="font-size:18px;font-weight:700;line-height:1.4;margin-bottom:10px;">
                            <a href="<?= BASE_URL ?>/cam-nang-chi-tiet.php?id=<?= $p['id'] ?>" style="color:#0F172A;">
                                <?= htmlspecialchars($p['title']) ?>
                            </a>
                        </h3>
                        <p style="font-size:14px;color:#64748B;line-height:1.6;margin-bottom:20px;flex:1;">
                            <?= htmlspecialchars($p['summary']) ?>
                        </p>
                        <div style="display:flex;justify-content:space-between;align-items:center;font-size:12.5px;color:#94A3B8;border-top:1px solid #E2E8F0;padding-top:14px;margin-top:auto;">
                            <span><i class="fa-solid fa-user-doctor"></i> <?= htmlspecialchars($p['author_name']) ?></span>
                            <span><i class="fa-regular fa-clock"></i> <?= formatDate($p['created_at']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
