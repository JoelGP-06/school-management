<?php

namespace School\Domain\Repository;

use School\Domain\Entity\Department;

interface DepartmentRepositoryInterface
{
    public function save(Department $department): void;
    public function findById(int $id): ?Department;
    public function findByCode(string $code): ?Department;
    public function findAll(): array;
    public function delete(int $id): void;
}
