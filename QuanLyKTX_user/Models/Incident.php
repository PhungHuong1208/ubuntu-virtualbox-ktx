<?php
/**
 * Incident Model
 * Xử lý business logic và database operations cho Sự Cố
 */

class IncidentModel {
    private $apiUrl = 'http://192.168.190.128:8080/QuanLyKTX_API/Routes/apiUser.php';

    public function timsuco(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $masv = $_SESSION['masv'] ?? null;
        if (empty($masv)) {
            return null;
        }

        return $this->findByMasv($masv);
    }

    public function findByMasv($masv) {
        $url = $this->apiUrl . '?action=incident&masv=' . urlencode($masv);
        $result = @file_get_contents($url);
        if ($result === false) return [];
        
        $response = json_decode($result, true);
        if ($response && $response['status'] === 'success') {
            return $response['data'];
        }
        return [];
    }

    public function sendIncidentRequest($masv, $maphong, $mota, $ngaybao) {
    $url = $this->apiUrl . '?action=reportIncident';
    $postData = [
        'masv' => $masv,
        'maphong' => $maphong,
        'mota' => $mota,
        'ngaybao' => $ngaybao
    ];

    // Thiết lập context cho POST request
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($postData)
        ]
    ];

    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === false) return ['status' => 'error', 'message' => 'Không thể kết nối API'];

    $response = json_decode($result, true);
    if ($response && $response['status'] === 'success') {
        return ['status' => 'success', 'message' => $response['message']];
    }
    return ['status' => 'error', 'message' => 'Gửi yêu cầu thất bại'];
    }

}
?>
