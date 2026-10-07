<?php
$page_title = "Thông tin cá nhân & Chuyên môn";
$activeNav = 'thong_tin';
require_once __DIR__ . '/../includes/doctor_header.php';

$success_msg = null;
$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $bio = sanitizeInput($_POST['bio'] ?? '');
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $rePassword = $_POST['re_password'] ?? '';

    try {
        $pdo->beginTransaction();

        // Cập nhật SĐT trong users
        $stmtU = $pdo->prepare("UPDATE users SET phone = ? WHERE id = ?");
        $stmtU->execute([$phone, $docUser['id']]);

        // Cập nhật bio trong doctors
        $stmtD = $pdo->prepare("UPDATE doctors SET bio = ? WHERE id = ?");
        $stmtD->execute([$bio, $currentDoctor['id']]);

        // Nếu có đổi mật khẩu
        if (!empty($newPassword)) {
            if ($newPassword !== $rePassword) {
                throw new Exception("Mật khẩu mới không trùng khớp!");
            }
            if (strlen($newPassword) < 6) {
                throw new Exception("Mật khẩu mới phải có tối thiểu 6 ký tự!");
            }

            // Kiểm tra mật khẩu cũ
            $stmtP = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmtP->execute([$docUser['id']]);
            $currentHash = $stmtP->fetchColumn();

            if (!password_verify($oldPassword, $currentHash)) {
                throw new Exception("Mật khẩu hiện tại không chính xác!");
            }

            $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmtPUpd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmtPUpd->execute([$newHash, $docUser['id']]);
        }

        $pdo->commit();
        $success_msg = "Cập nhật thông tin chuyên môn thành công!";
        // Tải lại thông tin mới
        $stmtDocInfo->execute([$docUser['id']]);
        $currentDoctor = $stmtDocInfo->fetch();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error_msg = $e->getMessage();
    }
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-id-card" style="color:var(--primary);"></i> Hồ Sơ Chuyên Môn & Tài Khoản Bác Sĩ</h2>
        <p>Quản lý thông tin giới thiệu, liên hệ và bảo mật mật khẩu tài khoản</p>
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

<div class="card" style="max-width:750px;">
    <div class="card-body" style="padding:30px;">
        <form method="POST" action="thong-tin-ca-nhan.php">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div class="form-group">
                    <label class="form-label">Họ và tên bác sĩ</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($currentDoctor['full_name']) ?>" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Học vị & Chức danh</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($currentDoctor['degree']) ?>" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Chuyên khoa phụ trách</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($currentDoctor['specialty_name']) ?>" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Phòng khám công tác</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($currentDoctor['clinic_room']) ?>" disabled>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Số điện thoại liên hệ <span class="required">*</span></label>
                <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($docUser['phone']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Tiểu sử tóm tắt & Kinh nghiệm công tác</label>
                <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($currentDoctor['bio']) ?></textarea>
                <small class="text-muted">Thông tin này sẽ được hiển thị công khai trên Website để người bệnh tìm hiểu trước khi đặt lịch.</small>
            </div>

            <div style="margin-top:28px;padding-top:20px;border-top:1px dashed #CBD5E1;">
                <h4 style="font-size:16px;color:#0F172A;margin-bottom:14px;"><i class="fa-solid fa-lock"></i> Đổi Mật Khẩu (Bỏ trống nếu không đổi)</h4>
                
                <div class="form-group">
                    <label class="form-label">Mật khẩu hiện tại</label>
                    <input type="password" name="old_password" class="form-control" placeholder="Nhập mật khẩu hiện tại nếu muốn đổi...">
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

            <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu Thay Đổi
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/doctor_footer.php'; ?>
