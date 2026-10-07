-- EXPORT DATABASE: phongkham_annhien
-- Nhóm 12 (74DCTT26 - UTT)
-- Created on 2026-10-07 17:21:46

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS appointments;
CREATE TABLE `appointments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `appointment_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_id` bigint DEFAULT NULL,
  `patient_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_phone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_dob` date DEFAULT NULL,
  `patient_gender` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'Nam',
  `patient_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doctor_id` bigint NOT NULL,
  `schedule_id` bigint NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `symptom_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `payment_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'UNPAID',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'AT_CLINIC',
  `amount` decimal(10,2) DEFAULT '200000.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `appointment_code` (`appointment_code`),
  KEY `fk_appointment_patient` (`patient_id`),
  KEY `fk_appointment_doctor` (`doctor_id`),
  KEY `fk_appointment_schedule` (`schedule_id`),
  CONSTRAINT `fk_appointment_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appointment_patient` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_appointment_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `doctor_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO appointments (id, appointment_code, patient_id, patient_name, patient_phone, patient_email, patient_dob, patient_gender, patient_address, doctor_id, schedule_id, appointment_date, appointment_time, symptom_description, status, payment_status, payment_method, amount, created_at) VALUES ('1', 'AN-2026-001', '8', 'Trần Văn Bình', '0988776655', 'binh.tran@gmail.com', '1985-05-15', 'Nam', 'Số 45 Trần Hưng Đạo, TP. Nam Định', '1', '1', '2026-10-06', '08:30:00', 'Thường xuyên tức ngực trái khi vận động mạnh, kèm khó thở về đêm.', 'COMPLETED', 'PAID', 'BANK_TRANSFER', '250000.00', '2026-10-07 22:07:44');
