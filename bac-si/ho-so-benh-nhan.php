<?php
$page_title = "Tra cứu hồ sơ bệnh án";
$activeNav = 'ho_so_benh_an';
require_once __DIR__ . '/../includes/doctor_header.php';

$search = sanitizeInput($_GET['search'] ?? '');
$patientRecords = [];

if ($search) {
    $stmt = $pdo->prepare("SELECT a.*, p.diagnosis, p.medicine_list, p.doctor_notes, p.re_examination_date,
                                  d.full_name as doctor_name, d.degree as doctor_degree, s.name as specialty_name
                           FROM appointments a
                           LEFT JOIN prescriptions p ON a.id = p.appointment_id
                           JOIN doctors d ON a.doctor_id = d.id
                           JOIN specialties s ON d.specialty_id = s.id
                           WHERE a.patient_phone = ? OR a.patient_name LIKE ? OR a.appointment_code = ?
                           ORDER BY a.appointment_date DESC");
    $stmt->execute([$search, "%$search%", $search]);
    $patientRecords = $stmt->fetchAll();
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-book-medical" style="color:var(--primary);"></i> Lịch Sử Khám Bệnh & Hồ Sơ Bệnh Án</h2>
        <p>Tra cứu tiền sử bệnh lý, chẩn đoán cũ và các đơn thuốc đã dùng của người bệnh</p>
    </div>
</div>

<div class="card" style="margin-bottom:30px;">
    <div class="card-body">
        <form method="GET" action="ho-so-benh-nhan.php" style="display:flex;gap:12px;flex-wrap:wrap;">
            <div style="flex:1;min-width:260px;">
                <input type="text" name="search" class="form-control" 
                       placeholder="Nhập số điện thoại, tên bệnh nhân hoặc mã phiếu hẹn..." 
                       value="<?= htmlspecialchars($search) ?>" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-magnifying-glass"></i> Tra cứu hồ sơ
            </button>
        </form>
    </div>
</div>

<?php if ($search): ?>
    <?php if (empty($patientRecords)): ?>
        <div class="card" style="text-align:center;padding:50px;">
            <i class="fa-solid fa-folder-open fa-3x" style="color:#CBD5E1;margin-bottom:12px;"></i>
            <h4>Không tìm thấy lịch sử bệnh án nào phù hợp!</h4>
            <p style="color:#64748B;">Bệnh nhân có thể khám lần đầu tại phòng khám An Nhiên.</p>
        </div>
    <?php else: ?>
        <h3 style="font-size:18px;margin-bottom:16px;color:#0F172A;">
            Tìm thấy <?= count($patientRecords) ?> đợt khám của bệnh nhân: <strong><?= htmlspecialchars($patientRecords[0]['patient_name']) ?></strong> (SĐT: <?= htmlspecialchars($patientRecords[0]['patient_phone']) ?>)
        </h3>

        <div style="display:flex;flex-direction:column;gap:20px;">
            <?php foreach ($patientRecords as $rec): ?>
                <div class="card" style="border-left:4px solid <?= $rec['status'] === 'COMPLETED' ? 'var(--accent)' : 'var(--primary)' ?>;">
                    <div class="card-header" style="background:#F8FAFC;">
                        <div>
                            <strong>Ngày khám: <?= formatDate($rec['appointment_date']) ?> (<?= formatTime($rec['appointment_time']) ?>)</strong>
                            <span style="color:#64748B;font-size:13px;margin-left:10px;">Mã: <?= htmlspecialchars($rec['appointment_code']) ?></span>
                        </div>
                        <div>
                            <?= getStatusBadge($rec['status']) ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:16px;">
                            <div>
                                <p><strong>Bác sĩ phụ trách:</strong> <?= htmlspecialchars($rec['doctor_degree']) ?> <?= htmlspecialchars($rec['doctor_name']) ?></p>
                                <p><strong>Chuyên khoa:</strong> <?= htmlspecialchars($rec['specialty_name']) ?></p>
                                <p><strong>Triệu chứng ban đầu:</strong> <?= htmlspecialchars($rec['symptom_description']) ?></p>
                            </div>
                            <div>
                                <?php if ($rec['diagnosis']): ?>
                                    <p><strong>Kết luận chẩn đoán:</strong> <span style="color:#0284C7;font-weight:700;"><?= htmlspecialchars($rec['diagnosis']) ?></span></p>
                                    <p><strong>Lời dặn:</strong> <?= htmlspecialchars($rec['doctor_notes'] ?? 'Không có') ?></p>
                                    <?php if ($rec['re_examination_date']): ?>
                                        <p><strong>Hẹn tái khám:</strong> <span style="color:#059669;font-weight:700;"><?= formatDate($rec['re_examination_date']) ?></span></p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p style="color:#94A3B8;">Chưa hoàn tất chẩn đoán.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php 
                        if ($rec['medicine_list']) {
                            $meds = json_decode($rec['medicine_list'], true) ?: [];
                            if (!empty($meds)): ?>
                                <div style="background:#F1F5F9;padding:12px;border-radius:6px;">
                                    <strong style="font-size:13px;color:#0F172A;"><i class="fa-solid fa-pills"></i> Đơn thuốc đã chỉ định trong lần khám này:</strong>
                                    <ul style="margin:6px 0 0 20px;font-size:13px;color:#334155;">
                                        <?php foreach ($meds as $m): ?>
                                            <li><strong><?= htmlspecialchars($m['name']) ?></strong> (SL: <?= $m['quantity'] ?> <?= htmlspecialchars($m['unit']) ?>) - <?= htmlspecialchars($m['usage']) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                        <?php endif; } ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/doctor_footer.php'; ?>
