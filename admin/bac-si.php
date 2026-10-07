<?php
$page_title = "Quản lý thông tin Bác sĩ";
$activeNav = 'bac_si';
require_once __DIR__ . '/../includes/admin_header.php';

// XỬ LÝ THÊM / SỬA / XÓA BÁC SĨ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_doctor') {
        $fullName = sanitizeInput($_POST['full_name'] ?? '');
        $username = sanitizeInput($_POST['username'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '123456';
        $specialtyId = filter_input(INPUT_POST, 'specialty_id', FILTER_VALIDATE_INT);
        $degree = sanitizeInput($_POST['degree'] ?? 'BS CKI');
        $expYears = (int)($_POST['experience_years'] ?? 5);
        $fee = (float)($_POST['consultation_fee'] ?? 200000);
        $room = sanitizeInput($_POST['clinic_room'] ?? 'Phòng 101');
        $bio = sanitizeInput($_POST['bio'] ?? '');

        try {
            $pdo->beginTransaction();

            // 1. Tạo tài khoản users
            $passHash = password_hash($password, PASSWORD_BCRYPT);
            $stmtU = $pdo->prepare("INSERT INTO users (username, password, full_name, email, phone, role) VALUES (?, ?, ?, ?, ?, 'ROLE_DOCTOR')");
            $stmtU->execute([$username, $passHash, $fullName, $email, $phone]);
            $userId = $pdo->lastInsertId();

            // 2. Tạo record doctors
            $avatar = 'assets/images/doctors/doctor-1.jpg'; // Ảnh vector mặc định
            $stmtD = $pdo->prepare("INSERT INTO doctors (user_id, specialty_id, full_name, degree, experience_years, consultation_fee, bio, avatar, clinic_room) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtD->execute([$userId, $specialtyId, $fullName, $degree, $expYears, $fee, $bio, $avatar, $room]);

            $pdo->commit();
            setFlashMessage('success', "Thêm bác sĩ {$fullName} và cấp tài khoản thành công!");
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            setFlashMessage('error', "Lỗi khi thêm bác sĩ: " . $e->getMessage());
        }
    } elseif ($action === 'update_doctor') {
        $docId = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
        $fullName = sanitizeInput($_POST['full_name'] ?? '');
        $specialtyId = filter_input(INPUT_POST, 'specialty_id', FILTER_VALIDATE_INT);
        $degree = sanitizeInput($_POST['degree'] ?? '');
        $expYears = (int)($_POST['experience_years'] ?? 1);
        $fee = (float)($_POST['consultation_fee'] ?? 200000);
        $room = sanitizeInput($_POST['clinic_room'] ?? '');
        $bio = sanitizeInput($_POST['bio'] ?? '');

        if ($docId) {
            $stmtUpd = $pdo->prepare("UPDATE doctors SET full_name = ?, specialty_id = ?, degree = ?, experience_years = ?, consultation_fee = ?, clinic_room = ?, bio = ? WHERE id = ?");
            $stmtUpd->execute([$fullName, $specialtyId, $degree, $expYears, $fee, $room, $bio, $docId]);
            setFlashMessage('success', "Cập nhật hồ sơ bác sĩ {$fullName} thành công!");
        }
    } elseif ($action === 'delete_doctor') {
        // UC-AD-04 Ngoại lệ 5a: Kiểm tra xem bác sĩ có lịch hẹn đang chờ khám không
        $docId = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
        if ($docId) {
            $today = date('Y-m-d');
            $stmtChkApps = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND appointment_date >= ? AND status IN ('PENDING', 'CONFIRMED', 'CHECKED_IN')");
            $stmtChkApps->execute([$docId, $today]);
            $hasFutureApps = $stmtChkApps->fetchColumn() > 0;

            if ($hasFutureApps) {
                setFlashMessage('error', "Không thể xóa bác sĩ này vì đang có lịch hẹn khám của bệnh nhân trong tương lai! Vui lòng dời lịch hẹn sang bác sĩ khác trước (Ngoại lệ UC-AD-04).");
            } else {
                $stmtGetUid = $pdo->prepare("SELECT user_id FROM doctors WHERE id = ?");
                $stmtGetUid->execute([$docId]);
                $uId = $stmtGetUid->fetchColumn();

                $pdo->beginTransaction();
                $pdo->prepare("DELETE FROM doctors WHERE id = ?")->execute([$docId]);
                if ($uId) {
                    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uId]);
                }
                $pdo->commit();
                setFlashMessage('success', "Đã xóa hồ sơ bác sĩ khỏi hệ thống thành công!");
            }
        }
    }
    header("Location: bac-si.php");
    exit;
}