INSERT INTO appointments (id, appointment_code, patient_id, patient_name, patient_phone, patient_email, patient_dob, patient_gender, patient_address, doctor_id, schedule_id, appointment_date, appointment_time, symptom_description, status, payment_status, payment_method, amount, created_at) VALUES ('2', 'AN-2026-002', '9', 'Nguyễn Thị Lan', '0977665544', 'lan.nguyen@gmail.com', '1992-09-20', 'Nữ', 'Xã Mỹ Phúc, Huyện Mỹ Lộc, Nam Định', '2', '3', '2026-10-07', '08:15:00', 'Khám thai định kỳ tuần thứ 28, muốn siêu âm 5D kiểm tra dị tật và cân nặng bé.', 'IN_PROGRESS', 'PAID', 'AT_CLINIC', '200000.00', '2026-10-07 22:07:44');
INSERT INTO appointments (id, appointment_code, patient_id, patient_name, patient_phone, patient_email, patient_dob, patient_gender, patient_address, doctor_id, schedule_id, appointment_date, appointment_time, symptom_description, status, payment_status, payment_method, amount, created_at) VALUES ('3', 'AN-2026-003', '10', 'Lê Tuấn Hùng', '0966554433', 'hung.le@gmail.com', '1978-11-03', 'Nam', 'Phường Năng Tĩnh, TP. Nam Định', '3', '5', '2026-10-07', '09:00:00', 'Đau khớp gối hai bên khi đi lại cầu thang, cứng khớp vào buổi sáng khoảng 20 phút.', 'CHECKED_IN', 'PAID', 'MOMO', '300000.00', '2026-10-07 22:07:44');
INSERT INTO appointments (id, appointment_code, patient_id, patient_name, patient_phone, patient_email, patient_dob, patient_gender, patient_address, doctor_id, schedule_id, appointment_date, appointment_time, symptom_description, status, payment_status, payment_method, amount, created_at) VALUES ('4', 'AN-2026-004', NULL, 'Hoàng Minh Khôi', '0933221100', 'khoi.hoang@yahoo.com', '2019-03-12', 'Nam', 'Huyện Vụ Bản, Tỉnh Nam Định', '4', '8', '2026-10-07', '14:00:00', 'Trẻ sốt cao 38.5 độ ngày thứ 2, ho húng hắng, sổ mũi nhiều, ăn kém.', 'CONFIRMED', 'UNPAID', 'AT_CLINIC', '200000.00', '2026-10-07 22:07:44');
INSERT INTO appointments (id, appointment_code, patient_id, patient_name, patient_phone, patient_email, patient_dob, patient_gender, patient_address, doctor_id, schedule_id, appointment_date, appointment_time, symptom_description, status, payment_status, payment_method, amount, created_at) VALUES ('5', 'AN-2026-005', NULL, 'Vũ Bích Thảo', '0911223344', 'thao.vu@gmail.com', '1996-07-25', 'Nữ', 'Thị trấn Cổ Lễ, Trực Ninh, Nam Định', '5', '10', '2026-10-07', '15:30:00', 'Đau rát cổ họng, khàn tiếng kéo dài hơn 1 tuần, nuốt nghẹn.', 'PENDING', 'UNPAID', 'AT_CLINIC', '200000.00', '2026-10-07 22:07:44');
INSERT INTO appointments (id, appointment_code, patient_id, patient_name, patient_phone, patient_email, patient_dob, patient_gender, patient_address, doctor_id, schedule_id, appointment_date, appointment_time, symptom_description, status, payment_status, payment_method, amount, created_at) VALUES ('6', 'AN-2026-006', NULL, 'Phạm Đức Anh', '0909090909', 'anh.pham@gmail.com', '1965-02-18', 'Nam', 'Xã Nam Phong, TP. Nam Định', '3', '12', '2026-10-06', '09:30:00', 'Suy thận mạn giai đoạn 4, định kỳ khám tư vấn chế độ lọc máu và bảo tồn chức năng thận.', 'COMPLETED', 'PAID', 'BANK_TRANSFER', '350000.00', '2026-10-07 22:07:44');
INSERT INTO appointments (id, appointment_code, patient_id, patient_name, patient_phone, patient_email, patient_dob, patient_gender, patient_address, doctor_id, schedule_id, appointment_date, appointment_time, symptom_description, status, payment_status, payment_method, amount, created_at) VALUES ('7', 'AN-2026-FBCA3', NULL, 'Hoàng Thị Mai', '0912888999', 'mai.hoang@gmail.com', NULL, 'Nữ', 'TP. Nam Định', '1', '1', '2026-10-08', '08:00:00', 'Đau tức ngực và khó thở sau khi leo cầu thang', 'CONFIRMED', 'UNPAID', 'AT_CLINIC', '250000.00', '2026-10-07 22:39:06');
INSERT INTO appointments (id, appointment_code, patient_id, patient_name, patient_phone, patient_email, patient_dob, patient_gender, patient_address, doctor_id, schedule_id, appointment_date, appointment_time, symptom_description, status, payment_status, payment_method, amount, created_at) VALUES ('8', 'AN-2026-DC6CD', NULL, 'Trần Thị Mai', '0912345999', 'maitt@gmail.com', '1995-05-15', 'Nữ', 'TP. Nam Định', '1', '19', '2026-10-08', '08:00:00', 'Đau đầu, chóng mặt buổi sáng', 'CONFIRMED', 'UNPAID', 'AT_CLINIC', '250000.00', '2026-10-07 23:37:20');
INSERT INTO appointments (id, appointment_code, patient_id, patient_name, patient_phone, patient_email, patient_dob, patient_gender, patient_address, doctor_id, schedule_id, appointment_date, appointment_time, symptom_description, status, payment_status, payment_method, amount, created_at) VALUES ('9', 'AN-2026-393C4', NULL, 'Trần Thị Mai', '0912345999', 'maitt@gmail.com', '1995-05-15', 'Nữ', 'TP. Nam Định', '1', '19', '2026-10-08', '08:00:00', 'Đau đầu, chóng mặt buổi sáng', 'CONFIRMED', 'UNPAID', 'AT_CLINIC', '250000.00', '2026-10-07 23:37:56');

DROP TABLE IF EXISTS clinics;
CREATE TABLE `clinics` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `room_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialty_id` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVE',
  PRIMARY KEY (`id`),
  KEY `fk_clinic_specialty` (`specialty_id`),
  CONSTRAINT `fk_clinic_specialty` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO clinics (id, name, room_number, specialty_id, description, status) VALUES ('1', 'Phòng Khám Nội Tổng Quát & Khám Sàng Lọc', 'P.101', '3', 'Khám và đánh giá ban đầu, đo điện tim, đường huyết', 'ACTIVE');
