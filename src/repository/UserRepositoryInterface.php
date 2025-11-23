<?php
namespace App\Repository;

use App\Model\User;

interface UserRepositoryInterface
{
    /**
     * @return User[]
     */
    public function all(): array;

    public function find(int $id): ?User;

    public function create(array $data): User;

    public function update(int $id, array $data): ?User;

    public function delete(int $id): bool;
}