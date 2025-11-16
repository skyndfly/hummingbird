<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%code}}`.
 */
class m251114_201741_add_company_id_column_to_code_table extends Migration
{
    public const string TABLE = 'code';
    public function safeUp()
    {
        $this->addColumn(
            table: self::TABLE,
            column: 'company_id',
            type: $this->integer()->notNull()
        );

        $this->addForeignKey(
            name: sprintf('%s-%s-%s', 'fk', self::TABLE, 'company_id' ),
            table: self::TABLE,
            columns: 'company_id',
            refTable: 'company',
            refColumns: 'id',
            delete: 'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            name: sprintf('%s-%s-%s', 'fk', self::TABLE, 'company_id' ),
            table: self::TABLE,
        );
        $this->dropColumn(
            table: self::TABLE,
            column: 'company_id',
        );
    }
}
