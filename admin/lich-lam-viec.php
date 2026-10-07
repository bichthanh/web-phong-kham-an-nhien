<?php
$page_title = "Quản lý lịch làm việc bác sĩ";
$activeNav = 'lich_lam_viec';
require_once __DIR__ . '/../includes/admin_header.php';

$filterDoc = filter_input(INPUT_GET, 'doctor_id', FILTER_VALIDATE_INT);
$filterDate = sanitizeInput($_GET['date'] ?? date('Y-m-d'));

// XỬ LÝ THÊM / XÓA CA TRỰC
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_schedule') {
        $docId = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
        $workDate = sanitizeInput($_POST['work_date'] ?? '');
        $session = sanitizeInput($_POST['session_name'] ?? 'Ca Sáng');
        $startTime = sanitizeInput($_POST['start_time'] ?? '07:30:00');
        $endTime = sanitizeInput($_POST['end_time'] ?? '11:30:00');
        $maxPatients = (int)($_POST['max_patients'] ?? 15);

        if ($docId && $workDate) {
            // Kiểm tra trùng ca
            $stmtChk = $pdo->prepare("SELECT COUNT(*) FROM doctor_schedules WHERE doctor_id = ? AND work_date = ? AND start_time = ?");
            $stmtChk->execute([$docId, $workDate, $startTime]);
            if ($stmtChk->fetchColumn() > 0) {
                setFlashMessage('error', 'Bác sĩ này đã có ca trực vào khung giờ này rồi!');
            } else {
                $stmtIns = $pdo->prepare("INSERT INTO doctor_schedules (doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES (?, ?, ?, ?, ?, ?, 0, 'AVAILABLE')");
                $stmtIns->execute([$docId, $workDate, $startTime, $endTime, $session, $maxPatients]);
                setFlashMessage('success', 'Xếp ca trực mới thành công!');
            }
        }
    } elseif ($action === 'delete_schedule') {
        $schedId = filter_input(INPUT_POST, 'schedule_id', FILTER_VALIDATE_INT);
        if ($schedId) {
            $pdo->prepare("DELETE FROM doctor_schedules WHERE id = ?")->execute([$schedId]);
            setFlashMessage('success', 'Đã xóa ca trực thành công!');
        }
    } elseif ($action === 'handle_request') {
        $reqId = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
        $decision = sanitizeInput($_POST['decision'] ?? 'APPROVED');
        if ($reqId) {
            $stmtR = $pdo->prepare("SELECT * FROM schedule_change_requests WHERE id = ?");
            $stmtR->execute([$reqId]);
            $req = $stmtR->fetch();
            if ($req) {
                $pdo->prepare("UPDATE schedule_change_requests SET status = ? WHERE id = ?")->execute([$decision, $reqId]);
                if ($decision === 'APPROVED' && $req['request_type'] === 'CANCEL') {
                    $pdo->prepare("UPDATE doctor_schedules SET status = 'OFF' WHERE id = ?")->execute([$req['schedule_id']]);
                }
                setFlashMessage('success', "Đã xử lý đơn của bác sĩ ({$decision})!");
            }
        }
    }
    header("Location: lich-lam-viec.php?doctor_id=" . ($filterDoc ?: '') . "&date=" . urlencode($filterDate));
    exit;
}

// Lấy danh sách bác sĩ
$doctors = $pdo->query("SELECT id, full_name, degree, clinic_room FROM doctors ORDER BY full_name ASC")->fetchAll();

// Lấy danh sách ca trực theo bộ lọc
$whereClauses = ["1=1"];
$params = [];
if ($filterDoc) {
    $whereClauses[] = "s.doctor_id = ?";
    $params[] = $filterDoc;
}
if ($filterDate) {
    $whereClauses[] = "s.work_date = ?";
    $params[] = $filterDate;
}
$whereSql = implode(" AND ", $whereClauses);

$sqlS = "SELECT s.*, d.full_name as doctor_name, d.degree as doctor_degree, d.clinic_room, sp.name as specialty_name 
         FROM doctor_schedules s 
         JOIN doctors d ON s.doctor_id = d.id 
         JOIN specialties sp ON d.specialty_id = sp.id 
         WHERE $whereSql 
         ORDER BY s.work_date ASC, s.start_time ASC";
$stmtS = $pdo->prepare($sqlS);
$stmtS->execute($params);
$schedules = $stmtS->fetchAll();