$specialties = $pdo->query("SELECT * FROM specialties ORDER BY name ASC")->fetchAll();
$doctors = $pdo->query("SELECT d.*, s.name as specialty_name, u.username, u.email, u.phone 
                        FROM doctors d 
                        JOIN specialties s ON d.specialty_id = s.id 
                        JOIN users u ON d.user_id = u.id 
                        ORDER BY d.id ASC")->fetchAll();

$flash = getFlashMessage();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-user-doctor" style="color:var(--primary);"></i> Quản Lý Danh Sách & Hồ Sơ Bác Sĩ</h2>
        <p>Quản lý nhân sự y tế, phân bổ chuyên khoa, học vị, mức phí khám và tài khoản truy cập</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openDoctorCreateModal()">
        <i class="fa-solid fa-user-plus"></i> Thêm Bác Sĩ Mới
    </button>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <div><?= htmlspecialchars($flash['message']) ?></div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-users" style="color:var(--primary);margin-right:8px;"></i> Danh Sách Bác Sĩ Phòng Khám An Nhiên (<?= count($doctors) ?> bác sĩ)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px;">STT</th>
                        <th>Họ Tên & Học Vị</th>
                        <th>Chuyên Khoa</th>
                        <th>Phòng Khám</th>
                        <th>Kinh Nghiệm</th>
                        <th>Phí Khám</th>
                        <th>Tài Khoản (Username / Email)</th>
                        <th style="text-align:right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; foreach ($doctors as $d): ?>
                        <tr>
                            <td><?= $stt++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($d['degree']) ?> <?= htmlspecialchars($d['full_name']) ?></strong>
                            </td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($d['specialty_name']) ?></span></td>
                            <td><span class="badge badge-purple"><?= htmlspecialchars($d['clinic_room']) ?></span></td>
                            <td><?= $d['experience_years'] ?> năm</td>
                            <td><strong style="color:#DC2626;"><?= formatMoney($d['consultation_fee']) ?></strong></td>
                            <td>
                                <code><?= htmlspecialchars($d['username']) ?></code><br>
                                <span style="font-size:12px;color:#64748B;"><?= htmlspecialchars($d['email']) ?></span>
                            </td>
                            <td style="text-align:right;white-space:nowrap;">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick='openDoctorEditModal(<?= json_encode($d, JSON_UNESCAPED_UNICODE) ?>)'>
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa bác sĩ này? Hành động này sẽ kiểm tra ràng buộc lịch hẹn.');">
                                    <input type="hidden" name="action" value="delete_doctor">
                                    <input type="hidden" name="doctor_id" value="<?= $d['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL THÊM BÁC SĨ MỚI -->
<div id="doctor_create_modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#FFF;max-width:650px;width:100%;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-xl);max-height:90vh;overflow-y:auto;">
        <div style="background:var(--primary);color:#FFF;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:16px;"><i class="fa-solid fa-user-plus"></i> Thêm Bác Sĩ Mới Vào Hệ Thống</h4>
            <button type="button" onclick="closeDoctorCreateModal()" style="background:none;border:none;color:#FFF;font-size:20px;cursor:pointer;">&times;</button>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="bac-si.php">
                <input type="hidden" name="action" value="create_doctor">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Họ và tên bác sĩ <span class="required">*</span></label>
                        <input type="text" name="full_name" class="form-control" required placeholder="Ví dụ: Nguyễn Văn Hải">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Học vị / Học hàm <span class="required">*</span></label>
                        <input type="text" name="degree" class="form-control" required placeholder="BS CKI, ThS.BS, TS.BS...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Chuyên khoa trực thuộc <span class="required">*</span></label>
                        <select name="specialty_id" class="form-select" required>
                            <?php foreach ($specialties as $sp): ?>
                                <option value="<?= $sp['id'] ?>"><?= htmlspecialchars($sp['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phòng khám công tác</label>
                        <input type="text" name="clinic_room" class="form-control" value="Phòng 101" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Số năm kinh nghiệm:</label>
                        <input type="number" name="experience_years" class="form-control" value="5" min="1">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phí khám tư vấn (VNĐ):</label>
                        <input type="number" name="consultation_fee" class="form-control" value="200000" step="10000">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tên đăng nhập (Username) <span class="required">*</span></label>
                        <input type="text" name="username" class="form-control" required placeholder="bs_vanhai">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mật khẩu cấp ban đầu <span class="required">*</span></label>
                        <input type="text" name="password" class="form-control" value="123456" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email liên hệ <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="hai.nguyen@annhienclinic.vn">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Số điện thoại liên hệ <span class="required">*</span></label>
                        <input type="tel" name="phone" class="form-control" required placeholder="0912345678">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Tiểu sử chuyên môn & Giới thiệu</label>
                    <textarea name="bio" class="form-control" rows="3" placeholder="Quá trình đào tạo, chuyên môn sâu..."></textarea>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
                    <button type="button" onclick="closeDoctorCreateModal()" class="btn btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Lưu Hồ Sơ Bác Sĩ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL SỬA BÁC SĨ -->
<div id="doctor_edit_modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#FFF;max-width:650px;width:100%;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-xl);max-height:90vh;overflow-y:auto;">
        <div style="background:var(--primary);color:#FFF;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:16px;"><i class="fa-solid fa-pen-to-square"></i> Cập Nhật Hồ Sơ Bác Sĩ</h4>
            <button type="button" onclick="closeDoctorEditModal()" style="background:none;border:none;color:#FFF;font-size:20px;cursor:pointer;">&times;</button>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="bac-si.php">
                <input type="hidden" name="action" value="update_doctor">
                <input type="hidden" name="doctor_id" id="edit_doc_id" value="">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Họ và tên bác sĩ <span class="required">*</span></label>
                        <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Học vị / Học hàm <span class="required">*</span></label>
                        <input type="text" name="degree" id="edit_degree" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Chuyên khoa trực thuộc <span class="required">*</span></label>
                        <select name="specialty_id" id="edit_specialty_id" class="form-select" required>
                            <?php foreach ($specialties as $sp): ?>
                                <option value="<?= $sp['id'] ?>"><?= htmlspecialchars($sp['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phòng khám công tác</label>
                        <input type="text" name="clinic_room" id="edit_clinic_room" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Số năm kinh nghiệm:</label>
                        <input type="number" name="experience_years" id="edit_exp" class="form-control" min="1">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phí khám tư vấn (VNĐ):</label>
                        <input type="number" name="consultation_fee" id="edit_fee" class="form-control" step="10000">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Tiểu sử chuyên môn & Giới thiệu</label>
                    <textarea name="bio" id="edit_bio" class="form-control" rows="3"></textarea>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
                    <button type="button" onclick="closeDoctorEditModal()" class="btn btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Cập Nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openDoctorCreateModal() { document.getElementById('doctor_create_modal').style.display = 'flex'; }
function closeDoctorCreateModal() { document.getElementById('doctor_create_modal').style.display = 'none'; }
function openDoctorEditModal(doc) {
    document.getElementById('edit_doc_id').value = doc.id;
    document.getElementById('edit_full_name').value = doc.full_name;
    document.getElementById('edit_degree').value = doc.degree;
    document.getElementById('edit_specialty_id').value = doc.specialty_id;
    document.getElementById('edit_clinic_room').value = doc.clinic_room;
    document.getElementById('edit_exp').value = doc.experience_years;
    document.getElementById('edit_fee').value = doc.consultation_fee;
    document.getElementById('edit_bio').value = doc.bio || '';
    document.getElementById('doctor_edit_modal').style.display = 'flex';
}
function closeDoctorEditModal() { document.getElementById('doctor_edit_modal').style.display = 'none'; }
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
