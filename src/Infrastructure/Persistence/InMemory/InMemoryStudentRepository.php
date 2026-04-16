<?php

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Student;
use School\Domain\Repository\StudentRepositoryInterface;

class InMemoryStudentRepository implements StudentRepositoryInterface
{
    private array $students = [];
    private int $nextId = 1;

    public function save(Student $student): void //guarda o actualiza el estudiante en el array, si ya existe con ese id lo actualiza, si no existía se añade nuevo
    {
        if ($student->getId() === null) {
            $student->setId($this->nextId++);
        }
        $this->students[$student->getId()] = $student;
    }

    public function findById(int $id): ?Student //busca estudiante por id y si no existe devuelve null
    {
        return $this->students[$id] ?? null;
    }

    public function findByUserId(int $userId): ?Student //busca estudiante por userId y si no existe devuelve null
    {
        foreach ($this->students as $student) {
            if ($student->getUserId() === $userId) {
                return $student;
            }
        }
        return null;
    }

    public function findByEnrollmentNumber(string $enrollmentNumber): ?Student //busca estudiante por enrollmentNumber y si no existe devuelve null
    {
        foreach ($this->students as $student) {
            if ($student->getEnrollmentNumber() === $enrollmentNumber) {
                return $student;
            }
        }
        return null;
    }

    public function findAll(): array //devuelve todos los estudiantes reindexados desde 0
    {
        return array_values($this->students);
    }

    public function delete(int $id): void
    {
        unset($this->students[$id]);
    }
}
