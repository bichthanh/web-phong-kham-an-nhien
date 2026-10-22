<?php
require_once __DIR__ . '/../includes/db.php';

echo "--- 1. KIEM TRA VA THEM COT MEDICINE_FEE VAO PRESCRIPTIONS ---\n";
$cols = $pdo->query("SHOW COLUMNS FROM prescriptions")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('medicine_fee', $cols)) {
    $pdo->exec("ALTER TABLE prescriptions ADD COLUMN medicine_fee DECIMAL(10,2) DEFAULT '0.00' COMMENT 'Chi phí tiền thuốc'");
    echo "-> Da them cot medicine_fee vao bang prescriptions thanh cong!\n";
} else {
    echo "-> Cot medicine_fee da ton tai trong prescriptions.\n";
}

// Cập nhật giá trị mẫu cho các đơn thuốc cũ
$pdo->exec("UPDATE prescriptions SET medicine_fee = 180000.00 WHERE id = 1 AND (medicine_fee = 0 OR medicine_fee IS NULL)");
$pdo->exec("UPDATE prescriptions SET medicine_fee = 320000.00 WHERE id = 2 AND (medicine_fee = 0 OR medicine_fee IS NULL)");

echo "\n--- HOAN THANH CAP NHAT DATABASE CHO TINH NANG THANH TOAN! ---\n";
