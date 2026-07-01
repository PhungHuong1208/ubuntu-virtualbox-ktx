<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập Dữ Liệu Sự Cố</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/import.css?v=<?= time() ?>">
</head>
<body>
    <div class="import-container">
        <h2> 📤  Nhập Dữ Liệu Sự Cố</h2>

        <?php if (isset($message) && !empty($message)): ?>
            <div class="alert alert-<?= (strpos($message_type, 'success') !== false) ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="instructions">
            <h4> 📋  Hướng Dẫn Định Dạng:</h4>
            <ul>
                <li>File CSV phải có các cột theo thứ tự: <strong>Mã SC, Mã Phòng, Mô Tả, Ngày Báo Cáo, Trạng Thái</strong></li>
                <li>Các cột phân cách bằng dấu <strong>;</strong> (chấm phẩy)</li>
            </ul>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Chọn File CSV:</label>
                <input type="file" name="file" accept=".csv" required>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary"> 📤  Nhập Dữ Liệu</button>
                <a href="<?= BASE_URL ?>incident" class="btn btn-secondary">← Quay Lại</a>
            </div>
        </form>
    </div>
</body>
</html>