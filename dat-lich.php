<?php
$page_title = "Đặt lịch khám trực tuyến";
require_once __DIR__ . '/includes/header.php';

$preset_specialty = filter_input(INPUT_GET, 'specialty_id', FILTER_VALIDATE_INT);
$preset_doctor = filter_input(INPUT_GET, 'doctor_id', FILTER_VALIDATE_INT);
$preset_date = filter_input(INPUT_GET, 'date') ?? date('Y-m-d');

// Nếu có preset doctor, lấy specialty_id của bác sĩ đó
if ($preset_doctor && !$preset_specialty) {
    $stmt = $pdo->prepare("SELECT specialty_id FROM doctors WHERE id = ?");
    $stmt->execute([$preset_doctor]);
    $preset_specialty = $stmt->fetchColumn();
}

$specialties = $pdo->query("SELECT * FROM specialties ORDER BY name ASC")->fetchAll();

$success_code = null;
$error_msg = null;

// Xử lý khi Submit Form Đặt Lịch
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctorId = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
    $scheduleId = filter_input(INPUT_POST, 'schedule_id', FILTER_VALIDATE_INT);
    $appointmentDate = sanitizeInput($_POST['appointment_date'] ?? '');
    $appointmentTime = sanitizeInput($_POST['appointment_time'] ?? '');
    
    $patientName = sanitizeInput($_POST['patient_name'] ?? '');
    $patientPhone = sanitizeInput($_POST['patient_phone'] ?? '');
    $patientEmail = sanitizeInput($_POST['patient_email'] ?? '');
    $patientDob = !empty($_POST['patient_dob']) ? sanitizeInput($_POST['patient_dob']) : null;
    $patientGender = sanitizeInput($_POST['patient_gender'] ?? 'Nam');
    $patientAddress = sanitizeInput($_POST['patient_address'] ?? '');
    $symptom = sanitizeInput($_POST['symptom_description'] ?? '');
    $paymentMethod = sanitizeInput($_POST['payment_method'] ?? 'AT_CLINIC');

    if (!$doctorId || !$scheduleId || !$patientName || !$patientPhone || !$symptom) {
        $error_msg = "Vui lòng điền đầy đủ các thông tin bắt buộc và chọn ca khám khả dụng!";
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Kiểm tra lại ca khám còn chỗ không
            $stmtCheck = $pdo->prepare("SELECT * FROM doctor_schedules WHERE id = ? FOR UPDATE");
            $stmtCheck->execute([$scheduleId]);
            $schedule = $stmtCheck->fetch();

            if (!$schedule || $schedule['status'] === 'OFF' || $schedule['current_booked'] >= $schedule['max_patients']) {
                $pdo->rollBack();
                $error_msg = "Rất tiếc, khung giờ khám này vừa hết chỗ hoặc đã bị hủy. Vui lòng chọn ca khám khác!";
            } else {
                // 2. Lấy thông tin giá khám của bác sĩ
                $stmtDoc = $pdo->prepare("SELECT consultation_fee FROM doctors WHERE id = ?");
                $stmtDoc->execute([$doctorId]);
                $consultFee = $stmtDoc->fetchColumn() ?: 200000.00;

                // 3. Sinh mã phiếu hẹn duy nhất AN-2026-XXXX
                $appCode = generateAppointmentCode($pdo);
                $patientId = isPatient() ? $_SESSION['user_id'] : null;

                // 4. Lưu phiếu hẹn vào appointments
                $sqlInsert = "INSERT INTO appointments (
                    appointment_code, patient_id, patient_name, patient_phone, patient_email, 
                    patient_dob, patient_gender, patient_address, doctor_id, schedule_id, 
                    appointment_date, appointment_time, symptom_description, status, 
                    payment_status, payment_method, amount
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'CONFIRMED', 'UNPAID', ?, ?)";

                $stmtInsert = $pdo->prepare($sqlInsert);
                $stmtInsert->execute([
                    $appCode, $patientId, $patientName, $patientPhone, $patientEmail,
                    $patientDob, $patientGender, $patientAddress, $doctorId, $scheduleId,
                    $appointmentDate, $appointmentTime, $symptom, $paymentMethod, $consultFee
                ]);

                // 5. Cập nhật số lượng đã đặt của ca trực
                $newBooked = $schedule['current_booked'] + 1;
                $newStatus = ($newBooked >= $schedule['max_patients']) ? 'FULL' : 'AVAILABLE';
                $stmtUpd = $pdo->prepare("UPDATE doctor_schedules SET current_booked = ?, status = ? WHERE id = ?");
                $stmtUpd->execute([$newBooked, $newStatus, $scheduleId]);

                $pdo->commit();
                $success_code = $appCode;
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error_msg = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>

<div style="background:linear-gradient(135deg, #042F2E 0%, #0F172A 100%);color:#FFF;padding:45px 0;">
    <div class="container">
        <h1 style="font-size:32px;font-weight:800;margin-bottom:8px;">Đăng Ký Khám Bệnh Trực Tuyến</h1>
        <p style="color:#94A3B8;font-size:16px;">Quy trình 3 bước tiện lợi: Chọn bác sĩ • Chọn khung giờ • Nhận mã phiếu hẹn ngay lập tức</p>
    </div>
</div>

<section class="section">
    <div class="container" style="max-width:960px;">

        <?php if ($success_code): ?>
            <!-- THÔNG BÁO ĐẶT LỊCH THÀNH CÔNG -->
            <div class="card" style="border-top:5px solid var(--accent);text-align:center;padding:40px 30px;">
                <div style="width:72px;height:72px;background:#D1FAE5;color:#059669;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h2 style="font-size:26px;font-weight:800;color:#0F172A;margin-bottom:10px;">Đặt Lịch Khám Thành Công!</h2>
                <p style="font-size:15px;color:#64748B;max-width:600px;margin:0 auto 24px;">
                    Hệ thống Phòng khám Đa khoa An Nhiên đã ghi nhận phiếu hẹn của bạn. Bạn có thể sử dụng mã phiếu hẹn dưới đây để tiếp đón tại quầy hoặc tra cứu đơn thuốc sau khám.
                </p>

                <div style="background:#F0FDFA;border:2px dashed var(--primary);border-radius:12px;padding:20px;display:inline-block;margin-bottom:30px;">
                    <span style="font-size:13px;color:#0F766E;font-weight:700;display:block;text-transform:uppercase;letter-spacing:1px;">Mã Phiếu Hẹn Của Bạn</span>
                    <strong style="font-size:32px;color:var(--primary-dark);letter-spacing:2px;display:block;margin:6px 0;"><?= $success_code ?></strong>
                    <span style="font-size:12.5px;color:#64748B;">Trạng thái: <strong style="color:#0284C7;">Đã xác nhận (CONFIRMED)</strong></span>
                </div>

                <div style="display:flex;justify-content:center;gap:14px;flex-wrap:wrap;">
                    <a href="<?= BASE_URL ?>/tra-cuu.php?code=<?= $success_code ?>" class="btn btn-primary">
                        <i class="fa-solid fa-file-invoice"></i> Xem chi tiết phiếu hẹn & In phiếu
                    </a>
                    <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline">
                        <i class="fa-solid fa-house"></i> Về trang chủ
                    </a>
                </div>
            </div>

        <?php else: ?>

            <?php if ($error_msg): ?>
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                    <div><?= htmlspecialchars($error_msg) ?></div>
                </div>
            <?php endif; ?>

            <!-- WIZARD FORM ĐẶT LỊCH -->
            <div class="booking-wizard">
                <div class="wizard-steps">
                    <div class="wizard-step active">
                        <div class="step-num">1</div>
                        <div class="step-info">
                            <h5>Chuyên khoa & Bác sĩ</h5>
                            <p>Chọn dịch vụ khám</p>
                        </div>
                    </div>
                    <div class="wizard-step active">
                        <div class="step-num">2</div>
                        <div class="step-info">
                            <h5>Thời gian khám</h5>
                            <p>Chọn ngày & ca trực</p>
                        </div>
                    </div>
                    <div class="wizard-step active">
                        <div class="step-num">3</div>
                        <div class="step-info">
                            <h5>Thông tin bệnh nhân</h5>
                            <p>Xác nhận đăng ký</p>
                        </div>
                    </div>
                </div>

                <form action="<?= BASE_URL ?>/dat-lich.php" method="POST" id="appointment_booking_form" class="wizard-body">
                    
                    <!-- BƯỚC 1: CHỌN CHUYÊN KHOA & BÁC SĨ -->
                    <div style="margin-bottom:30px;padding-bottom:24px;border-bottom:1px solid #E2E8F0;">
                        <h4 style="font-size:18px;color:#0F172A;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                            <span style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#FFF;display:flex;align-items:center;justify-content:center;font-size:14px;">1</span>
                            Chọn Chuyên Khoa & Bác Sĩ
                        </h4>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                            <div class="form-group">
                                <label class="form-label">Chuyên khoa khám <span class="required">*</span></label>
                                <select name="specialty_id" id="booking_specialty" class="form-select" required>
                                    <option value="">-- Chọn chuyên khoa --</option>
                                    <?php foreach ($specialties as $sp): ?>
                                        <option value="<?= $sp['id'] ?>" <?= $preset_specialty == $sp['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($sp['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Bác sĩ phụ trách <span class="required">*</span></label>
                                <select name="doctor_id" id="booking_doctor" class="form-select" required>
                                    <option value="">-- Vui lòng chọn chuyên khoa trước --</option>
                                    <?php if ($preset_specialty): 
                                        $docs = $pdo->prepare("SELECT id, full_name, degree, clinic_room, consultation_fee FROM doctors WHERE specialty_id = ?");
                                        $docs->execute([$preset_specialty]);
                                        foreach ($docs->fetchAll() as $d): ?>
                                            <option value="<?= $d['id'] ?>" <?= $preset_doctor == $d['id'] ? 'selected' : '' ?> data-fee="<?= $d['consultation_fee'] ?>">
                                                <?= htmlspecialchars($d['degree']) ?> <?= htmlspecialchars($d['full_name']) ?> (<?= htmlspecialchars($d['clinic_room']) ?>) - <?= formatMoney($d['consultation_fee']) ?>
                                            </option>
                                        <?php endforeach; 
                                    endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- BƯỚC 2: CHỌN NGÀY VÀ CA KHÁM -->
                    <div style="margin-bottom:30px;padding-bottom:24px;border-bottom:1px solid #E2E8F0;">
                        <h4 style="font-size:18px;color:#0F172A;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                            <span style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#FFF;display:flex;align-items:center;justify-content:center;font-size:14px;">2</span>
                            Chọn Ngày & Khung Giờ Khám
                        </h4>

                        <div class="form-group" style="max-width:320px;">
                            <label class="form-label">Ngày khám mong muốn <span class="required">*</span></label>
                            <input type="date" name="appointment_date" id="booking_date" class="form-control" 
                                   value="<?= htmlspecialchars($preset_date) ?>" min="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Khung giờ ca khám còn trống <span class="required">*</span></label>
                            <!-- Hidden inputs để lưu schedule_id và time được chọn qua click -->
                            <input type="hidden" name="schedule_id" id="booking_schedule_id" value="" required>
                            <input type="hidden" name="appointment_time" id="booking_time" value="" required>

                            <div id="booking_slots_container">
                                <p class="text-muted"><i class="fa-solid fa-circle-info"></i> Vui lòng chọn bác sĩ và ngày khám để hiển thị ca trực còn trống.</p>
                            </div>
                        </div>
                    </div>

                    <!-- BƯỚC 3: THÔNG TIN BỆNH NHÂN -->
                    <div style="margin-bottom:30px;">
                        <h4 style="font-size:18px;color:#0F172A;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                            <span style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#FFF;display:flex;align-items:center;justify-content:center;font-size:14px;">3</span>
                            Thông Tin Người Đến Khám
                        </h4>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                            <div class="form-group">
                                <label class="form-label">Họ và tên bệnh nhân <span class="required">*</span></label>
                                <input type="text" name="patient_name" class="form-control" placeholder="Ví dụ: Nguyễn Văn An" 
                                       value="<?= $user ? htmlspecialchars($user['full_name']) : '' ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Số điện thoại liên hệ <span class="required">*</span></label>
                                <input type="tel" name="patient_phone" class="form-control" placeholder="Ví dụ: 0912345678" 
                                       value="<?= $user ? htmlspecialchars($user['phone']) : '' ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Địa chỉ Email nhận phiếu khám</label>
                                <input type="email" name="patient_email" class="form-control" placeholder="email@gmail.com" 
                                       value="<?= $user ? htmlspecialchars($user['email']) : '' ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Ngày tháng năm sinh</label>
                                <input type="date" name="patient_dob" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Giới tính</label>
                                <select name="patient_gender" class="form-select">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                    <option value="Khác">Khác</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Địa chỉ cư trú (Xã/Phường, Huyện, Tỉnh)</label>
                                <input type="text" name="patient_address" class="form-control" placeholder="Ví dụ: Phường Năng Tĩnh, TP. Nam Định">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mô tả triệu chứng / Lý do khám bệnh <span class="required">*</span></label>
                            <textarea name="symptom_description" class="form-control" rows="3" placeholder="Ví dụ: Tức ngực khó thở khi vận động, ho sốt kéo dài 3 ngày, muốn khám kiểm tra..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Hình thức thanh toán dự kiến:</label>
                            <div style="display:flex;gap:20px;flex-wrap:wrap;margin-top:6px;">
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                    <input type="radio" name="payment_method" value="AT_CLINIC" checked>
                                    <span>Thanh toán tại quầy lễ tân khi đến khám</span>
                                </label>
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                    <input type="radio" name="payment_method" value="BANK_TRANSFER">
                                    <span>Chuyển khoản VietQR ngân hàng</span>
                                </label>
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                    <input type="radio" name="payment_method" value="MOMO">
                                    <span>Ví điện tử MoMo</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:14px;border-top:1px solid #E2E8F0;padding-top:20px;">
                        <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-calendar-check"></i> Xác Nhận Đăng Ký Lịch Khám
                        </button>
                    </div>

                </form>
            </div>

        <?php endif; ?>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
