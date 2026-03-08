<?php

use yii\db\Migration;
use yii\db\Query;

class m260308_000004_add_return_type_to_return_request extends Migration
{
    private const string TABLE = 'return_request';
    private const string IDX_RETURN_TYPE = 'idx-return_request-return_type';

    public function safeUp(): void
    {
        if (!$this->columnExists(self::TABLE, 'return_type')) {
            $this->addColumn(
                table: self::TABLE,
                column: 'return_type',
                type: $this->string(16)->notNull()->defaultValue('wb')
            );
        }

        if (!$this->indexExists(self::TABLE, self::IDX_RETURN_TYPE)) {
            $this->createIndex(self::IDX_RETURN_TYPE, self::TABLE, ['return_type']);
        }
    }

    public function safeDown(): void
    {
        if ($this->indexExists(self::TABLE, self::IDX_RETURN_TYPE)) {
            $this->dropIndex(self::IDX_RETURN_TYPE, self::TABLE);
        }

        if ($this->columnExists(self::TABLE, 'return_type')) {
            $this->dropColumn(self::TABLE, 'return_type');
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
