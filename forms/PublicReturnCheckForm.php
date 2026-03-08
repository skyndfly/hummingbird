<?php

namespace app\forms;

use yii\base\Model;

class PublicReturnCheckForm extends Model
{
    public string $returnId = '';
    public string $phone = '';

    public function rules(): array
    {
        return [
            [['returnId', 'phone'], 'required'],
            [['returnId', 'phone'], 'string', 'min' => 1, 'max' => 32],
        ];
    }
}
