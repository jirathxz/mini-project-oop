@echo off
setlocal
title CARDVAULT Server
cd /d "%~dp0"

echo ========================================================
echo   CARDVAULT - Digital Key Marketplace
echo ========================================================
echo.

where php >nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo [ERROR] PHP is not found in PATH.
    echo Please configure your PHP environment variable or WAMP/XAMPP.
    echo.
    pause
    exit /b 1
)

echo [*] Starting PHP built-in server at http://localhost:8000
echo [*] Document root: %~dp0public
echo [*] Opening browser...
echo [*] Press Ctrl+C to stop the server.
echo.

start "" "http://localhost:8000"
php -S localhost:8000 -t "%~dp0public"
