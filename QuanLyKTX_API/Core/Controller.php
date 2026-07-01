<?php
namespace Core;

class Controller {
    // Phương thức gọi view (trả về HTML)
    protected function view($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View không tồn tại: " . $view);
        }
    }

    // Phương thức trả về JSON (dành cho API)
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    // Phương thức chuyển hướng
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }

    // Kiểm tra request là POST
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    // Kiểm tra request là GET
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    // Lấy dữ liệu an toàn từ POST/GET
    protected function getInput($key, $default = null) {
        if (isset($_POST[$key])) {
            return trim($_POST[$key]);
        }
        if (isset($_GET[$key])) {
            return trim($_GET[$key]);
        }
        // Thử lấy từ JSON body nếu là dạng raw (fetch API)
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if ($data && isset($data[$key])) {
            return trim($data[$key]);
        }
        return $default;
    }

    // Kiểm tra request method chung
    protected function isRequestMethod($method) {
        return $_SERVER['REQUEST_METHOD'] === strtoupper($method);
    }

    // Yêu cầu đăng nhập mới được truy cập
    protected function requireAuth() {
        if (!isset($_SESSION['username'])) {
            // Nếu gọi qua API
            if (isset($_GET['api']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                $this->jsonResponse(['error' => 'Unauthorized. Please login first.'], 401);
            }
            // Nếu là trình duyệt
            $this->redirect(BASE_URL . 'auth/login');
        }
    }
}
