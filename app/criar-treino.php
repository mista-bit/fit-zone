<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

require_once __DIR__ . '/db.php';

$db = new BancoDeDados();

// Verificar se é aluno
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$aluno = $db->buscarPorId('alunos', $usuario_id);

if (!$aluno) {
    header("Location: login.php");
    exit();
}

$mensagem_sucesso = '';
$mensagem_erro = '';

// Processar criação do treino
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_treino'])) {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $exercicios_selecionados = $_POST['exercicios'] ?? [];
    
    if (empty($nome)) {
        $mensagem_erro = "O nome do treino é obrigatório!";
    } elseif (empty($exercicios_selecionados)) {
        $mensagem_erro = "Selecione pelo menos um exercício!";
    } else {
        // Criar o treino
        $treino_id = $db->inserir('treinos', [
            'aluno_id' => $usuario_id,
            'personal_id' => $aluno['personal_id'] ?? 1,
            'nome' => $nome,
            'descricao' => $descricao,
            'ativo' => 1
        ]);
        
        // Adicionar exercícios ao treino
        $ordem = 0;
        foreach ($exercicios_selecionados as $exercicio_id) {
            $ordem++;
            $series = $_POST['series_' . $exercicio_id] ?? 3;
            $repeticoes = $_POST['repeticoes_' . $exercicio_id] ?? 12;
            $carga = trim($_POST['carga_' . $exercicio_id] ?? '');
            $descanso = trim($_POST['descanso_' . $exercicio_id] ?? '');
            $observacoes = trim($_POST['observacoes_' . $exercicio_id] ?? '');
            
            $db->inserir('treino_exercicios', [
                'treino_id' => $treino_id,
                'exercicio_id' => $exercicio_id,
                'ordem' => $ordem,
                'series' => $series,
                'repeticoes' => $repeticoes,
                'carga' => $carga ?: null,
                'descanso' => $descanso ?: null,
                'observacoes' => $observacoes ?: null
            ]);
        }
        
        $mensagem_sucesso = "Treino criado com sucesso!";
        header("Location: area-cliente.php");
        exit();
    }
}

// Buscar todos os exercícios disponíveis
$exercicios = $db->consultar('SELECT * FROM exercicios ORDER BY categoria, nome');

