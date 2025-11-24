<?php

namespace app\forms\Code;

use app\repositories\Code\enums\CodeStatusEnum;
use yii\base\Model;

class EditCodeForm extends Model
{
    public string $id = '';
    public string $code = '';
    public string $status = '';
    public string $comment = '';
    public int $quantity = 0;
    public int $price = 0;
    public int $companyId = 0;
    public int $categoryId = 0;

    public function rules(): array
    {
        return [
            [['id', 'status', 'code','quantity', 'price'], 'required'],
            [['code'], 'string', 'max' => 255],
            [['comment'], 'string'],
            [['companyId', 'categoryId'], 'integer'],
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
            'quantity' => 'Количество',
            'comment' => 'Комментарий',
            'price' => 'Цена',
            'companyId' => 'Служба доставки',
            'categoryId' => 'Место хранения',
        ];
    }

}