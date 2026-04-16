<?php

namespace School\Infrastructure\Http\Controllers;

use School\Infrastructure\Http\Request;
use School\Infrastructure\Http\ResponseJson;
use School\Domain\Repository\CourseRepositoryInterface;
use School\Domain\Entity\Course;

class CoursesController
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository,
    ) {}

    /**
     * GET /api/courses
     */
    public function index(Request $request): void
    {
        $courses = $this->courseRepository->findAll();
        $data    = array_map(fn($c) => $this->serializeCourse($c), $courses);
        ResponseJson::success($data, 'Courses retrieved successfully');
    }

    /**
     * GET /api/courses/{id}
     */
    public function show(Request $request): void
    {
        $id     = (int) $request->getParam('id');
        $course = $this->courseRepository->findById($id);

        if (!$course) {
            ResponseJson::notFound("Course with ID {$id} not found");
        }

        ResponseJson::success($this->serializeCourse($course), 'Course retrieved successfully');
    }

    /**
     * POST /api/courses
     * Body: { "name": "...", "code": "...", "credits": int }
     */
    public function store(Request $request): void
    {
        $name    = $request->getBodyParam('name');
        $code    = $request->getBodyParam('code');
        $credits = $request->getBodyParam('credits');

        if (!$name || !$code || $credits === null) {
            ResponseJson::error('Fields required: name, code, credits');
        }

        if (!is_numeric($credits) || (int) $credits < 1) {
            ResponseJson::error('credits must be a positive integer');
        }

        // Check code uniqueness
        if ($this->courseRepository->findByCode($code)) {
            ResponseJson::error("Course code '{$code}' already exists", 409);
        }

        $course = new Course(null, $name, strtoupper($code), (int) $credits);
        $this->courseRepository->save($course);

        ResponseJson::created($this->serializeCourse($course), 'Course created successfully');
    }

    /**
     * PUT /api/courses/{id}
     * Body: { "name": "...", "code": "...", "credits": int }
     */
    public function update(Request $request): void
    {
        $id     = (int) $request->getParam('id');
        $course = $this->courseRepository->findById($id);

        if (!$course) {
            ResponseJson::notFound("Course with ID {$id} not found");
        }

        if ($request->getBodyParam('name') !== null) {
            $ref = new \ReflectionProperty($course, 'name');
            $ref->setAccessible(true);
            $ref->setValue($course, $request->getBodyParam('name'));
        }

        if ($request->getBodyParam('code') !== null) {
            $newCode  = strtoupper($request->getBodyParam('code'));
            $existing = $this->courseRepository->findByCode($newCode);
            if ($existing && $existing->getId() !== $id) {
                ResponseJson::error("Course code '{$newCode}' already exists", 409);
            }
            $ref = new \ReflectionProperty($course, 'code');
            $ref->setAccessible(true);
            $ref->setValue($course, $newCode);
        }

        if ($request->getBodyParam('credits') !== null) {
            $credits = (int) $request->getBodyParam('credits');
            if ($credits < 1) {
                ResponseJson::error('credits must be a positive integer');
            }
            $ref = new \ReflectionProperty($course, 'credits');
            $ref->setAccessible(true);
            $ref->setValue($course, $credits);
        }

        $this->courseRepository->save($course);

        ResponseJson::success($this->serializeCourse($course), 'Course updated successfully');
    }

    /**
     * DELETE /api/courses/{id}
     */
    public function delete(Request $request): void
    {
        $id     = (int) $request->getParam('id');
        $course = $this->courseRepository->findById($id);

        if (!$course) {
            ResponseJson::notFound("Course with ID {$id} not found");
        }

        $this->courseRepository->delete($id);

        ResponseJson::success(null, 'Course deleted successfully');
    }

    // -------------------------------------------------------------------------

    private function serializeCourse(Course $course): array
    {
        return [
            'id'         => $course->getId(),
            'name'       => $course->getName(),
            'code'       => $course->getCode(),
            'credits'    => $course->getCredits(),
            'created_at' => $course->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
