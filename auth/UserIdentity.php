<?php
declare(strict_types=1);

namespace app\auth;

use app\auth\dto\UserIdentityDto;
use app\repositories\User\UserRepository;
use Yii;
use yii\web\IdentityInterface;

class UserIdentity implements IdentityInterface
{
    public UserIdentityDto $user;

    public function __construct(
        UserIdentityDto $user
    ) {
        $this->user = $user;
    }


    /**
     * @param int $id
     */
    public static function findIdentity($id): IdentityInterface|static|null
    {
        $repository = new UserRepository();
        $user = $repository->getById($id);
        return $user !== null ? new self($user) : null;
    }

    /**
     * Авторизация через токен
     * @param string $token
     * @param $type
     * @return IdentityInterface|static|null
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByUsername(string $username): ?UserIdentity
    {
        $repository = new UserRepository();
        $user = $repository->getByUsername($username);
        return $user !== null ? new self($user) : null;
    }

    public function getId(): int
    {
        return $this->user->id;
    }

    public function getAuthKey(): ?string
    {
        return $this->user->authKey;
    }

    /**
     * Для cookie авторизации запомнить меня
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->user->authKey === $authKey;
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->user->password);
    }
}
