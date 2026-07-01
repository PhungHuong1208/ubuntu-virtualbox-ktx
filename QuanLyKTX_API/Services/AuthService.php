<?php
namespace Services;

use Models\AuthRepository;

class AuthService {
    private $authRepo;

    public function __construct() {
        $this->authRepo = new AuthRepository();
    }

    /**
     * Xác thực thông tin đăng nhập
     */
    public function authenticate($username, $password) {
        if (empty($username) || empty($password)) {
            throw new \Exception("Tên đăng nhập và mật khẩu không được để trống!");
        }

        $user = $this->authRepo->findByUsername($username);

        if (!$user) {
            throw new \Exception("Tên đăng nhập không tồn tại!");
        }

        // Trong hệ thống cũ, mật khẩu được lưu dạng plain text
        // (Khuyến cáo: Nên dùng password_hash() và password_verify() cho các hệ thống mới)
        if ($user['password'] !== $password) {
            throw new \Exception("Mật khẩu không chính xác!");
        }

        return $user;
    }
}
