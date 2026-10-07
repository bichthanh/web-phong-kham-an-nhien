<?php
$page_title = "Quản lý Chuyên khoa";
$activeNav = 'chuyen_khoa';
require_once __DIR__ . '/../includes/admin_header.php';

// XỬ LÝ THÊM / SỬA / XÓA CHUYÊN KHOA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_specialty') {
        $name = sanitizeInput($_POST['name'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $icon = sanitizeInput($_POST['icon'] ?? 'fa-stethoscope');

        if ($name) {
            $stmtChk = $pdo->prepare("SELECT COUNT(*) FROM specialties WHERE name = ?");
            $stmtChk->execute([$name]);
            if ($stmtChk->fetchColumn() > 0) {
                setFlashMessage('error', "Chuyên khoa '{$name}' đã tồn tại trong hệ thống!");
            } else {
                $img = 'assets/images/specialties/tim-mach.jpg';
                $stmtIns = $pdo->prepare("INSERT INTO specialties (name, description, image_url, icon) VALUES (?, ?, ?, ?)");
                $stmtIns->execute([$name, $description, $img, $icon]);
                setFlashMessage('success', "Thêm chuyên khoa '{$name}' thành công!");
            }
        }
    } elseif ($action === 'update_specialty') {
        $id = filter_input(INPUT_POST, 'specialty_id', FILTER_VALIDATE_INT);
        $name = sanitizeInput($_POST['name'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $icon = sanitizeInput($_POST['icon'] ?? 'fa-stethoscope');

        if ($id && $name) {
            $stmtUpd = $pdo->prepare("UPDATE specialties SET name = ?, description = ?, icon = ? WHERE id = ?");
            $stmtUpd->execute([$name, $description, $icon, $id]);
            setFlashMessage('success', "Cập nhật chuyên khoa thành công!");
        }
    } elseif ($action === 'delete_specialty') {
        // UC-AD-01 Ngoại lệ 5a: Kiểm tra có bác sĩ đang công tác trong khoa không
        $id = filter_input(INPUT_POST, 'specialty_id', FILTER_VALIDATE_INT);
        if ($id) {
            $stmtDocCount = $pdo->prepare("SELECT COUNT(*) FROM doctors WHERE specialty_id = ?");
            $stmtDocCount->execute([$id]);
            $docCount = $stmtDocCount->fetchColumn();

            if ($docCount > 0) {
                setFlashMessage('error', "Không thể xóa chuyên khoa này vì đang có {$docCount} bác sĩ công tác! Vui lòng chuyển công tác bác sĩ trước (Ràng buộc toàn vẹn UC-AD-01).");
            } else {
                $pdo->prepare("DELETE FROM specialties WHERE id = ?")->execute([$id]);
                setFlashMessage('success', "Đã xóa chuyên khoa thành công!");
            }
        }
    }
    header("Location: chuyen-khoa.php");
    exit;
}

$sql = "SELECT s.*, COUNT(d.id) as doc_count 
        FROM specialties s 
        LEFT JOIN doctors d ON s.id = d.specialty_id 
        GROUP BY s.id 
        ORDER BY s.id ASC";
$specialties = $pdo->query($sql)->fetchAll();
$flash = getFlashMessage();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-stethoscope" style="color:var(--primary);"></i> Quản Lý Danh Mục Chuyên Khoa</h2>
        <p>Quản lý các khoa khám bệnh tại Phòng khám Đa khoa An Nhiên Nam Định</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openSpecCreateModal()">
        <i class="fa-solid fa-plus"></i> Thêm Chuyên Khoa Mới
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
        <h3><i class="fa-solid fa-list-check" style="color:var(--primary);margin-right:8px;"></i> Danh Sách Chuyên Khoa (<?= count($specialties) ?> khoa)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:50px;">Mã</th>
                        <th>Biểu Tượng & Tên Chuyên Khoa</th>
                        <th>Mô Tả Chức Năng</th>
                        <th>Số Lượng Bác Sĩ</th>
                        <th style="text-align:right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($specialties as $sp): ?>
                        <tr>
                            <td>#<?= $sp['id'] ?></td>
                            <td>
                                <strong style="font-size:15px;color:#0F172A;">
                                    <i class="fa-solid <?= htmlspecialchars($sp['icon'] ?? 'fa-stethoscope') ?>" style="color:var(--primary);width:24px;"></i>
                                    <?= htmlspecialchars($sp['name']) ?>
                                </strong>
                            </td>
                            <td style="max-width:380px;font-size:13.5px;color:#64748B;">
                                <?= htmlspecialchars($sp['description']) ?>
                            </td>
                            <td>
                                <span class="badge badge-info"><i class="fa-solid fa-user-doctor"></i> <?= $sp['doc_count'] ?> Bác sĩ</span>
                            </td>
                            <td style="text-align:right;white-space:nowrap;">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick='openSpecEditModal(<?= json_encode($sp, JSON_UNESCAPED_UNICODE) ?>)'>
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa chuyên khoa này? Hệ thống sẽ kiểm tra ràng buộc bác sĩ.');">
                                    <input type="hidden" name="action" value="delete_specialty">
                                    <input type="hidden" name="specialty_id" value="<?= $sp['id'] ?>">
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

<!-- MODAL THÊM CHUYÊN KHOA -->
<div id="spec_create_modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#FFF;max-width:520px;width:100%;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-xl);">
        <div style="background:var(--primary);color:#FFF;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:16px;"><i class="fa-solid fa-plus"></i> Thêm Chuyên Khoa Mới</h4>
            <button type="button" onclick="closeSpecCreateModal()" style="background:none;border:none;color:#FFF;font-size:20px;cursor:pointer;">&times;</button>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="chuyen-khoa.php">
                <input type="hidden" name="action" value="create_specialty">

                <div class="form-group">
                    <label class="form-label">Tên chuyên khoa <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Khoa Mắt, Khoa Da Liễu..." required>
                </div>

                <div class="form-group">
                    <label class="form-label">Icon FontAwesome:</label>
                    <input type="text" name="icon" class="form-control" value="fa-stethoscope" placeholder="fa-heart, fa-bone...">
                </div>

                <div class="form-group">
                    <label class="form-label">Mô tả chuyên khoa <span class="required">*</span></label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Chức năng, nhiệm vụ thăm khám..." required></textarea>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
                    <button type="button" onclick="closeSpecCreateModal()" class="btn btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Thêm Chuyên Khoa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL SỬA CHUYÊN KHOA -->
<div id="spec_edit_modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#FFF;max-width:520px;width:100%;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-xl);">
        <div style="background:var(--primary);color:#FFF;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:16px;"><i class="fa-solid fa-pen-to-square"></i> Sửa Chuyên Khoa</h4>
            <button type="button" onclick="closeSpecEditModal()" style="background:none;border:none;color:#FFF;font-size:20px;cursor:pointer;">&times;</button>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="chuyen-khoa.php">
                <input type="hidden" name="action" value="update_specialty">
                <input type="hidden" name="specialty_id" id="edit_spec_id" value="">

                <div class="form-group">
                    <label class="form-label">Tên chuyên khoa <span class="required">*</span></label>
                    <input type="text" name="name" id="edit_spec_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Icon FontAwesome:</label>
                    <input type="text" name="icon" id="edit_spec_icon" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Mô tả chuyên khoa <span class="required">*</span></label>
                    <textarea name="description" id="edit_spec_desc" class="form-control" rows="3" required></textarea>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
                    <button type="button" onclick="closeSpecEditModal()" class="btn btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Cập Nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openSpecCreateModal() { document.getElementById('spec_create_modal').style.display = 'flex'; }
function closeSpecCreateModal() { document.getElementById('spec_create_modal').style.display = 'none'; }
function openSpecEditModal(sp) {
    document.getElementById('edit_spec_id').value = sp.id;
    document.getElementById('edit_spec_name').value = sp.name;
    document.getElementById('edit_spec_icon').value = sp.icon || 'fa-stethoscope';
    document.getElementById('edit_spec_desc').value = sp.description || '';
    document.getElementById('spec_edit_modal').style.display = 'flex';
}
function closeSpecEditModal() { document.getElementById('spec_edit_modal').style.display = 'none'; }
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
