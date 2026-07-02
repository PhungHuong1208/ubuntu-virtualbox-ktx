<?php
// Debug: Test the room API directly with SV001 to check data
$masv = 'SV001';
$url = 'http://localhost/HellomynameisPencilan/QuanLyKTX_API/Routes/apiUser.php?action=room&masv=' . urlencode($masv);
$result = @file_get_contents($url);
echo "=== Room API cho SV001 ===\n";
echo "URL: $url\n";
echo "Raw: $result\n\n";

$decoded = json_decode($result, true);
echo "Status: " . ($decoded['status'] ?? 'unknown') . "\n";
echo "Data: " . json_encode($decoded['data'] ?? null, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test SV002
$masv2 = 'SV002';
$url2 = 'http://localhost/HellomynameisPencilan/QuanLyKTX_API/Routes/apiUser.php?action=room&masv=' . urlencode($masv2);
$result2 = @file_get_contents($url2);
echo "=== Room API cho SV002 ===\n";
$decoded2 = json_decode($result2, true);
echo "Status: " . ($decoded2['status'] ?? 'unknown') . "\n";
echo "Data: " . json_encode($decoded2['data'] ?? null, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Check DB directly
require_once __DIR__ . '/../Config/Database.php';
$db = \Config\Database::getConnection();
$res = $db->query("SELECT h.masv, h.maphong, h.trangthai, h.batdau, h.hethan FROM hopdong h WHERE h.trangthai = 'Đang Hoạt Động' LIMIT 5");
echo "=== Hợp đồng đang hoạt động trong DB ===\n";
while ($row = $res->fetch_assoc()) {
    echo "masv: {$row['masv']}, phong: {$row['maphong']}, trang thai: {$row['trangthai']}\n";
}
