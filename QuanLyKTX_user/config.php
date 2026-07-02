<?php
// config.php


// Giữ nguyên phần API_URL đã có
if (!defined('API_URL')) {
    // Gọi API nội bộ trong Docker container:
    // - Apache trong container chạy port 80 (8080 chỉ là port bên ngoài VM)
    // - Volume mount: .:/var/www/html => không có prefix /ubuntu-ktx/
    define('API_URL', 'http://localhost/QuanLyKTX_API/Routes/apiUser.php');
}
?>