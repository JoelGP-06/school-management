<?php

namespace School\Domain\Repository;

use School\Domain\Entity\Student;

interface StudentRepositoryInterface
{
    public function save(Student $student): void;
    public function findById(int $id): ?Student;
    public function findByUserId(int $userId): ?Student;
    public function findByEnrollmentNumber(string $enrollmentNumber): ?Student;
    public function findAll(): array;
    public function delete(int $id): void;
}
