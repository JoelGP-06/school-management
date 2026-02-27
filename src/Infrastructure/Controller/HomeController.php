<?php

namespace School\Infrastructure\Controller;

class HomeController
{
    public function student(): void
    {
        require __DIR__ . '/../View/student.php';
    }

    public function teacher(): void
    {
        require __DIR__ . '/../View/teacher.php';
    }
}
