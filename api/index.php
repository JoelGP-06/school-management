<?php

/**
 * API REST - School Management
 * Entry point for all API requests.
 *
 * Base URL: /api/
 *
 * Endpoints:
 *   GET    /api/students
 *   GET    /api/students/{id}
 *   POST   /api/students
 *   PUT    /api/students/{id}
 *   DELETE /api/students/{id}
 *
 *   GET    /api/teachers
 *   GET    /api/teachers/{id}
 *   POST   /api/teachers
 *   PUT    /api/teachers/{id}
 *   DELETE /api/teachers/{id}
 *
 *   GET    /api/courses
 *   GET    /api/courses/{id}
 *   POST   /api/courses
 *   PUT    /api/courses/{id}
 *   DELETE /api/courses/{id}
 *
 *   GET    /api/departments
 *   GET    /api/departments/{id}
 *   POST   /api/departments
 *   PUT    /api/departments/{id}
 *   DELETE /api/departments/{id}
 */

require_once __DIR__ . '/../vendor/autoload.php';

use School\Infrastructure\Http\Request;
use School\Infrastructure\Http\ResponseJson;
use School\Infrastructure\Http\Routing\ApiRouter;
use School\Infrastructure\Http\Controllers\StudentsController;
use School\Infrastructure\Http\Controllers\TeachersController;
use School\Infrastructure\Http\Controllers\CoursesController;
use School\Infrastructure\Http\Controllers\DepartmentsController;

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

// ─── Repositories ────────────────────────────────────────────────────────────

$userRepository       = new InMemoryUserRepository();
$teacherRepository    = new InMemoryTeacherRepository();
$studentRepository    = new InMemoryStudentRepository();
$departmentRepository = new InMemoryDepartmentRepository();
$courseRepository     = new InMemoryCourseRepository();

// ─── Seed data ───────────────────────────────────────────────────────────────

// Users
$user1 = new User(null, "John Smith",    new Email("john.smith@school.edu"));
$user2 = new User(null, "Mary Johnson",  new Email("mary.johnson@school.edu"));
$user3 = new User(null, "Robert Brown",  new Email("robert.brown@school.edu"));
$user4 = new User(null, "Alice Wilson",  new Email("alice.wilson@school.edu"));
$user5 = new User(null, "Carlos García", new Email("carlos.garcia@school.edu"));
$user6 = new User(null, "Laia Puig",     new Email("laia.puig@school.edu"));

foreach ([$user1, $user2, $user3, $user4, $user5, $user6] as $u) {
    $userRepository->save($u);
}

// Teachers
$teacher1 = new Teacher(null, $user1->getId(), "Mathematics");
$teacher2 = new Teacher(null, $user2->getId(), "Computer Science");
foreach ([$teacher1, $teacher2] as $t) {
    $teacherRepository->save($t);
}

// Students
$student1 = new Student(null, $user3->getId(), "STU-2024-001");
$student2 = new Student(null, $user4->getId(), "STU-2024-002");
$student3 = new Student(null, $user5->getId(), "STU-2024-003");
$student4 = new Student(null, $user6->getId(), "STU-2024-004");
foreach ([$student1, $student2, $student3, $student4] as $s) {
    $studentRepository->save($s);
}

// Departments
$dept1 = new Department(null, "Mathematics Department",      "MATH");
$dept2 = new Department(null, "Computer Science Department", "CS");
$dept3 = new Department(null, "Physics Department",          "PHYS");
foreach ([$dept1, $dept2, $dept3] as $d) {
    $departmentRepository->save($d);
}

// Courses
$course1 = new Course(null, "Calculus I",      "MATH101", 4);
$course2 = new Course(null, "Data Structures", "CS201",   4);
$course3 = new Course(null, "Algorithms",      "CS301",   3);
$course4 = new Course(null, "Linear Algebra",  "MATH201", 3);
foreach ([$course1, $course2, $course3, $course4] as $c) {
    $courseRepository->save($c);
}

// Pre-assign some relations
$teacher1->assignToDepartment($dept1->getId());
$teacherRepository->save($teacher1);

$teacher2->assignToDepartment($dept2->getId());
$teacherRepository->save($teacher2);

$student1->assignToCourse($course1->getId());
$studentRepository->save($student1);

$student2->assignToCourse($course2->getId());
$studentRepository->save($student2);

// ─── Controllers ─────────────────────────────────────────────────────────────

$studentsController    = new StudentsController($studentRepository,    $userRepository, $courseRepository);
$teachersController    = new TeachersController($teacherRepository,    $userRepository, $departmentRepository);
$coursesController     = new CoursesController($courseRepository);
$departmentsController = new DepartmentsController($departmentRepository);

// ─── Router ──────────────────────────────────────────────────────────────────

$router = new ApiRouter();

// Students
$router->get('/api/students',          [$studentsController, 'index']);
$router->get('/api/students/{id}',     [$studentsController, 'show']);
$router->post('/api/students',         [$studentsController, 'store']);
$router->put('/api/students/{id}',     [$studentsController, 'update']);
$router->delete('/api/students/{id}',  [$studentsController, 'delete']);

// Teachers
$router->get('/api/teachers',          [$teachersController, 'index']);
$router->get('/api/teachers/{id}',     [$teachersController, 'show']);
$router->post('/api/teachers',         [$teachersController, 'store']);
$router->put('/api/teachers/{id}',     [$teachersController, 'update']);
$router->delete('/api/teachers/{id}',  [$teachersController, 'delete']);

// Courses
$router->get('/api/courses',           [$coursesController, 'index']);
$router->get('/api/courses/{id}',      [$coursesController, 'show']);
$router->post('/api/courses',          [$coursesController, 'store']);
$router->put('/api/courses/{id}',      [$coursesController, 'update']);
$router->delete('/api/courses/{id}',   [$coursesController, 'delete']);

// Departments
$router->get('/api/departments',        [$departmentsController, 'index']);
$router->get('/api/departments/{id}',   [$departmentsController, 'show']);
$router->post('/api/departments',       [$departmentsController, 'store']);
$router->put('/api/departments/{id}',   [$departmentsController, 'update']);
$router->delete('/api/departments/{id}',[$departmentsController, 'delete']);

// ─── Dispatch ─────────────────────────────────────────────────────────────────

$request = new Request();
$router->dispatch($request);
