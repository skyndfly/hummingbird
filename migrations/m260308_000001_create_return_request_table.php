<?php

use yii\db\Migration;
use yii\db\Query;

class m260308_000001_create_return_request_table extends Migration
{
    private const string TABLE = 'return_request';
    private const string IDX_NUMBER = 'idx-return_request-number';
    private const string IDX_PHONE = 'idx-return_request-phone';
    private const string IDX_STATUS = 'idx-return_request-status';
    private const string IDX_CREATED_BY = 'idx-return_request-created_by';

    public function safeUp(): void
    {
        if (!$this->tableExists(self::TABLE)) {
            $this->createTable(self::TABLE, [
                'id' => $this->primaryKey(),
                'number' => $this->string(32)->null(),
                'phone' => $this->string(32)->notNull(),
                'status' => $this->string(32)->notNull(),
                'photo_one' => $this->string()->notNull(),
                'photo_two' => $this->string()->notNull(),
                'created_by' => $this->integer()->notNull(),
                'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
        }

        if (!$this->indexExists(self::TABLE, self::IDX_NUMBER)) {
            $this->createIndex(self::IDX_NUMBER, self::TABLE, ['number'], true);
        }
        if (!$this->indexExists(self::TABLE, self::IDX_PHONE)) {
            $this->createIndex(self::IDX_PHONE, self::TABLE, ['phone']);
        }
        if (!$this->indexExists(self::TABLE, self::IDX_STATUS)) {
            $this->createIndex(self::IDX_STATUS, self::TABLE, ['status']);
        }
        if (!$this->indexExists(self::TABLE, self::IDX_CREATED_BY)) {
            $this->createIndex(self::IDX_CREATED_BY, self::TABLE, ['created_by']);
        }
    }

    public function safeDown(): void
    {
        if ($this->indexExists(self::TABLE, self::IDX_CREATED_BY)) {
            $this->dropIndex(self::IDX_CREATED_BY, self::TABLE);
        }
        if ($this->indexExists(self::TABLE, self::IDX_STATUS)) {
            $this->dropIndex(self::IDX_STATUS, self::TABLE);
        }
        if ($this->indexExists(self::TABLE, self::IDX_PHONE)) {
            $this->dropIndex(self::IDX_PHONE, self::TABLE);
        }
        if ($this->indexExists(self::TABLE, self::IDX_NUMBER)) {
            $this->dropIndex(self::IDX_NUMBER, self::TABLE);
        }

        if ($this->tableExists(self::TABLE)) {
            $this->dropTable(self::TABLE);
        }
    }

    private function tableExists(string $table): bool
    {
        return $this->db->schema->getTableSchema($table, true) !== null;
    }

    private function indexExists(string $table, string $index): bool
    {
        return (new Query())
            ->from('pg_indexes')
            ->where(['tablename' => $table, 'indexname' => $index])
            ->exists();
    }
}
