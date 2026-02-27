<?php

namespace School\Domain\Entity;

class Department
{
    private ?int $id;
    private string $name;
    private string $code;
    private \DateTime $createdAt;

    public function __construct(?int $id, string $name, string $code)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
