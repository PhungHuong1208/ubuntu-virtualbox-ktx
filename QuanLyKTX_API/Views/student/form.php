<?php
$isEdit = isset($student);
$pageTitle = $isEdit ? 'Chỉnh Sửa Sinh Viên' : 'Thêm Sinh Viên Mới';
$actionUrl = $isEdit ? BASE_URL . 'student/update/' . $student['masv'] : BASE_URL . 'student/store';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Quản Lý Ký Túc Xá</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/form.css?v=<?= time() ?>">
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <div>
                <a href="<?= BASE_URL ?>student" class="back-link">← Quay lại Danh Sách</a>
            </div>
            <h1><?= htmlspecialchars($pageTitle) ?></h1>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">⚠️ <?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars($actionUrl) ?>">
            <div class="form-row">
                <div class="form-group required">
                    <label for="masv">MSSV</label>
                    <input type="text" id="masv" name="masv" placeholder="VD: 74DCTT..." value="<?= htmlspecialchars($student['masv'] ?? '') ?>" <?= $isEdit ? 'readonly class="readonly-field"' : 'required' ?>>
                    <?php if (!$isEdit): ?><div class="field-hint">Nhập mã số sinh viên hợp lệ.</div><?php endif; ?>
                </div>
                <div class="form-group required">
                    <label for="hoten">Họ Tên</label>
                    <input type="text" id="hoten" name="hoten" placeholder="VD: Nguyễn Văn A" value="<?= htmlspecialchars($student['hoten'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group required">
                    <label for="lop">Lớp</label>
                    <input type="text" id="lop" name="lop" placeholder="VD: CT07A, CDTH21" value="<?= htmlspecialchars($student['lop'] ?? '') ?>" required>
                </div>
                <div class="form-group required">
                    <label for="gioitinh">Giới Tính</label>
                    <select id="gioitinh" name="gioitinh" required>
                        <option value="">-- Chọn Giới Tính --</option>
                        <option value="Nam" <?= ($student['gioitinh'] ?? '') === 'Nam' ? 'selected' : '' ?>>Nam</option>
                        <option value="Nữ" <?= ($student['gioitinh'] ?? '') === 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                        <option value="Khác" <?= ($student['gioitinh'] ?? '') === 'Khác' ? 'selected' : '' ?>>Khác</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="cccd">CCCD</label>
                    <input type="text" id="cccd" name="cccd" placeholder="VD: 123456789012" value="<?= htmlspecialchars($student['cccd'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="sodienthoai">Số Điện Thoại</label>
                    <input type="text" id="sodienthoai" name="sodienthoai" placeholder="VD: 0123456789" value="<?= htmlspecialchars($student['sodienthoai'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="VD: nguyenvana@example.com" value="<?= htmlspecialchars($student['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="diachi">Địa Chỉ</label>
                <textarea id="diachi" name="diachi" placeholder="Nhập địa chỉ đầy đủ"><?= htmlspecialchars($student['diachi'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">✓ <?= $isEdit ? 'Cập Nhật' : 'Thêm Mới' ?></button>
                <a href="<?= BASE_URL ?>student" class="btn btn-secondary">✕ Hủy</a>
            </div>
        </form>
    </div>
</body>
</html>