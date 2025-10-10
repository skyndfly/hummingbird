<?php

namespace app\repositories\User\dto;

use app\auth\enums\UserTypeEnum;

class UserSearchDto
{
    public function __construct(
        public UserTypeEnum $type,
        public ?string $username = null,
        public ?string $fio = null,
    )
    {
    }
}