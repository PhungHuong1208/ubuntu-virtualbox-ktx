<?php // Danh sách thanh toán ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Thanh Toán</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/list.css?v=<?= time() ?>">
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h1><?= htmlspecialchars($title) ?></h1>
            <a href="<?= BASE_URL ?>auth/dashboard" class="back-link">← Quay lại trang chính</a>
        </div>
        <div class="header-actions">
            <a href="<?= BASE_URL ?>payment/export" class="btn btn-success">Xuất Excel</a>
            <a href="<?= BASE_URL ?>payment/import" class="btn btn-success">Nhập Excel</a>
            <a href="<?= BASE_URL ?>payment/create" class="btn btn-primary">+ Thêm Thanh Toán</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"> ✓ <?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form method="GET" action="<?= BASE_URL ?>payment" class="search-box">
        <input type="text" name="search" placeholder=" 🔍 Tìm kiếm theo phòng, trạng thái..." value="<?= htmlspecialchars($keyword ?? '') ?>">
        <button type="submit" class="btn btn-primary">Tìm Kiếm</button>
        <?php if (!empty($keyword)): ?>
            <a href="<?= BASE_URL ?>payment" class="btn btn-secondary"> ✕ Reset</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Mã TT</th>
                <th>Phòng</th>
                <th>Số Tiền (VND)</th>
                <th>Hạn Trả</th>
                <th>Trạng Thái</th>
                <th style="text-align: center;">Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($payments)): ?>
                <?php foreach ($payments as $p): ?>
                    <tr>
                        <td><?= 'TT' . str_pad($p['mathanhtoan'], 3, '0', STR_PAD_LEFT) ?></td>
                        <td><?= htmlspecialchars($p['maphong'] ?? 'N/A') ?></td>
                        <td><?= number_format($p['sotien']) ?></td>
                        <td><?= $p['ngaytra'] ?></td>
                        <td>
                            <?php if ($p['trangthai'] == 'Chưa Thanh Toán'): ?>
                                <span style="color: #e74c3c; padding: 3px 8px; font-weight: bold;">Chưa Thanh Toán</span>
                            <?php else: ?>
                                <span style="color: #27ae60; padding: 3px 8px; font-weight: bold;"><?= htmlspecialchars($p['trangthai']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-links">
                                <?php if ($p['trangthai'] === 'Chưa Thanh Toán'): ?>
                                    <a href="<?= BASE_URL ?>payment/edit/<?= $p['mathanhtoan'] ?>" class="btn-action btn-edit"> ✏️ Sửa</a>
                                    <a href="<?= BASE_URL ?>payment/markAsPaid/<?= $p['mathanhtoan'] ?>" class="btn-action btn-pay"> ✓ Thanh Toán</a>
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>payment/delete/<?= $p['mathanhtoan'] ?>" class="btn-action btn-delete" onclick="return confirm('Xóa?')"> 🗑️ Xóa</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <p> 📭 Không có thanh toán nào</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>