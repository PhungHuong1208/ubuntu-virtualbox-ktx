<?php
/**
 * Contract - View Only cho Sinh Viên
 * @var Controller $this
 */

if (!isset($hopdong) || !$hopdong) {
    echo '<div class="alert alert-error">Bạn chưa có hợp đồng.</div>';
    return;
}

$pageTitle = 'Thông tin Hợp Đồng';
?>



<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Quản Lý Ký Túc Xá</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>Public/css/style.css">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f5f5;
        padding: 20px;
    }

    .form-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .form-header {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid #ecf0f1;
    }

    .form-header h1 {
        color: #2c3e50;
        margin-bottom: 5px;
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
        font-size: 14px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
    }

    .form-group.required label::after {
        content: " *";
        color: #e74c3c;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .form-row .form-group {
        margin-bottom: 0;
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
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
    }

    .btn-success {
        background: #27ae60;
        color: white;
    }

    .btn-success:hover {
        background: #229954;
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        padding: 12px;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .back-link {
        color: #3498db;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        display: inline-block;
        margin-bottom: 15px;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    @media (max-width: 600px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>

<body>
    <div class="form-container">
        <!-- Header -->
        <div class="form-header">
            <a href="<?= BASE_URL ?>dashboard" class="back-link">← Quay lại trang chủ</a>
            <h1><?= htmlspecialchars($pageTitle) ?></h1>
        </div>

        <!-- Messages -->
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert-error">
            ⚠️ <?= $_SESSION['error'] ?>
        </div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Form -->
        <form>
            <!-- Row 1: Mã Hợp Đồng & Mã SV -->
            <div class="form-row">
                <div class="form-group required">
                    <label for="mahopdong">Mã Hợp Đồng</label>
                    <input type="text" id="mahopdong" name="mahopdong"
                        value="<?= htmlspecialchars($hopdong['mahopdong'] ?? '') ?>" readonly>
                </div>

                <div class="form-group required">
                    <label for="masv">Mã Sinh Viên</label>
                    <input type="text" id="masv" name="masv" value="<?= htmlspecialchars($hopdong['masv'] ?? '') ?>"
                        readonly>
                </div>
            </div>

            <!-- Row 2: Mã Phòng & Trạng Thái -->
            <div class="form-row">
                <div class="form-group required">
                    <label for="maphong">Mã Phòng</label>
                    <input type="text" id="maphong" name="maphong"
                        value="<?= htmlspecialchars($hopdong['maphong'] ?? '') ?>" readonly>
                </div>

                <div class="form-group required">
                    <label for="trangthai">Trạng Thái</label>
                    <input type="text" id="trangthai" name="trangthai"
                        value="<?= htmlspecialchars($hopdong['trangthai'] ?? '') ?>" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group required">
                    <label for="batdau">Ngày Bắt Đầu</label>
                    <input type="date" id="batdau" name="batdau"
                        value="<?= htmlspecialchars($hopdong['batdau'] ?? '') ?>" readonly>
                </div>

                <div class="form-group required">
                    <label for="hethan">Ngày Hết Hạn</label>
                    <input type="date" id="hethan" name="hethan"
                        value="<?= htmlspecialchars($hopdong['hethan'] ?? '') ?>" readonly>
                </div>
            </div>
        </form>
    </div>
</body>

</html>