<?php
/**
 * User Router
 * Định tuyến cho app User, chỉ nhận dữ liệu từ API admin
 */

if (session_status() === PHP_SESSION_NONE) {
    session_name('USER_KTX_STUDENT');
    session_start();
}

$url = $_GET['url'] ?? '';
$path = trim($url, '/');

// bỏ query string nếu có
if (($qpos = strpos($path, '?')) !== false) {
    $path = substr($path, 0, $qpos);
}

$segments = explode('/', filter_var($path, FILTER_SANITIZE_URL));
$segments = array_values(array_filter($segments));

// shortcut: /dashboard → auth/dashboard
if (isset($segments[0]) && $segments[0] === 'dashboard') {
    $segments = ['auth', 'dashboard'];
}

// mặc định: / → auth/index
$module = $segments[0] ?? 'auth';
$action = $segments[1] ?? 'index';
$params = array_slice($segments, 2);

// đường dẫn controller theo cấu trúc mới
$controllerName = ucfirst($module) . 'Controller';
$controllerPath = BASE_PATH . '/Controllers/' . $controllerName . '.php';

if (!file_exists($controllerPath)) {
    // nếu không có controller, fallback về login hoặc dashboard
    if ($_SESSION['user_id'] ?? null) {
        header('Location: ' . BASE_URL . 'auth/dashboard');
    } else {
        header('Location: ' . BASE_URL . 'auth');
    }
    exit;
}

require_once $controllerPath;

if (!class_exists($controllerName)) {
    die('Class không tồn tại: ' . htmlspecialchars($controllerName));
}

$controller = new $controllerName();

$method = $action;
if (!method_exists($controller, $method)) {
    // fallback về index nếu action không tồn tại
    if (method_exists($controller, 'index')) {
        $method = 'index';
        $params = [];
    } else {
        die('Method không tồn tại: ' . htmlspecialchars($method));
    }
}

call_user_func_array([$controller, $method], $params);
