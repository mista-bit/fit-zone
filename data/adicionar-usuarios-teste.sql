-- Script para adicionar usuários de teste ao banco existente
-- Execute este script se você já tinha um banco criado

-- Personal de teste
INSERT OR IGNORE INTO personais (id, nome, email, senha, especialidade) VALUES 
    (1, 'João Personal', 'personal@fitzone.com', 'personal123', 'Musculação e Hipertrofia');

-- Aluno de teste
INSERT OR IGNORE INTO alunos (id, nome, email, senha, altura, peso, plano_id, personal_id) VALUES 
    (1, 'Maria Aluna', 'aluno@fitzone.com', 'aluno123', 1.65, 60.0, 2, 1);

-- Verificar se foram inseridos
SELECT 'PERSONAIS:' as tipo;
SELECT * FROM personais;

SELECT 'ALUNOS:' as tipo;
SELECT * FROM alunos;

SELECT 'ADMINS:' as tipo;
SELECT * FROM admins;
