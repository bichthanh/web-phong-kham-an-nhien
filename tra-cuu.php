<?php
$page_title = "Tra cứu phiếu hẹn & Đơn thuốc";
require_once __DIR__ . '/includes/header.php';

$code = sanitizeInput($_GET['code'] ?? '');
$phone = sanitizeInput($_GET['phone'] ?? '');
$search_query = $code ?: $phone;

$appointments = [];
$prescription = null;
$searched = false;

// Xử lý Hủy lịch nếu bệnh nhân yêu cầu
if (isset($_POST['action']) && $_POST['action'] === 'cancel_appointment') {
    $cancelCode = sanitizeInput($_POST['appointment_code'] ?? '');
    if ($cancelCode) {
        $stmtC = $pdo->prepare("SELECT id, schedule_id, status FROM appointments WHERE appointment_code = ?");
        $stmtC->execute([$cancelCode]);
        $appToCancel = $stmtC->fetch();

        if ($appToCancel && in_array($appToCancel['status'], ['PENDING', 'CONFIRMED'])) {
            $pdo->beginTransaction();
            // Cập nhật trạng thái thành CANCELLED
            $pdo->prepare("UPDATE appointments SET status = 'CANCELLED' WHERE id = ?")->execute([$appToCancel['id']]);
            // Hoàn lại 1 slot trống cho ca trực
            $pdo->prepare("UPDATE doctor_schedules SET current_booked = GREATEST(current_booked - 1, 0), status = 'AVAILABLE' WHERE id = ?")->execute([$appToCancel['schedule_id']]);
            $pdo->commit();
            setFlashMessage('success', "Đã hủy lịch khám mã {$cancelCode} thành công!");
            header("Location: tra-cuu.php?code=" . urlencode($cancelCode));
            exit;
        } else {
            setFlashMessage('error', "Không thể hủy lịch khám đã tiếp nhận hoặc đã hoàn thành!");
        }
    }
}

if ($search_query) {
    $searched = true;
    $sql = "SELECT a.*, d.full_name as doctor_name, d.degree as doctor_degree, 
                   d.clinic_room, s.name as specialty_name, ds.session_name
            FROM appointments a
            JOIN doctors d ON a.doctor_id = d.id
            JOIN specialties s ON d.specialty_id = s.id
            LEFT JOIN doctor_schedules ds ON a.schedule_id = ds.id
            WHERE a.appointment_code = ? OR a.patient_phone = ?
            ORDER BY a.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$search_query, $search_query]);
    $appointments = $stmt->fetchAll();

    // Nếu chỉ có 1 kết quả và đã COMPLETED, lấy luôn đơn thuốc
    if (count($appointments) === 1 && $appointments[0]['status'] === 'COMPLETED') {
        $stmtP = $pdo->prepare("SELECT * FROM prescriptions WHERE appointment_id = ?");
        $stmtP->execute([$appointments[0]['id']]);
        $prescription = $stmtP->fetch();
    }
}

$flash = getFlashMessage();
?>

<div style="background:linear-gradient(135deg, #042F2E 0%, #0F172A 100%);color:#FFF;padding:45px 0;">
    <div class="container">
        <h1 style="font-size:32px;font-weight:800;margin-bottom:8px;">Tra Cứu Phiếu Hẹn & Đơn Thuốc</h1>
        <p style="color:#94A3B8;font-size:16px;">Tra cứu tiến độ lịch khám, kết quả chẩn đoán và đơn thuốc sau khám mọi lúc mọi nơi.</p>
    </div>
</div>

