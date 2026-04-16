<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'config.php';

$stmt = $pdo->query("SELECT nombre_jours * tarif_journalier AS total FROM visiteurs");
$totaux = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    "total" => array_sum($totaux),
    "minimum" => count($totaux) ? min($totaux) : 0,
    "maximum" => count($totaux) ? max($totaux) : 0,
]);
?>
