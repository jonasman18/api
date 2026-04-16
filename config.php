<?php
$host = 'maglev.proxy.rlwy.net';
$port = '18393';
$db = 'railway';
$user = 'root';
$pass = 'UPIkpSmNXtkeJdrgceOWFkfObvTiDHxs';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";

try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}
?>