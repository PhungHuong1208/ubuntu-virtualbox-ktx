<?php
spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/../';
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Giả lập $_GET
$_GET['masv'] = 'SV001';
$_SERVER['REQUEST_METHOD'] = 'GET';

$controller = new \Controllers\UserController();
echo "Testing getProfile():\n";
// Sẽ exit vì jsonResponse gọi exit
$controller->getProfile();
