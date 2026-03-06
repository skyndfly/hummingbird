<?php

use yii\db\Migration;
use yii\db\Query;

class m260306_000001_create_address_table_and_link_uploaded_code extends Migration
{
    private const string ADDRESS_TABLE = 'address';
    private const string COMPANY_TABLE = 'company';
    private const string UPLOADED_CODE_TABLE = 'uploaded_code';

    public function safeUp()
    {
        $this->createTable(self::ADDRESS_TABLE, [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull(),
            'address' => $this->string()->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            name: sprintf('%s-%s-%s', 'fk', self::ADDRESS_TABLE, 'company_id'),
            table: self::ADDRESS_TABLE,
            columns: 'company_id',
            refTable: self::COMPANY_TABLE,
            refColumns: 'id'
        );

        $this->createIndex(
            name: sprintf('%s-%s-%s', 'idx', self::ADDRESS_TABLE, 'company_id'),
            table: self::ADDRESS_TABLE,
            columns: ['company_id']
        );

        $this->createIndex(
            name: sprintf('%s-%s-%s', 'uidx', self::ADDRESS_TABLE, 'company_id_address'),
            table: self::ADDRESS_TABLE,
            columns: ['company_id', 'address'],
            unique: true
        );

        $companies = [
            'wb' => ['name' => 'Wildberries', 'commission' => 'marketplace'],
            'sdek' => ['name' => 'СДЭК', 'commission' => 'fixed_sdek'],
            'ozon' => ['name' => 'Ozon', 'commission' => 'marketplace'],
            'apteka' => ['name' => 'Аптека.ру', 'commission' => 'marketplace'],
            'dns' => ['name' => 'DNS', 'commission' => 'marketplace'],
            'sunlight' => ['name' => 'SUNLIGHT', 'commission' => 'marketplace'],
            'avito' => ['name' => 'Авито', 'commission' => 'marketplace'],
            '5post' => ['name' => '5post', 'commission' => 'fixed_fivepost'],
            'market' => ['name' => 'Яндекс Маркет', 'commission' => 'marketplace'],
            'post' => ['name' => 'ПОЧТА РОССИИ', 'commission' => 'fixed_pochta'],
            'vsei' => ['name' => 'Все инструменты', 'commission' => 'marketplace'],
            'dostavka' => ['name' => 'Яндекс доставка', 'commission' => 'marketplace'],
            'pek' => ['name' => 'ПЭК', 'commission' => 'marketplace'],
        ];

        $addresses = [
            'ozon' => [
                'Киевская 28Б',
                'Вернигоренко 39',
                'Молодогвардейцев 25',
                'Белорусская 10',
                'Рабоче - крестьянская 32',
            ],
            'wb' => [
                'Киевская 28Б',
                'Вернигоренко 39',
                'Молодогвардейцев 25',
                'Белорусская 10',
                'Рабоче - крестьянская 32',
            ],
            'sdek' => [
                'Харьковская 113',
            ],
            'apteka' => [
                'Молодогвардейцев 20',
            ],
            'dns' => [
                'Садовая, дом 32, корпус 3',
            ],
            'sunlight' => [
                'Базарная ул. 26',
            ],
            'avito' => [
                'Садовая 26',
            ],
            '5post' => [
                'ул. Молодогвардейцев 24Б',
            ],
            'market' => [
                'Киевская 28',
                'Молодогвардейцев 19',
            ],
            'post' => [
                'Рабоче - крестьянская 37',
            ],
            'vsei' => [
                'Отечественная ул., 44',
            ],
            'dostavka' => [
                'Киевская 28Б',
                'Молодогвардейцев 19',
            ],
            'pek' => [
                'ул. Куйбышева 2А',
            ],
        ];

        foreach ($companies as $botKey => $data) {
            $companyId = (new Query())
                ->from(self::COMPANY_TABLE)
                ->select('id')
                ->where(['bot_key' => $botKey])
                ->scalar();

            if ($companyId === false || $companyId === null) {
                $this->insert(self::COMPANY_TABLE, [
                    'name' => $data['name'],
                    'commission_strategy' => $data['commission'],
                    'bot_key' => $botKey,
                ]);
                $companyId = (int) $this->db->getLastInsertID();
            }

            foreach ($addresses[$botKey] ?? [] as $address) {
                $this->insert(self::ADDRESS_TABLE, [
                    'company_id' => $companyId,
                    'address' => $address,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $this->addColumn(
            table: self::UPLOADED_CODE_TABLE,
            column: 'address_id',
            type: $this->integer()->null()
        );

        $this->addForeignKey(
            name: sprintf('%s-%s-%s', 'fk', self::UPLOADED_CODE_TABLE, 'address_id'),
            table: self::UPLOADED_CODE_TABLE,
            columns: 'address_id',
            refTable: self::ADDRESS_TABLE,
            refColumns: 'id'
        );

        $this->createIndex(
            name: sprintf('%s-%s-%s', 'idx', self::UPLOADED_CODE_TABLE, 'address_id'),
            table: self::UPLOADED_CODE_TABLE,
            columns: ['address_id']
        );
    }

    public function safeDown()
    {
        $this->dropIndex(
            name: sprintf('%s-%s-%s', 'idx', self::UPLOADED_CODE_TABLE, 'address_id'),
            table: self::UPLOADED_CODE_TABLE
        );

        $this->dropForeignKey(
            name: sprintf('%s-%s-%s', 'fk', self::UPLOADED_CODE_TABLE, 'address_id'),
            table: self::UPLOADED_CODE_TABLE
        );

        $this->dropColumn(self::UPLOADED_CODE_TABLE, 'address_id');

        $this->dropIndex(
            name: sprintf('%s-%s-%s', 'uidx', self::ADDRESS_TABLE, 'company_id_address'),
            table: self::ADDRESS_TABLE
        );

        $this->dropIndex(
            name: sprintf('%s-%s-%s', 'idx', self::ADDRESS_TABLE, 'company_id'),
            table: self::ADDRESS_TABLE
        );

        $this->dropForeignKey(
            name: sprintf('%s-%s-%s', 'fk', self::ADDRESS_TABLE, 'company_id'),
            table: self::ADDRESS_TABLE
        );

        $this->dropTable(self::ADDRESS_TABLE);
    }
}
