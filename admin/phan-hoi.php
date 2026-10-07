<?php
$page_title = "Hỗ trợ & Phản hồi bệnh nhân";
$activeNav = 'phan_hoi';
require_once __DIR__ . '/../includes/admin_header.php';

// XỬ LÝ TRẢ LỜI / XÓA PHẢN HỒI (YC-16)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $fId = filter_input(INPUT_POST, 'feedback_id', FILTER_VALIDATE_INT);

    if ($action === 'reply_feedback' && $fId) {
        $reply = sanitizeInput($_POST['reply'] ?? '');
        $pdo->prepare("UPDATE feedback SET reply = ?, status = 'REPLIED' WHERE id = ?")->execute([$reply, $fId]);
        setFlashMessage('success', "Đã lưu phản hồi câu trả lời cho bệnh nhân!");
    } elseif ($action === 'delete_feedback' && $fId) {
        $pdo->prepare("DELETE FROM feedback WHERE id = ?")->execute([$fId]);
        setFlashMessage('success', "Đã xóa thư phản hồi!");
    }
    header("Location: phan-hoi.php");
    exit;
}

$feedbacks = $pdo->query("SELECT * FROM feedback ORDER BY created_at DESC")->fetchAll();
$flash = getFlashMessage();
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-comments" style="color:var(--primary);"></i> Hỗ Trợ Khách Hàng & Phản Hồi Bệnh Nhân</h2>
        <p>Tiếp nhận thắc mắc, tư vấn chế độ BHYT và giải quyết phản hồi từ người dân</p>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <div><?= htmlspecialchars($flash['message']) ?></div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-inbox" style="color:var(--primary);margin-right:8px;"></i> Hòm Thư Liên Hệ (<?= count($feedbacks) ?> tin nhắn)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Người Gửi</th>
                        <th>Thông Tin Liên Lạc</th>
                        <th>Chủ Đề & Nội Dung Thắc Mắc</th>
                        <th>Phản Hồi Từ An Nhiên</th>
                        <th>Trạng Thái</th>
                        <th style="text-align:right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($feedbacks)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;padding:40px;color:#94A3B8;">
                                Hòm thư liên hệ hiện đang trống.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($feedbacks as $f): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($f['name']) ?></strong></td>
                            <td>
                                SĐT: <strong style="color:#0284C7;"><?= htmlspecialchars($f['phone']) ?></strong><br>
                                <span style="font-size:12px;color:#64748B;"><?= htmlspecialchars($f['email']) ?></span>
                            </td>
                            <td style="max-width:320px;">
                                <strong style="color:#0F172A;"><?= htmlspecialchars($f['subject']) ?></strong>
                                <p style="font-size:13px;color:#475569;margin-top:4px;"><?= nl2br(htmlspecialchars($f['message'])) ?></p>
                                <span style="font-size:11.5px;color:#94A3B8;"><?= formatDate($f['created_at']) ?></span>
                            </td>
                            <td style="max-width:280px;font-size:13px;">
                                <?php if ($f['reply']): ?>
                                    <div style="background:#F0FDFA;padding:8px;border-radius:6px;border-left:3px solid var(--primary);color:#0F766E;">
                                        <?= nl2br(htmlspecialchars($f['reply'])) ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color:#94A3B8;font-style:italic;">Chưa có phản hồi</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($f['status'] === 'REPLIED'): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-check"></i> Đã phản hồi</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> Chờ hỗ trợ</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;white-space:nowrap;">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="openReplyModal(<?= $f['id'] ?>, '<?= htmlspecialchars($f['name']) ?>', '<?= htmlspecialchars(addslashes($f['reply'] ?? '')) ?>')">
                                    <i class="fa-solid fa-reply"></i> Trả lời
                                </button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa tin nhắn này?');">
                                    <input type="hidden" name="action" value="delete_feedback">
                                    <input type="hidden" name="feedback_id" value="<?= $f['id'] ?>">
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

<!-- MODAL TRẢ LỜI PHẢN HỒI -->
<div id="reply_modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#FFF;max-width:500px;width:100%;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-xl);">
        <div style="background:var(--primary);color:#FFF;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:16px;"><i class="fa-solid fa-reply"></i> Soạn Câu Trả Lời Cho Bệnh Nhân</h4>
            <button type="button" onclick="closeReplyModal()" style="background:none;border:none;color:#FFF;font-size:20px;cursor:pointer;">&times;</button>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="phan-hoi.php">
                <input type="hidden" name="action" value="reply_feedback">
                <input type="hidden" name="feedback_id" id="reply_feedback_id" value="">

                <p style="font-size:14px;color:#475569;margin-bottom:12px;">
                    Trả lời người gửi: <strong id="reply_patient_name" style="color:var(--primary-dark);"></strong>
                </p>

                <div class="form-group">
                    <label class="form-label">Nội dung phản hồi hỗ trợ <span class="required">*</span></label>
                    <textarea name="reply" id="reply_text" class="form-control" rows="4" required placeholder="Nhập nội dung giải đáp cho bệnh nhân..."></textarea>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
                    <button type="button" onclick="closeReplyModal()" class="btn btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Gửi Phản Hồi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openReplyModal(id, name, reply) {
    document.getElementById('reply_feedback_id').value = id;
    document.getElementById('reply_patient_name').innerText = name;
    document.getElementById('reply_text').value = reply || '';
    document.getElementById('reply_modal').style.display = 'flex';
}
function closeReplyModal() { document.getElementById('reply_modal').style.display = 'none'; }
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
