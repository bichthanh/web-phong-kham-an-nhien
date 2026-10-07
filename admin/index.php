<?php
$page_title = "Bảng điều khiển quản trị";
$activeNav = 'dashboard';
require_once __DIR__ . '/../includes/admin_header.php';

$today = date('Y-m-d');

// Xử lý duyệt nhanh lịch hẹn từ Dashboard
if (isset($_POST['action']) && $_POST['action'] === 'quick_confirm_app') {
    $appId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    if ($appId) {
        $pdo->prepare("UPDATE appointments SET status = 'CONFIRMED' WHERE id = ?")->execute([$appId]);
        setFlashMessage('success', 'Đã duyệt xác nhận lịch hẹn thành công!');
        header("Location: index.php");
        exit;
    }
}

// Xử lý duyệt yêu cầu đổi ca của Bác sĩ
if (isset($_POST['action']) && $_POST['action'] === 'handle_schedule_request') {
    $reqId = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
    $decision = sanitizeInput($_POST['decision'] ?? 'APPROVED'); // APPROVED or REJECTED

    if ($reqId) {
        $pdo->beginTransaction();
        $stmtR = $pdo->prepare("SELECT * FROM schedule_change_requests WHERE id = ?");
        $stmtR->execute([$reqId]);
        $req = $stmtR->fetch();

        if ($req) {
            $pdo->prepare("UPDATE schedule_change_requests SET status = ? WHERE id = ?")->execute([$decision, $reqId]);
            if ($decision === 'APPROVED' && $req['request_type'] === 'CANCEL') {
                // Tắt ca trực
                $pdo->prepare("UPDATE doctor_schedules SET status = 'OFF' WHERE id = ?")->execute([$req['schedule_id']]);
            }
            $pdo->commit();
            setFlashMessage('success', "Đã xử lý yêu cầu đổi ca trực thành công ({$decision})!");
            header("Location: index.php");
            exit;
        }
    }
}

// KPI Thống kê
$totalDoctors = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
$totalPatients = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'ROLE_PATIENT'")->fetchColumn();
$todayApps = $pdo->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = '$today'")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(amount) FROM appointments WHERE payment_status = 'PAID'")->fetchColumn() ?: 0;

