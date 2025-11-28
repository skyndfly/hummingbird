<?php

namespace app\forms\UploadedCode;

use yii\base\Model;
use yii\web\UploadedFile;

class ManualUploadForm extends Model
{
    public string $note = '';
    public $image;
    public string $companyName = '';

    public function rules(): array
    {
        return [
            [['note', 'image', 'companyName'], 'required'],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'note' => 'Примечание',
            'image' => 'Код',
        ];
    }

}