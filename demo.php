<?php

require __DIR__ . '/vendor/autoload.php';

use School\Domain\Entity\User;
use School\Domain\Entity\Teacher;
use School\Domain\Entity\Student;
use School\Domain\Entity\Department;
use School\Domain\Entity\Course;
use School\Domain\ValueObject\Email;
use School\Infrastructure\Persistence\InMemory\InMemoryUserRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryTeacherRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryStudentRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryDepartmentRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryCourseRepository;
use School\Application\Service\AssignTeacherDepartmentService;
use School\Application\Service\AssignStudentCourseService;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║      SCHOOL MANAGEMENT SYSTEM - DEMOSTRACIÓN DDD          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Inicializar repositorios
$userRepo = new InMemoryUserRepository();
$teacherRepo = new InMemoryTeacherRepository();
$studentRepo = new InMemoryStudentRepository();
$deptRepo = new InMemoryDepartmentRepository();
$courseRepo = new InMemoryCourseRepository();

// ============================================================
// CASO DE USO 1: Asignar Profesor a Departamento
// ============================================================
echo "┌────────────────────────────────────────────────────────────┐\n";
echo "│  CASO DE USO 1: Asignación de Profesor a Departamento     │\n";
echo "└────────────────────────────────────────────────────────────┘\n\n";

echo "Paso 1: Crear User\n";
$user1 = new User(null, "Dr. Emily White", new Email("emily.white@school.edu"));
$userRepo->save($user1);
echo "  ✓ User creado: {$user1->getName()} (ID: {$user1->getId()})\n\n";

echo "Paso 2: Crear Teacher\n";
$teacher1 = new Teacher(null, $user1->getId(), "Mathematics");
$teacherRepo->save($teacher1);
echo "  ✓ Teacher creado: Especialidad en {$teacher1->getSpecialty()} (ID: {$teacher1->getId()})\n";
echo "  • Department ID inicial: " . ($teacher1->getDepartmentId() ?? 'NULL') . "\n\n";

echo "Paso 3: Crear Department\n";
$dept1 = new Department(null, "Mathematics Department", "MATH");
$deptRepo->save($dept1);
echo "  ✓ Department creado: {$dept1->getName()} [{$dept1->getCode()}] (ID: {$dept1->getId()})\n\n";

echo "Paso 4: Ejecutar AssignTeacherDepartmentService\n";
$teacherService = new AssignTeacherDepartmentService($teacherRepo, $deptRepo);
$teacherService->execute($teacher1->getId(), $dept1->getId());
$updatedTeacher = $teacherRepo->findById($teacher1->getId());
echo "  ✓ Servicio ejecutado correctamente\n";
echo "  • Department ID después de asignación: {$updatedTeacher->getDepartmentId()}\n";
echo "\n";
echo "  ✅ RESULTADO: Profesor asignado exitosamente al departamento\n";
echo "\n";

// ============================================================
// CASO DE USO 2: Asignar Estudiante a Curso
// ============================================================
echo "┌────────────────────────────────────────────────────────────┐\n";
echo "│  CASO DE USO 2: Asignación de Estudiante a Curso          │\n";
echo "└────────────────────────────────────────────────────────────┘\n\n";

echo "Paso 1: Crear User\n";
$user2 = new User(null, "James Miller", new Email("james.miller@school.edu"));
$userRepo->save($user2);
echo "  ✓ User creado: {$user2->getName()} (ID: {$user2->getId()})\n\n";

echo "Paso 2: Crear Student\n";
$student1 = new Student(null, $user2->getId(), "STU-2024-001");
$studentRepo->save($student1);
echo "  ✓ Student creado: Matrícula {$student1->getEnrollmentNumber()} (ID: {$student1->getId()})\n";
echo "  • Course ID inicial: " . ($student1->getCourseId() ?? 'NULL') . "\n\n";

echo "Paso 3: Crear Course\n";
$course1 = new Course(null, "Calculus I", "MATH101", 4);
$courseRepo->save($course1);
echo "  ✓ Course creado: {$course1->getName()} [{$course1->getCode()}] - {$course1->getCredits()} créditos (ID: {$course1->getId()})\n\n";

echo "Paso 4: Ejecutar AssignStudentCourseService\n";
$studentService = new AssignStudentCourseService($studentRepo, $courseRepo);
$studentService->execute($student1->getId(), $course1->getId());
$updatedStudent = $studentRepo->findById($student1->getId());
echo "  ✓ Servicio ejecutado correctamente\n";
echo "  • Course ID después de asignación: {$updatedStudent->getCourseId()}\n";
echo "\n";
echo "  ✅ RESULTADO: Estudiante asignado exitosamente al curso\n";
echo "\n";

// ============================================================
// DEMOSTRACIÓN DE MANEJO DE ERRORES
// ============================================================
echo "┌────────────────────────────────────────────────────────────┐\n";
echo "│  DEMOSTRACIÓN: Manejo de Errores                          │\n";
echo "└────────────────────────────────────────────────────────────┘\n\n";

echo "Intentando asignar profesor inexistente (ID: 999)...\n";
try {
    $teacherService->execute(999, $dept1->getId());
} catch (\RuntimeException $e) {
    echo "  ❌ Error capturado: {$e->getMessage()}\n";
}
echo "\n";

echo "Intentando asignar estudiante a curso inexistente (ID: 999)...\n";
try {
    $studentService->execute($student1->getId(), 999);
} catch (\RuntimeException $e) {
    echo "  ❌ Error capturado: {$e->getMessage()}\n";
}
echo "\n";

// ============================================================
// RESUMEN FINAL
// ============================================================
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                    RESUMEN DE LA DEMO                      ║\n";
echo "╠════════════════════════════════════════════════════════════╣\n";
echo "║  • Total Users creados: " . count($userRepo->findAll()) . "                                    ║\n";
echo "║  • Total Teachers creados: " . count($teacherRepo->findAll()) . "                                 ║\n";
echo "║  • Total Students creados: " . count($studentRepo->findAll()) . "                                 ║\n";
echo "║  • Total Departments creados: " . count($deptRepo->findAll()) . "                              ║\n";
echo "║  • Total Courses creados: " . count($courseRepo->findAll()) . "                                   ║\n";
echo "║                                                            ║\n";
echo "║  ✅ Todos los casos de uso ejecutados correctamente       ║\n";
echo "║  ✅ Arquitectura DDD implementada correctamente           ║\n";
echo "║  ✅ Separación de capas respetada                         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "Para ejecutar los tests: vendor/bin/phpunit\n";
echo "Para iniciar la aplicación web: php -S localhost:8000 -t public\n";
echo "\n";
