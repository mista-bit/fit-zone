<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

require_once __DIR__ . '/../db.php';

// Verificação de acesso admin
// if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
//     $_SESSION['erro_acesso'] = "Acesso negado. Apenas administradores.";
//     header("Location: ../index.php");
//     exit();
// }

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
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - FitZone</title>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    
    <!-- Navbar -->
    <nav>
        <div>
            <a href="../index.php">FitZone Admin</a>
            <span>Painel Administrativo</span>
            <span>Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
            <a href="../logout.php">Sair</a>
        </div>
    </nav>

    <div>
        <!-- Tabs Navigation -->
        <div>
            <button onclick="switchTab('dashboard')" class="tab-btn active" data-tab="dashboard">
                Dashboard
            </button>
            <button onclick="switchTab('usuarios')" class="tab-btn" data-tab="usuarios">
                Usuários
            </button>
            <button onclick="switchTab('planos')" class="tab-btn" data-tab="planos">
                Planos
            </button>
            <button onclick="switchTab('exercicios')" class="tab-btn" data-tab="exercicios">
                Exercícios
            </button>
            <button onclick="switchTab('treinos')" class="tab-btn" data-tab="treinos">
                Treinos
            </button>
        </div>

        <!-- Dashboard Tab -->
        <div id="tab-dashboard" class="tab-content active">
            <!-- Estatísticas -->
            <div>
                <div>
                    <h3>Total Alunos</h3>
                    <p><?= $totalAlunos ?></p>
                </div>
                <div>
                    <h3>Personal Trainers</h3>
                    <p><?= $totalPersonais ?></p>
                </div>
                <div>
                    <h3>Treinos Ativos</h3>
                    <p><?= $totalTreinos ?></p>
                </div>
                <div>
                    <h3>Exercícios</h3>
                    <p><?= $totalExercicios ?></p>
                </div>
            </div>

            <!-- Alunos Recentes e Treinos Recentes -->
            <div>
                <div>
                    <h3>Alunos Recentes</h3>
                    <div>
                        <?php foreach ($alunosRecentes as $aluno): ?>
                        <div>
                            <p><?= htmlspecialchars($aluno['nome']) ?></p>
                            <p><?= htmlspecialchars($aluno['email']) ?></p>
                            <p><?= date('d/m/Y', strtotime($aluno['created_at'])) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <h3>Treinos Recentes</h3>
                    <div>
                        <?php foreach ($treinosRecentes as $treino): ?>
                        <div>
                            <p><?= htmlspecialchars($treino['nome']) ?></p>
                            <p>Aluno: <?= htmlspecialchars($treino['aluno_nome']) ?></p>
                            <p>Personal: <?= htmlspecialchars($treino['personal_nome']) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usuários Tab -->
        <div id="tab-usuarios" class="tab-content">
            <div>
                <h2>Gerenciar Usuários</h2>
                <button onclick="location.href='../cadastro.php'">+ Novo Usuário</button>
                <div id="usuarios-list">
                    <p>Carregando...</p>
                </div>
            </div>
        </div>

        <!-- Planos Tab -->
        <div id="tab-planos" class="tab-content">
            <div>
                <h2>Gerenciar Planos</h2>
                <div id="planos-list">
                    <p>Carregando...</p>
                </div>
            </div>
        </div>

        <!-- Exercícios Tab -->
        <div id="tab-exercicios" class="tab-content">
            <div>
                <h2>Gerenciar Exercícios</h2>
                <button onclick="alert('Funcionalidade em desenvolvimento')">+ Novo Exercício</button>
                <div id="exercicios-list">
                    <p>Carregando...</p>
                </div>
            </div>
        </div>

        <!-- Treinos Tab -->
        <div id="tab-treinos" class="tab-content">
            <div>
                <h2>Todos os Treinos</h2>
                <div>
                    <?php foreach ($treinosRecentes as $treino): ?>
                    <div>
                        <h3><?= htmlspecialchars($treino['nome']) ?></h3>
                        <p><?= htmlspecialchars($treino['descricao'] ?? 'Sem descrição') ?></p>
                        <p>Aluno: <?= htmlspecialchars($treino['aluno_nome']) ?></p>
                        <p>Personal: <?= htmlspecialchars($treino['personal_nome']) ?></p>
                        <span><?= $treino['ativo'] ? 'Ativo' : 'Inativo' ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

    <script>
    // Sistema de Tabs
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        document.getElementById(`tab-${tabName}`).classList.add('active');
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
        
        if (tabName === 'usuarios') loadUsuarios();
        if (tabName === 'planos') loadPlanos();
        if (tabName === 'exercicios') loadExercicios();
    }

    // Carrega usuários via AJAX
    async function loadUsuarios() {
        const response = await fetch('?action=get_usuarios');
        const data = await response.json();
        
        const html = data.usuarios.map(u => `
            <div>
                <div>
                    <strong>${u.nome}</strong>
                    <p>${u.email}</p>
                    <span>${u.tipo}</span>
                </div>
                <button onclick="deleteUsuario(${u.id}, '${u.tipo}')">Excluir</button>
            </div>
        `).join('');
        
        document.getElementById('usuarios-list').innerHTML = html;
    }

    // Carrega planos
    async function loadPlanos() {
        const response = await fetch('?action=get_planos');
        const data = await response.json();
        
        const html = data.planos.map(p => `
            <div>
                <h3>${p.nome}</h3>
                <p>R$ ${parseFloat(p.preco).toFixed(2)}</p>
                <p>${p.descricao}</p>
                <button>Editar Plano</button>
            </div>
        `).join('');
        
        document.getElementById('planos-list').innerHTML = html;
    }

    // Carrega exercícios
    async function loadExercicios() {
        const response = await fetch('?action=get_exercicios');
        const data = await response.json();
        
        const html = data.exercicios.map(e => `
            <div>
                <strong>${e.nome}</strong>
                <p>${e.categoria} • ${e.descricao || 'Sem descrição'}</p>
                <button>Editar</button>
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
    </script>

</body>
</html>