<?php
require_once __DIR__ . '/../includes/db.php';

echo "--- DỌN DẸP TRÙNG LỊCH TRỰC TRONG DOCTOR_SCHEDULES ---\n";

// Tìm các ca trực bị trùng (cùng doctor_id, work_date, và cùng ca sáng/chiều)
$duplicates = $pdo->query("
    SELECT doctor_id, work_date, 
           CASE WHEN start_time < '12:00:00' THEN 'MORNING' ELSE 'AFTERNOON' END as period,
           COUNT(*) as cnt,
           GROUP_CONCAT(id ORDER BY id ASC) as ids
    FROM doctor_schedules
    GROUP BY doctor_id, work_date, period
    HAVING cnt > 1
")->fetchAll();

foreach ($duplicates as $dup) {
    $ids = explode(',', $dup['ids']);
    // Giữ lại id đầu tiên có liên kết với appointments, hoặc id nhỏ nhất
    $keepId = null;
    foreach ($ids as $id) {
        $hasApp = $pdo->query("SELECT COUNT(*) FROM appointments WHERE schedule_id = " . (int)$id)->fetchColumn();
        if ($hasApp > 0) {
            $keepId = $id;
            break;
        }
    }
    if (!$keepId) {
        $keepId = $ids[0];
    }

    foreach ($ids as $id) {
        if ($id != $keepId) {
            // Chuyển appointments (nếu có) sang keepId
            $pdo->prepare("UPDATE appointments SET schedule_id = ? WHERE schedule_id = ?")->execute([$keepId, $id]);
            // Xóa schedule thừa
            $pdo->prepare("DELETE FROM doctor_schedules WHERE id = ?")->execute([$id]);
        }
    }
}

// Chuẩn hóa lại start_time, end_time và session_name
$pdo->exec("UPDATE doctor_schedules SET 
    start_time = '07:00:00', end_time = '11:30:00', session_name = 'Ca Sáng (07:00 - 11:30)', max_patients = 5 
    WHERE start_time < '12:00:00'");

$pdo->exec("UPDATE doctor_schedules SET 
    start_time = '13:30:00', end_time = '17:30:00', session_name = 'Ca Chiều (13:30 - 17:30)', max_patients = 5 
    WHERE start_time >= '12:00:00'");

echo "-> Da don dep sach se cac ca trung! Chi con dung 1 Ca Sang va 1 Ca Chieu moi ngay cho moi bac si.\n";
