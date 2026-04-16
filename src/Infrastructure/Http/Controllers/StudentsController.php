<?php

namespace School\Infrastructure\Http\Controllers;

use School\Infrastructure\Http\Request;
use School\Infrastructure\Http\ResponseJson;
use School\Domain\Repository\StudentRepositoryInterface;
use School\Domain\Repository\UserRepositoryInterface;
use School\Domain\Repository\CourseRepositoryInterface;
use School\Domain\Entity\Student;
use School\Domain\Entity\User;
use School\Domain\ValueObject\Email;
use School\Application\Service\AssignStudentCourseService;

class StudentsController
{
    public function __construct(
        private StudentRepositoryInterface $studentRepository,
        private UserRepositoryInterface    $userRepository,
        private CourseRepositoryInterface  $courseRepository,
    ) {}

    /**
     * GET /api/students
     * Returns all students with their user info
     */
    public function index(Request $request): void
    {
        $students = $this->studentRepository->findAll();

        $data = array_map(fn($s) => $this->serializeStudent($s), $students);

        ResponseJson::success($data, 'Students retrieved successfully');
    }

    /**
     * GET /api/students/{id}
     * Returns a single student
     */
    public function show(Request $request): void
    {
        $id      = (int) $request->getParam('id');
        $student = $this->studentRepository->findById($id);

        if (!$student) {
            ResponseJson::notFound("Student with ID {$id} not found");
        }

        ResponseJson::success($this->serializeStudent($student), 'Student retrieved successfully');
    }

    /**
     * POST /api/students
     * Creates a new student (and its associated user)
     * Body: { "name": "...", "email": "...", "enrollment_number": "..." }
     */
    public function store(Request $request): void
    {
        $name             = $request->getBodyParam('name');
        $email            = $request->getBodyParam('email');
        $enrollmentNumber = $request->getBodyParam('enrollment_number');

        if (!$name || !$email || !$enrollmentNumber) {
            ResponseJson::error('Fields required: name, email, enrollment_number');
        }

        try {
            $emailVO = new Email($email);
        } catch (\InvalidArgumentException $e) {
            ResponseJson::error($e->getMessage());
        }

        // Check enrollment number uniqueness
        if ($this->studentRepository->findByEnrollmentNumber($enrollmentNumber)) {
            ResponseJson::error("Enrollment number '{$enrollmentNumber}' already exists", 409);
        }

        $user = new User(null, $name, $emailVO);
        $this->userRepository->save($user);

        $student = new Student(null, $user->getId(), $enrollmentNumber);
        $this->studentRepository->save($student);

        ResponseJson::created($this->serializeStudent($student), 'Student created successfully');
    }

    /**
     * PUT /api/students/{id}
     * Updates an existing student's enrollment number or course
     * Body: { "enrollment_number": "...", "course_id": null|int }
     */
    public function update(Request $request): void
    {
        $id      = (int) $request->getParam('id');
        $student = $this->studentRepository->findById($id);

        if (!$student) {
            ResponseJson::notFound("Student with ID {$id} not found");
        }

        // Update user name/email if provided
        $user = $this->userRepository->findById($student->getUserId());
        if ($user) {
            $name  = $request->getBodyParam('name');
            $email = $request->getBodyParam('email');

            if ($name || $email) {
                $newName  = $name  ?? $user->getName();
                $newEmail = $email ?? $user->getEmail()->getValue();
                try {
                    $emailVO = new Email($newEmail);
                } catch (\InvalidArgumentException $e) {
                    ResponseJson::error($e->getMessage());
                }
                // Re-create user object (immutable-ish approach: overwrite with same id)
                $updatedUser = new User($user->getId(), $newName, $emailVO);
                $this->userRepository->save($updatedUser);
            }
        }

        // Update enrollment number if provided
        if ($request->getBodyParam('enrollment_number') !== null) {
            $newEnrollment = $request->getBodyParam('enrollment_number');
            $existing      = $this->studentRepository->findByEnrollmentNumber($newEnrollment);
            if ($existing && $existing->getId() !== $student->getId()) {
                ResponseJson::error("Enrollment number '{$newEnrollment}' already exists", 409);
            }
            // Use reflection to update private property (or add a setter)
            $ref  = new \ReflectionProperty($student, 'enrollmentNumber');
            $ref->setAccessible(true);
            $ref->setValue($student, $newEnrollment);
        }

        // Assign to course if provided
        if ($request->getBodyParam('course_id') !== null) {
            $courseId = (int) $request->getBodyParam('course_id');
            $service  = new AssignStudentCourseService($this->studentRepository, $this->courseRepository);
            try {
                $service->execute($student->getId(), $courseId);
                // Re-fetch after update
                $student = $this->studentRepository->findById($id);
            } catch (\RuntimeException $e) {
                ResponseJson::error($e->getMessage(), 422);
            }
        } else {
            $this->studentRepository->save($student);
        }

        ResponseJson::success($this->serializeStudent($student), 'Student updated successfully');
    }

    /**
     * DELETE /api/students/{id}
     */
    public function delete(Request $request): void
    {
        $id      = (int) $request->getParam('id');
        $student = $this->studentRepository->findById($id);

        if (!$student) {
            ResponseJson::notFound("Student with ID {$id} not found");
        }

        $this->studentRepository->delete($id);

        ResponseJson::success(null, 'Student deleted successfully');
    }

    // -------------------------------------------------------------------------

    private function serializeStudent(Student $student): array
    {
        $user = $this->userRepository->findById($student->getUserId());

        return [
            'id'                => $student->getId(),
            'enrollment_number' => $student->getEnrollmentNumber(),
            'course_id'         => $student->getCourseId(),
            'enrolled_at'       => $student->getEnrolledAt()->format('Y-m-d H:i:s'),
            'user'              => $user ? [
                'id'    => $user->getId(),
                'name'  => $user->getName(),
                'email' => $user->getEmail()->getValue(),
            ] : null,
        ];
    }
}
