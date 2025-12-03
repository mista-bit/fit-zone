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
$usuario_tipo = $_SESSION['usuario_tipo'];

// Busca do usuário na tabela correta baseado no tipo
$tabela = '';
switch ($usuario_tipo) {
    case 'aluno':
        $tabela = 'alunos';
        break;
    case 'personal':
        $tabela = 'personais';
        break;
    case 'admin':
        $tabela = 'admins';
        break;
    default:
        $_SESSION['erro_acesso'] = "Tipo de usuário inválido.";
        header("Location: login.php");
        exit();
}

// Processar atualização de dados físicos (apenas para alunos)
$mensagem_sucesso = '';
$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario_tipo === 'aluno' && isset($_POST['atualizar_dados'])) {
    $altura = $_POST['altura'] ?? null;
    $peso = $_POST['peso'] ?? null;
    
    if ($altura && $peso) {
        $altura = floatval($altura);
        $peso = floatval($peso);
        
        if ($altura > 0 && $altura < 3 && $peso > 0 && $peso < 500) {
            $db->atualizar('alunos', $usuario_id, [
                'altura' => $altura,
                'peso' => $peso
            ]);
            $mensagem_sucesso = "Dados atualizados com sucesso!";
            // Recarregar dados do usuário
            $usuarioAtual = $db->buscarPorId($tabela, $usuario_id);
        } else {
            $mensagem_erro = "Valores inválidos. Altura deve estar entre 0 e 3m, peso entre 0 e 500kg.";
        }
    } else {
        $mensagem_erro = "Por favor, preencha altura e peso.";
    }
}

if (!isset($usuarioAtual)) {
    $usuarioAtual = $db->buscarPorId($tabela, $usuario_id);
}

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

        <?php if ($mensagem_sucesso): ?>
        <div class="mb-6 bg-green-500/20 border border-green-500/50 text-green-300 px-4 py-3 rounded-lg">
            ✓ <?= htmlspecialchars($mensagem_sucesso) ?>
        </div>
        <?php endif; ?>

        <?php if ($mensagem_erro): ?>
        <div class="mb-6 bg-red-500/20 border border-red-500/50 text-red-300 px-4 py-3 rounded-lg">
            ✗ <?= htmlspecialchars($mensagem_erro) ?>
        </div>
        <?php endif; ?>

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
                            $color = $badgeColors[$usuario_tipo] ?? 'bg-gray-500/20 text-gray-300 border-gray-500/50';
                            ?>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold border <?= $color ?>">
                                <?= ucfirst(htmlspecialchars($usuario_tipo)) ?>
                            </span>
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-sm">Data de Cadastro:</span>
                        <p class="text-white font-medium"><?= date('d/m/Y H:i', strtotime($usuarioAtual['created_at'])) ?></p>
                    </div>
                </div>
            </div>

            <?php if ($usuario_tipo === 'aluno'): ?>
            <?php
            // Para alunos, buscar informações do plano
            $plano = null;
            if (isset($usuarioAtual['plano_id']) && $usuarioAtual['plano_id']) {
                $plano = $db->buscarPorId('planos', $usuarioAtual['plano_id']);
            }
            ?>
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-6 border border-white/20">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Meu Plano e Dados Físicos
                </h2>
                <div class="space-y-4">
                    <div>
                        <span class="text-gray-400 text-sm">Plano:</span>
                        <p class="text-white font-medium"><?= $plano ? htmlspecialchars($plano['nome']) : 'Não definido' ?></p>
                    </div>
                    <?php if ($plano): ?>
                    <div>
                        <span class="text-gray-400 text-sm">Valor:</span>
                        <p class="text-white font-medium">R$ <?= number_format($plano['preco'], 2, ',', '.') ?></p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-sm">Descrição:</span>
                        <p class="text-white font-medium"><?= htmlspecialchars($plano['descricao'] ?? '') ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <hr class="border-white/10 my-4">
                    
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="text-gray-400 text-sm block mb-1">Altura (metros):</label>
                            <input type="number" 
                                   name="altura" 
                                   step="0.01" 
                                   min="0.5" 
                                   max="2.5"
                                   value="<?= isset($usuarioAtual['altura']) ? htmlspecialchars($usuarioAtual['altura']) : '' ?>"
                                   class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-purple-500"
                                   placeholder="Ex: 1.75"
                                   required>
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm block mb-1">Peso (kg):</label>
                            <input type="number" 
                                   name="peso" 
                                   step="0.1" 
                                   min="30" 
                                   max="300"
                                   value="<?= isset($usuarioAtual['peso']) ? htmlspecialchars($usuarioAtual['peso']) : '' ?>"
                                   class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-purple-500"
                                   placeholder="Ex: 70.5"
                                   required>
                        </div>
                        <button type="submit" 
                                name="atualizar_dados"
                                class="w-full bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                            💾 Atualizar Dados Físicos
                        </button>
                    </form>
                    
                    <?php if (isset($usuarioAtual['altura']) && isset($usuarioAtual['peso']) && $usuarioAtual['altura'] && $usuarioAtual['peso']): ?>
                    <?php
                    $imc = $usuarioAtual['peso'] / ($usuarioAtual['altura'] * $usuarioAtual['altura']);
                    $classificacao = '';
                    $cor = '';
                    if ($imc < 18.5) {
                        $classificacao = 'Abaixo do peso';
                        $cor = 'text-yellow-300';
                    } elseif ($imc < 25) {
                        $classificacao = 'Peso normal';
                        $cor = 'text-green-300';
                    } elseif ($imc < 30) {
                        $classificacao = 'Sobrepeso';
                        $cor = 'text-orange-300';
                    } else {
                        $classificacao = 'Obesidade';
                        $cor = 'text-red-300';
                    }
                    ?>
                    <div class="mt-4 p-4 bg-white/5 rounded-lg border border-white/10">
                        <span class="text-gray-400 text-sm">Seu IMC:</span>
                        <p class="text-white font-bold text-2xl"><?= number_format($imc, 1) ?></p>
                        <p class="<?= $cor ?> text-sm mt-1"><?= $classificacao ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

