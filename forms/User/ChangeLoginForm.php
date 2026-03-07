<?php

namespace app\forms\User;

use yii\base\Model;

class ChangeLoginForm extends Model
{
    public string $oldUsername = '';
    public string $newUsername = '';
    public string $newUsernameRepeat = '';

    public function rules(): array
    {
        return [
            [['oldUsername', 'newUsername', 'newUsernameRepeat'], 'required'],
            [['oldUsername', 'newUsername', 'newUsernameRepeat'], 'trim'],
            [['newUsername'], 'string', 'min' => 3],
            [['newUsernameRepeat'], 'compare', 'compareAttribute' => 'newUsername', 'message' => 'Логины не совпадают'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'oldUsername' => 'Старый логин',
            'newUsername' => 'Новый логин',
            'newUsernameRepeat' => 'Повторите новый логин',
        ];
    }
}
