<?php

namespace app\repositories\Code;

use app\repositories\BaseRepository;
use app\repositories\Category\CategoryRepository;
use app\repositories\Code\dto\CodeDto;
use app\repositories\Code\dto\CodeSearchDto;
use app\repositories\Code\enums\CodeStatusEnum;

class CodeRepository extends BaseRepository
{
    public const string TABLE_NAME = 'code';

    /**
     * @return CodeDto[]
     */
    public function getAll(?CodeSearchDto $dto = null): array
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
            ])
            ->from([self::TABLE_NAME])
            ->leftJoin(
                table: [CategoryRepository::TABLE_NAME],
                on: 'category.id = code.category_id'
            );

        if (!empty($dto->code)) {
            $query->andWhere(['like', 'LOWER(code)',  mb_strtolower($dto->code)]);
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

    public function issuedCode(CodeStatusEnum $status, int $id, ?string $comment = null): void
    {
        $columns = ['status' => $status->value];
        if ($comment !== null) {
            $columns['comment'] = $comment;
        }
        $this->getCommand()
            ->update(
                table: self::TABLE_NAME,
                columns: $columns,
                condition: ['id' => $id],
            )
            ->execute();
    }

    public function create(
        string $code,
        int $user_id,
        CodeStatusEnum $status,
        int $price,
        string $comment,
        int $categoryId,
        int $quantity
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
            ])
            ->from(self::TABLE_NAME)
            ->where(['code' => $code])
            ->andWhere(['category_id' => $categoryId])
            ->leftJoin(
                table: [CategoryRepository::TABLE_NAME],
                on: 'category.id = code.category_id'
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