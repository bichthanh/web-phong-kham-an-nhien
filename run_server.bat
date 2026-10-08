@echo off
title PHONG KHAM DA KHOA AN NHIEN - NHOM 12
color 0A

echo ======================================================================
echo    HE THONG PHONG KHAM DA KHOA AN NHIEN - NAM DINH (NHOM 12)
echo    DANG KHOI DONG MAY CHU WEB TAI: http://localhost:8000
echo ======================================================================
echo.

set "PHP_EXE=D:\laragon\laragon\bin\php\php-8.2.20-Win32-vs16-x64\php.exe"
if not exist "%PHP_EXE%" (
    where php >nul 2>nul
    if %errorlevel% equ 0 (
        set "PHP_EXE=php"
    ) else (
        echo [LOI] Khong tim thay PHP! Vui long bat Laragon hoac XAMPP!
        pause
        exit /b 1
    )
)

echo [*] Dang kiem tra va don dep cong 8000...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8000 ^| findstr LISTENING') do (
    taskkill /F /PID %%a >nul 2>nul
)

echo [*] Dang tu dong mo trinh duyet...
start http://localhost:8000

echo.
echo ======================================================================
echo    MAY CHU DANG HOAT DONG!
echo    - Truy cap tren may: http://localhost:8000
echo    - LUU Y: GIU NGUYEN CUA SO NAY DE DUNG WEB!
echo ======================================================================
echo.

"%PHP_EXE%" -S 0.0.0.0:8000 -t "%~dp0"
if %errorlevel% neq 0 pause