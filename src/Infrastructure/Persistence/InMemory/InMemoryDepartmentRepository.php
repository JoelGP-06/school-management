<?php

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Department;
use School\Domain\Repository\DepartmentRepositoryInterface;

class InMemoryDepartmentRepository implements DepartmentRepositoryInterface
{
    private array $departments = [];
    private int $nextId = 1;

    public function save(Department $department): void //guarda o actualiza el departamento en el array, si ya existe con ese id lo actualiza, si no existía se añade nuevo
    {
        if ($department->getId() === null) {
            $department->setId($this->nextId++);
        }
        $this->departments[$department->getId()] = $department;
    }

    public function findById(int $id): ?Department //busca departamento por id y si no existe devuelve null
    {
        return $this->departments[$id] ?? null;
    }

    public function findByCode(string $code): ?Department //recorre el array en busca un departamento con el código dado y devuelve el departamento si lo encuentra, o null si no lo encuentra
    {
        foreach ($this->departments as $department) {
            if ($department->getCode() === $code) {
                return $department;
            }
        }
        return null;
    }

    public function findAll(): array //devuelve todos los departamentos reindexados desde 0
    {
        return array_values($this->departments);
    }
}
