<?php
/**
 * API: Lấy các ca làm việc và chia chi tiết khung giờ (7-8h, 8-9h...) của Bác sĩ
 * Quy định: Mỗi ca (Sáng / Chiều) tối đa 5 người bệnh. Đủ 5 người là kín lịch.
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/db.php';

$doctorId = filter_input(INPUT_GET, 'doctor_id', FILTER_VALIDATE_INT) ?: (int)($_GET['doctor_id'] ?? 0);
$date = filter_input(INPUT_GET, 'date') ?: ($_GET['date'] ?? null);

if (!$doctorId || !$date) {
    echo json_encode(['success' => false, 'message' => 'Thiếu tham số bác sĩ hoặc ngày khám', 'schedules' => []]);
    exit;
}

try {
    // 1. Kiểm tra xem ngày này bác sĩ đã có lịch trực trong DB chưa, nếu chưa có thì tự động tạo ca Sáng và Chiều (tối đa 8 người mỗi ca)
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM doctor_schedules WHERE doctor_id = ? AND work_date = ?");
    $stmtCheck->execute([$doctorId, $date]);
    if ($stmtCheck->fetchColumn() == 0) {
        $ins = $pdo->prepare("INSERT INTO doctor_schedules (doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES 
            (?, ?, '07:00:00', '11:30:00', 'Ca Sáng (07:00 - 11:30)', 8, 0, 'AVAILABLE'),
            (?, ?, '13:30:00', '17:30:00', 'Ca Chiều (13:30 - 17:30)', 8, 0, 'AVAILABLE')");
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

        // Lấy tất cả lịch hẹn thực tế trong ca này (trừ các ca đã hủy)
        $stmtApps = $pdo->prepare("SELECT appointment_time FROM appointments WHERE schedule_id = ? AND status != 'CANCELLED'");
        $stmtApps->execute([$sid]);
        $appTimes = $stmtApps->fetchAll(PDO::FETCH_COLUMN);
        $realBooked = count($appTimes);

        $maxPatients = 8; // Ca khám tối đa 8 người (4 khung giờ x 2 người)
        $maxPerSlot = 2;  // Mỗi khung giờ tối đa 2 người

        $isMorning = (strtotime($sch['start_time']) < strtotime('12:00:00'));
        $timeSlots = [];

        if ($isMorning) {
            $slotsDef = [
                ['time' => '07:00:00', 'label' => '07:00 - 08:00', 'sub' => 'Đầu giờ sáng', 'range_start' => '07:00:00', 'range_end' => '07:59:59'],
                ['time' => '08:00:00', 'label' => '08:00 - 09:00', 'sub' => 'Giờ cao điểm', 'range_start' => '08:00:00', 'range_end' => '08:59:59'],
                ['time' => '09:00:00', 'label' => '09:00 - 10:00', 'sub' => 'Giữa buổi sáng', 'range_start' => '09:00:00', 'range_end' => '09:59:59'],
                ['time' => '10:00:00', 'label' => '10:00 - 11:30', 'sub' => 'Cuối buổi sáng', 'range_start' => '10:00:00', 'range_end' => '11:59:59']
            ];
        } else {
            $slotsDef = [
                ['time' => '13:30:00', 'label' => '13:30 - 14:30', 'sub' => 'Đầu giờ chiều', 'range_start' => '13:00:00', 'range_end' => '14:29:59'],
                ['time' => '14:30:00', 'label' => '14:30 - 15:30', 'sub' => 'Giữa buổi chiều', 'range_start' => '14:30:00', 'range_end' => '15:29:59'],
                ['time' => '15:30:00', 'label' => '15:30 - 16:30', 'sub' => 'Chiều mát', 'range_start' => '15:30:00', 'range_end' => '16:29:59'],
                ['time' => '16:30:00', 'label' => '16:30 - 17:30', 'sub' => 'Cuối giờ chiều', 'range_start' => '16:30:00', 'range_end' => '17:59:59']
            ];
        }

        // Tính số lượng người đã đặt theo từng khung giờ cụ thể
        $allSlotsFull = true;
        foreach ($slotsDef as $sDef) {
            $bookedInSlot = 0;
            foreach ($appTimes as $apt) {
                if ($apt >= $sDef['range_start'] && $apt <= $sDef['range_end']) {
                    $bookedInSlot++;
                }
            }

            $slotFull = ($bookedInSlot >= $maxPerSlot);
            if (!$slotFull) {
                $allSlotsFull = false;
            }

            $timeSlots[] = [
                'time' => substr($sDef['time'], 0, 5),
                'full_time' => $sDef['time'],
                'label' => $sDef['label'],
                'sub' => $sDef['sub'],
                'booked_in_slot' => $bookedInSlot,
                'max_in_slot' => $maxPerSlot,
                'remaining' => max(0, $maxPerSlot - $bookedInSlot),
                'is_full' => $slotFull
            ];
        }

        // Ca được coi là kín lịch nếu tổng số người >= 8 hoặc tất cả các khung giờ đều kín
        $isFull = ($realBooked >= $maxPatients || $allSlotsFull);
        $remaining = max(0, $maxPatients - $realBooked);

        // Cập nhật lại DB nếu có sự sai lệch
        $newStatus = $isFull ? 'FULL' : 'AVAILABLE';
        if ($sch['current_booked'] != $realBooked || $sch['status'] != $newStatus || $sch['max_patients'] != $maxPatients) {
            $upd = $pdo->prepare("UPDATE doctor_schedules SET current_booked = ?, max_patients = ?, status = ? WHERE id = ?");
            $upd->execute([$realBooked, $maxPatients, $newStatus, $sid]);
        }

        $schedules[] = [
            'id' => $sid,
            'session_name' => $sch['session_name'] ?: ($isMorning ? 'Ca Sáng (07:00 - 11:30)' : 'Ca Chiều (13:30 - 17:30)'),
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
