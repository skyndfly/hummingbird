<?php

namespace app\repositories\User\dto;

use app\auth\enums\UserTypeEnum;

class UserStoreDto
{
    public function __construct(
        public string $username,
        public string $password,
        public UserTypeEnum $type,
        public UserInfoDto $userInfo,
    )
    {
    }
}