<?php

require_once __DIR__ . '/../vendor/autoload.php';

use School\Infrastructure\Routing\Router;
use School\Infrastructure\Controller\HomeController;
use School\Infrastructure\Controller\AssignmentController;
use School\Infrastructure\Persistence\InMemory\InMemoryUserRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryTeacherRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryStudentRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryDepartmentRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryCourseRepository;
use School\Domain\Entity\User;
use School\Domain\Entity\Teacher;
use School\Domain\Entity\Student;
use School\Domain\Entity\Department;
use School\Domain\Entity\Course;
use School\Domain\ValueObject\Email;

// Inicializar repositorios
$userRepository = new InMemoryUserRepository();
$teacherRepository = new InMemoryTeacherRepository();
$studentRepository = new InMemoryStudentRepository();
$departmentRepository = new InMemoryDepartmentRepository();
$courseRepository = new InMemoryCourseRepository();

// Crear datos de ejemplo para demostración
// Usuarios
$user1 = new User(null, "John Smith", new Email("john.smith@school.edu"));
$userRepository->save($user1);

$user2 = new User(null, "Mary Johnson", new Email("mary.johnson@school.edu"));
$userRepository->save($user2);

$user3 = new User(null, "Robert Brown", new Email("robert.brown@school.edu"));
$userRepository->save($user3);

$user4 = new User(null, "Alice Wilson", new Email("alice.wilson@school.edu"));
$userRepository->save($user4);

// Profesores
$teacher1 = new Teacher(null, $user1->getId(), "Mathematics");
$teacherRepository->save($teacher1);

$teacher2 = new Teacher(null, $user2->getId(), "Computer Science");
$teacherRepository->save($teacher2);

// Estudiantes
$student1 = new Student(null, $user3->getId(), "STU-2024-001");
$studentRepository->save($student1);

$student2 = new Student(null, $user4->getId(), "STU-2024-002");
$studentRepository->save($student2);

// Departamentos
$dept1 = new Department(null, "Mathematics Department", "MATH");
$departmentRepository->save($dept1);

$dept2 = new Department(null, "Computer Science Department", "CS");
$departmentRepository->save($dept2);

$dept3 = new Department(null, "Physics Department", "PHYS");
$departmentRepository->save($dept3);

// Cursos
$course1 = new Course(null, "Calculus I", "MATH101", 4);
$courseRepository->save($course1);

$course2 = new Course(null, "Data Structures", "CS201", 4);
$courseRepository->save($course2);

$course3 = new Course(null, "Algorithms", "CS301", 3);
$courseRepository->save($course3);

// Inicializar controladores
$homeController = new HomeController();
$assignmentController = new AssignmentController(
    $userRepository,
    $teacherRepository,
    $studentRepository,
    $departmentRepository,
    $courseRepository
);

// Configurar rutas
$router = new Router();

// Rutas principales
$router->get('/student', function() use ($homeController) {
    $homeController->student();
});

$router->get('/teacher', function() use ($homeController) {
    $homeController->teacher();
});

// Rutas de asignación de profesor
$router->get('/assign-teacher', function() use ($assignmentController) {
    $assignmentController->showAssignTeacherForm();
});

$router->post('/assign-teacher', function() use ($assignmentController) {
    $assignmentController->assignTeacher();
});

// Rutas de asignación de estudiante
$router->get('/assign-student', function() use ($assignmentController) {
    $assignmentController->showAssignStudentForm();
});

$router->post('/assign-student', function() use ($assignmentController) {
    $assignmentController->assignStudent();
});

// Ruta por defecto
$router->get('/', function() use ($homeController) {
    $homeController->student();
});

// Despachar la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->dispatch($method, $uri);
