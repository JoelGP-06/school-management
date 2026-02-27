<?php

namespace School\Domain\Entity;

class Teacher
{
    private ?int $id;
    private int $userId;
    private string $specialty;
    private ?int $departmentId;
    private \DateTime $hiredAt;

    public function __construct(?int $id, int $userId, string $specialty, ?int $departmentId = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->specialty = $specialty;
        $this->departmentId = $departmentId;
        $this->hiredAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getSpecialty(): string
    {
        return $this->specialty;
    }

    public function getDepartmentId(): ?int
    {
        return $this->departmentId;
    }

    public function assignToDepartment(int $departmentId): void
    {
        $this->departmentId = $departmentId;
    }

    public function getHiredAt(): \DateTime
    {
        return $this->hiredAt;
    }
}
