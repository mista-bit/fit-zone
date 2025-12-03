<?php
require '../db.php';
require 'admin-only.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID inválido");

$stmt = $pdo->prepare("SELECT * FROM planos WHERE id = ?");
$stmt->execute([$id]);
$plano = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plano) die("Plano não encontrado");

if ($_POST) {
    $stmt = $pdo->prepare("UPDATE planos SET nome=?, preco=?, descricao=?, beneficios=? WHERE id=?");
    $stmt->execute([$_POST['nome'], $_POST['preco'], $_POST['descricao'], $_POST['beneficios'], $id]);
    header("Location: planos.php");
    exit;
}
?>
<h1>Editar Plano</h1>

<form method="POST">
    Nome: <input type="text" name="nome" value="<?= $plano['nome'] ?>" required><br><br>
    Preço: <input type="number" step="0.01" name="preco" value="<?= $plano['preco'] ?>" required><br><br>
    Descrição:<br>
    <textarea name="descricao"><?= $plano['descricao'] ?></textarea><br><br>
    Benefícios:<br>
    <textarea name="beneficios"><?= $plano['beneficios'] ?></textarea><br><br>

    <button type="submit">Salvar</button>
</form>

<p><a href="planos.php">⬅ Voltar</a></p>
