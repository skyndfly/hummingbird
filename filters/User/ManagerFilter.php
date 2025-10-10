<?php

namespace app\filters\User;

use yii\base\Model;

class ManagerFilter extends Model
{
    public ?string $username = null;
    public ?string $fio = null;

    public function rules(): array
    {
        return [
            [['username', 'fio'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Логин',
            'fio' => 'ФИО',
        ];
    }
}