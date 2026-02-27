<?php

namespace School\Tests\Application\Service;

use PHPUnit\Framework\TestCase;
use School\Application\Service\AssignTeacherDepartmentService;
use School\Domain\Entity\User;
use School\Domain\Entity\Teacher;
use School\Domain\Entity\Department;
use School\Domain\ValueObject\Email;
use School\Infrastructure\Persistence\InMemory\InMemoryUserRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryTeacherRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryDepartmentRepository;

class AssignTeacherDepartmentServiceTest extends TestCase
{
    private InMemoryUserRepository $userRepository;
    private InMemoryTeacherRepository $teacherRepository;
    private InMemoryDepartmentRepository $departmentRepository;
    private AssignTeacherDepartmentService $service;

    protected function setUp(): void
    {
        $this->userRepository = new InMemoryUserRepository();
        $this->teacherRepository = new InMemoryTeacherRepository();
        $this->departmentRepository = new InMemoryDepartmentRepository();
        
        $this->service = new AssignTeacherDepartmentService(
            $this->teacherRepository,
            $this->departmentRepository
        );
    }

    public function testAssignTeacherToDepartmentSuccessfully(): void
    {
        // Arrange: Crear User
        $user = new User(null, "Dr. John Doe", new Email("john.doe@school.edu"));
        $this->userRepository->save($user);

        // Arrange: Crear Teacher
        $teacher = new Teacher(null, $user->getId(), "Mathematics");
        $this->teacherRepository->save($teacher);

        // Arrange: Crear Department
        $department = new Department(null, "Mathematics Department", "MATH");
        $this->departmentRepository->save($department);

        // Verificar que el profesor no tiene departamento asignado
        $this->assertNull($teacher->getDepartmentId());

        // Act: Ejecutar el servicio de asignación
        $this->service->execute($teacher->getId(), $department->getId());

        // Assert: Verificar que la asignación se realizó correctamente
        $updatedTeacher = $this->teacherRepository->findById($teacher->getId());
        $this->assertNotNull($updatedTeacher);
        $this->assertEquals($department->getId(), $updatedTeacher->getDepartmentId());
    }

    public function testAssignTeacherToDepartmentThrowsExceptionWhenTeacherNotFound(): void
    {
        // Arrange: Crear solo un departamento
        $department = new Department(null, "Computer Science Department", "CS");
        $this->departmentRepository->save($department);

        // Assert & Act: Verificar que lanza excepción cuando el profesor no existe
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Teacher with ID 999 not found");

        $this->service->execute(999, $department->getId());
    }

    public function testAssignTeacherToDepartmentThrowsExceptionWhenDepartmentNotFound(): void
    {
        // Arrange: Crear User y Teacher
        $user = new User(null, "Dr. Jane Smith", new Email("jane.smith@school.edu"));
        $this->userRepository->save($user);

        $teacher = new Teacher(null, $user->getId(), "Physics");
        $this->teacherRepository->save($teacher);

        // Assert & Act: Verificar que lanza excepción cuando el departamento no existe
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Department with ID 999 not found");

        $this->service->execute($teacher->getId(), 999);
    }

    public function testCompleteWorkflowWithMultipleAssignments(): void
    {
        // Arrange: Crear múltiples usuarios, profesores y departamentos
        $user1 = new User(null, "Dr. Alice Brown", new Email("alice.brown@school.edu"));
        $this->userRepository->save($user1);

        $user2 = new User(null, "Dr. Bob Wilson", new Email("bob.wilson@school.edu"));
        $this->userRepository->save($user2);

        $teacher1 = new Teacher(null, $user1->getId(), "Chemistry");
        $this->teacherRepository->save($teacher1);

        $teacher2 = new Teacher(null, $user2->getId(), "Biology");
        $this->teacherRepository->save($teacher2);

        $dept1 = new Department(null, "Chemistry Department", "CHEM");
        $this->departmentRepository->save($dept1);

        $dept2 = new Department(null, "Biology Department", "BIO");
        $this->departmentRepository->save($dept2);

        // Act: Asignar profesores a departamentos
        $this->service->execute($teacher1->getId(), $dept1->getId());
        $this->service->execute($teacher2->getId(), $dept2->getId());

        // Assert: Verificar asignaciones
        $updatedTeacher1 = $this->teacherRepository->findById($teacher1->getId());
        $updatedTeacher2 = $this->teacherRepository->findById($teacher2->getId());

        $this->assertEquals($dept1->getId(), $updatedTeacher1->getDepartmentId());
        $this->assertEquals($dept2->getId(), $updatedTeacher2->getDepartmentId());
    }
}
