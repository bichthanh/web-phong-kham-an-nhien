<?php
$page_title = "Thanh toán viện phí & Hóa đơn";
$activeNav = 'thanh_toan';
require_once __DIR__ . '/../includes/patient_header.php';

$userId = $patUser['id'];
$userPhone = $patUser['phone'];

// Xử lý xác nhận thanh toán trực tuyến (Simulated Payment Gateway)
if (isset($_POST['action']) && $_POST['action'] === 'confirm_payment') {
    $appId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    $payMethod = sanitizeInput($_POST['payment_method'] ?? 'BANK_TRANSFER');

    if ($appId) {
        $stmtPay = $pdo->prepare("UPDATE appointments SET payment_status = 'PAID', payment_method = ? WHERE id = ? AND (patient_id = ? OR patient_phone = ?)");
        $stmtPay->execute([$payMethod, $appId, $userId, $userPhone]);
        setFlashMessage('success', 'Xác nhận thanh toán thành công! Hóa đơn điện tử đã được phát hành.');
        header("Location: thanh-toan.php");
        exit;
    }
}

// Lấy danh sách lịch hẹn cần thanh toán và đã thanh toán
$sql = "SELECT a.*, d.full_name as doctor_name, s.name as specialty_name
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        JOIN specialties s ON d.specialty_id = s.id
        WHERE a.patient_id = ? OR a.patient_phone = ?
        ORDER BY a.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $userPhone]);
$appointments = $stmt->fetchAll();

