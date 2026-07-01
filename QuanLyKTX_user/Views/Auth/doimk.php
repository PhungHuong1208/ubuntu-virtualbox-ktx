<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi Mật Khẩu - Cổng Sinh Viên</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>Public/css/doimk.css?v=<?= time() ?>">
</head>
<body>
    
    <div class="login-box">
        <h2>🔒 Đổi Mật Khẩu</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert-success">
                ✅ <?= $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert-error">
                ⚠️ <?= $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>auth/updatePassword" method="POST">
            <div class="form-group">
                <label>Mật khẩu cũ</label>
                <input type="password" name="old_password" placeholder="Nhập mật khẩu hiện tại" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu mới</label>
                <input type="password" name="new_password" placeholder="Nhập mật khẩu mới" required>
            </div>
            <div class="form-group">
                <label>Nhập lại mật khẩu mới</label>
                <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu mới" required>
            </div>
            <button class="btn-submit" type="submit">Cập Nhật</button>
        </form>

        <div class="form-header">
            <a href="<?= BASE_URL ?>dashboard" class="back-link">← Quay lại trang chủ</a>
        </div>
    </div>
</body>
</html>