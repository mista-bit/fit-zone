-- ============================================================================
-- FITZONE DATABASE SCHEMA - SQLite
-- ============================================================================
-- Script de criação do banco de dados normalizado (3FN)
-- Sistema de gestão de alunos e personal trainers com treinos personalizados
-- ============================================================================

-- ----------------------------------------------------------------------------
-- TABELA: users
-- Armazena todos os usuários do sistema (Alunos e Personal Trainers)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    type TEXT NOT NULL CHECK(type IN ('aluno', 'personal')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_users_type ON users(type);
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);

-- ----------------------------------------------------------------------------
-- TABELA: planos
-- Planos de assinatura disponíveis para alunos
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS planos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL UNIQUE,
    preco REAL NOT NULL,
    descricao TEXT,
    beneficios TEXT, -- JSON com lista de benefícios
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT OR IGNORE INTO planos (id, nome, preco, descricao, beneficios) VALUES 
    (1, 'basico', 99.90, 'Plano Básico', '["Acesso à academia", "1 aula experimental"]'),
    (2, 'premium', 149.90, 'Plano Premium', '["Acesso ilimitado", "Aulas em grupo", "Suporte nutricional"]'),
    (3, 'vip', 249.90, 'Plano VIP', '["Acesso total", "Personal trainer", "Nutricionista", "Avaliação física"]');

