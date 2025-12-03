<?php
require '../db.php';
require 'admin-only.php';

$stmt = $pdo->query("SELECT * FROM planos ORDER BY id ASC");
$planos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Gerenciar Planos</h1>

<p><a href="novo-plano.php">+ Novo Plano</a></p>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Preço</th>
        <th>Ações</th>
    </tr>

    <?php foreach ($planos as $plano): ?>
    <tr>
        <td><?= $plano['id'] ?></td>
        <td><?= $plano['nome'] ?></td>
        <td>R$ <?= number_format($plano['preco'], 2, ',', '.') ?></td>
        <td>
            <a href="editar-plano.php?id=<?= $plano['id'] ?>">Editar</a> |
            <a href="excluir-plano.php?id=<?= $plano['id'] ?>" onclick="return confirm('Excluir plano?');">Excluir</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<p><a href="index.php">⬅ Voltar ao Painel</a></p>
