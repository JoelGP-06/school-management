<?php

namespace School\Infrastructure\Persistence\InMemory;

use School\Domain\Entity\Course;
use School\Domain\Repository\CourseRepositoryInterface;

class InMemoryCourseRepository implements CourseRepositoryInterface
{
    private array $courses = [];
    private int $nextId = 1;

    public function save(Course $course): void //guarda o actualiza el curso en el array, si ya existe con ese id lo actualiza, si no existía se añade nuevo
    {
        if ($course->getId() === null) {
            $course->setId($this->nextId++);
        }
        $this->courses[$course->getId()] = $course;
    }

    public function findById(int $id): ?Course //busca curso por id y si no existe devuelve null
    {
        return $this->courses[$id] ?? null;
    }

    public function findByCode(string $code): ?Course //recorre el array en busca un curso con el código dado y devuelve el curso si lo encuentra, o null si no lo encuentra
    {
        foreach ($this->courses as $course) {
            if ($course->getCode() === $code) {
                return $course;
            }
        }
        return null;
    }

    public function findAll(): array //devuelve todos los cursos reindexados desde 0
    {
        return array_values($this->courses);
    }

    public function delete(int $id): void
    {
        unset($this->courses[$id]);
    }
}
