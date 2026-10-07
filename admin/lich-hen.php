<?php
$page_title = "Quản lý và theo dõi lịch hẹn";
$activeNav = 'lich_hen';
require_once __DIR__ . '/../includes/admin_header.php';

$filterStatus = sanitizeInput($_GET['status'] ?? '');
$search = sanitizeInput($_GET['search'] ?? '');
$filterDate = sanitizeInput($_GET['date'] ?? '');

// XỬ LÝ CÁC THAO TÁC TRÊN LỊCH HẸN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $appId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);

    if ($appId) {
        if ($action === 'update_status') {
            $newStatus = sanitizeInput($_POST['status'] ?? 'CONFIRMED');
            $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?")->execute([$newStatus, $appId]);
            setFlashMessage('success', "Đã cập nhật trạng thái lịch hẹn thành '{$newStatus}'!");
        } elseif ($action === 'change_schedule') {
            // UC-AD-05 Extend: Đổi sang bác sĩ hoặc ca trực khác khi phát sinh xung đột
            $newDocId = filter_input(INPUT_POST, 'new_doctor_id', FILTER_VALIDATE_INT);
            $newDate = sanitizeInput($_POST['new_date'] ?? '');
            $newTime = sanitizeInput($_POST['new_time'] ?? '');

            if ($newDocId && $newDate && $newTime) {
                $stmtChange = $pdo->prepare("UPDATE appointments SET doctor_id = ?, appointment_date = ?, appointment_time = ?, status = 'CONFIRMED' WHERE id = ?");
                $stmtChange->execute([$newDocId, $newDate, $newTime, $appId]);
                setFlashMessage('success', "Đã thỏa thuận và dời lịch hẹn thành công sang ngày {$newDate}!");
            }
        } elseif ($action === 'delete_appointment') {
            $pdo->prepare("DELETE FROM appointments WHERE id = ?")->execute([$appId]);
            setFlashMessage('success', "Đã xóa lịch hẹn khỏi hệ thống!");
        }
        header("Location: lich-hen.php");
        exit;
    }
}

// Xây dựng câu truy vấn
$whereClauses = ["1=1"];
$params = [];

