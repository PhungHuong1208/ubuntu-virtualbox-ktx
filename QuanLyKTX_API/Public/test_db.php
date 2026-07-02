<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Config/Database.php';

$db = \Config\Database::getConnection();
$result = $db->query("SELECT maphong FROM phong LIMIT 5");
while ($row = $result->fetch_assoc()) {
    echo $row['maphong'] . "\n";
}
