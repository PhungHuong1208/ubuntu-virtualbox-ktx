<?php
$isEdit = isset($room);
$pageTitle = $isEdit ? 'Chỉnh Sửa Phòng' : 'Thêm Phòng Mới';
$actionUrl = $isEdit ? BASE_URL . 'room/update/' . $room['maphong'] : BASE_URL . 'room/store';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/form.css?v=<?= time() ?>">
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <div>
                <a href="<?= BASE_URL ?>room" class="back-link">← Quay lại Danh Sách</a>
            </div>
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

        <form method="POST" action="<?= htmlspecialchars($actionUrl) ?>">
            <div class="form-row">
                <div class="form-group required">
                    <label>Mã Phòng</label>
                    <input type="text" id="maphongInput" name="maphong" value="<?= htmlspecialchars($room['maphong'] ?? '') ?>" <?= $isEdit ? 'readonly class="readonly-field"' : 'required placeholder="VD: P101 hoặc A101"' ?>>
                </div>
                <div class="form-group required">
                    <label>Số Phòng</label>
                    <input type="text" id="sophongInput" name="sophong" value="<?= htmlspecialchars($room['sophong'] ?? '') ?>" readonly class="readonly-field">
                </div>
            </div>


            <div class="form-row">
                <div class="form-group required">
                    <label>Tòa</label>
                    <input type="text" id="toaInput" name="toa" value="<?= htmlspecialchars($room['toa'] ?? '') ?>" readonly class="readonly-field" required placeholder="Tự động trích xuất">
                    <div id="toaError" class="field-error" style="display: none;">Tòa không tồn tại! (Hệ thống chỉ nhận A, B, C, D)</div>
                </div>

                
                <div class="form-group required">
                    <label>Sức Chứa</label>
                    <input type="number" name="succhua" value="<?= $room['succhua'] ?? 8 ?>" min="1" <?= $isEdit ? 'required' : 'readonly class="readonly-field"' ?>>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group required">
                    <label>Hiện Tại</label>
                    <input type="number" name="phonghientai" 
                        value="<?= $room['phonghientai'] ?? 0 ?>" 
                        readonly 
                        tabindex="-1"
                        class="readonly-field" 
                        style="pointer-events: none; background-color: #f0f0f0;">
                </div>

                <div class="form-group required">
                    <label>Giá (VND)</label>
                    <input type="number" name="gia" value="<?= $room['gia'] ?? '' ?>" required>
                </div>
            </div>

            <div class="form-group required">
                <label>Trạng Thái</label>
                <select name="trangthai" <?= $isEdit ? 'required' : 'class="readonly-field"' ?>>
                    <option value="Trống" <?= ($room['trangthai'] ?? 'Trống') === 'Trống' || ($room['trangthai'] ?? '') === 'Đầy' ? 'selected' : '' ?>>Trống</option>
                    <?php if (!$isEdit): ?>
                    <option value="Đầy" <?= ($room['trangthai'] ?? '') === 'Đầy' ? 'selected' : '' ?>>Đầy</option>
                    <?php endif; ?>
                    <option value="Bảo Trì" <?= ($room['trangthai'] ?? '') === 'Bảo Trì' ? 'selected' : '' ?>>Bảo Trì</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">✓ <?= $isEdit ? 'Cập Nhật' : 'Thêm Mới' ?></button>
                <a href="<?= BASE_URL ?>room" class="btn btn-secondary">✕ Hủy</a>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var maphongInput = document.getElementById('maphongInput');
            var sophongInput = document.getElementById('sophongInput');
            var toaInput = document.getElementById('toaInput');
            var toaError = document.getElementById('toaError');
            var validToas = ['A', 'B', 'C', 'D'];

            if (maphongInput && sophongInput && toaInput) {
                maphongInput.addEventListener('input', function() {
                    var val = this.value;
                    var numbers = val.replace(/\D/g, '');
                    sophongInput.value = numbers;
                    var letters = val.replace(/[^a-zA-Z]/g, '').toUpperCase();
                    if (letters.length > 0) {
                        var firstLetter = letters.charAt(0);
                        if (validToas.includes(firstLetter)) {
                            toaInput.value = firstLetter;
                            toaError.style.display = 'none';
                            maphongInput.setCustomValidity('');
                        } else {
                            toaInput.value = '';
                            toaError.style.display = 'block';
                            maphongInput.setCustomValidity('Tòa không tồn tại!');
                        }
                    } else {
                        toaInput.value = '';
                        toaError.style.display = 'none';
                        maphongInput.setCustomValidity('');
                    }
                });
            }
        });
    </script>
</body>
</html>