<?php
declare(strict_types=1);

namespace app\services\User;

use app\auth\dto\UserIdentityDto;
use app\repositories\User\dto\UserStoreDto;
use app\repositories\User\UserRepository;
use DateMalformedStringException;
use DomainException;
use Yii;
use yii\db\Exception;

class UserCreateService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function execute(UserStoreDto $dto): ?UserIdentityDto
    {
        try {
            if ($this->userRepository->getByUsername($dto->username) !== null) {
                throw new DomainException('Пользователь существует');
            }
            $user = $this->userRepository->store($dto);
            $auth = Yii::$app->getAuthManager();
            $role = $auth->getRole($dto->type->value);
            $auth->assign($role, $user->id);
            return $user;
        } catch (Exception $e) {
            Yii::error([
                'type' => 'UserCreateService',
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

}