// Agrupar exercícios por categoria
$exercicios_por_categoria = [];
foreach ($exercicios as $ex) {
    $categoria = $ex['categoria'] ?? 'Outros';
    if (!isset($exercicios_por_categoria[$categoria])) {
        $exercicios_por_categoria[$categoria] = [];
    }
    $exercicios_por_categoria[$categoria][] = $ex;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Meu Treino - FitZone</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        .exercicio-detalhes {
            display: none;
        }
        .exercicio-checkbox:checked ~ .exercicio-detalhes {
            display: block;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen">
    <nav class="bg-white/10 backdrop-blur-lg border-b border-white/20">
        <div class="container mx-auto px-4 py-4 max-w-7xl">
            <div class="flex items-center justify-between">
                <a href="index.php" class="text-2xl font-bold">
                    <span class="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                        FitZone
                    </span>
                </a>
                <div class="flex items-center gap-6">
                    <a href="area-cliente.php" class="text-gray-300 hover:text-white transition-colors">
                        ← Voltar
                    </a>
                    <span class="text-gray-300 text-sm">
                        Olá, <span class="text-purple-400 font-semibold"><?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-5xl">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">
                <span class="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                    Criar Meu Treino
                </span>
            </h1>
            <p class="text-gray-400">Monte seu próprio treino personalizado</p>
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

        <form method="POST" class="space-y-6">
            <!-- Informações do Treino -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-6 border border-white/20">
                <h2 class="text-xl font-bold text-white mb-4">📋 Informações do Treino</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-gray-400 text-sm block mb-2">Nome do Treino *</label>
                        <input type="text" 
                               name="nome" 
                               required
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500"
                               placeholder="Ex: Treino A - Peito e Tríceps">
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm block mb-2">Descrição (opcional)</label>
                        <textarea name="descricao" 
                                  rows="3"
                                  class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500"
                                  placeholder="Descreva o objetivo deste treino..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Seleção de Exercícios -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-6 border border-white/20">
                <h2 class="text-xl font-bold text-white mb-4">💪 Selecione os Exercícios</h2>
                <p class="text-gray-400 text-sm mb-6">Marque os exercícios que deseja incluir no treino e configure as séries e repetições</p>
                
                <div class="space-y-6">
                    <?php foreach ($exercicios_por_categoria as $categoria => $exercicios_cat): ?>
                    <div>
                        <h3 class="text-lg font-semibold text-purple-300 mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 bg-purple-400 rounded-full"></span>
                            <?= htmlspecialchars($categoria) ?>
                        </h3>
                        <div class="space-y-3">
                            <?php foreach ($exercicios_cat as $ex): ?>
                            <div class="bg-white/5 rounded-lg border border-white/10 overflow-hidden">
                                <label class="flex items-center p-4 cursor-pointer hover:bg-white/10 transition-all">
                                    <input type="checkbox" 
                                           name="exercicios[]" 
                                           value="<?= $ex['id'] ?>"
                                           class="exercicio-checkbox w-5 h-5 rounded text-purple-500 focus:ring-purple-500 focus:ring-offset-0 bg-white/10 border-white/20">
                                    <div class="ml-3 flex-grow">
                                        <div class="font-semibold text-white"><?= htmlspecialchars($ex['nome']) ?></div>
                                        <?php if ($ex['descricao']): ?>
                                        <div class="text-sm text-gray-400"><?= htmlspecialchars($ex['descricao']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </label>
                                
                                <!-- Detalhes do Exercício (aparecem quando marcado) -->
                                <div class="exercicio-detalhes p-4 bg-white/5 border-t border-white/10">
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="text-gray-400 text-xs block mb-1">Séries</label>
                                            <input type="number" 
                                                   name="series_<?= $ex['id'] ?>" 
                                                   value="3" 
                                                   min="1" 
                                                   max="10"
                                                   class="w-full bg-white/5 border border-white/10 rounded px-3 py-2 text-white text-sm focus:outline-none focus:border-purple-500">
                                        </div>
                                        <div>
                                            <label class="text-gray-400 text-xs block mb-1">Repetições</label>
                                            <input type="number" 
                                                   name="repeticoes_<?= $ex['id'] ?>" 
                                                   value="12" 
                                                   min="1" 
                                                   max="100"
                                                   class="w-full bg-white/5 border border-white/10 rounded px-3 py-2 text-white text-sm focus:outline-none focus:border-purple-500">
                                        </div>
                                        <div>
                                            <label class="text-gray-400 text-xs block mb-1">Carga</label>
                                            <input type="text" 
                                                   name="carga_<?= $ex['id'] ?>" 
                                                   placeholder="Ex: 20kg"
                                                   class="w-full bg-white/5 border border-white/10 rounded px-3 py-2 text-white text-sm focus:outline-none focus:border-purple-500">
                                        </div>
                                        <div>
                                            <label class="text-gray-400 text-xs block mb-1">Descanso</label>
                                            <input type="text" 
                                                   name="descanso_<?= $ex['id'] ?>" 
                                                   placeholder="Ex: 60s"
                                                   class="w-full bg-white/5 border border-white/10 rounded px-3 py-2 text-white text-sm focus:outline-none focus:border-purple-500">
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="text-gray-400 text-xs block mb-1">Observações</label>
                                        <input type="text" 
                                               name="observacoes_<?= $ex['id'] ?>" 
                                               placeholder="Dicas ou observações sobre este exercício..."
                                               class="w-full bg-white/5 border border-white/10 rounded px-3 py-2 text-white text-sm focus:outline-none focus:border-purple-500">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex gap-4">
                <a href="area-cliente.php" 
                   class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold px-6 py-4 rounded-lg transition-all text-center">
                    Cancelar
                </a>
                <button type="submit" 
                        name="criar_treino"
                        class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold px-6 py-4 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                    💾 Criar Treino
                </button>
            </div>
        </form>
    </div>

    <script>
        // Contador de exercícios selecionados
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.exercicio-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateCounter);
            });
            
            function updateCounter() {
                const checked = document.querySelectorAll('.exercicio-checkbox:checked').length;
                console.log(`${checked} exercícios selecionados`);
            }
        });
    </script>
</body>
</html>
