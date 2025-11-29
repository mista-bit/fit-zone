<?php
namespace App\Model;

/**
 * Representa um exercício dentro de um treino
 */
class TreinoExercicio
{
    private int $id;
    private int $treinoId;
    private int $exercicioId;
    private string $exercicioNome;
    private ?string $categoria;
    private int $ordem;
    private int $series;
    private int $repeticoes;
    private ?string $carga;
    private ?string $descanso;
    private ?string $observacoes;

    public function __construct(
        int $id,
        int $treinoId,
        int $exercicioId,
        string $exercicioNome,
        ?string $categoria = null,
        int $ordem = 0,
        int $series = 3,
        int $repeticoes = 12,
        ?string $carga = null,
        ?string $descanso = null,
        ?string $observacoes = null
    ) {
        $this->id = $id;
        $this->treinoId = $treinoId;
        $this->exercicioId = $exercicioId;
        $this->exercicioNome = $exercicioNome;
        $this->categoria = $categoria;
        $this->ordem = $ordem;
        $this->series = $series;
        $this->repeticoes = $repeticoes;
        $this->carga = $carga;
        $this->descanso = $descanso;
        $this->observacoes = $observacoes;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTreinoId(): int
    {
        return $this->treinoId;
    }

    public function getExercicioId(): int
    {
        return $this->exercicioId;
    }

    public function getExercicioNome(): string
    {
        return $this->exercicioNome;
    }

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    public function getOrdem(): int
    {
        return $this->ordem;
    }

    public function setOrdem(int $ordem): void
    {
        $this->ordem = $ordem;
    }

    public function getSeries(): int
    {
        return $this->series;
    }

    public function setSeries(int $series): void
    {
        $this->series = $series;
    }

    public function getRepeticoes(): int
    {
        return $this->repeticoes;
    }

    public function setRepeticoes(int $repeticoes): void
    {
        $this->repeticoes = $repeticoes;
    }

    public function getCarga(): ?string
    {
        return $this->carga;
    }

    public function setCarga(?string $carga): void
    {
        $this->carga = $carga;
    }

    public function getDescanso(): ?string
    {
        return $this->descanso;
    }

    public function setDescanso(?string $descanso): void
    {
        $this->descanso = $descanso;
    }

    public function getObservacoes(): ?string
    {
        return $this->observacoes;
    }

    public function setObservacoes(?string $observacoes): void
    {
        $this->observacoes = $observacoes;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'treinoId' => $this->treinoId,
            'exercicioId' => $this->exercicioId,
            'exercicioNome' => $this->exercicioNome,
            'categoria' => $this->categoria,
            'ordem' => $this->ordem,
            'series' => $this->series,
            'repeticoes' => $this->repeticoes,
            'carga' => $this->carga,
            'descanso' => $this->descanso,
            'observacoes' => $this->observacoes,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int)($data['id'] ?? 0),
            (int)($data['treinoId'] ?? $data['treino_id'] ?? 0),
            (int)($data['exercicioId'] ?? $data['exercicio_id'] ?? 0),
            (string)($data['exercicioNome'] ?? $data['exercicio_nome'] ?? ''),
            $data['categoria'] ?? null,
            (int)($data['ordem'] ?? 0),
            (int)($data['series'] ?? 3),
            (int)($data['repeticoes'] ?? 12),
            $data['carga'] ?? null,
            $data['descanso'] ?? null,
            $data['observacoes'] ?? null
        );
    }
}
