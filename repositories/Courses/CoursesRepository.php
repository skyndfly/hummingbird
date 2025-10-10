<?php

namespace app\repositories\Courses;

use app\repositories\BaseRepository;
use app\repositories\Courses\dto\CreateCourseDto;

class CoursesRepository extends BaseRepository
{
    public const string TABLE_NAME = 'courses';

    public function create(CreateCourseDto $dto): void
    {
        $this->getCommand()
            ->insert(
                table: self::TABLE_NAME,
                columns: [
                    'title' => $dto->title,
                    'description' => $dto->description,
                    'is_active' => $dto->isActive,
                    'price_per_lesson' => $dto->pricePerLesson,
                    'created_at' => $dto->createdAt,
                    'updated_at' => $dto->updatedAt,
                ]
            );
    }

}