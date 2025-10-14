<?php

namespace app\forms\Category;

use yii\base\Model;

class CreateCategoryForm extends Model
{
    public string $name = '';

    public function rules(): array
    {
        return [
            [['name'], 'required'],
        ];
    }
    public function attributeLabels(): array
    {
        return [
            'name' => 'Название места хранения'
        ];
    }
}