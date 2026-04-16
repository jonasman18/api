<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, X-Requested-With");

// 🔥 CONNEXION RAILWAY
$host = 'maglev.proxy.rlwy.net';
$port = '18393';
$dbname = 'railway';
$username = 'root';
$password = 'UPIkpSmNXtkeJdrgceOWFkfObvTiDHxs';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Erreur de connexion : ' . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM visiteurs ORDER BY id DESC");
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['nom'], $data['nombre_jours'], $data['tarif_journalier'])) {
        $stmt = $pdo->prepare("INSERT INTO visiteurs (nom, nombre_jours, tarif_journalier) VALUES (?, ?, ?)");
        $stmt->execute([$data['nom'], $data['nombre_jours'], $data['tarif_journalier']]);
        echo json_encode(['message' => 'Insertion réussie.']);
    } else {
        echo json_encode(['error' => 'Données incomplètes.']);
    }

} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'], $data['nom'], $data['nombre_jours'], $data['tarif_journalier'])) {
        $stmt = $pdo->prepare("UPDATE visiteurs SET nom = ?, nombre_jours = ?, tarif_journalier = ? WHERE id = ?");
        $stmt->execute([$data['nom'], $data['nombre_jours'], $data['tarif_journalier'], $data['id']]);
        echo json_encode(['message' => 'Modification réussie.']);
    } else {
        echo json_encode(['error' => 'Données incomplètes pour la mise à jour.']);
    }

} elseif ($method === 'DELETE') {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $pdo->prepare("DELETE FROM visiteurs WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['message' => 'Suppression réussie.']);
    } else {
        echo json_encode(['error' => 'ID non spécifié.']);
    }

} else {
    echo json_encode(['error' => 'Méthode non autorisée.']);
}
?>