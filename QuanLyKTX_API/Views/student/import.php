<?php
/**
 * Student Import View
 */

// Kiểm tra phiên đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit;
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/webktx/QuanLyKTX_API/Public/');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập Dữ Liệu Sinh Viên</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/import.css?v=<?= time() ?>">
</head>
<body>
    <div class="import-container">
        <h2>📤 Nhập Dữ Liệu Sinh Viên</h2>

        <?php if (isset($msg) && is_array($msg) && !empty($msg['text'])): ?>
            <div class="alert alert-<?= $msg['type'] === 'success' ? 'success' : 'error' ?>">
                <?= htmlspecialchars($msg['text']) ?>
            </div>
        <?php endif; ?>

        <div class="instructions">
            <h4>📋 Hướng Dẫn:</h4>
            <ul>
                <li>Chỉ hỗ trợ file <strong>.csv</strong> (Comma Separated Values)</li>
                <li>Kích thước file tối đa: <strong>5MB</strong></li>
                <li>Dòng đầu tiên phải là: <strong>Mã SV, Họ Tên, Lớp, Giới Tính, CCCD, Điện Thoại, Email, Địa Chỉ</strong></li>
                <li>Không được để trống Mã SV và Họ Tên</li>
                <li>Các bản ghi có Mã SV trùng sẽ bị bỏ qua</li>
            </ul>
        </div>

        <div class="csv-template">
            <strong>📝 Mẫu CSV:</strong><br>
            Mã SV,Họ Tên,Lớp,Giới Tính,CCCD,Điện Thoại,Email,Địa Chỉ<br>
            SV001,Nguyễn Văn A,DA01,Nam,012345678,0987654321,nguyenvana@example.com,123 Đường A<br>
            SV002,Trần Thị B,DA02,Nữ,012345679,0987654322,tranthib@example.com,456 Đường B
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Chọn file CSV:</label>
                <input type="file" id="file" name="file" accept=".csv,.xlsx,.xls" required>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">📤 Nhập Dữ Liệu</button>
                <a href="<?= BASE_URL ?>student" class="btn btn-secondary">← Quay Lại</a>
            </div>
        </form>
    </div>
</body>
</html>
