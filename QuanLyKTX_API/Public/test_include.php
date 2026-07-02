<?php
$file = __DIR__ . '/../Config/Database.php';
echo "File path: $file\n";
echo "File exists: " . (file_exists($file) ? 'YES' : 'NO') . "\n";
require_once $file;
echo "Class exists: " . (class_exists('Config\Database') ? 'YES' : 'NO') . "\n";
