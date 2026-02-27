<?php

namespace School\Infrastructure\Controller;

use School\Application\Service\AssignTeacherDepartmentService;
use School\Application\Service\AssignStudentCourseService;
use School\Domain\Repository\UserRepositoryInterface;
use School\Domain\Repository\TeacherRepositoryInterface;
use School\Domain\Repository\StudentRepositoryInterface;
use School\Domain\Repository\DepartmentRepositoryInterface;
use School\Domain\Repository\CourseRepositoryInterface;

class AssignmentController
{
    private UserRepositoryInterface $userRepository;
    private TeacherRepositoryInterface $teacherRepository;
    private StudentRepositoryInterface $studentRepository;
    private DepartmentRepositoryInterface $departmentRepository;
    private CourseRepositoryInterface $courseRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        TeacherRepositoryInterface $teacherRepository,
        StudentRepositoryInterface $studentRepository,
        DepartmentRepositoryInterface $departmentRepository,
        CourseRepositoryInterface $courseRepository
    ) {
        $this->userRepository = $userRepository;
        $this->teacherRepository = $teacherRepository;
        $this->studentRepository = $studentRepository;
        $this->departmentRepository = $departmentRepository;
        $this->courseRepository = $courseRepository;
    }

    public function showAssignTeacherForm(): void
    {
        // obtiene todos los profesores y departamentos(subjects) para el dropdown de formularios
        $teachers = $this->teacherRepository->findAll();
        $departments = $this->departmentRepository->findAll();

        // render de la vista con acceso a teachers y departments
        require __DIR__ . '/../View/assign_teacher_form.php';
    }

    public function assignTeacher(): void
    {
        // Extrae datos del formulario
        // cast de int por seguridad
        $teacherId = (int)$_POST['teacher_id'];
        $departmentId = (int)$_POST['department_id']; 

        // crea una instancia del servicio de asignación de profesor a departamento, inyectando los repositorios necesarios
        $service = new AssignTeacherDepartmentService(
            $this->teacherRepository,
            $this->departmentRepository
        );

        // ejecuta el caso de uso y captura el resultado
        try {
            // ejecutamos y si va bien el profesor queda asignado al departamento
            $service->execute($teacherId, $departmentId);
            $message = "Teacher assigned to department successfully!";
        } catch (\Exception $e) {
            // si hay error, cachea y lanza la excepción
            $message = "Error: " . $e->getMessage();
        }
        // prepara los datos para la vista
        $teachers = $this->teacherRepository->findAll();
        $departments = $this->departmentRepository->findAll();

        // render de la vista con acceso a teachers, departments y message
        require __DIR__ . '/../View/assign_teacher_form.php';
    }

    public function showAssignStudentForm(): void
    {
        // obtiene todos los estudiantes y cursos para el dropdown de formularios
        $students = $this->studentRepository->findAll();
        $courses = $this->courseRepository->findAll();
        require __DIR__ . '/../View/assign_student_form.php';
    }

    public function assignStudent(): void
    {
        // Extrae datos del formulario
        $studentId = (int)$_POST['student_id'];
        $courseId = (int)$_POST['course_id'];

        // crea una instancia del servicio de asignación de estudiante a curso, inyectando los repositorios necesarios
        $service = new AssignStudentCourseService(
            $this->studentRepository,
            $this->courseRepository
        );

        // ejecuta el caso de uso y captura el resultado
        try {
            $service->execute($studentId, $courseId);
            $message = "Student assigned to course successfully!";
        } catch (\Exception $e) {
            // si hay error, cachea y lanza la excepción
            $message = "Error: " . $e->getMessage();
        }

        // prepara los datos para la vista
        $students = $this->studentRepository->findAll();
        $courses = $this->courseRepository->findAll();
        require __DIR__ . '/../View/assign_student_form.php';
    }
}
