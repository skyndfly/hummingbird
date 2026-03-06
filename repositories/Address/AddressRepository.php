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
    public function getAll(): array
    {
        $records = $this->getQuery()
            ->from(self::TABLE)
            ->orderBy(['company_id' => SORT_ASC, 'address' => SORT_ASC])
            ->all();
        return array_map(
            callback: fn(array $record) => AddressDto::fromDbRecord($record),
            array: $records
        );
    }

    /**
     * @return AddressDto[]
     */
    public function getAllWithCompany(): array
    {
        $records = $this->getQuery()
            ->from(self::TABLE . ' a')
            ->leftJoin('company c', 'c.id = a.company_id')
            ->select([
                'a.id',
                'a.company_id',
                'a.address',
                'c.name as company_name',
                'c.bot_key as company_bot_key',
            ])
            ->orderBy(['c.name' => SORT_ASC, 'a.address' => SORT_ASC])
            ->all();
        return array_map(
            callback: fn(array $record) => AddressDto::fromDbRecord($record),
            array: $records
        );
    }

    /**
     * @return AddressDto[]
     */
    public function getByCompanyId(int $companyId): array
    {
        $records = $this->getQuery()
            ->from(self::TABLE)
            ->where(['company_id' => $companyId])
            ->orderBy(['address' => SORT_ASC])
            ->all();
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
            ->where(['company_id' => $companyId, 'address' => $address])
            ->one();
        if ($record === false) {
            return null;
        }
        return AddressDto::fromDbRecord($record);
    }
}
