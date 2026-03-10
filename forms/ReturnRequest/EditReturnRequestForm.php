<?php

namespace app\forms\ReturnRequest;

use yii\base\Model;
use yii\web\UploadedFile;

class EditReturnRequestForm extends Model
{
    public string $phone = '';
    public UploadedFile|string|null $photoOne = null;
    public UploadedFile|string|null $qrCode = null;

    public function rules(): array
    {
        return [
            [['phone'], 'required'],
            [['phone'], 'string', 'max' => 32],
            [['photoOne'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
            [['qrCode'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'phone' => 'Телефон клиента',
            'photoOne' => 'Фото 1',
            'qrCode' => 'QR код',
        ];
    }
}
