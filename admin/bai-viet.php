<?php
$page_title = "Quản lý bài viết y tế";
$activeNav = 'bai_viet';
require_once __DIR__ . '/../includes/admin_header.php';

// XỬ LÝ THÊM / XÓA BÀI VIẾT (YC-11)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_post') {
        $title = sanitizeInput($_POST['title'] ?? '');
        $summary = sanitizeInput($_POST['summary'] ?? '');
        $content = $_POST['content'] ?? '';
        $category = sanitizeInput($_POST['category'] ?? 'Cẩm nang sức khỏe');
        $author = sanitizeInput($_POST['author_name'] ?? 'Bác sĩ An Nhiên');
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))) . '-' . time();
        $img = 'assets/images/posts/post-1.jpg';

        if ($title && $content) {
            $stmtIns = $pdo->prepare("INSERT INTO posts (title, slug, summary, content, author_name, image_url, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtIns->execute([$title, $slug, $summary, $content, $author, $img, $category]);
            setFlashMessage('success', "Đăng bài viết y tế mới thành công!");
        }
    } elseif ($action === 'delete_post') {
        $pId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
        if ($pId) {
            $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$pId]);
            setFlashMessage('success', "Đã xóa bài viết thành công!");
        }
    }
    header("Location: bai-viet.php");
    exit;
}

$posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC")->fetchAll();
$flash = getFlashMessage();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-newspaper" style="color:var(--primary);"></i> Quản Lý Bài Viết Cẩm Nang Y Khoa</h2>
        <p>Đăng tải, cập nhật kiến thức y tế, tin tức phòng khám và tư vấn sức khỏe cho người dân</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openPostModal()">
        <i class="fa-solid fa-plus"></i> Soạn Bài Viết Mới
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
        <h3><i class="fa-solid fa-file-lines" style="color:var(--primary);margin-right:8px;"></i> Danh Sách Bài Đăng (<?= count($posts) ?> bài viết)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px;">STT</th>
                        <th>Tiêu Đề Bài Viết</th>
                        <th>Chuyên Mục</th>
                        <th>Tác Giả</th>
                        <th>Lượt Xem</th>
                        <th>Ngày Đăng</th>
                        <th style="text-align:right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $idx = 1; foreach ($posts as $p): ?>
                        <tr>
                            <td><?= $idx++ ?></td>
                            <td>
                                <strong style="font-size:14.5px;color:#0F172A;"><?= htmlspecialchars($p['title']) ?></strong>
                            </td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($p['category']) ?></span></td>
                            <td><?= htmlspecialchars($p['author_name']) ?></td>
                            <td><i class="fa-regular fa-eye"></i> <?= $p['views'] ?></td>
                            <td><?= formatDate($p['created_at']) ?></td>
                            <td style="text-align:right;white-space:nowrap;">
                                <a href="<?= BASE_URL ?>/cam-nang-chi-tiet.php?id=<?= $p['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Xem bài">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?');">
                                    <input type="hidden" name="action" value="delete_post">
                                    <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
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

<!-- MODAL SOẠN BÀI VIẾT MỚI -->
<div id="post_modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#FFF;max-width:700px;width:100%;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-xl);max-height:90vh;overflow-y:auto;">
        <div style="background:var(--primary);color:#FFF;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:16px;"><i class="fa-solid fa-pen-nib"></i> Soạn Thảo Bài Viết Y Tế</h4>
            <button type="button" onclick="closePostModal()" style="background:none;border:none;color:#FFF;font-size:20px;cursor:pointer;">&times;</button>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="bai-viet.php">
                <input type="hidden" name="action" value="create_post">

                <div class="form-group">
                    <label class="form-label">Tiêu đề bài viết <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Ví dụ: Lưu ý phòng ngừa đột quỵ mùa lạnh..." required>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Chuyên mục:</label>
                        <select name="category" class="form-select">
                            <option value="Cẩm nang sức khỏe">Cẩm nang sức khỏe</option>
                            <option value="Tim Mạch">Tim Mạch</option>
                            <option value="Sản Phụ Khoa">Sản Phụ Khoa</option>
                            <option value="Cơ Xương Khớp">Cơ Xương Khớp</option>
                            <option value="Thận Nhân Tạo">Thận Nhân Tạo</option>
                            <option value="Tin tức phòng khám">Tin tức phòng khám</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tác giả / Bác sĩ biên soạn:</label>
                        <input type="text" name="author_name" class="form-control" value="Ban Biên Tập Y Khoa An Nhiên">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Tóm tắt ngắn (Summary):</label>
                    <textarea name="summary" class="form-control" rows="2" placeholder="Tóm tắt 1-2 câu về nội dung bài viết..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Nội dung chi tiết (HTML) <span class="required">*</span></label>
                    <textarea name="content" class="form-control" rows="6" placeholder="Nội dung bài viết y tế..." required></textarea>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
                    <button type="button" onclick="closePostModal()" class="btn btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Đăng Bài Viết</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPostModal() { document.getElementById('post_modal').style.display = 'flex'; }
function closePostModal() { document.getElementById('post_modal').style.display = 'none'; }
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
