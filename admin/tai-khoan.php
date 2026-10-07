<?php
$page_title = "Quản lý Tài khoản người dùng";
$activeNav = 'tai_khoan';
require_once __DIR__ . '/../includes/admin_header.php';

// XỬ LÝ CÁC TÁC VỤ TÀI KHOẢN (UC-AD-03)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_user') {
        $username = sanitizeInput($_POST['username'] ?? '');
        $fullName = sanitizeInput($_POST['full_name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $role = sanitizeInput($_POST['role'] ?? 'ROLE_PATIENT');
        $password = $_POST['password'] ?? '123456';

        $stmtChk = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmtChk->execute([$username, $email]);
        if ($stmtChk->fetchColumn() > 0) {
            setFlashMessage('error', 'Tên đăng nhập hoặc email đã tồn tại trong hệ thống!');
        } else {
            $passHash = password_hash($password, PASSWORD_BCRYPT);
            $stmtIns = $pdo->prepare("INSERT INTO users (username, password, full_name, email, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtIns->execute([$username, $passHash, $fullName, $email, $phone, $role]);
            setFlashMessage('success', "Tạo tài khoản {$username} thành công!");
        }
    } elseif ($action === 'toggle_status') {
        $uId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $newStatus = (int)($_POST['status'] ?? 1);
        if ($uId && $uId != $adminUser['id']) {
            $pdo->prepare("UPDATE users SET status = ? WHERE id = ?")->execute([$newStatus, $uId]);
            setFlashMessage('success', "Cập nhật trạng thái tài khoản thành công!");
        }
    } elseif ($action === 'reset_password') {
        $uId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $newPass = $_POST['new_password'] ?? '123456';
        if ($uId) {
            $passHash = password_hash($newPass, PASSWORD_BCRYPT);
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$passHash, $uId]);
            setFlashMessage('success', "Đã đặt lại mật khẩu cho tài khoản!");
        }
    } elseif ($action === 'delete_user') {
        // UC-AD-03 Ngoại lệ 5a: Không được phép xóa tài khoản Quản trị viên đang trực tiếp đăng nhập
        $uId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        if ($uId == $adminUser['id']) {
            setFlashMessage('error', 'Không được phép xóa tài khoản Quản trị viên đang đăng nhập (Ngoại lệ UC-AD-03)!');
        } elseif ($uId) {
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uId]);
            setFlashMessage('success', "Đã xóa tài khoản khỏi hệ thống!");
        }
    }
    header("Location: tai-khoan.php");
    exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$flash = getFlashMessage();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-users-gear" style="color:var(--primary);"></i> Quản Lý Tài Khoản Thành Viên</h2>
        <p>Cấp tài khoản, phân quyền quản trị, bác sĩ, bệnh nhân và kiểm soát trạng thái hoạt động</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openUserCreateModal()">
        <i class="fa-solid fa-user-plus"></i> Cấp Tài Khoản Mới
    </button>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <div><?= htmlspecialchars($flash['message']) ?></div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-address-book" style="color:var(--primary);margin-right:8px;"></i> Danh Sách Tài Khoản (<?= count($users) ?> tài khoản)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px;">ID</th>
                        <th>Tên Đăng Nhập</th>
                        <th>Họ Và Tên</th>
                        <th>Email / SĐT</th>
                        <th>Vai Trò (Role)</th>
                        <th>Trạng Thái</th>
                        <th>Ngày Tạo</th>
                        <th style="text-align:right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>#<?= $u['id'] ?></td>
                            <td><code><?= htmlspecialchars($u['username']) ?></code></td>
                            <td><strong><?= htmlspecialchars($u['full_name']) ?></strong></td>
                            <td>
                                <?= htmlspecialchars($u['email']) ?><br>
                                <span style="font-size:12px;color:#64748B;"><?= htmlspecialchars($u['phone']) ?></span>
                            </td>
                            <td>
                                <?php if ($u['role'] === 'ROLE_ADMIN'): ?>
                                    <span class="badge badge-danger"><i class="fa-solid fa-shield-halved"></i> Quản trị viên</span>
                                <?php elseif ($u['role'] === 'ROLE_DOCTOR'): ?>
                                    <span class="badge badge-info"><i class="fa-solid fa-user-doctor"></i> Bác sĩ</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-user"></i> Bệnh nhân</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($u['status'] == 1): ?>
                                    <span class="badge badge-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Đã khóa</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:12.5px;color:#64748B;"><?= formatDate($u['created_at']) ?></td>
                            <td style="text-align:right;white-space:nowrap;">
                                <!-- Khóa / Mở khóa -->
                                <?php if ($u['id'] != $adminUser['id']): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="status" value="<?= $u['status'] == 1 ? 0 : 1 ?>">
                                        <button type="submit" class="btn btn-sm <?= $u['status'] == 1 ? 'btn-outline-danger' : 'btn-outline-success' ?>" title="<?= $u['status'] == 1 ? 'Khóa tài khoản' : 'Kích hoạt' ?>">
                                            <i class="fa-solid <?= $u['status'] == 1 ? 'fa-lock' : 'fa-lock-open' ?>"></i>
                                        </button>
                                    </form>

                                    <!-- Đổi mật khẩu nhanh -->
                                    <button type="button" class="btn btn-sm btn-outline" onclick="openResetPassModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')" title="Cấp lại mật khẩu">
                                        <i class="fa-solid fa-key"></i>
                                    </button>

                                    <!-- Xóa tài khoản -->
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này khỏi hệ thống?');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                <?php else: ?>
                                    <span style="font-size:12px;color:#0D9488;font-weight:700;">Đang sử dụng</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL CẤP TÀI KHOẢN MỚI -->
