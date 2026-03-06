<?php

namespace app\forms;

use yii\base\Model;

class PublicUploadForm extends Model
{
    public int $companyId = 0;
    public int $addressId = 0;
    public string $phone = '';
    public $image;

    public function rules(): array
    {
        return [
            [['companyId', 'addressId', 'image', 'phone'], 'required'],
            [['companyId', 'addressId'], 'integer', 'min' => 1],
            [['phone'], 'string', 'min' => 5, 'max' => 32],
            [['image'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg, gif'],
        ];
    }
}
