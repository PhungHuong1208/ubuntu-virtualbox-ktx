<?php
$isEdit = isset($contract);
$pageTitle = $isEdit ? 'Gia Hạn Hợp Đồng' : 'Thêm Hợp Đồng Mới';
$actionUrl = $isEdit ? BASE_URL . 'contract/update/' . $contract['mahopdong'] : BASE_URL . 'contract/store';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Quản Lý Ký Túc Xá</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/form.css?v=<?= time() ?>">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <div>
                <a href="<?= BASE_URL ?>contract" class="back-link">← Quay lại Danh Sách</a>
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
                    <label for="mahopdong">Mã Hợp Đồng</label>
                    <input type="text" id="mahopdong" name="mahopdong" placeholder="VD: HD001" value="<?= htmlspecialchars($contract['mahopdong'] ?? $nextMaHopDong ?? '') ?>" readonly class="readonly-field">
                </div>
                <div class="form-group required">
                    <label for="masv">Mã Sinh Viên</label>
                    <select id="masv" name="masv" class="select2-search" required>
                        <option value="">-- Chọn Sinh Viên --</option>
                        <?php foreach ($students ?? [] as $sv): ?>
                            <option value="<?= htmlspecialchars($sv['masv']) ?>" <?= ($contract['masv'] ?? '') === $sv['masv'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sv['masv'] . ' - ' . $sv['hoten']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group required">
                    <label for="maphong">Mã Phòng</label>
                    <select id="maphong" name="maphong" class="select2-search" required>
                        <option value="">-- Chọn Phòng --</option>
                        <?php foreach ($rooms ?? [] as $room): ?>
                            <option value="<?= htmlspecialchars($room['maphong']) ?>" <?= ($contract['maphong'] ?? '') === $room['maphong'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($room['maphong'] . ' - Tòa ' . $room['toa']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group required">
                    <label for="trangthai">Trạng Thái</label>
                    <select id="trangthai" name="trangthai" required>
                        <option value="Đang Hoạt Động" <?= ($contract['trangthai'] ?? 'Đang Hoạt Động') === 'Đang Hoạt Động' ? 'selected' : '' ?>>Đang Hoạt Động</option>
                        <option value="Hết Hạn" <?= ($contract['trangthai'] ?? '') === 'Hết Hạn' ? 'selected' : '' ?>>Hết Hạn</option>
                        <option value="Đã Chấm Dứt" <?= ($contract['trangthai'] ?? '') === 'Đã Chấm Dứt' ? 'selected' : '' ?>>Đã Chấm Dứt</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group required">
                    <label for="batdau">Ngày Bắt Đầu</label>
                    <input type="date" id="batdau" name="batdau" class="<?= $isEdit ? 'readonly-field' : '' ?>" value="<?= htmlspecialchars($contract['batdau'] ?? '') ?>" <?= $isEdit ? 'readonly' : 'required' ?>>
                </div>
                <div class="form-group required">
                    <label for="hethan">Ngày Hết Hạn</label>
                    <input type="date" id="hethan" name="hethan" value="<?= htmlspecialchars($contract['hethan'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">✓ <?= $isEdit ? 'Gia Hạn' : 'Thêm Mới' ?></button>
                <a href="<?= BASE_URL ?>contract" class="btn btn-secondary">✕ Hủy</a>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-search').select2({ width: '100%' });
        });
    </script>
</body>
</html>