<?php

namespace School\Application\Service;

use School\Domain\Repository\TeacherRepositoryInterface;
use School\Domain\Repository\DepartmentRepositoryInterface;

class AssignTeacherDepartmentService
{
    private TeacherRepositoryInterface $teacherRepository;
    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(
        TeacherRepositoryInterface $teacherRepository,
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->teacherRepository = $teacherRepository;
        $this->departmentRepository = $departmentRepository;
    }

    public function execute(int $teacherId, int $departmentId): void
    {
        // Buscar el profesor
        $teacher = $this->teacherRepository->findById($teacherId);
        if (!$teacher) {
            throw new \RuntimeException("Teacher with ID {$teacherId} not found");
        }

        // Buscar el departamento
        $department = $this->departmentRepository->findById($departmentId);
        if (!$department) {
            throw new \RuntimeException("Department with ID {$departmentId} not found");
        }

        // Asignar el profesor al departamento
        $teacher->assignToDepartment($departmentId);

        // Persistir el cambio
        $this->teacherRepository->save($teacher);
    }
}
