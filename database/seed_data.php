<?php
/**
 * Script khởi tạo và gieo dữ liệu mẫu (Seeder) cho Phòng khám Đa khoa An Nhiên
 * Nhóm 12 - Lớp 74DCTT26 - ĐH Công nghệ Giao thông Vận tải
 */

$host = '127.0.0.1';
$port = '3306';
$dbname = 'phongkham_annhien';
$username = 'root';
$password = '123456';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Tạo CSDL nếu chưa có
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    echo "Đang khởi tạo các bảng CSDL...\n";

    // Đọc và chạy file schema
    $schemaSql = file_get_contents(__DIR__ . '/phongkham_annhien.sql');
    $pdo->exec($schemaSql);
    echo "Khởi tạo bảng hoàn tất!\n";

    // Xóa dữ liệu cũ nếu có để nạp mới tinh
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $tables = ['prescriptions', 'appointments', 'schedule_change_requests', 'doctor_schedules', 'clinics', 'doctors', 'specialties', 'users', 'posts', 'feedback'];
    foreach ($tables as $tbl) {
        $pdo->exec("TRUNCATE TABLE `$tbl`");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "Đang gieo dữ liệu mẫu Users...\n";
    $passHash = password_hash('123456', PASSWORD_BCRYPT);
    $adminPassHash = password_hash('admin123', PASSWORD_BCRYPT);

    $users = [
        ['admin', $adminPassHash, 'Ban Quản Trị Phòng Khám An Nhiên', 'admin@annhienclinic.vn', '0944809221', 'ROLE_ADMIN'],
        ['bs_minhduc', $passHash, 'BS CKI. Nguyễn Minh Đức', 'duc.nguyen@annhienclinic.vn', '0912345678', 'ROLE_DOCTOR'],
        ['bs_thanhhang', $passHash, 'ThS.BS. Trần Thanh Hằng', 'hang.tran@annhienclinic.vn', '0923456789', 'ROLE_DOCTOR'],
        ['bs_quanghuy', $passHash, 'TS.BS. Lê Quang Huy', 'huy.le@annhienclinic.vn', '0934567890', 'ROLE_DOCTOR'],
        ['bs_ngocmai', $passHash, 'BS CKI. Phạm Ngọc Mai', 'mai.pham@annhienclinic.vn', '0945678901', 'ROLE_DOCTOR'],
        ['bs_vanthanh', $passHash, 'BS CKI. Đỗ Văn Thành', 'thanh.do@annhienclinic.vn', '0956789012', 'ROLE_DOCTOR'],
        ['benhnhan1', $passHash, 'Trần Văn Bình', 'binh.tran@gmail.com', '0988776655', 'ROLE_PATIENT'],
        ['benhnhan2', $passHash, 'Nguyễn Thị Lan', 'lan.nguyen@gmail.com', '0977665544', 'ROLE_PATIENT'],
        ['benhnhan3', $passHash, 'Lê Tuấn Hùng', 'hung.le@gmail.com', '0966554433', 'ROLE_PATIENT']
    ];

    $stmtUser = $pdo->prepare("INSERT INTO users (username, password, full_name, email, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($users as $u) {
        $stmtUser->execute($u);
    }

    echo "Đang gieo dữ liệu Chuyên khoa...\n";
    $specialties = [
        [1, 'Khoa Tim Mạch', 'Chuyên sâu khám, điều trị các bệnh lý tim mạch, huyết áp, rối loạn nhịp tim và bệnh mạch vành với máy móc hiện đại.', 'assets/images/specialties/tim-mach.jpg', 'fa-heart-pulse'],
        [2, 'Khoa Cơ Xương Khớp', 'Điều trị thoái hóa cột sống, thoát vị đĩa đệm, đau khớp gối, viêm gân bằng phương pháp nội khoa và phục hồi chức năng.', 'assets/images/specialties/co-xuong-khop.jpg', 'fa-bone'],
        [3, 'Khoa Tiêu Hóa - Gan Mật', 'Thực hiện nội soi tiêu hóa không đau độ phân giải cao, chẩn đoán sớm ung thư và điều trị bệnh lý gan mật tụy.', 'assets/images/specialties/tieu-hoa.jpg', 'fa-stethoscope'],
        [4, 'Khoa Nhi', 'Khám chữa bệnh chuyên sâu và chăm sóc toàn diện cho trẻ sơ sinh và trẻ nhỏ trong không gian thân thiện, ấm áp.', 'assets/images/specialties/nhi-khoa.jpg', 'fa-baby'],
        [5, 'Khoa Sản Phụ Khoa', 'Quản lý thai kỳ, siêu âm dị tật 5D, điều trị bệnh phụ khoa và tầm soát ung thư cổ tử cung hàng đầu Nam Định.', 'assets/images/specialties/san-phu-khoa.jpg', 'fa-female'],
        [6, 'Khoa Tai Mũi Họng', 'Nội soi tầm soát ung thư vòm họng, điều trị viêm mũi dị ứng, viêm xoang mạn tính và viêm tai giữa bằng công nghệ mới.', 'assets/images/specialties/tai-mui-hong.jpg', 'fa-head-side-cough'],
        [7, 'Trung Tâm Thận Nhân Tạo', 'Thế mạnh đặc biệt của An Nhiên với hệ thống 20 máy lọc máu Fresenius chuẩn Bộ Y tế.', 'assets/images/specialties/than-nhan-tao.jpg', 'fa-droplet'],
        [8, 'Chẩn Đoán Hình Ảnh & Xét Nghiệm', 'Hệ thống CT Scanner đa lát cắt, X-quang kỹ thuật số và xét nghiệm sinh hóa tự động trả kết quả nhanh chóng, chính xác.', 'assets/images/specialties/xet-nghiem.jpg', 'fa-x-ray']
    ];

    $stmtSpec = $pdo->prepare("INSERT INTO specialties (id, name, description, image_url, icon) VALUES (?, ?, ?, ?, ?)");
    foreach ($specialties as $s) {
        $stmtSpec->execute($s);
    }

    echo "Đang gieo dữ liệu Bác sĩ...\n";
    $doctors = [
        // user_id, specialty_id, full_name, degree, experience_years, consultation_fee, bio, avatar, clinic_room
        [2, 1, 'BS CKI. Nguyễn Minh Đức', 'Bác sĩ Chuyên khoa I', 15, 250000.00, 'Từng công tác tại BV Đa khoa Tỉnh Nam Định. Chuyên gia hàng đầu về tim mạch, huyết áp và tim bẩm sinh.', 'assets/images/doctors/doctor-1.jpg', 'Phòng 201'],
        [3, 5, 'ThS.BS. Trần Thanh Hằng', 'Thạc sĩ Bác sĩ', 12, 200000.00, 'Tốt nghiệp ĐH Y Hà Nội, chuyên gia siêu âm hình thái học thai nhi 5D và can thiệp sản phụ khoa ít xâm lấn.', 'assets/images/doctors/doctor-2.jpg', 'Phòng 105'],
        [4, 7, 'TS.BS. Lê Quang Huy', 'Tiến sĩ Y khoa', 18, 300000.00, 'Tiến sĩ Y khoa, chuyên gia hàng đầu về Thận nhân tạo, lọc máu kỹ thuật cao và hồi sức nội khoa tại Nam Định.', 'assets/images/doctors/doctor-3.jpg', 'Phòng 301'],
        [5, 4, 'BS CKI. Phạm Ngọc Mai', 'Bác sĩ Chuyên khoa I', 10, 200000.00, 'Bác sĩ tận tâm, giàu tình cảm, có nhiều năm kinh nghiệm cấp cứu và điều trị các bệnh lý hô hấp, tiêu hóa ở trẻ em.', 'assets/images/doctors/doctor-4.jpg', 'Phòng 102'],
        [6, 6, 'BS CKI. Đỗ Văn Thành', 'Bác sĩ Chuyên khoa I', 14, 200000.00, 'Chuyên gia nội soi tai mũi họng dải tần hẹp (NBI), phát hiện sớm ung thư vòm họng và phục hồi chức năng.', 'assets/images/doctors/doctor-5.jpg', 'Phòng 104']
    ];

    $stmtDoc = $pdo->prepare("INSERT INTO doctors (user_id, specialty_id, full_name, degree, experience_years, consultation_fee, bio, avatar, clinic_room) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($doctors as $d) {
        $stmtDoc->execute($d);
    }

    echo "Đang gieo dữ liệu Phòng khám (Cơ sở vật chất)...\n";
    $clinics = [
        ['Phòng Khám Nội Tổng Quát & Khám Sàng Lọc', 'P.101', 3, 'Khám và đánh giá ban đầu, đo điện tim, đường huyết', 'ACTIVE'],
        ['Phòng Khám Nhi & Tiêm Chủng Mở Rộng', 'P.102', 4, 'Không gian trang trí sinh động, dụng cụ tiệt trùng chuẩn quốc tế', 'ACTIVE'],
        ['Phòng Khám Tai Mũi Họng & Nội Soi HD', 'P.104', 6, 'Hệ thống máy nội soi Karl Storz của Đức, ống mềm không đau', 'ACTIVE'],
        ['Phòng Khám Sản Phụ Khoa & Siêu Âm 5D', 'P.105', 5, 'Máy siêu âm Voluson E10 công nghệ 5D HD-Live cao cấp', 'ACTIVE'],
        ['Phòng Khám Chuyên Khoa Tim Mạch', 'P.201', 1, 'Hệ thống điện tâm đồ 12 chuyển đạo, Holter 24h, siêu âm tim', 'ACTIVE'],
        ['Phòng Khám Cơ Xương Khớp & Phục Hồi Chức Năng', 'P.203', 2, 'Trang bị máy kéo giãn cột sống, sóng xung kích Shockwave', 'ACTIVE'],
        ['Trung Tâm Thận Nhân Tạo & Lọc Máu Kỹ Thuật Cao', 'P.301', 7, 'Hệ thống 20 máy lọc máu Fresenius 4008S thế hệ mới', 'ACTIVE'],
        ['Khoa Chẩn Đoán Hình Ảnh & Xét Nghiệm Tự Động', 'P.305', 8, 'Hệ thống xét nghiệm tự động hóa hoàn toàn của Roche', 'ACTIVE']
    ];
    $stmtClinic = $pdo->prepare("INSERT INTO clinics (name, room_number, specialty_id, description, status) VALUES (?, ?, ?, ?, ?)");
    foreach ($clinics as $c) {
        $stmtClinic->execute($c);
    }

    echo "Đang gieo dữ liệu Lịch làm việc (Doctor Schedules) cho tuần này & tuần tới...\n";
    $stmtSched = $pdo->prepare("INSERT INTO doctor_schedules (doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $today = new DateTime();
    // Tạo lịch cho 6 bác sĩ trong 7 ngày tới
    for ($dayOffset = 0; $dayOffset <= 7; $dayOffset++) {
        $currDate = clone $today;
        $currDate->modify("+$dayOffset day");
        $dateStr = $currDate->format('Y-m-d');

        for ($docId = 1; $docId <= 5; $docId++) {
            // Ca Sáng
            $stmtSched->execute([$docId, $dateStr, '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', 15, ($dayOffset == 0 ? 3 : 1), 'AVAILABLE']);
            // Ca Chiều
            $stmtSched->execute([$docId, $dateStr, '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', 15, ($dayOffset == 0 ? 2 : 0), 'AVAILABLE']);
            // Ca Tối (chỉ thứ 2, 4, 6)
            if (in_array($currDate->format('N'), [1, 3, 5])) {
                $stmtSched->execute([$docId, $dateStr, '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', 10, 0, 'AVAILABLE']);
            }
        }
    }

    echo "Đang gieo dữ liệu Lịch hẹn mẫu (Appointments) & Đơn thuốc (Prescriptions)...\n";
    $todayStr = $today->format('Y-m-d');
    $yesterdayStr = (clone $today)->modify('-1 day')->format('Y-m-d');

    $appointments = [
        // 1. Đã hoàn thành (COMPLETED) - có đơn thuốc
        ['AN-2026-001', 8, 'Trần Văn Bình', '0988776655', 'binh.tran@gmail.com', '1985-05-15', 'Nam', 'Số 45 Trần Hưng Đạo, TP. Nam Định', 1, 1, $yesterdayStr, '08:30:00', 'Thường xuyên tức ngực trái khi vận động mạnh, kèm khó thở về đêm.', 'COMPLETED', 'PAID', 'BANK_TRANSFER', 250000.00],
        
        // 2. Đang trong phòng khám (IN_PROGRESS) - Bác sĩ đang khám
        ['AN-2026-002', 9, 'Nguyễn Thị Lan', '0977665544', 'lan.nguyen@gmail.com', '1992-09-20', 'Nữ', 'Xã Mỹ Phúc, Huyện Mỹ Lộc, Nam Định', 2, 3, $todayStr, '08:15:00', 'Khám thai định kỳ tuần thứ 28, muốn siêu âm 5D kiểm tra dị tật và cân nặng bé.', 'IN_PROGRESS', 'PAID', 'AT_CLINIC', 200000.00],

        // 3. Đã tiếp nhận tại sảnh (CHECKED_IN) - Chờ bác sĩ gọi
        ['AN-2026-003', 10, 'Lê Tuấn Hùng', '0966554433', 'hung.le@gmail.com', '1978-11-03', 'Nam', 'Phường Năng Tĩnh, TP. Nam Định', 3, 5, $todayStr, '09:00:00', 'Đau khớp gối hai bên khi đi lại cầu thang, cứng khớp vào buổi sáng khoảng 20 phút.', 'CHECKED_IN', 'PAID', 'MOMO', 300000.00],

        // 4. Đã xác nhận (CONFIRMED) - Chiều nay đến khám
        ['AN-2026-004', NULL, 'Hoàng Minh Khôi', '0933221100', 'khoi.hoang@yahoo.com', '2019-03-12', 'Nam', 'Huyện Vụ Bản, Tỉnh Nam Định', 4, 8, $todayStr, '14:00:00', 'Trẻ sốt cao 38.5 độ ngày thứ 2, ho húng hắng, sổ mũi nhiều, ăn kém.', 'CONFIRMED', 'UNPAID', 'AT_CLINIC', 200000.00],

        // 5. Chờ duyệt (PENDING) - Bệnh nhân mới gửi trực tuyến
        ['AN-2026-005', NULL, 'Vũ Bích Thảo', '0911223344', 'thao.vu@gmail.com', '1996-07-25', 'Nữ', 'Thị trấn Cổ Lễ, Trực Ninh, Nam Định', 5, 10, $todayStr, '15:30:00', 'Đau rát cổ họng, khàn tiếng kéo dài hơn 1 tuần, nuốt nghẹn.', 'PENDING', 'UNPAID', 'AT_CLINIC', 200000.00],

        // 6. Đã hoàn thành (COMPLETED)
        ['AN-2026-006', NULL, 'Phạm Đức Anh', '0909090909', 'anh.pham@gmail.com', '1965-02-18', 'Nam', 'Xã Nam Phong, TP. Nam Định', 6, 12, $yesterdayStr, '09:30:00', 'Suy thận mạn giai đoạn 4, định kỳ khám tư vấn chế độ lọc máu và bảo tồn chức năng thận.', 'COMPLETED', 'PAID', 'BANK_TRANSFER', 350000.00]
    ];

    $stmtApp = $pdo->prepare("INSERT INTO appointments (appointment_code, patient_id, patient_name, patient_phone, patient_email, patient_dob, patient_gender, patient_address, doctor_id, schedule_id, appointment_date, appointment_time, symptom_description, status, payment_status, payment_method, amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($appointments as $a) {
        $stmtApp->execute($a);
    }

    echo "Đang gieo dữ liệu Đơn thuốc điện tử (Prescriptions)...\n";
    $medicine1 = json_encode([
        ['name' => 'Amlodipine 5mg', 'unit' => 'Viên', 'quantity' => 30, 'usage' => 'Uống 1 viên vào buổi sáng sau ăn'],
        ['name' => 'Concor 2.5mg (Bisoprolol)', 'unit' => 'Viên', 'quantity' => 30, 'usage' => 'Uống 1 viên vào buổi sáng'],
        ['name' => 'Aspirin 81mg', 'unit' => 'Viên', 'quantity' => 30, 'usage' => 'Uống 1 viên vào buổi tối sau ăn no']
    ], JSON_UNESCAPED_UNICODE);

    $medicine2 = json_encode([
        ['name' => 'Ketosteril 600mg', 'unit' => 'Viên', 'quantity' => 60, 'usage' => 'Uống 4 viên/ngày chia 2 lần trong bữa ăn'],
        ['name' => 'Calcitriol 0.25mcg', 'unit' => 'Viên', 'quantity' => 30, 'usage' => 'Uống 1 viên/ngày vào buổi sáng'],
        ['name' => 'B Complex + Kẽm', 'unit' => 'Ống', 'quantity' => 20, 'usage' => 'Uống 1 ống/ngày sau ăn trưa']
    ], JSON_UNESCAPED_UNICODE);

    $reExam1 = (clone $today)->modify('+14 day')->format('Y-m-d');
    $reExam2 = (clone $today)->modify('+30 day')->format('Y-m-d');

    $stmtPres = $pdo->prepare("INSERT INTO prescriptions (appointment_id, diagnosis, medicine_list, doctor_notes, re_examination_date) VALUES (?, ?, ?, ?, ?)");
    $stmtPres->execute([1, 'Tăng huyết áp độ 2 (JNC 7) - Theo dõi thiếu máu cơ tim cục bộ', $medicine1, 'Ăn nhạt tuyệt đối, hạn chế mỡ động vật và cà phê. Đo huyết áp 2 lần/ngày sáng - tối ghi vào sổ tay.', $reExam1]);
    $stmtPres->execute([6, 'Bệnh thận mạn giai đoạn 4 (CKD G4) - Rối loạn chuyển hóa khoáng xương', $medicine2, 'Hạn chế đạm động vật, kiểm soát lượng nước uống hàng ngày = lượng nước tiểu + 500ml. Tránh dùng thuốc giảm đau NSAID.', $reExam2]);

    echo "Đang gieo dữ liệu Bài viết cẩm nang y tế & Tin tức (Posts)...\n";
    $posts = [
        [
            'Dấu hiệu cảnh báo bệnh lý Tim mạch nguy hiểm chớ nên xem nhẹ',
            'dau-hieu-canh-bao-benh-ly-tim-mach-nguy-hiem',
            'Bệnh lý tim mạch là nguyên nhân gây tử vong hàng đầu. Nhận biết sớm các triệu chứng giúp bảo vệ trái tim và tính mạng của bạn.',
            '<p>Bệnh tim mạch thường diễn tiến âm thầm nhưng để lại hậu quả nghiêm trọng. Dưới đây là 5 dấu hiệu bạn cần thăm khám ngay:</p><ul><li><strong>Đau tức vùng ngực:</strong> Cảm giác bị đè nén, bóp nghẹt ở giữa ngực kéo dài trên 10 phút.</li><li><strong>Khó thở khi gắng sức:</strong> Hụt hơi ngay cả khi làm việc nhẹ hoặc nằm ngủ phải kê cao gối.</li><li><strong>Hoa mắt, chóng mặt:</strong> Dấu hiệu của tụt huyết áp hoặc rối loạn nhịp tim.</li><li><strong>Phù hai chi dưới:</strong> Mắt cá chân sưng to, ấn vào có vết lõm.</li></ul><p>Phòng khám Đa khoa An Nhiên Nam Định với đội ngũ bác sĩ chuyên khoa Tim mạch giàu kinh nghiệm cùng hệ thống điện tim, siêu âm tim thế hệ mới sẵn sàng đồng hành chăm sóc sức khỏe của bạn.</p>',
            'BS CKI. Nguyễn Minh Đức',
            'assets/images/posts/post-1.jpg',
            'Tim Mạch',
            352
        ],
        [
            'Chăm sóc thai kỳ khoa học và các mốc siêu âm dị tật quan trọng',
            'cham-soc-thai-ky-khoa-hoc-va-cac-moc-sieu-am-quan-trong',
            'Siêu âm đúng thời điểm giúp phát hiện sớm các bất thường về hình thái và sự phát triển của thai nhi.',
            '<p>Khám thai định kỳ là việc làm vô cùng quan trọng đối với mỗi mẹ bầu. Các mốc siêu âm không thể bỏ qua gồm:</p><ol><li><strong>Tuần 11 - 13 tuần 6 ngày:</strong> Đo độ mờ da gáy sàng lọc hội chứng Down, Patau, Edwards.</li><li><strong>Tuần 18 - 22:</strong> Siêu âm 5D khảo sát toàn bộ hình thái thai nhi (tim, não, cột sống, tay chân, khuôn mặt).</li><li><strong>Tuần 30 - 32:</strong> Đánh giá sự tăng trưởng, vị trí bánh rau, lượng nước ối và ngôi thai.</li></ol><p>Tại Khoa Sản Phụ Khoa An Nhiên Nam Định, công nghệ siêu âm Voluson 5D mang lại hình ảnh sắc nét, chân thực nhất cho mẹ và bé.</p>',
            'ThS.BS. Trần Thanh Hằng',
            'assets/images/posts/post-2.jpg',
            'Sản Phụ Khoa',
            520
        ],
        [
            'Trung tâm Thận nhân tạo An Nhiên Nam Định - Điểm tựa cho bệnh nhân thận',
            'trung-tam-than-nhan-tao-an-nhien-nam-dinh',
            'Hợp tác chiến lược giữa VKIM Việt - Hàn mang đến công nghệ lọc máu siêu sạch, an toàn cho người dân tỉnh Nam Định.',
            '<p>Trung tâm Thận nhân tạo tại Phòng khám Đa khoa An Nhiên (Mỹ Lộc, Nam Định) được đầu tư đồng bộ với 20 dàn máy lọc máu hiện đại, hệ thống xử lý nước RO đạt tiêu chuẩn quốc tế và màng lọc quả lọc tương thích sinh học cao.</p><p>Đặc biệt, phòng khám áp dụng chính sách Bảo hiểm Y tế (BHYT) thông tuyến, giúp người bệnh giảm gánh nặng chi phí điều trị mà vẫn được thụ hưởng dịch vụ y tế chất lượng cao.</p>',
            'Ban Biên Tập An Nhiên',
            'assets/images/posts/post-3.jpg',
            'Thận Nhân Tạo',
            840
        ],
        [
            'Phương pháp giảm đau thoái hóa khớp gối không phẫu thuật',
            'phuong-phap-giam-dau-thoai-hoa-khop-goi-khong-phau-thuat',
            'Kết hợp điều trị nội khoa, tiêm chất nhờn nhân tạo Acid Hyaluronic và vật lý trị liệu phục hồi chức năng.',
            '<p>Thoái hóa khớp gối gây đau đớn, hạn chế vận động và ảnh hưởng nghiêm trọng đến sinh hoạt của người trung niên và cao tuổi. Việc điều trị sớm giúp bảo tồn khớp và tránh nguy cơ phải thay khớp nhân tạo.</p><p>Các phương pháp hiệu quả hiện nay tại An Nhiên gồm: Liệu pháp huyết tương giàu tiểu cầu (PRP), tiêm chất nhờn nội khớp, kết hợp máy sóng xung kích Shockwave giảm đau nhanh chóng.</p>',
            'TS.BS. Lê Quang Huy',
            'assets/images/posts/post-4.jpg',
            'Cơ Xương Khớp',
            290
        ]
    ];

    $stmtPost = $pdo->prepare("INSERT INTO posts (title, slug, summary, content, author_name, image_url, category, views) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($posts as $p) {
        $stmtPost->execute($p);
    }

    echo "Đang gieo dữ liệu Phản hồi & Liên hệ (Feedback)...\n";
    $feedback = [
        ['Vũ Hoàng Long', 'long.vh@gmail.com', '0912998877', 'Tư vấn khám sức khỏe tổng quát gia đình', 'Cho tôi hỏi phòng khám có gói khám sức khỏe định kỳ cho gia đình không và chi phí khoảng bao nhiêu?', 'Chào anh Long, An Nhiên có các gói khám tổng quát cơ bản và nâng cao cho gia đình với mức giá ưu đãi từ 1.200.000đ. Nhân viên CSKH sẽ liên hệ tư vấn chi tiết cho anh ạ.', 'REPLIED'],
        ['Đặng Thị Mai', 'mai.dang@gmail.com', '0988112233', 'Hỏi về chế độ BHYT tại phòng khám', 'Tôi có thẻ BHYT ở huyện Xuân Trường thì khi đến khám tại An Nhiên có được hưởng đúng tuyến không?', 'Dạ chào chị Mai, phòng khám An Nhiên thực hiện thông tuyến BHYT toàn quốc, chị sẽ được hưởng quyền lợi BHYT theo đúng quy định của Bộ Y tế ạ!', 'REPLIED']
    ];
    $stmtFeed = $pdo->prepare("INSERT INTO feedback (name, email, phone, subject, message, reply, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($feedback as $f) {
        $stmtFeed->execute($f);
    }

    echo "=========================================================\n";
    echo "HOÀN TẤT NẠP DỮ LIỆU CƠ SỞ DỮ LIỆU PHÒNG KHÁM AN NHIÊN!\n";
    echo "Tài khoản kiểm thử:\n";
    echo "- Admin:     admin      | Mật khẩu: admin123\n";
    echo "- Bác sĩ:    bs_minhduc | Mật khẩu: 123456\n";
    echo "- Bệnh nhân: benhnhan1  | Mật khẩu: 123456\n";
    echo "=========================================================\n";

} catch (PDOException $e) {
    die("Lỗi thực thi Seeder: " . $e->getMessage() . "\n");
}
