<?php
/**
 * Header dành cho phân hệ Bác sĩ (Doctor Portal)
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

requireDoctor();
$docUser = getCurrentUser();

// Lấy thông tin chi tiết bác sĩ
$stmtDocInfo = $pdo->prepare("SELECT d.*, s.name as specialty_name 
                              FROM doctors d 
                              JOIN specialties s ON d.specialty_id = s.id 
                              WHERE d.user_id = ?");
$stmtDocInfo->execute([$docUser['id']]);
$currentDoctor = $stmtDocInfo->fetch();

$activeNav = $activeNav ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?>Phân Hệ Bác Sĩ - An Nhiên Nam Định</title>
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
                <span style="font-weight:800;color:#FFF;font-size:16px;">BÀN KHÁM BÁC SĨ</span>
            </a>
            <span class="badge badge-primary" style="font-size:12px;">PHÒNG KHÁM AN NHIÊN</span>
        </div>
        <div style="display:flex;align-items:center;gap:16px;">
            <span style="font-size:14px;color:#E2E8F0;">
                <i class="fa-solid fa-user-doctor"></i> <?= htmlspecialchars($currentDoctor['full_name'] ?? $docUser['full_name']) ?> (<?= htmlspecialchars($currentDoctor['clinic_room'] ?? '') ?>)
            </span>
            <a href="<?= BASE_URL ?>/logout.php" class="btn btn-sm btn-danger"><i class="fa-solid fa-power-off"></i> Đăng xuất</a>
        </div>
    </div>

    <div class="dashboard-layout">
        <!-- SIDEBAR DOCTOR -->
        <aside class="sidebar">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar" style="background:#0284C7;"><i class="fa-solid fa-stethoscope"></i></div>
                <div class="sidebar-user-info">
                    <h5><?= htmlspecialchars($currentDoctor['full_name'] ?? $docUser['full_name']) ?></h5>
                    <span><?= htmlspecialchars($currentDoctor['specialty_name'] ?? 'Bác sĩ') ?></span>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/bac-si/index.php">
                        <i class="fa-solid fa-users"></i> Bệnh nhân chờ khám hôm nay
                    </a>
                </li>
                <li class="<?= $activeNav === 'lich_truc' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/bac-si/lich-truc.php">
                        <i class="fa-solid fa-calendar-days"></i> Lịch làm việc cá nhân
                    </a>
                </li>
                <li class="<?= $activeNav === 'ho_so_benh_an' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/bac-si/ho-so-benh-nhan.php">
                        <i class="fa-solid fa-book-medical"></i> Lịch sử khám & Đơn thuốc
                    </a>
                </li>
                <li class="<?= $activeNav === 'thong_tin' ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>/bac-si/thong-tin-ca-nhan.php">
                        <i class="fa-solid fa-id-card"></i> Thông tin chuyên môn
                    </a>
                </li>
            </ul>
        </aside>

        <!-- MAIN DOCTOR CONTENT -->
        <main class="main-content">
