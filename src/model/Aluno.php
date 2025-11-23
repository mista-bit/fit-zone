<?php
namespace App\Model;

class Aluno extends User
{
    /** @var string[] lista de exercícios ou descricoes de treino e tals*/
    private array $treino;

    public function __construct(int $id, string $name, string $email, array $treino = [])
    {
        parent::__construct($id, $name, $email);
        $this->treino = $treino;
    }

    public function getType(): string
    {
        return 'aluno';
    }

    public function getTreino(): array
    {
        return $this->treino;
    }

    public function setTreino(array $treino): void
    {
        $this->treino = array_values($treino);
    }

    public function addExercicio(string $ex): void
    {
        $ex = trim($ex);
        if ($ex !== '') {
            $this->treino[] = $ex;
        }
    }

    public function toArray(): array
    {
        $base = parent::toArray();
        $base['treino'] = $this->treino;
        return $base;
    }

    public static function fromArray(array $data): Aluno
    {
        return new Aluno(
            (int)($data['id'] ?? 0),
            (string)($data['name'] ?? ''),
            (string)($data['email'] ?? ''),
            is_array($data['treino'] ?? null) ? $data['treino'] : []
        );
    }
}
