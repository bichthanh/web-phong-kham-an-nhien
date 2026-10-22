<?php
$page_title = "Bàn làm việc Bác sĩ - Xử lý lịch khám & Nhắc nhở";
$activeNav = 'dashboard';
require_once __DIR__ . '/../includes/doctor_header.php';

if (!$currentDoctor) {
    die("Tài khoản của bạn chưa được liên kết với hồ sơ bác sĩ trong hệ thống!");
}

$doctorId = $currentDoctor['id'];
$today = date('Y-m-d');
$filterDate = sanitizeInput($_GET['date'] ?? $today);
$viewAll = isset($_GET['view']) && $_GET['view'] === 'all';

// 1. XỬ LÝ DUYỆT LỊCH HẸN TRÊN WEB (PENDING -> CONFIRMED)
if (isset($_POST['action']) && $_POST['action'] === 'confirm_appointment') {
    $appId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    if ($appId) {
        $stmtConf = $pdo->prepare("UPDATE appointments SET status = 'CONFIRMED' WHERE id = ? AND doctor_id = ?");
        $stmtConf->execute([$appId, $doctorId]);
        setFlashMessage('success', "Đã duyệt tiếp nhận lịch hẹn khám của bệnh nhân thành công!");
        header("Location: index.php" . ($viewAll ? "?view=all" : "?date=" . urlencode($filterDate)));
        exit;
    }
}

// 2. XỬ LÝ TIẾP NHẬN VÀO BUỒNG KHÁM (-> IN_PROGRESS)
if (isset($_POST['action']) && $_POST['action'] === 'start_examination') {
    $appId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    if ($appId) {
        $stmtStart = $pdo->prepare("UPDATE appointments SET status = 'IN_PROGRESS' WHERE id = ? AND doctor_id = ?");
        $stmtStart->execute([$appId, $doctorId]);
        header("Location: kham-benh.php?id=" . $appId);
        exit;
    }
}

// 3. XỬ LÝ LƯU LỜI NHẮC NHỞ BỆNH NHÂN TRÊN WEB
if (isset($_POST['action']) && $_POST['action'] === 'save_reminder') {
    $appId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    $reminder = sanitizeInput($_POST['doctor_reminder'] ?? '');
    if ($appId) {
        $stmtRem = $pdo->prepare("UPDATE appointments SET doctor_reminder = ? WHERE id = ? AND doctor_id = ?");
        $stmtRem->execute([$reminder, $appId, $doctorId]);
        setFlashMessage('success', "Đã lưu và gửi lời nhắc nhở tới bệnh nhân thành công!");
        header("Location: index.php" . ($viewAll ? "?view=all" : "?date=" . urlencode($filterDate)));
        exit;
    }
}

// 4. XỬ LÝ HỦY LỊCH HẸN TRÊN WEB
if (isset($_POST['action']) && $_POST['action'] === 'cancel_appointment') {
    $appId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    $cancelReason = sanitizeInput($_POST['cancel_reason'] ?? 'Bệnh nhân bận lịch / Bác sĩ có ca mổ khẩn cấp');
    if ($appId) {
        $stmtSch = $pdo->prepare("SELECT schedule_id FROM appointments WHERE id = ? AND doctor_id = ?");
        $stmtSch->execute([$appId, $doctorId]);
        $schId = $stmtSch->fetchColumn();

        $stmtCan = $pdo->prepare("UPDATE appointments SET status = 'CANCELLED', doctor_reminder = CONCAT(COALESCE(doctor_reminder, ''), ' [Lý do hủy: ', ?, ']') WHERE id = ? AND doctor_id = ?");
        $stmtCan->execute([$cancelReason, $appId, $doctorId]);

        // Giải phóng slot cho ca trực
        if ($schId) {
            $pdo->prepare("UPDATE doctor_schedules SET current_booked = GREATEST(current_booked - 1, 0), status = 'AVAILABLE' WHERE id = ?")->execute([$schId]);
        }

        setFlashMessage('warning', "Đã hủy ca hẹn và giải phóng chỗ trống cho ca trực!");
        header("Location: index.php" . ($viewAll ? "?view=all" : "?date=" . urlencode($filterDate)));
        exit;
    }
}

