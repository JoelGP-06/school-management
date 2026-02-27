<?php

namespace School\Application\Service;

use School\Domain\Repository\StudentRepositoryInterface;
use School\Domain\Repository\CourseRepositoryInterface;

class AssignStudentCourseService
{
    private StudentRepositoryInterface $studentRepository;
    private CourseRepositoryInterface $courseRepository;

    public function __construct(
        StudentRepositoryInterface $studentRepository,
        CourseRepositoryInterface $courseRepository
    ) {
        $this->studentRepository = $studentRepository;
        $this->courseRepository = $courseRepository;
    }

    public function execute(int $studentId, int $courseId): void
    {
        // Buscar el estudiante
        $student = $this->studentRepository->findById($studentId);
        if (!$student) {
            throw new \RuntimeException("Student with ID {$studentId} not found");
        }

        // Buscar el curso
        $course = $this->courseRepository->findById($courseId);
        if (!$course) {
            throw new \RuntimeException("Course with ID {$courseId} not found");
        }

        // Asignar el estudiante al curso
        $student->assignToCourse($courseId);

        // Persistir el cambio
        $this->studentRepository->save($student);
    }
}
