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

        <!-- Seção de Treinos (para alunos) -->
        <?php if ($usuario_tipo === 'aluno'): ?>
        <?php
        // Buscar treinos do aluno
        $treinos = $db->consultar('
            SELECT t.*, p.nome as personal_nome 
            FROM treinos t
            LEFT JOIN personais p ON t.personal_id = p.id
            WHERE t.aluno_id = :aluno_id
            ORDER BY t.created_at DESC
        ', [':aluno_id' => $usuario_id]);
        ?>
        
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Meus Treinos
                </h2>
                <a href="criar-treino.php" 
                   class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Criar Meu Treino
                </a>
            </div>

            <?php if (empty($treinos)): ?>
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-white/20 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-400 text-lg mb-4">Você ainda não tem treinos cadastrados.</p>
                <p class="text-gray-500 text-sm mb-6">Crie seu próprio treino ou aguarde seu personal trainer criar um para você.</p>
                <a href="criar-treino.php" 
                   class="inline-block bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold px-8 py-3 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                    ➕ Criar Meu Primeiro Treino
                </a>
            </div>
            <?php else: ?>
            <div class="grid gap-6">
                <?php foreach ($treinos as $treino): ?>
                <?php
                // Buscar exercícios do treino
                $exercicios = $db->consultar('
                    SELECT te.*, e.nome as exercicio_nome, e.categoria
                    FROM treino_exercicios te
                    JOIN exercicios e ON te.exercicio_id = e.id
                    WHERE te.treino_id = :treino_id
                    ORDER BY te.ordem ASC
                ', [':treino_id' => $treino['id']]);
                ?>
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 overflow-hidden">
                    <!-- Cabeçalho do Treino -->
                    <div class="p-6 bg-gradient-to-r from-blue-500/20 to-purple-500/20 border-b border-white/10">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-2xl font-bold text-white mb-2"><?= htmlspecialchars($treino['nome']) ?></h3>
                                <p class="text-gray-300 mb-2"><?= htmlspecialchars($treino['descricao'] ?? 'Sem descrição') ?></p>
                                <div class="flex items-center gap-4 text-sm">
                                    <span class="text-gray-400">
                                        👨‍🏫 Personal: <span class="text-purple-300"><?= htmlspecialchars($treino['personal_nome']) ?></span>
                                    </span>
                                    <span class="text-gray-400">
                                        📅 <?= date('d/m/Y', strtotime($treino['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold <?= $treino['ativo'] ? 'bg-green-500/20 text-green-300 border border-green-500/50' : 'bg-gray-500/20 text-gray-300 border border-gray-500/50' ?>">
                                <?= $treino['ativo'] ? '✓ Ativo' : '○ Inativo' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Lista de Exercícios -->
                    <div class="p-6">
                        <?php if (empty($exercicios)): ?>
                        <p class="text-gray-400 text-center py-4">Nenhum exercício adicionado ainda.</p>
                        <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($exercicios as $idx => $ex): ?>
                            <div class="bg-white/5 rounded-lg p-4 border border-white/10 hover:bg-white/10 transition-all">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-8 h-8 bg-purple-500/20 rounded-full flex items-center justify-center text-purple-300 font-bold">
                                        <?= $idx + 1 ?>
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h4 class="text-lg font-semibold text-white"><?= htmlspecialchars($ex['exercicio_nome']) ?></h4>
                                            <span class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs rounded-full border border-blue-500/50">
                                                <?= htmlspecialchars($ex['categoria']) ?>
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                            <div>
                                                <span class="text-gray-400">Séries:</span>
                                                <span class="text-white font-semibold ml-1"><?= htmlspecialchars($ex['series'] ?? 3) ?></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400">Repetições:</span>
                                                <span class="text-white font-semibold ml-1"><?= htmlspecialchars($ex['repeticoes'] ?? 12) ?></span>
                                            </div>
                                            <?php if ($ex['carga']): ?>
                                            <div>
                                                <span class="text-gray-400">Carga:</span>
                                                <span class="text-white font-semibold ml-1"><?= htmlspecialchars($ex['carga']) ?></span>
                                            </div>
                                            <?php endif; ?>
                                            <?php if ($ex['descanso']): ?>
                                            <div>
                                                <span class="text-gray-400">Descanso:</span>
                                                <span class="text-white font-semibold ml-1"><?= htmlspecialchars($ex['descanso']) ?></span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($ex['observacoes']): ?>
                                        <div class="mt-2 text-sm text-gray-400 italic">
                                            💡 <?= htmlspecialchars($ex['observacoes']) ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-6 p-4 bg-blue-500/10 rounded-lg border border-blue-500/20">
                            <p class="text-blue-300 text-sm">
                                📊 Total: <strong><?= count($exercicios) ?> exercícios</strong> neste treino
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-b from-gray-900/50 to-black border-t border-white/10 mt-12">
        <div class="container mx-auto px-4 py-12 max-w-7xl">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <!-- Logo e Descrição -->
                <div class="md:col-span-2">
                    <a href="index.php" class="text-3xl font-bold mb-4 inline-block">
                        <span class="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                            FitZone
                        </span>
                    </a>
                    <p class="text-gray-400 mt-4 leading-relaxed">
                        Transformando vidas através do fitness. Sua jornada para um estilo de vida mais saudável começa aqui.
                    </p>
                    <div class="flex gap-4 mt-6">
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-purple-500/30 rounded-full flex items-center justify-center transition-all transform hover:scale-110 border border-white/20">
                            <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-purple-500/30 rounded-full flex items-center justify-center transition-all transform hover:scale-110 border border-white/20">
                            <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/>
                                <path d="M12 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-purple-500/30 rounded-full flex items-center justify-center transition-all transform hover:scale-110 border border-white/20">
                            <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Links Rápidos -->
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Links Rápidos</h3>
                    <ul class="space-y-3">
                        <li><a href="index.php" class="text-gray-400 hover:text-purple-400 transition-colors">Home</a></li>
                        <li><a href="planos.php" class="text-gray-400 hover:text-purple-400 transition-colors">Planos</a></li>
                        <li><a href="area-cliente.php" class="text-gray-400 hover:text-purple-400 transition-colors">Minha Área</a></li>
                        <li><a href="logout.php" class="text-gray-400 hover:text-purple-400 transition-colors">Sair</a></li>
                    </ul>
                </div>

                <!-- Contato -->
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Contato</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-2 text-gray-400">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>(83) 00000-0000</span>
                        </li>
                        <li class="flex items-center gap-2 text-gray-400">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>contato@fitzone.com</span>
                        </li>
                        <li class="flex items-start gap-2 text-gray-400">
                            <svg class="w-5 h-5 text-purple-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>João Pessoa - PB<br>Brasil</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10 pt-8 mt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-gray-400 text-sm">
                        © 2025 <span class="text-purple-400 font-semibold">FitZone</span>. Todos os direitos reservados.
                    </p>
                    <div class="flex gap-6 text-sm">
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors">Política de Privacidade</a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors">Termos de Uso</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>


