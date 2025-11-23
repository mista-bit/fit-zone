<?php
declare(strict_types=1);

// autoload simples: carrega classes d namespace App\ a partir de ../src/
// da p ver com use
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Garantir arquivo de dados
$dataDir = __DIR__ . '/../data';
$dataFile = $dataDir . '/users.json';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

use App\Repository\JsonUserRepository;
use App\Controller\UserController;

$repo = new JsonUserRepository($dataFile);
$controller = new UserController($repo);

// roteamento simples baseado em action
$action = $_REQUEST['action'] ?? 'list';
$errors = [];
$success = '';

try {
    if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->store($_POST);
        $success = 'Usuário criado com sucesso.';
        header('Location: ?');
        exit;
    } elseif ($action === 'edit') {
        $id = (int)($_GET['id'] ?? 0);
        $user = $controller->show($id);
        if (!$user) {
            $errors[] = 'Usuário não encontrado.';
        }
    } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        $updated = $controller->update($id, $_POST);
        if ($updated) {
            $success = 'Usuário atualizado.';
            header('Location: ?');
            exit;
        } else {
            $errors[] = 'Falha ao atualizar.';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_GET['id'] ?? 0);
        if ($controller->destroy($id)) {
            $success = 'Usuário removido.';
        } else {
            $errors[] = 'Falha ao remover usuário.';
        }
        header('Location: ?');
        exit;
    }
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}

// para a listagem principal
$users = $controller->index();

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"/>
    <title>Fit-zone</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 30px auto; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px 12px; border: 1px solid #ddd; }
        form { margin: 0; }
        .actions a { margin-right: 8px; }
        .error { color: #c00; }
        .success { color: #080; }
        .card { padding: 12px; border: 1px solid #eee; margin-bottom: 16px; background: #fafafa;}
        input[type="text"], input[type="email"] { width: 100%; padding: 6px; box-sizing: border-box; }
    </style>
</head>
<body>
    <h1>Fit-zone</h1>

    <?php if ($success): ?>
        <p class="success"><?=htmlspecialchars($success)?></p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="card error">
            <?php foreach ($errors as $err): ?>
                <div><?=htmlspecialchars($err)?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (($action ?? '') === 'edit' && isset($user) && $user): ?>
        <h2>Editar usuário #<?= $user->getId() ?></h2>
        <form method="post" action="?action=update">
            <input type="hidden" name="id" value="<?= $user->getId() ?>">
            <div>
                <label>Nome<br>
                    <input type="text" name="name" value="<?= htmlspecialchars($user->getName()) ?>">
                </label>
            </div>
            <div>
                <label>E-mail<br>
                    <input type="email" name="email" value="<?= htmlspecialchars($user->getEmail()) ?>">
                </label>
            </div>
            <div>
                <label>Tipo<br>
                    <input type="text" value="<?= method_exists($user,'getType')?htmlspecialchars($user->getType()):'n/a' ?>" disabled>
                </label>
            </div>
            <?php if (method_exists($user,'getType') && $user->getType()==='aluno'): ?>
                <div>
                    <label>Treino (separar por vírgulas)<br>
                        <input type="text" name="treino" value="<?= ($user instanceof \App\Model\Aluno) ? htmlspecialchars(implode(', ', $user->getTreino())) : '' ?>">
                    </label>
                </div>
            <?php endif; ?>
            <div style="margin-top:8px;">
                <button type="submit">Salvar</button>
                <a href="?">Cancelar</a>
            </div>
        </form>
    <?php else: ?>
        <h2>Criar usuário</h2>
        <form method="post" action="?action=create">
            <div>
                <label>Nome<br>
                    <input type="text" name="name" required>
                </label>
            </div>
            <div>
                <label>E-mail<br>
                    <input type="email" name="email" required>
                </label>
            </div>
            <div>
                <label>Tipo<br>
                    <select name="type">
                        <option value="aluno">Aluno</option>
                        <option value="personal">Personal</option>
                    </select>
                </label>
            </div>
            <div id="treinoCampo" style="display:block;">
                <label>Treino inicial (aluno) - separar por vírgulas<br>
                    <input type="text" name="treino" placeholder="ex: Supino, Agachamento, Corrida" />
                </label>
            </div>
            <div style="margin-top:8px;">
                <button type="submit">Criar</button>
            </div>
        </form>
        <script>
            const selectType = document.querySelector('select[name="type"]');
            const treinoCampo = document.getElementById('treinoCampo');
            function toggleTreino(){
                if(selectType.value === 'aluno'){treinoCampo.style.display='block';}else{treinoCampo.style.display='none';}
            }
            selectType.addEventListener('change', toggleTreino);
            toggleTreino();
        </script>
    <?php endif; ?>

    <h2 style="margin-top: 24px;">Usuários</h2>
    <?php if (empty($users)): ?>
        <p>Nenhum usuário cadastrado.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Tipo</th><th>Treino (Aluno)</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u->getId() ?></td>
                        <td><?= htmlspecialchars($u->getName()) ?></td>
                        <td><?= htmlspecialchars($u->getEmail()) ?></td>
                        <td><?= method_exists($u,'getType')?htmlspecialchars($u->getType()):'n/a' ?></td>
                        <td>
                            <?php if (method_exists($u,'getType') && $u->getType()==='aluno'): ?>
                                <?= htmlspecialchars(implode(', ', $u->getTreino())) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <a href="?action=edit&id=<?= $u->getId() ?>">Editar</a>
                            <a href="?action=delete&id=<?= $u->getId() ?>" onclick="return confirm('Remover usuário?')">Remover</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <footer style="margin-top:28px;">
        <small>Exemplo simples em PHP, JSON, MVC, princípios SOLID básicos.</small>
    </footer>
</body>
</html>