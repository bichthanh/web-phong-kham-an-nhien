<?php
$page_title = "Lịch khám của tôi";
$activeNav = 'dashboard';
require_once __DIR__ . '/../includes/patient_header.php';

$userId = $patUser['id'];
$userPhone = $patUser['phone'];

// Xử lý hủy lịch hẹn
if (isset($_POST['action']) && $_POST['action'] === 'cancel_appointment') {
    $appId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    if ($appId) {
        $stmtChk = $pdo->prepare("SELECT id, schedule_id, status FROM appointments WHERE id = ? AND (patient_id = ? OR patient_phone = ?)");
        $stmtChk->execute([$appId, $userId, $userPhone]);
        $app = $stmtChk->fetch();

        if ($app && in_array($app['status'], ['PENDING', 'CONFIRMED'])) {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE appointments SET status = 'CANCELLED' WHERE id = ?")->execute([$appId]);
            $pdo->prepare("UPDATE doctor_schedules SET current_booked = GREATEST(current_booked - 1, 0), status = 'AVAILABLE' WHERE id = ?")->execute([$app['schedule_id']]);
            $pdo->commit();
            setFlashMessage('success', 'Đã hủy lịch hẹn khám thành công!');
            header("Location: index.php");
            exit;
        } else {
            setFlashMessage('error', 'Không thể hủy lịch khám này!');
        }
    }
}

// Lấy danh sách lịch hẹn của bệnh nhân
$sql = "SELECT a.*, d.full_name as doctor_name, d.degree as doctor_degree, d.clinic_room, s.name as specialty_name
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        JOIN specialties s ON d.specialty_id = s.id
        WHERE a.patient_id = ? OR a.patient_phone = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $userPhone]);
$appointments = $stmt->fetchAll();

$flash = getFlashMessage();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-calendar-check" style="color:var(--primary);"></i> Lịch Hẹn Khám Bệnh Của Tôi</h2>
        <p>Theo dõi trạng thái lịch khám, kết quả chẩn đoán và đơn thuốc điện tử</p>
    </div>
    <a href="<?= BASE_URL ?>/dat-lich.php" class="btn btn-primary">
        <i class="fa-solid fa-calendar-plus"></i> Đặt Lịch Khám Mới
    </a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <div><?= htmlspecialchars($flash['message']) ?></div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-list-ul" style="color:var(--primary);margin-right:8px;"></i> Lịch Sử Lịch Đặt Khám (<?= count($appointments) ?> lượt)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã Phiếu</th>
                        <th>Chuyên Khoa & Bác Sĩ</th>
                        <th>Ngày & Giờ Khám</th>
                        <th>Phòng Khám</th>
                        <th>Trạng Thái</th>
                        <th>Thanh Toán</th>
                        <th style="text-align:right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;padding:40px;color:#94A3B8;">
                                <i class="fa-regular fa-calendar-plus fa-3x" style="margin-bottom:12px;display:block;"></i>
                                Bạn chưa có lịch hẹn khám nào tại Phòng khám An Nhiên.<br>
                                <a href="<?= BASE_URL ?>/dat-lich.php" class="btn btn-sm btn-primary" style="margin-top:10px;">Đặt lịch ngay bây giờ</a>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($appointments as $a): ?>
                        <tr>
                            <td>
                                <strong style="color:var(--primary-dark);"><?= htmlspecialchars($a['appointment_code']) ?></strong>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($a['doctor_degree']) ?> <?= htmlspecialchars($a['doctor_name']) ?></strong>
                                <span style="display:block;font-size:12px;color:#64748B;"><?= htmlspecialchars($a['specialty_name']) ?></span>
                            </td>
                            <td>
                                <strong style="color:#0284C7;"><?= formatDate($a['appointment_date']) ?></strong>
                                <span style="display:block;font-size:12px;color:#64748B;"><?= formatTime($a['appointment_time']) ?></span>
                            </td>
                            <td>
                                <span class="badge badge-purple"><?= htmlspecialchars($a['clinic_room']) ?></span>
                            </td>
                            <td>
                                <?= getStatusBadge($a['status']) ?>
                            </td>
                            <td>
                                <?= getPaymentBadge($a['payment_status']) ?>
                            </td>
                            <td style="text-align:right;white-space:nowrap;">
                                <a href="<?= BASE_URL ?>/tra-cuu.php?code=<?= urlencode($a['appointment_code']) ?>" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i> Chi tiết
                                </a>

                                <?php if ($a['status'] === 'COMPLETED'): ?>
                                    <a href="<?= BASE_URL ?>/tra-cuu.php?code=<?= urlencode($a['appointment_code']) ?>" class="btn btn-sm btn-success" title="Xem đơn thuốc">
                                        <i class="fa-solid fa-file-prescription"></i> Đơn thuốc
                                    </a>
                                <?php endif; ?>

                                <?php if (in_array($a['status'], ['PENDING', 'CONFIRMED'])): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn hủy lịch hẹn này?');">
                                        <input type="hidden" name="action" value="cancel_appointment">
                                        <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hủy lịch">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/patient_footer.php'; ?>
