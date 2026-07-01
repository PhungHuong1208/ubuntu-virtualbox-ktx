<?php
// Chuẩn hóa biến dùng trong view:
$pageTitle = 'Danh sách sinh viên - Phòng ' . ($room['sophong'] ?? '');
$students  = $room['students'] ?? [];

// Đảm bảo $students là mảng
if (!is_array($students)) {
    $students = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/danhsach.css?v=<?= time() ?>">
</head>
<body>
    <div class="container">
        <div class="header">
            <a class="back-link" href="<?= BASE_URL ?>room">← Quay lại danh sách phòng</a>
            <h1><?= htmlspecialchars($pageTitle) ?></h1>
        </div>

        <?php if (empty($students)): ?>
            <div class="empty-state">
                <p>📭 Chưa có sinh viên nào đang ở trong phòng này.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Số Phòng</th>
                            <th>Mã SV</th>
                            <th>Họ Tên</th>
                            <th>Lớp</th>
                            <th>Giới Tính</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $st): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($room['sophong'] ?? '') ?></strong></td>
                                <td><?= htmlspecialchars($st['masv'] ?? '') ?></td>
                                <td><?= htmlspecialchars($st['hoten'] ?? '') ?></td>
                                <td><?= htmlspecialchars($st['lop'] ?? '') ?></td>
                                <td><?= htmlspecialchars($st['gioitinh'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>