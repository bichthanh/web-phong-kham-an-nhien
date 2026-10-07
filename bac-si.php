<?php
$page_title = "Đội ngũ Bác sĩ";
require_once __DIR__ . '/includes/header.php';

$selected_spec = filter_input(INPUT_GET, 'specialty_id', FILTER_VALIDATE_INT);

// Lấy danh sách chuyên khoa cho bộ lọc
$specialties = $pdo->query("SELECT * FROM specialties ORDER BY name ASC")->fetchAll();

// Lấy danh sách bác sĩ
if ($selected_spec) {
    $stmt = $pdo->prepare("SELECT d.*, s.name as specialty_name 
                           FROM doctors d 
                           JOIN specialties s ON d.specialty_id = s.id 
                           WHERE d.specialty_id = ? 
                           ORDER BY d.experience_years DESC");
    $stmt->execute([$selected_spec]);
} else {
    $stmt = $pdo->query("SELECT d.*, s.name as specialty_name 
                         FROM doctors d 
                         JOIN specialties s ON d.specialty_id = s.id 
                         ORDER BY d.experience_years DESC");
}
$doctors = $stmt->fetchAll();
?>

<div style="background:linear-gradient(135deg, #042F2E 0%, #0F172A 100%);color:#FFF;padding:50px 0;">
    <div class="container">
        <h1 style="font-size:32px;font-weight:800;margin-bottom:8px;">Đội Ngũ Bác Sĩ Chuyên Khoa</h1>
        <p style="color:#94A3B8;font-size:16px;">Hội tụ các chuyên gia y tế giàu kinh nghiệm, tận tụy vì sức khỏe người bệnh tại Nam Định.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- BỘ LỌC CHUYÊN KHOA -->
        <div style="background:#FFF;padding:18px 24px;border-radius:12px;border:1px solid #E2E8F0;margin-bottom:35px;display:flex;align-items:center;gap:15px;flex-wrap:wrap;">
            <span style="font-weight:700;color:#0F172A;"><i class="fa-solid fa-filter" style="color:var(--primary);"></i> Lọc theo chuyên khoa:</span>
            <a href="<?= BASE_URL ?>/bac-si.php" class="btn btn-sm <?= !$selected_spec ? 'btn-primary' : 'btn-outline' ?>">
                Tất cả (<?= count($pdo->query("SELECT id FROM doctors")->fetchAll()) ?>)
            </a>
            <?php foreach ($specialties as $sp): ?>
                <a href="<?= BASE_URL ?>/bac-si.php?specialty_id=<?= $sp['id'] ?>" class="btn btn-sm <?= $selected_spec == $sp['id'] ? 'btn-primary' : 'btn-outline' ?>">
                    <?= htmlspecialchars($sp['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- DANH SÁCH BÁC SĨ -->
        <div class="doctors-grid">
            <?php if (empty($doctors)): ?>
                <div style="grid-column:1/-1;text-align:center;padding:50px;background:#FFF;border-radius:12px;border:1px solid #E2E8F0;">
                    <i class="fa-solid fa-user-doctor fa-3x" style="color:#CBD5E1;margin-bottom:12px;"></i>
                    <p style="font-size:16px;color:#64748B;">Chưa có bác sĩ trong chuyên khoa này.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($doctors as $doc): ?>
                <div class="doctor-card" id="doc-<?= $doc['id'] ?>">
                    <div class="doctor-header">
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($doc['avatar']) ?>" alt="<?= htmlspecialchars($doc['full_name']) ?>">
                        <span class="doctor-badge-room"><?= htmlspecialchars($doc['clinic_room']) ?></span>
                    </div>
                    <div class="doctor-body">
                        <div class="doctor-specialty"><?= htmlspecialchars($doc['specialty_name']) ?></div>
                        <h4 class="doctor-name"><?= htmlspecialchars($doc['full_name']) ?></h4>
                        <div class="doctor-degree"><?= htmlspecialchars($doc['degree']) ?> • <?= $doc['experience_years'] ?> năm kinh nghiệm</div>
                        <p style="font-size:13.5px;color:#64748B;margin-bottom:14px;line-height:1.5;">
                            <?= htmlspecialchars($doc['bio']) ?>
                        </p>
                        <div class="doctor-meta">
                            <span>Phí khám tư vấn:</span>
                            <span class="doctor-fee"><?= formatMoney($doc['consultation_fee']) ?></span>
                        </div>
                        <div class="doctor-footer">
                            <a href="<?= BASE_URL ?>/dat-lich.php?doctor_id=<?= $doc['id'] ?>&specialty_id=<?= $doc['specialty_id'] ?>" class="btn btn-primary" style="flex:1;">
                                <i class="fa-solid fa-calendar-check"></i> Đặt lịch với bác sĩ
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
