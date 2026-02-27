<?php

namespace School\Domain\Repository;

use School\Domain\Entity\User;
use School\Domain\ValueObject\Email;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(int $id): ?User;
    public function findByEmail(Email $email): ?User;
    public function findAll(): array;
}
