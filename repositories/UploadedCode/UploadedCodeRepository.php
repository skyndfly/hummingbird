<?php

namespace app\repositories\UploadedCode;

use app\repositories\BaseRepository;
use app\services\UploadCode\dto\UploadedCodeDto;
use app\services\UploadCode\enums\UploadedCodeCompanyKeyEnum;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use DomainException;

class UploadedCodeRepository extends BaseRepository
{
    public const string TABLE = 'uploaded_code';

    public function create(UploadedCodeDto $dto): void
    {
        $this->getCommand()
            ->insert(
                table: self::TABLE,
                columns: [
                    'company_key' => $dto->companyKey,
                    'file_name' => $dto->fileName,
                    'status' => $dto->status->value,
                    'chat_id' => $dto->chatId,
                    'created_at' => $this->getCurrentDate(),
                    'updated_at' => $this->getCurrentDate(),
                    'note' => $dto->note,
                    'address_id' => $dto->addressId,
                ]
            )
            ->execute();
    }

    public function findAwaitCodeToday(UploadedCodeCompanyKeyEnum $companyKey): ?UploadedCodeDto
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $record = $this->getQuery()
            ->from(self::TABLE)
            ->where(['company_key' => $companyKey->value])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->andWhere(['status' => UploadedCodeStatusEnum::AWAIT->value])
            ->one();
        if ($record === false) {
            return null;
        }
        return UploadedCodeDto::fromDbRecord($record);
    }

    public function findAwaitCodeTodayByAddress(int $addressId): ?UploadedCodeDto
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $record = $this->getQuery()
            ->from(self::TABLE)
            ->where(['address_id' => $addressId])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->andWhere(['status' => UploadedCodeStatusEnum::AWAIT->value])
            ->one();
        if ($record === false) {
            return null;
        }
        return UploadedCodeDto::fromDbRecord($record);
    }

    public function findPendingCodeToday(UploadedCodeCompanyKeyEnum $companyKey): ?UploadedCodeDto
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $record = $this->getQuery()
            ->from(self::TABLE)
            ->where(['company_key' => $companyKey->value])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->andWhere(['status' => UploadedCodeStatusEnum::PENDING->value])
            ->one();
        if ($record === false) {
            return null;
        }
        return UploadedCodeDto::fromDbRecord($record);
    }

    public function findPendingCodeTodayByAddress(int $addressId): ?UploadedCodeDto
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $record = $this->getQuery()
            ->from(self::TABLE)
            ->where(['address_id' => $addressId])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->andWhere(['status' => UploadedCodeStatusEnum::PENDING->value])
            ->one();
        if ($record === false) {
            return null;
        }
        return UploadedCodeDto::fromDbRecord($record);
    }

    /**
     * @return UploadedCodeDto[]
     */
    public function findAllAwaitCodeToday(?int $addressId = null): array
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $query = $this->getQuery()
            ->from(self::TABLE . ' uc')
            ->leftJoin('address a', 'a.id = uc.address_id')
            ->select([
                'uc.*',
                'a.address as address',
            ])
            ->andWhere(['>=', 'uc.created_at', $todayStart])
            ->andWhere(['<=', 'uc.created_at', $todayEnd]);
        if ($addressId !== null) {
            $query->andWhere(['uc.address_id' => $addressId]);
        }
        $records = $query->all();

        return array_map(
            callback: fn(array $record) => UploadedCodeDto::fromDbRecord($record),
            array: $records
        );
    }

    public function getPendingTodayCount(UploadedCodeCompanyKeyEnum $companyKey): int
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        return $this->getQuery()
            ->from(self::TABLE)
            ->where(['company_key' => $companyKey->value])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->andWhere(['status' => UploadedCodeStatusEnum::PENDING->value])
            ->count();
    }

    public function getPendingTodayCountByAddress(int $addressId): int
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        return $this->getQuery()
            ->from(self::TABLE)
            ->where(['address_id' => $addressId])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->andWhere(['status' => UploadedCodeStatusEnum::PENDING->value])
            ->count();
    }

    public function getAwaitTodayCountByAddress(int $addressId): int
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        return $this->getQuery()
            ->from(self::TABLE)
            ->where(['address_id' => $addressId])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->andWhere(['status' => UploadedCodeStatusEnum::AWAIT->value])
            ->count();
    }

    public function issuedCode(int $id, UploadedCodeStatusEnum $status): void
    {
        $this->getCommand()
            ->update(
                table: self::TABLE,
                columns: [
                    'status' => $status->value,
                ],
                condition: ['id' => $id]
            )
            ->execute();
    }

    public function getAllCodeTodayCount(UploadedCodeCompanyKeyEnum $companyKey): int
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        return $this->getQuery()
            ->from(self::TABLE)
            ->where(['company_key' => $companyKey->value])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->count();
    }

    public function getAllCodeTodayCountByAddress(int $addressId): int
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        return $this->getQuery()
            ->from(self::TABLE)
            ->where(['address_id' => $addressId])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->count();
    }

    public function getIssuedCodeTodayCount(UploadedCodeCompanyKeyEnum $companyKey): int
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        return $this->getQuery()
            ->from(self::TABLE)
            ->where(['company_key' => $companyKey->value])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->andWhere(['status' => UploadedCodeStatusEnum::ISSUED->value])
            ->count();
    }

    public function getAwaitCodeTodayCount(UploadedCodeCompanyKeyEnum $companyKey): int
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        return $this->getQuery()
            ->from(self::TABLE)
            ->where(['company_key' => $companyKey->value])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->andWhere(['status' => UploadedCodeStatusEnum::AWAIT->value])
            ->count();
    }

    public function getAwaitCodeTodayCountByAddress(int $addressId): int
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        return $this->getQuery()
            ->from(self::TABLE)
            ->where(['address_id' => $addressId])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->andWhere(['status' => UploadedCodeStatusEnum::AWAIT->value])
            ->count();
    }

    public function getNotpaidCodeTodayCount(UploadedCodeCompanyKeyEnum $companyKey): int
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        return $this->getQuery()
            ->from(self::TABLE)
            ->where(['company_key' => $companyKey->value])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->andWhere(['status' => UploadedCodeStatusEnum::NOT_PAID->value])
            ->count();
    }

    public function getOutdatedCodeTodayCount(UploadedCodeCompanyKeyEnum $companyKey): int
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        return $this->getQuery()
            ->from(self::TABLE)
            ->where(['company_key' => $companyKey->value])
            ->andWhere(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->andWhere(['status' => UploadedCodeStatusEnum::OUTDATED->value])
            ->count();
    }

    public function getById(int $id): UploadedCodeDto
    {
        $record = $this->getQuery()
            ->from(self::TABLE)
            ->where(['id' => $id])
            ->one();
        if ($record === false) {
            throw new DomainException('Record not found.');
        }
        return UploadedCodeDto::fromDbRecord($record);
    }

    public function findLatestByNote(string $note): ?UploadedCodeDto
    {
        $record = $this->getQuery()
            ->from(self::TABLE . ' uc')
            ->leftJoin('address a', 'a.id = uc.address_id')
            ->leftJoin('company c', 'c.bot_key = uc.company_key')
            ->select([
                'uc.*',
                'a.address as address',
                'c.name as company_name',
            ])
            ->where(['uc.note' => $note])
            ->orderBy(['uc.created_at' => SORT_DESC])
            ->one();
        if ($record === false) {
            return null;
        }
        return UploadedCodeDto::fromDbRecord($record);
    }

    /**
     * @return UploadedCodeDto[]
     */
    public function findAllTodayByNote(string $note): array
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $records = $this->getQuery()
            ->from(self::TABLE . ' uc')
            ->leftJoin('address a', 'a.id = uc.address_id')
            ->leftJoin('company c', 'c.bot_key = uc.company_key')
            ->select([
                'uc.*',
                'a.address as address',
                'c.name as company_name',
            ])
            ->where(['uc.note' => $note])
            ->andWhere(['>=', 'uc.created_at', $todayStart])
            ->andWhere(['<=', 'uc.created_at', $todayEnd])
            ->orderBy(['uc.created_at' => SORT_DESC])
            ->all();

        return array_map(
            callback: fn(array $record) => UploadedCodeDto::fromDbRecord($record),
            array: $records
        );
    }
}
