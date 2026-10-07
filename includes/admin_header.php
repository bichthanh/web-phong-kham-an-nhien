<?php
/**
 * Header dành cho phân hệ Quản trị viên (Admin Portal)
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

requireAdmin();
$adminUser = getCurrentUser();
$activeNav = $activeNav ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?>Quản Trị Hệ Thống An Nhiên</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <!-- Chart.js for Admin Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <!-- TOP HEADER -->
    <div style="background:#0F172A;color:#FFF;padding:12px 24px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #334155;">
        <div style="display:flex;align-items:center;gap:14px;">
            <a href="<?= BASE_URL ?>/index.php" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Logo" style="height:40px;width:40px;border-radius:50%;background:#FFF;padding:2px;">
                <span style="font-weight:800;color:#FFF;font-size:16px;letter-spacing:0.5px;">AN NHIÊN ADMIN</span>
            </a>
            <span class="badge badge-warning" style="font-size:12px;">HỆ THỐNG QUẢN TRỊ TOÀN DIỆN</span>
        </div>
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="<?= BASE_URL ?>/index.php" target="_blank" class="btn btn-sm btn-outline" style="color:#CBD5E1;border-color:#475569;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem Website
            </a>
            <span style="font-size:14px;color:#E2E8F0;"><i class="fa-solid fa-user-shield"></i> <?= htmlspecialchars($adminUser['full_name']) ?></span>
            <a href="<?= BASE_URL ?>/logout.php" class="btn btn-sm btn-danger"><i class="fa-solid fa-power-off"></i> Đăng xuất</a>
        </div>
    </div>

    <div class="dashboard-layout">
        <!-- SIDEBAR ADMIN -->
        <aside class="sidebar">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar"><i class="fa-solid fa-shield-halved"></i></div>
                <div class="sidebar-user-info">
                    <h5><?= htmlspecialchars($adminUser['username']) ?></h5>
                    <span>Quản trị viên tối cao</span>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/admin/index.php">
                        <i class="fa-solid fa-chart-pie"></i> Bảng điều khiển (KPI)
                    </a>
                </li>
                <li class="<?= $activeNav === 'lich_hen' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/admin/lich-hen.php">
                        <i class="fa-solid fa-calendar-check"></i> Quản lý lịch hẹn
                    </a>
                </li>
                <li class="<?= $activeNav === 'lich_lam_viec' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/admin/lich-lam-viec.php">
                        <i class="fa-solid fa-calendar-days"></i> Phân ca trực bác sĩ
                    </a>
                </li>
                <li class="<?= $activeNav === 'bac_si' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/admin/bac-si.php">
                        <i class="fa-solid fa-user-doctor"></i> Quản lý bác sĩ
                    </a>
                </li>
                <li class="<?= $activeNav === 'chuyen_khoa' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/admin/chuyen-khoa.php">
                        <i class="fa-solid fa-stethoscope"></i> Quản lý chuyên khoa
                    </a>
                </li>
                <li class="<?= $activeNav === 'phong_kham' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/admin/phong-kham.php">
                        <i class="fa-solid fa-hospital"></i> Quản lý phòng khám
                    </a>
                </li>
                <li class="<?= $activeNav === 'tai_khoan' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/admin/tai-khoan.php">
                        <i class="fa-solid fa-users-gear"></i> Tài khoản người dùng
                    </a>
                </li>
                <li class="<?= $activeNav === 'bai_viet' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/admin/bai-viet.php">
                        <i class="fa-solid fa-newspaper"></i> Bài viết y tế
                    </a>
                </li>
                <li class="<?= $activeNav === 'phan_hoi' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/admin/phan-hoi.php">
                        <i class="fa-solid fa-comments"></i> Hỗ trợ & Phản hồi
                    </a>
                </li>
                <li class="<?= $activeNav === 'bao_cao' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/admin/bao-cao.php">
                        <i class="fa-solid fa-file-waveform"></i> Báo cáo thống kê
                    </a>
                </li>
            </ul>
        </aside>

        <!-- MAIN ADMIN CONTENT -->
        <main class="main-content">
