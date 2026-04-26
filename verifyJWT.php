<?php
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function requireAuth(): array {

    // ✅ Apache ne transmet pas toujours Authorization via getallheaders()
    // On lit depuis $_SERVER directement
    $authHeader = '';

    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } elseif (function_exists('getallheaders')) {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    }

    if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token manquant']);
        exit;
    }

    $token = substr($authHeader, 7);

    $publicKeyRaw = getenv('JWT_PUBLIC_KEY');
    $publicKey = str_replace(['\\n', '\n'], "\n", $publicKeyRaw);

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
        echo json_encode(['success' => false, 'message' => 'Token invalide : ' . $e->getMessage()]);
        exit;
    }
}