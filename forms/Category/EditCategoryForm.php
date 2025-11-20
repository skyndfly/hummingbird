<?php

namespace app\forms\Category;

use yii\base\Model;

class EditCategoryForm extends Model
{
    public string $name = '';
    public int $id = 0;

    public function rules(): array
    {
        return [
            [['name', 'id'], 'required'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название места хранения'
        ];
    }
}