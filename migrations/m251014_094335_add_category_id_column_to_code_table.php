<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%code}}`.
 */
class m251014_094335_add_category_id_column_to_code_table extends Migration
{
    public const TABLE_NAME = 'code';
    public function safeUp()
    {
        $this->addColumn(
            table: self::TABLE_NAME,
            column: 'category_id',
            type: $this->bigInteger()->unsigned()->notNull(),
        );
        $this->addForeignKey(
            name: sprintf('%s-%s-%s', 'fk', self::TABLE_NAME, 'category_id'),
            table: self::TABLE_NAME,
            columns: 'category_id',
            refTable: 'category',
            refColumns: 'id',
            delete: 'RESTRICT'
        );
    }


    public function safeDown()
    {
        $this->dropForeignKey(
            name: sprintf('%s-%s-%s', 'fk', self::TABLE_NAME, 'category_id'),
            table: self::TABLE_NAME,
        );
        $this->dropColumn(
            table: self::TABLE_NAME,
            column: 'category_id',
        );
    }
}
