<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitizeInput($_POST['full_name'] ?? '');
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $re_password = $_POST['re_password'] ?? '';

    if (!$full_name || !$username || !$email || !$phone || !$password) {
        $error_msg = "Vui lòng nhập đầy đủ tất cả các trường thông tin!";
    } elseif ($password !== $re_password) {
        $error_msg = "Mật khẩu xác nhận không trùng khớp!";
    } elseif (strlen($password) < 6) {
        $error_msg = "Mật khẩu phải chứa ít nhất 6 ký tự!";
    } else {
        // Kiểm tra trùng username hoặc email
        $stmtChk = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmtChk->execute([$username, $email]);
        if ($stmtChk->fetchColumn() > 0) {
            $error_msg = "Tên đăng nhập hoặc địa chỉ email đã được sử dụng!";
        } else {
            $passHash = password_hash($password, PASSWORD_BCRYPT);
            $stmtIns = $pdo->prepare("INSERT INTO users (username, password, full_name, email, phone, role) VALUES (?, ?, ?, ?, ?, 'ROLE_PATIENT')");
            $stmtIns->execute([$username, $passHash, $full_name, $email, $phone]);

            // Đăng nhập luôn cho bệnh nhân
            $userId = $pdo->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;
            $_SESSION['role'] = 'ROLE_PATIENT';

            setFlashMessage('success', "Đăng ký tài khoản thành công! Chào mừng bạn đến với Phòng khám An Nhiên.");
            header("Location: " . BASE_URL . "/benh-nhan/index.php");
            exit;
        }
    }
}

$page_title = "Đăng ký tài khoản bệnh nhân";
require_once __DIR__ . '/includes/header.php';
?>

<div style="background:linear-gradient(135deg, #042F2E 0%, #0F172A 100%);color:#FFF;padding:40px 0;">
    <div class="container" style="text-align:center;">
        <h1 style="font-size:28px;font-weight:800;margin-bottom:6px;">Tạo Tài Khoản Bệnh Nhân Mới</h1>
        <p style="color:#94A3B8;font-size:15px;">Đăng ký để quản lý lịch khám, theo dõi đơn thuốc và hồ sơ sức khỏe trực tuyến</p>
    </div>
</div>

<section class="section">
    <div class="container" style="max-width:560px;">

        <?php if ($error_msg): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                <div><?= htmlspecialchars($error_msg) ?></div>
            </div>
        <?php endif; ?>

        <div class="card" style="padding:35px;box-shadow:var(--shadow-lg);">
            <form action="<?= BASE_URL ?>/register.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Họ và tên của bạn <span class="required">*</span></label>
                    <input type="text" name="full_name" class="form-control" required placeholder="Ví dụ: Nguyễn Văn An" autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Tên đăng nhập <span class="required">*</span></label>
                    <input type="text" name="username" class="form-control" required placeholder="viết liền không dấu, ví dụ: vanan2026">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Số điện thoại <span class="required">*</span></label>
                        <input type="tel" name="phone" class="form-control" required placeholder="0912345678">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Địa chỉ Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="an@gmail.com">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Mật khẩu <span class="required">*</span></label>
                        <input type="password" name="password" class="form-control" required placeholder="Tối thiểu 6 ký tự">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Xác nhận mật khẩu <span class="required">*</span></label>
                        <input type="password" name="re_password" class="form-control" required placeholder="Nhập lại mật khẩu">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;padding:13px;margin-top:10px;">
                    <i class="fa-solid fa-user-plus"></i> Đăng Ký Tài Khoản
                </button>
            </form>

            <div style="text-align:center;margin-top:20px;font-size:14px;color:#64748B;">
                Đã có tài khoản? <a href="<?= BASE_URL ?>/login.php" style="font-weight:700;">Đăng nhập ngay</a>
            </div>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
