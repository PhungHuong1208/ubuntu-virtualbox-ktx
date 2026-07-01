<?php

class ContractModel {
    private $apiUrl = 'http://localhost/webktx/QuanLyKTX_API/Routes/apiUser.php';

    public function timhopdong(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $masv = $_SESSION['masv'] ?? null;
        if (empty($masv)) {
            return null;
        }

        return $this->findByMaSV($masv);
    }

    public function findByMaSV($masv) {
        $url = $this->apiUrl . '?action=contract&masv=' . urlencode($masv);
        $result = @file_get_contents($url);
        if ($result === false) return null;
        
        $response = json_decode($result, true);
        if ($response && $response['status'] === 'success') {
            return $response['data'];
        }
        return null;
    }
}



?>