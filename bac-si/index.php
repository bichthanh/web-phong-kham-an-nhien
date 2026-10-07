<?php
$page_title = "Danh sách bệnh nhân khám hôm nay";
$activeNav = 'dashboard';
require_once __DIR__ . '/../includes/doctor_header.php';

if (!$currentDoctor) {
    die("Tài khoản của bạn chưa được liên kết với hồ sơ bác sĩ trong hệ thống!");
}

$doctorId = $currentDoctor['id'];
$today = date('Y-m-d');
$filterDate = sanitizeInput($_GET['date'] ?? $today);

// Xử lý chuyển trạng thái "Tiếp nhận vào khám" (CHECKED_IN -> IN_PROGRESS)
if (isset($_POST['action']) && $_POST['action'] === 'start_examination') {
    $appId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    if ($appId) {
        $stmtStart = $pdo->prepare("UPDATE appointments SET status = 'IN_PROGRESS' WHERE id = ? AND doctor_id = ?");
        $stmtStart->execute([$appId, $doctorId]);
        header("Location: kham-benh.php?id=" . $appId);
        exit;
    }
}

// Thống kê các ca khám trong ngày
$stmtStat = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status IN ('CONFIRMED', 'CHECKED_IN') THEN 1 ELSE 0 END) as waiting_count,
    SUM(CASE WHEN status = 'IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress_count,
    SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) as completed_count
    FROM appointments 
    WHERE doctor_id = ? AND appointment_date = ?");
$stmtStat->execute([$doctorId, $filterDate]);
$stats = $stmtStat->fetch();

