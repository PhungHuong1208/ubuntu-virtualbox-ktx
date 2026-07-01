<?php
$isEdit = isset($payment);
$pageTitle = $isEdit ? 'Chỉnh Sửa Thanh Toán' : 'Thêm Thanh Toán Mới';
$actionUrl = $isEdit ? BASE_URL . 'payment/update/' . $payment['mathanhtoan'] : BASE_URL . 'payment/store';
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
                <a href="<?= BASE_URL ?>payment" class="back-link">← Quay lại Danh Sách</a>
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
                    <label for="mathanhtoan">Mã Thanh Toán</label>
                    <input type="text" id="mathanhtoan" name="mathanhtoan" value="<?= htmlspecialchars($isEdit ? ('TT' . str_pad($payment['mathanhtoan'], 3, '0', STR_PAD_LEFT)) : ($nextPaymentCode ?? '')) ?>" placeholder="VD: TT001" readonly class="readonly-field">
                </div>
                <div class="form-group required">
                    <label for="maphong">Mã Phòng</label>
                    <select id="maphong" name="maphong" class="select2-search" required>
                        <option value="">-- Chọn Phòng --</option>
                        <?php foreach ($rooms ?? [] as $room): ?>
                            <?php $selected = ($isEdit && $payment['maphong'] === $room['maphong']) ? 'selected' : ''; ?>
                            <option value="<?= htmlspecialchars($room['maphong']) ?>" <?= $selected ?>>
                                <?= htmlspecialchars($room['maphong'] . ' - Tòa ' . $room['toa']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group required">
                    <label for="sotien">Số Tiền (VNĐ)</label>
                    <input type="text" id="sotien" name="sotien" value="<?= htmlspecialchars($payment['sotien'] ?? '') ?>" placeholder="0" readonly class="readonly-field">
                </div>
                <div class="form-group required">
                    <label for="ngaytra">Hạn Trả</label>
                    <input type="date" id="ngaytra" name="ngaytra" value="<?= htmlspecialchars($payment['ngaytra'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group required">
                <label for="trangthai">Trạng Thái</label>
                <input type="text" id="trangthai" name="trangthai" value="<?= htmlspecialchars($payment['trangthai'] ?? 'Chưa Thanh Toán') ?>" readonly class="readonly-field">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">✓ <?= $isEdit ? 'Cập Nhật' : 'Thêm Mới' ?></button>
                <a href="<?= BASE_URL ?>payment" class="btn btn-secondary">✕ Hủy</a>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-search').select2({ width: '100%' });

            function loadPrice() {
                var maphong = $('#maphong').val();
                if(maphong) {
                    $.ajax({
                        url: '<?= BASE_URL ?>payment/getRoomPrice',
                        type: 'POST',
                        data: { maphong: maphong },
                        dataType: 'json',
                        success: function(response) {
                            if(response && response.success) {
                                $('#sotien').val(response.gia);
                            } else {
                                $('#sotien').val('0');
                            }
                        },
                        error: function() { $('#sotien').val('0'); }
                    });
                } else {
                    $('#sotien').val('0');
                }
            }

            $('#maphong').on('change', loadPrice);
            loadPrice();
        });
    </script>
</body>
</html>