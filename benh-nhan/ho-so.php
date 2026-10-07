<?php
$page_title = "Thông tin cá nhân";
$activeNav = 'ho_so';
require_once __DIR__ . '/../includes/patient_header.php';

$success_msg = null;
$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitizeInput($_POST['full_name'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $oldPass = $_POST['old_password'] ?? '';
    $newPass = $_POST['new_password'] ?? '';
    $rePass = $_POST['re_password'] ?? '';

    if (!$fullName || !$phone || !$email) {
        $error_msg = "Vui lòng nhập đầy đủ họ tên, số điện thoại và email!";
    } else {
        try {
            $pdo->beginTransaction();

            $stmtU = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, email = ? WHERE id = ?");
            $stmtU->execute([$fullName, $phone, $email, $patUser['id']]);

            if (!empty($newPass)) {
                if ($newPass !== $rePass) {
                    throw new Exception("Mật khẩu mới không trùng khớp!");
                }
                if (strlen($newPass) < 6) {
                    throw new Exception("Mật khẩu mới tối thiểu 6 ký tự!");
                }

                $stmtP = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                $stmtP->execute([$patUser['id']]);
                if (!password_verify($oldPass, $stmtP->fetchColumn())) {
                    throw new Exception("Mật khẩu hiện tại không đúng!");
                }

                $newHash = password_hash($newPass, PASSWORD_BCRYPT);
                $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$newHash, $patUser['id']]);
            }

            $pdo->commit();
            $_SESSION['full_name'] = $fullName;
            $_SESSION['phone'] = $phone;
            $_SESSION['email'] = $email;
            $success_msg = "Cập nhật hồ sơ thông tin cá nhân thành công!";
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error_msg = $e->getMessage();
        }
    }
}

$stmtMe = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmtMe->execute([$patUser['id']]);
$me = $stmtMe->fetch();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-user-gear" style="color:var(--primary);"></i> Quản Lý Thông Tin Cá Nhân</h2>
        <p>Cập nhật số điện thoại liên hệ, email nhận kết quả và đổi mật khẩu</p>
    </div>
</div>

<?php if ($success_msg): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check fa-lg"></i>
        <div><?= htmlspecialchars($success_msg) ?></div>
    </div>
<?php endif; ?>

<?php if ($error_msg): ?>
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div><?= htmlspecialchars($error_msg) ?></div>
    </div>
<?php endif; ?>

<div class="card" style="max-width:650px;">
    <div class="card-body" style="padding:30px;">
        <form method="POST" action="ho-so.php">
            <div class="form-group">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($me['username']) ?>" disabled>
            </div>

            <div class="form-group">
                <label class="form-label">Họ và tên của bạn <span class="required">*</span></label>
                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($me['full_name']) ?>" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div class="form-group">
                    <label class="form-label">Số điện thoại <span class="required">*</span></label>
                    <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($me['phone']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email liên hệ <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($me['email']) ?>" required>
                </div>
            </div>

            <div style="margin-top:25px;padding-top:20px;border-top:1px dashed #CBD5E1;">
                <h4 style="font-size:15px;color:#0F172A;margin-bottom:12px;"><i class="fa-solid fa-lock"></i> Đổi Mật Khẩu (Để trống nếu không đổi)</h4>

                <div class="form-group">
                    <label class="form-label">Mật khẩu hiện tại</label>
                    <input type="password" name="old_password" class="form-control" placeholder="Mật khẩu hiện tại...">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div class="form-group">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Tối thiểu 6 ký tự">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" name="re_password" class="form-control" placeholder="Nhập lại mật khẩu mới">
                    </div>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:20px;">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu Hồ Sơ
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/patient_footer.php'; ?>
