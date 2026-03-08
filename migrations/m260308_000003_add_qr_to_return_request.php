<?php

use yii\db\Migration;
use yii\db\Query;

class m260308_000003_add_qr_to_return_request extends Migration
{
    private const string TABLE = 'return_request';
    private const string IDX_STATUS = 'idx-return_request-status';

    public function safeUp(): void
    {
        if (!$this->columnExists(self::TABLE, 'qr_code_file')) {
            $this->addColumn(
                table: self::TABLE,
                column: 'qr_code_file',
                type: $this->string()->null()
            );
        }
    }

    public function safeDown(): void
    {
        if ($this->columnExists(self::TABLE, 'qr_code_file')) {
            $this->dropColumn(self::TABLE, 'qr_code_file');
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
