@echo off
setlocal
title CARDVAULT Test Runner
cd /d "%~dp0"

echo ========================================================
echo   CARDVAULT - Running All Tests
echo ========================================================
echo.

php tests\DatabaseTest.php
if %ERRORLEVEL% neq 0 goto fail

php tests\CatalogTest.php
if %ERRORLEVEL% neq 0 goto fail

php tests\CheckoutTest.php
if %ERRORLEVEL% neq 0 goto fail

php tests\PaymentTest.php
if %ERRORLEVEL% neq 0 goto fail

php tests\TrackingOtpTest.php
if %ERRORLEVEL% neq 0 goto fail

php tests\AdminTest.php
if %ERRORLEVEL% neq 0 goto fail

php tests\OopArchitectureTest.php
if %ERRORLEVEL% neq 0 goto fail

echo.
echo ========================================================
echo   [SUCCESS] ALL TESTS PASSED (100%%)
echo ========================================================
echo.
pause
exit /b 0

:fail
echo.
echo ========================================================
echo   [FAILED] Test suite failed!
echo ========================================================
echo.
pause
exit /b 1