// Lấy danh sách bệnh nhân theo ngày
$stmtApps = $pdo->prepare("SELECT a.*, ds.session_name 
                           FROM appointments a
                           LEFT JOIN doctor_schedules ds ON a.schedule_id = ds.id
                           WHERE a.doctor_id = ? AND a.appointment_date = ?
                           ORDER BY a.appointment_time ASC");
$stmtApps->execute([$doctorId, $filterDate]);
$appointments = $stmtApps->fetchAll();

$flash = getFlashMessage();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-stethoscope" style="color:var(--primary);"></i> Buồng Khám: <?= htmlspecialchars($currentDoctor['clinic_room']) ?></h2>
        <p>Bác sĩ: <strong><?= htmlspecialchars($currentDoctor['degree']) ?> <?= htmlspecialchars($currentDoctor['full_name']) ?></strong> • Chuyên khoa: <?= htmlspecialchars($currentDoctor['specialty_name']) ?></p>
    </div>
    <form method="GET" style="display:flex;align-items:center;gap:10px;">
        <label style="font-weight:600;font-size:14px;color:#475569;">Chọn ngày khám:</label>
        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($filterDate) ?>" onchange="this.form.submit()" style="width:160px;padding:8px 12px;">
    </form>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <div><?= htmlspecialchars($flash['message']) ?></div>
    </div>
<?php endif; ?>

<!-- KPI THỐNG KÊ TRONG NGÀY -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-info">
            <h4><?= $stats['total'] ?: 0 ?></h4>
            <p>Tổng lịch hẹn ngày <?= formatDate($filterDate) ?></p>
        </div>
        <div class="kpi-icon" style="background:#E0F2FE;color:#0284C7;"><i class="fa-solid fa-users"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4><?= $stats['waiting_count'] ?: 0 ?></h4>
            <p>Bệnh nhân chờ khám</p>
        </div>
        <div class="kpi-icon" style="background:#FEF3C7;color:#D97706;"><i class="fa-solid fa-user-clock"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4><?= $stats['in_progress_count'] ?: 0 ?></h4>
            <p>Đang trong buồng khám</p>
        </div>
        <div class="kpi-icon" style="background:#F3E8FF;color:#7E22CE;"><i class="fa-solid fa-hospital-user"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4><?= $stats['completed_count'] ?: 0 ?></h4>
            <p>Ca khám đã hoàn thành</p>
        </div>
        <div class="kpi-icon" style="background:#D1FAE5;color:#059669;"><i class="fa-solid fa-circle-check"></i></div>
    </div>
</div>

<!-- BẢNG DANH SÁCH BỆNH NHÂN -->
<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-list-check" style="color:var(--primary);margin-right:8px;"></i> Danh Sách Bệnh Nhân Đặt Lịch Ngày <?= formatDate($filterDate) ?></h3>
        <span class="badge badge-info"><?= count($appointments) ?> Bệnh nhân</span>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px;">STT</th>
                        <th>Mã Phiếu</th>
                        <th>Bệnh Nhân</th>
                        <th>SĐT / Email</th>
                        <th>Giờ Khám / Ca</th>
                        <th>Triệu Chứng Khai Báo</th>
                        <th>Trạng Thái</th>
                        <th style="text-align:right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center;padding:40px;color:#94A3B8;">
                                <i class="fa-regular fa-calendar-xmark fa-2x" style="margin-bottom:8px;display:block;"></i>
                                Không có bệnh nhân nào đặt lịch khám trong ngày <?= formatDate($filterDate) ?>.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php $idx = 1; foreach ($appointments as $app): ?>
                        <tr style="<?= $app['status'] === 'IN_PROGRESS' ? 'background:#F0FDF4;' : '' ?>">
                            <td><?= $idx++ ?></td>
                            <td><strong style="color:var(--primary-dark);"><?= htmlspecialchars($app['appointment_code']) ?></strong></td>
                            <td>
                                <strong><?= htmlspecialchars($app['patient_name']) ?></strong>
                                <span style="display:block;font-size:12px;color:#64748B;">
                                    <?= htmlspecialchars($app['patient_gender']) ?> <?= $app['patient_dob'] ? '• Sinh: ' . formatDate($app['patient_dob']) : '' ?>
                                </span>
                            </td>
                            <td>
                                <?= htmlspecialchars($app['patient_phone']) ?>
                                <?php if ($app['patient_email']): ?>
                                    <span style="display:block;font-size:11.5px;color:#94A3B8;"><?= htmlspecialchars($app['patient_email']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="color:#0284C7;"><?= formatTime($app['appointment_time']) ?></strong>
                                <span style="display:block;font-size:12px;color:#64748B;"><?= htmlspecialchars($app['session_name'] ?? '') ?></span>
                            </td>
                            <td style="max-width:240px;font-size:13px;line-height:1.4;">
                                <?= htmlspecialchars(mb_strimwidth($app['symptom_description'], 0, 75, '...')) ?>
                            </td>
                            <td><?= getStatusBadge($app['status']) ?></td>
                            <td style="text-align:right;white-space:nowrap;">
                                <?php if ($app['status'] === 'IN_PROGRESS'): ?>
                                    <a href="kham-benh.php?id=<?= $app['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-stethoscope"></i> Tiếp tục khám
                                    </a>
                                <?php elseif (in_array($app['status'], ['CONFIRMED', 'CHECKED_IN'])): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="start_examination">
                                        <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fa-solid fa-door-open"></i> Vào khám ngay
                                        </button>
                                    </form>
                                <?php elseif ($app['status'] === 'COMPLETED'): ?>
                                    <a href="kham-benh.php?id=<?= $app['id'] ?>" class="btn btn-sm btn-outline-primary" title="Xem đơn thuốc & bệnh án">
                                        <i class="fa-solid fa-file-prescription"></i> Xem đơn
                                    </a>
                                <?php else: ?>
                                    <span style="font-size:12px;color:#94A3B8;">Chưa sẵn sàng</span>
                                <?php endif; ?>

                                <a href="ho-so-benh-nhan.php?phone=<?= urlencode($app['patient_phone']) ?>" class="btn btn-sm btn-outline" title="Lịch sử khám">
                                    <i class="fa-solid fa-history"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/doctor_footer.php'; ?>