// Thống kê lịch hẹn theo chuyên khoa cho biểu đồ
$specStats = $pdo->query("SELECT s.name, COUNT(a.id) as total 
                          FROM specialties s 
                          LEFT JOIN doctors d ON s.id = d.specialty_id 
                          LEFT JOIN appointments a ON d.id = a.doctor_id 
                          GROUP BY s.id ORDER BY total DESC LIMIT 6")->fetchAll();
$specLabels = array_column($specStats, 'name');
$specCounts = array_column($specStats, 'total');

// Thống kê theo trạng thái
$statusStats = $pdo->query("SELECT status, COUNT(*) as count FROM appointments GROUP BY status")->fetchAll();
$statusMap = [];
foreach ($statusStats as $st) {
    $statusMap[$st['status']] = $st['count'];
}

// Lấy 5 phiếu hẹn mới nhất đang chờ duyệt
$pendingApps = $pdo->query("SELECT a.*, d.full_name as doctor_name, s.name as specialty_name 
                            FROM appointments a 
                            JOIN doctors d ON a.doctor_id = d.id 
                            JOIN specialties s ON d.specialty_id = s.id 
                            WHERE a.status = 'PENDING' 
                            ORDER BY a.created_at DESC LIMIT 5")->fetchAll();

// Lấy các yêu cầu đổi ca đang chờ duyệt
$pendingReqs = $pdo->query("SELECT r.*, d.full_name as doctor_name, s.work_date, s.session_name 
                            FROM schedule_change_requests r 
                            JOIN doctors d ON r.doctor_id = d.id 
                            JOIN doctor_schedules s ON r.schedule_id = s.id 
                            WHERE r.status = 'PENDING' 
                            ORDER BY r.created_at DESC")->fetchAll();

$flash = getFlashMessage();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-chart-line" style="color:var(--primary);"></i> Tổng Quan Điều Hành Phòng Khám An Nhiên</h2>
        <p>Hệ thống giám sát chỉ số khám chữa bệnh, lịch hẹn trực tuyến và nhân sự y tế</p>
    </div>
    <div>
        <span class="badge badge-purple" style="font-size:13px;padding:6px 14px;">
            <i class="fa-regular fa-clock"></i> Hôm nay: <?= date('d/m/Y') ?>
        </span>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <div><?= htmlspecialchars($flash['message']) ?></div>
    </div>
<?php endif; ?>

<!-- 4 THẺ KPI -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-info">
            <h4><?= $totalDoctors ?></h4>
            <p>Bác sĩ chuyên khoa</p>
        </div>
        <div class="kpi-icon" style="background:#E0F2FE;color:#0284C7;"><i class="fa-solid fa-user-doctor"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4><?= $totalPatients ?></h4>
            <p>Bệnh nhân đăng ký</p>
        </div>
        <div class="kpi-icon" style="background:#D1FAE5;color:#059669;"><i class="fa-solid fa-users"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4><?= $todayApps ?></h4>
            <p>Lịch hẹn hôm nay</p>
        </div>
        <div class="kpi-icon" style="background:#FEF3C7;color:#D97706;"><i class="fa-solid fa-calendar-check"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4><?= formatMoney($totalRevenue) ?></h4>
            <p>Doanh thu đã quyết toán</p>
        </div>
        <div class="kpi-icon" style="background:#F3E8FF;color:#7E22CE;"><i class="fa-solid fa-coins"></i></div>
    </div>
</div>

<!-- BIỂU ĐỒ TRỰC QUAN CHART.JS -->
<div style="display:grid;grid-template-columns:1.2fr 0.8fr;gap:24px;margin-bottom:30px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-chart-column" style="color:var(--primary);margin-right:8px;"></i> Lượt Đặt Khám Theo Chuyên Khoa</h3>
        </div>
        <div class="card-body">
            <canvas id="specialtyChart" height="150"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-chart-pie" style="color:var(--primary);margin-right:8px;"></i> Tỷ Lệ Trạng Thái Lịch Hẹn</h3>
        </div>
        <div class="card-body">
            <canvas id="statusChart" height="150"></canvas>
        </div>
    </div>
</div>

<!-- PHIẾU HẸN CẦN DUYỆT & ĐƠN XIN ĐỔI CA TRỰC -->
<div style="display:grid;grid-template-columns:1.4fr 1fr;gap:24px;">
    <!-- BẢNG LỊCH HẸN MỚI CHỜ DUYỆT -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-bell" style="color:var(--warning);margin-right:8px;"></i> Lịch Hẹn Mới Gửi (Chờ Duyệt)</h3>
            <a href="lich-hen.php?status=PENDING" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mã Phiếu</th>
                            <th>Bệnh Nhân</th>
                            <th>Bác Sĩ</th>
                            <th>Ngày Khám</th>
                            <th style="text-align:right;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingApps)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;padding:30px;color:#94A3B8;">
                                    <i class="fa-solid fa-check fa-2x" style="margin-bottom:6px;display:block;"></i>
                                    Không có phiếu hẹn nào đang chờ duyệt!
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($pendingApps as $pa): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($pa['appointment_code']) ?></strong></td>
                                <td>
                                    <strong><?= htmlspecialchars($pa['patient_name']) ?></strong>
                                    <span style="display:block;font-size:12px;color:#64748B;"><?= htmlspecialchars($pa['patient_phone']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($pa['doctor_name']) ?></td>
                                <td><?= formatDate($pa['appointment_date']) ?> (<?= formatTime($pa['appointment_time']) ?>)</td>
                                <td style="text-align:right;white-space:nowrap;">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="quick_confirm_app">
                                        <input type="hidden" name="appointment_id" value="<?= $pa['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fa-solid fa-check"></i> Duyệt
                                        </button>
                                    </form>
                                    <a href="lich-hen.php?code=<?= urlencode($pa['appointment_code']) ?>" class="btn btn-sm btn-outline">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ĐƠN XIN ĐỔI CA CỦA BÁC SĨ (UC-AD-02) -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-user-clock" style="color:var(--primary);margin-right:8px;"></i> Bác Sĩ Xin Đổi/Nghỉ Ca Trực</h3>
            <span class="badge badge-warning"><?= count($pendingReqs) ?> Đơn</span>
        </div>
        <div class="card-body" style="padding:14px;">
            <?php if (empty($pendingReqs)): ?>
                <p style="text-align:center;color:#94A3B8;padding:25px 0;">Không có đơn xin điều chỉnh ca trực nào.</p>
            <?php else: ?>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <?php foreach ($pendingReqs as $pr): ?>
                        <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:8px;padding:12px;">
                            <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                                <strong style="color:var(--primary-dark);"><?= htmlspecialchars($pr['doctor_name']) ?></strong>
                                <span class="badge badge-danger"><?= $pr['request_type'] === 'CANCEL' ? 'Xin nghỉ ca' : 'Xin đổi ca' ?></span>
                            </div>
                            <p style="font-size:12.5px;color:#475569;margin-bottom:6px;">
                                Ca trực: <strong><?= formatDate($pr['work_date']) ?> (<?= htmlspecialchars($pr['session_name']) ?>)</strong><br>
                                Lý do: <em><?= htmlspecialchars($pr['reason']) ?></em>
                            </p>
                            <div style="display:flex;gap:8px;justify-content:flex-end;">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="handle_schedule_request">
                                    <input type="hidden" name="request_id" value="<?= $pr['id'] ?>">
                                    <input type="hidden" name="decision" value="APPROVED">
                                    <button type="submit" class="btn btn-sm btn-success">Duyệt cho nghỉ</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="handle_schedule_request">
                                    <input type="hidden" name="request_id" value="<?= $pr['id'] ?>">
                                    <input type="hidden" name="decision" value="REJECTED">
                                    <button type="submit" class="btn btn-sm btn-outline">Từ chối</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- KHỞI TẠO BIỂU ĐỒ CHART.JS -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Biểu đồ Chuyên khoa (Bar Chart)
    const ctxSpec = document.getElementById('specialtyChart').getContext('2d');
    new Chart(ctxSpec, {
        type: 'bar',
        data: {
            labels: <?= json_encode($specLabels, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                label: 'Số lượt đặt khám',
                data: <?= json_encode($specCounts) ?>,
                backgroundColor: '#0D9488',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    // 2. Biểu đồ Trạng thái (Doughnut Chart)
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Chờ duyệt', 'Đã xác nhận', 'Tiếp đón', 'Đang khám', 'Hoàn thành', 'Đã hủy'],
            datasets: [{
                data: [
                    <?= (int)($statusMap['PENDING'] ?? 0) ?>,
                    <?= (int)($statusMap['CONFIRMED'] ?? 0) ?>,
                    <?= (int)($statusMap['CHECKED_IN'] ?? 0) ?>,
                    <?= (int)($statusMap['IN_PROGRESS'] ?? 0) ?>,
                    <?= (int)($statusMap['COMPLETED'] ?? 0) ?>,
                    <?= (int)($statusMap['CANCELLED'] ?? 0) ?>
                ],
                backgroundColor: ['#F59E0B', '#0284C7', '#9333EA', '#6366F1', '#10B981', '#EF4444']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
