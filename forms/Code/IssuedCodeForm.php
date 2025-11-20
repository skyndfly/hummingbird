<?php

namespace app\forms\Code;

use app\repositories\Code\enums\CodeStatusEnum;
use yii\base\Model;

class IssuedCodeForm extends Model
{
    public array $id = [];
    public string $code = '';
    public string $status = '';

    public function rules(): array
    {
        return [
            [['id', 'status', 'code'], 'required'],
            [['code'], 'string', 'max' => 255],
            [['id'], 'safe'],
            [['status'], 'in', 'range' => [
                CodeStatusEnum::ISSUED->value,
                CodeStatusEnum::ISSUED_FREE->value,
                CodeStatusEnum::ISSUED_CARD->value,
                CodeStatusEnum::NEW->value,
                CodeStatusEnum::LOST->value,
            ]
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'code' => 'Код',
            'status' => 'Статус',
        ];
    }

    public function getStatusOptions(): array
    {
        return [
            CodeStatusEnum::ISSUED->value => CodeStatusEnum::ISSUED->value,
            CodeStatusEnum::ISSUED_FREE->value => CodeStatusEnum::ISSUED_FREE->value,
            CodeStatusEnum::ISSUED_CARD->value => CodeStatusEnum::ISSUED_CARD->value,
            CodeStatusEnum::LOST->value => CodeStatusEnum::LOST->value,
            CodeStatusEnum::NEW->value => CodeStatusEnum::NEW->value,
        ];
    }

    public function getStatusOptionsForManager(): array
    {
        return [
            CodeStatusEnum::NEW->value => CodeStatusEnum::NEW->value,
            CodeStatusEnum::LOST->value => CodeStatusEnum::LOST->value,
        ];
    }

    public function getStatusOptionsForIssuedForm(): array
    {
        return [
            CodeStatusEnum::ISSUED->value => CodeStatusEnum::ISSUED->value,
            CodeStatusEnum::ISSUED_FREE->value => CodeStatusEnum::ISSUED_FREE->value,
            CodeStatusEnum::ISSUED_CARD->value => CodeStatusEnum::ISSUED_CARD->value,
        ];
    }
}