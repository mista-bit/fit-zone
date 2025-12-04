<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

require_once __DIR__ . '/db.php';

$db = new BancoDeDados();
$mensagem = "";

$planoGet = isset($_GET['plano']) ? $_GET['plano'] : "";

if (isset($_POST['nome'])) {

    $nome = trim($_POST['nome']);
    $email = trim(strtolower($_POST['email']));
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];
    
    // Validações básicas
    if (empty($nome) || empty($email) || empty($senha)) {
        $mensagem = "Todos os campos são obrigatórios!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Email inválido!";
    } elseif (strlen($senha) < 6) {
        $mensagem = "A senha deve ter pelo menos 6 caracteres!";
    } else {
        try {
            if ($tipo === "aluno") {
                $altura = !empty($_POST["altura"]) ? floatval($_POST["altura"]) : null;
                $peso = !empty($_POST["peso"]) ? floatval($_POST["peso"]) : null;
                $plano = $_POST["plano"] ?? "basico";
                $plano_id = ($plano === 'basico' ? 1 : ($plano === 'premium' ? 2 : 3));

                $id = $db->inserir("alunos", [
                    "nome" => $nome,
                    "email" => $email,
                    "senha" => $senha,
                    "altura" => $altura,
                    "peso" => $peso,
                    "plano_id" => $plano_id,
                    "created_at" => date("Y-m-d H:i:s")
                ]);
            }

            if ($tipo === "personal") {
                $especialidade = trim($_POST["especialidade"] ?? "");
                
                $id = $db->inserir("personais", [
                    "nome" => $nome,
                    "email" => $email,
                    "senha" => $senha,
                    "especialidade" => !empty($especialidade) ? $especialidade : null,
                    "created_at" => date("Y-m-d H:i:s")
                ]);
            }

            if ($tipo === "admin") {
                $id = $db->inserir("admins", [
                    "nome" => $nome,
                    "email" => $email,
                    "senha" => $senha,
                    "nivel_acesso" => 1,
                    "created_at" => date("Y-m-d H:i:s")
                ]);
            }

            if ($id) {
                $_SESSION['mensagem_sucesso'] = "Usuário cadastrado com sucesso!";
                header("Location: index.php");
                exit();
            } else {
                $mensagem = "Erro ao cadastrar. Email já pode estar em uso.";
            }
        } catch (Exception $e) {
            $mensagem = "Erro ao cadastrar: Email já cadastrado ou dados inválidos.";
        }
    }
}

if (isset($_SESSION['mensagem_sucesso'])) {
    $mensagem = $_SESSION['mensagem_sucesso'];
    unset($_SESSION['mensagem_sucesso']);
}

