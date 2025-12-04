<?php
require '../db.php';
require 'admin-only.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID inválido");

$stmt = $pdo->prepare("SELECT * FROM planos WHERE id = ?");
$stmt->execute([$id]);
$plano = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plano) die("Plano não encontrado");

if ($_POST) {
    $stmt = $pdo->prepare("UPDATE planos SET nome=?, preco=?, descricao=?, beneficios=? WHERE id=?");
    $stmt->execute([$_POST['nome'], $_POST['preco'], $_POST['descricao'], $_POST['beneficios'], $id]);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Plano - FitZone Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen py-8 px-4">
    <nav class="max-w-4xl mx-auto mb-8">
        <a href="index.php" class="inline-flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Voltar ao Painel
        </a>
    </nav>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8 mb-6">
            <h1 class="text-3xl font-bold text-white mb-2">Editar Plano</h1>
            <div class="bg-purple-500/20 border-l-4 border-purple-500 p-4 rounded">
                <p class="text-purple-200">
                    <span class="font-semibold">Editando:</span> <?= htmlspecialchars($plano['nome']) ?>
                </p>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8">
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-gray-300 text-sm font-semibold mb-2">
                        Nome do Plano <span class="text-red-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nome" 
                        value="<?= htmlspecialchars($plano['nome']) ?>" 
                        required
                        class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                        placeholder="Ex: Plano Premium"
                    >
                </div>

                <div>
                    <label class="block text-gray-300 text-sm font-semibold mb-2">
                        Preço (R$) <span class="text-red-400">*</span>
                    </label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="preco" 
                        value="<?= htmlspecialchars($plano['preco']) ?>" 
                        required
                        class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                        placeholder="149.90"
                    >
                </div>

                <div>
                    <label class="block text-gray-300 text-sm font-semibold mb-2">
                        Descrição
                    </label>
                    <textarea 
                        name="descricao" 
                        rows="4"
                        class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200 resize-y"
                        placeholder="Descreva o plano..."
                    ><?= htmlspecialchars($plano['descricao'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-gray-300 text-sm font-semibold mb-2">
                        Benefícios
                    </label>
                    <textarea 
                        name="beneficios" 
                        rows="5"
                        class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200 resize-y"
                        placeholder="Liste os benefícios do plano (um por linha)..."
                    ><?= htmlspecialchars($plano['beneficios'] ?? '') ?></textarea>
                    <p class="text-gray-400 text-xs mt-2">Dica: Digite um benefício por linha</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a 
                        href="index.php" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 text-center"
                    >
                        Cancelar
                    </a>
                    <button 
                        type="submit"
                        class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
                    >
                        Salvar Alterações
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-8 border-t border-white/10">
                <a 
                    href="excluir-plano.php?id=<?= $id ?>" 
                    onclick="return confirm('Tem certeza que deseja excluir este plano? Esta ação não pode ser desfeita.')" 
                    class="inline-flex items-center gap-2 text-red-400 hover:text-red-300 font-semibold transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Excluir Plano
                </a>
            </div>
        </div>
    </div>
</body>
</html>
