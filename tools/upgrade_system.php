<?php
require_once __DIR__ . '/../includes/db.php';

echo "--- 1. KIEM TRA VA CAP NHAT BANG APPOINTMENTS ---\n";
// Kiểm tra cột doctor_reminder trong appointments
$cols = $pdo->query("SHOW COLUMNS FROM appointments")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('doctor_reminder', $cols)) {
    $pdo->exec("ALTER TABLE appointments ADD COLUMN doctor_reminder TEXT NULL COMMENT 'Lời nhắc nhở trước/trong khám của bác sĩ'");
    echo "-> Da them cot doctor_reminder vao appointments thanh cong!\n";
} else {
    echo "-> Cot doctor_reminder da ton tai trong appointments.\n";
}

echo "\n--- 2. CAP NHAT DOCTOR_SCHEDULES SANG GIOI HAN 5 NGUOI ---\n";
// Cập nhật max_patients = 5 cho tất cả ca trực
$pdo->exec("UPDATE doctor_schedules SET max_patients = 5 WHERE max_patients != 5");
echo "-> Da cap nhat max_patients = 5 cho toan bo ca truc!\n";

// Cập nhật lại số lượng đã đặt (current_booked) theo số lượng thực tế trong appointments (loại trừ CANCELLED)
$schedules = $pdo->query("SELECT id FROM doctor_schedules")->fetchAll(PDO::FETCH_COLUMN);
foreach ($schedules as $sid) {
    $stmtCnt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE schedule_id = ? AND status != 'CANCELLED'");
    $stmtCnt->execute([$sid]);
    $realBooked = (int)$stmtCnt->fetchColumn();
    $status = ($realBooked >= 5) ? 'FULL' : 'AVAILABLE';
    
    $upd = $pdo->prepare("UPDATE doctor_schedules SET current_booked = ?, status = ? WHERE id = ?");
    $upd->execute([$realBooked, $status, $sid]);
}
echo "-> Da dong bo lai current_booked va status theo du lieu thuc te!\n";

echo "\n--- 3. THEM LICH TRUC CHO CAC NGAY TIEP THEO TRONG THANG 10/2026 ---\n";
// Đảm bảo có lịch trực cho các ngày từ 2026-10-13 đến 2026-10-31 cho cả 5 bác sĩ
$doctors = $pdo->query("SELECT id FROM doctors")->fetchAll(PDO::FETCH_COLUMN);
$startDate = new DateTime('2026-10-13');
$endDate = new DateTime('2026-10-31');

$insertStmt = $pdo->prepare("INSERT INTO doctor_schedules (doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) 
    SELECT ?, ?, ?, ?, ?, 5, 0, 'AVAILABLE' 
    FROM DUAL 
    WHERE NOT EXISTS (
        SELECT 1 FROM doctor_schedules WHERE doctor_id = ? AND work_date = ? AND start_time = ?
    )");

while ($startDate <= $endDate) {
    $dStr = $startDate->format('Y-m-d');
    foreach ($doctors as $docId) {
        // Ca Sáng: 07:00 - 11:30 (Max 5)
        $insertStmt->execute([$docId, $dStr, '07:00:00', '11:30:00', 'Ca Sáng (07:00 - 11:30)', $docId, $dStr, '07:00:00']);
        // Ca Chiều: 13:30 - 17:30 (Max 5)
        $insertStmt->execute([$docId, $dStr, '13:30:00', '17:30:00', 'Ca Chiều (13:30 - 17:30)', $docId, $dStr, '13:30:00']);
    }
    $startDate->modify('+1 day');
}
echo "-> Da bo sung lich truc ca sang & chieu (toi da 5 nguoi) cho cac ngay tu 13/10 den 31/10/2026!\n";

echo "\n--- HOAN THANH CAP NHAT DATABASE! ---\n";
