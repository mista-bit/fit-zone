<?php
require '../db.php';
require 'admin-only.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM exercicios WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;
