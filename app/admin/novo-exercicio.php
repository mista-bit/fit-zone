<?php
require '../db.php';
require 'admin-only.php';

if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO exercicios (nome, categoria, descricao) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['nome'], $_POST['categoria'], $_POST['descricao']]);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Exercício - FitZone Admin</title>
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
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            flex: 1;
        }
        button:hover {
            background: #218838;
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
        .required {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ Novo Exercício</h1>

        <form method="POST">
            <div class="form-group">
                <label>Nome do Exercício <span class="required">*</span></label>
                <input type="text" name="nome" required placeholder="Ex: Supino Reto">
            </div>

            <div class="form-group">
                <label>Categoria <span class="required">*</span></label>
                <select name="categoria" required>
                    <option value="">Selecione uma categoria</option>
                    <option value="Peito">Peito</option>
                    <option value="Pernas">Pernas</option>
                    <option value="Cardio">Cardio</option>
                    <option value="Costas">Costas</option>
                    <option value="Braços">Braços</option>
                    <option value="Ombros">Ombros</option>
                    <option value="Core">Core</option>
                </select>
            </div>

            <div class="form-group">
                <label>Descrição</label>
                <textarea name="descricao" placeholder="Descreva o exercício, técnica de execução, músculos trabalhados, etc."></textarea>
            </div>

            <div class="btn-group">
                <a href="index.php" class="btn-back">⬅ Cancelar</a>
                <button type="submit">💾 Salvar Exercício</button>
            </div>
        </form>
    </div>
</body>
</html>
