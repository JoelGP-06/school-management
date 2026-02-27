<?php

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\User;
use School\Domain\Repository\UserRepositoryInterface;
use School\Domain\ValueObject\Email;

class InMemoryUserRepository implements UserRepositoryInterface
{
    private array $users = [];
    private int $nextId = 1;

    public function save(User $user): void
    {
        if ($user->getId() === null) {
            $user->setId($this->nextId++);
        }
        $this->users[$user->getId()] = $user;
    }

    public function findById(int $id): ?User
    {
        return $this->users[$id] ?? null;
    }

    public function findByEmail(Email $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->getEmail()->getValue() === $email->getValue()) {
                return $user;
            }
        }
        return null;
    }

    public function findAll(): array
    {
        return array_values($this->users);
    }
}
