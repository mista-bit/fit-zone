<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

require_once __DIR__ . '/../db.php';

// Verificação de acesso admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    $_SESSION['erro_acesso'] = "Acesso negado. Apenas administradores.";
    header("Location: ../login.php");
    exit();
}

$db = new BancoDeDados();

// Busca estatísticas para o dashboard
$totalAlunos = count($db->ler('alunos'));
$totalPersonais = count($db->ler('personais'));
$totalTreinos = count($db->ler('treinos'));
$totalExercicios = count($db->ler('exercicios'));

// Busca dados recentes
$alunosRecentes = $db->consultar('SELECT * FROM alunos ORDER BY created_at DESC LIMIT 5');
$treinosRecentes = $db->consultar('
    SELECT t.*, a.nome as aluno_nome, p.nome as personal_nome 
    FROM treinos t
    LEFT JOIN alunos a ON t.aluno_id = a.id
    LEFT JOIN personais p ON t.personal_id = p.id
    ORDER BY t.created_at DESC LIMIT 5
');

// Handler para requisições AJAX
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_usuarios':
            $alunos = $db->consultar('SELECT id, nome, email, created_at, "aluno" as tipo FROM alunos');
            $personais = $db->consultar('SELECT id, nome, email, created_at, "personal" as tipo FROM personais');
            $admins = $db->consultar('SELECT id, nome, email, created_at, "admin" as tipo FROM admins');
            echo json_encode(['usuarios' => array_merge($alunos, $personais, $admins)]);
            exit();
            
        case 'get_planos':
            echo json_encode(['planos' => $db->ler('planos')]);
            exit();
            
        case 'get_exercicios':
            echo json_encode(['exercicios' => $db->ler('exercicios')]);
            exit();
            
        case 'delete_usuario':
            $id = $_GET['id'] ?? 0;
            $tipo = $_GET['tipo'] ?? '';
            $tabela = $tipo === 'aluno' ? 'alunos' : ($tipo === 'personal' ? 'personais' : 'admins');
            $db->deletar($tabela, $id);
            echo json_encode(['success' => true]);
            exit();
            
        case 'delete_plano':
            $id = $_GET['id'] ?? 0;
            $db->deletar('planos', $id);
            echo json_encode(['success' => true]);
            exit();
            
        case 'delete_exercicio':
            $id = $_GET['id'] ?? 0;
            $db->deletar('exercicios', $id);
            echo json_encode(['success' => true]);
            exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - FitZone</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    
    <nav class="bg-gray-800 text-white px-8 py-4 shadow-lg border-b border-white/10">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div>
                <a href="../index.php" class="text-xl font-bold hover:text-blue-400 transition-colors">
                    <span class="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">FitZone</span> Admin
                </a>
            </div>
            <div class="flex items-center gap-6">
                <span class="text-gray-300 text-sm">Olá, <span class="text-purple-400 font-semibold"><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Admin') ?></span></span>
                <a href="../logout.php" class="text-gray-300 hover:text-white transition-colors font-medium">Sair</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-wrap gap-3 mb-8">
            <button onclick="switchTab('dashboard')" class="tab-btn px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold text-sm hover:bg-blue-600 transition-colors duration-200 shadow-md" data-tab="dashboard">
                Dashboard
            </button>
            <button onclick="switchTab('usuarios')" class="tab-btn px-6 py-3 bg-white text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors duration-200 shadow-md" data-tab="usuarios">
                Usuários
            </button>
            <button onclick="switchTab('planos')" class="tab-btn px-6 py-3 bg-white text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors duration-200 shadow-md" data-tab="planos">
                Planos
            </button>
            <button onclick="switchTab('exercicios')" class="tab-btn px-6 py-3 bg-white text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors duration-200 shadow-md" data-tab="exercicios">
                Exercícios
            </button>
            <button onclick="switchTab('treinos')" class="tab-btn px-6 py-3 bg-white text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors duration-200 shadow-md" data-tab="treinos">
                Treinos
            </button>
        </div>

        <div id="tab-dashboard" class="tab-content active">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg text-center">
                    <h3 class="text-sm font-medium mb-2 opacity-90">Total Alunos</h3>
                    <p class="text-4xl font-bold"><?= $totalAlunos ?></p>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-lg text-center">
                    <h3 class="text-sm font-medium mb-2 opacity-90">Personal Trainers</h3>
                    <p class="text-4xl font-bold"><?= $totalPersonais ?></p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg text-center">
                    <h3 class="text-sm font-medium mb-2 opacity-90">Treinos Ativos</h3>
                    <p class="text-4xl font-bold"><?= $totalTreinos ?></p>
                </div>
                <div class="bg-gradient-to-br from-pink-500 to-pink-600 text-white p-6 rounded-xl shadow-lg text-center">
                    <h3 class="text-sm font-medium mb-2 opacity-90">Exercícios</h3>
                    <p class="text-4xl font-bold"><?= $totalExercicios ?></p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Alunos Recentes
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($alunosRecentes as $aluno): ?>
                        <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($aluno['nome']) ?></p>
                            <p class="text-sm text-gray-600"><?= htmlspecialchars($aluno['email']) ?></p>
                            <p class="text-xs text-gray-500 mt-1"><?= date('d/m/Y', strtotime($aluno['created_at'])) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Treinos Recentes
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($treinosRecentes as $treino): ?>
                        <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-green-500">
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($treino['nome']) ?></p>
                            <p class="text-sm text-gray-600">Aluno: <?= htmlspecialchars($treino['aluno_nome']) ?></p>
                            <p class="text-sm text-gray-600">Personal: <?= htmlspecialchars($treino['personal_nome']) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="tab-usuarios" class="tab-content">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Gerenciar Usuários</h2>
                    <button onclick="location.href='../cadastro.php'" class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-3 rounded-lg transition-colors duration-200 shadow-md">
                        + Novo Usuário
                    </button>
                </div>
                <div id="usuarios-list">
                    <p class="text-gray-500 text-center py-8">Carregando...</p>
                </div>
            </div>
        </div>

        <div id="tab-planos" class="tab-content">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Gerenciar Planos</h2>
                    <button onclick="location.href='novo-plano.php'" class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-3 rounded-lg transition-colors duration-200 shadow-md">
                        + Novo Plano
                    </button>
                </div>
                <div id="planos-list">
                    <p class="text-gray-500 text-center py-8">Carregando...</p>
                </div>
            </div>
        </div>

        <div id="tab-exercicios" class="tab-content">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Gerenciar Exercícios</h2>
                    <button onclick="location.href='novo-exercicio.php'" class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-3 rounded-lg transition-colors duration-200 shadow-md">
                        + Novo Exercício
                    </button>
                </div>
                <div id="exercicios-list">
                    <p class="text-gray-500 text-center py-8">Carregando...</p>
                </div>
            </div>
        </div>

        <div id="tab-treinos" class="tab-content">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Todos os Treinos</h2>
                <div class="space-y-4">
                    <?php foreach ($treinosRecentes as $treino): ?>
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-5 rounded-lg border-l-4 border-blue-500">
                        <h3 class="text-lg font-bold text-gray-800 mb-2"><?= htmlspecialchars($treino['nome']) ?></h3>
                        <p class="text-sm text-gray-600 mb-3"><?= htmlspecialchars($treino['descricao'] ?? 'Sem descrição') ?></p>
                        <div class="flex items-center gap-4 text-sm">
                            <p class="text-gray-700">Aluno: <span class="font-semibold"><?= htmlspecialchars($treino['aluno_nome']) ?></span></p>
                            <p class="text-gray-700">Personal: <span class="font-semibold"><?= htmlspecialchars($treino['personal_nome']) ?></span></p>
                            <span class="ml-auto px-3 py-1 rounded-full text-xs font-semibold <?= $treino['ativo'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' ?>">
                                <?= $treino['ativo'] ? 'Ativo' : 'Inativo' ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

    <script>
    // Função para escape de HTML (previne XSS)
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Sistema de Tabs
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-blue-500', 'text-white');
            btn.classList.add('bg-white', 'text-gray-700');
        });
        
        document.getElementById(`tab-${tabName}`).classList.add('active');
        const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
        activeBtn.classList.remove('bg-white', 'text-gray-700');
        activeBtn.classList.add('bg-blue-500', 'text-white');
        
        if (tabName === 'usuarios') loadUsuarios();
        if (tabName === 'planos') loadPlanos();
        if (tabName === 'exercicios') loadExercicios();
    }

    // Carrega usuários via AJAX
    async function loadUsuarios() {
        const response = await fetch('?action=get_usuarios');
        const data = await response.json();
        
        const badges = {
            'aluno': 'bg-blue-100 text-blue-700',
            'personal': 'bg-purple-100 text-purple-700',
            'admin': 'bg-red-100 text-red-700'
        };
        
        const html = data.usuarios.map(u => `
            <div class="bg-gray-50 p-5 rounded-lg border-l-4 border-blue-500 flex items-center justify-between mb-3">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <strong class="text-gray-800 text-lg">${escapeHtml(u.nome)}</strong>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold ${badges[u.tipo] || 'bg-gray-100 text-gray-700'}">${escapeHtml(u.tipo)}</span>
                    </div>
                    <p class="text-sm text-gray-600">${escapeHtml(u.email)}</p>
                    <p class="text-xs text-gray-500 mt-1">${new Date(u.created_at).toLocaleDateString('pt-BR')}</p>
                </div>
                <button onclick="deleteUsuario(${u.id}, '${escapeHtml(u.tipo)}')" class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
                    Excluir
                </button>
            </div>
        `).join('');
        
        document.getElementById('usuarios-list').innerHTML = html;
    }

    // Carrega planos
    async function loadPlanos() {
        const response = await fetch('?action=get_planos');
        const data = await response.json();
        
        const html = data.planos.map(p => `
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-5 rounded-lg border-l-4 border-purple-500 mb-3">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">${escapeHtml(p.nome)}</h3>
                        <p class="text-2xl font-bold text-purple-600 mb-2">R$ ${parseFloat(p.preco).toFixed(2)}</p>
                        <p class="text-sm text-gray-600">${escapeHtml(p.descricao || '')}</p>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <button onclick="location.href='editar-plano.php?id=${p.id}'" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
                            Editar
                        </button>
                        <button onclick="deletePlano(${p.id})" class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
                            Excluir
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
        
        document.getElementById('planos-list').innerHTML = html;
    }

    // Carrega exercícios
    async function loadExercicios() {
        const response = await fetch('?action=get_exercicios');
        const data = await response.json();
        
        const html = data.exercicios.map(e => `
            <div class="bg-gray-50 p-5 rounded-lg border-l-4 border-green-500 flex items-center justify-between mb-3">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <strong class="text-gray-800 text-lg">${escapeHtml(e.nome)}</strong>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">${escapeHtml(e.categoria)}</span>
                    </div>
                    <p class="text-sm text-gray-600">${escapeHtml(e.descricao || 'Sem descrição')}</p>
                </div>
                <div class="flex gap-2 ml-4">
                    <button onclick="location.href='editar-exercicio.php?id=${e.id}'" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
                        Editar
                    </button>
                    <button onclick="deleteExercicio(${e.id})" class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
                        Excluir
                    </button>
                </div>
            </div>
        `).join('');
        
        document.getElementById('exercicios-list').innerHTML = html;
    }

    // Deleta usuário
    async function deleteUsuario(id, tipo) {
        if (!confirm('Tem certeza que deseja excluir este usuário?')) return;
        
        await fetch(`?action=delete_usuario&id=${id}&tipo=${tipo}`);
        loadUsuarios();
    }

    // Deleta plano
    async function deletePlano(id) {
        if (!confirm('Tem certeza que deseja excluir este plano?')) return;
        
        await fetch(`?action=delete_plano&id=${id}`);
        loadPlanos();
    }

    // Deleta exercício
    async function deleteExercicio(id) {
        if (!confirm('Tem certeza que deseja excluir este exercício?')) return;
        
        await fetch(`?action=delete_exercicio&id=${id}`);
        loadExercicios();
    }
    </script>

</body>
</html>