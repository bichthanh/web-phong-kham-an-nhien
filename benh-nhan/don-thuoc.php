<?php
$page_title = "Đơn thuốc & Kết quả sau khám";
$activeNav = 'don_thuoc';
require_once __DIR__ . '/../includes/patient_header.php';

$userId = $patUser['id'];
$userPhone = $patUser['phone'];

// Lấy danh sách đơn thuốc của bệnh nhân
$sql = "SELECT p.*, a.appointment_code, a.appointment_date, a.patient_name, a.payment_status, a.amount,
               d.full_name as doctor_name, d.degree as doctor_degree, d.consultation_fee, s.name as specialty_name
        FROM prescriptions p
        JOIN appointments a ON p.appointment_id = a.id
        JOIN doctors d ON a.doctor_id = d.id
        JOIN specialties s ON d.specialty_id = s.id
        WHERE a.patient_id = ? OR a.patient_phone = ?
        ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $userPhone]);
$prescriptions = $stmt->fetchAll();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-file-prescription" style="color:var(--primary);"></i> Đơn Thuốc & Kết Quả Thăm Khám</h2>
        <p>Tra cứu hồ sơ chỉ định thuốc, hướng dẫn liều dùng và lời dặn y khoa của bác sĩ</p>
    </div>
</div>

<?php if (empty($prescriptions)): ?>
    <div class="card" style="text-align:center;padding:50px;">
        <i class="fa-solid fa-pills fa-3x" style="color:#CBD5E1;margin-bottom:12px;"></i>
        <h4 style="color:#0F172A;">Chưa có đơn thuốc nào</h4>
        <p style="color:#64748B;">Khi bạn hoàn thành ca khám với bác sĩ tại phòng khám An Nhiên, đơn thuốc điện tử sẽ được hiển thị tại đây.</p>
    </div>
<?php else: ?>
    <div style="display:flex;flex-direction:column;gap:24px;">
        <?php foreach ($prescriptions as $p): 
            $medList = json_decode($p['medicine_list'], true) ?: [];
        ?>
            <div class="card">
                <div class="card-header" style="background:#F8FAFC;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                    <div>
                        <span style="font-size:13px;color:#64748B;">MÃ PHIẾU HẸN: <strong><?= htmlspecialchars($p['appointment_code']) ?></strong></span>
                        <span style="margin:0 10px;color:#CBD5E1;">•</span>
                        <span style="font-size:13px;color:#64748B;">Ngày kê đơn: <strong><?= formatDate($p['created_at']) ?></strong></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <?= getPaymentBadge($p['payment_status']) ?>
                        <a href="<?= BASE_URL ?>/tra-cuu.php?code=<?= urlencode($p['appointment_code']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-print"></i> Xem & In đơn đầy đủ
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div style="margin-bottom:16px;">
                        <span style="font-size:12px;color:#64748B;text-transform:uppercase;font-weight:700;">Chuyên khoa & Bác sĩ điều trị</span>
                        <h4 style="font-size:16px;color:#0F172A;margin-top:2px;">
                            <?= htmlspecialchars($p['doctor_degree']) ?> <?= htmlspecialchars($p['doctor_name']) ?> (<?= htmlspecialchars($p['specialty_name']) ?>)
                        </h4>
                    </div>

                    <div style="background:#F0F9FF;border:1px solid #BAE6FD;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
                        <strong style="color:#0369A1;">Kết luận chẩn đoán:</strong>
                        <p style="color:#0F172A;font-weight:600;margin-top:4px;"><?= htmlspecialchars($p['diagnosis']) ?></p>
                    </div>

                    <h5 style="font-size:14px;color:#0F172A;margin-bottom:10px;"><i class="fa-solid fa-capsules" style="color:var(--primary);"></i> Danh mục thuốc:</h5>
                    <div class="table-responsive">
                        <table class="table" style="font-size:13.5px;">
                            <thead>
                                <tr style="background:#F8FAFC;">
                                    <th>Tên thuốc</th>
                                    <th>Số lượng</th>
                                    <th>Liều uống / Hướng dẫn</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($medList as $med): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($med['name']) ?></strong></td>
                                        <td><?= $med['quantity'] ?> <?= htmlspecialchars($med['unit']) ?></td>
                                        <td><?= htmlspecialchars($med['usage']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- THÔNG TIN THANH TOÁN & HƯỚNG DẪN NHẬN THUỐC -->
                    <div style="margin-top:20px;padding:16px;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:8px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:12px;">
                            <span style="font-size:13px;font-weight:700;color:#0F172A;text-transform:uppercase;">
                                <i class="fa-solid fa-receipt" style="color:var(--primary);"></i> Chi phí thanh toán ca khám:
                            </span>
                            <div>
                                <?= getPaymentBadge($p['payment_status']) ?>
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:12px;font-size:13.5px;">
                            <div>
                                <span style="color:#64748B;">Phí khám:</span> 
                                <strong><?= formatMoney($p['consultation_fee'] ?? 200000) ?></strong>
                            </div>
                            <div>
                                <span style="color:#64748B;">Tiền thuốc:</span> 
                                <strong style="color:#DC2626;"><?= formatMoney($p['medicine_fee'] ?? 0) ?></strong>
                            </div>
                            <div>
                                <span style="color:#64748B;">Tổng thanh toán:</span> 
                                <strong style="color:var(--primary);font-size:15px;"><?= formatMoney($p['amount']) ?></strong>
                            </div>
                        </div>

                        <?php if ($p['payment_status'] !== 'PAID'): ?>
                            <div style="margin-top:12px;padding:10px 14px;background:#FFFBEB;border-left:4px solid #F59E0B;border-radius:4px;font-size:13px;color:#92400E;">
                                <i class="fa-solid fa-cash-register"></i> <strong>Hướng dẫn thanh toán tại quầy:</strong> Quý khách vui lòng đến <strong>Quầy thu ngân (Tầng 1)</strong> và đọc Mã phiếu hẹn <strong><?= htmlspecialchars($p['appointment_code']) ?></strong> để thanh toán số tiền <strong><?= formatMoney($p['amount']) ?></strong> trước khi qua Quầy Dược nhận thuốc.
                            </div>
                        <?php else: ?>
                            <div style="margin-top:12px;padding:10px 14px;background:#ECFDF5;border-left:4px solid #10B981;border-radius:4px;font-size:13px;color:#065F46;">
                                <i class="fa-solid fa-circle-check"></i> <strong>Đã thanh toán:</strong> Toàn bộ chi phí khám và thuốc đã được thanh toán thành công. Quý khách vui lòng qua <strong>Quầy Dược phòng khám</strong> và xuất trình mã đơn để nhận thuốc.
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($p['doctor_notes']): ?>
                        <div style="margin-top:14px;font-size:13px;color:#334155;background:#FFFBEB;padding:10px 14px;border-radius:6px;">
                            <strong><i class="fa-solid fa-stethoscope"></i> Lời dặn của bác sĩ:</strong> <?= htmlspecialchars($p['doctor_notes']) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($p['re_examination_date']): ?>
                        <div style="margin-top:12px;font-size:13px;color:#065F46;">
                            <i class="fa-solid fa-calendar-check"></i> Hẹn tái khám: <strong><?= formatDate($p['re_examination_date']) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/patient_footer.php'; ?>
