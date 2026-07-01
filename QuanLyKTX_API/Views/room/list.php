<?php // Danh sách phòng ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
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
            <a href="<?= BASE_URL ?>room/export" class="btn btn-success">Xuất Excel</a>
            <a href="<?= BASE_URL ?>room/import" class="btn btn-success">Nhập Excel</a>
            <a href="<?= BASE_URL ?>room/create" class="btn btn-primary">+ Thêm Phòng</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"> ✓ <?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"> ⚠️ <?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="GET" action="<?= BASE_URL ?>room" class="search-box">
        <input type="text" name="keyword" placeholder=" 🔍 Tìm kiếm theo mã, tên phòng" value="<?= htmlspecialchars($keyword ?? '') ?>">
        <button type="submit" class="btn btn-primary">Tìm Kiếm</button>
        <?php if (!empty($keyword)): ?>
            <a href="<?= BASE_URL ?>room" class="btn btn-secondary">Reset</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($rooms)): ?>
        <table>
            <thead>
                <tr>
                    <th>Mã Phòng</th>
                    <th>Số Phòng</th>
                    <th>Tòa</th>
                    <th>Sức Chứa</th>
                    <th>Hiện Tại</th>
                    <th>Giá (VND)</th>
                    <th>Trạng Thái</th>
                    <th style="text-align: center;">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $r): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($r['maphong'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($r['sophong'] ?? '') ?></td>
                        <td><?= htmlspecialchars($r['toa'] ?? '') ?></td>
                        <td><?= $r['succhua'] ?></td>
                        <td><?= $r['phonghientai'] ?></td>
                        <td><?= number_format($r['gia']) ?></td>
                        <td>
                            <?php if ($r['trangthai'] == 'Còn chỗ'): ?>
                                <span style="color: #27ae60; padding: 3px 8px; font-weight: bold;">Còn chỗ</span>
                            <?php elseif ($r['trangthai'] == 'Trống'): ?>
                                <span style="color: #27ae60; padding: 3px 8px; font-weight: bold;">Trống</span>
                            <?php else: ?>
                                <span style="color: #e74c3c; padding: 3px 8px; font-weight: bold;"><?= htmlspecialchars($r['trangthai'] ?? '') ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-links">
                                <a href="<?= BASE_URL ?>room/danhsach/<?= $r['maphong'] ?>" class="btn-action btn-view">👁️ Xem</a>
                                <a href="<?= BASE_URL ?>room/edit/<?= $r['maphong'] ?>" class="btn-action btn-edit"> ✏️ Sửa</a>
                                <a href="<?= BASE_URL ?>room/delete/<?= $r['maphong'] ?>" class="btn-action btn-delete" onclick="return confirm('Xác nhận xóa?')"> 🗑️ Xóa</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <p> 📭 Không tìm thấy phòng nào</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>