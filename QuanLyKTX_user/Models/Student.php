<?php
/**
 * Student Model
 * Xử lý business logic liên quan đến sinh viên
 */

class StudentModel {
private $apiUrl = 'http://192.168.190.128:8080/QuanLyKTX_API/Routes/apiUser.php';
    public function timmasv(){
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
        $url = $this->apiUrl . '?action=student&masv=' . urlencode($masv);
        $result = @file_get_contents($url);
        if ($result === false) return null;
        
        $response = json_decode($result, true);
        if ($response && $response['status'] === 'success') {
            return $response['data'];
        }
        return null;
    }

    public function updateStudent($masv, $data) {
        $url = $this->apiUrl . '?action=student_update';
        $data['masv'] = $masv;
        $postData = http_build_query($data);
        
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => $postData,
                'timeout' => 5
            ]
        ];
        
        $context  = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        
        if ($result === false) return false;
        
        $response = json_decode($result, true);
        return ($response && $response['status'] === 'success');
    }
}
     

?>