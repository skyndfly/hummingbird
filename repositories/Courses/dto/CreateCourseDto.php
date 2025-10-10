<?php

namespace app\repositories\Courses\dto;

class CreateCourseDto
{
    public function __construct(
        public string $title,
        public string $description,
        public string $createdAt,
        public string $updatedAt,
        public bool $isActive = false,
        public int $pricePerLesson = 1200,
    ) {
    }
}