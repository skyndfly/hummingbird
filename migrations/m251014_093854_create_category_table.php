<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category}}`.
 */
class m251014_093854_create_category_table extends Migration
{
    public const TABLE_NAME = 'category';

    public function safeUp()
    {
        $this->createTable(
            table: self::TABLE_NAME,
            columns: [
                'id' => $this->primaryKey(),
                'name' => $this->string()->unique()->notNull(),
            ]);
        $this->createIndex(
            name: sprintf('%s-%s-%s', 'idx', self::TABLE_NAME, 'name'),
            table: self::TABLE_NAME,
            columns: ['name'],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            name: sprintf('%s-%s-%s', 'idx', self::TABLE_NAME, 'name'),
            table: self::TABLE_NAME,
        );
        $this->dropTable(self::TABLE_NAME);
    }
}
