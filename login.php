<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Nếu đã đăng nhập thì chuyển hướng theo vai trò
if (isLoggedIn()) {
    if (isAdmin()) header("Location: " . BASE_URL . "/admin/index.php");
    elseif (isDoctor()) header("Location: " . BASE_URL . "/bac-si/index.php");
    else header("Location: " . BASE_URL . "/benh-nhan/index.php");
    exit;
}

$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error_msg = "Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ? OR phone = ?) AND status = 1");
        $stmt->execute([$username, $username, $username]);
        $userRow = $stmt->fetch();

        if ($userRow && password_verify($password, $userRow['password'])) {
            // Đăng nhập thành công, thiết lập session
            $_SESSION['user_id'] = $userRow['id'];
            $_SESSION['username'] = $userRow['username'];
            $_SESSION['full_name'] = $userRow['full_name'];
            $_SESSION['email'] = $userRow['email'];
            $_SESSION['phone'] = $userRow['phone'];
            $_SESSION['role'] = $userRow['role'];

            // Nếu là bác sĩ, lưu thêm doctor_id
            if ($userRow['role'] === 'ROLE_DOCTOR') {
                $stmtDoc = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
                $stmtDoc->execute([$userRow['id']]);
                $_SESSION['doctor_id'] = $stmtDoc->fetchColumn();
            }

            // Chuyển hướng theo vai trò
            if ($userRow['role'] === 'ROLE_ADMIN') {
                header("Location: " . BASE_URL . "/admin/index.php");
            } elseif ($userRow['role'] === 'ROLE_DOCTOR') {
                header("Location: " . BASE_URL . "/bac-si/index.php");
            } else {
                header("Location: " . BASE_URL . "/benh-nhan/index.php");
            }
            exit;
        } else {
            $error_msg = "Tên đăng nhập hoặc mật khẩu không chính xác!";
        }
    }
}

$page_title = "Đăng nhập hệ thống";
require_once __DIR__ . '/includes/header.php';
$flash = getFlashMessage();
?>

<div style="background:linear-gradient(135deg, #042F2E 0%, #0F172A 100%);color:#FFF;padding:40px 0;">
    <div class="container" style="text-align:center;">
        <h1 style="font-size:28px;font-weight:800;margin-bottom:6px;">Đăng Nhập Hệ Thống An Nhiên</h1>
        <p style="color:#94A3B8;font-size:15px;">Dành cho Quản trị viên, Bác sĩ chuyên khoa và Bệnh nhân</p>
    </div>
</div>

<section class="section">
    <div class="container" style="max-width:520px;">

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
                <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                <div><?= htmlspecialchars($flash['message']) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                <div><?= htmlspecialchars($error_msg) ?></div>
            </div>
        <?php endif; ?>

        <div class="card" style="padding:35px;box-shadow:var(--shadow-lg);">
            <div style="text-align:center;margin-bottom:24px;">
                <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Phòng Khám An Nhiên" style="height:64px;width:64px;border-radius:50%;object-fit:cover;box-shadow:0 4px 12px rgba(13,148,136,0.25);margin:0 auto 12px;display:block;">
                <h3 style="font-size:20px;color:#0F172A;font-weight:700;">Đăng nhập tài khoản</h3>
            </div>

            <form action="<?= BASE_URL ?>/login.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Tên đăng nhập / Email / Số điện thoại <span class="required">*</span></label>
                    <input type="text" name="username" class="form-control" required placeholder="Nhập username, email hoặc SĐT..." autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Mật khẩu <span class="required">*</span></label>
                    <input type="password" name="password" class="form-control" required placeholder="Nhập mật khẩu của bạn...">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;margin-top:10px;">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập
                </button>
            </form>

            <div style="text-align:center;margin-top:20px;font-size:14px;color:#64748B;">
                Chưa có tài khoản bệnh nhân? <a href="<?= BASE_URL ?>/register.php" style="font-weight:700;">Đăng ký ngay tại đây</a>
            </div>

            <!-- GỢI Ý TÀI KHOẢN KIỂM THỬ ĐỒ ÁN -->
            <div style="margin-top:25px;padding:16px;background:#F8FAFC;border:1px dashed #CBD5E1;border-radius:8px;font-size:12.5px;">
                <strong style="color:var(--primary-dark);display:block;margin-bottom:8px;">
                    <i class="fa-solid fa-key"></i> Tài khoản kiểm thử nhanh (Demo):
                </strong>
                <div style="display:flex;flex-direction:column;gap:4px;color:#475569;">
                    <div>• <strong>Admin:</strong> <code>admin</code> | MK: <code>admin123</code></div>
                    <div>• <strong>Bác sĩ:</strong> <code>bs_minhduc</code> | MK: <code>123456</code></div>
                    <div>• <strong>Bệnh nhân:</strong> <code>benhnhan1</code> | MK: <code>123456</code></div>
                </div>
            </div>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
