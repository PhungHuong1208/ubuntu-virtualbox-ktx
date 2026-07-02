<?php
// config.php

// Thêm điều kiện kiểm tra tồn tại cho BASE_URL
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost:8080/QuanLyKTX_user/');
}

// Giữ nguyên phần API_URL đã có
if (!defined('API_URL')) {
    define('API_URL', 'http://localhost:8080/QuanLyKTX_API/Routes/apiUser.php');
}
?>