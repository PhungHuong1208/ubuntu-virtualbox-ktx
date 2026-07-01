<?php
$pageTitle = 'Thông tin Sự cố Của Tôi';
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

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Mã Sự Cố</th>
                        <th>Mã Phòng</th>
                        <th>Mô Tả</th>
                        <th>Ngày Báo</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($incidents)): ?>
                        <tr>
                            <td colspan="5" class="empty-row">Bạn chưa ghi nhận sự cố nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($incidents as $incident): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($incident['masuco'] ?? '') ?></strong></td>
                                <td><?= htmlspecialchars($incident['maphong'] ?? '') ?></td>
                                <td><?= htmlspecialchars($incident['mota'] ?? '') ?></td>
                                <td><?= htmlspecialchars($incident['ngaybao'] ?? '') ?></td>
                                <td><?= htmlspecialchars($incident['trangthai'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            <a href="<?= BASE_URL ?>incident/baocao" class="btn btn-success">✍️ Báo cáo sự cố mới</a>
        </div>
    </div>
</body>
</html>