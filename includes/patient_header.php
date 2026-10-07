<?php
/**
 * Header dành cho phân hệ Bệnh nhân (Patient Portal)
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

requirePatient();
$patUser = getCurrentUser();
$activeNav = $activeNav ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?>Cổng Thông Tin Bệnh Nhân - An Nhiên Nam Định</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

    <!-- TOP HEADER -->
    <div style="background:#0F172A;color:#FFF;padding:12px 24px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #334155;">
        <div style="display:flex;align-items:center;gap:14px;">
            <a href="<?= BASE_URL ?>/index.php" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Logo" style="height:40px;width:40px;border-radius:50%;background:#FFF;padding:2px;">
                <span style="font-weight:800;color:#FFF;font-size:16px;">AN NHIÊN CLINIC</span>
            </a>
            <span class="badge badge-success" style="font-size:12px;">CỔNG DỊCH VỤ BỆNH NHÂN</span>
        </div>
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-sm btn-outline" style="color:#CBD5E1;border-color:#475569;">
                <i class="fa-solid fa-house"></i> Trang chủ
            </a>
            <span style="font-size:14px;color:#E2E8F0;"><i class="fa-solid fa-user-circle"></i> <?= htmlspecialchars($patUser['full_name']) ?></span>
            <a href="<?= BASE_URL ?>/logout.php" class="btn btn-sm btn-danger"><i class="fa-solid fa-power-off"></i> Đăng xuất</a>
        </div>
    </div>

    <div class="dashboard-layout">
        <!-- SIDEBAR PATIENT -->
        <aside class="sidebar">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar" style="background:#10B981;"><i class="fa-solid fa-user"></i></div>
                <div class="sidebar-user-info">
                    <h5><?= htmlspecialchars($patUser['full_name']) ?></h5>
                    <span><?= htmlspecialchars($patUser['phone']) ?></span>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/benh-nhan/index.php">
                        <i class="fa-solid fa-calendar-check"></i> Lịch khám của tôi
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/dat-lich.php">
                        <i class="fa-solid fa-calendar-plus"></i> Đặt lịch khám mới
                    </a>
                </li>
                <li class="<?= $activeNav === 'don_thuoc' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/benh-nhan/don-thuoc.php">
                        <i class="fa-solid fa-file-prescription"></i> Đơn thuốc & Kết quả
                    </a>
                </li>
                <li class="<?= $activeNav === 'thanh_toan' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/benh-nhan/thanh-toan.php">
                        <i class="fa-solid fa-receipt"></i> Thanh toán & Hóa đơn
                    </a>
                </li>
                <li class="<?= $activeNav === 'ho_so' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/benh-nhan/ho-so.php">
                        <i class="fa-solid fa-user-gear"></i> Thông tin cá nhân
                    </a>
                </li>
            </ul>
        </aside>

        <!-- MAIN PATIENT CONTENT -->
        <main class="main-content">
