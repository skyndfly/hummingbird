<?php

use yii\db\Migration;
use yii\db\Query;

class m260310_000002_add_cancel_reason_to_return_request extends Migration
{
    private const string TABLE = 'return_request';

    public function safeUp(): void
    {
        if (!$this->columnExists(self::TABLE, 'cancel_reason')) {
            $this->addColumn(self::TABLE, 'cancel_reason', $this->text()->null());
        }
    }

    public function safeDown(): void
    {
        if ($this->columnExists(self::TABLE, 'cancel_reason')) {
            $this->dropColumn(self::TABLE, 'cancel_reason');
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
}
