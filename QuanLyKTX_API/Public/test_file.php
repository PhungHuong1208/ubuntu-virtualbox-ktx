<?php
$url = 'http://localhost/HellomynameisPencilan/QuanLyKTX_API/Public/api/user/profile?masv=SV001';
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "Accept: application/json\r\n"
    ]
];
$context = stream_context_create($opts);
$result = @file_get_contents($url, false, $context);
echo "Result: $result\n";
echo "Headers: ";
print_r($http_response_header);
