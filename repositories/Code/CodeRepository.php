<?php

namespace app\repositories\Code;

use app\repositories\BaseRepository;
use app\repositories\Code\dto\CodeDto;
use app\repositories\Code\enums\CodeStatusEnum;

class CodeRepository extends BaseRepository
{
    public const string TABLE_NAME = 'code';

    /**
     * @param array $filters
     * @return CodeDto[]
     */
    public function getAll(array $filters = []): array
    {
        $query = $this->getQuery()
            ->from(self::TABLE_NAME);

        if(!empty($filters['code'])){
            $query->andWhere(['code' => $filters['code']]);
        }
        if(!empty($filters['created_at'])){
            $query->andWhere(['DATE(created_at)' => $filters['created_at']]);
        }

        return array_map(
            callback: fn ($item) => CodeDto::fromDbRecord($item),
            array: $query->all()
        );
    }

    public function issuedCode(CodeStatusEnum $status, int $id, ?string $comment = null): void
    {
        $columns = ['status' => $status->value];
        if ($comment !== null){
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
        int $code,
        int $user_id,
        CodeStatusEnum $status,
        int $price,
        string $comment,
        string $place,
    ): void {
        $this->getCommand()->insert(
            table: self::TABLE_NAME,
            columns: [
                'code' => $code,
                'user_id' => $user_id,
                'status' => $status->value,
                'price' => $price,
                'comment' => $comment,
                'place' => $place,
                'created_at' => $this->getCurrentDate(),
                'updated_at' => $this->getCurrentDate(),
            ]
        )->execute();

    }

}