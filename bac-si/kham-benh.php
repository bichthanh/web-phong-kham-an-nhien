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

// XỬ LÝ LƯU ĐƠN THUỐC & HOÀN TẤT CA KHÁM TRÊN WEB
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

            // Chuyển trạng thái lịch hẹn sang COMPLETED và cập nhật lời dặn dò của bác sĩ
            $stmtC = $pdo->prepare("UPDATE appointments SET status = 'COMPLETED', doctor_reminder = ? WHERE id = ?");
            $stmtC->execute([$doctorNotes, $appId]);

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

<div class="page-header" style="background:#FFF;padding:20px;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:24px;">
    <div>
        <h2 style="font-size:22px;color:#0F172A;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-stethoscope" style="color:var(--primary);"></i> 
            Buồng Khám Bệnh & Kê Đơn Thuốc Điện Tử
        </h2>
        <p style="color:#64748B;margin-top:4px;font-size:14px;">
            Bệnh nhân: <strong style="color:#0F172A;"><?= htmlspecialchars($appointment['patient_name']) ?></strong> • Mã phiếu: <span class="badge badge-info"><?= htmlspecialchars($appointment['appointment_code']) ?></span>
        </p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="index.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Về danh sách bệnh nhân</a>
        <?php if ($appointment['status'] === 'COMPLETED'): ?>
            <button type="button" onclick="window.print()" class="btn btn-primary">
                <i class="fa-solid fa-print"></i> In Đơn Thuốc
            </button>
        <?php endif; ?>
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
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:16px;">
            <div>
                <span style="font-size:12px;color:#64748B;display:block;">Họ và tên bệnh nhân:</span>
                <strong style="font-size:16px;color:#0F172A;"><?= htmlspecialchars($appointment['patient_name']) ?></strong>
            </div>
            <div>
                <span style="font-size:12px;color:#64748B;display:block;">Giới tính / Ngày sinh:</span>
                <span style="font-size:14px;color:#0F172A;"><?= htmlspecialchars($appointment['patient_gender']) ?> • <?= $appointment['patient_dob'] ? formatDate($appointment['patient_dob']) : 'Chưa rõ ngày sinh' ?></span>
            </div>
            <div>
                <span style="font-size:12px;color:#64748B;display:block;">Số điện thoại liên hệ:</span>
                <strong style="font-size:14px;color:#0284C7;"><?= htmlspecialchars($appointment['patient_phone']) ?></strong>
            </div>
            <div>
                <span style="font-size:12px;color:#64748B;display:block;">Khung giờ hẹn khám:</span>
                <strong style="font-size:14px;color:#0D9488;"><?= formatTime($appointment['appointment_time']) ?> (Ngày <?= formatDate($appointment['appointment_date']) ?>)</strong>
            </div>
        </div>

        <div style="margin-top:14px;padding:14px;background:#F8FAFC;border-radius:8px;font-size:13.5px;border:1px solid #E2E8F0;">
            <strong style="color:var(--primary);"><i class="fa-solid fa-notes-medical"></i> Triệu chứng ban đầu người bệnh khai báo:</strong>
            <p style="margin-top:4px;color:#334155;line-height:1.5;"><?= nl2br(htmlspecialchars($appointment['symptom_description'])) ?></p>
        </div>
    </div>
</div>

