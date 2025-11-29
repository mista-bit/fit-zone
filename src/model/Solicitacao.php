<?php
namespace App\Model;

/**
 * representa uma solicitação de aluno para personal trainer*/
class Solicitacao
{
    private int $id;
    private int $alunoId;
    private int $personalId;
    private string $status; // pending, accepted, rejected
    private ?string $mensagem;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(
        int $id,
        int $alunoId,
        int $personalId,
        string $status = 'pending',
        ?string $mensagem = null,
        string $createdAt = '',
        string $updatedAt = ''
    ) {
        $this->id = $id;
        $this->alunoId = $alunoId;
        $this->personalId = $personalId;
        $this->setStatus($status);
        $this->mensagem = $mensagem;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        if (!in_array($status, ['pending', 'accepted', 'rejected'])) {
            throw new \InvalidArgumentException("Status inválido: {$status}");
        }
        $this->status = $status;
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function getMensagem(): ?string
    {
        return $this->mensagem;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function aceitar(): void
    {
        $this->setStatus('accepted');
    }

    public function rejeitar(): void
    {
        $this->setStatus('rejected');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'alunoId' => $this->alunoId,
            'personalId' => $this->personalId,
            'status' => $this->status,
            'mensagem' => $this->mensagem,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int)($data['id'] ?? 0),
            (int)($data['alunoId'] ?? $data['aluno_id'] ?? 0),
            (int)($data['personalId'] ?? $data['personal_id'] ?? 0),
            (string)($data['status'] ?? 'pending'),
            $data['mensagem'] ?? null,
            (string)($data['createdAt'] ?? $data['created_at'] ?? ''),
            (string)($data['updatedAt'] ?? $data['updated_at'] ?? '')
        );
    }
}
