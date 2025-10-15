<?php

namespace app\forms\Code;

use yii\base\Model;

class CreateCodeForm extends Model
{
    public string $code = '';
    public int $price = 0;
    public string $comment = '';
    public int $quantity = 1;
    public int $categoryId = 0;

    public function rules(): array
    {
        return [
            [['code', 'price', 'categoryId', 'quantity'], 'required'],
            [['price'], 'integer'],
            [['quantity', 'categoryId'], 'integer', 'min' => 1],
            [['comment', 'code'], 'string']

        ];
    }

    public function attributeLabels(): array
    {
        return [
            'code' => 'Код',
            'price' => 'Стоимость',
            'categoryId' => 'Место хранения',
            'comment' => 'Комментарий (необязательное поле)',
            'quantity' => 'Количество'
        ];
    }

}