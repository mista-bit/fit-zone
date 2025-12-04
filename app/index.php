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

    <!-- Footer -->
    <footer class="bg-gradient-to-b from-gray-900/50 to-black border-t border-white/10">
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
                        <li><a href="cadastro.php" class="text-gray-400 hover:text-purple-400 transition-colors">Cadastre-se</a></li>
                        <li><a href="login.php" class="text-gray-400 hover:text-purple-400 transition-colors">Login</a></li>
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
