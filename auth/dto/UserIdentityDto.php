<?php

namespace app\auth\dto;

use app\auth\enums\UserTypeEnum;
use app\repositories\User\dto\UserInfoDto;

class UserIdentityDto
{
    public function __construct(
        public int $id,
        public string $username,
        public string $password,
        public string $createdAt,
        public string $updatedAt,
        public UserTypeEnum $type,
        public UserInfoDto $userInfoDto,
        public ?string $accessToken = null,
        public ?string $authKey = null,
    ) {
    }
}