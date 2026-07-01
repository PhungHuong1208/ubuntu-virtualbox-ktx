<?php
/**
 * Student - List View
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Quản Lý Ký Túc Xá</title>
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
            <a href="<?= BASE_URL ?>student/export" class="btn btn-success">Xuất Excel</a>
            <a href="<?= BASE_URL ?>student/import" class="btn btn-success">Nhập Excel</a>
            <a href="<?= BASE_URL ?>student/create" class="btn btn-primary">+ Thêm Sinh Viên</a>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">⚠️ <?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">✓ <?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form method="GET" action="<?= BASE_URL ?>student" class="search-box">
        <input type="text" name="keyword" placeholder=" 🔍 Tìm kiếm theo MSSV hoặc tên..." value="<?= htmlspecialchars($keyword ?? '') ?>">
        <button type="submit" class="btn btn-primary">Tìm Kiếm</button>
        <?php if (!empty($keyword)): ?>
            <a href="<?= BASE_URL ?>student" class="btn btn-secondary">Reset</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($students)): ?>
        <table>
            <thead>
                <tr>
                    <th>MSSV</th>
                    <th>Họ Tên</th>
                    <th>Lớp</th>
                    <th>Giới Tính</th>
                    <th>CCCD</th>
                    <th>Số Điện Thoại</th>
                    <th>Email</th>
                    <th>Địa Chỉ</th>
                    <th style="text-align: center;">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($student['masv'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($student['hoten'] ?? '') ?></td>
                        <td><?= htmlspecialchars($student['lop'] ?? '') ?></td>
                        <td><?= htmlspecialchars($student['gioitinh'] ?? '') ?></td>
                        <td><?= htmlspecialchars($student['cccd'] ?? '') ?></td>
                        <td><?= htmlspecialchars($student['sodienthoai'] ?? '') ?></td>
                        <td><?= htmlspecialchars($student['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($student['diachi'] ?? '') ?></td>
                        <td>
                            <div class="action-links">
                                <a href="<?= BASE_URL ?>student/edit/<?= $student['masv'] ?>" class="btn-action btn-edit"> ✏️ Sửa</a>
                                <a href="<?= BASE_URL ?>student/delete/<?= $student['masv'] ?>" class="btn-action btn-delete" onclick="return confirm('Bạn chắc chắn muốn xóa?')"> 🗑️ Xóa</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="footer-info">📊 Tìm thấy <strong><?= $total ?></strong> sinh viên</div>
    <?php else: ?>
        <div class="empty-state">
            <p> 📭 Không tìm thấy sinh viên nào</p>
            <a href="<?= BASE_URL ?>student/create" class="btn btn-primary">+ Thêm Sinh Viên Mới</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>