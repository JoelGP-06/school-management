<?php

namespace School\Infrastructure\Http\Controllers;

use School\Infrastructure\Http\Request;
use School\Infrastructure\Http\ResponseJson;
use School\Domain\Repository\TeacherRepositoryInterface;
use School\Domain\Repository\UserRepositoryInterface;
use School\Domain\Repository\DepartmentRepositoryInterface;
use School\Domain\Entity\Teacher;
use School\Domain\Entity\User;
use School\Domain\ValueObject\Email;
use School\Application\Service\AssignTeacherDepartmentService;

class TeachersController
{
    public function __construct(
        private TeacherRepositoryInterface    $teacherRepository,
        private UserRepositoryInterface       $userRepository,
        private DepartmentRepositoryInterface $departmentRepository,
    ) {}

    /**
     * GET /api/teachers
     */
    public function index(Request $request): void
    {
        $teachers = $this->teacherRepository->findAll();
        $data     = array_map(fn($t) => $this->serializeTeacher($t), $teachers);
        ResponseJson::success($data, 'Teachers retrieved successfully');
    }

    /**
     * GET /api/teachers/{id}
     */
    public function show(Request $request): void
    {
        $id      = (int) $request->getParam('id');
        $teacher = $this->teacherRepository->findById($id);

        if (!$teacher) {
            ResponseJson::notFound("Teacher with ID {$id} not found");
        }

        ResponseJson::success($this->serializeTeacher($teacher), 'Teacher retrieved successfully');
    }

    /**
     * POST /api/teachers
     * Body: { "name": "...", "email": "...", "specialty": "..." }
     */
    public function store(Request $request): void
    {
        $name      = $request->getBodyParam('name');
        $email     = $request->getBodyParam('email');
        $specialty = $request->getBodyParam('specialty');

        if (!$name || !$email || !$specialty) {
            ResponseJson::error('Fields required: name, email, specialty');
        }

        try {
            $emailVO = new Email($email);
        } catch (\InvalidArgumentException $e) {
            ResponseJson::error($e->getMessage());
        }

        $user = new User(null, $name, $emailVO);
        $this->userRepository->save($user);

        $teacher = new Teacher(null, $user->getId(), $specialty);
        $this->teacherRepository->save($teacher);

        ResponseJson::created($this->serializeTeacher($teacher), 'Teacher created successfully');
    }

    /**
     * PUT /api/teachers/{id}
     * Body: { "name": "...", "email": "...", "specialty": "...", "department_id": int|null }
     */
    public function update(Request $request): void
    {
        $id      = (int) $request->getParam('id');
        $teacher = $this->teacherRepository->findById($id);

        if (!$teacher) {
            ResponseJson::notFound("Teacher with ID {$id} not found");
        }

        // Update user data
        $user = $this->userRepository->findById($teacher->getUserId());
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
                $updatedUser = new User($user->getId(), $newName, $emailVO);
                $this->userRepository->save($updatedUser);
            }
        }

        // Update specialty
        if ($request->getBodyParam('specialty') !== null) {
            $ref = new \ReflectionProperty($teacher, 'specialty');
            $ref->setAccessible(true);
            $ref->setValue($teacher, $request->getBodyParam('specialty'));
        }

        // Assign to department
        if ($request->getBodyParam('department_id') !== null) {
            $deptId  = (int) $request->getBodyParam('department_id');
            $service = new AssignTeacherDepartmentService($this->teacherRepository, $this->departmentRepository);
            try {
                $service->execute($teacher->getId(), $deptId);
                $teacher = $this->teacherRepository->findById($id);
            } catch (\RuntimeException $e) {
                ResponseJson::error($e->getMessage(), 422);
            }
        } else {
            $this->teacherRepository->save($teacher);
        }

        ResponseJson::success($this->serializeTeacher($teacher), 'Teacher updated successfully');
    }

    /**
     * DELETE /api/teachers/{id}
     */
    public function delete(Request $request): void
    {
        $id      = (int) $request->getParam('id');
        $teacher = $this->teacherRepository->findById($id);

        if (!$teacher) {
            ResponseJson::notFound("Teacher with ID {$id} not found");
        }

        $this->teacherRepository->delete($id);

        ResponseJson::success(null, 'Teacher deleted successfully');
    }

    // -------------------------------------------------------------------------

    private function serializeTeacher(Teacher $teacher): array
    {
        $user = $this->userRepository->findById($teacher->getUserId());

        return [
            'id'            => $teacher->getId(),
            'specialty'     => $teacher->getSpecialty(),
            'department_id' => $teacher->getDepartmentId(),
            'hired_at'      => $teacher->getHiredAt()->format('Y-m-d H:i:s'),
            'user'          => $user ? [
                'id'    => $user->getId(),
                'name'  => $user->getName(),
                'email' => $user->getEmail()->getValue(),
            ] : null,
        ];
    }
}
