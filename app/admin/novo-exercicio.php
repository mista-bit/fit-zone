<?php
require '../db.php';
require 'admin-only.php';

if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO exercicios (nome, categoria, descricao) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['nome'], $_POST['categoria'], $_POST['descricao']]);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Exercício - FitZone Admin</title>
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
            <h1 class="text-3xl font-bold text-white mb-2">Novo Exercício</h1>
            <p class="text-gray-300">Adicione um novo exercício ao banco de dados</p>
        </div>

        <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8">
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-gray-300 text-sm font-semibold mb-2">
                        Nome do Exercício <span class="text-red-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nome" 
                        required
                        class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-200"
                        placeholder="Ex: Supino reto"
                    >
                </div>

                <div>
                    <label class="block text-gray-300 text-sm font-semibold mb-2">
                        Categoria <span class="text-red-400">*</span>
                    </label>
                    <select 
                        name="categoria" 
                        required
                        class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-200"
                    >
                        <option value="" class="bg-gray-800">Selecione uma categoria</option>
                        <option value="Peito" class="bg-gray-800">Peito</option>
                        <option value="Pernas" class="bg-gray-800">Pernas</option>
                        <option value="Cardio" class="bg-gray-800">Cardio</option>
                        <option value="Costas" class="bg-gray-800">Costas</option>
                        <option value="Braços" class="bg-gray-800">Braços</option>
                        <option value="Ombros" class="bg-gray-800">Ombros</option>
                        <option value="Core" class="bg-gray-800">Core</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-300 text-sm font-semibold mb-2">
                        Descrição
                    </label>
                    <textarea 
                        name="descricao" 
                        rows="5"
                        class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-200 resize-y"
                        placeholder="Descreva o exercício, técnica de execução, músculos trabalhados, etc."
                    ></textarea>
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
                        class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
                    >
                        Salvar Exercício
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
