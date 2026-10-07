# HỆ THỐNG WEB ĐĂNG KÝ VÀ QUẢN LÝ LỊCH KHÁM BỆNH PHÒNG KHÁM ĐA KHOA AN NHIÊN - NAM ĐỊNH

> **ĐỒ ÁN PHÁT TRIỂN PHẦN MỀM**  
> **KHOA CÔNG NGHỆ THÔNG TIN - TRƯỜNG ĐẠI HỌC CÔNG NGHỆ GIAO THÔNG VẬN TẢI (UTT)**  
> **Lớp:** 74DCTT26 | **Giảng viên hướng dẫn:** Cô Phạm Thị Thuận  
> **Nhóm 12 thực hiện (5 thành viên):**  
> 1. 32. Nguyễn Ngọc Mỹ  
> 2. 07. Nguyễn Hồng Đăng  
> 3. 49. Vũ Thị Minh Thư  
> 4. 57. Trần Thị Hồng Xoan  
> 5. 46. Lưu Thị Bích Thanh  

---

## 1. Công nghệ phát triển
Hệ thống được phát triển theo đúng chuẩn đề tài yêu cầu:
- **Frontend:** HTML5, CSS3 (Medical Responsive Design System), JavaScript (AJAX Wizard, Dynamic Slots, Validation, Print)
- **Backend:** PHP 8.2 (Session, PDO MySQL, Authentication, BCrypt Password Hash)
- **Cơ sở dữ liệu:** MySQL (Chuẩn hóa 3NF, Khóa ngoại InnoDB, Bộ mã UTF8MB4)
- **Biểu đồ & Thư viện:** Chart.js, FontAwesome 6, Google Fonts Inter

---

## 2. Thông tin Phòng khám Đa khoa An Nhiên
- **Địa chỉ:**
  - **Cơ sở 1:** Khu Bồi Tây, Xã Mỹ Phúc, Huyện Mỹ Lộc, Tỉnh Nam Định
  - **Cơ sở 2:** Đường Trần Tự Khánh, TP. Nam Định
- **Hotline:** 0944.809.221 | **Khoa Thận nhân tạo & Cấp cứu 24/7:** 0983.301.202
- **Thế mạnh:** Trung tâm Thận nhân tạo liên kết Viện Y Dược Việt - Hàn (VKIM), Sản phụ khoa siêu âm 5D, Tim mạch, Tai Mũi Họng, Nhi khoa.
- **Bảo hiểm y tế:** Tiếp nhận khám chữa bệnh BHYT thông tuyến toàn quốc.

---

## 3. Danh sách tài khoản kiểm thử (Demo Credentials)

| Vai trò | Tên đăng nhập | Mật khẩu | Chức năng chính |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin` | `admin123` | Quản lý toàn bộ lịch hẹn, phân ca trực bác sĩ, quản lý bác sĩ, chuyên khoa, phòng khám, tài khoản, bài viết, xem báo cáo KPI. |
| **Bác sĩ (Tim Mạch)** | `bs_minhduc` | `123456` | Xem bệnh nhân hẹn hôm nay, tiếp nhận vào khám, chẩn đoán, kê đơn thuốc điện tử, dặn dò, hẹn tái khám, xem lịch trực. |
| **Bác sĩ (Sản Phụ Khoa)** | `bs_thanhhang` | `123456` | Thăm khám thai kỳ, siêu âm 5D, kê đơn thuốc. |
| **Bác sĩ (Cơ Xương Khớp)** | `bs_quanghuy` | `123456` | Khám khớp cột sống, kê đơn, gửi đơn xin đổi ca trực. |
| **Bác sĩ (Nhi Khoa)** | `bs_ngocmai` | `123456` | Khám bệnh nhi, tiếp nhận bệnh nhân. |
| **Bác sĩ (Tai Mũi Họng)** | `bs_vanthanh` | `123456` | Khám nội soi tai mũi họng, kê đơn thuốc. |
| **Bác sĩ (Thận Nhân Tạo)** | `bs_kimwoo` | `123456` | Khám lọc máu VKIM, kê đơn thuốc. |
| **Bệnh nhân 1** | `benhnhan1` | `123456` | Xem lịch khám của tôi, đặt lịch mới, xem đơn thuốc sau khám, thanh toán VietQR. |
| **Bệnh nhân 2** | `benhnhan2` | `123456` | Quản lý tài khoản cá nhân, xem lịch sử. |

---

## 4. Hướng dẫn chạy và trải nghiệm Web

### Cách 1: Chạy nhanh 1-Click bằng file `run_server.bat` (Khuyên dùng)
- Nhấp đúp chuột vào file: `c:\xampp\htdocs\product_management\run_server.bat`
- Trình duyệt sẽ tự động mở trang web tại địa chỉ: **`http://localhost:8000`**

### Cách 2: Chạy qua XAMPP hoặc Laragon
- Bật Apache và MySQL trong bảng điều khiển XAMPP/Laragon.
- Mở trình duyệt và truy cập: **`http://localhost/product_management/`**

