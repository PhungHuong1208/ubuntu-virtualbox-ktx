<?php
require_once __DIR__ . '/../Config/Database.php';

$db = \Config\Database::getConnection();

// Kiểm tra xem cột masv đã tồn tại chưa
$result = $db->query("SHOW COLUMNS FROM suco LIKE 'masv'");
if ($result->num_rows == 0) {
    // Thêm cột masv
    $db->query("ALTER TABLE suco ADD masv VARCHAR(30) NULL AFTER masuco");
    // Thêm khóa ngoại
    $db->query("ALTER TABLE suco ADD CONSTRAINT fk_suco_sinhvien FOREIGN KEY (masv) REFERENCES sinhvien(masv) ON DELETE SET NULL");
    echo json_encode(['status' => 'success', 'message' => 'Đã thêm cột masv và khóa ngoại thành công']);
} else {
    echo json_encode(['status' => 'info', 'message' => 'Cột masv đã tồn tại']);
}