<section class="section">
    <div class="container" style="max-width:960px;">

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
                <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                <div><?= htmlspecialchars($flash['message']) ?></div>
            </div>
        <?php endif; ?>

        <!-- FORM TÌM KIẾM -->
        <div class="card" style="margin-bottom:35px;">
            <div class="card-body" style="padding:30px;">
                <form action="<?= BASE_URL ?>/tra-cuu.php" method="GET" style="display:flex;gap:12px;flex-wrap:wrap;">
                    <div style="flex:1;min-width:260px;">
                        <input type="text" name="code" class="form-control" 
                               placeholder="Nhập Mã phiếu hẹn (VD: AN-2026-001) hoặc Số điện thoại..." 
                               value="<?= htmlspecialchars($search_query) ?>" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary" style="padding:12px 24px;">
                        <i class="fa-solid fa-magnifying-glass"></i> Tra cứu ngay
                    </button>
                </form>
                <div style="margin-top:12px;font-size:13px;color:#64748B;">
                    <i class="fa-solid fa-lightbulb" style="color:var(--warning);"></i> 
                    Gợi ý mã kiểm thử mẫu: <code>AN-2026-001</code> (Đã hoàn thành, có đơn thuốc), <code>AN-2026-002</code>, <code>0988776655</code>
                </div>
            </div>
        </div>

        <?php if ($searched): ?>
            <?php if (empty($appointments)): ?>
                <div class="card" style="text-align:center;padding:50px 20px;">
                    <i class="fa-regular fa-folder-open fa-3x" style="color:#CBD5E1;margin-bottom:12px;"></i>
                    <h3 style="font-size:18px;color:#0F172A;margin-bottom:6px;">Không tìm thấy lịch hẹn phù hợp</h3>
                    <p style="color:#64748B;font-size:14px;">Vui lòng kiểm tra lại chính xác Mã phiếu hẹn hoặc Số điện thoại bạn đã đăng ký.</p>
                </div>
            <?php else: ?>

                <?php foreach ($appointments as $app): ?>
                    <div class="card" style="margin-bottom:30px;border-top:4px solid var(--primary);">
                        <div class="card-header" style="background:#F8FAFC;">
                            <div>
                                <span style="font-size:13px;color:#64748B;">MÃ PHIẾU HẸN:</span>
                                <strong style="font-size:18px;color:var(--primary-dark);margin-left:6px;"><?= htmlspecialchars($app['appointment_code']) ?></strong>
                            </div>
                            <div style="display:flex;gap:10px;align-items:center;">
                                <?= getStatusBadge($app['status']) ?>
                                <?= getPaymentBadge($app['payment_status']) ?>
                            </div>
                        </div>

                        <div class="card-body">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
                                <!-- Cột 1: Thông tin bệnh nhân -->
                                <div>
                                    <h4 style="font-size:15px;color:var(--primary);margin-bottom:12px;text-transform:uppercase;letter-spacing:0.5px;">
                                        <i class="fa-solid fa-user"></i> Thông tin bệnh nhân
                                    </h4>
                                    <p style="margin-bottom:6px;"><strong>Họ và tên:</strong> <?= htmlspecialchars($app['patient_name']) ?></p>
                                    <p style="margin-bottom:6px;"><strong>Số điện thoại:</strong> <?= htmlspecialchars($app['patient_phone']) ?></p>
                                    <p style="margin-bottom:6px;"><strong>Email:</strong> <?= htmlspecialchars($app['patient_email'] ?: 'Chưa cập nhật') ?></p>
                                    <p style="margin-bottom:6px;"><strong>Giới tính:</strong> <?= htmlspecialchars($app['patient_gender']) ?></p>
                                    <p style="margin-bottom:6px;"><strong>Địa chỉ:</strong> <?= htmlspecialchars($app['patient_address'] ?: 'Nam Định') ?></p>
                                </div>

                                <!-- Cột 2: Thông tin ca khám -->
                                <div>
                                    <h4 style="font-size:15px;color:var(--primary);margin-bottom:12px;text-transform:uppercase;letter-spacing:0.5px;">
                                        <i class="fa-solid fa-hospital-user"></i> Thông tin buổi khám
                                    </h4>
                                    <p style="margin-bottom:6px;"><strong>Chuyên khoa:</strong> <?= htmlspecialchars($app['specialty_name']) ?></p>
                                    <p style="margin-bottom:6px;"><strong>Bác sĩ khám:</strong> <?= htmlspecialchars($app['doctor_degree']) ?> <?= htmlspecialchars($app['doctor_name']) ?></p>
                                    <p style="margin-bottom:6px;"><strong>Phòng khám:</strong> <span class="badge badge-purple"><?= htmlspecialchars($app['clinic_room']) ?></span></p>
                                    <p style="margin-bottom:6px;"><strong>Ngày hẹn:</strong> <span style="color:#0284C7;font-weight:700;"><?= formatDate($app['appointment_date']) ?></span></p>
                                    <p style="margin-bottom:6px;"><strong>Khung giờ:</strong> <?= formatTime($app['appointment_time']) ?> (<?= htmlspecialchars($app['session_name'] ?? 'Ca trong ngày') ?>)</p>
                                    <p style="margin-bottom:6px;"><strong>Phí khám:</strong> <strong style="color:#DC2626;"><?= formatMoney($app['amount']) ?></strong></p>
                                </div>
                            </div>

                            <div style="background:#F1F5F9;padding:14px;border-radius:8px;margin-bottom:20px;">
                                <strong style="font-size:13.5px;color:#0F172A;"><i class="fa-solid fa-clipboard-list"></i> Triệu chứng khai báo:</strong>
                                <p style="font-size:14px;color:#334155;margin-top:4px;"><?= nl2br(htmlspecialchars($app['symptom_description'])) ?></p>
                            </div>

                            <!-- NÚT HÀNH ĐỘNG CHO PHIẾU HẸN -->
                            <div style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid #E2E8F0;padding-top:16px;flex-wrap:wrap;gap:12px;">
                                <div style="display:flex;gap:10px;">
                                    <button type="button" onclick="window.print()" class="btn btn-sm btn-outline">
                                        <i class="fa-solid fa-print"></i> In phiếu khám
                                    </button>
                                </div>

                                <div>
                                    <?php if (in_array($app['status'], ['PENDING', 'CONFIRMED'])): ?>
                                        <form method="POST" action="<?= BASE_URL ?>/tra-cuu.php" onsubmit="return confirm('Bạn có chắc chắn muốn hủy phiếu hẹn này không? Hành động này không thể hoàn tác.');" style="display:inline;">
                                            <input type="hidden" name="action" value="cancel_appointment">
                                            <input type="hidden" name="appointment_code" value="<?= htmlspecialchars($app['appointment_code']) ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa-solid fa-trash-can"></i> Hủy lịch hẹn này
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- ĐƠN THUỐC ĐIỆN TỬ (NẾU ĐÃ HOÀN THÀNH KHÁM) -->
                            <?php 
                            if ($app['status'] === 'COMPLETED') {
                                $stmtPr = $pdo->prepare("SELECT * FROM prescriptions WHERE appointment_id = ?");
                                $stmtPr->execute([$app['id']]);
                                $pres = $stmtPr->fetch();
                                if ($pres): 
                                    $medList = json_decode($pres['medicine_list'], true) ?: [];
                            ?>
                                <div class="prescription-view" style="margin-top:28px;">
                                    <div class="pres-header">
                                        <div>
                                            <h3 style="font-size:16px;color:var(--primary-dark);text-transform:uppercase;margin-bottom:2px;">
                                                PHÒNG KHÁM ĐA KHOA AN NHIÊN
                                            </h3>
                                            <p style="font-size:12px;color:#64748B;">Cơ sở y tế VKIM Nam Định • Hotline: 0944.809.221</p>
                                        </div>
                                        <div style="text-align:right;">
                                            <span style="font-size:12px;color:#64748B;">Mã đơn: <strong>DT-<?= $pres['id'] ?></strong></span><br>
                                            <span style="font-size:12px;color:#64748B;">Ngày kê: <?= formatDate($pres['created_at']) ?></span>
                                        </div>
                                    </div>

                                    <h2 class="pres-title">ĐƠN THUỐC ĐIỆN TỬ</h2>

                                    <div style="margin-bottom:20px;padding:12px;background:#F8FAFC;border-radius:6px;font-size:14px;">
                                        <strong>Chẩn đoán bệnh của bác sĩ:</strong>
                                        <p style="color:#0284C7;font-weight:700;margin-top:4px;font-size:15px;">
                                            <?= htmlspecialchars($pres['diagnosis']) ?>
                                        </p>
                                    </div>

                                    <h4 style="font-size:15px;margin-bottom:10px;color:#0F172A;"><i class="fa-solid fa-pills" style="color:var(--primary);"></i> Chỉ định thuốc:</h4>
                                    <table class="table" style="margin-bottom:20px;">
                                        <thead>
                                            <tr>
                                                <th style="width:40px;">STT</th>
                                                <th>Tên thuốc / Biệt dược</th>
                                                <th style="width:80px;">Đơn vị</th>
                                                <th style="width:70px;">SL</th>
                                                <th>Cách dùng / Liều uống</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1; foreach ($medList as $med): ?>
                                                <tr>
                                                    <td><?= $stt++ ?></td>
                                                    <td><strong><?= htmlspecialchars($med['name']) ?></strong></td>
                                                    <td><?= htmlspecialchars($med['unit']) ?></td>
                                                    <td><strong style="color:var(--primary-dark);"><?= htmlspecialchars($med['quantity']) ?></strong></td>
                                                    <td><?= htmlspecialchars($med['usage']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <?php if (!empty($pres['doctor_notes'])): ?>
                                        <div style="margin-bottom:20px;font-size:13.5px;color:#334155;background:#FFFBEB;padding:12px;border-radius:6px;border-left:4px solid var(--warning);">
                                            <strong><i class="fa-solid fa-user-doctor"></i> Lời dặn dò của bác sĩ:</strong>
                                            <p style="margin-top:4px;"><?= nl2br(htmlspecialchars($pres['doctor_notes'])) ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($pres['re_examination_date'])): ?>
                                        <div style="margin-bottom:20px;font-size:14px;color:#065F46;background:#D1FAE5;padding:10px 14px;border-radius:6px;">
                                            <strong><i class="fa-regular fa-calendar-check"></i> Hẹn tái khám vào ngày:</strong> 
                                            <strong style="font-size:15px;"><?= formatDate($pres['re_examination_date']) ?></strong>
                                        </div>
                                    <?php endif; ?>

                                    <div style="display:flex;justify-content:space-between;margin-top:30px;padding-top:20px;border-top:1px dashed #CBD5E1;">
                                        <div style="font-size:12px;color:#94A3B8;">
                                            Đơn thuốc có giá trị mua tại nhà thuốc An Nhiên<br>
                                            hoặc các nhà thuốc đạt chuẩn GPP trên toàn quốc.
                                        </div>
                                        <div style="text-align:center;">
                                            <div style="font-size:13px;color:#64748B;">Bác sĩ điều trị</div>
                                            <div style="font-weight:700;color:var(--primary-dark);margin-top:35px;">
                                                <?= htmlspecialchars($app['doctor_degree']) ?> <?= htmlspecialchars($app['doctor_name']) ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="text-align:center;margin-top:24px;" class="no-print">
                                        <button type="button" onclick="window.print()" class="btn btn-primary">
                                            <i class="fa-solid fa-print"></i> In Đơn Thuốc Này
                                        </button>
                                    </div>
                                </div>
                            <?php endif; } ?>

                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        <?php endif; ?>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
