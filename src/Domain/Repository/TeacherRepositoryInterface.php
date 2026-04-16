<?php

namespace School\Domain\Repository;

use School\Domain\Entity\Teacher;

interface TeacherRepositoryInterface
{
    public function save(Teacher $teacher): void;
    public function findById(int $id): ?Teacher;
    public function findByUserId(int $userId): ?Teacher;
    public function findAll(): array;
    public function delete(int $id): void;
}
