<?php
/**
 * Các hàm tiện ích dùng chung toàn hệ thống
 * Phòng khám Đa khoa An Nhiên - Nam Định
 */

if (ob_get_level() === 0) {
    ob_start();
}

if (!defined('BASE_URL')) {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    if (strpos($scriptName, '/product_management') === 0) {
        define('BASE_URL', '/product_management');
    } else {
        define('BASE_URL', '');
    }
}

// Định dạng tiền tệ VNĐ (ví dụ: 250.000 đ)
function formatMoney($amount) {
    return number_format((float)$amount, 0, ',', '.') . ' đ';
}

// Định dạng ngày (ví dụ: 15/10/2026)
function formatDate($dateStr) {
    if (!$dateStr) return '---';
    $d = new DateTime($dateStr);
    return $d->format('d/m/Y');
}

// Định dạng giờ (ví dụ: 08:30)
function formatTime($timeStr) {
    if (!$timeStr) return '---';
    return substr($timeStr, 0, 5);
}

// Định dạng ngày giờ đầy đủ
function formatDateTime($dateTimeStr) {
    if (!$dateTimeStr) return '---';
    $d = new DateTime($dateTimeStr);
    return $d->format('d/m/Y H:i');
}

// Tạo mã phiếu lịch hẹn duy nhất dạng AN-2026-XXXXX
function generateAppointmentCode($pdo) {
    $year = date('Y');
    do {
        $rand = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
        $code = "AN-{$year}-{$rand}";
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_code = ?");
        $stmt->execute([$code]);
        $exists = $stmt->fetchColumn() > 0;
    } while ($exists);
    return $code;
}

// Huy hiệu trạng thái lịch hẹn
function getStatusBadge($status) {
    switch ($status) {
        case 'PENDING':
            return '<span class="badge badge-warning"><i class="fa-solid fa-clock"></i> Chờ duyệt</span>';
        case 'CONFIRMED':
            return '<span class="badge badge-info"><i class="fa-solid fa-check"></i> Đã xác nhận</span>';
        case 'CHECKED_IN':
            return '<span class="badge badge-purple"><i class="fa-solid fa-door-open"></i> Đã tiếp đón</span>';
        case 'IN_PROGRESS':
            return '<span class="badge badge-primary"><i class="fa-solid fa-stethoscope"></i> Đang khám</span>';
        case 'COMPLETED':
            return '<span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Hoàn thành</span>';
        case 'CANCELLED':
            return '<span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> Đã hủy</span>';
        case 'NO_SHOW':
            return '<span class="badge badge-secondary"><i class="fa-solid fa-user-slash"></i> Vắng mặt</span>';
        default:
            return '<span class="badge badge-secondary">' . htmlspecialchars($status) . '</span>';
    }
}

// Tên tiếng Việt của trạng thái
function getStatusText($status) {
    switch ($status) {
        case 'PENDING': return 'Chờ duyệt';
        case 'CONFIRMED': return 'Đã xác nhận';
        case 'CHECKED_IN': return 'Đã tiếp đón';
        case 'IN_PROGRESS': return 'Đang khám';
        case 'COMPLETED': return 'Hoàn thành';
        case 'CANCELLED': return 'Đã hủy';
        case 'NO_SHOW': return 'Vắng mặt';
        default: return $status;
    }
}

// Huy hiệu trạng thái thanh toán
function getPaymentBadge($status) {
    switch ($status) {
        case 'PAID':
            return '<span class="badge badge-success"><i class="fa-solid fa-receipt"></i> Đã thanh toán</span>';
        case 'UNPAID':
            return '<span class="badge badge-warning"><i class="fa-solid fa-hourglass-half"></i> Chưa thanh toán</span>';
        default:
            return '<span class="badge badge-secondary">' . htmlspecialchars($status) . '</span>';
    }
}

// Làm sạch dữ liệu đầu vào
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Lấy thông báo Flash từ session
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $msg = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $msg;
    }
    return null;
}

// Gán thông báo Flash vào session
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}
