<?php
/**
 * QuanLyKTX - Trang Chủ (Index)
 * Xác định hiển thị login hay dashboard
 */

session_name('USER_KTX_STUDENT');
session_start();

// Định nghĩa base path
define('BASE_PATH', __DIR__);
// BASE_URL tự động theo môi trường (XAMPP hay Docker đều đúng)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$dir      = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';
define('BASE_URL', $protocol . '://' . $host . $dir);

// Không gọi CSDL nữa (Đã chuyển sang API)
// require_once BASE_PATH . '/Config/Database.php';

// Load Core Controller
require_once BASE_PATH . '/Core/Controller.php';

// KHÔNG CẦN TẢI Core/Model.php nữa vì các API model không dùng SQL
// require_once BASE_PATH . '/Core/Model.php';

// Gọi Router để xử lý request
require_once BASE_PATH . '/UserRouter.php';
?>