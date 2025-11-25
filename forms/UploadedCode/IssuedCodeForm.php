<?php

namespace app\forms\UploadedCode;

use yii\base\Model;

class IssuedCodeForm extends Model
{
    public int $id = 0;
    public string $status = '';
    public string $chatId = '';

    public function rules(): array
    {
        return [
            [['id', 'status', 'chatId'], 'required'],
        ];
    }
}