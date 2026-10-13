<?php
/**
 * API: Lấy các ca làm việc và chia chi tiết khung giờ (7-8h, 8-9h...) của Bác sĩ
 * Quy định: Mỗi ca (Sáng / Chiều) tối đa 5 người bệnh. Đủ 5 người là kín lịch.
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/db.php';

$doctorId = filter_input(INPUT_GET, 'doctor_id', FILTER_VALIDATE_INT);
$date = filter_input(INPUT_GET, 'date');

if (!$doctorId || !$date) {
    echo json_encode(['success' => false, 'message' => 'Thiếu tham số bác sĩ hoặc ngày khám', 'schedules' => []]);
    exit;
}

try {
    // 1. Kiểm tra xem ngày này bác sĩ đã có lịch trực trong DB chưa, nếu chưa có thì tự động tạo ca Sáng và Chiều
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM doctor_schedules WHERE doctor_id = ? AND work_date = ?");
    $stmtCheck->execute([$doctorId, $date]);
    if ($stmtCheck->fetchColumn() == 0) {
        $ins = $pdo->prepare("INSERT INTO doctor_schedules (doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES 
            (?, ?, '07:00:00', '11:30:00', 'Ca Sáng (07:00 - 11:30)', 5, 0, 'AVAILABLE'),
            (?, ?, '13:30:00', '17:30:00', 'Ca Chiều (13:30 - 17:30)', 5, 0, 'AVAILABLE')");
        $ins->execute([$doctorId, $date, $doctorId, $date]);
    }

    // 2. Lấy danh sách ca trực của bác sĩ trong ngày
    $stmt = $pdo->prepare("SELECT id, session_name, start_time, end_time, max_patients, current_booked, status 
                           FROM doctor_schedules 
                           WHERE doctor_id = ? AND work_date = ? AND status != 'OFF'
                           ORDER BY start_time ASC");
    $stmt->execute([$doctorId, $date]);
    $rawSchedules = $stmt->fetchAll();

    $schedules = [];

    foreach ($rawSchedules as $sch) {
        $sid = $sch['id'];

        // Đếm số lượng bệnh nhân thực tế đã đặt trong ca này (trừ các ca đã hủy)
        $stmtCnt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE schedule_id = ? AND status != 'CANCELLED'");
        $stmtCnt->execute([$sid]);
        $realBooked = (int)$stmtCnt->fetchColumn();

        $maxPatients = 5; // Cố định 5 người/ca theo yêu cầu nghiệp vụ
        $isFull = ($realBooked >= $maxPatients);
        $remaining = max(0, $maxPatients - $realBooked);

        // Cập nhật lại DB nếu có sự sai lệch
        $newStatus = $isFull ? 'FULL' : 'AVAILABLE';
        if ($sch['current_booked'] != $realBooked || $sch['status'] != $newStatus || $sch['max_patients'] != 5) {
            $upd = $pdo->prepare("UPDATE doctor_schedules SET current_booked = ?, max_patients = 5, status = ? WHERE id = ?");
            $upd->execute([$realBooked, $newStatus, $sid]);
        }

        // Định nghĩa các khung giờ chi tiết từng tiếng (7-8h, 8-9h, 13h30-14h30,...)
        $isMorning = (strtotime($sch['start_time']) < strtotime('12:00:00'));
        $timeSlots = [];

        if ($isMorning) {
            $slotsDef = [
                ['time' => '07:00:00', 'label' => '07:00 - 08:00', 'sub' => 'Đầu giờ sáng'],
                ['time' => '08:00:00', 'label' => '08:00 - 09:00', 'sub' => 'Giờ cao điểm'],
                ['time' => '09:00:00', 'label' => '09:00 - 10:00', 'sub' => 'Giữa buổi sáng'],
                ['time' => '10:00:00', 'label' => '10:00 - 11:00', 'sub' => 'Cuối buổi sáng']
            ];
        } else {
            $slotsDef = [
                ['time' => '13:30:00', 'label' => '13:30 - 14:30', 'sub' => 'Đầu giờ chiều'],
                ['time' => '14:30:00', 'label' => '14:30 - 15:30', 'sub' => 'Giữa buổi chiều'],
                ['time' => '15:30:00', 'label' => '15:30 - 16:30', 'sub' => 'Chiều mát'],
                ['time' => '16:30:00', 'label' => '16:30 - 17:30', 'sub' => 'Cuối giờ chiều']
            ];
        }

        // Lấy số lượng người đã đặt theo từng khung giờ cụ thể
        $stmtSlots = $pdo->prepare("SELECT appointment_time, COUNT(*) as cnt FROM appointments WHERE schedule_id = ? AND status != 'CANCELLED' GROUP BY appointment_time");
        $stmtSlots->execute([$sid]);
        $bookedTimes = $stmtSlots->fetchAll(PDO::FETCH_KEY_PAIR);

        foreach ($slotsDef as $sDef) {
            $t = $sDef['time'];
            $bookedInSlot = isset($bookedTimes[$t]) ? (int)$bookedTimes[$t] : 0;
            $timeSlots[] = [
                'time' => substr($t, 0, 5),
                'full_time' => $t,
                'label' => $sDef['label'],
                'sub' => $sDef['sub'],
                'booked_in_slot' => $bookedInSlot
            ];
        }

        $schedules[] = [
            'id' => $sid,
            'session_name' => $isMorning ? 'Ca Sáng (07:00 - 11:30)' : 'Ca Chiều (13:30 - 17:30)',
            'is_morning' => $isMorning,
            'max_patients' => $maxPatients,
            'current_booked' => $realBooked,
            'remaining' => $remaining,
            'is_full' => $isFull,
            'time_slots' => $timeSlots
        ];
    }

    echo json_encode([
        'success' => true,
        'doctor_id' => $doctorId,
        'date' => $date,
        'schedules' => $schedules
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'schedules' => []]);
}
