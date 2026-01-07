<?php

use yii\db\Migration;

class m251122_192730_add_column_key_to_company_table extends Migration
{
    public const string TABLE_NAME = 'company';

    public function safeUp()
    {
        $this->addColumn(
            table: self::TABLE_NAME,
            column: 'bot_key',
            type: $this->string()->null()
        );
        $this->batchInsert(
            table: self::TABLE_NAME,
            columns: ['id', 'name', 'commission_strategy', 'bot_key'],
            rows: [
                [1, 'Wildberries', 'marketplace', 'wb'],
                [2, 'Ozon', 'marketplace', 'ozon'],
                [3, 'Яндекс маркет', 'marketplace', 'market'],
                [4, 'Почта России', 'fixed_pochta', 'post'],
                [5, 'СДЭК', 'fixed_sdek', 'sdek'],
                [6, 'Аптека.ру', 'marketplace', 'apteka'],
                [7, 'DNS', 'marketplace', 'dns'],
                [8, 'SUNLIGHT', 'marketplace', 'sunlight'],
                [9, 'Все инструменты', 'marketplace', 'vsei'],
                [10, '5post', 'fixed_fivepost', '5post'],
                [11, 'Авито', 'marketplace', 'avito'],
                [12, 'ПЭК', 'marketplace', 'pek'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE_NAME, 'bot_key');
    }

}
