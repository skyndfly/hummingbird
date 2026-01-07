<?php
declare(strict_types=1);

namespace app\commands;

use app\auth\enums\UserTypeEnum;
use app\config\EnvRegistry;
use app\repositories\User\dto\UserInfoDto;
use app\repositories\User\dto\UserStoreDto;
use app\services\User\UserCreateService;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class RbacController extends Controller
{
    private UserCreateService $userCreateService;
    private const array BASE_ROLES = [
        'owner',
        'manager',
        'point'
    ];

    public function __construct(
        $id,
        $module,
        UserCreateService $userCreateService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->userCreateService = $userCreateService;
    }

    public function actionInit(): void
    {
        try {
            $auth = Yii::$app->getAuthManager();
            foreach (self::BASE_ROLES as $newRole) {
                $role = $auth->getRole($newRole);
                if ($role === null) {
                    $role = $auth->createRole($newRole);
                    $auth->add($role);
                }
            }


            try {
                $userInfo = new UserInfoDto(
                    firstName: EnvRegistry::getOwnerFirstName(),
                    name: EnvRegistry::getOwnerName(),
                    lastName: EnvRegistry::getOwnerLastName(),
                    birthDate: '27-11-1996',
                    numberPhone: EnvRegistry::getOwnerPhoneNumber()
                );
                $userDto = new UserStoreDto(
                    username: EnvRegistry::getOwnerLogin(),
                    password: EnvRegistry::getOwnerPassword(),
                    type: UserTypeEnum::OWNER,
                    userInfo: $userInfo
                );
                $user = $this->userCreateService->execute($userDto);
                $ownerRole = $auth->getRole('owner');
                $auth->assign($ownerRole, $user->id);
            } catch (Throwable $e) {
                Console::error($e->getMessage());
            }

            Console::input('Владелец и базовые роли созданы');
        } catch (Throwable $e) {
            Console::error('Ошибка инициализации:');
            Console::error($e->getMessage());
        }
    }

    public function actionCreateRole(string $name): void
    {
        $auth = Yii::$app->getAuthManager();

        $role = $auth->createRole($name);
        $auth->add($role);
    }

    public function actionBindToRole(int $userId, string $role): void
    {
        $auth = Yii::$app->getAuthManager();
        $role = $auth->getRole($role);
        $auth->assign($role, $userId);
    }
}