<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "visiteurs_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base"]);
    exit;
}

// Récupération des données JSON envoyées depuis React
$data = json_decode(file_get_contents("php://input"), true);

$nom = trim($data['nom'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Validation simple
if (empty($nom) || empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Tous les champs sont obligatoires"]);
    exit;
}

// Vérifier si l'e-mail est déjà utilisé
$stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Cet email est déjà utilisé"]);
    exit;
}

// Hasher le mot de passe
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insertion de l'utilisateur
$stmt = $conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nom, $email, $hashed_password);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Inscription réussie"]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de l'inscription"]);
}