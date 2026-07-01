<?php
/**
 * Auth - Dashboard View
 */

// Kiểm tra đã login hay chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . (defined('BASE_URL') ? BASE_URL : 'http://192.168.190.128:8080/QuanLyKTX_user/') . 'index.php');
    exit;
}

// Define BASE_URL nếu chưa được định nghĩa
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://192.168.190.128:8080/QuanLyKTX_user/');
}

$hoten = $_SESSION['hoten'];
$base_url = BASE_URL;
?>
<!DOCTYPE html>
<html lang="vi"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinh viên Ký Túc Xá</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>Public/css/dashboard.css?v=<?= time() ?>">
</head>
<body>
    <div class="container-fluid">
        <div class="sidebar">
            <h2>🎓 CỔNG KTX</h2>
            <ul>
                <li><a href="<?= BASE_URL ?>auth/dashboard">🏠 Trang Chủ</a></li>
                <li><a href="<?= BASE_URL ?>student">👥 Thông tin Sinh Viên</a></li>
                <li><a href="<?= BASE_URL ?>room">🛏️ Xem Phòng KTX</a></li>
                <li><a href="<?= BASE_URL ?>contract">📄 Xem Hợp Đồng</a></li>
                <li><a href="<?= BASE_URL ?>incident">⚠️ Báo Cáo Sự Cố</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="header">
                <div>
                    <h1>Cổng Thông Tin Ký Túc Xá</h1>
                </div>
                <div class="user-info">
                    <span>👤 <?= htmlspecialchars($hoten) ?></span>
                    <a href="<?= BASE_URL ?>auth/doimk">✍️ Đổi mật khẩu</a>
                    <a href="<?= BASE_URL ?>auth/logout">Đăng Xuất</a>
                </div>
            </div>

            <div class="welcome-banner">
                <h2>👋 Xin chào, <?= htmlspecialchars($hoten) ?>!</h2>
                <p>Chào mừng bạn quay lại hệ thống quản lý thông tin lưu trú sinh viên.</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                ⚠️ <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                ✅ <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); endif; ?>

            <div class="dashboard-grid">
                <a href="<?= BASE_URL ?>student" class="dashboard-card">
                    <div class="icon">👥</div>
                    <h3>Hồ Sơ Của Tôi</h3>
                    <p>Xem thông tin cá nhân</p>
                </a>

                <a href="<?= BASE_URL ?>room" class="dashboard-card">
                    <div class="icon">🛏️</div>
                    <h3>Phòng Ở</h3>
                    <p>Tra cứu thông tin phòng</p>
                </a>

                <a href="<?= BASE_URL ?>contract" class="dashboard-card">
                    <div class="icon">📄</div>
                    <h3>Hợp Đồng</h3>
                    <p>Trạng thái hợp đồng lưu trú</p>
                </a>

                <a href="<?= BASE_URL ?>incident" class="dashboard-card">
                    <div class="icon">⚠️</div>
                    <h3>Sự Cố</h3>
                    <p>Gửi yêu cầu hỗ trợ sửa chữa</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>