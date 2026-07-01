<?php // Danh sách sự cố ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Sự Cố</title>
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
            <a href="<?= BASE_URL ?>incident/export" class="btn btn-success">Xuất Excel</a>
            <a href="<?= BASE_URL ?>incident/import" class="btn btn-success">Nhập Excel</a>
            <a href="<?= BASE_URL ?>incident/create" class="btn btn-primary">+ Báo Cáo Sự Cố</a>
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

    <form method="GET" action="<?= BASE_URL ?>incident" class="search-box">
        <input type="text" name="search" placeholder=" 🔍 Tìm kiếm theo phòng, mô tả..." value="<?= htmlspecialchars($keyword ?? '') ?>">
        <select name="status">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="Mới gửi" <?= (isset($status) && $status === 'Mới gửi') ? 'selected' : '' ?>>Mới gửi</option>
            <option value="Chờ Xử Lý" <?= (isset($status) && $status === 'Chờ Xử Lý') ? 'selected' : '' ?>>Chờ Xử Lý</option>
            <option value="Đang Xử Lý" <?= (isset($status) && $status === 'Đang Xử Lý') ? 'selected' : '' ?>>Đang Xử Lý</option>
            <option value="Đã Xử Lý" <?= (isset($status) && $status === 'Đã Xử Lý') ? 'selected' : '' ?>>Đã Xử Lý</option>
        </select>
        <button type="submit" class="btn btn-primary">Tìm Kiếm</button>
        <?php if (!empty($keyword) || !empty($status)): ?>
            <a href="<?= BASE_URL ?>incident" class="btn btn-secondary"> ✕ Reset</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Mã SC</th>
                <th>Phòng</th>
                <th>Người Báo</th>
                <th>Mô Tả</th>
                <th>Ngày Báo</th>
                <th>Trạng Thái</th>
                <th style="text-align: center;">Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($incidents)): ?>
                <?php foreach ($incidents as $i): ?>
                    <tr>
                        <td><?= $i['masuco'] ?></td>
                        <td><?= htmlspecialchars($i['maphong'] ?? 'N/A') ?></td>
                        <td>
                            <?php if (!empty($i['hoten'])): ?>
                                <strong><?= htmlspecialchars($i['hoten']) ?></strong><br>
                                <small>(<?= htmlspecialchars($i['masv']) ?>)</small>
                            <?php else: ?>
                                <span style="color: #999;">Admin tạo</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars(substr($i['mota'], 0, 50)) ?></td>
                        <td><?= $i['ngaybao'] ?></td>
                        <td>
                            <?php if ($i['trangthai'] == 'Mới gửi'): ?>
                                <span style="color: #e74c3c; padding: 3px 8px; font-weight: bold;">Mới gửi</span>
                            <?php elseif ($i['trangthai'] == 'Chờ Xử Lý'): ?>
                                <span style="color: #FF7F00; padding: 3px 8px; font-weight: bold;">Chờ Xử Lý</span>
                            <?php elseif ($i['trangthai'] == 'Đang Xử Lý'): ?>
                                <span style="color: #FFA500; padding: 3px 8px; font-weight: bold;">Đang Xử Lý</span>
                            <?php else: ?>
                                <span style="color: #27ae60; padding: 3px 8px; font-weight: bold;"><?= htmlspecialchars($i['trangthai']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-links">
                                <a href="<?= BASE_URL ?>incident/edit/<?= $i['masuco'] ?>" class="btn-action btn-edit"> ✏️ Sửa</a>
                                <a href="<?= BASE_URL ?>incident/delete/<?= $i['masuco'] ?>" class="btn-action btn-delete" onclick="return confirm('Xóa?')"> 🗑️ Xóa</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <p> ✓ Không có sự cố nào</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>