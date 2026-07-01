<?php
// Form nhập khẩu dữ liệu tiền điện/nước
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập Dữ Liệu - Tiền Điện & Nước</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/import.css?v=<?= time() ?>">
</head>
<body>
    <div class="import-container">
        <h2> 📥  Nhập Dữ Liệu Tiền Điện & Nước</h2>

        <?php if (isset($msg) && is_array($msg) && !empty($msg['text'])): ?>
            <div class="alert alert-<?= $msg['type'] === 'success' ? 'success' : 'error' ?>">
                <?= htmlspecialchars($msg['text']) ?>
            </div>
        <?php endif; ?>

        <div class="instructions">
            <h4> 📋  Hướng dẫn định dạng file CSV:</h4>
            <ul>
                <li><strong>Tiền Điện:</strong> Mã HĐ Điện | Mã Phòng | Số Tiền | Ngày | Trạng Thái</li>
                <li><strong>Tiền Nước:</strong> Mã HĐ Nước | Mã Phòng | Số Tiền | Ngày | Trạng Thái</li>
                <li>Các cột phân cách bằng dấu <strong>;</strong> (semicolon)</li>
                <li>Hàng đầu tiên là header (tên cột), sẽ được bỏ qua</li>
                <li>Trạng thái: "Chưa thanh toán" hoặc "Đã thanh toán"</li>
                <li>Ngày định dạng: YYYY-MM-DD (ví dụ: 2024-12-25)</li>
            </ul>
        </div>

        <div class="csv-template">
            <strong> 💡  Ví dụ file CSV (Tiền điện):</strong>
            Mã HĐ Điện;Mã Phòng;Số Tiền;Ngày;Trạng Thái<br>
            D001;P101;150000;2024-12-01;Chưa thanh toán<br>
            D002;P102;175000;2024-12-01;Đã thanh toán
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Chọn loại dữ liệu (*):</label>
                <select name="type" required>
                    <option value="electricity"> ⚡  Tiền Điện</option>
                    <option value="water"> 🚰  Tiền Nước</option>
                </select>
            </div>
            <div class="form-group">
                <label>Chọn File CSV (*):</label>
                <input type="file" name="file" accept=".csv" required>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary"> 📤  Nhập Dữ Liệu</button>
                <a href="<?= BASE_URL ?>utility" class="btn btn-secondary">← Quay Lại</a>
            </div>
        </form>
    </div>
</body>
</html>