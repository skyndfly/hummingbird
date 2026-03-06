<?php

use yii\db\Migration;

class m260307_000002_reset_company_table extends Migration
{
    public const string TABLE_NAME = 'company';

    public function safeUp()
    {
        $this->truncateTable(self::TABLE_NAME);
        $this->execute(
            "SELECT setval(pg_get_serial_sequence('" . self::TABLE_NAME . "', 'id'), 0)"
        );

        $this->batchInsert(
            table: self::TABLE_NAME,
            columns: ['name', 'commission_strategy', 'bot_key'],
            rows: [
                ['Wildberries', 'marketplace', 'wb'],
                ['Ozon', 'marketplace', 'ozon'],
                ['Яндекс Маркет', 'marketplace', 'market'],
                ['ПОЧТА РОССИИ', 'fixed_pochta', 'post'],
                ['СДЭК', 'fixed_sdek', 'sdek'],
                ['Аптека.ру', 'marketplace', 'apteka'],
                ['DNS', 'marketplace', 'dns'],
                ['SUNLIGHT', 'marketplace', 'sunlight'],
                ['Все инструменты', 'marketplace', 'vsei'],
                ['5post', 'fixed_fivepost', '5post'],
                ['Авито', 'marketplace', 'avito'],
                ['ПЭК', 'marketplace', 'pek'],
                ['Яндекс доставка', 'marketplace', 'dostavka'],
            ]
        );
    }

    public function safeDown()
    {
        $this->truncateTable(self::TABLE_NAME);
        $this->execute(
            "SELECT setval(pg_get_serial_sequence('" . self::TABLE_NAME . "', 'id'), 0)"
        );
    }
}
