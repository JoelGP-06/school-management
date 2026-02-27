# Ejemplos de Ejecución - School Management System

## 🎯 Ejecución de Casos de Uso

Este documento muestra cómo ejecutar los dos casos de uso principales del sistema.

---

## CASO DE USO 1: Asignación de Profesor a Departamento

### Paso 1: Crear las entidades necesarias

```php
<?php
require 'vendor/autoload.php';

use School\Domain\Entity\User;
use School\Domain\Entity\Teacher;
use School\Domain\Entity\Department;
use School\Domain\ValueObject\Email;
use School\Infrastructure\Persistence\InMemory\InMemoryUserRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryTeacherRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryDepartmentRepository;
use School\Application\Service\AssignTeacherDepartmentService;

// Inicializar repositorios
$userRepository = new InMemoryUserRepository();
$teacherRepository = new InMemoryTeacherRepository();
$departmentRepository = new InMemoryDepartmentRepository();

// Crear User
$user = new User(null, "Dr. Sarah Anderson", new Email("sarah.anderson@school.edu"));
$userRepository->save($user);
echo "✓ User created with ID: " . $user->getId() . "\n";

// Crear Teacher
$teacher = new Teacher(null, $user->getId(), "Computer Science");
$teacherRepository->save($teacher);
echo "✓ Teacher created with ID: " . $teacher->getId() . "\n";

// Crear Department
$department = new Department(null, "Computer Science Department", "CS");
$departmentRepository->save($department);
echo "✓ Department created with ID: " . $department->getId() . "\n";
```

### Paso 2: Ejecutar el servicio de asignación

```php
// Verificar estado inicial
echo "\n=== Estado Inicial ===\n";
echo "Teacher Department ID: " . ($teacher->getDepartmentId() ?? 'NULL') . "\n";

// Crear y ejecutar el servicio
$service = new AssignTeacherDepartmentService(
    $teacherRepository,
    $departmentRepository
);

$service->execute($teacher->getId(), $department->getId());

echo "\n=== Después de la Asignación ===\n";
$updatedTeacher = $teacherRepository->findById($teacher->getId());
echo "Teacher Department ID: " . $updatedTeacher->getDepartmentId() . "\n";
echo "✓ Profesor asignado correctamente al departamento!\n";
```

### Salida Esperada

```
✓ User created with ID: 1
✓ Teacher created with ID: 1
✓ Department created with ID: 1

=== Estado Inicial ===
Teacher Department ID: NULL

=== Después de la Asignación ===
Teacher Department ID: 1
✓ Profesor asignado correctamente al departamento!
```

---

## CASO DE USO 2: Asignación de Estudiante a Curso

### Paso 1: Crear las entidades necesarias

```php
<?php
require 'vendor/autoload.php';

use School\Domain\Entity\User;
use School\Domain\Entity\Student;
use School\Domain\Entity\Course;
use School\Domain\ValueObject\Email;
use School\Infrastructure\Persistence\InMemory\InMemoryUserRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryStudentRepository;
use School\Infrastructure\Persistence\InMemory\InMemoryCourseRepository;
use School\Application\Service\AssignStudentCourseService;

// Inicializar repositorios
$userRepository = new InMemoryUserRepository();
$studentRepository = new InMemoryStudentRepository();
$courseRepository = new InMemoryCourseRepository();

// Crear User
$user = new User(null, "Michael Thompson", new Email("michael.thompson@school.edu"));
$userRepository->save($user);
echo "✓ User created with ID: " . $user->getId() . "\n";

// Crear Student
$student = new Student(null, $user->getId(), "STU-2024-555");
$studentRepository->save($student);
echo "✓ Student created with ID: " . $student->getId() . "\n";

// Crear Course
$course = new Course(null, "Advanced Algorithms", "CS401", 4);
$courseRepository->save($course);
echo "✓ Course created with ID: " . $course->getId() . "\n";
```

### Paso 2: Ejecutar el servicio de asignación

```php
// Verificar estado inicial
echo "\n=== Estado Inicial ===\n";
echo "Student Course ID: " . ($student->getCourseId() ?? 'NULL') . "\n";

// Crear y ejecutar el servicio
$service = new AssignStudentCourseService(
    $studentRepository,
    $courseRepository
);

$service->execute($student->getId(), $course->getId());

echo "\n=== Después de la Asignación ===\n";
$updatedStudent = $studentRepository->findById($student->getId());
echo "Student Course ID: " . $updatedStudent->getCourseId() . "\n";
echo "✓ Estudiante asignado correctamente al curso!\n";
```

### Salida Esperada

```
✓ User created with ID: 1
✓ Student created with ID: 1
✓ Course created with ID: 1

=== Estado Inicial ===
Student Course ID: NULL

=== Después de la Asignación ===
Student Course ID: 1
✓ Estudiante asignado correctamente al curso!
```

