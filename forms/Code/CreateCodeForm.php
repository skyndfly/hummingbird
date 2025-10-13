<?php

namespace app\forms\Code;

use yii\base\Model;

class CreateCodeForm extends Model
{
    public string $code = '';
    public string $price = '';
    public string $comment = '';
    public string $place = '';

    public function rules(): array
    {
        return [
            [['code', 'price', 'place'], 'required'],
            [['code', 'price'], 'integer'],
            [['comment'], 'string']

        ];
    }

    public function attributeLabels(): array
    {
        return [
            'code' => 'Код',
            'price' => 'Стоимость',
            'place' => 'Место хранения',
            'comment' => 'Комментарий (необязательное поле)',
        ];
    }

}