<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%company}}`.
 */
class m251106_151208_create_company_table extends Migration
{
    public const string TABLE_NAME = 'company';

    public function safeUp()
    {
        $this->createTable(
            table: self::TABLE_NAME,
            columns: [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull(),
                'commission_strategy' => $this->string()->notNull(),
            ]);
    }
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
