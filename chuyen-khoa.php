<?php
$page_title = "Danh mục Chuyên khoa";
require_once __DIR__ . '/includes/header.php';

// Lấy danh sách chuyên khoa kèm số lượng bác sĩ của mỗi khoa
$sql = "SELECT s.*, COUNT(d.id) as total_doctors 
        FROM specialties s 
        LEFT JOIN doctors d ON s.id = d.specialty_id 
        GROUP BY s.id 
        ORDER BY s.id ASC";
$specialties = $pdo->query($sql)->fetchAll();
?>

<div style="background:linear-gradient(135deg, #042F2E 0%, #0F172A 100%);color:#FFF;padding:50px 0;">
    <div class="container">
        <h1 style="font-size:32px;font-weight:800;margin-bottom:8px;">Danh Mục Chuyên Khoa Khám Bệnh</h1>
        <p style="color:#94A3B8;font-size:16px;">Phòng khám Đa khoa An Nhiên Nam Định cung cấp dịch vụ khám chữa bệnh chất lượng cao ở nhiều chuyên khoa.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:30px;">
            <?php foreach ($specialties as $s): ?>
                <div class="card" style="display:flex;flex-direction:row;overflow:hidden;gap:0;">
                    <div style="width:40%;background:#F1F5F9;position:relative;">
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($s['image_url']) ?>" alt="<?= htmlspecialchars($s['name']) ?>" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <div style="width:60%;padding:24px;display:flex;flex-direction:column;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                            <span class="badge badge-info"><i class="fa-solid fa-user-doctor"></i> <?= $s['total_doctors'] ?> Bác sĩ</span>
                            <span style="font-size:13px;color:#94A3B8;">Khoa số #<?= $s['id'] ?></span>
                        </div>
                        <h3 style="font-size:20px;font-weight:700;color:#0F172A;margin-bottom:10px;">
                            <i class="fa-solid <?= htmlspecialchars($s['icon'] ?? 'fa-stethoscope') ?>" style="color:var(--primary);margin-right:6px;"></i>
                            <?= htmlspecialchars($s['name']) ?>
                        </h3>
                        <p style="font-size:14px;color:#64748B;line-height:1.6;margin-bottom:20px;flex:1;">
                            <?= htmlspecialchars($s['description']) ?>
                        </p>
                        <div style="display:flex;gap:10px;margin-top:auto;">
                            <a href="<?= BASE_URL ?>/dat-lich.php?specialty_id=<?= $s['id'] ?>" class="btn btn-primary" style="flex:1;">
                                <i class="fa-solid fa-calendar-check"></i> Đặt khám khoa này
                            </a>
                            <a href="<?= BASE_URL ?>/bac-si.php?specialty_id=<?= $s['id'] ?>" class="btn btn-outline" title="Xem bác sĩ thuộc khoa">
                                <i class="fa-solid fa-users"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
