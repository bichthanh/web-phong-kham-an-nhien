-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: phongkham_annhien
-- ------------------------------------------------------
-- Server version	9.5.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ 'ae9a9790-e240-11f0-9fbe-cc483a5de96d:1-579';

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  `doctor_reminder` text COLLATE utf8mb4_unicode_ci COMMENT 'Lời nhắc nhở trước/trong khám của bác sĩ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `appointment_code` (`appointment_code`),
  KEY `fk_appointment_patient` (`patient_id`),
  KEY `fk_appointment_doctor` (`doctor_id`),
  KEY `fk_appointment_schedule` (`schedule_id`),
  CONSTRAINT `fk_appointment_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appointment_patient` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_appointment_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `doctor_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
INSERT INTO `appointments` VALUES (1,'AN-2026-001',8,'Trần Văn Bình','0988776655','binh.tran@gmail.com','1985-05-15','Nam','Số 45 Trần Hưng Đạo, TP. Nam Định',1,1,'2026-10-06','08:30:00','Thường xuyên tức ngực trái khi vận động mạnh, kèm khó thở về đêm.','COMPLETED','PAID','BANK_TRANSFER',250000.00,'2026-10-07 15:07:44',NULL),(2,'AN-2026-002',9,'Nguyễn Thị Lan','0977665544','lan.nguyen@gmail.com','1992-09-20','Nữ','Xã Mỹ Phúc, Huyện Mỹ Lộc, Nam Định',2,3,'2026-10-07','08:15:00','Khám thai định kỳ tuần thứ 28, muốn siêu âm 5D kiểm tra dị tật và cân nặng bé.','IN_PROGRESS','PAID','AT_CLINIC',200000.00,'2026-10-07 15:07:44',NULL),(3,'AN-2026-003',10,'Lê Tuấn Hùng','0966554433','hung.le@gmail.com','1978-11-03','Nam','Phường Năng Tĩnh, TP. Nam Định',3,5,'2026-10-07','09:00:00','Đau khớp gối hai bên khi đi lại cầu thang, cứng khớp vào buổi sáng khoảng 20 phút.','CHECKED_IN','PAID','MOMO',300000.00,'2026-10-07 15:07:44',NULL),(4,'AN-2026-004',NULL,'Hoàng Minh Khôi','0933221100','khoi.hoang@yahoo.com','2019-03-12','Nam','Huyện Vụ Bản, Tỉnh Nam Định',4,8,'2026-10-07','14:00:00','Trẻ sốt cao 38.5 độ ngày thứ 2, ho húng hắng, sổ mũi nhiều, ăn kém.','CONFIRMED','UNPAID','AT_CLINIC',200000.00,'2026-10-07 15:07:44',NULL),(5,'AN-2026-005',NULL,'Vũ Bích Thảo','0911223344','thao.vu@gmail.com','1996-07-25','Nữ','Thị trấn Cổ Lễ, Trực Ninh, Nam Định',5,10,'2026-10-07','15:30:00','Đau rát cổ họng, khàn tiếng kéo dài hơn 1 tuần, nuốt nghẹn.','CONFIRMED','UNPAID','AT_CLINIC',200000.00,'2026-10-07 15:07:44',NULL),(6,'AN-2026-006',NULL,'Phạm Đức Anh','0909090909','anh.pham@gmail.com','1965-02-18','Nam','Xã Nam Phong, TP. Nam Định',3,12,'2026-10-06','09:30:00','Suy thận mạn giai đoạn 4, định kỳ khám tư vấn chế độ lọc máu và bảo tồn chức năng thận.','COMPLETED','PAID','BANK_TRANSFER',350000.00,'2026-10-07 15:07:44',NULL),(7,'AN-2026-FBCA3',NULL,'Hoàng Thị Mai','0912888999','mai.hoang@gmail.com',NULL,'Nữ','TP. Nam Định',1,1,'2026-10-08','08:00:00','Đau tức ngực và khó thở sau khi leo cầu thang','CONFIRMED','PAID','CASH',250000.00,'2026-10-07 15:39:06',NULL),(8,'AN-2026-DC6CD',NULL,'Trần Thị Mai','0912345999','maitt@gmail.com','1995-05-15','Nữ','TP. Nam Định',1,19,'2026-10-08','08:00:00','Đau đầu, chóng mặt buổi sáng','CHECKED_IN','UNPAID','AT_CLINIC',250000.00,'2026-10-07 16:37:20',NULL),(9,'AN-2026-393C4',NULL,'Trần Thị Mai','0912345999','maitt@gmail.com','1995-05-15','Nữ','TP. Nam Định',1,19,'2026-10-08','08:00:00','Đau đầu, chóng mặt buổi sáng','CONFIRMED','UNPAID','AT_CLINIC',250000.00,'2026-10-07 16:37:56',NULL),(10,'AN-2026-FAE30',NULL,'Lưu Thị Bích Thanh','0944809221','luuthanh86@gmail.com','2005-10-15','Nữ','Hà Nội',5,44,'2026-10-09','13:30:00','ho đau họng','CHECKED_IN','PAID','CASH',200000.00,'2026-10-08 07:52:58',NULL),(11,'AN-2026-76A8B',NULL,'thư','0944809221','admin@annhienclinic.vn','2026-10-06','Nữ','Hà Nội',5,27,'2026-10-08','07:30:00','ho','CHECKED_IN','UNPAID','AT_CLINIC',200000.00,'2026-10-08 09:28:31',NULL),(12,'AN-2026-CFFA3',NULL,'an','123','ann@gmail.com',NULL,'Nam','ha noi',5,69,'2026-10-11','07:30:00','ho qua nhiue','CHECKED_IN','UNPAID','BANK_TRANSFER',200000.00,'2026-10-10 07:25:53',NULL),(13,'AN-2026-8166B',NULL,'Nguyễn Mai Hoa','0945678432','admin@annhienclinic.vn','1997-11-20','Nữ','Nam Định',2,205,'2026-10-22','08:00:00','khó chịu trong người , đau bụng','CHECKED_IN','PAID','CASH',200000.00,'2026-10-22 01:59:05',NULL),(14,'AN-2026-0BE10',NULL,'Hoàng Tuấn Anh','0914567432','duc.nguyen@annhienclinic.vn','1993-10-13','Nam','Nam Định',1,202,'2026-10-22','15:30:00','đau tim , tức ngực','COMPLETED','PAID','CASH',250000.00,'2026-10-22 02:03:11','Ăn uống lành mạnh hơn'),(15,'AN-2026-1D12E',NULL,'Hồ Minh Tâm','0923456345','duc.nguyen@annhienclinic.vn','2000-01-20','Nam','Nam Định',1,201,'2026-10-22','08:00:00','ho sốt tức ngực , đau im tức ngực','IN_PROGRESS','PAID','CASH',250000.00,'2026-10-22 03:01:00',NULL),(16,'AN-2026-A7DD5',11,'Lưu Thị Bích Thanh','0987654356','bichthanh@gamil.com','2000-06-21','Nữ','Hà Nội',1,202,'2026-10-22','15:30:00','muốn khám kiểm tra','CONFIRMED','UNPAID','AT_CLINIC',250000.00,'2026-10-22 06:13:13','Có mặt trước 15 phút tại quầy lễ tân tầng 1 để đo huyết áp ban đầu.'),(17,'AN-2026-6835C',11,'Hùng','0982387967','bichthanh@gamil.com','2006-10-20','Nam','',1,202,'2026-10-22','15:30:00','muốn khám kiểm tra','CONFIRMED','UNPAID','MOMO',250000.00,'2026-10-22 06:26:04',NULL),(18,'AN-2026-D9006',11,'Mai Anh','0965432789','maianh@gmail.com','1996-05-22','Nữ','Hà Nội',1,202,'2026-10-22','15:30:00','muốn  khám kiểm tra','CONFIRMED','UNPAID','BANK_TRANSFER',250000.00,'2026-10-22 06:28:21',NULL),(19,'AN-2026-1E3EB',11,'Ngọc My','0923456542','ngicmy@gmail.com','1996-10-23','Nữ','Nam Định',1,202,'2026-10-22','15:30:00','muốn kiểm tra khám','CONFIRMED','UNPAID','AT_CLINIC',250000.00,'2026-10-22 06:29:55',NULL);
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clinics`
--

