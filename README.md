# HỆ THỐNG WEB ĐĂNG KÝ & QUẢN LÝ LỊCH KHÁM BỆNH PHÒNG KHÁM ĐA KHOA AN NHIÊN (NAM ĐỊNH)

> **TRƯỜNG ĐẠI HỌC CÔNG NGHỆ GIAO THÔNG VẬN TẢI (UTT)**  
> **Khoa:** Công nghệ Thông tin  
> **Học phần:** Đồ án Phát triển Phần mềm  
> **Giảng viên hướng dẫn:** Cô Phạm Thị Thuận  
> **Nhóm thực hiện:** Nhóm 12 • Lớp 74DCTT26  

---

## 👥 THÀNH VIÊN NHÓM 12

| STT | Họ và Tên | Vai trò thực hiện |
| :---: | :--- | :--- |
| **32** | **Nguyễn Ngọc Mỹ** | Lập trình viên Fullstack, kiến trúc backend & API |
| **07** | **Nguyễn Hồng Đăng** | Thiết kế hệ thống, phân tích cơ sở dữ liệu & biểu đồ UML |
| **49** | **Vũ Thị Minh Thư** | Thiết kế giao diện (UI/UX), CSS Design System |
| **57** | **Trần Thị Hồng Xoan** | Kiểm thử chức năng, thiết kế cơ sở dữ liệu MySQL |
| **46** | **Lưu Thị Bích Thanh** | Kiểm thử hệ thống, tài liệu báo cáo đồ án |

---

## 🏥 TỔNG QUAN HỆ THỐNG

Dự án xây dựng nền tảng website phục vụ hoạt động quản lý khám chữa bệnh trực tuyến tại **Phòng khám Đa khoa An Nhiên (Nam Định)** thuộc hệ sinh thái Viện Nghiên cứu & Đào tạo Y Dược Việt – Hàn (VKIM).

### 🌟 Tính năng nổi bật theo 4 phân hệ:
1. **Dành cho Khách vãng lai & Bệnh nhân:**
   - Xem thông tin phòng khám, cơ sở vật chất, 8 chuyên khoa kỹ thuật cao.
   - Danh sách và hồ sơ đội ngũ bác sĩ chuyên khoa Việt Nam.
   - **Quy trình đặt lịch trực tuyến 3 bước:** Chọn Chuyên khoa $\rightarrow$ Bác sĩ $\rightarrow$ Khung giờ trống (AJAX thời gian thực) $\rightarrow$ Cấp mã phiếu hẹn duy nhất dạng `AN-2026-XXXX`.
   - **Tra cứu lịch khám & xem Đơn thuốc điện tử:** Tra cứu qua mã phiếu hẹn hoặc số điện thoại, in phiếu khám và đơn thuốc trực tiếp từ trình duyệt (`window.print()`).
   - Cẩm nang y tế & tin tức sức khỏe.
   - Gửi phản hồi / liên hệ trực tuyến.
2. **Cổng Dịch vụ Bệnh nhân (`/benh-nhan`):**
   - Đăng ký / Đăng nhập tài khoản bệnh nhân.
   - Theo dõi lịch sử khám bệnh, trạng thái ca khám.
   - Quản lý kho đơn thuốc điện tử, dặn dò của bác sĩ và ngày tái khám.
   - Lịch sử hóa đơn viện phí & thanh toán.
3. **Bàn khám Bác sĩ (`/bac-si`):**
   - Danh sách bệnh nhân chờ khám trong ngày.
   - Tiếp đón, nhập chẩn đoán lâm sàng.
   - **Kê đơn thuốc điện tử:** Chọn tên thuốc, đơn vị, số lượng, hướng dẫn uống, lời dặn bác sĩ, hẹn ngày tái khám.
   - Xem lịch trực cá nhân trong tuần & gửi yêu cầu xin nghỉ / đổi ca.
4. **Hệ thống Quản trị Admin (`/admin`):**
   - Bảng điều khiển (Dashboard) KPI: tổng bệnh nhân, lịch hẹn hôm nay, doanh thu, biểu đồ phân bổ chuyên khoa (Chart.js).
   - Duyệt và quản lý lịch hẹn khám, hỗ trợ dời lịch khám cho bệnh nhân.
   - Quản lý phân ca trực bác sĩ (xếp lịch, bật/tắt ca trực).
   - Quản lý danh mục bác sĩ, chuyên khoa, phòng khám, bài viết, phản hồi khách hàng.
   - Báo cáo thống kê doanh thu và lượt khám theo khoảng thời gian.

---

## 🛠️ CÔNG NGHỆ SỬ DỤNG

