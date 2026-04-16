<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$uri = $_SERVER['REQUEST_URI'];

if (strpos($uri, 'login') !== false) {
    require '../login.php';
} elseif (strpos($uri, 'register') !== false) {
    require '../register.php';
} elseif (strpos($uri, 'visiteurs') !== false) {
    require '../visiteurs.php';
} elseif (strpos($uri, 'bilan') !== false) {
    require '../bilan.php';
} else {
    echo json_encode(["message" => "API running"]);
}