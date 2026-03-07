<?php

namespace app\forms\User;

use yii\base\Model;

class ChangePasswordForm extends Model
{
    public string $oldPassword = '';
    public string $newPassword = '';
    public string $newPasswordRepeat = '';

    public function rules(): array
    {
        return [
            [['oldPassword', 'newPassword', 'newPasswordRepeat'], 'required'],
            [['newPassword'], 'string', 'min' => 6],
            [['newPasswordRepeat'], 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Пароли не совпадают'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'oldPassword' => 'Старый пароль',
            'newPassword' => 'Новый пароль',
            'newPasswordRepeat' => 'Повторите новый пароль',
        ];
    }
}
