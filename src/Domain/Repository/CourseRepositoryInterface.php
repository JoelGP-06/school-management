<?php

namespace School\Domain\Repository;

use School\Domain\Entity\Course;

interface CourseRepositoryInterface
{
    public function save(Course $course): void;
    public function findById(int $id): ?Course;
    public function findByCode(string $code): ?Course;
    public function findAll(): array;
    public function delete(int $id): void;
}
