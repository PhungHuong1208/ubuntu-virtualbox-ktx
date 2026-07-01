<?php
if (!isset($_SESSION['user_id'])) { header('Location: ../../index.php'); exit; }
if (!defined('BASE_URL')) { define('BASE_URL', 'http://localhost/webktx/QuanLyKTX_API/Public/'); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập Dữ Liệu Hợp Đồng</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/import.css?v=<?= time() ?>">
</head>
<body>
    <div class="import-container">
        <h2> 📤  Nhập Dữ Liệu Hợp Đồng</h2>
        
        <?php if (isset($msg) && is_array($msg) && !empty($msg['text'])): ?>
            <div class="alert alert-<?= $msg['type'] === 'success' ? 'success' : 'error' ?>">
                <?= htmlspecialchars($msg['text']) ?>
            </div>
        <?php endif; ?>
        
        <div class="instructions">
            <h4> 📋  Hướng Dẫn:</h4>
            <ul>
                <li>Chỉ hỗ trợ file <strong>.csv</strong></li>
                <li>Kích thước file tối đa: <strong>5MB</strong></li>
                <li>Dòng đầu: <strong>Mã Hợp Đồng, Mã SV, Mã Phòng, Bắt Đầu, Kết Thúc, Trạng Thái</strong></li>
                <li>Không được để trống Mã Hợp Đồng và Mã SV</li>
            </ul>
        </div>
        
        <div class="csv-template">
            <strong> 📝  Mẫu CSV:</strong><br>
            Mã Hợp Đồng,Mã SV,Mã Phòng,Bắt Đầu,Kết Thúc,Trạng Thái<br>
            HD001,SV001,P001,2024-01-01,2025-01-01,Còn Hiệu Lực
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Chọn file CSV:</label>
                <input type="file" id="file" name="file" accept=".csv" required>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary"> 📤  Nhập Dữ Liệu</button>
                <a href="<?= BASE_URL ?>contract" class="btn btn-secondary">← Quay Lại</a>
            </div>
        </form>
    </div>
</body>
</html>