<?php
$page_title = "Quản lý Phòng khám cơ sở y tế";
$activeNav = 'phong_kham';
require_once __DIR__ . '/../includes/admin_header.php';

// XỬ LÝ THÊM / SỬA / XÓA PHÒNG KHÁM (YC-15, Sequence 3.4.5)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_clinic') {
        $name = sanitizeInput($_POST['name'] ?? '');
        $room = sanitizeInput($_POST['room_number'] ?? '');
        $specId = filter_input(INPUT_POST, 'specialty_id', FILTER_VALIDATE_INT);
        $desc = sanitizeInput($_POST['description'] ?? '');

        if ($name && $room) {
            $stmtIns = $pdo->prepare("INSERT INTO clinics (name, room_number, specialty_id, description, status) VALUES (?, ?, ?, ?, 'ACTIVE')");
            $stmtIns->execute([$name, $room, $specId, $desc]);
            setFlashMessage('success', "Thêm phòng khám {$room} thành công!");
        }
    } elseif ($action === 'delete_clinic') {
        $cId = filter_input(INPUT_POST, 'clinic_id', FILTER_VALIDATE_INT);
        if ($cId) {
            $pdo->prepare("DELETE FROM clinics WHERE id = ?")->execute([$cId]);
            setFlashMessage('success', "Đã xóa phòng khám khỏi hệ thống!");
        }
    }
    header("Location: phong-kham.php");
    exit;
}

$specialties = $pdo->query("SELECT * FROM specialties ORDER BY name ASC")->fetchAll();
$clinics = $pdo->query("SELECT c.*, s.name as specialty_name 
                        FROM clinics c 
                        LEFT JOIN specialties s ON c.specialty_id = s.id 
                        ORDER BY c.room_number ASC")->fetchAll();

$flash = getFlashMessage();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-hospital" style="color:var(--primary);"></i> Quản Lý Phòng Khám & Cơ Sở Y Tế</h2>
        <p>Thiết lập danh mục các phòng chức năng, buồng khám thực tế tại An Nhiên Nam Định</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openClinicModal()">
        <i class="fa-solid fa-plus"></i> Thêm Phòng Khám Mới
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
        <h3><i class="fa-solid fa-door-closed" style="color:var(--primary);margin-right:8px;"></i> Danh Sách Buồng Khám (<?= count($clinics) ?> phòng)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:100px;">Số Phòng</th>
                        <th>Tên Buồng Khám / Khoa Chức Năng</th>
                        <th>Chuyên Khoa</th>
                        <th>Trang Thiết Bị / Mô Tả</th>
                        <th>Trạng Thái</th>
                        <th style="text-align:right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clinics as $c): ?>
                        <tr>
                            <td><strong style="font-size:15px;color:var(--primary);"><?= htmlspecialchars($c['room_number']) ?></strong></td>
                            <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                            <td><span class="badge badge-purple"><?= htmlspecialchars($c['specialty_name'] ?? 'Đa khoa') ?></span></td>
                            <td style="max-width:300px;font-size:13px;color:#64748B;"><?= htmlspecialchars($c['description']) ?></td>
                            <td><span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Đang hoạt động</span></td>
                            <td style="text-align:right;">
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa phòng khám này?');">
                                    <input type="hidden" name="action" value="delete_clinic">
                                    <input type="hidden" name="clinic_id" value="<?= $c['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL THÊM PHÒNG KHÁM -->
<div id="clinic_modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#FFF;max-width:500px;width:100%;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-xl);">
        <div style="background:var(--primary);color:#FFF;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:16px;"><i class="fa-solid fa-plus"></i> Thêm Buồng Khám Mới</h4>
            <button type="button" onclick="closeClinicModal()" style="background:none;border:none;color:#FFF;font-size:20px;cursor:pointer;">&times;</button>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="phong-kham.php">
                <input type="hidden" name="action" value="create_clinic">

                <div class="form-group">
                    <label class="form-label">Số phòng (Mã buồng) <span class="required">*</span></label>
                    <input type="text" name="room_number" class="form-control" placeholder="Ví dụ: P.205, P.302..." required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tên phòng khám <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Phòng Khám Nội Tiết & Đái Tháo Đường" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Chuyên khoa trực thuộc</label>
                    <select name="specialty_id" class="form-select">
                        <option value="">-- Đa khoa / Dùng chung --</option>
                        <?php foreach ($specialties as $sp): ?>
                            <option value="<?= $sp['id'] ?>"><?= htmlspecialchars($sp['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Mô tả thiết bị & chức năng:</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Trang thiết bị trong buồng..."></textarea>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
                    <button type="button" onclick="closeClinicModal()" class="btn btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Thêm Phòng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openClinicModal() { document.getElementById('clinic_modal').style.display = 'flex'; }
function closeClinicModal() { document.getElementById('clinic_modal').style.display = 'none'; }
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
