<?php
/**
 * Auth Controller
 * Xử lý logic xác thực người dùng
 */


require_once __DIR__ . '/../Models/Auth.php';

class AuthController extends Controller {
    private $authRepo;

    public function __construct() {
        $this->authRepo = new AuthModel();
    }

    /**
     * Trang login
     */
    public function index() {
        // Nếu đã login, redirect đến dashboard
        if ($this->getSession('user_id')) {
            $this->redirect(BASE_URL . 'auth/dashboard');
        }

        $this->view('auth/login');
    }

    /**
     * Xử lý login
     */
    public function login() {
        if (!$this->isPost()) {
            $this->redirect(BASE_URL . 'auth');
        }

        $masv = $this->getInput('masv');
        $password = $this->getInput('password');

        // Kiểm tra input
        if (empty($masv) || empty($password)) {
            $_SESSION['error'] = 'Tên đăng nhập và mật khẩu không được để trống!';
            $this->redirect(BASE_URL . 'auth');
        }

        // Xác thực người dùng
        $account = $this->authRepo->authenticate($masv, $password);

        if (!$account) {
            $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không chính xác!';
            $this->redirect(BASE_URL . 'auth');
        }

        // Lưu session - dùng masv làm user_id vì bảng taikhoan_user không có cột id
        $this->setSession('user_id', $account['masv']);
        $this->setSession('masv', $account['masv']);
        if (isset($account['hoten'])) {
            $this->setSession('hoten', $account['hoten']);
        }
        $_SESSION['success'] = 'Đăng nhập thành công!';

        // Redirect tới action dashboard trong module auth
        $this->redirect(BASE_URL . 'auth/dashboard');
    }

    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        $_SESSION = [];
        $this->redirect(BASE_URL . 'auth');
    }

    /**
     * Dashboard
     */
    public function dashboard() {
        // Kiểm tra đã login hay chưa
        if (!$this->getSession('user_id')) {
            $this->redirect(BASE_URL . 'auth');
        }

        // Lấy tên sinh viên từ session (đã lưu khi đăng nhập)
        $hoten = $this->getSession('hoten');

        $data = [
            'masv' => $this->getSession('masv'),
            'user_id' => $this->getSession('user_id'),
            'hoten' => $hoten
        ];

        $this->view('auth/dashboard', $data);
    }
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Kiểm tra mật khẩu xác nhận trước khi gọi repository
            if ($_POST['new_password'] !== $_POST['confirm_password']) {
                $_SESSION['error'] = 'Mật khẩu xác nhận không khớp';
                header("Location: " . BASE_URL . "auth/doimk");
                exit();
            }

            $result = $this->authRepo->changePassword(
                $_SESSION['masv'] ?? '',
                $_POST['old_password'],
                $_POST['new_password']
            );

            if (isset($result['status']) && $result['status'] === 'success') {
                $_SESSION['success'] = "Đổi mật khẩu thành công!";
            } else {
                $_SESSION['error'] = $result['message'] ?? 'Có lỗi xảy ra';
            }

            // Chuyển hướng về trang đổi mật khẩu
            header("Location: " . BASE_URL . "auth/doimk");
            exit();
        }
    }

    /**
     * Hiển thị giao diện Đổi mật khẩu
     */
    public function doimk() {
        // Kiểm tra đã login hay chưa
        if (!$this->getSession('user_id')) {
            $this->redirect(BASE_URL . 'auth');
        }

        $data = [
            'masv'    => $this->getSession('masv'),
            'hoten'   => $this->getSession('hoten')
        ];

        // Gọi ra file View: Views/auth/doimk.php
        $this->view('auth/doimk', $data);
    }
}
?>
