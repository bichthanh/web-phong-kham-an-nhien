@echo off
chcp 65001 > nul
title PHÒNG KHÁM ĐA KHOA AN NHIÊN - NAM ĐỊNH (NHÓM 12 - UTT)
color 0A

echo ======================================================================
echo    HỆ THỐNG WEB ĐĂNG KÝ VÀ QUẢN LÝ LỊCH KHÁM BỆNH PHÒNG KHÁM AN NHIÊN
echo    ĐỒ ÁN PHÁT TRIỂN PHẦN MỀM - NHÓM 12 (74DCTT26) - UTT
echo    HTML5 + CSS3 + JAVASCRIPT + PHP 8.2 + MYSQL
echo ======================================================================
echo.

set PHP_EXE=D:\laragon\laragon\bin\php\php-8.2.20-Win32-vs16-x64\php.exe

if not exist "%PHP_EXE%" (
    where php >nul 2>nul
    if %errorlevel% equ 0 (
        set PHP_EXE=php
    ) else (
        echo [!] Không tìm thấy PHP tự động. Vui lòng bật Laragon/XAMPP!
        pause
        exit /b
    )
)

echo [*] Đang khởi chạy máy chủ thử nghiệm:
echo     - Truy cập trên máy này:       http://localhost:8000
echo     - Các bạn cùng Wi-Fi truy cập: http://192.168.1.14:8000
echo.
echo [*] Mở trình duyệt Web...
start http://localhost:8000

echo [*] Máy chủ đang hoạt động (Đang lắng nghe tất cả thiết bị trong Wi-Fi).
echo [*] Nhấn Ctrl + C để dừng máy chủ.
echo.
"%PHP_EXE%" -S 0.0.0.0:8000 -t "%~dp0"
pause
