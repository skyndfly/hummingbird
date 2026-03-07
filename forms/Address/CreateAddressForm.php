<?php

namespace app\forms\Address;

use yii\base\Model;

class CreateAddressForm extends Model
{
    public int $companyId = 0;
    public string $address = '';

    public function rules(): array
    {
        return [
            [['companyId', 'address'], 'required'],
            [['companyId'], 'integer', 'min' => 1],
            [['address'], 'string', 'min' => 2],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'companyId' => 'Служба доставки',
            'address' => 'Адрес',
        ];
    }
}