INSERT INTO clinics (id, name, room_number, specialty_id, description, status) VALUES ('2', 'Phòng Khám Nhi & Tiêm Chủng Mở Rộng', 'P.102', '4', 'Không gian trang trí sinh động, dụng cụ tiệt trùng chuẩn quốc tế', 'ACTIVE');
INSERT INTO clinics (id, name, room_number, specialty_id, description, status) VALUES ('3', 'Phòng Khám Tai Mũi Họng & Nội Soi HD', 'P.104', '6', 'Hệ thống máy nội soi Karl Storz của Đức, ống mềm không đau', 'ACTIVE');
INSERT INTO clinics (id, name, room_number, specialty_id, description, status) VALUES ('4', 'Phòng Khám Sản Phụ Khoa & Siêu Âm 5D', 'P.105', '5', 'Máy siêu âm Voluson E10 công nghệ 5D HD-Live cao cấp', 'ACTIVE');
INSERT INTO clinics (id, name, room_number, specialty_id, description, status) VALUES ('5', 'Phòng Khám Chuyên Khoa Tim Mạch', 'P.201', '1', 'Hệ thống điện tâm đồ 12 chuyển đạo, Holter 24h, siêu âm tim', 'ACTIVE');
INSERT INTO clinics (id, name, room_number, specialty_id, description, status) VALUES ('6', 'Phòng Khám Cơ Xương Khớp & Phục Hồi Chức Năng', 'P.203', '2', 'Trang bị máy kéo giãn cột sống, sóng xung kích Shockwave', 'ACTIVE');
INSERT INTO clinics (id, name, room_number, specialty_id, description, status) VALUES ('7', 'Trung Tâm Thận Nhân Tạo & Lọc Máu Kỹ Thuật Cao', 'P.301', '7', 'Hệ thống 20 máy lọc máu Fresenius 4008S thế hệ mới', 'ACTIVE');
INSERT INTO clinics (id, name, room_number, specialty_id, description, status) VALUES ('8', 'Khoa Chẩn Đoán Hình Ảnh & Xét Nghiệm Tự Động', 'P.305', '8', 'Hệ thống xét nghiệm tự động hóa hoàn toàn của Roche', 'ACTIVE');

