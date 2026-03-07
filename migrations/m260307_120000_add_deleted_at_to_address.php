<?php

use yii\db\Migration;
use yii\db\Query;

class m260307_120000_add_deleted_at_to_address extends Migration
{
    private const string ADDRESS_TABLE = 'address';

    public function safeUp()
    {
        if (!$this->columnExists(self::ADDRESS_TABLE, 'deleted_at')) {
            $this->addColumn(self::ADDRESS_TABLE, 'deleted_at', $this->dateTime()->null());
        }
        if (!$this->indexExists(self::ADDRESS_TABLE, 'idx-address-deleted_at')) {
            $this->createIndex('idx-address-deleted_at', self::ADDRESS_TABLE, ['deleted_at']);
        }
    }

    public function safeDown()
    {
        if ($this->indexExists(self::ADDRESS_TABLE, 'idx-address-deleted_at')) {
            $this->dropIndex('idx-address-deleted_at', self::ADDRESS_TABLE);
        }
        if ($this->columnExists(self::ADDRESS_TABLE, 'deleted_at')) {
            $this->dropColumn(self::ADDRESS_TABLE, 'deleted_at');
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
        $result = (new Query())
            ->from('pg_indexes')
            ->where(['tablename' => $table, 'indexname' => $index])
            ->exists();
        return $result;
    }
}
