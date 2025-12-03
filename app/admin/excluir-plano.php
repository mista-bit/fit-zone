<?php
require '../db.php';
require 'admin-only.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM planos WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: planos.php");
exit;
