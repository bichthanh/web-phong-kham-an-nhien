@echo off
chcp 65001 > nul
title CHIA SẺ LINK TRUY CẬP ONLINE - PHÒNG KHÁM AN NHIÊN (NHÓM 12)
color 0B

echo ======================================================================
echo    TẠO ĐƯỜNG LINK ONLINE CÔNG KHAI CHO BẠN BÈ / GIẢNG VIÊN TRUY CẬP
echo    HỆ THỐNG PHÒNG KHÁM ĐA KHOA AN NHIÊN - NAM ĐỊNH (NHÓM 12)
echo ======================================================================
echo.
echo [*] LƯU Ý QUAN TRỌNG:
echo     1. Hãy giữ cửa sổ chạy web (run_server.bat) luôn mở!
echo     2. Cửa sổ này sẽ sinh ra 1 đường link online miễn phí có dạng:
echo        https://xxxxxx.trycloudflare.com
echo     3. Bạn chỉ cần copy link đó gửi qua Zalo/Facebook cho bạn bè xem!
echo.
echo [*] Đang khởi tạo đường truyền bảo mật Cloudflare Tunnel...
echo ======================================================================
echo.

"%~dp0cloudflared.exe" tunnel --url http://127.0.0.1:8000
pause
