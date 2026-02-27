<?php

namespace School\Tests\Application\Service;

use PHPUnit\Framework\TestCase;
use School\Application\Service\AssignStudentCourseService;
use School\Domain\Entity\User;
use School\Domain\Entity\Student;
use School\Domain\Entity\Course;
use School\Domain\ValueObject\Email;
use School\Infrastructure\Persistence\InMemory\InMemoryUserRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryStudentRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryCourseRepository;

class AssignStudentCourseServiceTest extends TestCase
{
    private InMemoryUserRepository $userRepository;
    private InMemoryStudentRepository $studentRepository;
    private InMemoryCourseRepository $courseRepository;
    private AssignStudentCourseService $service;

    protected function setUp(): void
    {
        $this->userRepository = new InMemoryUserRepository();
        $this->studentRepository = new InMemoryStudentRepository();
        $this->courseRepository = new InMemoryCourseRepository();
        
        $this->service = new AssignStudentCourseService(
            $this->studentRepository,
            $this->courseRepository
        );
    }

    public function testAssignStudentToCourseSuccessfully(): void
    {
        // Arrange: Crear User
        $user = new User(null, "Alice Johnson", new Email("alice.johnson@school.edu"));
        $this->userRepository->save($user);

        // Arrange: Crear Student
        $student = new Student(null, $user->getId(), "STU-2024-001");
        $this->studentRepository->save($student);

        // Arrange: Crear Course
        $course = new Course(null, "Introduction to Programming", "CS101", 4);
        $this->courseRepository->save($course);

        // Verificar que el estudiante no tiene curso asignado
        $this->assertNull($student->getCourseId());

        // Act: Ejecutar el servicio de asignación
        $this->service->execute($student->getId(), $course->getId());

        // Assert: Verificar que la asignación se realizó correctamente
        $updatedStudent = $this->studentRepository->findById($student->getId());
        $this->assertNotNull($updatedStudent);
        $this->assertEquals($course->getId(), $updatedStudent->getCourseId());
    }

    public function testAssignStudentToCourseThrowsExceptionWhenStudentNotFound(): void
    {
        // Arrange: Crear solo un curso
        $course = new Course(null, "Data Structures", "CS201", 4);
        $this->courseRepository->save($course);

        // Assert & Act: Verificar que lanza excepción cuando el estudiante no existe
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Student with ID 999 not found");

        $this->service->execute(999, $course->getId());
    }

    public function testAssignStudentToCourseThrowsExceptionWhenCourseNotFound(): void
    {
        // 1: Create User and Student
        $user = new User(null, "Bob Martinez", new Email("bob.martinez@school.edu"));
        $this->userRepository->save($user);

        $student = new Student(null, $user->getId(), "STU-2024-002");
        $this->studentRepository->save($student);

        // Verificar que lanza excepción cuando el curso no existe
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Course with ID 999 not found");

        $this->service->execute($student->getId(), 999);
    }

    public function testCompleteWorkflowWithMultipleAssignments(): void
    {
        // Arrange: Crear múltiples usuarios, estudiantes y cursos
        $user1 = new User(null, "Carlos Garcia", new Email("carlos.garcia@school.edu"));
        $this->userRepository->save($user1);

        $user2 = new User(null, "Diana Lee", new Email("diana.lee@school.edu"));
        $this->userRepository->save($user2);

        $student1 = new Student(null, $user1->getId(), "STU-2024-101");
        $this->studentRepository->save($student1);

        $student2 = new Student(null, $user2->getId(), "STU-2024-102");
        $this->studentRepository->save($student2);

        $course1 = new Course(null, "Calculus I", "MATH101", 4);
        $this->courseRepository->save($course1);

        $course2 = new Course(null, "Physics I", "PHYS101", 4);
        $this->courseRepository->save($course2);

        // Act: Asignar estudiantes a cursos
        $this->service->execute($student1->getId(), $course1->getId());
        $this->service->execute($student2->getId(), $course2->getId());

        // Assert: Verificar asignaciones
        $updatedStudent1 = $this->studentRepository->findById($student1->getId());
        $updatedStudent2 = $this->studentRepository->findById($student2->getId());

        $this->assertEquals($course1->getId(), $updatedStudent1->getCourseId());
        $this->assertEquals($course2->getId(), $updatedStudent2->getCourseId());
    }

    public function testReassignStudentToDifferentCourse(): void
    {
        // Arrange: Crear User, Student y Courses
        $user = new User(null, "Eva Rodriguez", new Email("eva.rodriguez@school.edu"));
        $this->userRepository->save($user);

        $student = new Student(null, $user->getId(), "STU-2024-201");
        $this->studentRepository->save($student);

        $course1 = new Course(null, "Chemistry I", "CHEM101", 4);
        $this->courseRepository->save($course1);

        $course2 = new Course(null, "Chemistry II", "CHEM201", 4);
        $this->courseRepository->save($course2);

        // Act: Asignar a primer curso
        $this->service->execute($student->getId(), $course1->getId());
        $firstAssignment = $this->studentRepository->findById($student->getId());
        $this->assertEquals($course1->getId(), $firstAssignment->getCourseId());

        // Act: Reasignar a segundo curso
        $this->service->execute($student->getId(), $course2->getId());
        $secondAssignment = $this->studentRepository->findById($student->getId());

        // Assert: Verificar que el estudiante ahora está en el segundo curso
        $this->assertEquals($course2->getId(), $secondAssignment->getCourseId());
    }
}
