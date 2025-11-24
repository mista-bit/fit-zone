-- ============================================================================
-- FITZONE DATABASE SCHEMA - SQLite
-- ============================================================================
-- Script de criação do banco de dados normalizado
-- Salve este arquivo como: data/schema.sql
--
-- Estrutura: Normalizada (3FN) mas simples de entender
-- Migração: Converte users.json para estrutura relacional profissional
-- ============================================================================

-- ----------------------------------------------------------------------------
-- TABELA: users
-- Armazena todos os usuários do sistema (Alunos e Personal Trainers)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    type TEXT NOT NULL CHECK(type IN ('aluno', 'personal')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Índice para buscar usuários por tipo rapidamente
CREATE INDEX IF NOT EXISTS idx_users_type ON users(type);

-- Índice para buscar por email (já tem UNIQUE, mas índice melhora SELECT)
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);

-- ----------------------------------------------------------------------------
-- TABELA: exercicios
-- Catálogo de exercícios disponíveis na academia
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS exercicios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL UNIQUE,
    descricao TEXT,
    categoria TEXT, -- ex: 'Peito', 'Pernas', 'Cardio', 'Costas'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------------------------------------------
-- TABELA: treinos
-- Relacionamento N:N entre alunos e exercícios
-- Cada linha representa um exercício no treino de um aluno
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS treinos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    aluno_id INTEGER NOT NULL,
    exercicio_id INTEGER NOT NULL,
    ordem INTEGER NOT NULL DEFAULT 0, -- mantém a sequência do treino
    series INTEGER DEFAULT 3,
    repeticoes INTEGER DEFAULT 12,
    observacoes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Chaves estrangeiras
    FOREIGN KEY (aluno_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (exercicio_id) REFERENCES exercicios(id) ON DELETE RESTRICT,
    
    -- Garante que um aluno não tenha o mesmo exercício duplicado
    UNIQUE(aluno_id, exercicio_id)
);

-- Índice para buscar treinos de um aluno específico (query mais comum)
CREATE INDEX IF NOT EXISTS idx_treinos_aluno ON treinos(aluno_id);

-- Índice para buscar todos os alunos que fazem determinado exercício
CREATE INDEX IF NOT EXISTS idx_treinos_exercicio ON treinos(exercicio_id);

-- ----------------------------------------------------------------------------
-- DADOS INICIAIS - Catálogo de Exercícios Comuns
-- ----------------------------------------------------------------------------
INSERT INTO exercicios (nome, categoria) VALUES 
    ('Supino Reto', 'Peito'),
    ('Supino Inclinado', 'Peito'),
    ('Agachamento', 'Pernas'),
    ('Leg Press', 'Pernas'),
    ('Corrida', 'Cardio'),
    ('Esteira', 'Cardio'),
    ('Rosca Direta', 'Braços'),
    ('Tríceps Pulley', 'Braços'),
    ('Remada Curvada', 'Costas'),
    ('Puxada Frontal', 'Costas'),
    ('Desenvolvimento', 'Ombros'),
    ('Elevação Lateral', 'Ombros'),
    ('Abdominal', 'Core'),
    ('Prancha', 'Core');

-- ----------------------------------------------------------------------------
-- DADOS DE EXEMPLO - Usuários e Treinos
-- Remova esta seção se não quiser dados de teste
-- ----------------------------------------------------------------------------
INSERT INTO users (name, email, type) VALUES 
    ('João Silva', 'joao@exemplo.com', 'aluno'),
    ('Maria Santos', 'maria@exemplo.com', 'personal'),
    ('Pedro Oliveira', 'pedro@exemplo.com', 'aluno');

-- Treino do João (aluno_id = 1)
INSERT INTO treinos (aluno_id, exercicio_id, ordem, series, repeticoes) VALUES 
    (1, 1, 1, 3, 12),  -- Supino Reto
    (1, 3, 2, 4, 10),  -- Agachamento
    (1, 5, 3, 1, 20);  -- Corrida (20 minutos)

-- Treino do Pedro (aluno_id = 3)
INSERT INTO treinos (aluno_id, exercicio_id, ordem, series, repeticoes, observacoes) VALUES 
    (3, 7, 1, 3, 15, 'Aumentar carga progressivamente'),
    (3, 9, 2, 3, 12, NULL),
    (3, 13, 3, 3, 20, 'Manter postura');

-- ----------------------------------------------------------------------------
-- VIEW ÚTIL: treinos_completos
-- Facilita consultas mostrando treinos com nomes dos exercícios
-- ----------------------------------------------------------------------------
CREATE VIEW IF NOT EXISTS treinos_completos AS
SELECT 
    t.id,
    t.aluno_id,
    u.name AS aluno_nome,
    t.exercicio_id,
    e.nome AS exercicio_nome,
    e.categoria AS exercicio_categoria,
    t.ordem,
    t.series,
    t.repeticoes,
    t.observacoes
FROM treinos t
JOIN users u ON t.aluno_id = u.id
JOIN exercicios e ON t.exercicio_id = e.id
ORDER BY t.aluno_id, t.ordem;