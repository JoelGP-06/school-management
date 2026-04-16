<?php

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Teacher;
use School\Domain\Repository\TeacherRepositoryInterface;

class InMemoryTeacherRepository implements TeacherRepositoryInterface
{
    private array $teachers = [];
    private int $nextId = 1;

    public function save(Teacher $teacher): void //guarda o actualiza el profesor en el array, si ya existe con ese id lo actualiza, si no existía se añade nuevo
    {
        if ($teacher->getId() === null) {
            $teacher->setId($this->nextId++);
        }
        $this->teachers[$teacher->getId()] = $teacher;
    }

    public function findById(int $id): ?Teacher //busca profesor por id y si no existe devuelve null
    {
        return $this->teachers[$id] ?? null;
    }

    public function findByUserId(int $userId): ?Teacher //busca profesor por userId y si no existe devuelve null
    {
        foreach ($this->teachers as $teacher) {
            if ($teacher->getUserId() === $userId) {
                return $teacher;
            }
        }
        return null;
    }

    public function findAll(): array //devuelve todos los profesores reindexados desde 0
    {
        return array_values($this->teachers);
    }

    public function delete(int $id): void
    {
        unset($this->teachers[$id]);
    }
}
