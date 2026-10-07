<?php
/**
 * API: Lấy các ca làm việc của Bác sĩ theo ngày
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
    $stmt = $pdo->prepare("SELECT id, session_name, start_time, end_time, max_patients, current_booked, status 
                           FROM doctor_schedules 
                           WHERE doctor_id = ? AND work_date = ? AND status != 'OFF'
                           ORDER BY start_time ASC");
    $stmt->execute([$doctorId, $date]);
    $schedules = $stmt->fetchAll();

    echo json_encode(['success' => true, 'schedules' => $schedules]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'schedules' => []]);
}
