<?php
/**
 * Base Controller Class
 * Tất cả các controller khác sẽ kế thừa từ class này
 */

abstract class Controller {
    protected $data = [];

    /**
     * Load một view file
     * @param string $view - Tên file view (ví dụ: 'list', 'form')
     * @param array $data - Dữ liệu để truyền vào view
     */
    protected function view($view, $data = []) {
        $this->data = $data;
        
        // Xác định đường dẫn view dựa trên vị trí thực tế của Controller con
        $reflector = new ReflectionClass($this);
        $controllerDir = dirname($reflector->getFileName());
        
        // $controllerDir đang là .../Modules/user/Auth/Controllers
        // Vậy thư mục view sẽ là .../Modules/user/Auth/Views
        // Tách "auth/login" → ["auth", "login"] → "Auth/login.php"
        $viewParts = explode('/', $view);
        if (count($viewParts) >= 2) {
            $viewParts[0] = ucfirst($viewParts[0]); // auth → Auth
        }
        $viewPath = $controllerDir . '/../Views/' . implode('/', $viewParts) . '.php';
        
        if (!file_exists($viewPath)) {
            die('View file not found: ' . $viewPath);
        }

        // Trích xuất dữ liệu để dễ truy cập trong view
        extract($this->data);
        
        // Load view
        include $viewPath;
    }

    /**
     * Lấy tên module từ tên class
     * @return string tên module
     */
    protected function getModuleName() {
        $class = get_class($this);
        // Tách StudentController -> Student
        return str_replace('Controller', '', $class);
    }

    /**
     * Redirect tới URL khác
     * @param string $url - URL hoặc action
     */
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirect quay lại trang trước
     */
    protected function redirectBack() {
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header('Location: ' . $referer);
        exit;
    }

    /**
     * Gửi JSON response
     * @param mixed $data - Dữ liệu để gửi
     * @param int $statusCode - HTTP status code
     */
    protected function json($data, $statusCode = 200) {
        header('Content-Type: application/json', true, $statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * Kiểm tra xem request có phải POST không
     * @return bool
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Kiểm tra xem request có phải GET không
     * @return bool
     */
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Lấy giá trị từ $_GET hoặc $_POST
     * @param string $key - Tên key
     * @param mixed $default - Giá trị mặc định
     * @return mixed
     */
    protected function getInput($key, $default = null) {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        return $default;
    }

    /**
     * Kiểm tra session có tồn tại không
     * @param string $key - Tên key session
     * @return bool
     */
    protected function hasSession($key) {
        return isset($_SESSION[$key]);
    }

    /**
     * Lấy giá trị session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getSession($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Thiết lập session
     * @param string $key
     * @param mixed $value
     */
    protected function setSession($key, $value) {
        $_SESSION[$key] = $value;
    }
}
?>
