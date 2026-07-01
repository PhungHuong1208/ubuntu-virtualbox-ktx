<?php
/**
 * Room Model
 * Xử lý business logic và database operations cho Phòng
 */

class RoomModel {
    private $apiUrl = 'http://192.168.190.128:8080/QuanLyKTX_API/Routes/apiUser.php';
    public function timphong(){
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
        $url = $this->apiUrl . '?action=room&masv=' . urlencode($masv);
        $result = @file_get_contents($url);
        if ($result === false) return [];
        
        $response = json_decode($result, true);
        if ($response && $response['status'] === 'success') {
            return $response['data'];
        }
        return [];
    }
}
?>
