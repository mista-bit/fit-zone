<?php
/**
 * Script para adicionar usuários de teste ao banco de dados
 * Execute este arquivo uma vez: http://localhost/fit-zone/data/criar-usuarios-teste.php
 */

require_once __DIR__ . '/../app/db.php';

$db = new BancoDeDados();

echo "<h1>Criando Usuários de Teste...</h1>";

try {
    // Verifica se o personal já existe
    $personalExiste = $db->consultarUnico("SELECT * FROM personais WHERE email = 'personal@fitzone.com'", []);
    
    if (!$personalExiste) {
        $db->inserir('personais', [
            'nome' => 'João Personal',
            'email' => 'personal@fitzone.com',
            'senha' => 'personal123',
            'especialidade' => 'Musculação e Hipertrofia'
        ]);
        echo "<p>✅ Personal Trainer criado com sucesso!</p>";
    } else {
        echo "<p>⚠️ Personal Trainer já existe.</p>";
    }
    
    // Verifica se o aluno já existe
    $alunoExiste = $db->consultarUnico("SELECT * FROM alunos WHERE email = 'aluno@fitzone.com'", []);
    
    if (!$alunoExiste) {
        $db->inserir('alunos', [
            'nome' => 'Maria Aluna',
            'email' => 'aluno@fitzone.com',
            'senha' => 'aluno123',
            'altura' => 1.65,
            'peso' => 60.0,
            'plano_id' => 2,
            'personal_id' => 1
        ]);
        echo "<p>✅ Aluno criado com sucesso!</p>";
    } else {
        echo "<p>⚠️ Aluno já existe.</p>";
    }
    
    // Verifica se o admin já existe
    $adminExiste = $db->consultarUnico("SELECT * FROM admins WHERE email = 'admin@fitzone.com'", []);
    
    if (!$adminExiste) {
        $db->inserir('admins', [
            'nome' => 'Administrador',
            'email' => 'admin@fitzone.com',
            'senha' => 'admin123',
            'nivel_acesso' => 1
        ]);
        echo "<p>✅ Admin criado com sucesso!</p>";
    } else {
        echo "<p>⚠️ Admin já existe.</p>";
    }
    
    echo "<hr>";
    echo "<h2>📋 Credenciais de Acesso:</h2>";
    echo "<h3>👨‍💼 Admin:</h3>";
    echo "<p>Email: <strong>admin@fitzone.com</strong><br>Senha: <strong>admin123</strong></p>";
    
    echo "<h3>🏋️ Personal Trainer:</h3>";
    echo "<p>Email: <strong>personal@fitzone.com</strong><br>Senha: <strong>personal123</strong></p>";
    
    echo "<h3>👤 Aluno:</h3>";
    echo "<p>Email: <strong>aluno@fitzone.com</strong><br>Senha: <strong>aluno123</strong></p>";
    
    echo "<hr>";
    echo "<p><a href='../app/login.php' style='background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px;'>➡️ Ir para Login</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f4f4f4;
    }
    h1, h2, h3 {
        color: #2c3e50;
    }
    p {
        line-height: 1.6;
    }
</style>
