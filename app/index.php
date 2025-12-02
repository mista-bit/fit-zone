<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

require_once __DIR__ . '/db.php';

$db = new BancoDeDados();
$mensagem = "";

if (isset($_SESSION['mensagem_sucesso'])) {
    $mensagem = $_SESSION['mensagem_sucesso'];
    unset($_SESSION['mensagem_sucesso']);
}

$erro_acesso = "";
if (isset($_SESSION['erro_acesso'])) {
    $erro_acesso = $_SESSION['erro_acesso'];
    unset($_SESSION['erro_acesso']);
}

// Combina todos os usuários
$alunos = $db->consultar('SELECT id, nome, email, created_at, "aluno" as tipo FROM alunos');
$personais = $db->consultar('SELECT id, nome, email, created_at, "personal" as tipo FROM personais');
$admins = $db->consultar('SELECT id, nome, email, created_at, "admin" as tipo FROM admins');
$usuarios = array_merge($alunos, $personais, $admins);
$paginaAtual = 'home';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitZone - Transforme seu Corpo, Transforme sua Vida</title>
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
                       class="text-gray-300 hover:text-white transition-colors <?= $paginaAtual === 'home' ? 'text-white font-semibold' : '' ?>">
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

    <?php if ($mensagem): ?>
    <div class="container mx-auto px-4 pt-6 max-w-7xl">
        <div class="mb-6 bg-green-500/20 border border-green-500/50 text-green-300 px-6 py-4 rounded-lg 
                    flex items-center gap-3 backdrop-blur-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium"><?= htmlspecialchars($mensagem) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($erro_acesso): ?>
    <div class="container mx-auto px-4 pt-6 max-w-7xl">
        <div class="mb-6 bg-red-500/20 border border-red-500/50 text-red-300 px-6 py-4 rounded-lg 
                    flex items-center gap-3 backdrop-blur-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium"><?= htmlspecialchars($erro_acesso) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/50 via-purple-900/50 to-pink-900/50"></div>
        <div class="container mx-auto px-4 py-20 max-w-7xl relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-6xl md:text-7xl font-extrabold text-white mb-6 leading-tight">
                    <span class="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                        Transforme seu Corpo
                    </span>
                    <br>
                    <span class="text-white">Transforme sua Vida</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 leading-relaxed">
                    Sua jornada para um estilo de vida mais saudável começa aqui.<br>
                    <span class="text-purple-400 font-semibold">A força está dentro de você. Nós apenas ajudamos você a encontrá-la.</span>
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="cadastro.php" 
                       class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 
                              text-white font-bold text-lg px-8 py-4 rounded-lg transition-all duration-200 
                              transform hover:scale-105 shadow-2xl hover:shadow-purple-500/50">
                        Comece Agora
                    </a>
                    <a href="planos.php" 
                       class="bg-white/10 backdrop-blur-lg border-2 border-white/30 hover:border-white/50 
                              text-white font-bold text-lg px-8 py-4 rounded-lg transition-all duration-200 
                              transform hover:scale-105">
                        Ver Planos
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-gradient-to-b from-transparent to-gray-900/50">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    Por que escolher a <span class="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">FitZone</span>?
                </h2>
                <p class="text-xl text-gray-400">Equipamentos modernos, profissionais qualificados e resultados garantidos</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-16">
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20 hover:border-purple-500/50 transition-all transform hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Equipamentos de Última Geração</h3>
                    <p class="text-gray-300 leading-relaxed">
                        Academia equipada com os melhores aparelhos do mercado para garantir seus resultados.
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20 hover:border-pink-500/50 transition-all transform hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Personal Trainers Certificados</h3>
                    <p class="text-gray-300 leading-relaxed">
                        Profissionais altamente qualificados prontos para te ajudar a alcançar seus objetivos.
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20 hover:border-blue-500/50 transition-all transform hover:scale-105">
                    <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Resultados Comprovados</h3>
                    <p class="text-gray-300 leading-relaxed">
                        Milhares de alunos já transformaram suas vidas conosco. Você será o próximo!
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-white/20">
                <h2 class="text-3xl font-bold text-white mb-8 text-center flex items-center justify-center gap-3">
                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Nossa Comunidade
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-xl p-6 border border-blue-500/30 text-center">
                        <div class="text-4xl font-bold text-white mb-2"><?= count($usuarios) ?></div>
                        <div class="text-blue-300 text-sm font-medium">Total de Membros</div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-xl p-6 border border-purple-500/30 text-center">
                        <div class="text-4xl font-bold text-white mb-2">
                            <?= count(array_filter($usuarios, fn($u) => $u['tipo'] === 'aluno')) ?>
                        </div>
                        <div class="text-purple-300 text-sm font-medium">Alunos Ativos</div>
                    </div>
                    <div class="bg-gradient-to-br from-pink-500/20 to-pink-600/20 rounded-xl p-6 border border-pink-500/30 text-center">
                        <div class="text-4xl font-bold text-white mb-2">
                            <?= count(array_filter($usuarios, fn($u) => $u['tipo'] === 'personal')) ?>
                        </div>
                        <div class="text-pink-300 text-sm font-medium">Personal Trainers</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-500/20 to-green-600/20 rounded-xl p-6 border border-green-500/30 text-center">
                        <div class="text-4xl font-bold text-white mb-2">
                            <?= count(array_filter($usuarios, fn($u) => $u['tipo'] === 'admin')) ?>
                        </div>
                        <div class="text-green-300 text-sm font-medium">Administradores</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-gradient-to-b from-gray-900/50 to-transparent">
        <div class="container mx-auto px-4 max-w-4xl text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                Pronto para começar sua transformação?
            </h2>
            <p class="text-xl text-gray-300 mb-8">
                Junte-se a centenas de pessoas que já mudaram suas vidas na FitZone
            </p>
            <a href="cadastro.php" 
               class="inline-block bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 
                      text-white font-bold text-xl px-10 py-5 rounded-lg transition-all duration-200 
                      transform hover:scale-105 shadow-2xl hover:shadow-purple-500/50">
                Comece Sua Jornada Agora
            </a>
        </div>
    </section>
</body>
</html>
