<?php
/**
 * Kết nối Cơ sở dữ liệu MySQL bằng PDO
 * Hỗ trợ tự động cấu hình cho XAMPP và Laragon
 * Nhóm 12 - Lớp 74DCTT26 - ĐH Công nghệ Giao thông Vận tải
 */

if (ob_get_level() === 0) {
    ob_start();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    if (strpos($scriptName, '/product_management') === 0) {
        define('BASE_URL', '/product_management');
    } else {
        define('BASE_URL', '');
    }
}

$db_config = [
    'host' => '127.0.0.1',
    'port' => '3306',
    'dbname' => 'phongkham_annhien',
    'charset' => 'utf8mb4'
];

$passwords_to_try = ['123456', '', 'root', 'rootpassword123'];
$pdo = null;
$error_message = '';

foreach ($passwords_to_try as $pwd) {
    try {
        $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['dbname']};charset={$db_config['charset']}";
        $pdo = new PDO($dsn, 'root', $pwd, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]);
        break; // Kết nối thành công!
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
    }
}

if (!$pdo) {
    die("<div style='font-family:sans-serif;padding:30px;background:#FEF2F2;border:1px solid #F87171;color:#991B1B;border-radius:8px;max-width:700px;margin:50px auto;'>
        <h3 style='margin-top:0;'>❌ Lỗi kết nối Cơ sở dữ liệu (MySQL)</h3>
        <p>Hệ thống không thể kết nối tới cơ sở dữ liệu <strong>{$db_config['dbname']}</strong>.</p>
        <p>Chi tiết lỗi: <code>{$error_message}</code></p>
        <p>Vui lòng đảm bảo dịch vụ MySQL đang chạy trên cổng 3306 và đã chạy file tạo dữ liệu: <code>database/seed_data.php</code>.</p>
    </div>");
}

return $pdo;
