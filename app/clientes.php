<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

require_once __DIR__ . '/db.php';

$db = new BancoDeDados();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo'])) {
    $_SESSION['erro_acesso'] = "Você precisa estar logado para acessar esta página.";
    header("Location: index.php");
    exit();
}

if ($_SESSION['usuario_tipo'] !== 'admin') {
    $_SESSION['erro_acesso'] = "Acesso negado. Apenas administradores podem visualizar esta página.";
    header("Location: index.php");
    exit();
}

// Carrega todos os usuários ordenados por created_at desc
$alunos = $db->consultar('SELECT id, nome, email, created_at, "aluno" as tipo FROM alunos ORDER BY created_at DESC');
$personais = $db->consultar('SELECT id, nome, email, created_at, "personal" as tipo FROM personais ORDER BY created_at DESC');
$admins = $db->consultar('SELECT id, nome, email, created_at, "admin" as tipo FROM admins ORDER BY created_at DESC');
$usuarios = array_merge($alunos, $personais, $admins);
// Reordena por data
usort($usuarios, function($a, $b) { return strtotime($b['created_at']) - strtotime($a['created_at']); });
$paginaAtual = 'clientes';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Administrativa - FitZone</title>
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
                    <a href="clientes.php" 
                       class="text-gray-300 hover:text-white transition-colors <?= $paginaAtual === 'clientes' ? 'text-white font-semibold' : '' ?>">
                        Clientes
                    </a>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
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
                    Área Administrativa
                </span>
            </h1>
            <p class="text-gray-400">Gerenciamento de todos os clientes cadastrados</p>
        </div>

        <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-6 border border-white/20">
            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Clientes Cadastrados
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/20">
                            <th class="text-left py-2.5 px-3 text-gray-300 font-semibold text-sm">
                                ID
                            </th>
                            <th class="text-left py-2.5 px-3 text-gray-300 font-semibold text-sm">
                                Nome
                            </th>
                            <th class="text-left py-2.5 px-3 text-gray-300 font-semibold text-sm">
                                Email
                            </th>
                            <th class="text-left py-2.5 px-3 text-gray-300 font-semibold text-sm">
                                Tipo
                            </th>
                            <th class="text-left py-2.5 px-3 text-gray-300 font-semibold text-sm">
                                Data de Cadastro
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-400 text-sm">
                                Nenhum cliente cadastrado ainda.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($usuarios as $u): ?>
                        <tr class="border-b border-white/10 hover:bg-white/5 transition-colors">
                            <td class="py-2.5 px-3 text-white font-mono text-sm">
                                #<?= htmlspecialchars($u['id']) ?>
                            </td>
                            <td class="py-2.5 px-3 text-white font-medium text-sm">
                                <?= htmlspecialchars($u['nome']) ?>
                            </td>
                            <td class="py-2.5 px-3 text-gray-300 text-sm">
                                <?= htmlspecialchars($u['email']) ?>
                            </td>
                            <td class="py-2.5 px-3">
                                <?php
                                $badgeColors = [
                                    'aluno' => 'bg-blue-500/20 text-blue-300 border-blue-500/50',
                                    'personal' => 'bg-purple-500/20 text-purple-300 border-purple-500/50',
                                    'admin' => 'bg-red-500/20 text-red-300 border-red-500/50'
                                ];
                                $color = $badgeColors[$u['tipo']] ?? 'bg-gray-500/20 text-gray-300 border-gray-500/50';
                                ?>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold border <?= $color ?>">
                                    <?= ucfirst(htmlspecialchars($u['tipo'])) ?>
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-gray-400 text-xs">
                                <?= date('d/m/Y H:i', strtotime($u['created_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

