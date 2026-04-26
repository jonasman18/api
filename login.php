<?php  // ← AJOUTE CETTE LIGNE TOUT EN HAUT
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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
    echo json_encode(["success" => false, "message" => "Erreur connexion DB"]);
    exit;
}

// 🔷 Récupération JSON
$data  = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$pass  = $data['password'] ?? '';

// 🔷 Validation
if (empty($email) || empty($pass)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Champs requis"]);
    exit;
}

// 🔷 Requête utilisateur
$stmt = $pdo->prepare("SELECT id, nom, mot_de_passe, role FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Email non trouvé"]);
    exit;
}

// 🔷 Vérification mot de passe
if (!password_verify($pass, $user['mot_de_passe'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Mot de passe incorrect"]);
    exit;
}

// ✅ Lire la clé privée depuis les variables d'environnement Render
$privateKeyRaw = getenv('JWT_PRIVATE_KEY');
$privateKey = str_replace(['\\n', '\n', ' '], ["\n", "\n", "\n"], $privateKeyRaw);
$privateKey = trim($privateKey);

if (empty($privateKey)) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Clé JWT non configurée"]);
    exit;
}

// ✅ Génération du JWT RS256
$payload = [
    'iss'  => 'visiteur-api',
    'aud'  => 'visiteur-app',
    'iat'  => time(),
    'exp'  => time() + (8 * 3600),
    'sub'  => $user['id'],
    'nom'  => $user['nom'],
    'role' => $user['role'] ?? 'agent',
];

$token = JWT::encode($payload, $privateKey, 'RS256');

echo json_encode([
    "success" => true,
    "token"   => $token,
    "nom"     => $user['nom'],
    "role"    => $user['role'] ?? 'agent',
]);