$flash = getFlashMessage();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-receipt" style="color:var(--primary);"></i> Thanh Toán Phí Khám & Xuất Hóa Đơn</h2>
        <p>Hỗ trợ thanh toán nhanh qua quét mã VietQR, ví điện tử hoặc thanh toán tại quầy</p>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <div><?= htmlspecialchars($flash['message']) ?></div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-money-check-dollar" style="color:var(--primary);margin-right:8px;"></i> Danh Sách Khoản Phí Khám Chữa Bệnh</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã Phiếu</th>
                        <th>Chuyên Khoa & Bác Sĩ</th>
                        <th>Ngày Khám</th>
                        <th>Số Tiền</th>
                        <th>Phương Thức</th>
                        <th>Trạng Thái</th>
                        <th style="text-align:right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;padding:40px;color:#94A3B8;">
                                Bạn không có khoản phí khám nào.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($appointments as $a): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($a['appointment_code']) ?></strong></td>
                            <td><?= htmlspecialchars($a['specialty_name']) ?> - <?= htmlspecialchars($a['doctor_name']) ?></td>
                            <td><?= formatDate($a['appointment_date']) ?></td>
                            <td><strong style="color:#DC2626;font-size:15px;"><?= formatMoney($a['amount']) ?></strong></td>
                            <td>
                                <?php if ($a['payment_method'] === 'BANK_TRANSFER'): ?>
                                    <span class="badge badge-info"><i class="fa-solid fa-qrcode"></i> VietQR Chuyển khoản</span>
                                <?php elseif ($a['payment_method'] === 'MOMO'): ?>
                                    <span class="badge badge-purple"><i class="fa-solid fa-wallet"></i> Ví MoMo</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><i class="fa-solid fa-money-bill"></i> Tại quầy lễ tân</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= getPaymentBadge($a['payment_status']) ?>
                            </td>
                            <td style="text-align:right;white-space:nowrap;">
                                <?php if ($a['payment_status'] === 'UNPAID'): ?>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="openPaymentModal(<?= $a['id'] ?>, '<?= htmlspecialchars($a['appointment_code']) ?>', <?= $a['amount'] ?>)">
                                        <i class="fa-solid fa-credit-card"></i> Thanh toán ngay
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-outline" onclick="openInvoiceModal('<?= htmlspecialchars($a['appointment_code']) ?>', '<?= htmlspecialchars($a['patient_name']) ?>', '<?= htmlspecialchars($a['specialty_name']) ?>', '<?= htmlspecialchars($a['doctor_name']) ?>', <?= $a['amount'] ?>, '<?= formatDate($a['appointment_date']) ?>')">
                                        <i class="fa-solid fa-file-invoice-dollar"></i> Xem hóa đơn
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL THANH TOÁN QR -->
<div id="payment_modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#FFF;max-width:500px;width:100%;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-xl);">
        <div style="background:var(--primary);color:#FFF;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:16px;"><i class="fa-solid fa-qrcode"></i> Thanh Toán VietQR Phòng Khám An Nhiên</h4>
            <button type="button" onclick="closePaymentModal()" style="background:none;border:none;color:#FFF;font-size:20px;cursor:pointer;">&times;</button>
        </div>
        <div style="padding:24px;text-align:center;">
            <p style="font-size:14px;color:#64748B;">Mã phiếu hẹn: <strong id="modal_app_code" style="color:var(--primary-dark);"></strong></p>
            <h3 style="color:#DC2626;font-size:24px;margin:10px 0;" id="modal_amount"></h3>

            <!-- SVG QR Code giả lập cực đẹp -->
            <div style="background:#F8FAFC;border:2px dashed #CBD5E1;border-radius:10px;padding:16px;display:inline-block;margin:12px 0;">
                <svg width="180" height="180" viewBox="0 0 180 180">
                    <rect width="180" height="180" fill="#FFFFFF"/>
                    <!-- QR code elements simulated -->
                    <rect x="15" y="15" width="45" height="45" fill="#0F172A"/>
                    <rect x="22" y="22" width="31" height="31" fill="#FFFFFF"/>
                    <rect x="28" y="28" width="19" height="19" fill="#0F172A"/>

                    <rect x="120" y="15" width="45" height="45" fill="#0F172A"/>
                    <rect x="127" y="22" width="31" height="31" fill="#FFFFFF"/>
                    <rect x="133" y="28" width="19" height="19" fill="#0F172A"/>

                    <rect x="15" y="120" width="45" height="45" fill="#0F172A"/>
                    <rect x="22" y="127" width="31" height="31" fill="#FFFFFF"/>
                    <rect x="28" y="133" width="19" height="19" fill="#0F172A"/>

                    <rect x="70" y="20" width="12" height="25" fill="#0F172A"/>
                    <rect x="90" y="15" width="20" height="15" fill="#0F172A"/>
                    <rect x="70" y="60" width="40" height="40" fill="#0D9488" rx="8"/>
                    <text x="90" y="85" font-family="Arial" font-size="12" font-weight="bold" fill="#FFF" text-anchor="middle">AN NHIÊN</text>

                    <rect x="120" y="70" width="15" height="40" fill="#0F172A"/>
                    <rect x="145" y="80" width="20" height="20" fill="#0F172A"/>
                    <rect x="70" y="120" width="30" height="15" fill="#0F172A"/>
                    <rect x="110" y="130" width="40" height="20" fill="#0F172A"/>
                </svg>
                <div style="font-size:11.5px;color:#64748B;margin-top:6px;">VietinBank • STK: 1088.6868.9999 • An Nhiên Nam Định</div>
            </div>

            <form method="POST" action="thanh-toan.php" style="margin-top:16px;">
                <input type="hidden" name="action" value="confirm_payment">
                <input type="hidden" name="appointment_id" id="modal_app_id" value="">
                <input type="hidden" name="payment_method" value="BANK_TRANSFER">
                <button type="submit" class="btn btn-success" style="width:100%;padding:12px;">
                    <i class="fa-solid fa-circle-check"></i> Xác Nhận Đã Chuyển Khoản Thành Công
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openPaymentModal(id, code, amount) {
    document.getElementById('modal_app_id').value = id;
    document.getElementById('modal_app_code').innerText = code;
    document.getElementById('modal_amount').innerText = Number(amount).toLocaleString('vi-VN') + ' đ';
    document.getElementById('payment_modal').style.display = 'flex';
}
function closePaymentModal() {
    document.getElementById('payment_modal').style.display = 'none';
}
function openInvoiceModal(code, patient, spec, doc, amt, date) {
    alert(`HÓA ĐƠN ĐIỆN TỬ PHÒNG KHÁM AN NHIÊN\n--------------------------------\nMã phiếu: ${code}\nBệnh nhân: ${patient}\nChuyên khoa: ${spec}\nBác sĩ: ${doc}\nNgày khám: ${date}\nSố tiền: ${Number(amt).toLocaleString('vi-VN')} đ\nTrạng thái: ĐÃ QUYẾT TOÁN\nCơ sở: An Nhiên Nam Định (BHYT)`);
}
</script>

<?php require_once __DIR__ . '/../includes/patient_footer.php'; ?>
