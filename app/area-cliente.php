<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

require_once __DIR__ . '/db.php';

$db = new BancoDeDados();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo'])) {
    $_SESSION['erro_acesso'] = "Você precisa estar logado para acessar esta página.";
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
// Busca direta do usuário por ID
$usuarioAtual = $db->buscarPorId('users', $usuario_id);

if (!$usuarioAtual) {
    $_SESSION['erro_acesso'] = "Usuário não encontrado.";
    header("Location: login.php");
    exit();
}

$paginaAtual = 'area-cliente';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Área - FitZone</title>
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
                    <a href="area-cliente.php" 
                       class="text-gray-300 hover:text-white transition-colors <?= $paginaAtual === 'area-cliente' ? 'text-white font-semibold' : '' ?>">
                        Minha Área
                    </a>
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

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">
                <span class="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                    Minha Área
                </span>
            </h1>
            <p class="text-gray-400">Bem-vindo, <?= htmlspecialchars($usuarioAtual['nome']) ?>!</p>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-6 border border-white/20">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Meus Dados
                </h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-400 text-sm">Nome:</span>
                        <p class="text-white font-medium"><?= htmlspecialchars($usuarioAtual['nome']) ?></p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-sm">Email:</span>
                        <p class="text-white font-medium"><?= htmlspecialchars($usuarioAtual['email']) ?></p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-sm">Tipo de Conta:</span>
                        <p class="text-white font-medium">
                            <?php
                            $badgeColors = [
                                'aluno' => 'bg-blue-500/20 text-blue-300 border-blue-500/50',
                                'personal' => 'bg-purple-500/20 text-purple-300 border-purple-500/50',
                                'admin' => 'bg-red-500/20 text-red-300 border-red-500/50'
                            ];
                            $color = $badgeColors[$tipo] ?? 'bg-gray-500/20 text-gray-300 border-gray-500/50';
                            ?>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold border <?= $color ?>">
                                <?= ucfirst(htmlspecialchars($tipo)) ?>
                            </span>
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-sm">Data de Cadastro:</span>
                        <p class="text-white font-medium"><?= date('d/m/Y H:i', strtotime($usuarioAtual['created_at'])) ?></p>
                    </div>
                </div>
            </div>

            <?php if ($usuarioAtual['tipo'] === 'aluno'): ?>
            <?php
            // Busca direta dos dados do aluno
            $dadosAluno = $db->consultarUnico('SELECT * FROM alunos WHERE user_id = :uid LIMIT 1', [':uid' => $usuario_id]);
            ?>
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-6 border border-white/20">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Meu Plano
                </h2>
                <?php if ($dadosAluno): ?>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-400 text-sm">Plano:</span>
                        <p class="text-white font-medium"><?= $dadosAluno['plano'] ? htmlspecialchars($dadosAluno['plano']) : 'Não definido' ?></p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-sm">Altura:</span>
                        <p class="text-white font-medium"><?= $dadosAluno['altura'] ? htmlspecialchars($dadosAluno['altura']) : 'Não informado' ?></p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-sm">Peso:</span>
                        <p class="text-white font-medium"><?= $dadosAluno['peso'] ? htmlspecialchars($dadosAluno['peso']) : 'Não informado' ?></p>
                    </div>
                </div>
                <?php else: ?>
                <p class="text-gray-400">Dados do aluno ainda não foram configurados.</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

