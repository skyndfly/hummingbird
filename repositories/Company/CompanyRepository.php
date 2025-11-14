<?php

namespace app\repositories\Company;

use app\repositories\BaseRepository;
use app\services\Company\dto\CompanyDto;
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
            callback: fn(array $item) =>  CompanyDto::fromArray($item),
            array: $result
        );
    }

    /**
     * @return array<int, string>
     */
    public function getAllAsMap(): array
    {
        $companies =  $this->getAllCompany();
        return ArrayHelper::map($companies, 'id', 'name');
    }
}