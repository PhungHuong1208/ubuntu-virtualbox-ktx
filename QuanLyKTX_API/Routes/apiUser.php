<?php
// Routes/apiUser.php
// Đây là API endpoint trung gian để QuanLyKTX_user giao tiếp với backend.
// Dùng autoloader chuẩn giống index.php để đảm bảo namespace đúng.

// ĐẶT TÊN SESSION RIÊNG trước session_start() để tránh xung đột với session admin
// Admin dùng tên mặc định PHPSESSID, User dùng USER_KTX_STUDENT
session_name('USER_KTX_STUDENT');
session_start();

// Autoloader giống index.php
spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/../';
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Xử lý preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$controller = new \Controllers\UserController();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        // POST: masv, password
        $controller->login();
        break;

    case 'student':
        // GET/POST: masv
        $controller->getProfile();
        break;

    case 'student_update':
        // POST: masv, hoten, lop, gioitinh, cccd, sodienthoai, email, diachi
        $controller->updateProfile();
        break;

    case 'change_password':
        // POST: masv, old_password, new_password
        $controller->updatePassword();
        break;

    case 'room':
        // GET/POST: masv
        $controller->getRoom();
        break;

    case 'contract':
        // GET/POST: masv
        $controller->getContracts();
        break;

    case 'incident':
        // GET/POST: masv
        $controller->getIncidents();
        break;

    case 'reportIncident':
        // POST: masv, maphong, mota, ngaybao
        $controller->createIncident();
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'status'  => 'error',
            'message' => "Action '$action' không tồn tại",
            'available_actions' => [
                'login', 'student', 'student_update', 'change_password',
                'room', 'contract', 'incident', 'reportIncident'
            ]
        ]);
        break;
}
