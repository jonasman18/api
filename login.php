<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
// Connexion à la base avec PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=visiteurs_db;charset=utf8", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base"]);
    exit;
}

// Récupération des données JSON
$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Validation des champs
if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Champs requis"]);
    exit;
}

// Requête préparée pour récupérer l'utilisateur
$stmt = $pdo->prepare("SELECT nom, mot_de_passe FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["success" => false, "message" => "Email non trouvé"]);
    exit;
}

// Vérification du mot de passe
if (password_verify($password, $user['mot_de_passe'])) {
    echo json_encode(["success" => true, "nom" => $user['nom']]);
} else {
    echo json_encode(["success" => false, "message" => "Mot de passe incorrect"]);
}