DROP TABLE IF EXISTS doctor_schedules;
CREATE TABLE `doctor_schedules` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `doctor_id` bigint NOT NULL,
  `work_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `session_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ca Sáng',
  `max_patients` int DEFAULT '15',
  `current_booked` int DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'AVAILABLE',
  PRIMARY KEY (`id`),
  KEY `fk_schedule_doctor` (`doctor_id`),
  CONSTRAINT `fk_schedule_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('1', '1', '2026-10-07', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '4', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('2', '1', '2026-10-07', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '2', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('3', '1', '2026-10-07', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('4', '2', '2026-10-07', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '3', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('5', '2', '2026-10-07', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '2', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('6', '2', '2026-10-07', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('7', '3', '2026-10-07', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '3', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('8', '3', '2026-10-07', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '2', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('9', '3', '2026-10-07', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('10', '4', '2026-10-07', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '3', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('11', '4', '2026-10-07', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '2', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('12', '4', '2026-10-07', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('13', '5', '2026-10-07', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '3', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('14', '5', '2026-10-07', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '2', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('15', '5', '2026-10-07', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('19', '1', '2026-10-08', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '3', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('20', '1', '2026-10-08', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('21', '2', '2026-10-08', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('22', '2', '2026-10-08', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('23', '3', '2026-10-08', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('24', '3', '2026-10-08', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('25', '4', '2026-10-08', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('26', '4', '2026-10-08', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('27', '5', '2026-10-08', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('28', '5', '2026-10-08', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('31', '1', '2026-10-09', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('32', '1', '2026-10-09', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('33', '1', '2026-10-09', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('34', '2', '2026-10-09', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('35', '2', '2026-10-09', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('36', '2', '2026-10-09', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('37', '3', '2026-10-09', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('38', '3', '2026-10-09', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('39', '3', '2026-10-09', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('40', '4', '2026-10-09', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('41', '4', '2026-10-09', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('42', '4', '2026-10-09', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('43', '5', '2026-10-09', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('44', '5', '2026-10-09', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('45', '5', '2026-10-09', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('49', '1', '2026-10-10', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('50', '1', '2026-10-10', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('51', '2', '2026-10-10', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('52', '2', '2026-10-10', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('53', '3', '2026-10-10', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('54', '3', '2026-10-10', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('55', '4', '2026-10-10', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('56', '4', '2026-10-10', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('57', '5', '2026-10-10', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('58', '5', '2026-10-10', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('61', '1', '2026-10-11', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('62', '1', '2026-10-11', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('63', '2', '2026-10-11', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('64', '2', '2026-10-11', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('65', '3', '2026-10-11', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('66', '3', '2026-10-11', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('67', '4', '2026-10-11', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('68', '4', '2026-10-11', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('69', '5', '2026-10-11', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('70', '5', '2026-10-11', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('73', '1', '2026-10-12', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('74', '1', '2026-10-12', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('75', '1', '2026-10-12', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('76', '2', '2026-10-12', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('77', '2', '2026-10-12', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('78', '2', '2026-10-12', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('79', '3', '2026-10-12', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('80', '3', '2026-10-12', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('81', '3', '2026-10-12', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('82', '4', '2026-10-12', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('83', '4', '2026-10-12', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('84', '4', '2026-10-12', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('85', '5', '2026-10-12', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('86', '5', '2026-10-12', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('87', '5', '2026-10-12', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('91', '1', '2026-10-13', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('92', '1', '2026-10-13', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('93', '2', '2026-10-13', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('94', '2', '2026-10-13', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('95', '3', '2026-10-13', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('96', '3', '2026-10-13', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('97', '4', '2026-10-13', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('98', '4', '2026-10-13', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('99', '5', '2026-10-13', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('100', '5', '2026-10-13', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('103', '1', '2026-10-14', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('104', '1', '2026-10-14', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('105', '1', '2026-10-14', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('106', '2', '2026-10-14', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('107', '2', '2026-10-14', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('108', '2', '2026-10-14', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('109', '3', '2026-10-14', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('110', '3', '2026-10-14', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('111', '3', '2026-10-14', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('112', '4', '2026-10-14', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('113', '4', '2026-10-14', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('114', '4', '2026-10-14', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('115', '5', '2026-10-14', '07:30:00', '11:30:00', 'Ca Sáng (07:30 - 11:30)', '15', '1', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('116', '5', '2026-10-14', '13:30:00', '17:00:00', 'Ca Chiều (13:30 - 17:00)', '15', '0', 'AVAILABLE');
INSERT INTO doctor_schedules (id, doctor_id, work_date, start_time, end_time, session_name, max_patients, current_booked, status) VALUES ('117', '5', '2026-10-14', '17:30:00', '20:00:00', 'Ca Tối (17:30 - 20:00)', '10', '0', 'AVAILABLE');

DROP TABLE IF EXISTS doctors;
CREATE TABLE `doctors` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `specialty_id` int NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `degree` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `experience_years` int DEFAULT '1',
  `consultation_fee` decimal(10,2) DEFAULT '200000.00',
  `bio` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clinic_room` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Phòng 101',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `fk_doctor_specialty` (`specialty_id`),
  CONSTRAINT `fk_doctor_specialty` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_doctor_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO doctors (id, user_id, specialty_id, full_name, degree, experience_years, consultation_fee, bio, avatar, clinic_room) VALUES ('1', '2', '1', 'BS CKI. Nguyễn Minh Đức', 'Bác sĩ Chuyên khoa I', '15', '250000.00', 'Từng công tác tại BV Đa khoa Tỉnh Nam Định. Chuyên gia hàng đầu về tim mạch, huyết áp và tim bẩm sinh.', 'assets/images/doctors/doctor-1.jpg', 'Phòng 201');
INSERT INTO doctors (id, user_id, specialty_id, full_name, degree, experience_years, consultation_fee, bio, avatar, clinic_room) VALUES ('2', '3', '5', 'ThS.BS. Trần Thanh Hằng', 'Thạc sĩ Bác sĩ', '12', '200000.00', 'Tốt nghiệp ĐH Y Hà Nội, chuyên gia siêu âm hình thái học thai nhi 5D và can thiệp sản phụ khoa ít xâm lấn.', 'assets/images/doctors/doctor-2.jpg', 'Phòng 105');
INSERT INTO doctors (id, user_id, specialty_id, full_name, degree, experience_years, consultation_fee, bio, avatar, clinic_room) VALUES ('3', '4', '7', 'TS.BS. Lê Quang Huy', 'Tiến sĩ Y khoa', '18', '300000.00', 'Tiến sĩ Y khoa, chuyên gia hàng đầu về Thận nhân tạo, lọc máu kỹ thuật cao và hồi sức nội khoa tại Nam Định.', 'assets/images/doctors/doctor-3.jpg', 'Phòng 301');
INSERT INTO doctors (id, user_id, specialty_id, full_name, degree, experience_years, consultation_fee, bio, avatar, clinic_room) VALUES ('4', '5', '4', 'BS CKI. Phạm Ngọc Mai', 'Bác sĩ Chuyên khoa I', '10', '200000.00', 'Bác sĩ tận tâm, giàu tình cảm, có nhiều năm kinh nghiệm cấp cứu và điều trị các bệnh lý hô hấp, tiêu hóa ở trẻ em.', 'assets/images/doctors/doctor-4.jpg', 'Phòng 102');
INSERT INTO doctors (id, user_id, specialty_id, full_name, degree, experience_years, consultation_fee, bio, avatar, clinic_room) VALUES ('5', '6', '6', 'BS CKI. Đỗ Văn Thành', 'Bác sĩ Chuyên khoa I', '14', '200000.00', 'Chuyên gia nội soi tai mũi họng dải tần hẹp (NBI), phát hiện sớm ung thư vòm họng và điều trị viêm xoang.', 'assets/images/doctors/doctor-5.jpg', 'Phòng 104');

