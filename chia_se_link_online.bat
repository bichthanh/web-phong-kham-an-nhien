@echo off
title CHIA SE LINK ONLINE - PHONG KHAM AN NHIEN (NHOM 12)
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0share_link.ps1"
if %errorlevel% neq 0 pause