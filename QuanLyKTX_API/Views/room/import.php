<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập Dữ Liệu Phòng</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/import.css?v=<?= time() ?>">
</head>
<body>
    <div class="import-container">
        <h2>📤 Nhập Dữ Liệu Phòng</h2>

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
                <li>Dòng đầu tiên phải là: <strong>Mã Phòng, Số Phòng, Tòa, Sức Chứa, Phòng Hiện Tại, Giá, Trạng Thái</strong></li>
                <li>Không được để trống Mã Phòng và Số Phòng</li>
                <li>Các bản ghi có Mã Phòng trùng sẽ bị bỏ qua</li>
            </ul>
        </div>

        <div class="csv-template">
            <strong>📝 Mẫu CSV:</strong><br>
            Mã Phòng,Số Phòng,Tòa,Sức Chứa,Phòng Hiện Tại,Giá,Trạng Thái<br>
            P001,101,A,4,2,2000000,Đã Kín<br>
            P002,102,A,4,0,2000000,Trống
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Chọn file CSV:</label>
                <input type="file" id="file" name="file" accept=".csv,.xlsx,.xls" required>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">📤 Nhập Dữ Liệu</button>
                <a href="<?= BASE_URL ?>room" class="btn btn-secondary">← Quay Lại</a>
            </div>
        </form>
    </div>
</body>
</html>
