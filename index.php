<?php
$page_title = "Trang chủ";
require_once __DIR__ . '/includes/header.php';

// Lấy danh sách chuyên khoa
$stmtSpec = $pdo->query("SELECT * FROM specialties ORDER BY id ASC LIMIT 8");
$specialties = $stmtSpec->fetchAll();

// Lấy danh sách bác sĩ tiêu biểu
$stmtDoc = $pdo->query("SELECT d.*, s.name as specialty_name 
                        FROM doctors d 
                        JOIN specialties s ON d.specialty_id = s.id 
                        ORDER BY d.experience_years DESC LIMIT 6");
$doctors = $stmtDoc->fetchAll();

// Lấy bài viết mới nhất
$stmtPosts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC LIMIT 3");
$recentPosts = $stmtPosts->fetchAll();
?>

<!-- HERO BANNER -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content">
                <div class="hero-tag">
                    <i class="fa-solid fa-shield-heart" style="color:#DC2626;"></i> Cơ sở y tế thực hành lâm sàng VKIM Việt - Hàn
                </div>
                <h1 class="hero-title">
                    Phòng Khám Đa Khoa <span>An Nhiên</span> Nam Định
                </h1>
                <p class="hero-subtitle">
                    Hệ thống chăm sóc sức khỏe toàn diện với đội ngũ chuyên gia giàu kinh nghiệm, trang thiết bị chẩn đoán hiện đại và chính sách BHYT thông tuyến toàn quốc.
                </p>
                <div class="hero-buttons">
                    <a href="<?= BASE_URL ?>/dat-lich.php" class="btn btn-lg btn-primary btn-pulse">
                        <i class="fa-solid fa-calendar-check"></i> Đặt lịch khám ngay
                    </a>
                    <a href="<?= BASE_URL ?>/tra-cuu.php" class="btn btn-lg btn-outline">
                        <i class="fa-solid fa-magnifying-glass"></i> Tra cứu phiếu hẹn
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat-item">
                        <h3>08+</h3>
                        <p>Chuyên khoa mũi nhọn</p>
                    </div>
                    <div class="hero-stat-item">
                        <h3>100%</h3>
                        <p>BHYT thông tuyến</p>
                    </div>
                    <div class="hero-stat-item">
                        <h3>24/7</h3>
                        <p>Hỗ trợ & Cấp cứu</p>
                    </div>
                </div>
            </div>

            <div class="hero-card-booking">
                <div class="hero-card-header">
                    <h3><i class="fa-solid fa-bolt"></i> Đặt lịch khám nhanh 30s</h3>
                    <p style="font-size:13px;color:#64748B;margin-top:4px;">Chủ động chọn bác sĩ và thời gian, không chờ đợi</p>
                </div>
                <form action="<?= BASE_URL ?>/dat-lich.php" method="GET">
                    <div class="form-group">
                        <label class="form-label">Chọn chuyên khoa khám:</label>
                        <select name="specialty_id" class="form-select" required>
                            <option value="">-- Chọn chuyên khoa --</option>
                            <?php foreach ($specialties as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ngày khám dự kiến:</label>
                        <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;padding:13px;">
                        <i class="fa-solid fa-arrow-right"></i> Tiếp tục chọn Bác sĩ & Giờ
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- 4 LÝ DO CHỌN AN NHIÊN -->
<section class="section" style="background:#FFFFFF;border-bottom:1px solid #E2E8F0;">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-user-doctor"></i></div>
                <h4>Đội ngũ Chuyên gia</h4>
                <p>Bác sĩ Chuyên khoa I, Thạc sĩ, Tiến sĩ từng công tác tại các bệnh viện lớn và hợp tác quốc tế VKIM.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-microscope"></i></div>
                <h4>Trang thiết bị Hiện đại</h4>
                <p>Máy siêu âm 5D Voluson E10, nội soi tiêu hóa HD Karl Storz, hệ thống CT Scanner đa lát cắt chuẩn châu Âu.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-droplet"></i></div>
                <h4>Thận nhân tạo Kỹ thuật cao</h4>
                <p>Hệ thống 20 máy lọc máu thế hệ mới Fresenius, công nghệ xử lý nước siêu tinh khiết chuẩn Bộ Y tế.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-id-card"></i></div>
                <h4>BHYT Thông Tuyến</h4>
                <p>Tiếp nhận người bệnh có thẻ BHYT trên toàn quốc, thanh toán minh bạch, giảm tối đa gánh nặng chi phí.</p>
            </div>
        </div>
    </div>
</section>

<!-- DANH MỤC CHUYÊN KHOA -->
<section class="section">
    <div class="container">
        <div class="section-title-wrap">
            <span class="section-tag">Khám chữa bệnh đa khoa</span>
            <h2 class="section-title">Các Chuyên Khoa Mũi Nhọn</h2>
            <p class="section-desc">Phòng khám Đa khoa An Nhiên đáp ứng đầy đủ các dịch vụ y tế kỹ thuật cao phục vụ nhân dân Nam Định và các tỉnh lân cận.</p>
        </div>

        <div class="specialties-grid">
            <?php foreach ($specialties as $sp): ?>
                <div class="specialty-card">
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($sp['image_url']) ?>" alt="<?= htmlspecialchars($sp['name']) ?>" class="specialty-img">
                    <div class="specialty-body">
                        <h4 class="specialty-title">
                            <i class="fa-solid <?= htmlspecialchars($sp['icon'] ?? 'fa-stethoscope') ?>"></i>
                            <?= htmlspecialchars($sp['name']) ?>
                        </h4>
                        <p class="specialty-desc"><?= htmlspecialchars($sp['description']) ?></p>
                        <a href="<?= BASE_URL ?>/dat-lich.php?specialty_id=<?= $sp['id'] ?>" class="btn btn-sm btn-outline-primary" style="margin-top:auto;">
                            <i class="fa-solid fa-calendar-plus"></i> Đặt lịch chuyên khoa
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ĐỘI NGŨ BÁC SĨ -->
<section class="section" style="background:#F1F5F9;">
    <div class="container">
        <div class="section-title-wrap">
            <span class="section-tag">Tận tâm • Y đức • Trách nhiệm</span>
            <h2 class="section-title">Đội Ngũ Bác Sĩ Tiêu Biểu</h2>
            <p class="section-desc">Bác sĩ chuyên khoa đầu ngành sẵn sàng tư vấn và điều trị với phác đồ tối ưu nhất cho người bệnh.</p>
        </div>

        <div class="doctors-grid">
            <?php foreach ($doctors as $doc): ?>
                <div class="doctor-card">
                    <div class="doctor-header">
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($doc['avatar']) ?>" alt="<?= htmlspecialchars($doc['full_name']) ?>">
                        <span class="doctor-badge-room"><?= htmlspecialchars($doc['clinic_room']) ?></span>
                    </div>
                    <div class="doctor-body">
                        <div class="doctor-specialty"><?= htmlspecialchars($doc['specialty_name']) ?></div>
                        <h4 class="doctor-name"><?= htmlspecialchars($doc['full_name']) ?></h4>
                        <div class="doctor-degree"><?= htmlspecialchars($doc['degree']) ?> • <?= $doc['experience_years'] ?> năm kinh nghiệm</div>
                        <p style="font-size:13.5px;color:#64748B;margin-bottom:14px;line-height:1.5;">
                            <?= htmlspecialchars(mb_strimwidth($doc['bio'], 0, 110, '...')) ?>
                        </p>
                        <div class="doctor-meta">
                            <span>Phí khám tư vấn:</span>
                            <span class="doctor-fee"><?= formatMoney($doc['consultation_fee']) ?></span>
                        </div>
                        <div class="doctor-footer">
                            <a href="<?= BASE_URL ?>/dat-lich.php?doctor_id=<?= $doc['id'] ?>" class="btn btn-primary" style="flex:1;">
                                <i class="fa-solid fa-calendar-check"></i> Đặt khám
                            </a>
                            <a href="<?= BASE_URL ?>/bac-si.php#doc-<?= $doc['id'] ?>" class="btn btn-outline" title="Xem hồ sơ">
                                <i class="fa-solid fa-circle-info"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center;margin-top:40px;">
            <a href="<?= BASE_URL ?>/bac-si.php" class="btn btn-lg btn-outline-primary">
                <i class="fa-solid fa-users"></i> Xem tất cả Bác sĩ phòng khám
            </a>
        </div>
    </div>
</section>

<!-- QUY TRÌNH KHÁM BỆNH 4 BƯỚC -->
<section class="section" style="background:#FFFFFF;">
    <div class="container">
        <div class="section-title-wrap">
            <span class="section-tag">Nhanh chóng • Thuận tiện</span>
            <h2 class="section-title">Quy Trình Khám Bệnh Tại An Nhiên</h2>
            <p class="section-desc">Tiết kiệm thời gian, giảm thiểu thủ tục hành chính, tối đa hóa trải nghiệm của người bệnh.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:24px;position:relative;">
            <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:14px;padding:28px 20px;text-align:center;">
                <div style="width:52px;height:52px;background:var(--primary);color:#FFF;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-weight:800;font-size:18px;">1</div>
                <h4 style="font-size:16.5px;margin-bottom:8px;color:#0F172A;">Đặt lịch trực tuyến</h4>
                <p style="font-size:13.5px;color:#64748B;">Chọn chuyên khoa, bác sĩ và khung giờ trên Website.</p>
            </div>
            <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:14px;padding:28px 20px;text-align:center;">
                <div style="width:52px;height:52px;background:var(--secondary);color:#FFF;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-weight:800;font-size:18px;">2</div>
                <h4 style="font-size:16.5px;margin-bottom:8px;color:#0F172A;">Tiếp đón tại sảnh</h4>
                <p style="font-size:13.5px;color:#64748B;">Đọc mã phiếu hẹn tại quầy, nhân viên hướng dẫn vào buồng khám.</p>
            </div>
            <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:14px;padding:28px 20px;text-align:center;">
                <div style="width:52px;height:52px;background:#6366F1;color:#FFF;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-weight:800;font-size:18px;">3</div>
                <h4 style="font-size:16.5px;margin-bottom:8px;color:#0F172A;">Bác sĩ thăm khám</h4>
                <p style="font-size:13.5px;color:#64748B;">Bác sĩ chuyên khoa trực tiếp khám, siêu âm, xét nghiệm khi cần.</p>
            </div>
            <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:14px;padding:28px 20px;text-align:center;">
                <div style="width:52px;height:52px;background:var(--accent-green);color:#FFF;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-weight:800;font-size:18px;">4</div>
                <h4 style="font-size:16.5px;margin-bottom:8px;color:#0F172A;">Đơn thuốc điện tử</h4>
                <p style="font-size:13.5px;color:#64748B;">Nhận đơn thuốc, dặn dò và tra cứu lại mọi lúc trên hệ thống.</p>
            </div>
        </div>
    </div>
</section>

<!-- CẨM NANG Y TẾ -->
<section class="section" style="background:#F8FAFC;">
    <div class="container">
        <div class="section-title-wrap">
            <span class="section-tag">Kiến thức y khoa</span>
            <h2 class="section-title">Cẩm Nang Sức Khỏe An Nhiên</h2>
            <p class="section-desc">Những thông tin y khoa chính thống được biên soạn bởi đội ngũ bác sĩ phòng khám.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:28px;">
            <?php foreach ($recentPosts as $p): ?>
                <div class="card" style="overflow:hidden;">
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" style="height:210px;width:100%;object-fit:cover;">
                    <div class="card-body">
                        <span class="badge badge-info" style="margin-bottom:10px;"><?= htmlspecialchars($p['category']) ?></span>
                        <h4 style="font-size:17.5px;font-weight:700;margin-bottom:10px;line-height:1.4;">
                            <a href="<?= BASE_URL ?>/cam-nang-chi-tiet.php?id=<?= $p['id'] ?>" style="color:#0F172A;">
                                <?= htmlspecialchars($p['title']) ?>
                            </a>
                        </h4>
                        <p style="font-size:14px;color:#64748B;margin-bottom:16px;">
                            <?= htmlspecialchars(mb_strimwidth($p['summary'], 0, 110, '...')) ?>
                        </p>
                        <div style="display:flex;justify-content:space-between;align-items:center;font-size:12.5px;color:#94A3B8;border-top:1px solid #E2E8F0;padding-top:12px;">
                            <span><i class="fa-solid fa-user-pen"></i> <?= htmlspecialchars($p['author_name']) ?></span>
                            <span><i class="fa-regular fa-calendar"></i> <?= formatDate($p['created_at']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CALL TO ACTION BANNER -->
<section style="background:linear-gradient(135deg, #042F2E 0%, #0F766E 50%, #0369A1 100%);color:#FFFFFF;padding:65px 0;">
    <div class="container" style="text-align:center;">
        <h2 style="font-size:34px;font-weight:800;margin-bottom:16px;">Sức Khỏe Của Bạn Là Ưu Tiên Hàng Đầu Của An Nhiên</h2>
        <p style="font-size:16.5px;max-width:720px;margin:0 auto 32px;opacity:0.92;line-height:1.7;">
            Hãy để đội ngũ y bác sĩ giàu tâm đức tại Nam Định đồng hành cùng bạn và gia đình. Đặt lịch khám ngay hôm nay để được phục vụ chu đáo nhất!
        </p>
        <div style="display:flex;justify-content:center;gap:16px;flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>/dat-lich.php" class="btn btn-lg btn-success">
                <i class="fa-solid fa-calendar-check"></i> Đặt lịch hẹn ngay
            </a>
            <a href="tel:0944809221" class="btn btn-lg btn-outline" style="border-color:#FFF;color:#FFF;">
                <i class="fa-solid fa-phone"></i> Gọi Hotline: 0944.809.221
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
