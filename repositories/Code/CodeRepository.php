<?php

namespace app\repositories\Code;

use app\filters\Code\CodeFilter;
use app\repositories\BaseRepository;
use app\repositories\Category\CategoryRepository;
use app\repositories\Code\dto\CodeDto;
use app\repositories\Code\dto\CodeSearchDto;
use app\repositories\Code\dto\GroupedCodeDto;
use app\repositories\Code\enums\CodeStatusEnum;
use app\repositories\Company\CompanyRepository;
use yii\db\Expression;

class CodeRepository extends BaseRepository
{
    public const string TABLE_NAME = 'code';

    /**
     * @return CodeDto[]
     */
    public function getAll(?CodeFilter $dto = null): array
    {
        $query = $this->getQuery()
            ->select([
                'code.id',
                'code.code',
                'code.status',
                'code.price',
                'code.comment',
                'code.quantity',
                'code.user_id',
                'code.created_at',
                'code.updated_at',
                'category.id as category_id',
                'category.name as category_name',
                'company.id as company_id',
                'company.name as company_name',
                'company.commission_strategy as company_commission_strategy',
            ])
            ->from([self::TABLE_NAME])
            ->leftJoin(
                table: [CategoryRepository::TABLE_NAME],
                on: 'category.id = code.category_id'
            )
            ->leftJoin(
                table: [CompanyRepository::TABLE],
                on: 'company.id = code.company_id'
            );

        if (!empty($dto->code)) {
            $query->andWhere(['like', 'LOWER(code)', mb_strtolower($dto->code)]);
        }
        if (!empty($dto->categoryId)) {
            $query->andWhere(['code.category_id' => $dto->categoryId]);
        }
        if (!empty($dto->date)) {
            $query->andWhere(['DATE(created_at)' => $dto->date]);
        }

        return array_map(
            callback: fn($item) => CodeDto::fromDbRecord($item),
            array: $query->all()
        );
    }

    /**
     * Группированный поиск кодов
     * @return GroupedCodeDto[]
     */
    public function findCodes(?CodeSearchDto $dto = null): array
    {
        $query = $this->getQuery()
            ->select([
                'code.code',
                'code.status',
                'code.price',
                new Expression('STRING_AGG(DISTINCT code.comment, \', \' ORDER BY code.comment) AS comments'),
                'SUM(code.quantity) as quantity',
                'category.id as category_id',
                'category.name as category_name',
                'company.id as company_id',
                'company.name as company_name',
                'company.commission_strategy as company_commission_strategy',
                new Expression('SUM(
                    CASE
                        WHEN code.status NOT IN (\'Выдан/Наличные\', \'Выдан/Бесплатно\', \'Не найден\', \'Выдан/Оплата картой\')
                        THEN code.price
                        ELSE 0
                    END
                ) AS unpaid_total'),
                new Expression('STRING_AGG(code.id::text, \',\') as id')
            ])
            ->from([self::TABLE_NAME . ' code'])
            ->leftJoin(
                table: [CategoryRepository::TABLE_NAME . ' category'],
                on: 'category.id = code.category_id'
            )
            ->leftJoin(
                table: [CompanyRepository::TABLE . ' company'],
                on: 'company.id = code.company_id'
            );
        if (!empty($dto->code)) {
            $query->andWhere(['like', 'LOWER(code.code)', mb_strtolower($dto->code)]);
        }
        if (!empty($dto->categoryId)) {
            $query->andWhere(['code.category_id' => $dto->categoryId]);
        }
        if (!empty($dto->date)) {
            $query->andWhere(['DATE(code.created_at)' => $dto->date]);
        }

        $query->groupBy(['code.code', 'code.status', 'code.price', 'category.id', 'company.id'])
            ->orderBy(['code.code' => SORT_ASC, 'category.name' => SORT_ASC]);

        return array_map(
            callback: fn($item) => GroupedCodeDto::fromDbRecord($item),
            array: $query->all()
        );
    }

    /**
     * @param int[] $id
     */
    public function issuedCode(CodeStatusEnum $status, array $id): void
    {
        $this->getCommand()
            ->update(
                table: self::TABLE_NAME,
                columns: [
                    'status' => $status->value,
                    'updated_at' => $this->getCurrentDate()
                ],
                condition: [
                    'and',
                    ['id' => $id],
                    ['status' => CodeStatusEnum::NEW->value]
                ],
            )
            ->execute();
    }

    //TODO вынести параметры в ДТО
    public function create(
        string $code,
        int $user_id,
        CodeStatusEnum $status,
        int $price,
        string $comment,
        int $categoryId,
        int $quantity,
        int $companyId
    ): void {
        $this->getCommand()->insert(
            table: self::TABLE_NAME,
            columns: [
                'code' => $code,
                'user_id' => $user_id,
                'status' => $status->value,
                'price' => $price,
                'comment' => $comment,
                'category_id' => $categoryId,
                'company_id' => $companyId,
                'quantity' => $quantity,
                'created_at' => $this->getCurrentDate(),
                'updated_at' => $this->getCurrentDate(),
            ]
        )->execute();

    }

    public function findCodeByIdAndCategory(string $code, int $categoryId): ?CodeDto
    {
        $row = $this->getQuery()
            ->select([
                'code.id',
                'code.code',
                'code.status',
                'code.price',
                'code.comment',
                'code.quantity',
                'code.user_id',
                'code.created_at',
                'code.updated_at',
                'category.id as category_id',
                'category.name as category_name',
                'company.id as company_id',
                'company.name as company_name',
                'company.commission_strategy as company_commission_strategy',
            ])
            ->from(self::TABLE_NAME)
            ->where(['code' => $code])
            ->andWhere(['category_id' => $categoryId])
            ->leftJoin(
                table: [CategoryRepository::TABLE_NAME],
                on: 'category.id = code.category_id'
            )
            ->leftJoin(
                table: [CompanyRepository::TABLE],
                on: 'company.id = code.company_id'
            )
            ->one();


        if ($row === false) {
            return null;
        }
        return CodeDto::fromDbRecord($row);
    }

    public function update(CodeDto $dto): void
    {
        $this->getCommand()->update(
            table: self::TABLE_NAME,
            columns: [
                'code' => $dto->code,
                'user_id' => $dto->userId,
                'status' => $dto->status->value,
                'price' => $dto->price,
                'comment' => $dto->comment,
                'category_id' => $dto->category->id,
                'quantity' => $dto->quantity,
                'updated_at' => $this->getCurrentDate(),
            ],
            condition: ['id' => $dto->id]
        )
            ->execute();
    }
}