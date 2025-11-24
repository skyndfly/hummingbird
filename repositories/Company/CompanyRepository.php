<?php

namespace app\repositories\Company;

use app\repositories\BaseRepository;
use app\services\Company\dto\CompanyDto;
use app\services\Company\exceptions\CompanyNotFoundException;
use yii\helpers\ArrayHelper;

class CompanyRepository extends BaseRepository
{
    public const string TABLE = 'company';

    /**
     * @return CompanyDto[]
     */
    public function getAllCompany(): array
    {
        $result = $this->getQuery()
            ->from(self::TABLE)
            ->all();
        return array_map(
            callback: fn(array $item) => CompanyDto::fromDbRecord($item),
            array: $result
        );
    }

    /**
     * @return array<int, string>
     */
    public function getAllAsMap(): array
    {
        $companies = $this->getAllCompany();
        return ArrayHelper::map($companies, 'id', 'name');
    }

    public function getById(int $companyId): CompanyDto
    {
        $record = $this->getQuery()
            ->from(self::TABLE)
            ->where(['id' => $companyId])
            ->one();
        if ($record === false) {
            throw new CompanyNotFoundException($companyId);
        }
        return CompanyDto::fromDbRecord($record);
    }

    public function update(string $name, string $botKey, int $id): void
    {
        $this->getCommand()->update(
            table: self::TABLE,
            columns: [
                'bot_key' => $botKey,
                'name' => $name,
            ],
            condition: ['id' => $id]
        )
            ->execute();
    }
}