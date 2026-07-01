<?php
// Danh sách tiền điện và tiền nước
$tab = $tab ?? 'electricity';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/list.css?v=<?= time() ?>">
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h1><?= $title ?></h1>
            <a href="<?= BASE_URL ?>auth/dashboard" class="back-link">← Quay lại trang chính</a>
        </div>
        <div class="header-actions">
            <a href="<?= BASE_URL ?>utility/export?type=<?= $tab ?>" class="btn btn-success">Xuất Excel</a>
            <a href="<?= BASE_URL ?>utility/import" class="btn btn-success">Nhập Excel</a>
            <a href="<?= BASE_URL ?>utility/create?type=<?= $tab ?>" class="btn btn-primary">+ Tính tiền <?= $tab === 'water' ? 'Nước' : 'Điện' ?></a>
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

    <div class="tabs">
        <button class="tab-btn <?= $tab === 'electricity' ? 'active' : '' ?>" onclick="switchTab('electricity')">⚡ Tiền Điện</button>
        <button class="tab-btn <?= $tab === 'water' ? 'active' : '' ?>" onclick="switchTab('water')">🚰 Tiền Nước</button>
    </div>

    <form method="post" action="<?= BASE_URL ?>utility/search" class="search-box">
        <input type="text" name="search" placeholder=" 🔍 Tìm  mã phòng..." value="<?= isset($keyword) ? htmlspecialchars($keyword) : '' ?>">
        <input type="hidden" name="tab" value="<?= $tab ?>">
        <button type="submit" class="btn btn-primary">Tìm Kiếm</button>
        
        <?php if (!empty($keyword)): ?>
            <a href="<?= BASE_URL ?>utility?tab=<?= $tab ?>" class="btn btn-secondary">Reset</a>
        <?php endif; ?>
    </form>

    <?php
    function formatUtilityCode($value, $prefix) {
        $value = trim($value);
        if (preg_match('/^' . preg_quote($prefix, '/') . '\d+$/i', $value)) { return strtoupper($value); }
        $number = preg_replace('/\D+/', '', $value);
        if ($number === '') { $number = '1'; }
        return $prefix . str_pad(intval($number), 3, '0', STR_PAD_LEFT);
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th><?= $tab === 'water' ? 'Mã HĐ Nước' : 'Mã HĐ Điện' ?></th>
                <th>Mã Phòng</th>
                <th>Số tiền (VNĐ)</th>
                <th>Ngày tính</th>
                <th>Trạng thái</th>
                <th style="text-align: center;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data)): ?>
                <?php $i = 0; ?>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= ++$i ?></td>
                        <td><strong><?= $tab === 'water' ? htmlspecialchars(formatUtilityCode($row['matn'], 'TN')) : htmlspecialchars(formatUtilityCode($row['matd'], 'TD')) ?></strong></td>
                        <td><?= htmlspecialchars($row['maphong']) ?></td>
                        <td class="amount-column"><?= number_format($tab === 'water' ? $row['gianuoc'] : $row['giadien']) ?></td>
                        <td><?= htmlspecialchars($row['ngay']) ?></td>
                        <td>
                            <span class="<?= strpos(strtolower($row['trangthai']), 'đã') !== false ? 'status-paid' : 'status-unpaid' ?>">
                                <?= htmlspecialchars($row['trangthai']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-links">
                                <?php if (strpos(strtolower($row['trangthai']), 'chưa') !== false): ?>
                                    <a href="<?= BASE_URL ?>utility/pay<?= $tab === 'water' ? 'Water' : 'Electricity' ?>/<?= $tab === 'water' ? htmlspecialchars(formatUtilityCode($row['matn'], 'TN')) : htmlspecialchars(formatUtilityCode($row['matd'], 'TD')) ?>" class="btn-action btn-pay" onclick="return confirm('Xác nhận đã thanh toán?')"> 💳 Thanh toán</a>
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>utility/delete<?= $tab === 'water' ? 'Water' : 'Electricity' ?>/<?= $tab === 'water' ? htmlspecialchars(formatUtilityCode($row['matn'], 'TN')) : htmlspecialchars(formatUtilityCode($row['matd'], 'TD')) ?>" class="btn-action btn-delete" onclick="return confirm('Xoá hóa đơn này?')"> 🗑️ Xóa</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <?php if (isset($keyword)): ?>
                                <p> 📭 Không tìm thấy kết quả cho "<?= htmlspecialchars($keyword) ?>"</p>
                            <?php else: ?>
                                <p> 📭 Chưa có hóa đơn nào</p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
function switchTab(tabName) {
    window.location.href = '<?= BASE_URL ?>utility?tab=' + tabName;
}
</script>
</body>
</html>