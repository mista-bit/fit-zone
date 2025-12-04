<?php
require '../db.php';
require 'admin-only.php';

if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO planos (nome, preco, descricao, beneficios) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['nome'], $_POST['preco'], $_POST['descricao'], $_POST['beneficios']]);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Plano - FitZone Admin</title>
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
            <h1 class="text-3xl font-bold text-white mb-2">Novo Plano</h1>
            <p class="text-gray-300">Crie um novo plano de assinatura para a academia</p>
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
                    ></textarea>
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
                    ></textarea>
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
                        Salvar Plano
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
