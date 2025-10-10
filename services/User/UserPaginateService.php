<?php

namespace app\services\User;

use app\auth\dto\UserIdentityDto;
use app\repositories\User\dto\UserSearchDto;
use app\repositories\User\UserRepository;


class UserPaginateService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return UserIdentityDto[]
     */
    public function execute(UserSearchDto $dto): array
    {
        return $this->userRepository->getAllManagerAndSearch($dto);
    }
}