$paginaAtual = 'cadastro';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - FitZone</title>
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
                    <a href="index.php" class="text-gray-300 hover:text-white transition-colors">Home</a>
                    <a href="planos.php" class="text-gray-300 hover:text-white transition-colors">Planos</a>

                    <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin'): ?>
                        <a href="clientes.php" class="text-gray-300 hover:text-white">Clientes</a>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <?php if ($_SESSION['usuario_tipo'] !== 'admin'): ?>
                            <a href="area-cliente.php" class="text-gray-300 hover:text-white">Minha Área</a>
                        <?php endif; ?>

                        <span class="text-gray-300 text-sm">
                            Olá, <span class="text-purple-400 font-semibold"><?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                        </span>
                        <a href="logout.php" class="text-gray-300 hover:text-white">Sair</a>

                    <?php else: ?>
                        <a href="login.php" class="text-gray-300 hover:text-white">Login</a>
                        <a href="cadastro.php" class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 
                        text-white font-semibold px-6 py-2 rounded-lg shadow-lg transition-all">
                            Cadastre-se
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-4xl">

        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">
                <span class="bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">Cadastro</span>
            </h1>
            <p class="text-gray-400">Preencha os dados para se cadastrar</p>
        </div>

        <?php if ($mensagem): ?>
            <div class="mb-6 bg-green-500/20 border border-green-500/50 text-green-300 px-6 py-4 rounded-lg flex items-center gap-3 backdrop-blur-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium"><?= htmlspecialchars($mensagem) ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-white/20">

            <form method="POST" class="space-y-4">

                <div>
                    <label class="block text-gray-300 text-sm font-medium mb-1.5">Nome Completo</label>
                    <input type="text" name="nome" required
                        class="w-full px-3 py-2 bg-white/5 border border-white/20 rounded-lg text-white 
                        placeholder-gray-400 text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="Digite o nome completo">
                </div>

                <div>
                    <label class="block text-gray-300 text-sm font-medium mb-1.5">Email</label>
                    <input type="email" name="email" required
                        class="w-full px-3 py-2 bg-white/5 border border-white/20 rounded-lg text-white 
                        placeholder-gray-400 text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="exemplo@email.com">
                </div>

                <div>
                    <label class="block text-gray-300 text-sm font-medium mb-1.5">Senha</label>
                    <input type="password" name="senha" required
                        class="w-full px-3 py-2 bg-white/5 border border-white/20 rounded-lg 
                        text-white placeholder-gray-400 text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="••••••••">
                </div>

                <div>
                    <label class="block text-gray-300 text-sm font-medium mb-1.5">Tipo de Usuário</label>
                    <select id="tipoUsuario" name="tipo"
                        class="w-full px-3 py-2 bg-white/5 border border-white/20 rounded-lg 
                        text-white text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="aluno" class="bg-gray-800">Aluno</option>
                        <option value="personal" class="bg-gray-800">Personal Trainer</option>
                        <option value="admin" class="bg-gray-800">Administrador</option>
                    </select>
                </div>

                <div id="camposAluno" class="space-y-4 hidden">

                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-1.5">Altura (cm)</label>
                        <input type="number" name="altura" step="0.01"
                            class="w-full px-3 py-2 bg-white/5 border border-white/20 rounded-lg 
                            text-white placeholder-gray-400 text-sm focus:ring-2 focus:ring-blue-500"
                            placeholder="Ex: 175">
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-1.5">Peso (kg)</label>
                        <input type="number" name="peso" step="0.01"
                            class="w-full px-3 py-2 bg-white/5 border border-white/20 rounded-lg 
                            text-white placeholder-gray-400 text-sm focus:ring-2 focus:ring-blue-500"
                            placeholder="Ex: 70.5">
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-1.5">Plano Escolhido</label>
                        <select name="plano"
                            class="w-full px-3 py-2 bg-white/5 border border-white/20 rounded-lg 
                            text-white text-sm focus:ring-2 focus:ring-blue-500">
                            
                            <option value="" class="bg-gray-800" <?= $planoGet === "" ? "selected" : "" ?>>Selecione...</option>
                            <option value="basico" class="bg-gray-800" <?= $planoGet === "basico" ? "selected" : "" ?>>Básico</option>
                            <option value="premium" class="bg-gray-800" <?= $planoGet === "premium" ? "selected" : "" ?>>Premium</option>
                            <option value="vip" class="bg-gray-800" <?= $planoGet === "vip" ? "selected" : "" ?>>VIP</option>

                        </select>
                    </div>

                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 
                    hover:from-blue-600 hover:to-purple-700 text-white font-semibold 
                    py-2.5 px-6 rounded-lg text-sm transform hover:scale-105 shadow-lg">
                    Cadastrar Usuário
                </button>

            </form>

        </div>
    </div>

<script>
document.getElementById("tipoUsuario").addEventListener("change", function () {
    let campos = document.getElementById("camposAluno");
    this.value === "aluno" ? campos.classList.remove("hidden") : campos.classList.add("hidden");
});

window.onload = () => {
    if (document.getElementById("tipoUsuario").value === "aluno") {
        document.getElementById("camposAluno").classList.remove("hidden");
    }
};
</script>

    <!-- Footer -->
    <footer class="bg-gradient-to-b from-gray-900/50 to-black border-t border-white/10 mt-12">
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
