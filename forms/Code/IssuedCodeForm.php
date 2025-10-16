<?php

namespace app\forms\Code;

use app\repositories\Code\enums\CodeStatusEnum;
use yii\base\Model;

class IssuedCodeForm extends Model
{
    public string $id = '';
    public string $comment = '';
    public string $code = '';
    public string $status = '';

    public function rules(): array
    {
        return [
            [['id', 'status', 'code'], 'required'],
            [['comment'], 'string', 'max' => 255],
            [['code'], 'integer'],
            [['status'], 'in', 'range' => [CodeStatusEnum::ISSUED->value, CodeStatusEnum::ISSUED_FREE->value, CodeStatusEnum::LOST->value]],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'comment' => 'Комментарий',
            'code' => 'Код',
            'status' => 'Статус',
        ];
    }

    public function getStatusOptions(): array
    {
        return [
            CodeStatusEnum::ISSUED->value => CodeStatusEnum::ISSUED->value,
            CodeStatusEnum::ISSUED_FREE->value => CodeStatusEnum::ISSUED_FREE->value,
            CodeStatusEnum::LOST->value => CodeStatusEnum::LOST->value,
        ];
    }
}