---

## 🌐 Ejecución mediante Interfaz Web

### 1. Iniciar el servidor

```bash
php -S localhost:8000 -t public
```

### 2. Acceder a las rutas

**Portal de Estudiantes**
```
http://localhost:8000/student
```

**Portal de Profesores**
```
http://localhost:8000/teacher
```

**Formulario de Asignación de Profesor**
```
http://localhost:8000/assign-teacher
```

**Formulario de Asignación de Estudiante**
```
http://localhost:8000/assign-student
```

### 3. Flujo de trabajo web

#### Asignar Profesor a Departamento:
1. Navegar a `/assign-teacher`
2. Seleccionar un profesor del dropdown
3. Seleccionar un departamento del dropdown
4. Click en "Assign Teacher"
5. Ver mensaje de confirmación

#### Asignar Estudiante a Curso:
1. Navegar a `/assign-student`
2. Seleccionar un estudiante del dropdown
3. Seleccionar un curso del dropdown
4. Click en "Assign Student"
5. Ver mensaje de confirmación

---

## 🧪 Ejecución de Tests

### Ejecutar todos los tests

```bash
vendor/bin/phpunit
```

### Ejecutar un test específico

```bash
vendor/bin/phpunit tests/Application/Service/AssignTeacherDepartmentServiceTest.php
```

```bash
vendor/bin/phpunit tests/Application/Service/AssignStudentCourseServiceTest.php
```

### Salida esperada de tests

```
PHPUnit 10.x.x by Sebastian Bergmann and contributors.

Runtime:       PHP 8.x.x

...........                                                       11 / 11 (100%)

Time: 00:00.123, Memory: 6.00 MB

OK (11 tests, 22 assertions)
```

---

## 📊 Script de Demostración Completo

Crea un archivo `demo.php` en la raíz del proyecto:

```php
<?php
require 'vendor/autoload.php';

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

echo "=== SCHOOL MANAGEMENT SYSTEM - DEMO ===\n\n";

// Inicializar repositorios
$userRepo = new InMemoryUserRepository();
$teacherRepo = new InMemoryTeacherRepository();
$studentRepo = new InMemoryStudentRepository();
$deptRepo = new InMemoryDepartmentRepository();
$courseRepo = new InMemoryCourseRepository();

// DEMO CASO 1: Asignar Profesor a Departamento
echo "--- CASO DE USO 1: Asignar Profesor a Departamento ---\n";

$user1 = new User(null, "Dr. Emily White", new Email("emily.white@school.edu"));
$userRepo->save($user1);

$teacher1 = new Teacher(null, $user1->getId(), "Mathematics");
$teacherRepo->save($teacher1);

$dept1 = new Department(null, "Mathematics Department", "MATH");
$deptRepo->save($dept1);

$teacherService = new AssignTeacherDepartmentService($teacherRepo, $deptRepo);
$teacherService->execute($teacher1->getId(), $dept1->getId());

echo "✓ Profesor '{$user1->getName()}' asignado a '{$dept1->getName()}'\n\n";

// DEMO CASO 2: Asignar Estudiante a Curso
echo "--- CASO DE USO 2: Asignar Estudiante a Curso ---\n";

$user2 = new User(null, "James Miller", new Email("james.miller@school.edu"));
$userRepo->save($user2);

$student1 = new Student(null, $user2->getId(), "STU-2024-001");
$studentRepo->save($student1);

$course1 = new Course(null, "Calculus I", "MATH101", 4);
$courseRepo->save($course1);

$studentService = new AssignStudentCourseService($studentRepo, $courseRepo);
$studentService->execute($student1->getId(), $course1->getId());

echo "✓ Estudiante '{$user2->getName()}' (matrícula: {$student1->getEnrollmentNumber()}) ";
echo "asignado a '{$course1->getName()}' ({$course1->getCode()})\n\n";

echo "=== DEMO COMPLETADA EXITOSAMENTE ===\n";
```

Ejecutar:
```bash
php demo.php
```

---

## ✅ Verificación de Asignaciones

```php
// Verificar profesor asignado
$teacher = $teacherRepo->findById(1);
echo "Profesor con ID 1 está asignado al departamento: " . $teacher->getDepartmentId() . "\n";

// Verificar estudiante asignado
$student = $studentRepo->findById(1);
echo "Estudiante con ID 1 está asignado al curso: " . $student->getCourseId() . "\n";
```

---

## 🚨 Manejo de Errores

```php
try {
    $service->execute(999, 1); // ID de profesor inexistente
} catch (\RuntimeException $e) {
    echo "Error capturado: " . $e->getMessage() . "\n";
    // Salida: "Error capturado: Teacher with ID 999 not found"
}
```
