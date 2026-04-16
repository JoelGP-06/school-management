<?php

namespace School\Infrastructure\Http\Controllers;

use School\Infrastructure\Http\Request;
use School\Infrastructure\Http\ResponseJson;
use School\Domain\Repository\DepartmentRepositoryInterface;
use School\Domain\Entity\Department;

class DepartmentsController
{
    public function __construct(
        private DepartmentRepositoryInterface $departmentRepository,
    ) {}

    /**
     * GET /api/departments
     */
    public function index(Request $request): void
    {
        $departments = $this->departmentRepository->findAll();
        $data        = array_map(fn($d) => $this->serializeDepartment($d), $departments);
        ResponseJson::success($data, 'Departments retrieved successfully');
    }

    /**
     * GET /api/departments/{id}
     */
    public function show(Request $request): void
    {
        $id         = (int) $request->getParam('id');
        $department = $this->departmentRepository->findById($id);

        if (!$department) {
            ResponseJson::notFound("Department with ID {$id} not found");
        }

        ResponseJson::success($this->serializeDepartment($department), 'Department retrieved successfully');
    }

    /**
     * POST /api/departments
     * Body: { "name": "...", "code": "..." }
     */
    public function store(Request $request): void
    {
        $name = $request->getBodyParam('name');
        $code = $request->getBodyParam('code');

        if (!$name || !$code) {
            ResponseJson::error('Fields required: name, code');
        }

        // Check code uniqueness
        if ($this->departmentRepository->findByCode(strtoupper($code))) {
            ResponseJson::error("Department code '{$code}' already exists", 409);
        }

        $department = new Department(null, $name, strtoupper($code));
        $this->departmentRepository->save($department);

        ResponseJson::created($this->serializeDepartment($department), 'Department created successfully');
    }

    /**
     * PUT /api/departments/{id}
     * Body: { "name": "...", "code": "..." }
     */
    public function update(Request $request): void
    {
        $id         = (int) $request->getParam('id');
        $department = $this->departmentRepository->findById($id);

        if (!$department) {
            ResponseJson::notFound("Department with ID {$id} not found");
        }

        if ($request->getBodyParam('name') !== null) {
            $ref = new \ReflectionProperty($department, 'name');
            $ref->setAccessible(true);
            $ref->setValue($department, $request->getBodyParam('name'));
        }

        if ($request->getBodyParam('code') !== null) {
            $newCode  = strtoupper($request->getBodyParam('code'));
            $existing = $this->departmentRepository->findByCode($newCode);
            if ($existing && $existing->getId() !== $id) {
                ResponseJson::error("Department code '{$newCode}' already exists", 409);
            }
            $ref = new \ReflectionProperty($department, 'code');
            $ref->setAccessible(true);
            $ref->setValue($department, $newCode);
        }

        $this->departmentRepository->save($department);

        ResponseJson::success($this->serializeDepartment($department), 'Department updated successfully');
    }

    /**
     * DELETE /api/departments/{id}
     */
    public function delete(Request $request): void
    {
        $id         = (int) $request->getParam('id');
        $department = $this->departmentRepository->findById($id);

        if (!$department) {
            ResponseJson::notFound("Department with ID {$id} not found");
        }

        $this->departmentRepository->delete($id);

        ResponseJson::success(null, 'Department deleted successfully');
    }

    // -------------------------------------------------------------------------

    private function serializeDepartment(Department $department): array
    {
        return [
            'id'         => $department->getId(),
            'name'       => $department->getName(),
            'code'       => $department->getCode(),
            'created_at' => $department->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
