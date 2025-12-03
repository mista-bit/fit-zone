<?php
// Proteção de acesso - apenas administradores
date_default_timezone_set('America/Sao_Paulo');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado e se é admin
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    $_SESSION['erro_acesso'] = "Acesso negado. Apenas administradores podem acessar esta área.";
    header("Location: ../login.php");
    exit();
}

// Conecta ao banco via PDO (compatível com os arquivos antigos que usam $pdo)
try {
    $dbPath = __DIR__ . '/../../data/fitzone.db';
    $pdo = new PDO("sqlite:{$dbPath}");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}