<!-- BIỂU MẪU CHẨN ĐOÁN & KÊ ĐƠN THUỐC -->
<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 style="font-size:17px;font-weight:700;color:#0F172A;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-file-prescription" style="color:var(--primary);"></i> 
            Kết Luận Chẩn Đoán & Kê Đơn Thuốc Trực Tuyến
        </h3>
        <?php if ($appointment['status'] === 'COMPLETED'): ?>
            <span class="badge badge-success"><i class="fa-solid fa-check-double"></i> Đã hoàn thành</span>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <form method="POST" action="kham-benh.php?id=<?= $appId ?>" id="prescription_form">
            <input type="hidden" name="action" value="save_prescription">

            <!-- 1. KẾT LUẬN CHẨN ĐOÁN BỆNH -->
            <div class="form-group">
                <label class="form-label" style="font-size:15px;font-weight:700;color:#0F172A;">
                    1. Kết luận chẩn đoán bệnh của Bác sĩ <span class="required">*</span>
                </label>
                <textarea name="diagnosis" class="form-control" rows="2" required 
                          placeholder="Ví dụ: Tăng huyết áp vô căn độ 1 (JNC 7) - Rối loạn mỡ máu nhẹ, theo dõi tim mạch định kỳ..."><?= htmlspecialchars($prescription['diagnosis'] ?? '') ?></textarea>
            </div>

            <!-- 2. KHO THUỐC MẪU CHỌN NHANH (QUICK PRESCRIBE TAGS) -->
            <div style="margin:20px 0;padding:16px;background:#F0FDFA;border:1px dashed #5EEAD4;border-radius:10px;">
                <label style="font-size:13px;color:#0F766E;font-weight:700;display:block;margin-bottom:8px;">
                    <i class="fa-solid fa-bolt" style="color:#EAB308;"></i> Bấm chọn nhanh thuốc thường dùng của phòng khám (1-Click thêm vào đơn):
                </label>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    <button type="button" class="btn btn-sm" style="background:#FFF;border:1px solid #99F6E4;color:#0F766E;font-size:12px;font-weight:600;" 
                            onclick="addQuickMedicine('Amlodipin 5mg', 'Viên', 30, 'Uống 1 viên sau ăn sáng')">
                        + Amlodipin 5mg (Huyết áp)
                    </button>
                    <button type="button" class="btn btn-sm" style="background:#FFF;border:1px solid #99F6E4;color:#0F766E;font-size:12px;font-weight:600;" 
                            onclick="addQuickMedicine('Losartan 50mg', 'Viên', 30, 'Uống 1 viên vào 8h sáng')">
                        + Losartan 50mg (Tim mạch)
                    </button>
                    <button type="button" class="btn btn-sm" style="background:#FFF;border:1px solid #99F6E4;color:#0F766E;font-size:12px;font-weight:600;" 
                            onclick="addQuickMedicine('Omeprazole 20mg', 'Viên', 20, 'Uống 1 viên trước ăn sáng 30 phút')">
                        + Omeprazole 20mg (Dạ dày)
                    </button>
                    <button type="button" class="btn btn-sm" style="background:#FFF;border:1px solid #99F6E4;color:#0F766E;font-size:12px;font-weight:600;" 
                            onclick="addQuickMedicine('Panadol Extra 500mg', 'Viên', 10, 'Uống 1 viên khi đau đầu, sốt trên 38.5 độ')">
                        + Panadol Extra (Hạ sốt/Đau đầu)
                    </button>
                    <button type="button" class="btn btn-sm" style="background:#FFF;border:1px solid #99F6E4;color:#0F766E;font-size:12px;font-weight:600;" 
                            onclick="addQuickMedicine('Augmentin 1g', 'Viên', 14, 'Uống 1 viên x 2 lần/ngày sau bữa ăn')">
                        + Augmentin 1g (Kháng sinh)
                    </button>
                    <button type="button" class="btn btn-sm" style="background:#FFF;border:1px solid #99F6E4;color:#0F766E;font-size:12px;font-weight:600;" 
                            onclick="addQuickMedicine('Berocca viên sủi', 'Viên', 10, 'Hòa tan 1 viên vào 200ml nước, uống buổi sáng')">
                        + Berocca sủi (Bổ sung vi chất)
                    </button>
                    <button type="button" class="btn btn-sm" style="background:#FFF;border:1px solid #99F6E4;color:#0F766E;font-size:12px;font-weight:600;" 
                            onclick="addQuickMedicine('Ginkgo Biloba 120mg', 'Viên', 30, 'Uống 1 viên x 2 lần/ngày sau ăn')">
                        + Ginkgo Biloba (Tuần hoàn não)
                    </button>
                </div>
            </div>

            <!-- 3. BẢNG DANH MỤC THUỐC CHỈ ĐỊNH -->
            <div class="form-group">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <label class="form-label" style="font-size:15px;font-weight:700;color:#0F172A;margin-bottom:0;">
                        2. Danh mục thuốc kê đơn <span class="required">*</span>
                    </label>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn_add_medicine">
                        <i class="fa-solid fa-plus"></i> Thêm dòng thuốc mới
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table" id="medicine_table">
                        <thead>
                            <tr style="background:#F1F5F9;">
                                <th style="width:32%;">Tên thuốc & hàm lượng</th>
                                <th style="width:14%;">Đơn vị</th>
                                <th style="width:12%;">Số lượng</th>
                                <th style="width:34%;">Cách dùng & liều uống (sáng/trưa/tối)</th>
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
                                <tr>
                                    <td><input type="text" name="med_name[]" class="form-control" placeholder="Ví dụ: Amlodipin 5mg" required></td>
                                    <td><input type="text" name="med_unit[]" class="form-control" value="Viên"></td>
                                    <td><input type="number" name="med_quantity[]" class="form-control" value="30" min="1"></td>
                                    <td><input type="text" name="med_usage[]" class="form-control" placeholder="Uống 1 viên sau ăn sáng"></td>
                                    <td style="text-align:center;"><button type="button" class="btn btn-sm btn-danger remove-med"><i class="fa-solid fa-trash"></i></button></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. LỜI DẶN DÒ Y KHOA & NHẮC NHỞ BỆNH NHÂN -->
            <div class="form-group" style="margin-top:24px;">
                <label class="form-label" style="font-size:15px;font-weight:700;color:#0F172A;">
                    3. Lời dặn dò y khoa & Nhắc nhở người bệnh (Sẽ hiển thị cho bệnh nhân xem)
                </label>
                <textarea name="doctor_notes" class="form-control" rows="3" 
                          placeholder="Ví dụ: Ăn giảm muối, hạn chế dầu mỡ động vật, uống đủ 2 lít nước/ngày, tập thể dục nhẹ nhàng 30 phút mỗi ngày. Tái khám ngay nếu có dấu hiệu đau ngực dữ dội..."><?= htmlspecialchars($prescription['doctor_notes'] ?? ($appointment['doctor_reminder'] ?? '')) ?></textarea>
            </div>

            <!-- 5. HẸN NGÀY TÁI KHÁM -->
            <div class="form-group" style="max-width:320px;margin-top:20px;">
                <label class="form-label" style="font-size:15px;font-weight:700;color:#0F172A;">
                    4. Hẹn ngày tái khám (nếu cần)
                </label>
                <input type="date" name="re_examination_date" class="form-control" 
                       value="<?= htmlspecialchars($prescription['re_examination_date'] ?? '') ?>" min="<?= date('Y-m-d') ?>">
            </div>

            <div style="display:flex;justify-content:flex-end;gap:14px;border-top:1px solid #E2E8F0;padding-top:20px;margin-top:30px;">
                <a href="index.php" class="btn btn-outline">Hủy bỏ</a>
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fa-solid fa-circle-check"></i> Lưu Bệnh Án & Ký Đơn Thuốc Điện Tử
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function addQuickMedicine(name, unit, qty, usage) {
    const tbody = document.getElementById('medicine_rows');
    // Nếu dòng đầu tiên đang trống thì điền vào dòng đó
    const firstRowName = tbody.querySelector('tr:first-child input[name="med_name[]"]');
    if (firstRowName && !firstRowName.value.trim()) {
        const tr = tbody.querySelector('tr:first-child');
        tr.querySelector('input[name="med_name[]"]').value = name;
        tr.querySelector('input[name="med_unit[]"]').value = unit;
        tr.querySelector('input[name="med_quantity[]"]').value = qty;
        tr.querySelector('input[name="med_usage[]"]').value = usage;
        return;
    }

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" name="med_name[]" class="form-control" value="${name}" required></td>
        <td><input type="text" name="med_unit[]" class="form-control" value="${unit}"></td>
        <td><input type="number" name="med_quantity[]" class="form-control" value="${qty}" min="1"></td>
        <td><input type="text" name="med_usage[]" class="form-control" value="${usage}"></td>
        <td style="text-align:center;"><button type="button" class="btn btn-sm btn-danger remove-med"><i class="fa-solid fa-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
    attachRemoveBtn(tr.querySelector('.remove-med'));
}

function attachRemoveBtn(btn) {
    btn.addEventListener('click', function () {
        const tbody = document.getElementById('medicine_rows');
        if (tbody.querySelectorAll('tr').length > 1) {
            this.closest('tr').remove();
        } else {
            alert('Đơn thuốc phải có ít nhất 1 loại thuốc!');
        }
    });
}

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

        tbody.querySelectorAll('.remove-med').forEach(btn => attachRemoveBtn(btn));
    }
});
</script>

<?php require_once __DIR__ . '/../includes/doctor_footer.php'; ?>
