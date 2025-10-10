<?php
declare(strict_types=1);

namespace app\repositories\User;

use app\auth\dto\UserIdentityDto;
use app\auth\enums\UserTypeEnum;
use app\repositories\User\dto\UserSearchDto;
use DateMalformedStringException;
use DateTimeImmutable;
use app\repositories\BaseRepository;
use app\repositories\User\dto\UserInfoDto;
use app\repositories\User\dto\UserStoreDto;
use Yii;
use yii\base\Exception as ExceptionAlias;
use yii\db\Exception;

class UserRepository extends BaseRepository
{
    public const string TABLE_NAME = 'users';

    public function getById(int $id): ?UserIdentityDto
    {
        $result = $this->getQuery()
            ->from(self::TABLE_NAME)
            ->where(['id' => $id])
            ->one();
        if ($result === false) {
            return null;
        }
        return $this->mapToDto($result);
    }

    public function getByUsername(string $username): ?UserIdentityDto
    {
        $result = $this->getQuery()
            ->from(self::TABLE_NAME)
            ->where(['username' => $username])
            ->one();
        if ($result === false) {
            return null;
        }
        return $this->mapToDto($result);
    }

    /**
     * @throws DateMalformedStringException
     * @throws ExceptionAlias
     * @throws Exception
     */
    public function updateUser(UserStoreDto $dto): UserIdentityDto
    {
        $this->getCommand()
            ->update(
                self::TABLE_NAME,
                [
                    'username' => $dto->username,
                    'password_hash' => Yii::$app->security->generatePasswordHash($dto->password),
                    'first_name' => $dto->userInfo->firstName,
                    'name' => $dto->userInfo->name,
                    'type' => $dto->type->value,
                    'last_name' => $dto->userInfo->lastName,
                    'birth_day' => (new DateTimeImmutable($dto->userInfo->birthDate))->format('Y-m-d'),
                    'number_phone' => $dto->userInfo->numberPhone,
                ],
                ['username' => $dto->username]
            )->execute();
        return $this->getByUsername($dto->username);
    }

    /**
     * @throws DateMalformedStringException
     * @throws Exception|ExceptionAlias
     */
    public function store(UserStoreDto $dto): ?UserIdentityDto
    {
        $this->getCommand()
            ->insert(
                self::TABLE_NAME,
                [
                    'username' => $dto->username,
                    'password_hash' => Yii::$app->security->generatePasswordHash($dto->password),
                    'first_name' => $dto->userInfo->firstName,
                    'name' => $dto->userInfo->name,
                    'type' => $dto->type->value,
                    'last_name' => $dto->userInfo->lastName,
                    'birth_day' => new DateTimeImmutable($dto->userInfo->birthDate)->format('Y-m-d'),
                    'number_phone' => $dto->userInfo->numberPhone,
                ]
            )
            ->execute();
        return $this->getById((int) Yii::$app->db->getLastInsertID());
    }

    /**
     * @return UserIdentityDto[]
     */
    public function getAllManagerAndSearch(
        UserSearchDto $dto
    ): array {
        $query = $this->getQuery()
            ->from(self::TABLE_NAME)
            ->where(['!=', 'type', UserTypeEnum::OWNER->value])
            ->andWhere(['type' => $dto->type->value]);


        if (!empty($dto->username)) {
            $query->andWhere(['like', 'username', $dto->username]);
        }
        if (!empty($dto->fio)) {
            $lower = mb_strtolower($dto->fio);
            $query->andWhere(['like', "LOWER(CONCAT_WS(' ', first_name, name ,last_name))", $lower]);
        }
        $all = $query->all();
        return array_map(
            fn($item) => $this->mapToDto($item),
            $all
        );
    }

    /**
     * @return UserIdentityDto[]
     */
    public function getAll(): array
    {
        return array_map(
            fn($item) => $this->mapToDto($item),
            $this->getQuery()
                ->from(self::TABLE_NAME)
                ->all()
        );
    }

    /**
     * @param array{
     *     id: int,
     *     username: string,
     *     password_hash: string,
     *     created_at: string,
     *     updated_at: string,
     *     type: string,
     *     first_name: string,
     *     name: string,
     *     last_name: string,
     *     birth_day: string,
     *     number_phone: string,
     *     access_token: null|string,
     *     auth_key: null|string,
     * } $data
     * @return UserIdentityDto
     */
    private function mapToDto(array $data): UserIdentityDto
    {
        $userInfo = new UserInfoDto(
            firstName: $data['first_name'],
            name: $data['name'],
            lastName: $data['last_name'],
            birthDate: $data['birth_day'],
            numberPhone: $data['number_phone'],
        );
        return new UserIdentityDto(
            id: $data['id'],
            username: $data['username'],
            password: $data['password_hash'],
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at'],
            type: UserTypeEnum::from($data['type']),
            userInfoDto: $userInfo,
            accessToken: $data['access_token'],
            authKey: $data['auth_key'],
        );
    }
}