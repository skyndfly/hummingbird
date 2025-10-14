<?php

use yii\db\Migration;

class m251012_060830_create_codes_table extends Migration
{
    public const TABLE_NAME = 'code';

    public function safeUp()
    {
        $this->createTable(
            table: self::TABLE_NAME,
            columns: [
                'id' => $this->primaryKey(),
                'code' => $this->string()->notNull(),
                'user_id' => $this->integer()->notNull(),
                'status' => $this->string()->notNull(),
                'price' => $this->integer()->notNull(),
                'comment' => $this->string()->null(),
                'quantity' => $this->bigInteger()->notNull(),
                'created_at' => $this->timestamp()->notNull(),
                'updated_at' => $this->timestamp()->null(),
                'deleted_at' => $this->timestamp()->null(),
            ]);

        $this->addForeignKey(
            name: sprintf('%s-%s-%s', 'fk', self::TABLE_NAME, 'user_id'),
            table: self::TABLE_NAME,
            columns: 'user_id',
            refTable: 'users',
            refColumns: 'id',
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            name: sprintf('%s-%s-%s', 'fk', self::TABLE_NAME, 'user_id'),
            table: self::TABLE_NAME
            );
        $this->dropTable(self::TABLE_NAME);
    }
}
