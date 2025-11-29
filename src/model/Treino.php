<?php
namespace App\Model;

/**
 * Representa um treino completo criado por um personal para um aluno
 * Segue SRP - apenas dados do treino
 */
class Treino
{
    private int $id;
    private int $alunoId;
    private int $personalId;
    private string $nome;
    private ?string $descricao;
    private ?string $diaSemana;
    private bool $ativo;
    private array $exercicios; // Array de TreinoExercicio
    private string $createdAt;
    private string $updatedAt;

    public function __construct(
        int $id,
        int $alunoId,
        int $personalId,
        string $nome,
        ?string $descricao = null,
        ?string $diaSemana = null,
        bool $ativo = true,
        array $exercicios = [],
        string $createdAt = '',
        string $updatedAt = ''
    ) {
        $this->id = $id;
        $this->alunoId = $alunoId;
        $this->personalId = $personalId;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->diaSemana = $diaSemana;
        $this->ativo = $ativo;
        $this->exercicios = $exercicios;
        $this->createdAt = $createdAt ?: date('Y-m-d H:i:s');
        $this->updatedAt = $updatedAt ?: date('Y-m-d H:i:s');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAlunoId(): int
    {
        return $this->alunoId;
    }

    public function getPersonalId(): int
    {
        return $this->personalId;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function getDiaSemana(): ?string
    {
        return $this->diaSemana;
    }

    public function setDiaSemana(?string $diaSemana): void
    {
        $this->diaSemana = $diaSemana;
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function ativar(): void
    {
        $this->ativo = true;
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function arquivar(): void
    {
        $this->ativo = false;
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function getExercicios(): array
    {
        return $this->exercicios;
    }

    public function setExercicios(array $exercicios): void
    {
        $this->exercicios = $exercicios;
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function addExercicio(TreinoExercicio $exercicio): void
    {
        $this->exercicios[] = $exercicio;
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'alunoId' => $this->alunoId,
            'personalId' => $this->personalId,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'dia_semana' => $this->diaSemana,
            'ativo' => $this->ativo,
            'exercicios' => array_map(fn($e) => $e->toArray(), $this->exercicios),
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public static function fromArray(array $data): self
    {
        $exercicios = [];
        if (isset($data['exercicios']) && is_array($data['exercicios'])) {
            $exercicios = array_map(
                fn($e) => TreinoExercicio::fromArray($e),
                $data['exercicios']
            );
        }

        return new self(
            (int)($data['id'] ?? 0),
            (int)($data['alunoId'] ?? $data['aluno_id'] ?? 0),
            (int)($data['personalId'] ?? $data['personal_id'] ?? 0),
            (string)($data['nome'] ?? ''),
            $data['descricao'] ?? null,
            $data['dia_semana'] ?? $data['diaSemana'] ?? null,
            (bool)($data['ativo'] ?? true),
            $exercicios,
            (string)($data['createdAt'] ?? $data['created_at'] ?? ''),
            (string)($data['updatedAt'] ?? $data['updated_at'] ?? '')
        );
    }
}
