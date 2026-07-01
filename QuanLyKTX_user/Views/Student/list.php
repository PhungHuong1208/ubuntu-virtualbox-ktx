<?php
$student = $students[0] ?? null;
if (!$student) {
    echo '<div class="alert alert-error">Không tìm thấy sinh viên để chỉnh sửa.</div>';
    return;
}
$isEdit = true;
$pageTitle = 'Chỉnh Sửa Hồ Sơ';
$actionUrl = BASE_URL . 'student/update/' . urlencode($student['masv']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Cổng Sinh Viên</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>Public/css/list.css?v=<?= time() ?>">
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="<?= BASE_URL ?>dashboard" class="back-link">← Quay lại trang chủ</a>
            <h1><?= htmlspecialchars($pageTitle) ?></h1>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">⚠️ <?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">✅ <?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="field-hint">📌 Mã số sinh viên, họ tên, lớp, giới tính, CCCD là thông tin cố định không thể tự thay đổi.</div>

        <form method="POST" action="<?= htmlspecialchars($actionUrl) ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>MSSV</label>
                    <input type="text" name="masv" value="<?= htmlspecialchars($student['masv'] ?? '') ?>" required readonly>
                </div>
                <div class="form-group">
                    <label>Họ Tên</label>
                    <input type="text" name="hoten" value="<?= htmlspecialchars($student['hoten'] ?? '') ?>" required readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Lớp</label>
                    <input type="text" name="lop" value="<?= htmlspecialchars($student['lop'] ?? '') ?>" required readonly>
                </div>
                <div class="form-group">
                    <label>Giới Tính</label>
                    <input type="text" name="gioitinh" value="<?= htmlspecialchars($student['gioitinh'] ?? '') ?>" required readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>CCCD</label>
                    <input type="text" name="cccd" value="<?= htmlspecialchars($student['cccd'] ?? '') ?>" required readonly>
                </div>
                <div class="form-group">
                    <label>Số Điện Thoại</label>
                    <input type="text" name="sodienthoai" value="<?= htmlspecialchars($student['sodienthoai'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($student['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Địa Chỉ</label>
                <textarea name="diachi" required><?= htmlspecialchars($student['diachi'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Cập Nhật Hồ Sơ</button>
            </div>
        </form>
    </div>
</body>
</html>