if (!empty($filterStatus)) {
    $whereClauses[] = "a.status = ?";
    $params[] = $filterStatus;
}
if (!empty($filterDate)) {
    $whereClauses[] = "a.appointment_date = ?";
    $params[] = $filterDate;
}
if (!empty($search)) {
    $whereClauses[] = "(a.appointment_code LIKE ? OR a.patient_name LIKE ? OR a.patient_phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereSql = implode(" AND ", $whereClauses);
$sql = "SELECT a.*, d.full_name as doctor_name, d.degree as doctor_degree, d.clinic_room, s.name as specialty_name 
        FROM appointments a 
        JOIN doctors d ON a.doctor_id = d.id 
        JOIN specialties s ON d.specialty_id = s.id 
        WHERE $whereSql 
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll();

$allDoctors = $pdo->query("SELECT id, full_name, degree, clinic_room FROM doctors ORDER BY full_name ASC")->fetchAll();
$flash = getFlashMessage();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-calendar-check" style="color:var(--primary);"></i> Quản Lý & Theo Dõi Lịch Hẹn Khám Bệnh</h2>
        <p>Giám sát, duyệt phiếu, tiếp nhận tại quầy và điều phối dời lịch khi có sự cố phát sinh</p>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <div><?= htmlspecialchars($flash['message']) ?></div>
    </div>
<?php endif; ?>

<!-- THANH TÌM KIẾM VÀ BỘ LỌC -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-body" style="padding:18px 24px;">
        <form method="GET" action="lich-hen.php" style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:200px;">
                <label class="form-label" style="font-size:13px;">Tìm kiếm từ khóa:</label>
                <input type="text" name="search" class="form-control" placeholder="Mã phiếu, tên bệnh nhân, SĐT..." value="<?= htmlspecialchars($search) ?>">
            </div>

            <div style="width:160px;">
                <label class="form-label" style="font-size:13px;">Trạng thái:</label>
                <select name="status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="PENDING" <?= $filterStatus === 'PENDING' ? 'selected' : '' ?>>Chờ duyệt</option>
                    <option value="CONFIRMED" <?= $filterStatus === 'CONFIRMED' ? 'selected' : '' ?>>Đã xác nhận</option>
                    <option value="CHECKED_IN" <?= $filterStatus === 'CHECKED_IN' ? 'selected' : '' ?>>Đã tiếp đón</option>
                    <option value="IN_PROGRESS" <?= $filterStatus === 'IN_PROGRESS' ? 'selected' : '' ?>>Đang khám</option>
                    <option value="COMPLETED" <?= $filterStatus === 'COMPLETED' ? 'selected' : '' ?>>Hoàn thành</option>
                    <option value="CANCELLED" <?= $filterStatus === 'CANCELLED' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>

            <div style="width:160px;">
                <label class="form-label" style="font-size:13px;">Ngày khám:</label>
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($filterDate) ?>">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>
            <a href="lich-hen.php" class="btn btn-outline" title="Đặt lại bộ lọc"><i class="fa-solid fa-rotate-left"></i></a>
        </form>
    </div>
</div>

<!-- BẢNG DỮ LIỆU LỊCH HẸN -->
<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-table-list" style="color:var(--primary);margin-right:8px;"></i> Danh Sách Lịch Hẹn (<?= count($appointments) ?> kết quả)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã Phiếu</th>
                        <th>Bệnh Nhân</th>
                        <th>Bác Sĩ & Chuyên Khoa</th>
                        <th>Ngày & Giờ</th>
                        <th>Phòng Khám</th>
                        <th>Trạng Thái</th>
                        <th style="text-align:right;">Thao Tác Nghiệp Vụ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;padding:40px;color:#94A3B8;">
                                Không có lịch hẹn nào khớp với bộ lọc.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($appointments as $a): ?>
                        <tr>
                            <td><strong style="color:var(--primary-dark);"><?= htmlspecialchars($a['appointment_code']) ?></strong></td>
                            <td>
                                <strong><?= htmlspecialchars($a['patient_name']) ?></strong>
                                <span style="display:block;font-size:12px;color:#64748B;">
                                    SĐT: <?= htmlspecialchars($a['patient_phone']) ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($a['doctor_degree']) ?> <?= htmlspecialchars($a['doctor_name']) ?></strong>
                                <span style="display:block;font-size:12px;color:#64748B;"><?= htmlspecialchars($a['specialty_name']) ?></span>
                            </td>
                            <td>
                                <strong><?= formatDate($a['appointment_date']) ?></strong>
                                <span style="display:block;font-size:12px;color:#64748B;"><?= formatTime($a['appointment_time']) ?></span>
                            </td>
                            <td><span class="badge badge-purple"><?= htmlspecialchars($a['clinic_room']) ?></span></td>
                            <td><?= getStatusBadge($a['status']) ?></td>
                            <td style="text-align:right;white-space:nowrap;">
                                <?php if ($a['status'] === 'PENDING'): ?>
                                    <!-- Duyệt xác nhận -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                                        <input type="hidden" name="status" value="CONFIRMED">
                                        <button type="submit" class="btn btn-sm btn-success" title="Xác nhận duyệt">
                                            <i class="fa-solid fa-check"></i> Duyệt
                                        </button>
                                    </form>
                                <?php elseif ($a['status'] === 'CONFIRMED'): ?>
                                    <!-- Tiếp đón tại quầy -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                                        <input type="hidden" name="status" value="CHECKED_IN">
                                        <button type="submit" class="btn btn-sm btn-primary" title="Đánh dấu đã đến sảnh">
                                            <i class="fa-solid fa-door-open"></i> Tiếp đón
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Đổi giờ / Dời lịch (UC-AD-05 Extend Flow) -->
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="openRescheduleModal(<?= $a['id'] ?>, '<?= htmlspecialchars($a['appointment_code']) ?>', '<?= htmlspecialchars($a['patient_name']) ?>', <?= $a['doctor_id'] ?>, '<?= $a['appointment_date'] ?>', '<?= formatTime($a['appointment_time']) ?>')" 
                                        title="Dời / Đổi lịch">
                                    <i class="fa-solid fa-arrows-rotate"></i> Dời lịch
                                </button>

                                <?php if (!in_array($a['status'], ['COMPLETED', 'CANCELLED'])): ?>
                                    <!-- Hủy lịch -->
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn hủy lịch này?');">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                                        <input type="hidden" name="status" value="CANCELLED">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hủy">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <a href="<?= BASE_URL ?>/tra-cuu.php?code=<?= urlencode($a['appointment_code']) ?>" target="_blank" class="btn btn-sm btn-outline" title="Xem chi tiết & In">
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

<!-- MODAL DỜI LỊCH / THỎA THUẬN LỊCH HẸN VỚI BỆNH NHÂN (UC-AD-05 EXTEND) -->
<div id="reschedule_modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#FFF;max-width:540px;width:100%;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-xl);">
        <div style="background:var(--primary);color:#FFF;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:16px;"><i class="fa-solid fa-phone"></i> Liên Hệ Bệnh Nhân & Sắp Xếp Lịch Phù Hợp</h4>
            <button type="button" onclick="closeRescheduleModal()" style="background:none;border:none;color:#FFF;font-size:20px;cursor:pointer;">&times;</button>
        </div>
        <div style="padding:24px;">
            <p style="font-size:13.5px;color:#475569;margin-bottom:16px;">
                Trường hợp bác sĩ bận đột xuất hoặc quá tải ca trực, Admin chủ động liên hệ bệnh nhân và chọn lại ngày giờ mới dưới đây:
            </p>
            <form method="POST" action="lich-hen.php">
                <input type="hidden" name="action" value="change_schedule">
                <input type="hidden" name="appointment_id" id="resched_app_id" value="">

                <div class="form-group">
                    <label class="form-label">Bệnh nhân / Mã phiếu:</label>
                    <input type="text" id="resched_patient_info" class="form-control" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Chọn Bác sĩ mới (hoặc giữ nguyên):</label>
                    <select name="new_doctor_id" id="resched_doctor_id" class="form-select" required>
                        <?php foreach ($allDoctors as $doc): ?>
                            <option value="<?= $doc['id'] ?>">
                                <?= htmlspecialchars($doc['degree']) ?> <?= htmlspecialchars($doc['full_name']) ?> (<?= htmlspecialchars($doc['clinic_room']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Ngày khám mới:</label>
                        <input type="date" name="new_date" id="resched_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Giờ khám mới:</label>
                        <input type="time" name="new_time" id="resched_time" class="form-control" required>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
                    <button type="button" onclick="closeRescheduleModal()" class="btn btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check"></i> Lưu Lịch Khám Mới
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRescheduleModal(id, code, patient, docId, date, time) {
    document.getElementById('resched_app_id').value = id;
    document.getElementById('resched_patient_info').value = `${patient} (${code})`;
    document.getElementById('resched_doctor_id').value = docId;
    document.getElementById('resched_date').value = date;
    document.getElementById('resched_time').value = time;
    document.getElementById('reschedule_modal').style.display = 'flex';
}
function closeRescheduleModal() {
    document.getElementById('reschedule_modal').style.display = 'none';
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
