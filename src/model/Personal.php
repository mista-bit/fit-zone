<?php
namespace App\Model;

class Personal extends User
{
    public function __construct(int $id, string $name, string $email)
    {
        parent::__construct($id, $name, $email);
    }

    public function getType(): string
    {
        return 'personal';
    }

    /**
     * atualiza o treino completo de um aluno.
     */
    public function atualizarTreino(Aluno $aluno, array $novoTreino): void
    {
        $aluno->setTreino($novoTreino);
    }

    /**
     * adiciona um exercício ao treino do aluno.
     */
    public function adicionarExercicio(Aluno $aluno, string $exercicio): void
    {
        $aluno->addExercicio($exercicio);
    }
}