DROP TABLE IF EXISTS `clinics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clinics`
--

LOCK TABLES `clinics` WRITE;
/*!40000 ALTER TABLE `clinics` DISABLE KEYS */;
INSERT INTO `clinics` VALUES (1,'Phòng Khám Nội Tổng Quát & Khám Sàng Lọc','P.101',3,'Khám và đánh giá ban đầu, đo điện tim, đường huyết','ACTIVE'),(2,'Phòng Khám Nhi & Tiêm Chủng Mở Rộng','P.102',4,'Không gian trang trí sinh động, dụng cụ tiệt trùng chuẩn quốc tế','ACTIVE'),(3,'Phòng Khám Tai Mũi Họng & Nội Soi HD','P.104',6,'Hệ thống máy nội soi Karl Storz của Đức, ống mềm không đau','ACTIVE'),(4,'Phòng Khám Sản Phụ Khoa & Siêu Âm 5D','P.105',5,'Máy siêu âm Voluson E10 công nghệ 5D HD-Live cao cấp','ACTIVE'),(5,'Phòng Khám Chuyên Khoa Tim Mạch','P.201',1,'Hệ thống điện tâm đồ 12 chuyển đạo, Holter 24h, siêu âm tim','ACTIVE'),(6,'Phòng Khám Cơ Xương Khớp & Phục Hồi Chức Năng','P.203',2,'Trang bị máy kéo giãn cột sống, sóng xung kích Shockwave','ACTIVE'),(7,'Trung Tâm Thận Nhân Tạo & Lọc Máu Kỹ Thuật Cao','P.301',7,'Hệ thống 20 máy lọc máu Fresenius 4008S thế hệ mới','ACTIVE'),(8,'Khoa Chẩn Đoán Hình Ảnh & Xét Nghiệm Tự Động','P.305',8,'Hệ thống xét nghiệm tự động hóa hoàn toàn của Roche','ACTIVE');
/*!40000 ALTER TABLE `clinics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctor_schedules`
--

DROP TABLE IF EXISTS `doctor_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctor_schedules`
--

LOCK TABLES `doctor_schedules` WRITE;
/*!40000 ALTER TABLE `doctor_schedules` DISABLE KEYS */;
INSERT INTO `doctor_schedules` VALUES (1,1,'2026-10-07','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,2,'AVAILABLE'),(3,1,'2026-10-07','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,1,'AVAILABLE'),(4,2,'2026-10-07','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(5,2,'2026-10-07','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,1,'AVAILABLE'),(7,3,'2026-10-07','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(8,3,'2026-10-07','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,1,'AVAILABLE'),(10,4,'2026-10-07','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,1,'AVAILABLE'),(12,4,'2026-10-07','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,1,'AVAILABLE'),(13,5,'2026-10-07','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(14,5,'2026-10-07','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(19,1,'2026-10-08','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,2,'AVAILABLE'),(20,1,'2026-10-08','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(21,2,'2026-10-08','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(22,2,'2026-10-08','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(23,3,'2026-10-08','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(24,3,'2026-10-08','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(25,4,'2026-10-08','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(26,4,'2026-10-08','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(27,5,'2026-10-08','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,1,'AVAILABLE'),(28,5,'2026-10-08','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(31,1,'2026-10-09','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(32,1,'2026-10-09','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(34,2,'2026-10-09','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(35,2,'2026-10-09','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(37,3,'2026-10-09','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(38,3,'2026-10-09','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(40,4,'2026-10-09','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(41,4,'2026-10-09','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(43,5,'2026-10-09','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(44,5,'2026-10-09','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,1,'AVAILABLE'),(49,1,'2026-10-10','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(50,1,'2026-10-10','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(51,2,'2026-10-10','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(52,2,'2026-10-10','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(53,3,'2026-10-10','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(54,3,'2026-10-10','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(55,4,'2026-10-10','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(56,4,'2026-10-10','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(57,5,'2026-10-10','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(58,5,'2026-10-10','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(61,1,'2026-10-11','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(62,1,'2026-10-11','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(63,2,'2026-10-11','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(64,2,'2026-10-11','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(65,3,'2026-10-11','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(66,3,'2026-10-11','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(67,4,'2026-10-11','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(68,4,'2026-10-11','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(69,5,'2026-10-11','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,1,'AVAILABLE'),(70,5,'2026-10-11','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(73,1,'2026-10-12','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(74,1,'2026-10-12','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(76,2,'2026-10-12','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(77,2,'2026-10-12','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(79,3,'2026-10-12','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(80,3,'2026-10-12','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(82,4,'2026-10-12','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(83,4,'2026-10-12','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(85,5,'2026-10-12','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(86,5,'2026-10-12','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(91,1,'2026-10-13','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(92,1,'2026-10-13','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(93,2,'2026-10-13','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(94,2,'2026-10-13','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(95,3,'2026-10-13','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(96,3,'2026-10-13','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(97,4,'2026-10-13','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(98,4,'2026-10-13','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(99,5,'2026-10-13','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(100,5,'2026-10-13','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(103,1,'2026-10-14','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(104,1,'2026-10-14','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(106,2,'2026-10-14','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(107,2,'2026-10-14','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(109,3,'2026-10-14','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(110,3,'2026-10-14','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(112,4,'2026-10-14','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(113,4,'2026-10-14','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(115,5,'2026-10-14','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(116,5,'2026-10-14','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(131,1,'2026-10-15','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(132,1,'2026-10-15','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(133,4,'2026-10-15','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(134,4,'2026-10-15','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(135,2,'2026-10-15','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(136,2,'2026-10-15','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(137,5,'2026-10-15','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(138,5,'2026-10-15','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(139,3,'2026-10-15','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(140,3,'2026-10-15','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(141,1,'2026-10-16','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(142,1,'2026-10-16','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(143,4,'2026-10-16','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(144,4,'2026-10-16','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(145,2,'2026-10-16','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(146,2,'2026-10-16','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(147,5,'2026-10-16','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(148,5,'2026-10-16','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(149,3,'2026-10-16','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(150,3,'2026-10-16','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(151,1,'2026-10-17','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(152,1,'2026-10-17','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(153,4,'2026-10-17','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(154,4,'2026-10-17','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(155,2,'2026-10-17','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(156,2,'2026-10-17','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(157,5,'2026-10-17','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(158,5,'2026-10-17','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(159,3,'2026-10-17','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(160,3,'2026-10-17','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(161,1,'2026-10-18','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(162,1,'2026-10-18','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(163,4,'2026-10-18','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(164,4,'2026-10-18','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(165,2,'2026-10-18','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(166,2,'2026-10-18','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(167,5,'2026-10-18','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(168,5,'2026-10-18','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(169,3,'2026-10-18','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(170,3,'2026-10-18','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(171,1,'2026-10-19','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(172,1,'2026-10-19','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(173,4,'2026-10-19','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(174,4,'2026-10-19','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(175,2,'2026-10-19','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(176,2,'2026-10-19','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(177,5,'2026-10-19','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(178,5,'2026-10-19','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(179,3,'2026-10-19','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(180,3,'2026-10-19','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(181,1,'2026-10-20','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(182,1,'2026-10-20','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(183,4,'2026-10-20','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(184,4,'2026-10-20','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(185,2,'2026-10-20','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(186,2,'2026-10-20','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(187,5,'2026-10-20','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(188,5,'2026-10-20','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(189,3,'2026-10-20','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(190,3,'2026-10-20','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(191,1,'2026-10-21','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(192,1,'2026-10-21','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(193,4,'2026-10-21','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(194,4,'2026-10-21','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(195,2,'2026-10-21','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(196,2,'2026-10-21','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(197,5,'2026-10-21','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(198,5,'2026-10-21','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(199,3,'2026-10-21','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(200,3,'2026-10-21','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(201,1,'2026-10-22','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,1,'AVAILABLE'),(202,1,'2026-10-22','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,5,'AVAILABLE'),(203,4,'2026-10-22','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(204,4,'2026-10-22','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(205,2,'2026-10-22','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,1,'AVAILABLE'),(206,2,'2026-10-22','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(207,5,'2026-10-22','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(208,5,'2026-10-22','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(209,3,'2026-10-22','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(210,3,'2026-10-22','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(211,1,'2026-10-23','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(212,1,'2026-10-23','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(213,4,'2026-10-23','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(214,4,'2026-10-23','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(215,2,'2026-10-23','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(216,2,'2026-10-23','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(217,5,'2026-10-23','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(218,5,'2026-10-23','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(219,3,'2026-10-23','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(220,3,'2026-10-23','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(221,1,'2026-10-24','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(222,1,'2026-10-24','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(223,4,'2026-10-24','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(224,4,'2026-10-24','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(225,2,'2026-10-24','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(226,2,'2026-10-24','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(227,5,'2026-10-24','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(228,5,'2026-10-24','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(229,3,'2026-10-24','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(230,3,'2026-10-24','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(231,1,'2026-10-25','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(232,1,'2026-10-25','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(233,4,'2026-10-25','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(234,4,'2026-10-25','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(235,2,'2026-10-25','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(236,2,'2026-10-25','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(237,5,'2026-10-25','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(238,5,'2026-10-25','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(239,3,'2026-10-25','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(240,3,'2026-10-25','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(241,1,'2026-10-26','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(242,1,'2026-10-26','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(243,4,'2026-10-26','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(244,4,'2026-10-26','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(245,2,'2026-10-26','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(246,2,'2026-10-26','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(247,5,'2026-10-26','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(248,5,'2026-10-26','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(249,3,'2026-10-26','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(250,3,'2026-10-26','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(251,1,'2026-10-27','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(252,1,'2026-10-27','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(253,4,'2026-10-27','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(254,4,'2026-10-27','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(255,2,'2026-10-27','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(256,2,'2026-10-27','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(257,5,'2026-10-27','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(258,5,'2026-10-27','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(259,3,'2026-10-27','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(260,3,'2026-10-27','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(261,1,'2026-10-28','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(262,1,'2026-10-28','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(263,4,'2026-10-28','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(264,4,'2026-10-28','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(265,2,'2026-10-28','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(266,2,'2026-10-28','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(267,5,'2026-10-28','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(268,5,'2026-10-28','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(269,3,'2026-10-28','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(270,3,'2026-10-28','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(271,1,'2026-10-29','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(272,1,'2026-10-29','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(273,4,'2026-10-29','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(274,4,'2026-10-29','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(275,2,'2026-10-29','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(276,2,'2026-10-29','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(277,5,'2026-10-29','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(278,5,'2026-10-29','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(279,3,'2026-10-29','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(280,3,'2026-10-29','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(281,1,'2026-10-30','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(282,1,'2026-10-30','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(283,4,'2026-10-30','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(284,4,'2026-10-30','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(285,2,'2026-10-30','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(286,2,'2026-10-30','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(287,5,'2026-10-30','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(288,5,'2026-10-30','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(289,3,'2026-10-30','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(290,3,'2026-10-30','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(291,1,'2026-10-31','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(292,1,'2026-10-31','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(293,4,'2026-10-31','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(294,4,'2026-10-31','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(295,2,'2026-10-31','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(296,2,'2026-10-31','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(297,5,'2026-10-31','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(298,5,'2026-10-31','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE'),(299,3,'2026-10-31','07:00:00','11:30:00','Ca Sáng (07:00 - 11:30)',8,0,'AVAILABLE'),(300,3,'2026-10-31','13:30:00','17:30:00','Ca Chiều (13:30 - 17:30)',8,0,'AVAILABLE');
/*!40000 ALTER TABLE `doctor_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctors`
--

LOCK TABLES `doctors` WRITE;
/*!40000 ALTER TABLE `doctors` DISABLE KEYS */;
INSERT INTO `doctors` VALUES (1,2,1,'BS CKI. Nguyễn Minh Đức','Bác sĩ Chuyên khoa I',15,250000.00,'Từng công tác tại BV Đa khoa Tỉnh Nam Định. Chuyên gia hàng đầu về tim mạch, huyết áp và tim bẩm sinh.','assets/images/doctors/doctor-1.jpg','Phòng 201'),(2,3,5,'ThS.BS. Trần Thanh Hằng','Thạc sĩ Bác sĩ',12,200000.00,'Tốt nghiệp ĐH Y Hà Nội, chuyên gia siêu âm hình thái học thai nhi 5D và can thiệp sản phụ khoa ít xâm lấn.','assets/images/doctors/doctor-2.jpg','Phòng 105'),(3,4,7,'TS.BS. Lê Quang Huy','Tiến sĩ Y khoa',18,300000.00,'Tiến sĩ Y khoa, chuyên gia hàng đầu về Thận nhân tạo, lọc máu kỹ thuật cao và hồi sức nội khoa tại Nam Định.','assets/images/doctors/doctor-3.jpg','Phòng 301'),(4,5,4,'BS CKI. Phạm Ngọc Mai','Bác sĩ Chuyên khoa I',10,200000.00,'Bác sĩ tận tâm, giàu tình cảm, có nhiều năm kinh nghiệm cấp cứu và điều trị các bệnh lý hô hấp, tiêu hóa ở trẻ em.','assets/images/doctors/doctor-4.jpg','Phòng 102'),(5,6,6,'BS CKI. Đỗ Văn Thành','Bác sĩ Chuyên khoa I',14,200000.00,'Chuyên gia nội soi tai mũi họng dải tần hẹp (NBI), phát hiện sớm ung thư vòm họng và điều trị viêm xoang.','assets/images/doctors/doctor-5.jpg','Phòng 104');
/*!40000 ALTER TABLE `doctors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback`
--

LOCK TABLES `feedback` WRITE;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
INSERT INTO `feedback` VALUES (1,'Vũ Hoàng Long','long.vh@gmail.com','0912998877','Tư vấn khám sức khỏe tổng quát gia đình','Cho tôi hỏi phòng khám có gói khám sức khỏe định kỳ cho gia đình không và chi phí khoảng bao nhiêu?','Chào anh Long, An Nhiên có các gói khám tổng quát cơ bản và nâng cao cho gia đình với mức giá ưu đãi từ 1.200.000đ. Nhân viên CSKH sẽ liên hệ tư vấn chi tiết cho anh ạ.','REPLIED','2026-10-07 15:07:44'),(2,'Đặng Thị Mai','mai.dang@gmail.com','0988112233','Hỏi về chế độ BHYT tại phòng khám','Tôi có thẻ BHYT ở huyện Xuân Trường thì khi đến khám tại An Nhiên có được hưởng đúng tuyến không?','Dạ chào chị Mai, phòng khám An Nhiên thực hiện thông tuyến BHYT toàn quốc, chị sẽ được hưởng quyền lợi BHYT theo đúng quy định của Bộ Y tế ạ!','REPLIED','2026-10-07 15:07:44');
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,'Dấu hiệu cảnh báo bệnh lý Tim mạch nguy hiểm chớ nên xem nhẹ','dau-hieu-canh-bao-benh-ly-tim-mach-nguy-hiem','Bệnh lý tim mạch là nguyên nhân gây tử vong hàng đầu. Nhận biết sớm các triệu chứng giúp bảo vệ trái tim và tính mạng của bạn.','<p>Bệnh tim mạch thường diễn tiến âm thầm nhưng để lại hậu quả nghiêm trọng. Dưới đây là 5 dấu hiệu bạn cần thăm khám ngay:</p><ul><li><strong>Đau tức vùng ngực:</strong> Cảm giác bị đè nén, bóp nghẹt ở giữa ngực kéo dài trên 10 phút.</li><li><strong>Khó thở khi gắng sức:</strong> Hụt hơi ngay cả khi làm việc nhẹ hoặc nằm ngủ phải kê cao gối.</li><li><strong>Hoa mắt, chóng mặt:</strong> Dấu hiệu của tụt huyết áp hoặc rối loạn nhịp tim.</li><li><strong>Phù hai chi dưới:</strong> Mắt cá chân sưng to, ấn vào có vết lõm.</li></ul><p>Phòng khám Đa khoa An Nhiên Nam Định với đội ngũ bác sĩ chuyên khoa Tim mạch giàu kinh nghiệm cùng hệ thống điện tim, siêu âm tim thế hệ mới sẵn sàng đồng hành chăm sóc sức khỏe của bạn.</p>','BS CKI. Nguyễn Minh Đức','assets/images/posts/post-1.jpg','Tim Mạch',353,'2026-10-07 15:07:44'),(2,'Chăm sóc thai kỳ khoa học và các mốc siêu âm dị tật quan trọng','cham-soc-thai-ky-khoa-hoc-va-cac-moc-sieu-am-quan-trong','Siêu âm đúng thời điểm giúp phát hiện sớm các bất thường về hình thái và sự phát triển của thai nhi.','<p>Khám thai định kỳ là việc làm vô cùng quan trọng đối với mỗi mẹ bầu. Các mốc siêu âm không thể bỏ qua gồm:</p><ol><li><strong>Tuần 11 - 13 tuần 6 ngày:</strong> Đo độ mờ da gáy sàng lọc hội chứng Down, Patau, Edwards.</li><li><strong>Tuần 18 - 22:</strong> Siêu âm 5D khảo sát toàn bộ hình thái thai nhi (tim, não, cột sống, tay chân, khuôn mặt).</li><li><strong>Tuần 30 - 32:</strong> Đánh giá sự tăng trưởng, vị trí bánh rau, lượng nước ối và ngôi thai.</li></ol><p>Tại Khoa Sản Phụ Khoa An Nhiên Nam Định, công nghệ siêu âm Voluson 5D mang lại hình ảnh sắc nét, chân thực nhất cho mẹ và bé.</p>','ThS.BS. Trần Thanh Hằng','assets/images/posts/post-2.jpg','Sản Phụ Khoa',520,'2026-10-07 15:07:44'),(3,'Trung tâm Thận nhân tạo An Nhiên Nam Định - Điểm tựa cho bệnh nhân thận','trung-tam-than-nhan-tao-an-nhien-nam-dinh','Hợp tác chiến lược giữa VKIM Việt - Hàn mang đến công nghệ lọc máu siêu sạch, an toàn cho người dân tỉnh Nam Định.','<p>Trung tâm Thận nhân tạo tại Phòng khám Đa khoa An Nhiên (Mỹ Lộc, Nam Định) được đầu tư đồng bộ với 20 dàn máy lọc máu hiện đại, hệ thống xử lý nước RO đạt tiêu chuẩn quốc tế và màng lọc quả lọc tương thích sinh học cao.</p><p>Đặc biệt, phòng khám áp dụng chính sách Bảo hiểm Y tế (BHYT) thông tuyến, giúp người bệnh giảm gánh nặng chi phí điều trị mà vẫn được thụ hưởng dịch vụ y tế chất lượng cao.</p>','Ban Biên Tập An Nhiên','assets/images/posts/post-3.jpg','Thận Nhân Tạo',841,'2026-10-07 15:07:44'),(4,'Phương pháp giảm đau thoái hóa khớp gối không phẫu thuật','phuong-phap-giam-dau-thoai-hoa-khop-goi-khong-phau-thuat','Kết hợp điều trị nội khoa, tiêm chất nhờn nhân tạo Acid Hyaluronic và vật lý trị liệu phục hồi chức năng.','<p>Thoái hóa khớp gối gây đau đớn, hạn chế vận động và ảnh hưởng nghiêm trọng đến sinh hoạt của người trung niên và cao tuổi. Việc điều trị sớm giúp bảo tồn khớp và tránh nguy cơ phải thay khớp nhân tạo.</p><p>Các phương pháp hiệu quả hiện nay tại An Nhiên gồm: Liệu pháp huyết tương giàu tiểu cầu (PRP), tiêm chất nhờn nội khớp, kết hợp máy sóng xung kích Shockwave giảm đau nhanh chóng.</p>','TS.BS. Lê Quang Huy','assets/images/posts/post-4.jpg','Cơ Xương Khớp',290,'2026-10-07 15:07:44');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescriptions`
--

DROP TABLE IF EXISTS `prescriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prescriptions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `appointment_id` bigint NOT NULL,
  `diagnosis` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `medicine_list` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `doctor_notes` text COLLATE utf8mb4_unicode_ci,
  `re_examination_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `medicine_fee` decimal(10,2) DEFAULT '0.00' COMMENT 'Chi phí tiền thuốc',
  PRIMARY KEY (`id`),
  UNIQUE KEY `appointment_id` (`appointment_id`),
  CONSTRAINT `fk_prescription_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescriptions`
--

LOCK TABLES `prescriptions` WRITE;
/*!40000 ALTER TABLE `prescriptions` DISABLE KEYS */;
INSERT INTO `prescriptions` VALUES (1,1,'Tăng huyết áp độ 2 (JNC 7) - Theo dõi thiếu máu cơ tim cục bộ','[{\"name\":\"Amlodipine 5mg\",\"unit\":\"Viên\",\"quantity\":30,\"usage\":\"Uống 1 viên vào buổi sáng sau ăn\"},{\"name\":\"Concor 2.5mg (Bisoprolol)\",\"unit\":\"Viên\",\"quantity\":30,\"usage\":\"Uống 1 viên vào buổi sáng\"},{\"name\":\"Aspirin 81mg\",\"unit\":\"Viên\",\"quantity\":30,\"usage\":\"Uống 1 viên vào buổi tối sau ăn no\"}]','Ăn nhạt tuyệt đối, hạn chế mỡ động vật và cà phê. Đo huyết áp 2 lần/ngày sáng - tối ghi vào sổ tay.','2026-10-21','2026-10-07 15:07:44',180000.00),(2,6,'Bệnh thận mạn giai đoạn 4 (CKD G4) - Rối loạn chuyển hóa khoáng xương','[{\"name\":\"Ketosteril 600mg\",\"unit\":\"Viên\",\"quantity\":60,\"usage\":\"Uống 4 viên\\/ngày chia 2 lần trong bữa ăn\"},{\"name\":\"Calcitriol 0.25mcg\",\"unit\":\"Viên\",\"quantity\":30,\"usage\":\"Uống 1 viên\\/ngày vào buổi sáng\"},{\"name\":\"B Complex + Kẽm\",\"unit\":\"Ống\",\"quantity\":20,\"usage\":\"Uống 1 ống\\/ngày sau ăn trưa\"}]','Hạn chế đạm động vật, kiểm soát lượng nước uống hàng ngày = lượng nước tiểu + 500ml. Tránh dùng thuốc giảm đau NSAID.','2026-11-06','2026-10-07 15:07:44',320000.00),(3,14,'tăng huyết áp','[{\"name\":\"Ginkgo Biloba 120mg\",\"unit\":\"Viên\",\"quantity\":30,\"usage\":\"Uống 1 viên x 2 lần\\/ngày sau ăn\"},{\"name\":\"Augmentin 1g\",\"unit\":\"Viên\",\"quantity\":14,\"usage\":\"Uống 1 viên x 2 lần\\/ngày sau bữa ăn\"},{\"name\":\"Losartan 50mg\",\"unit\":\"Viên\",\"quantity\":30,\"usage\":\"Uống 1 viên vào 8h sáng\"}]','Ăn uống lành mạnh hơn','2026-10-29','2026-10-22 02:04:54',0.00);
/*!40000 ALTER TABLE `prescriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule_change_requests`
--

DROP TABLE IF EXISTS `schedule_change_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule_change_requests`
--

LOCK TABLES `schedule_change_requests` WRITE;
/*!40000 ALTER TABLE `schedule_change_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedule_change_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `specialties`
--

DROP TABLE IF EXISTS `specialties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `specialties` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `specialties`
--

LOCK TABLES `specialties` WRITE;
/*!40000 ALTER TABLE `specialties` DISABLE KEYS */;
INSERT INTO `specialties` VALUES (1,'Khoa Tim Mạch','Chuyên sâu khám, điều trị các bệnh lý tim mạch, huyết áp, rối loạn nhịp tim và bệnh mạch vành với máy móc hiện đại.','assets/images/specialties/tim-mach.jpg','fa-heart-pulse'),(2,'Khoa Cơ Xương Khớp','Điều trị thoái hóa cột sống, thoát vị đĩa đệm, đau khớp gối, viêm gân bằng phương pháp nội khoa và phục hồi chức năng.','assets/images/specialties/co-xuong-khop.jpg','fa-bone'),(3,'Khoa Tiêu Hóa - Gan Mật','Thực hiện nội soi tiêu hóa không đau độ phân giải cao, chẩn đoán sớm ung thư và điều trị bệnh lý gan mật tụy.','assets/images/specialties/tieu-hoa.jpg','fa-stethoscope'),(4,'Khoa Nhi','Khám chữa bệnh chuyên sâu và chăm sóc toàn diện cho trẻ sơ sinh và trẻ nhỏ trong không gian thân thiện, ấm áp.','assets/images/specialties/nhi-khoa.jpg','fa-baby'),(5,'Khoa Sản Phụ Khoa','Quản lý thai kỳ, siêu âm dị tật 5D, điều trị bệnh phụ khoa và tầm soát ung thư cổ tử cung hàng đầu Nam Định.','assets/images/specialties/san-phu-khoa.jpg','fa-female'),(6,'Khoa Tai Mũi Họng','Nội soi tầm soát ung thư vòm họng, điều trị viêm mũi dị ứng, viêm xoang mạn tính và viêm tai giữa bằng công nghệ mới.','assets/images/specialties/tai-mui-hong.jpg','fa-head-side-cough'),(7,'Trung Tâm Thận Nhân Tạo','Thế mạnh đặc biệt của An Nhiên liên kết Viện Y Dược Việt - Hàn (VKIM) với hệ thống máy lọc máu chuẩn quốc tế.','assets/images/specialties/than-nhan-tao.jpg','fa-droplet'),(8,'Chẩn Đoán Hình Ảnh & Xét Nghiệm','Hệ thống CT Scanner đa lát cắt, X-quang kỹ thuật số và xét nghiệm sinh hóa tự động trả kết quả nhanh chóng, chính xác.','assets/images/specialties/xet-nghiem.jpg','fa-x-ray');
/*!40000 ALTER TABLE `specialties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$mC3SPRaWgugkBBXGuNPrIORcLa3g8JRsLahvCRCpcB0iTTDu2v9WK','Ban Quản Trị Phòng Khám An Nhiên','admin@annhienclinic.vn','0944809221','ROLE_ADMIN',1,'2026-10-07 15:07:43'),(2,'bs_minhduc','$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O','BS CKI. Nguyễn Minh Đức','duc.nguyen@annhienclinic.vn','0912345678','ROLE_DOCTOR',1,'2026-10-07 15:07:43'),(3,'bs_thanhhang','$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O','ThS.BS. Trần Thanh Hằng','hang.tran@annhienclinic.vn','0923456789','ROLE_DOCTOR',1,'2026-10-07 15:07:43'),(4,'bs_quanghuy','$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O','TS.BS. Lê Quang Huy','huy.le@annhienclinic.vn','0934567890','ROLE_DOCTOR',1,'2026-10-07 15:07:43'),(5,'bs_ngocmai','$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O','BS CKI. Phạm Ngọc Mai','mai.pham@annhienclinic.vn','0945678901','ROLE_DOCTOR',1,'2026-10-07 15:07:43'),(6,'bs_vanthanh','$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O','BS CKI. Đỗ Văn Thành','thanh.do@annhienclinic.vn','0956789012','ROLE_DOCTOR',1,'2026-10-07 15:07:43'),(8,'benhnhan1','$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O','Trần Văn Bình','binh.tran@gmail.com','0988776655','ROLE_PATIENT',1,'2026-10-07 15:07:43'),(9,'benhnhan2','$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O','Nguyễn Thị Lan','lan.nguyen@gmail.com','0977665544','ROLE_PATIENT',1,'2026-10-07 15:07:43'),(10,'benhnhan3','$2y$10$y/5sIKu0MD0gQGhLP9PwiePnKocuVxYoehIbN5sbYGyxb700onH9O','Lê Tuấn Hùng','hung.le@gmail.com','0966554433','ROLE_PATIENT',1,'2026-10-07 15:07:43'),(11,'bichthanh0806','$2y$10$euRjq1OxgtP3LNw6ev7rfuMtHwFJtkTfJKwA5NAkOCMc4SV4i3jHe','Lưu Thị Bích Thanh','bichthanh@gamil.com','0987654356','ROLE_PATIENT',1,'2026-10-22 06:11:48');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-22 13:47:15
