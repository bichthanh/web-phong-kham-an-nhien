<?php
/**
 * Quản lý phiên làm việc, xác thực và phân quyền (Authentication & Authorization)
 * Phòng khám Đa khoa An Nhiên
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'full_name' => $_SESSION['full_name'],
        'email' => $_SESSION['email'],
        'phone' => $_SESSION['phone'],
        'role' => $_SESSION['role'],
        'doctor_id' => $_SESSION['doctor_id'] ?? null
    ];
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'ROLE_ADMIN';
}

function isDoctor() {
    return isLoggedIn() && $_SESSION['role'] === 'ROLE_DOCTOR';
}

function isPatient() {
    return isLoggedIn() && $_SESSION['role'] === 'ROLE_PATIENT';
}

function requireLogin($redirectTo = null) {
    if ($redirectTo === null) $redirectTo = (defined('BASE_URL') ? BASE_URL : '') . '/login.php';
    if (!isLoggedIn()) {
        header("Location: $redirectTo");
        exit;
    }
}

function requireAdmin($redirectTo = null) {
    if ($redirectTo === null) $redirectTo = (defined('BASE_URL') ? BASE_URL : '') . '/login.php';
    if (!isAdmin()) {
        header("Location: $redirectTo");
        exit;
    }
}

function requireDoctor($redirectTo = null) {
    if ($redirectTo === null) $redirectTo = (defined('BASE_URL') ? BASE_URL : '') . '/login.php';
    if (!isDoctor()) {
        header("Location: $redirectTo");
        exit;
    }
}

function requirePatient($redirectTo = null) {
    if ($redirectTo === null) $redirectTo = (defined('BASE_URL') ? BASE_URL : '') . '/login.php';
    if (!isPatient()) {
        header("Location: $redirectTo");
        exit;
    }
}
