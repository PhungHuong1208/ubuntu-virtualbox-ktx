<?php
namespace Controllers;

use Core\Controller;
use Services\AuthService;

class AuthController extends Controller {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    /**
     * Màn hình hoặc API đăng nhập
     */
    public function login() {
        // Nếu dùng API và gọi GET (không hợp lệ)
        if ($this->wantsJson() && !$this->isRequestMethod('POST')) {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        // Nếu là POST (Submit form hoặc API call)
        if ($this->isRequestMethod('POST')) {
            $username = $this->getInput('username');
            $password = $this->getInput('password');

            try {
                $user = $this->authService->authenticate($username, $password);
                
                // Lưu Session (Cho giao diện web)
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['username']; // Tương thích với code cũ

                if ($this->wantsJson()) {
                    return $this->jsonResponse([
                        'success' => true, 
                        'message' => 'Đăng nhập thành công',
                        'token' => session_id(), // Fake token đơn giản dựa trên session
                        'user' => $user
                    ]);
                }

                // Redirect về dashboard cho HTML
                $this->redirect(BASE_URL . 'auth/dashboard');

            } catch (\Exception $e) {
                if ($this->wantsJson()) {
                    return $this->jsonResponse(['error' => $e->getMessage()], 401);
                }
                
                $_SESSION['error'] = $e->getMessage();
                $this->redirect(BASE_URL . 'auth/login');
            }
        }

        // Nếu đã đăng nhập, tự động sang dashboard
        if (isset($_SESSION['username'])) {
            $this->redirect(BASE_URL . 'auth/dashboard');
        }

        $this->view('auth/login');
    }

    /**
     * Màn hình trang chủ quản trị
     */
    public function dashboard() {
        $this->requireAuth();
        $this->view('auth/dashboard');
    }

    /**
     * Đăng xuất
     */
    public function logout() {
        session_destroy();
        
        if ($this->wantsJson()) {
            return $this->jsonResponse(['success' => true, 'message' => 'Đăng xuất thành công']);
        }
        
        $this->redirect(BASE_URL . 'auth/login');
    }

    private function wantsJson() {
        return isset($_GET['api']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}
