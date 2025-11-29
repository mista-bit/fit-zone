<?php
namespace App\Model;

/*
* Representa um exercício do catálogo
*/
class Exercicio
{
    private int $id;
    private string $nome;
    private ?string $descricao;
    private ?string $categoria;

    public function __construct(
        int $id,
        string $nome,
        ?string $descricao = null,
        ?string $categoria = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->categoria = $categoria;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'categoria' => $this->categoria,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int)($data['id'] ?? 0),
            (string)($data['nome'] ?? ''),
            $data['descricao'] ?? null,
            $data['categoria'] ?? null
        );
    }
}
