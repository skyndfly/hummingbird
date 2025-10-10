<?php

namespace app\repositories\User\dto;

class UserInfoDto
{
    public function __construct(
        public string $firstName,
        public string $name,
        public string $lastName,
        public string $birthDate,
        public string $numberPhone,
    )
    {
    }
}