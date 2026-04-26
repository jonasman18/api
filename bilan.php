<?php
require_once __DIR__ . '/verifyJWT.php';

header("Content-Type: application/json");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 🔐 Vérification JWT
$authUser = requireAuth();

// 🔥 CONNEXION RAILWAY
$host     = 'maglev.proxy.rlwy.net';
$port     = '18393';
$dbname   = 'railway';
$username = 'root';
$password = 'UPIkpSmNXtkeJdrgceOWFkfObvTiDHxs';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de connexion']);
    exit;
}

$stmt   = $pdo->query("SELECT nombre_jours * tarif_journalier AS total FROM visiteurs");
$totaux = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    "total"   => array_sum($totaux),
    "minimum" => count($totaux) ? min($totaux) : 0,
    "maximum" => count($totaux) ? max($totaux) : 0,
]);