---

## 5. Cấu trúc thư mục dự án
```text
c:\xampp\htdocs\product_management\
├── index.php                 # Trang chủ phòng khám An Nhiên
├── chuyen-khoa.php           # Danh mục 8 chuyên khoa mũi nhọn
├── bac-si.php                # Đội ngũ y bác sĩ & bộ lọc chuyên khoa
├── phong-kham.php            # Cơ sở vật chất & buồng khám thực tế
├── dat-lich.php              # Quy trình 3 bước Đặt lịch trực tuyến (Wizard)
├── tra-cuu.php               # Tra cứu phiếu hẹn qua mã code & In đơn thuốc
├── cam-nang.php              # Danh sách cẩm nang sức khỏe y khoa
├── cam-nang-chi-tiet.php     # Chi tiết bài viết sức khỏe
├── lien-he.php               # Liên hệ Nam Định & Giới thiệu Nhóm 12 UTT
├── login.php                 # Đăng nhập đa vai trò (Admin / Doctor / Patient)
├── register.php              # Đăng ký tài khoản bệnh nhân mới
├── logout.php                # Đăng xuất tài khoản
├── run_server.bat            # File 1-click khởi chạy máy chủ PHP cục bộ
│
├── includes/
│   ├── db.php                # Kết nối PDO MySQL tương thích nhiều môi trường
│   ├── auth.php              # Xác thực và phân quyền 3 tác nhân
│   ├── functions.php         # Tiện ích format tiền tệ, mã phiếu hẹn, badge
│   ├── header.php / footer.php
│   ├── admin_header.php / admin_footer.php
│   ├── doctor_header.php / doctor_footer.php
│   └── patient_header.php / patient_footer.php
│
├── assets/
│   ├── css/style.css         # Hệ thống giao diện y tế hiện đại, responsive
│   ├── js/main.js            # Xử lý AJAX booking, filter bác sĩ, validation
│   └── images/
│       ├── logo.svg          # Logo vector SVG Phòng khám Đa khoa An Nhiên
│       ├── doctors/          # Ảnh vector bác sĩ chuyên khoa
│       ├── specialties/      # Ảnh chuyên khoa
│       └── posts/            # Ảnh bài viết sức khỏe
│
├── api/
│   ├── get_doctors_by_specialty.php  # API AJAX lấy bác sĩ theo chuyên khoa
│   └── get_schedules_by_doctor.php   # API AJAX lấy ca trực còn trống theo ngày
│
├── benh-nhan/                # Phân hệ Bệnh nhân (Patient Portal)
│   ├── index.php             # Danh sách lịch khám đã đặt & hủy lịch
│   ├── don-thuoc.php         # Xem danh mục đơn thuốc sau khám & in đơn
│   ├── thanh-toan.php        # Thanh toán VietQR & hóa đơn điện tử
│   └── ho-so.php             # Cập nhật thông tin cá nhân & mật khẩu
│
├── bac-si/                   # Phân hệ Bác sĩ (Doctor Portal)
│   ├── index.php             # Bàn khám: Bệnh nhân hẹn hôm nay & Tiếp nhận
│   ├── kham-benh.php         # Buồng khám: Chẩn đoán, kê đơn thuốc động, hẹn tái khám
│   ├── lich-truc.php         # Lịch trực cá nhân & Gửi đơn xin đổi/nghỉ ca
│   ├── ho-so-benh-nhan.php   # Tra cứu lịch sử bệnh án các lần khám trước
│   └── thong-tin-ca-nhan.php # Cập nhật SĐT, tiểu sử và đổi mật khẩu
│
├── admin/                    # Phân hệ Quản trị viên (Admin Portal)
│   ├── index.php             # Dashboard KPI & biểu đồ trực quan Chart.js
│   ├── lich-hen.php          # Quản lý & điều phối lịch hẹn, dời lịch khi bận
│   ├── lich-lam-viec.php     # Phân ca trực bác sĩ, duyệt đơn xin đổi ca
│   ├── bac-si.php            # Thêm, sửa, xóa bác sĩ (kiểm tra ràng buộc lịch hẹn)
│   ├── chuyen-khoa.php       # Quản lý chuyên khoa (kiểm tra ràng buộc bác sĩ)
│   ├── phong-kham.php        # Quản lý các buồng khám cơ sở vật chất
│   ├── tai-khoan.php         # Quản lý tài khoản, phân quyền, khóa/mở
│   ├── bai-viet.php          # Quản lý bài đăng cẩm nang y tế
│   ├── phan-hoi.php          # Hỗ trợ bệnh nhân & trả lời phản hồi
│   └── bao-cao.php           # Báo cáo thống kê hiệu suất & xuất dữ liệu
│
└── database/
    ├── phongkham_annhien.sql # Script cấu trúc bảng CSDL chuẩn 3NF
    └── seed_data.php         # Script nạp dữ liệu mẫu phong phú
```
