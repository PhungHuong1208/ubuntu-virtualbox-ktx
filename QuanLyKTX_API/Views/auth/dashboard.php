<?php
// Kiểm tra đã login hay chưa (giả lập nếu chưa login)
$username = $_SESSION['username'] ?? 'Admin KTX';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRTS Dashboard - Quản Lý Ký Túc Xá</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/dashboard.css?v=<?= time() ?>">
</head>
<body>
    <div class="container-fluid">
        <div class="sidebar">
            <h2>PRTS // MENU</h2>
            <ul>
                <li><a href="<?= BASE_URL ?>auth/dashboard">🏠 Trang Chủ</a></li>
                <li><a href="<?= BASE_URL ?>student">👥 Quản Lý Sinh Viên</a></li>
                <li><a href="<?= BASE_URL ?>room">🛏️ Quản Lý Phòng</a></li>
                <li><a href="<?= BASE_URL ?>contract">📄 Quản Lý Hợp Đồng</a></li>
                <li><a href="<?= BASE_URL ?>payment">💰 Quản Lý Thanh Toán</a></li>
                <li><a href="<?= BASE_URL ?>utility">💳 Quản Lý Điện Nước</a></li>
                <li><a href="<?= BASE_URL ?>incident">⚠️ Quản Lý Sự Cố</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="header">
                <div>
                    <h1>Hệ Thống Quản Lý Ký Túc Xá</h1>
                </div>
                <div class="user-info">
                    <span>OPERATOR: <?= htmlspecialchars($username) ?></span>
                    <a href="<?= BASE_URL ?>auth/logout">LOGOUT</a>
                </div>
            </div>

            <div class="welcome-banner">
                <h2>> KẾT NỐI THÀNH CÔNG. CHÀO MỪNG QUẢN TRỊ VIÊN, <?= htmlspecialchars($username) ?>.</h2>
            </div>

            <div class="dashboard-grid">
                <a href="<?= BASE_URL ?>student" class="dashboard-card" data-module="STUDENT_DB">
                    <div class="icon">👥</div>
                    <h3>Sinh Viên</h3>
                    <p>Truy xuất & quản lý cơ sở dữ liệu hồ sơ cá nhân của sinh viên.</p>
                </a>

                <a href="<?= BASE_URL ?>room" class="dashboard-card" data-module="ROOM_CTRL">
                    <div class="icon">🛏️</div>
                    <h3>Phòng</h3>
                    <p>Giám sát không gian, phân bổ chỗ ở và kiểm tra tình trạng vật lý.</p>
                </a>

                <a href="<?= BASE_URL ?>contract" class="dashboard-card" data-module="AGREEMENT">
                    <div class="icon">📄</div>
                    <h3>Hợp Đồng</h3>
                    <p>Quản lý hiệu lực, ký kết và gia hạn hợp đồng lưu trú dài hạn.</p>
                </a>

                <a href="<?= BASE_URL ?>payment" class="dashboard-card" data-module="FINANCE">
                    <div class="icon">💰</div>
                    <h3>Thanh Toán</h3>
                    <p>Kiểm soát dòng tiền, hóa đơn lệ phí và trạng thái công nợ.</p>
                </a>

                <a href="<?= BASE_URL ?>utility" class="dashboard-card" data-module="ENERGY">
                    <div class="icon">💳</div>
                    <h3>Điện Nước</h3>
                    <p>Thống kê chỉ số tiêu thụ năng lượng và lập hóa đơn sinh hoạt.</p>
                </a>

                <a href="<?= BASE_URL ?>incident" class="dashboard-card" data-module="EMERGENCY">
                    <div class="icon">⚠️</div>
                    <h3>Sự Cố</h3>
                    <p>Hệ thống tiếp nhận, theo dõi và điều phối xử lý các tình huống phát sinh.</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>