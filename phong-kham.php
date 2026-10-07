<?php
$page_title = "Cơ sở vật chất & Phòng khám";
require_once __DIR__ . '/includes/header.php';

$clinics = $pdo->query("SELECT c.*, s.name as specialty_name 
                        FROM clinics c 
                        LEFT JOIN specialties s ON c.specialty_id = s.id 
                        ORDER BY c.room_number ASC")->fetchAll();
?>

<div style="background:linear-gradient(135deg, #042F2E 0%, #0F172A 100%);color:#FFF;padding:50px 0;">
    <div class="container">
        <h1 style="font-size:32px;font-weight:800;margin-bottom:8px;">Hệ Thống Phòng Khám & Cơ Sở Vật Chất</h1>
        <p style="color:#94A3B8;font-size:16px;">Không gian y tế hiện đại, vô trùng, đạt chuẩn quy chuẩn y tế Bộ Y tế và đối tác VKIM Hàn Quốc.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:28px;">
            <?php foreach ($clinics as $c): ?>
                <div class="card" style="display:flex;flex-direction:row;padding:24px;align-items:flex-start;gap:20px;">
                    <div style="width:70px;height:70px;border-radius:14px;background:var(--primary-light);border:1px solid var(--primary-border);display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--primary);flex-shrink:0;">
                        <i class="fa-solid fa-hospital-user" style="font-size:22px;margin-bottom:2px;"></i>
                        <span style="font-size:11px;font-weight:700;"><?= htmlspecialchars($c['room_number']) ?></span>
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                            <span class="badge badge-purple"><?= htmlspecialchars($c['specialty_name'] ?? 'Đa chuyên khoa') ?></span>
                            <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Sẵn sàng phục vụ</span>
                        </div>
                        <h4 style="font-size:18px;color:#0F172A;margin-bottom:8px;"><?= htmlspecialchars($c['name']) ?></h4>
                        <p style="font-size:14px;color:#64748B;line-height:1.5;margin-bottom:14px;">
                            <?= htmlspecialchars($c['description']) ?>
                        </p>
                        <div style="display:flex;gap:12px;font-size:13px;color:#0F766E;font-weight:600;">
                            <span><i class="fa-solid fa-door-open"></i> Số phòng: <?= htmlspecialchars($c['room_number']) ?></span>
                            <span>•</span>
                            <span><i class="fa-solid fa-shield-virus"></i> Khử khuẩn 100%</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