<div id="user_create_modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#FFF;max-width:520px;width:100%;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-xl);">
        <div style="background:var(--primary);color:#FFF;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:16px;"><i class="fa-solid fa-user-plus"></i> Cấp Tài Khoản Thành Viên Mới</h4>
            <button type="button" onclick="closeUserCreateModal()" style="background:none;border:none;color:#FFF;font-size:20px;cursor:pointer;">&times;</button>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="tai-khoan.php">
                <input type="hidden" name="action" value="create_user">

                <div class="form-group">
                    <label class="form-label">Tên đăng nhập <span class="required">*</span></label>
                    <input type="text" name="username" class="form-control" required placeholder="viết liền không dấu...">
                </div>

                <div class="form-group">
                    <label class="form-label">Họ và tên <span class="required">*</span></label>
                    <input type="text" name="full_name" class="form-control" required placeholder="Họ và tên đầy đủ...">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="email@gmail.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Số điện thoại <span class="required">*</span></label>
                        <input type="tel" name="phone" class="form-control" required placeholder="0912345678">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Vai trò (Role) <span class="required">*</span></label>
                        <select name="role" class="form-select">
                            <option value="ROLE_PATIENT">Bệnh nhân (Patient)</option>
                            <option value="ROLE_DOCTOR">Bác sĩ (Doctor)</option>
                            <option value="ROLE_ADMIN">Quản trị viên (Admin)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mật khẩu ban đầu <span class="required">*</span></label>
                        <input type="text" name="password" class="form-control" value="123456" required>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
                    <button type="button" onclick="closeUserCreateModal()" class="btn btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Tạo Tài Khoản</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CẤP LẠI MẬT KHẨU -->
<div id="reset_pass_modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#FFF;max-width:440px;width:100%;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-xl);">
        <div style="background:var(--primary);color:#FFF;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:16px;"><i class="fa-solid fa-key"></i> Đặt Lại Mật Khẩu</h4>
            <button type="button" onclick="closeResetPassModal()" style="background:none;border:none;color:#FFF;font-size:20px;cursor:pointer;">&times;</button>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="tai-khoan.php">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="user_id" id="reset_user_id" value="">

                <p style="font-size:14px;color:#475569;margin-bottom:14px;">
                    Đặt lại mật khẩu cho tài khoản: <strong id="reset_username" style="color:var(--primary-dark);"></strong>
                </p>

                <div class="form-group">
                    <label class="form-label">Mật khẩu mới:</label>
                    <input type="text" name="new_password" class="form-control" value="123456" required>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
                    <button type="button" onclick="closeResetPassModal()" class="btn btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary">Xác Nhận Đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUserCreateModal() { document.getElementById('user_create_modal').style.display = 'flex'; }
function closeUserCreateModal() { document.getElementById('user_create_modal').style.display = 'none'; }
function openResetPassModal(id, username) {
    document.getElementById('reset_user_id').value = id;
    document.getElementById('reset_username').innerText = username;
    document.getElementById('reset_pass_modal').style.display = 'flex';
}
function closeResetPassModal() { document.getElementById('reset_pass_modal').style.display = 'none'; }
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
