<?php

namespace app\repositories\Address;

use app\repositories\BaseRepository;
use app\services\Address\dto\AddressDto;
use DomainException;

class AddressRepository extends BaseRepository
{
    public const string TABLE = 'address';

    /**
     * @return AddressDto[]
     */
    public function getAll(bool $includeDeleted = false): array
    {
        $query = $this->getQuery()
            ->from(self::TABLE)
            ->orderBy(['company_id' => SORT_ASC, 'address' => SORT_ASC]);
        if (!$includeDeleted) {
            $query->andWhere(['deleted_at' => null]);
        }
        $records = $query->all();
        return array_map(
            callback: fn(array $record) => AddressDto::fromDbRecord($record),
            array: $records
        );
    }

    /**
     * @return AddressDto[]
     */
    public function getAllWithCompany(bool $includeDeleted = false): array
    {
        $query = $this->getQuery()
            ->from(self::TABLE . ' a')
            ->leftJoin('company c', 'c.id = a.company_id')
            ->select([
                'a.id',
                'a.company_id',
                'a.address',
                'a.deleted_at',
                'c.name as company_name',
                'c.bot_key as company_bot_key',
            ])
            ->orderBy(['c.name' => SORT_ASC, 'a.address' => SORT_ASC]);
        if (!$includeDeleted) {
            $query->andWhere(['a.deleted_at' => null]);
        }
        $records = $query->all();
        return array_map(
            callback: fn(array $record) => AddressDto::fromDbRecord($record),
            array: $records
        );
    }

    /**
     * @return AddressDto[]
     */
    public function getByCompanyId(int $companyId, bool $includeDeleted = false): array
    {
        $query = $this->getQuery()
            ->from(self::TABLE)
            ->where(['company_id' => $companyId])
            ->orderBy(['address' => SORT_ASC]);
        if (!$includeDeleted) {
            $query->andWhere(['deleted_at' => null]);
        }
        $records = $query->all();
        return array_map(
            callback: fn(array $record) => AddressDto::fromDbRecord($record),
            array: $records
        );
    }

    public function getById(int $id): AddressDto
    {
        $record = $this->getQuery()
            ->from(self::TABLE . ' a')
            ->leftJoin('company c', 'c.id = a.company_id')
            ->select([
                'a.id',
                'a.company_id',
                'a.address',
                'a.deleted_at',
                'c.name as company_name',
                'c.bot_key as company_bot_key',
            ])
            ->where(['a.id' => $id, 'a.deleted_at' => null])
            ->one();
        if ($record === false) {
            throw new DomainException('Address not found.');
        }
        return AddressDto::fromDbRecord($record);
    }

    public function getByIdIncludingDeleted(int $id): AddressDto
    {
        $record = $this->getQuery()
            ->from(self::TABLE . ' a')
            ->leftJoin('company c', 'c.id = a.company_id')
            ->select([
                'a.id',
                'a.company_id',
                'a.address',
                'a.deleted_at',
                'c.name as company_name',
                'c.bot_key as company_bot_key',
            ])
            ->where(['a.id' => $id])
            ->one();
        if ($record === false) {
            throw new DomainException('Address not found.');
        }
        return AddressDto::fromDbRecord($record);
    }

    public function findByCompanyAndAddress(int $companyId, string $address): ?AddressDto
    {
        $record = $this->getQuery()
            ->from(self::TABLE)
            ->where(['company_id' => $companyId, 'address' => $address, 'deleted_at' => null])
            ->one();
        if ($record === false) {
            return null;
        }
        return AddressDto::fromDbRecord($record);
    }

    public function create(int $companyId, string $address): void
    {
        $this->getCommand()->insert(
            table: self::TABLE,
            columns: [
                'company_id' => $companyId,
                'address' => $address,
                'created_at' => $this->getCurrentDate(),
                'deleted_at' => null,
            ]
        )->execute();
    }

    public function update(int $id, int $companyId, string $address): void
    {
        $this->getCommand()->update(
            table: self::TABLE,
            columns: [
                'company_id' => $companyId,
                'address' => $address,
            ],
            condition: ['id' => $id]
        )->execute();
    }

    public function softDelete(int $id): void
    {
        $this->getCommand()->update(
            table: self::TABLE,
            columns: [
                'deleted_at' => $this->getCurrentDate(),
            ],
            condition: ['id' => $id]
        )->execute();
    }

    public function restore(int $id): void
    {
        $this->getCommand()->update(
            table: self::TABLE,
            columns: [
                'deleted_at' => null,
            ],
            condition: ['id' => $id]
        )->execute();
    }
}
