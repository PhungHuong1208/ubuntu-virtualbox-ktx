<?php
$url = 'http://localhost/HellomynameisPencilan/QuanLyKTX_API/Public/api/user/incidents';
$data = json_encode([
    'masv' => 'SV001',
    'maphong' => 'A101',
    'mota' => 'Bóng đèn bị cháy',
    'ngaybao' => date('Y-m-d')
]);

$opts = [
    "http" => [
        "method" => "POST",
        "header" => "Content-Type: application/json\r\nAccept: application/json\r\n",
        "content" => $data
    ]
];
$context = stream_context_create($opts);
$result = @file_get_contents($url, false, $context);
echo "Result: $result\n";
echo "Headers: ";
print_r($http_response_header);