DROP TABLE IF EXISTS feedback;
CREATE TABLE `feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO feedback (id, name, email, phone, subject, message, reply, status, created_at) VALUES ('1', 'Vũ Hoàng Long', 'long.vh@gmail.com', '0912998877', 'Tư vấn khám sức khỏe tổng quát gia đình', 'Cho tôi hỏi phòng khám có gói khám sức khỏe định kỳ cho gia đình không và chi phí khoảng bao nhiêu?', 'Chào anh Long, An Nhiên có các gói khám tổng quát cơ bản và nâng cao cho gia đình với mức giá ưu đãi từ 1.200.000đ. Nhân viên CSKH sẽ liên hệ tư vấn chi tiết cho anh ạ.', 'REPLIED', '2026-10-07 22:07:44');
INSERT INTO feedback (id, name, email, phone, subject, message, reply, status, created_at) VALUES ('2', 'Đặng Thị Mai', 'mai.dang@gmail.com', '0988112233', 'Hỏi về chế độ BHYT tại phòng khám', 'Tôi có thẻ BHYT ở huyện Xuân Trường thì khi đến khám tại An Nhiên có được hưởng đúng tuyến không?', 'Dạ chào chị Mai, phòng khám An Nhiên thực hiện thông tuyến BHYT toàn quốc, chị sẽ được hưởng quyền lợi BHYT theo đúng quy định của Bộ Y tế ạ!', 'REPLIED', '2026-10-07 22:07:44');

DROP TABLE IF EXISTS posts;
CREATE TABLE `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Ban Biên Tập Y Khoa An Nhiên',
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Cẩm nang sức khỏe',
  `views` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO posts (id, title, slug, summary, content, author_name, image_url, category, views, created_at) VALUES ('1', 'Dấu hiệu cảnh báo bệnh lý Tim mạch nguy hiểm chớ nên xem nhẹ', 'dau-hieu-canh-bao-benh-ly-tim-mach-nguy-hiem', 'Bệnh lý tim mạch là nguyên nhân gây tử vong hàng đầu. Nhận biết sớm các triệu chứng giúp bảo vệ trái tim và tính mạng của bạn.', '<p>Bệnh tim mạch thường diễn tiến âm thầm nhưng để lại hậu quả nghiêm trọng. Dưới đây là 5 dấu hiệu bạn cần thăm khám ngay:</p><ul><li><strong>Đau tức vùng ngực:</strong> Cảm giác bị đè nén, bóp nghẹt ở giữa ngực kéo dài trên 10 phút.</li><li><strong>Khó thở khi gắng sức:</strong> Hụt hơi ngay cả khi làm việc nhẹ hoặc nằm ngủ phải kê cao gối.</li><li><strong>Hoa mắt, chóng mặt:</strong> Dấu hiệu của tụt huyết áp hoặc rối loạn nhịp tim.</li><li><strong>Phù hai chi dưới:</strong> Mắt cá chân sưng to, ấn vào có vết lõm.</li></ul><p>Phòng khám Đa khoa An Nhiên Nam Định với đội ngũ bác sĩ chuyên khoa Tim mạch giàu kinh nghiệm cùng hệ thống điện tim, siêu âm tim thế hệ mới sẵn sàng đồng hành chăm sóc sức khỏe của bạn.</p>', 'BS CKI. Nguyễn Minh Đức', 'assets/images/posts/post-1.jpg', 'Tim Mạch', '353', '2026-10-07 22:07:44');
