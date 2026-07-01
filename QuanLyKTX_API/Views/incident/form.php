<?php
$isEdit = isset($incident);
$pageTitle = $isEdit ? 'Cập Nhật Sự Cố' : 'Báo Cáo Sự Cố Mới';
$actionUrl = $isEdit ? BASE_URL . 'incident/update/' . $incident['masuco'] : BASE_URL . 'incident/store';
$today = date('Y-m-d');
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
                <a href="<?= BASE_URL ?>incident" class="back-link">← Quay lại Danh Sách</a>
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
                    <label for="masuco">Mã Yêu Cầu / Sự Cố</label>
                    <input type="text" id="masuco" name="masuco" value="<?= htmlspecialchars($incident['masuco'] ?? $nextMaSuCo ?? '') ?>" readonly class="readonly-field">
                </div>
                <div class="form-group required">
                    <label for="maphong">Mã Phòng</label>
                    <select id="maphong" name="maphong" class="select2-search" required>
                        <option value="">-- Chọn Phòng --</option>
                        <?php foreach ($rooms ?? [] as $room): ?>
                            <?php $selected = ($isEdit && $incident['maphong'] === $room['maphong']) ? 'selected' : ''; ?>
                            <option value="<?= htmlspecialchars($room['maphong']) ?>" <?= $selected ?>>
                                <?= htmlspecialchars($room['maphong'] . ' - Tòa ' . $room['toa']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group required">
                <label for="mota">Mô Tả Sự Cố</label>
                <textarea id="mota" name="mota" placeholder="Nhập chi tiết về sự cố hoặc yêu cầu..." required><?= htmlspecialchars($incident['mota'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group required">
                    <label for="ngaybao">Ngày Báo</label>
                    <input type="date" id="ngaybao" name="ngaybao" value="<?= htmlspecialchars($incident['ngaybao'] ?? $today) ?>" required>
                </div>
                <div class="form-group required">
                    <label for="trangthai">Trạng Thái</label>
                    <select id="trangthai" name="trangthai" required>
                        <option value="Chờ Xử Lý" <?= ($incident['trangthai'] ?? '') === 'Chờ Xử Lý' ? 'selected' : '' ?>>Chờ Xử Lý</option>
                        <option value="Đang Xử Lý" <?= ($incident['trangthai'] ?? '') === 'Đang Xử Lý' ? 'selected' : '' ?>>Đang Xử Lý</option>
                        <option value="Đã Xử Lý" <?= ($incident['trangthai'] ?? '') === 'Đã Xử Lý' ? 'selected' : '' ?>>Đã Xử Lý</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">✓ <?= $isEdit ? 'Cập Nhật' : 'Thêm Mới' ?></button>
                <a href="<?= BASE_URL ?>incident" class="btn btn-secondary">✕ Hủy</a>
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