// Lấy danh sách đơn xin đổi ca chờ duyệt
$pendingRequests = $pdo->query("SELECT r.*, d.full_name as doctor_name, s.work_date, s.session_name 
                                FROM schedule_change_requests r 
                                JOIN doctors d ON r.doctor_id = d.id 
                                JOIN doctor_schedules s ON r.schedule_id = s.id 
                                WHERE r.status = 'PENDING' 
                                ORDER BY r.created_at DESC")->fetchAll();

$flash = getFlashMessage();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-calendar-days" style="color:var(--primary);"></i> Quản Lý Lịch Làm Việc & Phân Ca Trực</h2>
        <p>Xếp lịch khám định kỳ cho đội ngũ y bác sĩ, duyệt đơn đăng ký ca và giải quyết xung đột lịch</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
        <i class="fa-solid fa-plus"></i> Xếp Ca Trực Mới
    </button>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <div><?= htmlspecialchars($flash['message']) ?></div>
    </div>
<?php endif; ?>

<!-- BỘ LỌC NGÀY VÀ BÁC SĨ -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-body" style="padding:16px 24px;">
        <form method="GET" action="lich-lam-viec.php" style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:220px;">
                <label class="form-label" style="font-size:13px;">Lọc theo Bác sĩ:</label>
                <select name="doctor_id" class="form-select">
                    <option value="">-- Tất cả bác sĩ --</option>
                    <?php foreach ($doctors as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $filterDoc == $d['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['degree']) ?> <?= htmlspecialchars($d['full_name']) ?> (<?= htmlspecialchars($d['clinic_room']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="width:180px;">
                <label class="form-label" style="font-size:13px;">Ngày làm việc:</label>
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($filterDate) ?>">
            </div>

            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Lọc ca trực</button>
            <a href="lich-lam-viec.php" class="btn btn-outline" title="Tất cả"><i class="fa-solid fa-rotate-left"></i></a>
        </form>
    </div>
</div>

<!-- NẾU CÓ ĐƠN CHỜ DUYỆT TỪ BÁC SĨ -->
<?php if (!empty($pendingRequests)): ?>
    <div class="card" style="border-left:4px solid var(--warning);margin-bottom:24px;">
        <div class="card-header" style="background:#FFFBEB;">
            <h3 style="color:#B45309;"><i class="fa-solid fa-bell"></i> Có <?= count($pendingRequests) ?> Đơn Xin Đổi/Hủy Ca Cần Duyệt</h3>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:16px;">
                <?php foreach ($pendingRequests as $pr): ?>
                    <div style="padding:14px;background:#FFF;border:1px solid #FCD34D;border-radius:8px;">
                        <strong><?= htmlspecialchars($pr['doctor_name']) ?></strong>: 
                        <span class="badge badge-danger"><?= $pr['request_type'] === 'CANCEL' ? 'Xin nghỉ ca' : 'Xin đổi ca' ?></span>
                        <p style="font-size:13px;color:#475569;margin:6px 0;">
                            Ca ngày <?= formatDate($pr['work_date']) ?> (<?= htmlspecialchars($pr['session_name']) ?>)<br>
                            Lý do: <em><?= htmlspecialchars($pr['reason']) ?></em>
                        </p>
                        <div style="display:flex;gap:8px;">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="handle_request">
                                <input type="hidden" name="request_id" value="<?= $pr['id'] ?>">
                                <input type="hidden" name="decision" value="APPROVED">
                                <button type="submit" class="btn btn-sm btn-success">Chấp thuận</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="handle_request">
                                <input type="hidden" name="request_id" value="<?= $pr['id'] ?>">
                                <input type="hidden" name="decision" value="REJECTED">
                                <button type="submit" class="btn btn-sm btn-outline">Từ chối</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- BẢNG LỊCH TRỰC -->
<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-list-check" style="color:var(--primary);margin-right:8px;"></i> Danh Sách Ca Trực Đã Phân Công (<?= count($schedules) ?> ca)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ngày Làm</th>
                        <th>Bác Sĩ Phụ Trách</th>
                        <th>Chuyên Khoa / Phòng</th>
                        <th>Ca Trực</th>
                        <th>Khung Giờ</th>
                        <th>Số Lượng Bệnh Nhân</th>
                        <th>Trạng Thái</th>
                        <th style="text-align:right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schedules)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center;padding:40px;color:#94A3B8;">
                                Không có ca trực nào trong ngày đã chọn.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($schedules as $s): ?>
                        <tr>
                            <td>
                                <strong><?= formatDate($s['work_date']) ?></strong>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($s['doctor_degree']) ?> <?= htmlspecialchars($s['doctor_name']) ?></strong>
                            </td>
                            <td>
                                <?= htmlspecialchars($s['specialty_name']) ?><br>
                                <span class="badge badge-purple"><?= htmlspecialchars($s['clinic_room']) ?></span>
                            </td>
                            <td><strong style="color:var(--primary);"><?= htmlspecialchars($s['session_name']) ?></strong></td>
                            <td><?= formatTime($s['start_time']) ?> - <?= formatTime($s['end_time']) ?></td>
                            <td>
                                <strong><?= $s['current_booked'] ?></strong> / <?= $s['max_patients'] ?> chỗ
                            </td>
                            <td>
                                <?php if ($s['status'] === 'AVAILABLE'): ?>
                                    <span class="badge badge-success">Mở đặt khám</span>
                                <?php elseif ($s['status'] === 'FULL'): ?>
                                    <span class="badge badge-warning">Đã kín lịch</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Đã tắt ca</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;">
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa ca trực này?');">
                                    <input type="hidden" name="action" value="delete_schedule">
                                    <input type="hidden" name="schedule_id" value="<?= $s['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa ca"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TẠO CA TRỰC MỚI -->
<div id="create_schedule_modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#FFF;max-width:520px;width:100%;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-xl);">
        <div style="background:var(--primary);color:#FFF;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:16px;"><i class="fa-solid fa-calendar-plus"></i> Xếp Lịch Phân Ca Trực Cho Bác Sĩ</h4>
            <button type="button" onclick="closeCreateModal()" style="background:none;border:none;color:#FFF;font-size:20px;cursor:pointer;">&times;</button>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="lich-lam-viec.php">
                <input type="hidden" name="action" value="create_schedule">

                <div class="form-group">
                    <label class="form-label">Chọn Bác sĩ <span class="required">*</span></label>
                    <select name="doctor_id" class="form-select" required>
                        <option value="">-- Chọn bác sĩ --</option>
                        <?php foreach ($doctors as $doc): ?>
                            <option value="<?= $doc['id'] ?>">
                                <?= htmlspecialchars($doc['degree']) ?> <?= htmlspecialchars($doc['full_name']) ?> (<?= htmlspecialchars($doc['clinic_room']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Ngày làm việc <span class="required">*</span></label>
                    <input type="date" name="work_date" class="form-control" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tên ca trực</label>
                    <select name="session_name" class="form-select" onchange="autoFillTimes(this.value)">
                        <option value="Ca Sáng (07:30 - 11:30)">Ca Sáng (07:30 - 11:30)</option>
                        <option value="Ca Chiều (13:30 - 17:00)">Ca Chiều (13:30 - 17:00)</option>
                        <option value="Ca Tối (17:30 - 20:00)">Ca Tối (17:30 - 20:00)</option>
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Giờ bắt đầu:</label>
                        <input type="time" name="start_time" id="start_time" class="form-control" value="07:30:00" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Giờ kết thúc:</label>
                        <input type="time" name="end_time" id="end_time" class="form-control" value="11:30:00" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Số lượng bệnh nhân tiếp nhận tối đa:</label>
                    <input type="number" name="max_patients" class="form-control" value="15" min="1" max="50" required>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
                    <button type="button" onclick="closeCreateModal()" class="btn btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Lưu Ca Trực</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCreateModal() { document.getElementById('create_schedule_modal').style.display = 'flex'; }
function closeCreateModal() { document.getElementById('create_schedule_modal').style.display = 'none'; }
function autoFillTimes(val) {
    if (val.includes('Sáng')) {
        document.getElementById('start_time').value = '07:30:00';
        document.getElementById('end_time').value = '11:30:00';
    } else if (val.includes('Chiều')) {
        document.getElementById('start_time').value = '13:30:00';
        document.getElementById('end_time').value = '17:00:00';
    } else if (val.includes('Tối')) {
        document.getElementById('start_time').value = '17:30:00';
        document.getElementById('end_time').value = '20:00:00';
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
