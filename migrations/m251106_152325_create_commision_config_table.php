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
        $this->batchInsert(self::TABLE_NAME,
            ['id', 'strategy', 'from_amount', 'to_amount', 'type', 'value'],
            [
                [1, 'fixed_pochta', null, null, 'per_unit', 15000],
                [2, 'fixed_sdek', null, null, 'per_unit', 25000],
                [3, 'fixed_fivepost', null, null, 'per_unit', 15000],
                [4, 'marketplace', 0, 100000, 'fixed', 5000],
                [5, 'marketplace', 100100, 400000, 'fixed', 10000],
                [6, 'marketplace', 400100, 1000000, 'percent', 4],
                [7, 'marketplace', 1000100, null, 'percent', 3],
            ]
        );
    }


    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