INSERT INTO posts (id, title, slug, summary, content, author_name, image_url, category, views, created_at) VALUES ('2', 'Chăm sóc thai kỳ khoa học và các mốc siêu âm dị tật quan trọng', 'cham-soc-thai-ky-khoa-hoc-va-cac-moc-sieu-am-quan-trong', 'Siêu âm đúng thời điểm giúp phát hiện sớm các bất thường về hình thái và sự phát triển của thai nhi.', '<p>Khám thai định kỳ là việc làm vô cùng quan trọng đối với mỗi mẹ bầu. Các mốc siêu âm không thể bỏ qua gồm:</p><ol><li><strong>Tuần 11 - 13 tuần 6 ngày:</strong> Đo độ mờ da gáy sàng lọc hội chứng Down, Patau, Edwards.</li><li><strong>Tuần 18 - 22:</strong> Siêu âm 5D khảo sát toàn bộ hình thái thai nhi (tim, não, cột sống, tay chân, khuôn mặt).</li><li><strong>Tuần 30 - 32:</strong> Đánh giá sự tăng trưởng, vị trí bánh rau, lượng nước ối và ngôi thai.</li></ol><p>Tại Khoa Sản Phụ Khoa An Nhiên Nam Định, công nghệ siêu âm Voluson 5D mang lại hình ảnh sắc nét, chân thực nhất cho mẹ và bé.</p>', 'ThS.BS. Trần Thanh Hằng', 'assets/images/posts/post-2.jpg', 'Sản Phụ Khoa', '520', '2026-10-07 22:07:44');
INSERT INTO posts (id, title, slug, summary, content, author_name, image_url, category, views, created_at) VALUES ('3', 'Trung tâm Thận nhân tạo An Nhiên Nam Định - Điểm tựa cho bệnh nhân thận', 'trung-tam-than-nhan-tao-an-nhien-nam-dinh', 'Hợp tác chiến lược giữa VKIM Việt - Hàn mang đến công nghệ lọc máu siêu sạch, an toàn cho người dân tỉnh Nam Định.', '<p>Trung tâm Thận nhân tạo tại Phòng khám Đa khoa An Nhiên (Mỹ Lộc, Nam Định) được đầu tư đồng bộ với 20 dàn máy lọc máu hiện đại, hệ thống xử lý nước RO đạt tiêu chuẩn quốc tế và màng lọc quả lọc tương thích sinh học cao.</p><p>Đặc biệt, phòng khám áp dụng chính sách Bảo hiểm Y tế (BHYT) thông tuyến, giúp người bệnh giảm gánh nặng chi phí điều trị mà vẫn được thụ hưởng dịch vụ y tế chất lượng cao.</p>', 'Ban Biên Tập An Nhiên', 'assets/images/posts/post-3.jpg', 'Thận Nhân Tạo', '840', '2026-10-07 22:07:44');
INSERT INTO posts (id, title, slug, summary, content, author_name, image_url, category, views, created_at) VALUES ('4', 'Phương pháp giảm đau thoái hóa khớp gối không phẫu thuật', 'phuong-phap-giam-dau-thoai-hoa-khop-goi-khong-phau-thuat', 'Kết hợp điều trị nội khoa, tiêm chất nhờn nhân tạo Acid Hyaluronic và vật lý trị liệu phục hồi chức năng.', '<p>Thoái hóa khớp gối gây đau đớn, hạn chế vận động và ảnh hưởng nghiêm trọng đến sinh hoạt của người trung niên và cao tuổi. Việc điều trị sớm giúp bảo tồn khớp và tránh nguy cơ phải thay khớp nhân tạo.</p><p>Các phương pháp hiệu quả hiện nay tại An Nhiên gồm: Liệu pháp huyết tương giàu tiểu cầu (PRP), tiêm chất nhờn nội khớp, kết hợp máy sóng xung kích Shockwave giảm đau nhanh chóng.</p>', 'TS.BS. Lê Quang Huy', 'assets/images/posts/post-4.jpg', 'Cơ Xương Khớp', '290', '2026-10-07 22:07:44');

