<?php
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function requireAuth(): array {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!str_starts_with($authHeader, 'Bearer ')) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token manquant']);
        exit;
    }

    $token = substr($authHeader, 7);

    // ✅ Lire depuis variable d'environnement (pas depuis fichier)
    $publicKey = str_replace('\n', "\n", getenv('JWT_PUBLIC_KEY'));

    if (empty($publicKey)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Clé publique non configurée']);
        exit;
    }

    try {
        $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
        return (array) $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token invalide ou expiré']);
        exit;
    }
}