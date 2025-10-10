<?php

namespace app\forms\User;

use app\auth\enums\UserTypeEnum;
use app\repositories\User\dto\UserInfoDto;
use app\repositories\User\dto\UserStoreDto;
use yii\base\Model;

class CreateManagerForm extends Model
{
    public string $username = '';
    public string $password = '';
    public string $first_name = '';
    public string $name = '';
    public string $last_name = '';
    public string $birth_day = '';
    public string $number_phone = '';
    
    public function rules(): array
    {
        return [
            [
                [
                    'username', 'password', 'first_name', 'name', 'last_name', 'birth_day', 'number_phone'
                ],
                'required'
            ],
            ['password', 'string', 'min' => 4],
        ];
    }

    public function attributeLabels():array
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
            'first_name' => 'Фамилия',
            'name' => 'Имя',
            'last_name' => 'Отчество',
            'birth_day' => 'Дата рождения',
            'number_phone' => 'Номер телефона',
        ];
    }
    public function toDto(): UserStoreDto
    {
        $userInfoDto = new UserInfoDto(
            firstName: $this->first_name,
            name: $this->name,
            lastName: $this->last_name,
            birthDate: $this->birth_day,
            numberPhone: $this->number_phone
        );
        return new UserStoreDto(
            username: $this->username,
            password: $this->password,
            type: UserTypeEnum::MANAGER,
            userInfo: $userInfoDto                
        );
    }
    
}