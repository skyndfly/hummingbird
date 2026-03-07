<?php

namespace app\forms\BotSettings;

use yii\base\Model;

class BotSettingsForm extends Model
{
    public int $cutoffHour = 16;

    public function rules(): array
    {
        return [
            [['cutoffHour'], 'required'],
            [['cutoffHour'], 'integer', 'min' => 0, 'max' => 23],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cutoffHour' => 'Время окончания приема (час)',
        ];
    }
}
