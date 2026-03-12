<?php

namespace app\repositories\ReturnRequest;

use app\repositories\BaseRepository;
use yii\db\Expression;

class ReturnRequestRepository extends BaseRepository
{
    public const string TABLE = 'return_request';

    public function create(array $data): int
    {
        $this->getCommand()
            ->insert(
                table: self::TABLE,
                columns: $data
            )
            ->execute();

        return (int) $this->getCommand()->db->getLastInsertID();
    }

    public function getById(int $id): ?array
    {
        $row = $this->getQuery()
            ->from(self::TABLE)
            ->where(['id' => $id])
            ->andWhere(['deleted_at' => null])
            ->one();
        if ($row === false) {
            return null;
        }
        return $row;
    }

    public function getByIdAndPhone(int $id, string $phone): ?array
    {
        $row = $this->getQuery()
            ->from(self::TABLE)
            ->where(['id' => $id, 'phone' => $phone])
            ->andWhere(['deleted_at' => null])
            ->one();
        if ($row === false) {
            return null;
        }
        return $row;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    /**
     * @param array<int, string> $statuses
     * @return array<int, array<string, mixed>>
     */
    public function getForPointToday(array $statuses, string $returnType, string $from, string $to): array
    {
        $qrStatus = \app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum::QR_UPLOADED->value;
        $deliveredStatus = \app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum::DELIVERED->value;

        $rows = $this->getQuery()
            ->from(self::TABLE)
            ->where(['status' => $statuses, 'return_type' => $returnType, 'deleted_at' => null])
            ->andWhere(['>=', 'created_at', $from])
            ->andWhere(['<=', 'created_at', $to])
            ->orderBy(new Expression(
                'CASE status WHEN :qr THEN 0 WHEN :delivered THEN 1 ELSE 2 END, created_at DESC, id DESC',
                [':qr' => $qrStatus, ':delivered' => $deliveredStatus]
            ))
            ->all();
        if ($rows === false) {
            return [];
        }
        return $rows;
    }

    /**
     * @param array<int, string> $statuses
     * @return array<int, array<string, mixed>>
     */
    public function getForPoint(
        array $statuses,
        string $returnType,
        int $offset,
        int $limit,
        ?string $status = null,
        ?int $id = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): array
    {
        $query = $this->getQuery()
            ->from(self::TABLE)
            ->where(['status' => $statuses, 'return_type' => $returnType, 'deleted_at' => null])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->offset($offset)
            ->limit($limit);
        if ($status !== null && $status !== '') {
            $query->andWhere(['status' => $status]);
        }
        if ($id !== null) {
            $query->andWhere(['id' => $id]);
        }
        if ($dateFrom !== null && $dateTo !== null) {
            $query->andWhere(['>=', 'created_at', $dateFrom])
                ->andWhere(['<=', 'created_at', $dateTo]);
        }
        $rows = $query->all();
        if ($rows === false) {
            return [];
        }
        return $rows;
    }

    /**
     * @param array<int, string> $statuses
     */
    public function countForPoint(
        array $statuses,
        string $returnType,
        ?string $status = null,
        ?int $id = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): int
    {
        $query = $this->getQuery()
            ->from(self::TABLE)
            ->where(['status' => $statuses, 'return_type' => $returnType, 'deleted_at' => null]);
        if ($status !== null && $status !== '') {
            $query->andWhere(['status' => $status]);
        }
        if ($id !== null) {
            $query->andWhere(['id' => $id]);
        }
        if ($dateFrom !== null && $dateTo !== null) {
            $query->andWhere(['>=', 'created_at', $dateFrom])
                ->andWhere(['<=', 'created_at', $dateTo]);
        }
        return (int) $query->count('*');
    }

    public function getNextForPointToday(string $status, string $returnType, string $from, string $to, int $excludeId): ?array
    {
        $row = $this->getQuery()
            ->from(self::TABLE)
            ->where(['status' => $status, 'return_type' => $returnType, 'deleted_at' => null])
            ->andWhere(['>=', 'created_at', $from])
            ->andWhere(['<=', 'created_at', $to])
            ->andWhere(['<>', 'id', $excludeId])
            ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC])
            ->one();
        if ($row === false) {
            return null;
        }
        return $row;
    }

    public function updateById(int $id, array $columns): void
    {
        $this->getCommand()
            ->update(
                table: self::TABLE,
                columns: $columns,
                condition: ['id' => $id, 'deleted_at' => null]
            )
            ->execute();
    }

    public function updateQrUploaded(int $id, string $qrPath): void
    {
        $this->getCommand()
            ->update(
                table: self::TABLE,
                columns: [
                    'qr_code_file' => $qrPath,
                    'status' => \app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum::QR_UPLOADED->value,
                    'updated_at' => $this->getCurrentDate(),
                ],
                condition: ['id' => $id, 'deleted_at' => null]
            )
            ->execute();
    }

    public function updateStatus(int $id, string $status): void
    {
        $this->getCommand()
            ->update(
                table: self::TABLE,
                columns: [
                    'status' => $status,
                    'updated_at' => $this->getCurrentDate(),
                ],
                condition: ['id' => $id, 'deleted_at' => null]
            )
            ->execute();
    }

    public function softDelete(int $id): void
    {
        $this->getCommand()
            ->update(
                table: self::TABLE,
                columns: ['deleted_at' => $this->getCurrentDate(), 'updated_at' => $this->getCurrentDate()],
                condition: ['id' => $id, 'deleted_at' => null]
            )
            ->execute();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAll(?string $number, ?string $phone, ?string $status = null): array
    {
        $query = $this->getQuery()
            ->from(self::TABLE)
            ->where(['deleted_at' => null])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]);

        if ($number !== null && $number !== '') {
            if (ctype_digit($number)) {
                $query->andWhere(['id' => (int) $number]);
            } else {
                $query->andWhere(['id' => -1]);
            }
        }
        if ($phone !== null && $phone !== '') {
            $query->andWhere(['like', 'phone', $phone]);
        }
        if ($status !== null && $status !== '') {
            $query->andWhere(['status' => $status]);
        }

        $rows = $query->all();
        if ($rows === false) {
            return [];
        }
        return $rows;
    }
}
