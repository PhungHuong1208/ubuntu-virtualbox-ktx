<?php // Danh sách hợp đồng ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Hợp Đồng</title>
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
            <a href="<?= BASE_URL ?>contract/export" class="btn btn-success">Xuất Excel</a>
            <a href="<?= BASE_URL ?>contract/import" class="btn btn-success">Nhập Excel</a>
            <a href="<?= BASE_URL ?>contract/create" class="btn btn-primary">+ Thêm Hợp Đồng</a>
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

    <form method="GET" action="<?= BASE_URL ?>contract" class="search-box">
        <input type="text" name="search" placeholder=" 🔍 Tìm kiếm theo Mã HĐ, tên SV, phòng..." value="<?= htmlspecialchars($keyword ?? '') ?>">
        <button type="submit" class="btn btn-primary">Tìm Kiếm</button>
        <?php if (!empty($keyword)): ?>
            <a href="<?= BASE_URL ?>contract" class="btn btn-secondary"> ✕ Reset</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($contracts)): ?>
        <table>
            <thead>
                <tr>
                    <th>Mã HĐ</th>
                    <th>MSSV</th>
                    <th>Sinh Viên</th>
                    <th>Phòng</th>
                    <th>Bắt Đầu</th>
                    <th>Hết Hạn</th>
                    <th>Trạng Thái</th>
                    <th style="text-align: center;">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contracts as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['mahopdong']) ?></td>
                        <td><?= htmlspecialchars($c['masv']) ?></td>
                        <td><?= htmlspecialchars($c['hoten'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($c['maphong'] ?? 'N/A') ?></td>
                        <td><?= $c['batdau'] ?></td>
                        <td><?= $c['hethan'] ?></td>
                        <td>
                            <?php if ($c['trangthai'] == 'Đang Hoạt Động'): ?>
                                <span style="color: #27ae60; padding: 3px 8px; font-weight: bold;">Đang Hoạt Động</span>
                            <?php else: ?>
                                <span style="color: #e74c3c; padding: 3px 8px; font-weight: bold;"><?= htmlspecialchars($c['trangthai']) ?></span>
                            <?php endif; ?>`
                        </td>
                        <td>
                            <div class="action-links">
                                <?php if ($c['trangthai'] !== 'Đã Chấm Dứt'): ?>
                                    <a href="<?= BASE_URL ?>contract/edit/<?= $c['mahopdong'] ?>" class="btn-action btn-edit"> ✏️ Gia Hạn</a>
                                    <a href="<?= BASE_URL ?>contract/terminate/<?= $c['mahopdong'] ?>" class="btn-action btn-warning" onclick="return confirm('Bạn có chắc chắn muốn kết thúc hợp đồng này? Hợp đồng sẽ chuyển sang trạng thái Đã Chấm Dứt thay vì xóa khỏi hệ thống.')"> 🛑 Kết Thúc</a>
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>contract/delete/<?= $c['mahopdong'] ?>" class="btn-action btn-delete" onclick="return confirm('Xóa luôn khỏi CSDL?')"> 🗑️ Xóa</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <p> 📭 Không có hợp đồng nào</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>