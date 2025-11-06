<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%commision_config}}`.
 */
class m251106_152325_create_commision_config_table extends Migration
{
    public const string TABLE_NAME = 'commision_config';

    public function safeUp()
    {
        $this->createTable(
            table: self::TABLE_NAME,
            columns: [
                'id' => $this->primaryKey(),
                'strategy' => $this->string()->notNull(),
                'from_amount' => $this->integer()->null(),
                'to_amount' => $this->integer()->null(),
                'type' => $this->string()->notNull(),
                'value' => $this->integer()->null(),
            ]);
    }


    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
