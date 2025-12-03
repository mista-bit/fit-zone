<?php
require '../db.php';
require 'admin-only.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID inválido");

$stmt = $pdo->prepare("SELECT * FROM exercicios WHERE id = ?");
$stmt->execute([$id]);
$exercicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exercicio) die("Exercício não encontrado");

if ($_POST) {
    $stmt = $pdo->prepare("UPDATE exercicios SET nome=?, categoria=?, descricao=? WHERE id=?");
    $stmt->execute([$_POST['nome'], $_POST['categoria'], $_POST['descricao'], $id]);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Exercício - FitZone Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        label {
            font-weight: bold;
            color: #555;
        }
        input, select, textarea {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #007bff;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        button {
            padding: 12px 24px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            flex: 1;
        }
        button:hover {
            background: #0056b3;
        }
        .btn-back {
            background: #6c757d;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            line-height: 1;
            padding: 12px 24px;
        }
        .btn-back:hover {
            background: #5a6268;
        }
        .btn-delete {
            background: #dc3545;
        }
        .btn-delete:hover {
            background: #c82333;
        }
        .required {
            color: red;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info-box p {
            margin: 0;
            color: #004085;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Editar Exercício</h1>

        <div class="info-box">
            <p><strong>Editando:</strong> <?= htmlspecialchars($exercicio['nome']) ?></p>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Nome do Exercício <span class="required">*</span></label>
                <input type="text" name="nome" value="<?= htmlspecialchars($exercicio['nome']) ?>" required>
            </div>

            <div class="form-group">
                <label>Categoria <span class="required">*</span></label>
                <select name="categoria" required>
                    <option value="">Selecione uma categoria</option>
                    <option value="Peito" <?= $exercicio['categoria'] === 'Peito' ? 'selected' : '' ?>>Peito</option>
                    <option value="Pernas" <?= $exercicio['categoria'] === 'Pernas' ? 'selected' : '' ?>>Pernas</option>
                    <option value="Cardio" <?= $exercicio['categoria'] === 'Cardio' ? 'selected' : '' ?>>Cardio</option>
                    <option value="Costas" <?= $exercicio['categoria'] === 'Costas' ? 'selected' : '' ?>>Costas</option>
                    <option value="Braços" <?= $exercicio['categoria'] === 'Braços' ? 'selected' : '' ?>>Braços</option>
                    <option value="Ombros" <?= $exercicio['categoria'] === 'Ombros' ? 'selected' : '' ?>>Ombros</option>
                    <option value="Core" <?= $exercicio['categoria'] === 'Core' ? 'selected' : '' ?>>Core</option>
                </select>
            </div>

            <div class="form-group">
                <label>Descrição</label>
                <textarea name="descricao"><?= htmlspecialchars($exercicio['descricao'] ?? '') ?></textarea>
            </div>

            <div class="btn-group">
                <a href="index.php" class="btn-back">⬅ Cancelar</a>
                <button type="submit">💾 Salvar Alterações</button>
            </div>
        </form>

        <div style="margin-top: 20px; text-align: center;">
            <a href="excluir-exercicio.php?id=<?= $id ?>" 
               onclick="return confirm('Tem certeza que deseja excluir este exercício? Esta ação não pode ser desfeita.')" 
               style="color: #dc3545; text-decoration: none; font-weight: bold;">
                🗑️ Excluir Exercício
            </a>
        </div>
    </div>
</body>
</html>
