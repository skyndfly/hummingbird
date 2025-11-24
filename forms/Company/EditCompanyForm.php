<?php

namespace app\forms\Company;

use yii\base\Model;

class EditCompanyForm extends Model
{
    public string $name = '';
    public ?string $botKey = '';
    public int $id = 0;

    public function rules(): array
    {
        return [
            [['name', 'id', 'botKey'], 'required'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Служба доставки',
            'botKey' => 'Ключ для бота	'
        ];
    }
}