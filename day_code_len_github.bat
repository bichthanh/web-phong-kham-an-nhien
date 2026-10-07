@echo off
chcp 65001 > nul
title ĐẨY CODE LÊN GITHUB - PHÒNG KHÁM AN NHIÊN (NHÓM 12)
color 0A

echo ======================================================================
echo    HƯỚNG DẪN ĐẨY SOURCE CODE LÊN GITHUB CHO CẢ NHÓM 12 CÙNG XEM
echo ======================================================================
echo.
echo Bước 1: Vào trang https://github.com và đăng nhập tài khoản.
echo Bước 2: Bấm vào dấu [+] góc trên bên phải -> Chọn "New repository".
echo Bước 3: Đặt tên Repo (ví dụ: phongkham-annhien-nhom12).
echo         Để chế độ Public (hoặc Private), KHÔNG tích chọn tạo README.
echo         Sau đó bấm nút "Create repository".
echo Bước 4: Sao chép đường link HTTPS của repository vừa tạo
echo         (Có dạng: https://github.com/ten_ban/phongkham-annhien-nhom12.git).
echo.
set /p REPO_URL="Dán đường link GitHub vào đây rồi nhấn Enter: "

if "%REPO_URL%"=="" (
    echo [!] Bạn chưa nhập link. Huỷ thao tác.
    pause
    exit /b
)

echo.
echo [*] Đang thiết lập branch main và tải mã nguồn lên GitHub...
git branch -M main
git remote remove origin 2>nul
git remote add origin %REPO_URL%
git push -u origin main

echo.
echo ======================================================================
echo  THÀNH CÔNG! Mã nguồn đã được đưa lên GitHub.
echo  Bạn hãy gửi link GitHub này cho các bạn trong nhóm xem code nhé!
echo ======================================================================
pause
