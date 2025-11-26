#!/bin/bash
# Script compile SCSS sang CSS
# Yêu cầu: Cài đặt Dart Sass hoặc Node Sass

echo "Compiling SCSS to CSS..."

# Kiểm tra xem có sass CLI không
if ! command -v sass &> /dev/null; then
    echo "ERROR: Sass CLI chưa được cài đặt!"
    echo ""
    echo "Cách cài đặt:"
    echo "1. Cài Dart Sass: https://sass-lang.com/install"
    echo "2. Hoặc cài Node.js và chạy: npm install -g sass"
    exit 1
fi

# Compile SCSS sang CSS
sass assets/scss/main.scss:assets/css/main.css --style=expanded --source-map

if [ $? -eq 0 ]; then
    echo ""
    echo "SUCCESS: SCSS đã được compile thành công!"
    echo "Output: assets/css/main.css"
else
    echo ""
    echo "ERROR: Có lỗi xảy ra khi compile SCSS!"
    exit 1
fi



