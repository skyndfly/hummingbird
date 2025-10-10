<?php

namespace app\services\Courses;

use app\repositories\Courses\CoursesRepository;
use app\repositories\Courses\dto\CreateCourseDto;

class CourseCreateService
{
    private CoursesRepository $coursesRepository;

    public function __construct(CoursesRepository $coursesRepository)
    {
        $this->coursesRepository = $coursesRepository;
    }


    public function execute(CreateCourseDto $dto): void
    {
        $this->coursesRepository->create($dto);
    }
}