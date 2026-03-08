<?php

namespace app\forms\ReturnRequest;

use yii\base\Model;
use yii\web\UploadedFile;

class EditReturnRequestForm extends Model
{
    public string $phone = '';
    public UploadedFile|string|null $photoOne = null;
    public UploadedFile|string|null $photoTwo = null;

    public function rules(): array
    {
        return [
            [['phone'], 'required'],
            [['phone'], 'string', 'max' => 32],
            [['photoOne', 'photoTwo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'phone' => 'Телефон клиента',
            'photoOne' => 'Фото 1',
            'photoTwo' => 'Фото 2',
        ];
    }
}
