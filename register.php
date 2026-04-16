<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

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
    echo json_encode(["success" => false, "message" => "Erreur connexion DB"]);
    exit;
}

// 🔷 Récupération JSON
$data = json_decode(file_get_contents("php://input"), true);

$nom = trim($data['nom'] ?? '');
$email = trim($data['email'] ?? '');
$pass = $data['password'] ?? '';

// 🔷 Validation
if (empty($nom) || empty($email) || empty($pass)) {
    echo json_encode(["success" => false, "message" => "Tous les champs sont obligatoires"]);
    exit;
}

// 🔷 Vérifier email existant
$stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->rowCount() > 0) {
    echo json_encode(["success" => false, "message" => "Cet email est déjà utilisé"]);
    exit;
}

// 🔷 Hash password
$hashed_password = password_hash($pass, PASSWORD_DEFAULT);

// 🔷 Insertion
$stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
$success = $stmt->execute([$nom, $email, $hashed_password]);

if ($success) {
    echo json_encode(["success" => true, "message" => "Inscription réussie"]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de l'inscription"]);
}
?>