// 5. XỬ LÝ NHANH THU TIỀN / TRẠNG THÁI THANH TOÁN TRÊN WEB
if (isset($_POST['action']) && $_POST['action'] === 'toggle_payment') {
    $appId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    $newStatus = sanitizeInput($_POST['payment_status'] ?? 'PAID');
    if ($appId) {
        $stmtPay = $pdo->prepare("UPDATE appointments SET payment_status = ?, payment_method = 'CASH' WHERE id = ? AND doctor_id = ?");
        $stmtPay->execute([$newStatus, $appId, $doctorId]);
        setFlashMessage('success', "Đã cập nhật trạng thái thanh toán thành công!");
        header("Location: index.php" . ($viewAll ? "?view=all" : "?date=" . urlencode($filterDate)));
        exit;
    }
}

// Thống kê các ca khám
$stmtStat = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status IN ('PENDING', 'CONFIRMED', 'CHECKED_IN') THEN 1 ELSE 0 END) as waiting_count,
    SUM(CASE WHEN status = 'IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress_count,
    SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) as completed_count
    FROM appointments 
    WHERE doctor_id = ? AND " . ($viewAll ? "status != 'CANCELLED'" : "appointment_date = ?"));

if ($viewAll) {
    $stmtStat->execute([$doctorId]);
} else {
    $stmtStat->execute([$doctorId, $filterDate]);
}
$stats = $stmtStat->fetch();

// Lấy danh sách bệnh nhân
$sqlApps = "SELECT a.*, ds.session_name 
            FROM appointments a
            LEFT JOIN doctor_schedules ds ON a.schedule_id = ds.id
            WHERE a.doctor_id = ? " . ($viewAll ? "AND a.status != 'CANCELLED'" : "AND a.appointment_date = ?") . "
            ORDER BY a.appointment_date ASC, a.appointment_time ASC";
$stmtApps = $pdo->prepare($sqlApps);

if ($viewAll) {
    $stmtApps->execute([$doctorId]);
} else {
    $stmtApps->execute([$doctorId, $filterDate]);
}
$appointments = $stmtApps->fetchAll();

$flash = getFlashMessage();
?>

<div class="page-header" style="background:#FFF;padding:20px;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:24px;">
    <div>
        <h2 style="font-size:22px;color:#0F172A;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-stethoscope" style="color:var(--primary);"></i> 
            Bàn Khám Bác Sĩ: <?= htmlspecialchars($currentDoctor['clinic_room']) ?>
        </h2>
        <p style="color:#64748B;margin-top:4px;font-size:14px;">
            Bác sĩ phụ trách: <strong style="color:#0F172A;"><?= htmlspecialchars($currentDoctor['degree']) ?> <?= htmlspecialchars($currentDoctor['full_name']) ?></strong> • Chuyên khoa: <span class="badge badge-info"><?= htmlspecialchars($currentDoctor['specialty_name']) ?></span>
        </p>
    </div>

    <!-- BỘ LỌC NGÀY VÀ CHẾ ĐỘ XEM -->
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <a href="index.php?date=<?= date('Y-m-d') ?>" class="btn btn-sm <?= (!$viewAll && $filterDate == $today) ? 'btn-primary' : 'btn-outline' ?>">
            <i class="fa-solid fa-calendar-day"></i> Hôm nay (<?= date('d/m') ?>)
        </a>
        <a href="index.php?view=all" class="btn btn-sm <?= $viewAll ? 'btn-primary' : 'btn-outline' ?>">
            <i class="fa-solid fa-list"></i> Tất cả ca cần khám
        </a>
        <form method="GET" style="display:flex;align-items:center;gap:8px;margin:0;">
            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($filterDate) ?>" onchange="this.form.submit()" style="width:150px;padding:6px 10px;font-size:13px;">
        </form>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'warning' ? 'warning' : 'danger') ?>">
        <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <div><?= htmlspecialchars($flash['message']) ?></div>
    </div>
<?php endif; ?>

<!-- KPI THỐNG KÊ TRÊN WEB -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-info">
            <h4><?= $stats['total'] ?: 0 ?></h4>
            <p><?= $viewAll ? 'Tổng lịch hẹn khám' : 'Lịch hẹn ngày ' . formatDate($filterDate) ?></p>
        </div>
        <div class="kpi-icon" style="background:#E0F2FE;color:#0284C7;"><i class="fa-solid fa-users"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4><?= $stats['waiting_count'] ?: 0 ?></h4>
            <p>Bệnh nhân chờ duyệt / khám</p>
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
            <p>Đã khám & kê đơn xong</p>
        </div>
        <div class="kpi-icon" style="background:#D1FAE5;color:#059669;"><i class="fa-solid fa-circle-check"></i></div>
    </div>
