<?php
require_once __DIR__ . '/db.php';

$db = new BancoDeDados();
$mensagem = "";

// Quando o usuário envia o formulário
if (isset($_POST['nome'])) {

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];

    // Cria usuário básico
    $id = $db->inserir("usuarios", [
        "nome" => $nome,
        "email" => $email,
        "senha" => password_hash($senha, PASSWORD_DEFAULT),
        "tipo" => $tipo,
        "dataCadastro" => date("Y-m-d H:i:s")
    ]);

    // Se for aluno, cria registro adicional
    if ($tipo === "aluno") {
        $db->inserir("alunos", [
            "usuario_id" => $id,
            "altura" => "",
            "peso" => "",
            "plano" => "",
            "treinos" => []
        ]);
    }

    // Se for personal
    if ($tipo === "personal") {
        $db->inserir("personais", [
            "usuario_id" => $id,
            "acesso_treinos" => true
        ]);
    }

    // Se for admin
    if ($tipo === "admin") {
        $db->inserir("admins", [
            "usuario_id" => $id,
            "acesso_total" => true
        ]);
    }

    $mensagem = "Usuário cadastrado com sucesso!";
}

// Lê usuários
$usuarios = $db->ler("usuarios");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema</title>
</head>
<body>
    <h1>Cadastro de Usuário</h1>

    <p><?= $mensagem ?></p>

    <form method="POST">
        Nome: <input type="text" name="nome" required><br>
        Email: <input type="email" name="email" required><br>
        Senha: <input type="password" name="senha" required><br>

        Tipo:
        <select name="tipo">
            <option value="aluno">Aluno</option>
            <option value="personal">Personal</option>
            <option value="admin">Admin</option>
        </select>
        <br><br>

        <button type="submit">Cadastrar</button>
    </form>

    <h2>Usuários Cadastrados</h2>

    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Data de Cadastro</th>
        </tr>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= $u['nome'] ?></td>
            <td><?= $u['email'] ?></td>
            <td><?= $u['tipo'] ?></td>
            <td><?= $u['dataCadastro'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
