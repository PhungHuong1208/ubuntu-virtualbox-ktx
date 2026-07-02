<?php
// Test login qua apiUser.php (giống cách QuanLyKTX_user gọi)
$url = 'http://localhost/HellomynameisPencilan/QuanLyKTX_API/Routes/apiUser.php?action=login';
$data = http_build_query(['masv' => 'SV001', 'password' => '123456']);

$opts = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'content' => $data,
        'timeout' => 5
    ]
];
$context = stream_context_create($opts);
$result = @file_get_contents($url, false, $context);

echo "=== Kết quả Login ===\n";
echo "Raw: $result\n\n";
$decoded = json_decode($result, true);
echo "Status: " . ($decoded['status'] ?? 'unknown') . "\n";
if (isset($decoded['data'])) {
    echo "masv: " . $decoded['data']['masv'] . "\n";
    echo "hoten: " . $decoded['data']['hoten'] . "\n";
} else {
    echo "Error: " . ($decoded['message'] ?? 'Không có phản hồi') . "\n";
}

// Thêm: kiểm tra mật khẩu nào đúng bằng cách hỏi DB trực tiếp
echo "\n=== Kiểm tra tài khoản trong DB ===\n";
require_once __DIR__ . '/../Config/Database.php';
$db = \Config\Database::getConnection();
$res = $db->query("SELECT masv, password FROM taikhoan_user LIMIT 5");
while ($row = $res->fetch_assoc()) {
    echo "masv: {$row['masv']}, password: {$row['password']}\n";
}
