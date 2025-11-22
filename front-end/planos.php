<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

require_once __DIR__ . '/../back-end/db.php';

$db = new BancoDeDados();
$paginaAtual = 'planos';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos - FitZone</title>
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
                       class="text-gray-300 hover:text-white transition-colors <?= $paginaAtual === 'planos' ? 'text-white font-semibold' : '' ?>">
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
                    <a href="../back-end/logout.php" 
                       class="text-gray-300 hover:text-white transition-colors">
                        Sair
                    </a>
                    <?php else: ?>
                    <a href="../back-end/login.php" 
                       class="text-gray-300 hover:text-white transition-colors">
                        Login
                    </a>
                    <a href="../back-end/cadastro.php" 
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
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-white mb-2">
                <span class="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                    Nossos Planos
                </span>
            </h1>
            <p class="text-gray-400">Escolha o plano ideal para você</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">

            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-6 border border-white/20 hover:border-blue-500/50 transition-all">
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-white mb-2">Básico</h3>
                    <div class="text-4xl font-bold text-blue-400 mb-1">R$ 99<span class="text-lg text-gray-400">/mês</span></div>
                </div>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-center gap-2 text-gray-300"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Acesso à academia</li>
                    <li class="flex items-center gap-2 text-gray-300"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Treinos básicos</li>
                    <li class="flex items-center gap-2 text-gray-300"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Suporte por email</li>
                </ul>

                <a href="../back-end/cadastro.php?plano=basico" 
                   class="w-full block text-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition-all">
                    Escolher Plano
                </a>
            </div>

            <div class="bg-gradient-to-br from-blue-500/20 to-purple-500/20 backdrop-blur-lg rounded-2xl shadow-2xl p-6 border-2 border-blue-500/50 hover:border-purple-500/50 transition-all transform scale-105">
                <div class="text-center mb-6">
                    <span class="bg-blue-500 text-white text-xs font-semibold px-3 py-1 rounded-full mb-2 inline-block">Mais Popular</span>
                    <h3 class="text-2xl font-bold text-white mb-2">Premium</h3>
                    <div class="text-4xl font-bold text-purple-400 mb-1">R$ 149<span class="text-lg text-gray-400">/mês</span></div>
                </div>

                <ul class="space-y-3 mb-6">
                    <li class="flex items-center gap-2 text-gray-300"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Tudo do plano Básico</li>
                    <li class="flex items-center gap-2 text-gray-300"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Personal Trainer</li>
                    <li class="flex items-center gap-2 text-gray-300"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Acompanhamento nutricional</li>
                    <li class="flex items-center gap-2 text-gray-300"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Suporte 24/7</li>
                </ul>

                <a href="../back-end/cadastro.php?plano=premium" 
                   class="w-full block text-center bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 
                          text-white font-semibold py-2 px-6 rounded-lg transition-all">
                    Escolher Plano
                </a>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-6 border border-white/20 hover:border-purple-500/50 transition-all">
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-white mb-2">VIP</h3>
                    <div class="text-4xl font-bold text-purple-400 mb-1">R$ 249<span class="text-lg text-gray-400">/mês</span></div>
                </div>

                <ul class="space-y-3 mb-6">
                    <li class="flex items-center gap-2 text-gray-300"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Tudo do plano Premium</li>
                    <li class="flex items-center gap-2 text-gray-300"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Personal Trainer exclusivo</li>
                    <li class="flex items-center gap-2 text-gray-300"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Acesso a todas as áreas</li>
                    <li class="flex items-center gap-2 text-gray-300"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Consultoria premium</li>
                </ul>

                <a href="../back-end/cadastro.php?plano=vip" 
                   class="w-full block text-center bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-6 rounded-lg transition-all">
                    Escolher Plano
                </a>
            </div>

        </div>
    </div>
</body>
</html>
