<?php
$page_title = "Thống kê - Báo cáo";
$activeNav = 'bao_cao';
require_once __DIR__ . '/../includes/admin_header.php';

$reportType = sanitizeInput($_GET['type'] ?? 'doctors'); // doctors, patients, appointments
$fromDate = sanitizeInput($_GET['from_date'] ?? date('Y-m-01'));
$toDate = sanitizeInput($_GET['to_date'] ?? date('Y-m-d'));

// 1. Thống kê theo Bác sĩ
$doctorReport = $pdo->query("SELECT d.id, d.full_name, d.degree, s.name as specialty_name,
                                    COUNT(a.id) as total_appointments,
                                    SUM(CASE WHEN a.status = 'COMPLETED' THEN 1 ELSE 0 END) as completed_count,
                                    SUM(CASE WHEN a.payment_status = 'PAID' THEN a.amount ELSE 0 END) as total_revenue
                             FROM doctors d
                             JOIN specialties s ON d.specialty_id = s.id
                             LEFT JOIN appointments a ON d.id = a.doctor_id AND a.appointment_date BETWEEN '$fromDate' AND '$toDate'
                             GROUP BY d.id
                             ORDER BY total_appointments DESC")->fetchAll();

// 2. Thống kê theo Trạng thái Lịch hẹn
$appointmentReport = $pdo->query("SELECT a.status, COUNT(*) as count, SUM(a.amount) as total_amount 
                                  FROM appointments a 
                                  WHERE a.appointment_date BETWEEN '$fromDate' AND '$toDate'
                                  GROUP BY a.status")->fetchAll();

// 3. Thống kê Bệnh nhân
$patientReport = $pdo->query("SELECT u.id, u.full_name, u.phone, u.email, u.created_at,
                                     COUNT(a.id) as total_booked,
                                     SUM(CASE WHEN a.status = 'COMPLETED' THEN 1 ELSE 0 END) as total_completed
                              FROM users u
                              LEFT JOIN appointments a ON (u.id = a.patient_id OR u.phone = a.patient_phone)
                              WHERE u.role = 'ROLE_PATIENT'
                              GROUP BY u.id
                              ORDER BY total_booked DESC")->fetchAll();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-file-waveform" style="color:var(--primary);"></i> Thống Kê & Kết Xuất Báo Cáo Y Tế</h2>
        <p>Phân tích hiệu suất hoạt động phòng khám An Nhiên theo tiêu chí Bác sĩ, Bệnh nhân và Lịch hẹn</p>
    </div>
    <button type="button" onclick="window.print()" class="btn btn-outline">
        <i class="fa-solid fa-print"></i> In / Xuất Báo Cáo
    </button>
</div>

<!-- FORM CHỌN TIÊU CHÍ VÀ THỜI GIAN (ACTIVITY DIAGRAM 3.21) -->
<div class="card no-print" style="margin-bottom:24px;">
    <div class="card-body" style="padding:18px 24px;">
        <form method="GET" action="bao-cao.php" style="display:flex;gap:16px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:200px;">
                <label class="form-label" style="font-size:13px;">Phân loại muốn thống kê:</label>
                <select name="type" class="form-select">
                    <option value="doctors" <?= $reportType === 'doctors' ? 'selected' : '' ?>>1. Thống kê theo Bác sĩ chuyên khoa</option>
                    <option value="appointments" <?= $reportType === 'appointments' ? 'selected' : '' ?>>2. Thống kê tình hình Lịch hẹn</option>
                    <option value="patients" <?= $reportType === 'patients' ? 'selected' : '' ?>>3. Thống kê danh sách Bệnh nhân</option>
                </select>
            </div>

            <div style="width:160px;">
                <label class="form-label" style="font-size:13px;">Từ ngày:</label>
                <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($fromDate) ?>">
            </div>

            <div style="width:160px;">
                <label class="form-label" style="font-size:13px;">Đến ngày:</label>
                <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($toDate) ?>">
            </div>

            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-chart-simple"></i> Xem Báo Cáo</button>
        </form>
    </div>
</div>

<!-- KẾT QUẢ BÁO CÁO -->
<?php if ($reportType === 'doctors'): ?>
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-user-doctor" style="color:var(--primary);margin-right:8px;"></i> Báo Cáo Hiệu Suất Khám Của Bác Sĩ (Từ <?= formatDate($fromDate) ?> đến <?= formatDate($toDate) ?>)</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Bác Sĩ</th>
                            <th>Học Vị / Chức Danh</th>
                            <th>Chuyên Khoa</th>
                            <th>Tổng Số Lịch Hẹn</th>
                            <th>Ca Đã Hoàn Thành</th>
                            <th>Tỷ Lệ Hoàn Tất</th>
                            <th style="text-align:right;">Doanh Thu Phí Khám</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doctorReport as $dr): 
                            $rate = $dr['total_appointments'] > 0 ? round(($dr['completed_count'] / $dr['total_appointments']) * 100, 1) : 0;
                        ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($dr['full_name']) ?></strong></td>
                                <td><?= htmlspecialchars($dr['degree']) ?></td>
                                <td><span class="badge badge-info"><?= htmlspecialchars($dr['specialty_name']) ?></span></td>
                                <td><strong><?= $dr['total_appointments'] ?></strong> lượt</td>
                                <td><strong style="color:#059669;"><?= $dr['completed_count'] ?></strong> ca</td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div style="flex:1;background:#E2E8F0;height:8px;border-radius:4px;overflow:hidden;width:70px;">
                                            <div style="background:var(--primary);height:100%;width:<?= $rate ?>%;"></div>
                                        </div>
                                        <span style="font-size:12px;font-weight:700;"><?= $rate ?>%</span>
                                    </div>
                                </td>
                                <td style="text-align:right;"><strong style="color:#DC2626;"><?= formatMoney($dr['total_revenue']) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($reportType === 'appointments'): ?>
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-calendar-check" style="color:var(--primary);margin-right:8px;"></i> Báo Cáo Phân Bổ Trạng Thái Lịch Hẹn (Từ <?= formatDate($fromDate) ?> đến <?= formatDate($toDate) ?>)</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Trạng Thái Lịch Hẹn</th>
                            <th>Số Lượng Phiếu</th>
                            <th>Tỷ Lệ</th>
                            <th style="text-align:right;">Tổng Giá Trị</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalA = array_sum(array_column($appointmentReport, 'count')) ?: 1;
                        foreach ($appointmentReport as $ar): 
                            $pct = round(($ar['count'] / $totalA) * 100, 1);
                        ?>
                            <tr>
                                <td><?= getStatusBadge($ar['status']) ?></td>
                                <td><strong><?= $ar['count'] ?></strong> lượt</td>
                                <td><?= $pct ?>%</td>
                                <td style="text-align:right;"><strong><?= formatMoney($ar['total_amount']) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-users" style="color:var(--primary);margin-right:8px;"></i> Báo Cáo Lưu Lượng Bệnh Nhân</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Bệnh Nhân</th>
                            <th>Số Điện Thoại</th>
                            <th>Email</th>
                            <th>Ngày Đăng Ký TK</th>
                            <th>Tổng Lượt Đặt Khám</th>
                            <th>Lượt Đã Khám Thành Công</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patientReport as $pr): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($pr['full_name']) ?></strong></td>
                                <td><?= htmlspecialchars($pr['phone']) ?></td>
                                <td><?= htmlspecialchars($pr['email']) ?></td>
                                <td><?= formatDate($pr['created_at']) ?></td>
                                <td><strong><?= $pr['total_booked'] ?></strong> lần</td>
                                <td><strong style="color:#059669;"><?= $pr['total_completed'] ?></strong> lần</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
