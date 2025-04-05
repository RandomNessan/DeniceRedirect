<?php
require_once '../config/db.php';

$stmt = $pdo->query("SELECT id, url FROM urls ORDER BY RAND() LIMIT 2");
$urls = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($urls);
?>
