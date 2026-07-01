<?php
if (!defined('BASE_URL')) { define('BASE_URL', 'http://192.168.190.128:8080/QuanLyKTX_API/Public/'); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập Dữ Liệu Thanh Toán</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/import.css?v=<?= time() ?>">
</head>
<body>
    <div class="import-container">
        <h2> 📤  Nhập Dữ Liệu Thanh Toán</h2>

        <?php if (!empty($msg['text'])): ?>
            <div class="alert alert-<?= (isset($msg['type']) && $msg['type'] === 'success') ? 'success' : 'error' ?>">
                <?= htmlspecialchars($msg['text']) ?>
            </div>
        <?php endif; ?>

        <div class="instructions">
            <h4> 📋  Hướng Dẫn Định Dạng:</h4>
            <ul>
                <li>File CSV phải có các cột theo thứ tự: <strong>Mã TT, Mã SV, Số Tiền, Ngày Trả, Trạng Thái</strong></li>
                <li>Các cột phân cách bằng dấu <strong>;</strong> (chấm phẩy)</li>
                <li>Dòng đầu tiên là tiêu đề sẽ tự động bị bỏ qua.</li>
            </ul>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Chọn File CSV:</label>
                <input type="file" name="file" accept=".csv" required>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary"> 📤  Nhập Dữ Liệu</button>
                <a href="<?= BASE_URL ?>payment" class="btn btn-secondary">← Quay Lại</a>
            </div>
        </form>
    </div>
</body>
</html>