<?php

namespace app\forms\ReturnRequest;

use yii\base\Model;
use yii\web\UploadedFile;

class CreateReturnRequestForm extends Model
{
    public string $phone = '';
    public string $returnType = 'wb';
    public UploadedFile|string|null $photoOne = null;

    public function rules(): array
    {
        return [
            [['phone', 'photoOne', 'returnType'], 'required'],
            [['phone'], 'string', 'max' => 32],
            [['returnType'], 'in', 'range' => ['wb', 'ozon']],
            [['photoOne'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg, gif, webp'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'phone' => 'Телефон клиента',
            'returnType' => 'Тип возврата',
            'photoOne' => 'Фото 1',
        ];
    }
}
