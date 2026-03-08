<?php

use yii\db\Migration;
use yii\db\Query;

class m260308_000002_add_deleted_at_to_return_request extends Migration
{
    private const string TABLE = 'return_request';
    private const string IDX_DELETED_AT = 'idx-return_request-deleted_at';

    public function safeUp(): void
    {
        if (!$this->columnExists(self::TABLE, 'deleted_at')) {
            $this->addColumn(
                table: self::TABLE,
                column: 'deleted_at',
                type: $this->dateTime()->null()
            );
        }

        if (!$this->indexExists(self::TABLE, self::IDX_DELETED_AT)) {
            $this->createIndex(self::IDX_DELETED_AT, self::TABLE, ['deleted_at']);
        }
    }

    public function safeDown(): void
    {
        if ($this->indexExists(self::TABLE, self::IDX_DELETED_AT)) {
            $this->dropIndex(self::IDX_DELETED_AT, self::TABLE);
        }

        if ($this->columnExists(self::TABLE, 'deleted_at')) {
            $this->dropColumn(self::TABLE, 'deleted_at');
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        $schema = $this->db->schema->getTableSchema($table, true);
        if ($schema === null) {
            return false;
        }
        return isset($schema->columns[$column]);
    }

    private function indexExists(string $table, string $index): bool
    {
        return (new Query())
            ->from('pg_indexes')
            ->where(['tablename' => $table, 'indexname' => $index])
            ->exists();
    }
}
