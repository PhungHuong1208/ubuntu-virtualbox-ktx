<?php
$pageTitle = 'Gửi Yêu Cầu Sự Cố';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Quản Lý Ký Túc Xá</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>Public/css/baocao.css?v=<?= time() ?>">
</head>
<body>
    <div class="form-header">
        <a href="<?= BASE_URL ?>dashboard" class="back-link">← Quay lại trang chủ</a>
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert-success">✅ <?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="">
            <div class="form-group required">
                <label for="masv">Mã Sinh Viên</label>
                <input
                    type="text"
                    id="masv"
                    name="masv"
                    value="<?= htmlspecialchars($_SESSION['masv'] ?? '') ?>"
                    readonly
                >
            </div>

            <div class="form-group required">
                <label for="maphong">Mã Phòng</label>
                <input
                    type="text"
                    id="maphong"
                    name="maphong"
                    value="<?= htmlspecialchars($maphong ?? '') ?>"
                    readonly
                >
            </div>

            <div class="form-group required">
                <label for="mota">Mô Tả Sự Cố</label>
                <textarea
                    id="mota"
                    name="mota"
                    rows="5"
                    required
                    placeholder="Nhập chi tiết vấn đề bạn đang gặp phải..."
                ></textarea>
            </div>

            <div class="form-group required">
                <label for="ngaybao">Ngày Báo</label>
                <input
                    type="date"
                    id="ngaybao"
                    name="ngaybao"
                    value="<?= date('Y-m-d') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="trangthai">Trạng Thái</label>
                <input
                    type="text"
                    id="trangthai"
                    name="trangthai"
                    value="Chờ duyệt"
                    readonly
                >
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Gửi Yêu Cầu</button>
                <a href="<?= BASE_URL ?>dashboard" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</body>
</html>