DROP TABLE IF EXISTS prescriptions;
CREATE TABLE `prescriptions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `appointment_id` bigint NOT NULL,
  `diagnosis` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `medicine_list` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `doctor_notes` text COLLATE utf8mb4_unicode_ci,
  `re_examination_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `appointment_id` (`appointment_id`),
  CONSTRAINT `fk_prescription_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO prescriptions (id, appointment_id, diagnosis, medicine_list, doctor_notes, re_examination_date, created_at) VALUES ('1', '1', 'Tăng huyết áp độ 2 (JNC 7) - Theo dõi thiếu máu cơ tim cục bộ', '[{\"name\":\"Amlodipine 5mg\",\"unit\":\"Viên\",\"quantity\":30,\"usage\":\"Uống 1 viên vào buổi sáng sau ăn\"},{\"name\":\"Concor 2.5mg (Bisoprolol)\",\"unit\":\"Viên\",\"quantity\":30,\"usage\":\"Uống 1 viên vào buổi sáng\"},{\"name\":\"Aspirin 81mg\",\"unit\":\"Viên\",\"quantity\":30,\"usage\":\"Uống 1 viên vào buổi tối sau ăn no\"}]', 'Ăn nhạt tuyệt đối, hạn chế mỡ động vật và cà phê. Đo huyết áp 2 lần/ngày sáng - tối ghi vào sổ tay.', '2026-10-21', '2026-10-07 22:07:44');
INSERT INTO prescriptions (id, appointment_id, diagnosis, medicine_list, doctor_notes, re_examination_date, created_at) VALUES ('2', '6', 'Bệnh thận mạn giai đoạn 4 (CKD G4) - Rối loạn chuyển hóa khoáng xương', '[{\"name\":\"Ketosteril 600mg\",\"unit\":\"Viên\",\"quantity\":60,\"usage\":\"Uống 4 viên\\/ngày chia 2 lần trong bữa ăn\"},{\"name\":\"Calcitriol 0.25mcg\",\"unit\":\"Viên\",\"quantity\":30,\"usage\":\"Uống 1 viên\\/ngày vào buổi sáng\"},{\"name\":\"B Complex + Kẽm\",\"unit\":\"Ống\",\"quantity\":20,\"usage\":\"Uống 1 ống\\/ngày sau ăn trưa\"}]', 'Hạn chế đạm động vật, kiểm soát lượng nước uống hàng ngày = lượng nước tiểu + 500ml. Tránh dùng thuốc giảm đau NSAID.', '2026-11-06', '2026-10-07 22:07:44');

DROP TABLE IF EXISTS schedule_change_requests;
CREATE TABLE `schedule_change_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `doctor_id` bigint NOT NULL,
  `schedule_id` bigint NOT NULL,
  `request_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_change_doctor` (`doctor_id`),
  KEY `fk_change_schedule` (`schedule_id`),
  CONSTRAINT `fk_change_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_change_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `doctor_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS specialties;
CREATE TABLE `specialties` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO specialties (id, name, description, image_url, icon) VALUES ('1', 'Khoa Tim Mạch', 'Chuyên sâu khám, điều trị các bệnh lý tim mạch, huyết áp, rối loạn nhịp tim và bệnh mạch vành với máy móc hiện đại.', 'assets/images/specialties/tim-mach.jpg', 'fa-heart-pulse');
INSERT INTO specialties (id, name, description, image_url, icon) VALUES ('2', 'Khoa Cơ Xương Khớp', 'Điều trị thoái hóa cột sống, thoát vị đĩa đệm, đau khớp gối, viêm gân bằng phương pháp nội khoa và phục hồi chức năng.', 'assets/images/specialties/co-xuong-khop.jpg', 'fa-bone');
INSERT INTO specialties (id, name, description, image_url, icon) VALUES ('3', 'Khoa Tiêu Hóa - Gan Mật', 'Thực hiện nội soi tiêu hóa không đau độ phân giải cao, chẩn đoán sớm ung thư và điều trị bệnh lý gan mật tụy.', 'assets/images/specialties/tieu-hoa.jpg', 'fa-stethoscope');
INSERT INTO specialties (id, name, description, image_url, icon) VALUES ('4', 'Khoa Nhi', 'Khám chữa bệnh chuyên sâu và chăm sóc toàn diện cho trẻ sơ sinh và trẻ nhỏ trong không gian thân thiện, ấm áp.', 'assets/images/specialties/nhi-khoa.jpg', 'fa-baby');
INSERT INTO specialties (id, name, description, image_url, icon) VALUES ('5', 'Khoa Sản Phụ Khoa', 'Quản lý thai kỳ, siêu âm dị tật 5D, điều trị bệnh phụ khoa và tầm soát ung thư cổ tử cung hàng đầu Nam Định.', 'assets/images/specialties/san-phu-khoa.jpg', 'fa-female');
INSERT INTO specialties (id, name, description, image_url, icon) VALUES ('6', 'Khoa Tai Mũi Họng', 'Nội soi tầm soát ung thư vòm họng, điều trị viêm mũi dị ứng, viêm xoang mạn tính và viêm tai giữa bằng công nghệ mới.', 'assets/images/specialties/tai-mui-hong.jpg', 'fa-head-side-cough');
INSERT INTO specialties (id, name, description, image_url, icon) VALUES ('7', 'Trung Tâm Thận Nhân Tạo', 'Thế mạnh đặc biệt của An Nhiên liên kết Viện Y Dược Việt - Hàn (VKIM) với hệ thống máy lọc máu chuẩn quốc tế.', 'assets/images/specialties/than-nhan-tao.jpg', 'fa-droplet');
INSERT INTO specialties (id, name, description, image_url, icon) VALUES ('8', 'Chẩn Đoán Hình Ảnh & Xét Nghiệm', 'Hệ thống CT Scanner đa lát cắt, X-quang kỹ thuật số và xét nghiệm sinh hóa tự động trả kết quả nhanh chóng, chính xác.', 'assets/images/specialties/xet-nghiem.jpg', 'fa-x-ray');

