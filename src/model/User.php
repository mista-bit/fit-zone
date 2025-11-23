<?php
namespace App\Model;

/**
 * classe base abstrata User -> ela q vai ser a classe (pai) das outras classes criadas - q sao usuarios.
 */
abstract class User
{
    private int $id;
    private string $name;
    private string $email; 

    protected function __construct(int $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = trim($name);
        $this->email = trim($email);
    }

    public function getId(): int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }

    abstract public function getType(): string;

    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'type'  => $this->getType(),
        ];
    }
}

