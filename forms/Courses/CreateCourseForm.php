<?php

namespace app\forms\Courses;

use app\repositories\Courses\dto\CreateCourseDto;
use DateTimeImmutable;
use yii\base\Model;

class CreateCourseForm extends Model
{
    public string $title = '';
    public string $description = '';
    public bool $isActive = false;
    public int $pricePerLesson = 1200;

    public function rules(): array
    {
        return [
            [['title', 'description', 'isActive', 'pricePerLesson'], 'required'],
        ];
    }

    public function attributeLabels():array
    {
        return [
            'title' => 'Название курса',
            'description' => 'Описание',
            'isActive' => 'Активно',
            'pricePerLesson' => 'Стоимость урока',
        ];
    }

    public function mapToDto(): CreateCourseDto
    {
        return new CreateCourseDto(
            title: $this->title,
            description: $this->description,
            createdAt: new DateTimeImmutable()->format('Y-m-d H:i:s'),
            updatedAt: new DateTimeImmutable()->format('Y-m-d H:i:s'),
            isActive: $this->isActive,
            pricePerLesson: $this->pricePerLesson,
        );
    }

}