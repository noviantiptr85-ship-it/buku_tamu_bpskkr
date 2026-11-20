@echo off
cd /d "%~dp0"
echo Menjalankan Buku Tamu di http://localhost:8000 ...
echo Tekan Ctrl+C untuk berhenti.
"C:\xampp\php\php.exe" -S localhost:8000 -t public public/index.php
