<?php
// Simple autoloader
spl_autoload_register(function ($class) {
    // Convert namespace to full file path
    $base_dir = __DIR__ . '/../';
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

session_start();

// Define base URL for views
define('BASE_URL', '/QuanLyKTX_API/Public/');
define('PUBLIC_URL', '/QuanLyKTX_API/Public/');

// Tự động nhận diện thư mục gốc để linh hoạt cho mọi môi trường
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

if (strpos($request_uri, $base_dir) === 0) {
    $uri = substr($request_uri, strlen($base_dir));
} else {
    $uri = $request_uri;
}

// Loại bỏ 'index.php' nếu người dùng gõ trực tiếp trên URL
$uri = str_replace('/index.php', '', $uri);
$uri = trim($uri, '/');

// file_put_contents(__DIR__ . '/debug_global.log', "URI Global: $uri, Request URI: " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);

if (empty($uri)) {
    // Mặc định chuyển hướng vào Dashboard nếu URL trống
    $uri = 'auth/dashboard';
}

// REST API Interceptor
if (strpos($uri, 'api/') === 0) {
    $_GET['api'] = 1; // Ép hệ thống trả về JSON
    $apiRoutes = require __DIR__ . '/../Routes/api.php';
    $method = $_SERVER['REQUEST_METHOD'];
    
    // DEBUG: Ghi lại log để xem chuyện gì đang xảy ra
    // file_put_contents(__DIR__ . '/debug.log', "URI: $uri, Method: $method\n", FILE_APPEND);
    
    // Fallback cho form HTML thỉnh thoảng dùng _method=PUT
    if ($method === 'POST' && isset($_POST['_method'])) {
        $method = strtoupper($_POST['_method']);
    }

    if (isset($apiRoutes[$method])) {
        foreach ($apiRoutes[$method] as $route => $action) {
            // Biến route dạng api/rooms/{id} thành regex
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $route);
            if (preg_match("#^$pattern$#", $uri, $matches)) {
                array_shift($matches); // Bỏ full match
                list($controllerName, $methodName) = explode('@', $action);
                $controllerClass = '\\Controllers\\' . $controllerName;
                
                if (class_exists($controllerClass)) {
                    $instance = new $controllerClass();
                    
                    // Bypass isPost() strict check in controllers by faking SERVER method for compatibility
                    // Or let controller logic handle it since getInput parses raw JSON payload
                    $_SERVER['REQUEST_METHOD'] = 'POST'; // Hack nhỏ để các hàm store()/update() vốn đang check isPost() hoạt động
                    
                    if (method_exists($instance, $methodName)) {
                        call_user_func_array([$instance, $methodName], $matches);
                        exit;
                    }
                }
            }
        }
    }
    
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'API Endpoint Not Found']);
    exit;
}

$segments = explode('/', $uri);
$controllerName = ucfirst($segments[0]) . 'Controller';
$methodName = isset($segments[1]) ? $segments[1] : 'index';
$param = isset($segments[2]) ? $segments[2] : null;

// Require the router maps or just dynamically dispatch
$controllerClass = '\\Controllers\\' . $controllerName;

if (class_exists($controllerClass)) {
    $controllerInstance = new $controllerClass();
    if (method_exists($controllerInstance, $methodName)) {
        if ($param !== null) {
            $controllerInstance->$methodName($param);
        } else {
            $controllerInstance->$methodName();
        }
    } else {
        http_response_code(404);
        echo "404 - Phương thức không tồn tại: $methodName";
    }
} else {
    http_response_code(404);
    echo "404 - Controller không tồn tại: $controllerName";
}