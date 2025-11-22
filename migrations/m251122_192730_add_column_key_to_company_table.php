<?php

use yii\db\Migration;

class m251122_192730_add_column_key_to_company_table extends Migration
{
    public const string TABLE_NAME = 'company';
    public function safeUp()
    {
        $this->addColumn(
            table: self::TABLE_NAME,
            column: 'key',
            type: $this->string()->null()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE_NAME, 'key');
    }

}
