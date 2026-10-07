<?php
/**
 * Header chung cho Website Phòng khám Đa khoa An Nhiên
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

$user = getCurrentUser();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?>Phòng Khám Đa Khoa An Nhiên Nam Định</title>
    <!-- Favicon: Logo An Nhiên -->
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/logo.png">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Main Style -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

    <!-- TOP BAR -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-info">
                <span><i class="fa-solid fa-location-dot"></i> Khu Bồi Tây, Xã Mỹ Phúc, H. Mỹ Lộc, T. Nam Định</span>
                <span><i class="fa-solid fa-phone-volume"></i> Hotline: <strong>0944.809.221</strong></span>
                <span><i class="fa-solid fa-truck-medical"></i> Cấp cứu: <strong>1900 6868</strong></span>
                <span><i class="fa-regular fa-clock"></i> 07:30 - 20:00 (Cả Thứ 7, CN, Lễ)</span>
            </div>
            <div class="top-bar-badges">
                <span class="top-bar-badge"><i class="fa-solid fa-id-card"></i> BHYT Thông Tuyến Toàn Quốc</span>
                <span class="top-bar-badge"><i class="fa-solid fa-handshake"></i> Đối tác Viện VKIM Việt - Hàn</span>
            </div>
        </div>
    </div>

    <!-- MAIN NAVBAR -->
    <nav class="navbar">
        <div class="container">
            <a href="<?= BASE_URL ?>/index.php" class="nav-brand">
                <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Logo Phòng Khám Đa Khoa An Nhiên" class="brand-logo-img">
                <div class="brand-text-wrap">
                    <span class="brand-title">ĐA KHOA AN NHIÊN</span>
                    <span class="brand-sub">NAM ĐỊNH • CHĂM SÓC BẰNG TÂM</span>
                </div>
            </a>

            <button class="mobile-toggle" aria-label="Toggle Navigation">
                <i class="fa-solid fa-bars"></i>
            </button>

            <ul class="nav-menu">
                <li class="nav-item <?= $current_page == 'index.php' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/index.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
                </li>
                <li class="nav-item <?= $current_page == 'chuyen-khoa.php' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/chuyen-khoa.php">Chuyên khoa</a>
                </li>
                <li class="nav-item <?= $current_page == 'bac-si.php' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/bac-si.php">Bác sĩ</a>
                </li>
                <li class="nav-item <?= $current_page == 'phong-kham.php' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/phong-kham.php">Cơ sở vật chất</a>
                </li>
                <li class="nav-item <?= $current_page == 'dat-lich.php' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/dat-lich.php">Đặt lịch khám</a>
                </li>
                <li class="nav-item <?= $current_page == 'tra-cuu.php' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/tra-cuu.php">Tra cứu phiếu</a>
                </li>
                <li class="nav-item <?= $current_page == 'cam-nang.php' || $current_page == 'cam-nang-chi-tiet.php' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/cam-nang.php">Cẩm nang y tế</a>
                </li>
                <li class="nav-item <?= $current_page == 'lien-he.php' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/lien-he.php">Liên hệ & Nhóm 12</a>
                </li>
            </ul>

            <div class="nav-actions">
                <?php if ($user): ?>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <?php if (isAdmin()): ?>
                            <a href="<?= BASE_URL ?>/admin/index.php" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-shield-halved"></i> Quản trị Admin
                            </a>
                        <?php elseif (isDoctor()): ?>
                            <a href="<?= BASE_URL ?>/bac-si/index.php" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-user-doctor"></i> Bàn khám Bác sĩ
                            </a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/benh-nhan/index.php" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-user"></i> Hồ sơ của tôi
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?= BASE_URL ?>/logout.php" class="btn btn-sm btn-outline" title="Đăng xuất">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login.php" class="btn btn-sm btn-outline">
                        <i class="fa-solid fa-user"></i> Đăng nhập
                    </a>
                    <a href="<?= BASE_URL ?>/dat-lich.php" class="btn btn-sm btn-primary btn-pulse">
                        <i class="fa-solid fa-calendar-check"></i> Đặt lịch ngay
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
