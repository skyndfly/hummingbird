<?php

namespace app\forms;

use yii\base\Model;

class PublicCheckForm extends Model
{
    public string $phone = '';

    public function rules(): array
    {
        return [
            [['phone'], 'required'],
            [['phone'], 'string', 'min' => 5, 'max' => 32],
        ];
    }
}
