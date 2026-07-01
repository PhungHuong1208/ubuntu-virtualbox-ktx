<?php
// Danh sách Phòng (Phía Sinh Viên)

if (!isset($room) || empty($room)) {
    echo '<div class="alert alert-error">Bạn chưa có thông tin phòng.</div>';
    return;
}



$pageTitle = 'Thông tin Phòng Của Tôi';
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>Public/css/style.css">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI';
        background: #f5f5f5;
        padding: 20px;
    }

    .form-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 5px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: 600;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #3498db;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .btn {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-success {
        background: #27ae60;
        color: white;
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn:hover {
        opacity: 0.8;
    }

    /* Thông báo */
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-error {
        background: #fee;
        border: 1px solid #f98866;
        color: #c33;
    }

    .alert-success {
        background: #eef;
        border: 1px solid #88e;
        color: #33c;
    }
    </style>
</head>

<body>

    <body>



        <div class="form-container">
            <a href="<?= BASE_URL ?>dashboard" class="back-link">← Quay lại trang chủ</a>
            <h1><?= htmlspecialchars($pageTitle) ?></h1>





            <form>
                <div class="form-group">
                    <label>Mã Phòng</label>
                    <input type="text" name="maphong" value="<?= htmlspecialchars($room['maphong'] ?? '') ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Số Phòng</label>
                    <input type="text" name="sophong" value="<?= htmlspecialchars($room['sophong'] ?? '') ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Tòa</label>
                    <input type="text" name="toa" value="<?= htmlspecialchars($room['toa'] ?? '') ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Sức Chứa</label>
                    <input type="number" name="succhua" value="<?= $room['succhua'] ?? 8 ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Hiện Tại</label>
                    <input type="number" name="phonghientai" value="<?= $room['phonghientai'] ?? 0 ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Giá (VND)</label>
                    <input type="number" name="gia" value="<?= $room['gia'] ?? '' ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Trạng Thái</label>
                    <input type="text" name="trangthai" value="<?= htmlspecialchars($room['trangthai'] ?? '') ?>"
                        readonly>
                </div>

            </form>
        </div>
    </body>

</html>

</body>

</html>