</div>

<!-- BẢNG DANH SÁCH BỆNH NHÂN & XỬ LÝ LỊCH ĐẶT -->
<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 style="font-size:17px;font-weight:700;color:#0F172A;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-clipboard-list" style="color:var(--primary);"></i> 
            Danh Sách Bệnh Nhân (<?= count($appointments) ?> bệnh nhân)
        </h3>
        <span style="font-size:12.5px;color:#64748B;">
            <i class="fa-solid fa-shield-heart" style="color:#0D9488;"></i> Quy định: Tối đa 5 bệnh nhân / ca (sáng & chiều)
        </span>
    </div>

    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr style="background:#F8FAFC;">
                        <th style="width:40px;">STT</th>
                        <th>Mã Phiếu</th>
                        <th>Bệnh Nhân</th>
                        <th>SĐT Liên Hệ</th>
                        <th>Thời Gian Khám</th>
                        <th>Triệu Chứng & Lời Nhắc Bác Sĩ</th>
                        <th>Trạng Thái</th>
                        <th>Thanh Toán</th>
                        <th style="text-align:right;min-width:200px;">Thao Tác Trực Tiếp Trên Web</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="9" style="text-align:center;padding:50px 20px;color:#94A3B8;">
                                <i class="fa-regular fa-calendar-check fa-3x" style="margin-bottom:12px;display:block;color:#CBD5E1;"></i>
                                Không có ca khám nào cần xử lý trong thời gian này.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php $idx = 1; foreach ($appointments as $app): ?>
                        <tr style="<?= $app['status'] === 'IN_PROGRESS' ? 'background:#F0FDF4;' : ($app['status'] === 'PENDING' ? 'background:#FFFBEB;' : '') ?>">
                            <td><?= $idx++ ?></td>
                            <td>
                                <strong style="color:var(--primary-dark);font-size:13.5px;"><?= htmlspecialchars($app['appointment_code']) ?></strong>
                            </td>
                            <td>
                                <strong style="font-size:14px;color:#0F172A;"><?= htmlspecialchars($app['patient_name']) ?></strong>
                                <span style="display:block;font-size:12px;color:#64748B;">
                                    <?= htmlspecialchars($app['patient_gender']) ?> <?= $app['patient_dob'] ? '• ' . formatDate($app['patient_dob']) : '' ?>
                                </span>
                            </td>
                            <td>
                                <a href="tel:<?= htmlspecialchars($app['patient_phone']) ?>" style="font-weight:600;color:#0284C7;">
                                    <i class="fa-solid fa-phone" style="font-size:11px;"></i> <?= htmlspecialchars($app['patient_phone']) ?>
                                </a>
                            </td>
                            <td>
                                <strong style="color:#0F172A;display:block;font-size:13.5px;">
                                    <i class="fa-regular fa-clock" style="color:#0D9488;"></i> <?= formatTime($app['appointment_time']) ?>
                                </strong>
                                <span style="font-size:12px;color:#64748B;">
                                    Ngày: <strong><?= formatDate($app['appointment_date']) ?></strong>
                                </span>
                                <span style="display:block;font-size:11px;color:#94A3B8;">
                                    <?= htmlspecialchars($app['session_name'] ?? '') ?>
                                </span>
                            </td>
                            <td style="max-width:280px;">
                                <!-- Triệu chứng -->
                                <div style="font-size:12.5px;color:#334155;line-height:1.4;margin-bottom:6px;">
                                    <strong>Lý do khám:</strong> <?= htmlspecialchars($app['symptom_description']) ?>
                                </div>

                                <!-- Lời nhắc nhở của bác sĩ nếu có -->
                                <?php if (!empty($app['doctor_reminder'])): ?>
                                    <div style="background:#FEF3C7;border-left:3px solid #D97706;padding:6px 10px;border-radius:4px;font-size:12px;color:#92400E;">
                                        <strong><i class="fa-solid fa-bell"></i> Lời nhắc bác sĩ:</strong> <?= htmlspecialchars($app['doctor_reminder']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= getStatusBadge($app['status']) ?>
                            </td>
                            <td>
                                <div><?= getPaymentBadge($app['payment_status']) ?></div>
                                <div style="font-size:12px;color:#DC2626;font-weight:700;margin-top:4px;">
                                    <?= formatMoney($app['amount']) ?>
                                </div>
                            </td>
                            <td style="text-align:right;white-space:nowrap;">
                                <div style="display:inline-flex;gap:6px;align-items:center;">
                                    
                                    <!-- NÚT 1: DUYỆT TIẾP NHẬN (Nếu PENDING) -->
                                    <?php if ($app['status'] === 'PENDING'): ?>
                                        <form method="POST" style="margin:0;">
                                            <input type="hidden" name="action" value="confirm_appointment">
                                            <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-info" title="Xác nhận duyệt lịch hẹn">
                                                <i class="fa-solid fa-check"></i> Duyệt lịch
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- NÚT 2: VÀO KHÁM & KÊ ĐƠN THUỐC -->
                                    <?php if (in_array($app['status'], ['CONFIRMED', 'CHECKED_IN'])): ?>
                                        <form method="POST" style="margin:0;">
                                            <input type="hidden" name="action" value="start_examination">
                                            <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-success" title="Mời bệnh nhân vào buồng khám">
                                                <i class="fa-solid fa-door-open"></i> Vào khám ngay
                                            </button>
                                        </form>
                                    <?php elseif ($app['status'] === 'IN_PROGRESS'): ?>
                                        <a href="kham-benh.php?id=<?= $app['id'] ?>" class="btn btn-sm btn-primary" style="animation:pulse 2s infinite;">
                                            <i class="fa-solid fa-stethoscope"></i> Tiếp tục kê thuốc
                                        </a>
                                    <?php elseif ($app['status'] === 'COMPLETED'): ?>
                                        <a href="kham-benh.php?id=<?= $app['id'] ?>" class="btn btn-sm btn-outline-primary" title="Xem lại đơn thuốc đã kê">
                                            <i class="fa-solid fa-file-prescription"></i> Xem đơn
                                        </a>

                                        <?php if ($app['payment_status'] !== 'PAID'): ?>
                                            <form method="POST" style="margin:0;display:inline;">
                                                <input type="hidden" name="action" value="toggle_payment">
                                                <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                                                <input type="hidden" name="payment_status" value="PAID">
                                                <button type="submit" class="btn btn-sm btn-success" title="Xác nhận đã thu tiền ca này">
                                                    <i class="fa-solid fa-cash-register"></i> Thu tiền
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <!-- NÚT 3: NHẮC NHỞ BỆNH NHÂN TRÊN WEB (MODAL) -->
                                    <?php if ($app['status'] !== 'CANCELLED'): ?>
                                        <button type="button" class="btn btn-sm btn-warning btn-open-reminder" 
                                                data-id="<?= $app['id'] ?>" 
                                                data-code="<?= htmlspecialchars($app['appointment_code']) ?>"
                                                data-name="<?= htmlspecialchars($app['patient_name']) ?>"
                                                data-reminder="<?= htmlspecialchars($app['doctor_reminder'] ?? '') ?>"
                                                title="Gửi lời nhắc nhở cho bệnh nhân trên web">
                                            <i class="fa-solid fa-bell"></i> Nhắc nhở
                                        </button>
                                    <?php endif; ?>

                                    <!-- NÚT 4: HỦY CA NẾU CẦN -->
                                    <?php if (in_array($app['status'], ['PENDING', 'CONFIRMED'])): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-open-cancel"
                                                data-id="<?= $app['id'] ?>"
                                                data-name="<?= htmlspecialchars($app['patient_name']) ?>"
                                                title="Hủy ca hẹn">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    <?php endif; ?>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL GỬI LỜI NHẮC NHỞ CHO BỆNH NHÂN TRÊN WEB -->
<div id="reminder_modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#FFF;border-radius:12px;max-width:520px;width:90%;padding:25px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h4 style="margin:0;font-size:18px;color:#0F172A;display:flex;align-items:center;gap:8px;">
                <i class="fa-solid fa-bell" style="color:#EAB308;"></i> Gửi Lời Nhắc Nhở Cho Bệnh Nhân
            </h4>
            <button type="button" onclick="closeReminderModal()" style="background:none;border:none;font-size:18px;cursor:pointer;color:#94A3B8;">&times;</button>
        </div>

        <p style="font-size:13.5px;color:#64748B;margin-bottom:14px;">
            Bệnh nhân: <strong id="modal_patient_name" style="color:#0F172A;"></strong> • Mã: <span id="modal_app_code" class="badge badge-info"></span>
        </p>

        <form method="POST" action="index.php">
            <input type="hidden" name="action" value="save_reminder">
            <input type="hidden" name="appointment_id" id="modal_app_id" value="">

            <div class="form-group">
                <label class="form-label">Nội dung lời nhắc nhở của bác sĩ: <span class="required">*</span></label>
                <textarea name="doctor_reminder" id="modal_reminder_text" class="form-control" rows="4" required
                          placeholder="Ví dụ: Bệnh nhân vui lòng nhịn ăn sáng từ 22h tối hôm trước để lấy máu xét nghiệm đường huyết và mỡ máu. Có mặt trước giờ hẹn 15 phút tại P.101..."></textarea>
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:12px;color:#475569;font-weight:600;display:block;margin-bottom:6px;">Gợi ý mẫu nhắc nhở nhanh:</label>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    <button type="button" class="btn btn-sm btn-outline" onclick="fillReminder('Vui lòng nhịn ăn sáng trước khi đến để làm xét nghiệm máu chính xác.')">Nhịn ăn sáng</button>
                    <button type="button" class="btn btn-sm btn-outline" onclick="fillReminder('Mang theo các kết quả xét nghiệm, đơn thuốc cũ và thẻ BHYT.')">Mang đơn thuốc cũ</button>
                    <button type="button" class="btn btn-sm btn-outline" onclick="fillReminder('Có mặt trước 15 phút tại quầy lễ tân tầng 1 để đo huyết áp ban đầu.')">Đến sớm 15 phút</button>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeReminderModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane"></i> Lưu & Gửi Nhắc Nhở
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL HỦY CA HẸN -->
<div id="cancel_modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#FFF;border-radius:12px;max-width:460px;width:90%;padding:25px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
        <h4 style="margin:0 0 12px 0;font-size:18px;color:#DC2626;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-triangle-exclamation"></i> Xác Nhận Hủy Ca Hẹn Khám
        </h4>
        <p style="font-size:13.5px;color:#64748B;margin-bottom:14px;">
            Bạn có chắc chắn muốn hủy ca hẹn của bệnh nhân <strong id="cancel_patient_name" style="color:#0F172A;"></strong>? Chỗ trống này sẽ được hoàn lại cho ca trực.
        </p>

        <form method="POST" action="index.php">
            <input type="hidden" name="action" value="cancel_appointment">
            <input type="hidden" name="appointment_id" id="cancel_app_id" value="">

            <div class="form-group">
                <label class="form-label">Lý do hủy ca hẹn:</label>
                <input type="text" name="cancel_reason" class="form-control" value="Bệnh nhân báo bận không đến" required>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeCancelModal()">Không hủy</button>
                <button type="submit" class="btn btn-danger">
                    <i class="fa-solid fa-trash"></i> Xác Nhận Hủy
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function closeReminderModal() {
    document.getElementById('reminder_modal').style.display = 'none';
}

function fillReminder(text) {
    document.getElementById('modal_reminder_text').value = text;
}

function closeCancelModal() {
    document.getElementById('cancel_modal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    // Xử lý mở Modal nhắc nhở
    document.querySelectorAll('.btn-open-reminder').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modal_app_id').value = this.getAttribute('data-id');
            document.getElementById('modal_app_code').innerText = this.getAttribute('data-code');
            document.getElementById('modal_patient_name').innerText = this.getAttribute('data-name');
            document.getElementById('modal_reminder_text').value = this.getAttribute('data-reminder') || '';
            document.getElementById('reminder_modal').style.display = 'flex';
        });
    });

    // Xử lý mở Modal hủy ca
    document.querySelectorAll('.btn-open-cancel').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('cancel_app_id').value = this.getAttribute('data-id');
            document.getElementById('cancel_patient_name').innerText = this.getAttribute('data-name');
            document.getElementById('cancel_modal').style.display = 'flex';
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/doctor_footer.php'; ?>
