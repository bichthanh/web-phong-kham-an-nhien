<?php
$page_title = "Liên hệ & Đội ngũ Nhóm 12";
require_once __DIR__ . '/includes/header.php';

$feedback_success = false;
$feedback_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');

    if (!$name || !$email || !$phone || !$message) {
        $feedback_error = "Vui lòng điền đầy đủ các thông tin liên hệ bắt buộc!";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO feedback (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            $feedback_success = true;
        } catch (Exception $e) {
            $feedback_error = "Lỗi khi gửi phản hồi: " . $e->getMessage();
        }
    }
}
?>

<div style="background:linear-gradient(135deg, #042F2E 0%, #0F172A 100%);color:#FFF;padding:50px 0;">
    <div class="container">
        <h1 style="font-size:32px;font-weight:800;margin-bottom:8px;">Liên Hệ Phòng Khám & Thông Tin Đồ Án</h1>
        <p style="color:#94A3B8;font-size:16px;">Phòng khám Đa khoa An Nhiên Nam Định • Sản phẩm đồ án Nhóm 12 (74DCTT26 - UTT)</p>
    </div>
</div>

<section class="section">
    <div class="container">

        <!-- KHỐI GIỚI THIỆU ĐỘI NGŨ NHÓM 12 UTT ĐƯỢC THUÊ CODE -->
        <div class="card" style="border:2px solid var(--primary);background:linear-gradient(135deg, #F0FDFA 0%, #FFFFFF 100%);margin-bottom:45px;padding:30px;">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;border-bottom:1px solid #CCFBF1;padding-bottom:16px;">
                <div style="width:56px;height:56px;background:var(--primary);color:#FFF;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:26px;">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <span class="badge badge-primary" style="margin-bottom:4px;">BÁO CÁO KẾT QUẢ THỰC HIỆN ĐỒ ÁN PHÁT TRIỂN PHẦN MỀM</span>
                    <h2 style="font-size:22px;color:#0F172A;font-weight:800;">Nhóm 12 • Lớp 74DCTT26 • Khoa Công Nghệ Thông Tin</h2>
                    <p style="color:#0F766E;font-size:14px;font-weight:600;">Trường Đại Học Công Nghệ Giao Thông Vận Tải (UTT)</p>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:30px;align-items:center;">
                <div>
                    <h4 style="font-size:16px;color:#0F172A;margin-bottom:12px;"><i class="fa-solid fa-users-gear" style="color:var(--primary);"></i> Danh sách 5 thành viên nhóm lập trình:</h4>
                    <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:10px;">
                        <div style="background:#FFF;padding:10px 14px;border-radius:8px;border:1px solid #E2E8F0;display:flex;align-items:center;gap:10px;">
                            <span style="font-weight:800;color:var(--primary);font-size:16px;">32</span>
                            <div>
                                <strong style="color:#0F172A;font-size:14px;">Nguyễn Ngọc Mỹ</strong>
                                <span style="display:block;font-size:11.5px;color:#64748B;">Lập trình viên Fullstack</span>
                            </div>
                        </div>

                        <div style="background:#FFF;padding:10px 14px;border-radius:8px;border:1px solid #E2E8F0;display:flex;align-items:center;gap:10px;">
                            <span style="font-weight:800;color:var(--primary);font-size:16px;">07</span>
                            <div>
                                <strong style="color:#0F172A;font-size:14px;">Nguyễn Hồng Đăng</strong>
                                <span style="display:block;font-size:11.5px;color:#64748B;">Thiết kế & Phân tích HT</span>
                            </div>
                        </div>

                        <div style="background:#FFF;padding:10px 14px;border-radius:8px;border:1px solid #E2E8F0;display:flex;align-items:center;gap:10px;">
                            <span style="font-weight:800;color:var(--primary);font-size:16px;">49</span>
                            <div>
                                <strong style="color:#0F172A;font-size:14px;">Vũ Thị Minh Thư</strong>
                                <span style="display:block;font-size:11.5px;color:#64748B;">Giao diện & Trải nghiệm UI</span>
                            </div>
                        </div>

                        <div style="background:#FFF;padding:10px 14px;border-radius:8px;border:1px solid #E2E8F0;display:flex;align-items:center;gap:10px;">
                            <span style="font-weight:800;color:var(--primary);font-size:16px;">57</span>
                            <div>
                                <strong style="color:#0F172A;font-size:14px;">Trần Thị Hồng Xoan</strong>
                                <span style="display:block;font-size:11.5px;color:#64748B;">Kiểm thử & Cơ sở dữ liệu</span>
                            </div>
                        </div>

                        <div style="background:#FFF;padding:10px 14px;border-radius:8px;border:1px solid #E2E8F0;display:flex;align-items:center;gap:10px;grid-column:1/-1;">
                            <span style="font-weight:800;color:var(--primary);font-size:16px;">46</span>
                            <div>
                                <strong style="color:#0F172A;font-size:14px;">Lưu Thị Bích Thanh</strong>
                                <span style="display:block;font-size:11.5px;color:#64748B;">Báo cáo & Kiểm thử chức năng</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="background:#FFF;padding:20px;border-radius:12px;border:1px solid #E2E8F0;">
                    <div style="margin-bottom:12px;">
                        <span style="font-size:12px;color:#64748B;text-transform:uppercase;font-weight:700;">Giảng viên hướng dẫn</span>
                        <h4 style="font-size:16px;color:#0F766E;margin-top:2px;">Cô Phạm Thị Thuận</h4>
                    </div>
                    <div style="margin-bottom:12px;">
                        <span style="font-size:12px;color:#64748B;text-transform:uppercase;font-weight:700;">Đề tài đồ án</span>
                        <p style="font-size:13.5px;color:#0F172A;font-weight:600;margin-top:2px;">
                            Xây dựng hệ thống web đăng ký và quản lý lịch khám bệnh Phòng khám Đa khoa An Nhiên
                        </p>
                    </div>
                    <div>
                        <span style="font-size:12px;color:#64748B;text-transform:uppercase;font-weight:700;">Công nghệ phát triển</span>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:6px;">
                            <span class="badge badge-secondary">HTML5</span>
                            <span class="badge badge-secondary">CSS3</span>
                            <span class="badge badge-secondary">JavaScript</span>
                            <span class="badge badge-secondary">PHP 8.2</span>
                            <span class="badge badge-secondary">MySQL</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;">
            <!-- CỘT 1: THÔNG TIN PHÒNG KHÁM AN NHIÊN NAM ĐỊNH -->
            <div>
                <h3 style="font-size:22px;color:#0F172A;font-weight:800;margin-bottom:20px;">
                    Thông Tin Phòng Khám An Nhiên
                </h3>
                <p style="color:#64748B;line-height:1.7;margin-bottom:24px;">
                    Phòng khám Đa khoa An Nhiên trân trọng cảm ơn sự tin tưởng và ủng hộ của quý khách hàng tại Nam Định. Chúng tôi luôn sẵn sàng lắng nghe mọi ý kiến đóng góp nhằm nâng cao chất lượng khám chữa bệnh.
                </p>

                <div style="display:flex;flex-direction:column;gap:18px;">
                    <div style="display:flex;gap:16px;align-items:flex-start;">
                        <div style="width:44px;height:44px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <strong style="color:#0F172A;display:block;margin-bottom:2px;">Địa chỉ phòng khám:</strong>
                            <p style="font-size:14px;color:#64748B;">
                                <strong>Cơ sở 1:</strong> Khu Bồi Tây, Xã Mỹ Phúc, Huyện Mỹ Lộc, Tỉnh Nam Định<br>
                                <strong>Cơ sở 2:</strong> Đường Trần Tự Khánh, TP. Nam Định
                            </p>
                        </div>
                    </div>

                    <div style="display:flex;gap:16px;align-items:flex-start;">
                        <div style="width:44px;height:44px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <strong style="color:#0F172A;display:block;margin-bottom:2px;">Đường dây nóng:</strong>
                            <p style="font-size:14px;color:#64748B;">
                                Tổng đài tư vấn: <strong style="color:#0284C7;">0944.809.221</strong><br>
                                Khoa Thận nhân tạo & Cấp cứu: <strong style="color:#DC2626;">0983.301.202</strong>
                            </p>
                        </div>
                    </div>

                    <div style="display:flex;gap:16px;align-items:flex-start;">
                        <div style="width:44px;height:44px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <div>
                            <strong style="color:#0F172A;display:block;margin-bottom:2px;">Giờ mở cửa:</strong>
                            <p style="font-size:14px;color:#64748B;">
                                Sáng: 07:30 - 11:30 | Chiều: 13:30 - 17:00 | Tối: 17:30 - 20:00<br>
                                Khám bệnh tất cả các ngày trong tuần (kể cả Thứ 7 & Chủ Nhật)
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CỘT 2: FORM LIÊN HỆ PHẢN HỒI -->
            <div>
                <div class="card" style="padding:30px;">
                    <h3 style="font-size:20px;color:#0F172A;font-weight:700;margin-bottom:14px;">Gửi Tin Nhắn / Phản Hồi</h3>
                    <p style="font-size:13.5px;color:#64748B;margin-bottom:20px;">
                        Điền thông tin vào biểu mẫu dưới đây, nhân viên chăm sóc khách hàng của An Nhiên sẽ liên hệ hỗ trợ bạn sớm nhất.
                    </p>

                    <?php if ($feedback_success): ?>
                        <div class="alert alert-success">
                            <i class="fa-solid fa-circle-check fa-lg"></i>
                            <div>Cảm ơn bạn! Phản hồi của bạn đã được gửi thành công đến ban quản trị phòng khám An Nhiên.</div>
                        </div>
                    <?php endif; ?>

                    <?php if ($feedback_error): ?>
                        <div class="alert alert-danger">
                            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                            <div><?= htmlspecialchars($feedback_error) ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/lien-he.php" method="POST">
                        <div class="form-group">
                            <label class="form-label">Họ và tên của bạn <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="Ví dụ: Hoàng Văn Nam">
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div class="form-group">
                                <label class="form-label">Số điện thoại <span class="required">*</span></label>
                                <input type="tel" name="phone" class="form-control" required placeholder="0912345678">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email liên hệ <span class="required">*</span></label>
                                <input type="email" name="email" class="form-control" required placeholder="email@gmail.com">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Chủ đề cần tư vấn</label>
                            <input type="text" name="subject" class="form-control" placeholder="Ví dụ: Tư vấn gói khám BHYT, hỏi về lịch bác sĩ...">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nội dung tin nhắn <span class="required">*</span></label>
                            <textarea name="message" class="form-control" rows="4" required placeholder="Nhập câu hỏi hoặc phản hồi của bạn..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;">
                            <i class="fa-solid fa-paper-plane"></i> Gửi Tin Nhắn Cho Phòng Khám
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
