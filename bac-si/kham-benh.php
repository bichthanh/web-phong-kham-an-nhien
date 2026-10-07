<?php
$page_title = "Buồng khám bệnh & Kê đơn thuốc";
$activeNav = 'dashboard';
require_once __DIR__ . '/../includes/doctor_header.php';

$appId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$appId) {
    header("Location: index.php");
    exit;
}

// Lấy thông tin lịch hẹn
$stmt = $pdo->prepare("SELECT a.*, ds.session_name, s.name as specialty_name 
                       FROM appointments a 
                       JOIN doctors d ON a.doctor_id = d.id 
                       JOIN specialties s ON d.specialty_id = s.id 
                       LEFT JOIN doctor_schedules ds ON a.schedule_id = ds.id 
                       WHERE a.id = ? AND a.doctor_id = ?");
$stmt->execute([$appId, $currentDoctor['id']]);
$appointment = $stmt->fetch();

if (!$appointment) {
    die("<div class='alert alert-danger'>Không tìm thấy ca khám này hoặc bạn không được phân công phụ trách!</div>");
}

// Lấy đơn thuốc đã có nếu ca khám đã hoàn thành
$stmtPres = $pdo->prepare("SELECT * FROM prescriptions WHERE appointment_id = ?");
$stmtPres->execute([$appId]);
$prescription = $stmtPres->fetch();
$existingMedicines = $prescription ? json_decode($prescription['medicine_list'], true) : [];

$error_msg = null;

// XỬ LÝ LƯU ĐƠN THUỐC & HOÀN TẤT CA KHÁM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_prescription') {
    $diagnosis = sanitizeInput($_POST['diagnosis'] ?? '');
    $doctorNotes = sanitizeInput($_POST['doctor_notes'] ?? '');
    $reExamDate = !empty($_POST['re_examination_date']) ? sanitizeInput($_POST['re_examination_date']) : null;

    $medNames = $_POST['med_name'] ?? [];
    $medUnits = $_POST['med_unit'] ?? [];
    $medQtys = $_POST['med_quantity'] ?? [];
    $medUsages = $_POST['med_usage'] ?? [];

    $medicineList = [];
    for ($i = 0; $i < count($medNames); $i++) {
        $mName = trim($medNames[$i]);
        if (!empty($mName)) {
            $medicineList[] = [
                'name' => $mName,
                'unit' => trim($medUnits[$i] ?? 'Viên'),
                'quantity' => (int)($medQtys[$i] ?? 1),
                'usage' => trim($medUsages[$i] ?? '')
            ];
        }
    }

    if (empty($diagnosis)) {
        $error_msg = "Vui lòng nhập kết luận chẩn đoán bệnh cho bệnh nhân!";
    } elseif (empty($medicineList)) {
        $error_msg = "Vui lòng kê ít nhất 1 loại thuốc hoặc thực phẩm bổ trợ trong đơn!";
    } else {
        try {
            $pdo->beginTransaction();

            $medJson = json_encode($medicineList, JSON_UNESCAPED_UNICODE);

            if ($prescription) {
                // Cập nhật đơn thuốc cũ
                $stmtU = $pdo->prepare("UPDATE prescriptions SET diagnosis = ?, medicine_list = ?, doctor_notes = ?, re_examination_date = ? WHERE appointment_id = ?");
                $stmtU->execute([$diagnosis, $medJson, $doctorNotes, $reExamDate, $appId]);
            } else {
                // Tạo mới đơn thuốc
                $stmtI = $pdo->prepare("INSERT INTO prescriptions (appointment_id, diagnosis, medicine_list, doctor_notes, re_examination_date) VALUES (?, ?, ?, ?, ?)");
                $stmtI->execute([$appId, $diagnosis, $medJson, $doctorNotes, $reExamDate]);
            }

            // Chuyển trạng thái lịch hẹn sang COMPLETED
            $stmtC = $pdo->prepare("UPDATE appointments SET status = 'COMPLETED' WHERE id = ?");
            $stmtC->execute([$appId]);

            $pdo->commit();
            setFlashMessage('success', "Đã hoàn tất ca khám và lưu đơn thuốc điện tử cho bệnh nhân {$appointment['patient_name']} thành công!");
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error_msg = "Lỗi khi lưu bệnh án: " . $e->getMessage();
        }
    }
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-stethoscope" style="color:var(--primary);"></i> Thăm Khám & Kê Đơn Điện Tử</h2>
        <p>Bệnh nhân: <strong><?= htmlspecialchars($appointment['patient_name']) ?></strong> • Mã phiếu: <strong><?= htmlspecialchars($appointment['appointment_code']) ?></strong></p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="index.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Về danh sách</a>
        <a href="ho-so-benh-nhan.php?phone=<?= urlencode($appointment['patient_phone']) ?>" target="_blank" class="btn btn-outline-primary">
            <i class="fa-solid fa-clock-rotate-left"></i> Xem lịch sử bệnh án cũ
        </a>
    </div>
</div>

<?php if ($error_msg): ?>
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div><?= htmlspecialchars($error_msg) ?></div>
    </div>
<?php endif; ?>

<!-- THÔNG TIN BỆNH NHÂN ĐANG KHÁM -->
<div class="card" style="margin-bottom:24px;border-left:4px solid var(--primary);">
    <div class="card-body" style="padding:20px;">
        <div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:16px;">
            <div>
                <span style="font-size:12px;color:#64748B;display:block;">Họ và tên:</span>
                <strong style="font-size:16px;color:#0F172A;"><?= htmlspecialchars($appointment['patient_name']) ?></strong>
            </div>
            <div>
                <span style="font-size:12px;color:#64748B;display:block;">Giới tính / Ngày sinh:</span>
                <span style="font-size:14px;"><?= htmlspecialchars($appointment['patient_gender']) ?> • <?= $appointment['patient_dob'] ? formatDate($appointment['patient_dob']) : 'Chưa rõ' ?></span>
            </div>
            <div>
                <span style="font-size:12px;color:#64748B;display:block;">Số điện thoại liên hệ:</span>
                <strong style="font-size:14px;color:#0284C7;"><?= htmlspecialchars($appointment['patient_phone']) ?></strong>
            </div>
            <div>
                <span style="font-size:12px;color:#64748B;display:block;">Trạng thái hiện tại:</span>
                <?= getStatusBadge($appointment['status']) ?>
            </div>
        </div>

        <div style="margin-top:14px;padding:12px;background:#F8FAFC;border-radius:6px;font-size:13.5px;">
            <strong><i class="fa-solid fa-notes-medical" style="color:var(--primary);"></i> Triệu chứng ban đầu người bệnh khai báo:</strong>
            <p style="margin-top:4px;color:#334155;"><?= nl2br(htmlspecialchars($appointment['symptom_description'])) ?></p>
        </div>
    </div>
</div>

<!-- BIỂU MẪU CHẨN ĐOÁN & KÊ ĐƠN THUỐC -->
<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-file-prescription" style="color:var(--primary);margin-right:8px;"></i> Kết Luận Bệnh Án & Chỉ Định Đơn Thuốc</h3>
        <?php if ($appointment['status'] === 'COMPLETED'): ?>
            <span class="badge badge-success"><i class="fa-solid fa-check-double"></i> Ca khám đã hoàn thành</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <form method="POST" action="kham-benh.php?id=<?= $appId ?>" id="prescription_form">
            <input type="hidden" name="action" value="save_prescription">

            <!-- 1. CHẨN ĐOÁN TÌNH TRẠNG BỆNH -->
            <div class="form-group">
                <label class="form-label" style="font-size:15px;">
                    1. Kết luận chẩn đoán bệnh của Bác sĩ <span class="required">*</span>
                </label>
                <textarea name="diagnosis" class="form-control" rows="2" required 
                          placeholder="Ví dụ: Tăng huyết áp độ 2 (JNC 7) - Rối loạn lipid máu, theo dõi thiếu máu cơ tim cục bộ..."><?= htmlspecialchars($prescription['diagnosis'] ?? '') ?></textarea>
            </div>

            <!-- 2. BẢNG KÊ ĐƠN THUỐC ĐỘNG -->
            <div class="form-group" style="margin-top:24px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <label class="form-label" style="font-size:15px;margin-bottom:0;">
                        2. Danh mục thuốc chỉ định <span class="required">*</span>
                    </label>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn_add_medicine">
                        <i class="fa-solid fa-plus"></i> Thêm loại thuốc
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table" id="medicine_table">
                        <thead>
                            <tr style="background:#F1F5F9;">
                                <th style="width:30%;">Tên thuốc & hàm lượng</th>
                                <th style="width:15%;">Đơn vị tính</th>
                                <th style="width:12%;">Số lượng</th>
                                <th style="width:35%;">Cách dùng & liều uống (sáng/trưa/tối)</th>
                                <th style="width:8%;text-align:center;">Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="medicine_rows">
                            <?php if (!empty($existingMedicines)): ?>
                                <?php foreach ($existingMedicines as $m): ?>
                                    <tr>
                                        <td><input type="text" name="med_name[]" class="form-control" value="<?= htmlspecialchars($m['name']) ?>" required></td>
                                        <td><input type="text" name="med_unit[]" class="form-control" value="<?= htmlspecialchars($m['unit']) ?>"></td>
                                        <td><input type="number" name="med_quantity[]" class="form-control" value="<?= (int)$m['quantity'] ?>" min="1"></td>
                                        <td><input type="text" name="med_usage[]" class="form-control" value="<?= htmlspecialchars($m['usage']) ?>"></td>
                                        <td style="text-align:center;"><button type="button" class="btn btn-sm btn-danger remove-med"><i class="fa-solid fa-trash"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Dòng mặc định 1 -->
                                <tr>
                                    <td><input type="text" name="med_name[]" class="form-control" placeholder="Tên thuốc..." required></td>
                                    <td><input type="text" name="med_unit[]" class="form-control" value="Viên"></td>
                                    <td><input type="number" name="med_quantity[]" class="form-control" value="10" min="1"></td>
                                    <td><input type="text" name="med_usage[]" class="form-control" placeholder="Uống 1 viên sau ăn sáng"></td>
                                    <td style="text-align:center;"><button type="button" class="btn btn-sm btn-danger remove-med"><i class="fa-solid fa-trash"></i></button></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. LỜI DẶN DÒ Y KHOA -->
            <div class="form-group" style="margin-top:24px;">
                <label class="form-label" style="font-size:15px;">3. Lời dặn dò chế độ sinh hoạt & dinh dưỡng của bác sĩ</label>
                <textarea name="doctor_notes" class="form-control" rows="3" 
                          placeholder="Ví dụ: Ăn nhạt tuyệt đối, uống nhiều nước, tập thể dục nhẹ nhàng 30 phút mỗi ngày..."><?= htmlspecialchars($prescription['doctor_notes'] ?? '') ?></textarea>
            </div>

            <!-- 4. HẸN NGÀY TÁI KHÁM (USE CASE EXTEND) -->
            <div class="form-group" style="max-width:320px;margin-top:20px;">
                <label class="form-label" style="font-size:15px;">4. Hẹn ngày tái khám (nếu có)</label>
                <input type="date" name="re_examination_date" class="form-control" 
                       value="<?= htmlspecialchars($prescription['re_examination_date'] ?? '') ?>" min="<?= date('Y-m-d') ?>">
            </div>

            <div style="display:flex;justify-content:flex-end;gap:14px;border-top:1px solid #E2E8F0;padding-top:20px;margin-top:30px;">
                <a href="index.php" class="btn btn-outline">Hủy bỏ</a>
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fa-solid fa-circle-check"></i> Lưu Bệnh Án & Hoàn Tất Ca Khám
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnAdd = document.getElementById('btn_add_medicine');
    const tbody = document.getElementById('medicine_rows');

    if (btnAdd && tbody) {
        btnAdd.addEventListener('click', function () {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="med_name[]" class="form-control" placeholder="Tên thuốc..." required></td>
                <td><input type="text" name="med_unit[]" class="form-control" value="Viên"></td>
                <td><input type="number" name="med_quantity[]" class="form-control" value="10" min="1"></td>
                <td><input type="text" name="med_usage[]" class="form-control" placeholder="Cách dùng..."></td>
                <td style="text-align:center;"><button type="button" class="btn btn-sm btn-danger remove-med"><i class="fa-solid fa-trash"></i></button></td>
            `;
            tbody.appendChild(tr);
            attachRemoveBtn(tr.querySelector('.remove-med'));
        });

        function attachRemoveBtn(btn) {
            btn.addEventListener('click', function () {
                if (tbody.querySelectorAll('tr').length > 1) {
                    this.closest('tr').remove();
                } else {
                    alert('Đơn thuốc phải có ít nhất 1 loại thuốc!');
                }
            });
        }

        tbody.querySelectorAll('.remove-med').forEach(btn => attachRemoveBtn(btn));
    }
});
</script>

<?php require_once __DIR__ . '/../includes/doctor_footer.php'; ?>
