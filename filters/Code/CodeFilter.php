<?php

namespace app\filters\Code;

use yii\base\Model;

class CodeFilter extends Model
{
    public ?string $code = null;
    public ?string $date = null;
    public ?string $categoryId = null;

    public function rules(): array
    {
        return [
            [['code', 'date', 'categoryId'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'code' => 'Код',
            'date' => 'Дата прихода',
            'categoryId' => 'Место хранения',
        ];
    }
}