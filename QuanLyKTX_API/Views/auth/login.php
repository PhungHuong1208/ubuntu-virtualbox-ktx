<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Quản Lý Ký Túc Xá</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/login.css?v=<?= time() ?>">
</head>
<body>
    <div class="login-container">
        <h1>🏠 Quản Lý Ký Túc Xá</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Form gửi dữ liệu dạng POST về AuthController@login -->
        <form method="POST" action="<?= BASE_URL ?>auth/login">
            <div class="form-group">
                <label for="username">Tên Đăng Nhập</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Nhập tên đăng nhập"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Mật Khẩu</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Nhập mật khẩu"
                    required
                >
            </div>

            <button type="submit" class="btn-login">Đăng Nhập</button>
        </form>

        <div class="login-footer">
            <p>Bạn là Sinh viên? <a href="http://192.168.190.128:8080/QuanLyKTX_user/">Đăng nhập Cổng Sinh Viên</a></p>
        </div>
    </div>
</body>
</html>
