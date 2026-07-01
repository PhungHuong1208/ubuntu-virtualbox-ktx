<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng Sinh Viên - KTX</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>Public/css/login.css?v=<?= time() ?>">
</head>
<body>
    <div class="login-box">
        <h2>🎓 CỔNG SINH VIÊN</h2>
        <p style="color: #78909c; font-size: 14px; margin-bottom: 20px;">Đăng nhập để xem thông tin KTX</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert">⚠️ <?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>auth/login">
            <div class="form-group">
                <label>Mã Số Sinh Viên</label>
                <input type="text" name="masv" placeholder="Ví dụ: 74DCTT001" required>
            </div>
            <div class="form-group">
                <label>Mật Khẩu</label>
                <input type="password" name="password" placeholder="Nhập mật khẩu của bạn..." required>
            </div>
            <button class="btn-submit" type="submit">Đăng Nhập</button>
        </form>
        
        <div class="footer-link">
            Bạn là Quản trị viên (Admin)? <br>
            <a href="http://192.168.190.128:8080/QuanLyKTX_API/Public/auth/login">Đăng nhập tại Cổng Quản Lý</a>
        </div>
    </div>
</body>
</html>