DROP TABLE IF EXISTS users;
CREATE TABLE `users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ROLE_PATIENT',
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (id, username, password, full_name, email, phone, role, status, created_at) VALUES ('1', 'admin', '$2y$10$mC3SPRaWgugkBBXGuNPrIORcLa3g8JRsLahvCRCpcB0iTTDu2v9WK', 'Ban Quản Trị Phòng Khám An Nhiên', 'admin@annhienclinic.vn', '0944809221', 'ROLE_ADMIN', '1', '2026-10-07 22:07:43');
INSERT INTO users (id, username, password, full_name, email, phone, role, status, created_at) VALUES ('2', 'bs_minhduc', '$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O', 'BS CKI. Nguyễn Minh Đức', 'duc.nguyen@annhienclinic.vn', '0912345678', 'ROLE_DOCTOR', '1', '2026-10-07 22:07:43');
INSERT INTO users (id, username, password, full_name, email, phone, role, status, created_at) VALUES ('3', 'bs_thanhhang', '$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O', 'ThS.BS. Trần Thanh Hằng', 'hang.tran@annhienclinic.vn', '0923456789', 'ROLE_DOCTOR', '1', '2026-10-07 22:07:43');
INSERT INTO users (id, username, password, full_name, email, phone, role, status, created_at) VALUES ('4', 'bs_quanghuy', '$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O', 'TS.BS. Lê Quang Huy', 'huy.le@annhienclinic.vn', '0934567890', 'ROLE_DOCTOR', '1', '2026-10-07 22:07:43');
INSERT INTO users (id, username, password, full_name, email, phone, role, status, created_at) VALUES ('5', 'bs_ngocmai', '$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O', 'BS CKI. Phạm Ngọc Mai', 'mai.pham@annhienclinic.vn', '0945678901', 'ROLE_DOCTOR', '1', '2026-10-07 22:07:43');
INSERT INTO users (id, username, password, full_name, email, phone, role, status, created_at) VALUES ('6', 'bs_vanthanh', '$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O', 'BS CKI. Đỗ Văn Thành', 'thanh.do@annhienclinic.vn', '0956789012', 'ROLE_DOCTOR', '1', '2026-10-07 22:07:43');
INSERT INTO users (id, username, password, full_name, email, phone, role, status, created_at) VALUES ('8', 'benhnhan1', '$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O', 'Trần Văn Bình', 'binh.tran@gmail.com', '0988776655', 'ROLE_PATIENT', '1', '2026-10-07 22:07:43');
INSERT INTO users (id, username, password, full_name, email, phone, role, status, created_at) VALUES ('9', 'benhnhan2', '$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O', 'Nguyễn Thị Lan', 'lan.nguyen@gmail.com', '0977665544', 'ROLE_PATIENT', '1', '2026-10-07 22:07:43');
INSERT INTO users (id, username, password, full_name, email, phone, role, status, created_at) VALUES ('10', 'benhnhan3', '$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O', 'Lê Tuấn Hùng', 'hung.le@gmail.com', '0966554433', 'ROLE_PATIENT', '1', '2026-10-07 22:07:43');

SET FOREIGN_KEY_CHECKS = 1;
