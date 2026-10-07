<?php
/**
 * API: Lấy danh sách Bác sĩ theo Chuyên khoa
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/db.php';

$specialtyId = filter_input(INPUT_GET, 'specialty_id', FILTER_VALIDATE_INT);

if (!$specialtyId) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã chuyên khoa', 'doctors' => []]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, full_name, degree, experience_years, consultation_fee, clinic_room 
                           FROM doctors 
                           WHERE specialty_id = ? 
                           ORDER BY full_name ASC");
    $stmt->execute([$specialtyId]);
    $doctors = $stmt->fetchAll();

    echo json_encode(['success' => true, 'doctors' => $doctors]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'doctors' => []]);
}