- **Frontend:** HTML5, CSS3 (Modern Medical UI Design System, Flexbox, CSS Grid), JavaScript (Vanilla JS, AJAX Fetch API), Font Awesome 6, Google Fonts (Plus Jakarta Sans, Inter).
- **Backend:** PHP 8.2 (Mô hình MVC linh hoạt, Prepared Statements PDO, mã hóa bcrypt).
- **Cơ sở dữ liệu:** MySQL (10 bảng dữ liệu quan hệ, ràng buộc khóa ngoại, hỗ trợ UTF8MB4).
- **Bảo mật:** Session Authentication, CSRF & XSS Sanitization, SQL Injection Prevention bằng PDO.

---

## 🚀 HƯỚNG DẪN KHỞI CHẠY (1-CLICK)

### Cách 1: Khởi chạy nhanh bằng file tự động (Khuyên dùng)
1. Đảm bảo MySQL đang chạy (trên cổng 3306, user `root`).
2. Nhấp đúp chuột vào file: **`run_server.bat`**.
3. Trình duyệt sẽ tự động mở trang web tại địa chỉ: **`http://localhost:8000`**.

### Cách 2: Khởi chạy bằng XAMPP / Laragon
1. Đặt thư mục dự án vào `c:\xampp\htdocs\product_management` (hoặc thư mục www của Laragon).
2. Mở trình duyệt truy cập: **`http://localhost/product_management`**.

### Cách 3: Chia sẻ đường link Online cho bạn bè xem từ xa
- Giữ file `run_server.bat` đang chạy.
- Nhấp đúp file: **`chia_se_link_online.bat`** (cửa sổ sẽ tự động sinh link online miễn phí `https://xxxx.trycloudflare.com` gửi cho bạn bè xem trên điện thoại hoặc máy tính ở bất kỳ đâu).

---

## 🔑 TÀI KHOẢN TRUY CẬP DEMO

| Vai trò | Tên đăng nhập | Mật khẩu | Ghi chú |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin` | `admin123` | Toàn quyền quản trị hệ thống |
| **Bác sĩ chuyên khoa** | `bs_minhduc` | `123456` | BS CKI. Nguyễn Minh Đức (Tim mạch) |
| **Bác sĩ chuyên khoa** | `bs_quanghuy` | `123456` | TS.BS. Lê Quang Huy (Thận nhân tạo) |
| **Bác sĩ chuyên khoa** | `bs_thanhhang` | `123456` | ThS.BS. Trần Thanh Hằng (Sản khoa) |
| **Bệnh nhân** | `benhnhan1` | `123456` | Tài khoản bệnh nhân mẫu |

- **Mã phiếu hẹn tra cứu nhanh:** `AN-2026-001` (Đã hoàn thành khám, có đơn thuốc điện tử).

---

## 📁 CẤU TRÚC THƯ MỤC DỰ ÁN

```
product_management/
├── admin/                      # Phân hệ quản trị viên
│   ├── index.php               # Dashboard KPI điều hành
│   ├── lich-hen.php            # Quản lý & duyệt lịch hẹn
│   ├── lich-lam-viec.php       # Phân ca trực bác sĩ
│   ├── bac-si.php              # Quản lý bác sĩ
│   ├── chuyen-khoa.php         # Quản lý chuyên khoa
│   └── ...
├── bac-si/                     # Phân hệ Bác sĩ
│   ├── index.php               # Hàng chờ bệnh nhân
│   ├── kham-benh.php           # Thăm khám & kê đơn thuốc điện tử
│   ├── lich-truc.php           # Xem ca trực & xin đổi ca
│   └── ...
├── benh-nhan/                  # Cổng thông tin Bệnh nhân
│   ├── index.php               # Lịch sử khám bệnh
│   ├── don-thuoc.php           # Danh sách đơn thuốc điện tử
│   └── ...
├── api/                        # API Endpoint AJAX
│   ├── get_doctors_by_specialty.php
│   └── get_schedules_by_doctor.php
├── assets/
│   ├── css/style.css           # Toàn bộ CSS giao diện y tế cao cấp
│   ├── js/main.js              # JavaScript tương tác & AJAX
│   └── images/                 # Logo, ảnh bác sĩ Việt Nam, chuyên khoa
├── database/
│   ├── phongkham_annhien.sql   # File export CSDL MySQL hoàn chỉnh
│   └── seed_data.php           # Script gieo dữ liệu mẫu tự động
├── includes/                   # Các file dùng chung (db, header, footer, auth)
├── index.php                   # Trang chủ phòng khám
├── dat-lich.php                # Wizard đặt lịch khám trực tuyến 3 bước
├── tra-cuu.php                 # Tra cứu phiếu hẹn & đơn thuốc
├── run_server.bat              # Script chạy server 1-click (hỗ trợ LAN Wi-Fi)
├── chia_se_link_online.bat     # Script chia sẻ link online toàn quốc
└── README.md                   # Hướng dẫn đồ án Nhóm 12 (UTT)
```

---
© 2026 **Nhóm 12 - Lớp 74DCTT26 - Trường Đại Học Công Nghệ Giao Thông Vận Tải (UTT)**.
Mã nguồn phục vụ đồ án môn học.