-- ----------------------------------------------------------------------------
-- TABELA: alunos
-- Dados específicos de alunos (1:1 com users onde type='aluno')
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS alunos (
    user_id INTEGER PRIMARY KEY,
    plano_id INTEGER NOT NULL DEFAULT 1,
    personal_id INTEGER, -- Personal trainer vinculado (pode ser NULL)
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plano_id) REFERENCES planos(id),
    FOREIGN KEY (personal_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_alunos_personal ON alunos(personal_id);

-- ----------------------------------------------------------------------------
-- TABELA: solicitacoes
-- Solicitações de alunos para personal trainers
-- Status: pending (aguardando), accepted (aceito), rejected (recusado)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS solicitacoes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    aluno_id INTEGER NOT NULL,
    personal_id INTEGER NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending', 'accepted', 'rejected')),
    mensagem TEXT, -- Mensagem do aluno ao solicitar
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (personal_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(aluno_id, personal_id) -- Evita duplicatas
);

CREATE INDEX IF NOT EXISTS idx_solicitacoes_personal ON solicitacoes(personal_id, status);
CREATE INDEX IF NOT EXISTS idx_solicitacoes_aluno ON solicitacoes(aluno_id);

-- ----------------------------------------------------------------------------
-- TABELA: exercicios
-- Catálogo de exercícios disponíveis na academia
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS exercicios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL UNIQUE,
    descricao TEXT,
    categoria TEXT CHECK(categoria IN ('Peito', 'Pernas', 'Cardio', 'Costas', 'Braços', 'Ombros', 'Core')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT OR IGNORE INTO exercicios (nome, categoria, descricao) VALUES 
    ('Supino Reto', 'Peito', 'Exercício básico para peitoral'),
    ('Supino Inclinado', 'Peito', 'Foca na parte superior do peitoral'),
    ('Agachamento Livre', 'Pernas', 'Exercício completo para membros inferiores'),
    ('Leg Press 45°', 'Pernas', 'Fortalecimento de quadríceps e glúteos'),
    ('Corrida', 'Cardio', 'Exercício aeróbico'),
    ('Esteira', 'Cardio', 'Caminhada ou corrida indoor'),
    ('Rosca Direta', 'Braços', 'Isolamento de bíceps'),
    ('Tríceps Pulley', 'Braços', 'Isolamento de tríceps'),
    ('Remada Curvada', 'Costas', 'Desenvolvimento das costas'),
    ('Puxada Frontal', 'Costas', 'Fortalecimento do latíssimo'),
    ('Desenvolvimento', 'Ombros', 'Exercício composto para deltoides'),
    ('Elevação Lateral', 'Ombros', 'Isolamento de deltoide medial'),
    ('Abdominal Reto', 'Core', 'Fortalecimento abdominal'),
    ('Prancha', 'Core', 'Isometria para core completo');

-- ----------------------------------------------------------------------------
-- TABELA: treinos
-- Treinos criados por personal trainers para alunos
-- Um treino é um conjunto de exercícios
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS treinos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    aluno_id INTEGER NOT NULL,
    personal_id INTEGER NOT NULL,
    nome TEXT NOT NULL, -- Ex: "Treino A - Peito e Tríceps"
    descricao TEXT,
    ativo INTEGER DEFAULT 1, -- 1 = ativo, 0 = arquivado
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (personal_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_treinos_aluno ON treinos(aluno_id, ativo);
CREATE INDEX IF NOT EXISTS idx_treinos_personal ON treinos(personal_id);

-- ----------------------------------------------------------------------------
-- TABELA: treino_exercicios
-- Exercícios que compõem cada treino (relacionamento N:N)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS treino_exercicios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    treino_id INTEGER NOT NULL,
    exercicio_id INTEGER NOT NULL,
    ordem INTEGER NOT NULL DEFAULT 0,
    series INTEGER DEFAULT 3,
    repeticoes INTEGER DEFAULT 12,
    carga TEXT, -- Ex: "20kg", "50% 1RM"
    descanso TEXT, -- Ex: "60s", "1min"
    observacoes TEXT,
    FOREIGN KEY (treino_id) REFERENCES treinos(id) ON DELETE CASCADE,
    FOREIGN KEY (exercicio_id) REFERENCES exercicios(id) ON DELETE RESTRICT
);

CREATE INDEX IF NOT EXISTS idx_treino_exercicios_treino ON treino_exercicios(treino_id, ordem);

-- ----------------------------------------------------------------------------
-- VIEWS ÚTEIS
-- ----------------------------------------------------------------------------

-- View: Lista de personals disponíveis com quantidade de alunos
CREATE VIEW IF NOT EXISTS personals_disponiveis AS
SELECT 
    u.id,
    u.name,
    u.email,
    COUNT(DISTINCT a.user_id) AS total_alunos
FROM users u
LEFT JOIN alunos a ON a.personal_id = u.id
WHERE u.type = 'personal'
GROUP BY u.id, u.name, u.email;

-- View: Treinos completos com exercícios
CREATE VIEW IF NOT EXISTS treinos_completos AS
SELECT 
    t.id AS treino_id,
    t.nome AS treino_nome,
    t.descricao AS treino_descricao,
    t.ativo,
    ua.name AS aluno_nome,
    up.name AS personal_nome,
    te.ordem,
    e.nome AS exercicio_nome,
    e.categoria,
    te.series,
    te.repeticoes,
    te.carga,
    te.descanso,
    te.observacoes
FROM treinos t
JOIN users ua ON t.aluno_id = ua.id
JOIN users up ON t.personal_id = up.id
LEFT JOIN treino_exercicios te ON te.treino_id = t.id
LEFT JOIN exercicios e ON te.exercicio_id = e.id
ORDER BY t.id, te.ordem;

-- View: Solicitações pendentes
CREATE VIEW IF NOT EXISTS solicitacoes_pendentes AS
SELECT 
    s.id,
    s.status,
    s.mensagem,
    s.created_at,
    ua.id AS aluno_id,
    ua.name AS aluno_nome,
    ua.email AS aluno_email,
    up.id AS personal_id,
    up.name AS personal_nome
FROM solicitacoes s
JOIN users ua ON s.aluno_id = ua.id
JOIN users up ON s.personal_id = up.id
WHERE s.status = 'pending'
ORDER BY s.created_at DESC;

-- ============================================================================
-- FIM DO SCHEMA
-- ============================================================================