<?php
/**
 * Đăng xuất tài khoản
 */
require_once __DIR__ . '/includes/functions.php';

session_unset();
session_destroy();

session_start();
$_SESSION['flash'] = [
    'type' => 'success',
    'message' => 'Bạn đã đăng xuất khỏi hệ thống thành công!'
];

header("Location: " . BASE_URL . "/login.php");
exit;
