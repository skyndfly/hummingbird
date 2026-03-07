<?php

namespace app\forms\Address;

use yii\base\Model;

class EditAddressForm extends Model
{
    public int $id = 0;
    public int $companyId = 0;
    public string $address = '';

    public function rules(): array
    {
        return [
            [['id', 'companyId', 'address'], 'required'],
            [['id', 'companyId'], 'integer', 'min' => 1],
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
