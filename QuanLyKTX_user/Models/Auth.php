<?php
/**
 * Auth Repository
 * Data Access Layer cho Authentication
 */

class AuthModel {
    private $apiUrl = 'http://localhost/webktx/QuanLyKTX_API/Routes/apiUser.php';

    public function authenticate($masv, $password) {
        $url = $this->apiUrl . '?action=login';
        $data = http_build_query(['masv' => $masv, 'password' => $password]);
        
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => $data,
                'timeout' => 5,
                'ignore_errors' => true
            ]
        ];
        
        $context  = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        
        if ($result === false) return null;
        
        $response = json_decode($result, true);
        if ($response && $response['status'] === 'success') {
            return $response['data']; // trả về mảng có chứa ['masv', 'hoten']
        }
        return null;
    }

    public function getNameByMaSV($masv) {
        // Có thể tái sử dụng endpoint student để lấy họ tên
        $url = $this->apiUrl . '?action=student&masv=' . urlencode($masv);
        $result = @file_get_contents($url);
        if ($result === false) return '';
        
        $response = json_decode($result, true);
        if ($response && $response['status'] === 'success') {
            return $response['data']['hoten'] ?? '';
        }
        return '';
    }
    public function changePassword($masv, $oldPw, $newPw) {
    if (strlen($newPw) < 3) {
        return ['status' => 'error', 'message' => 'Mật khẩu phải từ 3 ký tự trở lên'];
    }

    // 2. Gửi yêu cầu đến API (giống như hàm authenticate bạn đã viết)
    $url = $this->apiUrl . '?action=change_password';
    $data = http_build_query([
        'masv' => $masv,
        'old_password' => $oldPw,
        'new_password' => $newPw
    ]);

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => $data,
            'timeout' => 5,
            'ignore_errors' => true
        ]
    ];

    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === false) {
        return ['status' => 'error', 'message' => 'Lỗi kết nối máy chủ API'];
    }

    return json_decode($result, true);
}
}
?>