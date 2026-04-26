<?php
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function requireAuth(): array {
    $publicKey = str_replace('\n', "\n", getenv('JWT_PUBLIC_KEY'));

    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!str_starts_with($authHeader, 'Bearer ')) {
        http_response_code(401);
        echo json_encode(['error' => 'Token manquant']);
        exit;
    }

    $token = substr($authHeader, 7);

    try {
        $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
        return (array) $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token invalide']);
        exit;
    }
}