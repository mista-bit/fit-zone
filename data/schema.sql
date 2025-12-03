-- FitZone SQLite Schema - Tabelas independentes por tipo de usuário

-- Tabela de Alunos
CREATE TABLE IF NOT EXISTS alunos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    altura REAL,
    peso REAL,
    plano_id INTEGER DEFAULT 1,
    personal_id INTEGER,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (plano_id) REFERENCES planos(id),
    FOREIGN KEY (personal_id) REFERENCES personais(id) ON DELETE SET NULL
);

-- Tabela de Personal Trainers
CREATE TABLE IF NOT EXISTS personais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    especialidade TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Administradores
CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    nivel_acesso INTEGER DEFAULT 1,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS planos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL UNIQUE,
    preco REAL NOT NULL,
    descricao TEXT,
    beneficios TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS exercicios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL UNIQUE,
    categoria TEXT CHECK(categoria IN ('Peito','Pernas','Cardio','Costas','Braços','Ombros','Core')),
    descricao TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS treinos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    aluno_id INTEGER NOT NULL,
    personal_id INTEGER NOT NULL,
    nome TEXT NOT NULL,
    descricao TEXT,
    ativo INTEGER DEFAULT 1,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    FOREIGN KEY (personal_id) REFERENCES personais(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS treino_exercicios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    treino_id INTEGER NOT NULL,
    exercicio_id INTEGER NOT NULL,
    ordem INTEGER NOT NULL DEFAULT 0,
    series INTEGER DEFAULT 3,
    repeticoes INTEGER DEFAULT 12,
    carga TEXT,
    descanso TEXT,
    observacoes TEXT,
    FOREIGN KEY (treino_id) REFERENCES treinos(id) ON DELETE CASCADE,
    FOREIGN KEY (exercicio_id) REFERENCES exercicios(id) ON DELETE RESTRICT
);

CREATE INDEX IF NOT EXISTS idx_alunos_email ON alunos(email);
CREATE INDEX IF NOT EXISTS idx_alunos_plano ON alunos(plano_id);
CREATE INDEX IF NOT EXISTS idx_alunos_personal ON alunos(personal_id);
CREATE INDEX IF NOT EXISTS idx_personais_email ON personais(email);
CREATE INDEX IF NOT EXISTS idx_admins_email ON admins(email);
CREATE INDEX IF NOT EXISTS idx_exercicios_categoria ON exercicios(categoria);
CREATE INDEX IF NOT EXISTS idx_treinos_aluno ON treinos(aluno_id);
CREATE INDEX IF NOT EXISTS idx_treinos_personal ON treinos(personal_id);

INSERT OR IGNORE INTO planos (id, nome, preco, descricao, beneficios) VALUES 
    (1, 'basico', 99.90, 'Plano Básico', '["Acesso à academia", "1 aula experimental"]'),
    (2, 'premium', 149.90, 'Plano Premium', '["Acesso ilimitado", "Aulas em grupo", "Suporte nutricional"]'),
    (3, 'vip', 249.90, 'Plano VIP', '["Acesso total", "Personal trainer", "Nutricionista", "Avaliação física"]');

-- Admin padrão: email: admin@fitzone.com | senha: admin123
INSERT OR IGNORE INTO admins (id, nome, email, senha, nivel_acesso) VALUES 
    (1, 'Administrador', 'admin@fitzone.com', 'admin123', 1);

-- Personal de teste: email: personal@fitzone.com | senha: personal123
INSERT OR IGNORE INTO personais (id, nome, email, senha, especialidade) VALUES 
    (1, 'João Personal', 'personal@fitzone.com', 'personal123', 'Musculação e Hipertrofia');

-- Aluno de teste: email: aluno@fitzone.com | senha: aluno123
INSERT OR IGNORE INTO alunos (id, nome, email, senha, altura, peso, plano_id, personal_id) VALUES 
    (1, 'Maria Aluna', 'aluno@fitzone.com', 'aluno123', 1.65, 60.0, 2, 1);

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
