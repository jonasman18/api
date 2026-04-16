<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 🔥 CONNEXION RAILWAY
$host = 'maglev.proxy.rlwy.net';
$port = '18393';
$dbname = 'railway';
$username = 'root';
$password = 'UPIkpSmNXtkeJdrgceOWFkfObvTiDHxs';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Erreur connexion DB"]);
    exit;
}

// 🔷 Récupération JSON
$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$pass = $data['password'] ?? '';

// 🔷 Validation
if (empty($email) || empty($pass)) {
    echo json_encode(["success" => false, "message" => "Champs requis"]);
    exit;
}

// 🔷 Requête utilisateur
$stmt = $pdo->prepare("SELECT nom, mot_de_passe FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["success" => false, "message" => "Email non trouvé"]);
    exit;
}

// 🔷 Vérification password
if (password_verify($pass, $user['mot_de_passe'])) {
    echo json_encode([
        "success" => true,
        "nom" => $user['nom']
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Mot de passe incorrect"
    ]);
}
?>