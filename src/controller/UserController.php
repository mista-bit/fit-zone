<?php
namespace App\Controller;

use App\Repository\UserRepositoryInterface;
use App\Model\Aluno;
use App\Model\Personal;

class UserController
{
    /**
     * Repositório responsável por salvar, buscar, atualizar e remover usuários.
     */
    private UserRepositoryInterface $repo;

    /**
     * Injeta o repositório (permite trocar JSON por DB futuramente sem mudar o controller)
     */
    public function __construct(UserRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Retorna todos os usuários cadastrados.
     */
    public function index(): array
    {
        return $this->repo->all();
    }

    /**
     * Cria um novo usuário (Aluno ou Personal).
     * Valida dados essenciais e envia o payload para o repositório.
     */
    public function store(array $data)
    {
        // Campos obrigatórios
        $name  = trim($data['name']  ?? '');
        $email = trim($data['email'] ?? '');
        $type  = $data['type'] ?? 'aluno';

        if ($name === '' || $email === '') {
            throw new \InvalidArgumentException('Nome e e-mail são obrigatórios.');
        }

        // Base do payload de criação
        $payload = [
            'name'  => $name,
            'email' => $email,
            'type'  => $type,
        ];

        // Se for aluno, pode receber treino
        if ($type === 'aluno' && isset($data['treino'])) {
            $payload['treino'] = $data['treino'];
        }

        // Delegamos ao repositório a criação do usuário
        return $this->repo->create($payload);
    }

    /**
     * Mostra os dados de um único usuário pelo ID.
     */
    public function show(int $id)
    {
        return $this->repo->find($id);
    }

    /**
     * Atualiza qualquer campo permitido do usuário.
     * A lógica de validação específica está no repositório.
     */
    public function update(int $id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    /**
     * Atualiza somente o treino de um aluno.
     * Se o ID não pertencer a um Aluno, lança exceção.
     */
    public function atualizarTreino(int $alunoId, array $treino): ?Aluno
    {
        // Verifica se o usuário existe e é aluno
        $user = $this->repo->find($alunoId);
        if (!$user instanceof Aluno) {
            throw new \RuntimeException('Usuário não é Aluno ou não existe.');
        }

        // Monta payload contendo apenas o treino
        $data = [
            'treino' => $treino,
        ];

        // Repositório cuida da atualização
        $updated = $this->repo->update($alunoId, $data);

        // Garante retorno somente se for de fato um aluno
        return $updated instanceof Aluno ? $updated : null;
    }

    /**
     * Remove um usuário pelo ID.
     * Retorna true se o usuário foi apagado.
     */
    public function destroy(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
