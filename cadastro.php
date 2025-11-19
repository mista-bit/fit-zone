<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

require_once __DIR__ . '/db.php';

$db = new BancoDeDados();
$mensagem = "";

if (isset($_POST['nome'])) {

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];

    $id = $db->inserir("usuarios", [
        "nome" => $nome,
        "email" => $email,
        "senha" => password_hash($senha, PASSWORD_DEFAULT),
        "tipo" => $tipo,
        "dataCadastro" => date("Y-m-d H:i:s")
    ]);

    if ($tipo === "aluno") {
        $db->inserir("alunos", [
            "usuario_id" => $id,
            "altura" => "",
            "peso" => "",
            "plano" => "",
            "treinos" => []
        ]);
    }

    if ($tipo === "personal") {
        $db->inserir("personais", [
            "usuario_id" => $id,
            "acesso_treinos" => true
        ]);
    }

    if ($tipo === "admin") {
        $db->inserir("admins", [
            "usuario_id" => $id,
            "acesso_total" => true
        ]);
    }

    $_SESSION['mensagem_sucesso'] = "Usuário cadastrado com sucesso!";
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['mensagem_sucesso'])) {
    $mensagem = $_SESSION['mensagem_sucesso'];
    unset($_SESSION['mensagem_sucesso']);
}

$paginaAtual = 'cadastro';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - FitZone</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen">
    <nav class="bg-white/10 backdrop-blur-lg border-b border-white/20 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 max-w-7xl">
            <div class="flex items-center justify-between">
                <a href="index.php" class="text-2xl font-bold">
                    <span class="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                        FitZone
                    </span>
                </a>
                <div class="flex items-center gap-6">
                    <a href="index.php" 
                       class="text-gray-300 hover:text-white transition-colors">
                        Home
                    </a>
                    <a href="planos.php" 
                       class="text-gray-300 hover:text-white transition-colors">
                        Planos
                    </a>
                    <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin'): ?>
                    <a href="clientes.php" 
                       class="text-gray-300 hover:text-white transition-colors">
                        Clientes
                    </a>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php if ($_SESSION['usuario_tipo'] !== 'admin'): ?>
                    <a href="area-cliente.php" 
                       class="text-gray-300 hover:text-white transition-colors">
                        Minha Área
                    </a>
                    <?php endif; ?>
                    <span class="text-gray-300 text-sm">
                        Olá, <span class="text-purple-400 font-semibold"><?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                    </span>
                    <a href="logout.php" 
                       class="text-gray-300 hover:text-white transition-colors">
                        Sair
                    </a>
                    <?php else: ?>
                    <a href="login.php" 
                       class="text-gray-300 hover:text-white transition-colors">
                        Login
                    </a>
                    <a href="cadastro.php" 
                       class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 
                              text-white font-semibold px-6 py-2 rounded-lg transition-all duration-200 
                              transform hover:scale-105 shadow-lg">
                        Cadastre-se
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">
                <span class="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                    Cadastro
                </span>
            </h1>
            <p class="text-gray-400">Preencha os dados para se cadastrar</p>
        </div>

        <?php if ($mensagem): ?>
        <div class="mb-6 bg-green-500/20 border border-green-500/50 text-green-300 px-6 py-4 rounded-lg 
                    flex items-center gap-3 backdrop-blur-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium"><?= htmlspecialchars($mensagem) ?></span>
        </div>
        <?php endif; ?>

        <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-white/20">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Novo Cadastro
            </h2>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-300 text-sm font-medium mb-1.5">
                        Nome Completo
                    </label>
                    <input 
                        type="text" 
                        name="nome" 
                        required
                        class="w-full px-3 py-2 bg-white/5 border border-white/20 rounded-lg 
                               text-white placeholder-gray-400 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                               transition-all"
                        placeholder="Digite o nome completo"
                    >
                </div>

                <div>
                    <label class="block text-gray-300 text-sm font-medium mb-1.5">
                        Email
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        required
                        class="w-full px-3 py-2 bg-white/5 border border-white/20 rounded-lg 
                               text-white placeholder-gray-400 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                               transition-all"
                        placeholder="exemplo@email.com"
                    >
                </div>

                <div>
                    <label class="block text-gray-300 text-sm font-medium mb-1.5">
                        Senha
                    </label>
                    <input 
                        type="password" 
                        name="senha" 
                        required
                        class="w-full px-3 py-2 bg-white/5 border border-white/20 rounded-lg 
                               text-white placeholder-gray-400 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                               transition-all"
                        placeholder="••••••••"
                    >
                </div>

                <div>
                    <label class="block text-gray-300 text-sm font-medium mb-1.5">
                        Tipo de Usuário
                    </label>
                    <select 
                        name="tipo"
                        class="w-full px-3 py-2 bg-white/5 border border-white/20 rounded-lg 
                               text-white text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                               transition-all"
                    >
                        <option value="aluno" class="bg-gray-800">Aluno</option>
                        <option value="personal" class="bg-gray-800">Personal Trainer</option>
                        <option value="admin" class="bg-gray-800">Administrador</option>
                    </select>
                </div>

                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 
                           hover:from-blue-600 hover:to-purple-700 
                           text-white font-semibold py-2.5 px-6 rounded-lg text-sm
                           transition-all duration-200 transform hover:scale-105 
                           shadow-lg hover:shadow-xl"
                >
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Cadastrar Usuário
                    </span>
                </button>
            </form>
        </div>
    </div>
</body>
</html>

