<?php
$page_title = "Lịch trực cá nhân";
$activeNav = 'lich_truc';
require_once __DIR__ . '/../includes/doctor_header.php';

$doctorId = $currentDoctor['id'];
$today = date('Y-m-d');

// Xử lý gửi đơn yêu cầu đổi/nghỉ ca trực (UC-DOC-02 Extend Flow)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_change') {
    $scheduleId = filter_input(INPUT_POST, 'schedule_id', FILTER_VALIDATE_INT);
    $reqType = sanitizeInput($_POST['request_type'] ?? 'CANCEL');
    $reason = sanitizeInput($_POST['reason'] ?? '');

    if (!$scheduleId || !$reason) {
        setFlashMessage('error', 'Vui lòng chọn ca trực và nêu rõ lý do xin điều chỉnh!');
    } else {
        $stmtReq = $pdo->prepare("INSERT INTO schedule_change_requests (doctor_id, schedule_id, request_type, reason) VALUES (?, ?, ?, ?)");
        $stmtReq->execute([$doctorId, $scheduleId, $reqType, $reason]);
        setFlashMessage('success', 'Đơn xin điều chỉnh ca trực đã được gửi tới Ban quản trị phòng khám An Nhiên để xét duyệt!');
        header("Location: lich-truc.php");
        exit;
    }
}

// Lấy danh sách lịch trực của bác sĩ từ hôm nay trở đi
$stmtS = $pdo->prepare("SELECT * FROM doctor_schedules 
                        WHERE doctor_id = ? AND work_date >= ? 
                        ORDER BY work_date ASC, start_time ASC");
$stmtS->execute([$doctorId, $today]);
$schedules = $stmtS->fetchAll();

// Lấy các đơn xin đổi ca đã gửi
$stmtR = $pdo->prepare("SELECT r.*, s.work_date, s.session_name 
                        FROM schedule_change_requests r 
                        JOIN doctor_schedules s ON r.schedule_id = s.id 
                        WHERE r.doctor_id = ? 
                        ORDER BY r.created_at DESC LIMIT 5");
$stmtR->execute([$doctorId]);
$myRequests = $stmtR->fetchAll();

$flash = getFlashMessage();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-calendar-days" style="color:var(--primary);"></i> Lịch Làm Việc & Ca Trực Cá Nhân</h2>
        <p>Bác sĩ: <strong><?= htmlspecialchars($currentDoctor['full_name']) ?></strong> • Phòng: <?= htmlspecialchars($currentDoctor['clinic_room']) ?></p>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <div><?= htmlspecialchars($flash['message']) ?></div>
    </div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1.6fr 1fr;gap:24px;">
    <!-- CỘT 1: BẢNG LỊCH TRỰC CÁ NHÂN -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-clipboard-user" style="color:var(--primary);margin-right:8px;"></i> Các Ca Trực Trong 7 Ngày Tới</h3>
            <span class="badge badge-info"><?= count($schedules) ?> Ca trực</span>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ngày Trực</th>
                            <th>Ca / Khung Giờ</th>
                            <th>Đã Đặt / Tối Đa</th>
                            <th>Trạng Thái</th>
                            <th style="text-align:right;">Gửi Đơn Xin Đổi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $s): ?>
                            <tr>
                                <td>
                                    <strong style="color:#0F172A;"><?= formatDate($s['work_date']) ?></strong>
                                    <span style="display:block;font-size:12px;color:#64748B;">
                                        <?= date('l', strtotime($s['work_date'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong style="color:var(--primary);"><?= htmlspecialchars($s['session_name']) ?></strong>
                                    <span style="display:block;font-size:12px;color:#64748B;">
                                        <?= formatTime($s['start_time']) ?> - <?= formatTime($s['end_time']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= $s['current_booked'] ?></strong> / <?= $s['max_patients'] ?> BN
                                </td>
                                <td>
                                    <?php if ($s['status'] === 'AVAILABLE'): ?>
                                        <span class="badge badge-success">Đang mở ca</span>
                                    <?php elseif ($s['status'] === 'FULL'): ?>
                                        <span class="badge badge-warning">Đã kín chỗ</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Nghỉ trực</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right;">
                                    <button type="button" class="btn btn-sm btn-outline" 
                                            onclick="selectScheduleForChange(<?= $s['id'] ?>, '<?= formatDate($s['work_date']) ?> - <?= htmlspecialchars($s['session_name']) ?>')">
                                        <i class="fa-solid fa-paper-plane"></i> Xin đổi
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CỘT 2: FORM XIN ĐIỀU CHỈNH CA TRỰC -->
    <div>
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <h3><i class="fa-solid fa-envelope-open-text" style="color:var(--primary);margin-right:8px;"></i> Đơn Xin Đổi / Hủy Ca Trực</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="lich-truc.php">
                    <input type="hidden" name="action" value="request_change">

                    <div class="form-group">
                        <label class="form-label">Chọn ca trực cần điều chỉnh <span class="required">*</span></label>
                        <select name="schedule_id" id="req_schedule_id" class="form-select" required>
                            <option value="">-- Chọn ca trực --</option>
                            <?php foreach ($schedules as $s): ?>
                                <option value="<?= $s['id'] ?>">
                                    <?= formatDate($s['work_date']) ?> - <?= htmlspecialchars($s['session_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Loại yêu cầu</label>
                        <select name="request_type" class="form-select">
                            <option value="CANCEL">Xin nghỉ ca trực (Đột xuất / Việc riêng)</option>
                            <option value="SWAP">Xin hoán đổi ca sang ngày khác</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Lý do điều chỉnh cụ thể <span class="required">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Nêu rõ lý do để ban quản lý sắp xếp nhân sự thay thế..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        <i class="fa-solid fa-paper-plane"></i> Gửi Yêu Cầu Tới Admin
                    </button>
                </form>
            </div>
        </div>

        <!-- LỊCH SỬ ĐƠN YÊU CẦU -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-clock-rotate-left" style="color:var(--primary);margin-right:8px;"></i> Các Đơn Đã Gửi Gần Đây</h3>
            </div>
            <div class="card-body" style="padding:14px;">
                <?php if (empty($myRequests)): ?>
                    <p style="font-size:13px;color:#94A3B8;text-align:center;">Chưa có yêu cầu nào.</p>
                <?php else: ?>
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <?php foreach ($myRequests as $mr): ?>
                            <div style="padding:10px;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
                                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                                    <strong><?= formatDate($mr['work_date']) ?> (<?= htmlspecialchars($mr['session_name']) ?>)</strong>
                                    <?php if ($mr['status'] === 'APPROVED'): ?>
                                        <span class="badge badge-success">Đã duyệt</span>
                                    <?php elseif ($mr['status'] === 'REJECTED'): ?>
                                        <span class="badge badge-danger">Từ chối</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Chờ duyệt</span>
                                    <?php endif; ?>
                                </div>
                                <p style="color:#64748B;"><?= htmlspecialchars($mr['reason']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function selectScheduleForChange(id, label) {
    const sel = document.getElementById('req_schedule_id');
    if (sel) {
        sel.value = id;
        sel.scrollIntoView({ behavior: 'smooth' });
    }
}
</script>

<?php require_once __DIR__ . '/../includes/doctor_footer.php'; ?>
