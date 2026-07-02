<?php
// Test script for API
$base_url = 'http://localhost/HellomynameisPencilan/QuanLyKTX_API/Public/';

function test_api($method, $endpoint, $data = []) {
    global $base_url;
    $url = $base_url . $endpoint;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    if (!empty($data)) {
        if ($method === 'GET') {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            // Send as JSON
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
        }
    } else {
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "[$method] $endpoint -> HTTP $httpCode\n";
    echo "Response: $response\n\n";
}

echo "--- BẮT ĐẦU TEST USER API ---\n";

// 1. Test Profile (GET)
test_api('GET', 'api/user/profile', ['masv' => 'SV001']);

// 2. Test Report Incident (POST)
test_api('POST', 'api/user/incidents', [
    'masv' => 'SV001',
    'maphong' => 'P101',
    'mota' => 'Bóng đèn bị cháy (Test từ API)',
    'ngaybao' => date('Y-m-d')
]);

// 3. Test Incidents List (GET)
test_api('GET', 'api/user/incidents', ['masv' => 'SV001']);
