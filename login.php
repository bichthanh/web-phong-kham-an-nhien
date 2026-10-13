<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Xử lý chuyển đổi tài khoản hoặc đăng xuất nhanh nếu có yêu cầu
if (isset($_GET['switch']) || isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    session_start();
    header("Location: login.php");
    exit;
}

$currentUser = getCurrentUser();
$error_msg = null;

// XỬ LÝ ĐĂNG NHẬP (Cả đăng nhập thủ công và đăng nhập 1-click)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Hỗ trợ nút đăng nhập nhanh (Quick Login) cho Demo
    if (isset($_POST['quick_role'])) {
        $quickRole = $_POST['quick_role'];
        if ($quickRole === 'admin') {
            $username = 'admin';
            $password = 'admin123';
        } elseif ($quickRole === 'doctor') {
            $username = 'bs_minhduc';
            $password = '123456';
        } elseif ($quickRole === 'patient') {
            $username = 'benhnhan1';
            $password = '123456';
        }
    }

    if (!$username || !$password) {
        $error_msg = "Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ? OR phone = ?) AND status = 1");
        $stmt->execute([$username, $username, $username]);
        $userRow = $stmt->fetch();

        if ($userRow && password_verify($password, $userRow['password'])) {
            // Xóa session cũ và tạo session mới sạch sẽ
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
            $error_msg = "Tên đăng nhập hoặc mật khẩu không chính xác! Vui lòng kiểm tra lại.";
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
        <p style="color:#94A3B8;font-size:15px;">Cổng làm việc dành cho Bác sĩ, Quản trị viên và Bệnh nhân</p>
    </div>
</div>

<section class="section">
    <div class="container" style="max-width:540px;">

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
                <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                <div><?= htmlspecialchars($flash['message']) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="alert alert-danger" style="display:flex;align-items:center;gap:12px;">
                <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                <div><?= htmlspecialchars($error_msg) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($currentUser): ?>
            <!-- NẾU ĐÃ CÓ TÀI KHOẢN ĐANG ĐĂNG NHẬP -->
            <div class="card" style="padding:25px;margin-bottom:20px;border-left:4px solid var(--primary);background:#F0FDFA;">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:48px;height:48px;border-radius:50%;background:var(--primary);color:#FFF;display:flex;align-items:center;justify-content:center;font-size:20px;">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div>
                        <span style="font-size:13px;color:#0F766E;font-weight:700;">BẠN ĐANG ĐĂNG NHẬP VỚI TƯ CÁCH:</span>
                        <h4 style="margin:2px 0;font-size:16px;color:#0F172A;">
                            <?= htmlspecialchars($currentUser['full_name']) ?> (<?= htmlspecialchars($currentUser['username']) ?>)
                        </h4>
                        <span style="font-size:12.5px;color:#64748B;">
                            Vai trò: <strong><?= $currentUser['role'] === 'ROLE_ADMIN' ? 'Quản trị viên (Admin)' : ($currentUser['role'] === 'ROLE_DOCTOR' ? 'Bác sĩ chuyên khoa' : 'Bệnh nhân') ?></strong>
                        </span>
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-top:16px;">
                    <?php if (isAdmin()): ?>
                        <a href="<?= BASE_URL ?>/admin/index.php" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-gauge"></i> Vào Trang Quản Trị
                        </a>
                    <?php elseif (isDoctor()): ?>
                        <a href="<?= BASE_URL ?>/bac-si/index.php" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-stethoscope"></i> Vào Bàn Khám Bác Sĩ
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/benh-nhan/index.php" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-folder-open"></i> Vào Hồ Sơ Bệnh Nhân
                        </a>
                    <?php endif; ?>
                    <a href="login.php?switch=1" class="btn btn-sm btn-outline-danger">
                        <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất để đổi tài khoản khác
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- KHUNG ĐĂNG NHẬP CHÍNH -->
        <div class="card" style="padding:32px;box-shadow:var(--shadow-lg);">
            <div style="text-align:center;margin-bottom:24px;">
                <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Phòng Khám An Nhiên" style="height:60px;width:60px;border-radius:50%;object-fit:cover;box-shadow:0 4px 12px rgba(13,148,136,0.25);margin:0 auto 10px;display:block;">
                <h3 style="font-size:22px;color:#0F172A;font-weight:700;">Đăng Nhập Tài Khoản</h3>
                <p style="font-size:13.5px;color:#64748B;margin-top:4px;">Nhập thông tin bên dưới hoặc bấm nút Đăng nhập nhanh để thử nghiệm</p>
            </div>

            <!-- 1-CLICK DEMO LOGIN (CỰC KỲ TIỆN CHO BÁO CÁO ĐỒ ÁN) -->
            <div style="margin-bottom:24px;padding:16px;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:10px;">
                <label style="font-size:12.5px;color:#475569;font-weight:700;display:block;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;">
                    <i class="fa-solid fa-bolt" style="color:#EAB308;"></i> 1-Click Đăng nhập nhanh (Dành cho Demo):
                </label>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
                    <form method="POST" action="login.php" style="margin:0;">
                        <input type="hidden" name="quick_role" value="doctor">
                        <button type="submit" class="btn btn-sm" style="width:100%;background:#0D9488;color:#FFF;padding:8px 4px;font-size:12px;font-weight:600;border:none;">
                            <i class="fa-solid fa-user-doctor"></i> Bác Sĩ
                        </button>
                    </form>
                    <form method="POST" action="login.php" style="margin:0;">
                        <input type="hidden" name="quick_role" value="admin">
                        <button type="submit" class="btn btn-sm" style="width:100%;background:#0F172A;color:#FFF;padding:8px 4px;font-size:12px;font-weight:600;border:none;">
                            <i class="fa-solid fa-shield-halved"></i> Admin
                        </button>
                    </form>
                    <form method="POST" action="login.php" style="margin:0;">
                        <input type="hidden" name="quick_role" value="patient">
                        <button type="submit" class="btn btn-sm" style="width:100%;background:#0284C7;color:#FFF;padding:8px 4px;font-size:12px;font-weight:600;border:none;">
                            <i class="fa-solid fa-user"></i> Bệnh Nhân
                        </button>
                    </form>
                </div>
            </div>

            <div style="position:relative;text-align:center;margin:15px 0;">
                <hr style="border:none;border-top:1px solid #E2E8F0;margin:0;">
                <span style="position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:#FFF;padding:0 12px;font-size:12px;color:#94A3B8;font-weight:600;">HOẶC ĐĂNG NHẬP TAY</span>
            </div>

            <form action="login.php" method="POST" id="main_login_form">
                <div class="form-group">
                    <label class="form-label">Tên đăng nhập / Email / SĐT <span class="required">*</span></label>
                    <div style="position:relative;">
                        <input type="text" name="username" id="login_username" class="form-control" required placeholder="Nhập username, email hoặc SĐT..." style="padding-left:38px;" autofocus>
                        <i class="fa-solid fa-user" style="position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#94A3B8;"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" style="display:flex;justify-content:space-between;align-items:center;">
                        <span>Mật khẩu <span class="required">*</span></span>
                    </label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="login_password" class="form-control" required placeholder="Nhập mật khẩu của bạn..." style="padding-left:38px;padding-right:40px;">
                        <i class="fa-solid fa-lock" style="position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#94A3B8;"></i>
                        <button type="button" id="toggle_password" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94A3B8;cursor:pointer;padding:4px;" title="Hiện/ẩn mật khẩu">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="btn_submit_login" class="btn btn-primary" style="width:100%;padding:12px;font-size:15px;font-weight:700;margin-top:12px;">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập
                </button>
            </form>

            <div style="text-align:center;margin-top:20px;font-size:14px;color:#64748B;">
                Chưa có tài khoản bệnh nhân? <a href="<?= BASE_URL ?>/register.php" style="font-weight:700;color:var(--primary);">Đăng ký ngay tại đây</a>
            </div>

            <!-- GỢI Ý CHI TIẾT TÀI KHOẢN MẪU -->
            <div style="margin-top:24px;padding:14px;background:#F1F5F9;border-radius:8px;font-size:12px;color:#475569;">
                <div style="font-weight:700;color:#0F172A;margin-bottom:6px;">
                    <i class="fa-solid fa-circle-info"></i> Danh sách tài khoản thử nghiệm:
                </div>
                <div style="display:flex;flex-direction:column;gap:3px;">
                    <div>• <strong>Bác sĩ CKI. Nguyễn Minh Đức:</strong> <code>bs_minhduc</code> / <code>123456</code></div>
                    <div>• <strong>Bác sĩ ThS. Trần Thanh Hằng:</strong> <code>bs_thanhhang</code> / <code>123456</code></div>
                    <div>• <strong>Quản trị viên:</strong> <code>admin</code> / <code>admin123</code></div>
                    <div>• <strong>Bệnh nhân mẫu:</strong> <code>benhnhan1</code> / <code>123456</code></div>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Nút ẩn / hiện mật khẩu
    const toggleBtn = document.getElementById('toggle_password');
    const pwdInput = document.getElementById('login_password');
    if (toggleBtn && pwdInput) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = this.querySelector('i');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwdInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }

    // Hiệu ứng loading khi bấm đăng nhập để người dùng an tâm
    const loginForm = document.getElementById('main_login_form');
    const submitBtn = document.getElementById('btn_submit_login');
    if (loginForm && submitBtn) {
        loginForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang đăng nhập...';
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
