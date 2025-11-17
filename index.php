<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/app/service/UsuarioService.php';

$usuarioService = new UsuarioService($conexao);
$mensagem = "";

if (isset($_POST['nome'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];

    $id = $usuarioService->criarUsuario($nome, $email, $senha, $tipo);

    if ($id) {
        $mensagem = "Usuário criado com sucesso! ID = $id";
    } else {
        $mensagem = "Falha ao criar usuário. Preencha todos os campos!";
    }
}

$usuarios = $usuarioService->listarUsuarios();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Academia - Usuários</title>
</head>
<body>
    <h1>Cadastro de Usuário</h1>

    <?php if ($mensagem != "") echo "<p>$mensagem</p>"; ?>

    <form method="post" action="">
        <label>Nome:</label><br>
        <input type="text" name="nome"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email"><br><br>

        <label>Senha:</label><br>
        <input type="text" name="senha"><br><br>

        <label>Tipo de Usuário:</label><br>
        <select name="tipo">
            <option value="admin">Admin</option>
            <option value="aluno">Aluno</option>
            <option value="personal">Personal</option>
        </select><br><br>

        <input type="submit" value="Cadastrar">
    </form>

    <h2>Usuários Cadastrados</h2>
    <table border="1" cellpadding="5" cellspacing="0">
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
            <td><?= $u['tipoUsuario'] ?></td>
            <td><?= $u['dataCadastro'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
