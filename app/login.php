<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

require_once __DIR__ . '/db.php';

$db = new BancoDeDados();
$erro = "";

if (isset($_POST['email']) && isset($_POST['senha'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    // Consulta direta por email em vez de carregar toda a tabela
    $usuario = $db->consultarUnico('SELECT * FROM users WHERE email = :email LIMIT 1', [':email' => $email]);
    $usuarioEncontrado = null;
    if ($usuario && password_verify($senha, $usuario['password'])) {
        $usuarioEncontrado = $usuario;
    }
    
    if ($usuarioEncontrado) {
        $_SESSION['usuario_id'] = $usuarioEncontrado['id'];
        $_SESSION['usuario_nome'] = $usuarioEncontrado['name'];
        $_SESSION['usuario_email'] = $usuarioEncontrado['email'];
        $_SESSION['usuario_tipo'] = $usuarioEncontrado['type'];
        
        if ($usuarioEncontrado['tipo'] === 'admin') {
            header("Location: clientes.php");
        } else {
            header("Location: area-cliente.php");
        }
        exit();
    } else {
        $erro = "Email ou senha incorretos!";
    }
}

if (isset($_SESSION['usuario_id'])) {
    if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin') {
        header("Location: clientes.php");
    } else {
        header("Location: area-cliente.php");
    }
    exit();
}

$paginaAtual = 'login';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitZone</title>
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
                    <a href="cadastro.php" 
                       class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 
                              text-white font-semibold px-6 py-2 rounded-lg transition-all duration-200 
                              transform hover:scale-105 shadow-lg">
                        Cadastre-se
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-16 max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">
                <span class="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                    Login
                </span>
            </h1>
            <p class="text-gray-400">Acesse sua conta FitZone</p>
        </div>

        <?php if ($erro): ?>
        <div class="mb-6 bg-red-500/20 border border-red-500/50 text-red-300 px-6 py-4 rounded-lg 
                    flex items-center gap-3 backdrop-blur-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium"><?= htmlspecialchars($erro) ?></span>
        </div>
        <?php endif; ?>

        <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-white/20">
            <form method="POST" class="space-y-6">
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
                        placeholder="seu@email.com"
                        autocomplete="email"
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
                        autocomplete="current-password"
                    >
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Entrar
                    </span>
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-400 text-sm">
                    Não tem uma conta? 
                    <a href="cadastro.php" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors">
                        Cadastre-se aqui
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

