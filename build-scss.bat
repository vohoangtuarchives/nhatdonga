@echo off
REM Script compile SCSS sang CSS
REM Yêu cầu: Cài đặt Dart Sass hoặc Node Sass

echo Compiling SCSS to CSS...

REM Kiểm tra xem có sass CLI không
where sass >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Sass CLI chua duoc cai dat!
    echo.
    echo Cach cai dat:
    echo 1. Cai Dart Sass: https://sass-lang.com/install
    echo 2. Hoac cai Node.js va chay: npm install -g sass
    echo.
    pause
    exit /b 1
)

REM Compile SCSS sang CSS
sass assets/scss/main.scss:assets/css/main.css --style=expanded --source-map

if %errorlevel% equ 0 (
    echo.
    echo SUCCESS: SCSS da duoc compile thanh cong!
    echo Output: assets/css/main.css
) else (
    echo.
    echo ERROR: Co loi xay ra khi compile SCSS!
)

pause




