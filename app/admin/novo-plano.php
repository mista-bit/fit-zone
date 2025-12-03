<?php
require '../db.php';
require 'admin-only.php';

if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO planos (nome, preco, descricao, beneficios) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['nome'], $_POST['preco'], $_POST['descricao'], $_POST['beneficios']]);
    header("Location: planos.php");
    exit;
}
?>

<h1>Novo Plano</h1>

<form method="POST">
    Nome: <input type="text" name="nome" required><br><br>
    Preço: <input type="number" step="0.01" name="preco" required><br><br>
    Descrição:<br>
    <textarea name="descricao"></textarea><br><br>
    Benefícios (texto livre ou JSON):<br>
    <textarea name="beneficios"></textarea><br><br>
    <button type="submit">Salvar</button>
</form>

<p><a href="planos.php">⬅ Voltar</a></p>
