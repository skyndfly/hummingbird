<?php

use yii\db\Migration;
use yii\db\Query;

class m260306_000001_create_address_table_and_link_uploaded_code extends Migration
{
    private const string ADDRESS_TABLE = 'address';
    private const string COMPANY_TABLE = 'company';
    private const string UPLOADED_CODE_TABLE = 'uploaded_code';
    private const string FK_ADDRESS_COMPANY = 'fk-address-company_id';
    private const string IDX_ADDRESS_COMPANY = 'idx-address-company_id';
    private const string UIDX_ADDRESS_COMPANY_ADDRESS = 'uidx-address-company_id_address';
    private const string FK_UPLOADED_CODE_ADDRESS = 'fk-uploaded_code-address_id';
    private const string IDX_UPLOADED_CODE_ADDRESS = 'idx-uploaded_code-address_id';

    public function safeUp()
    {
        if (!$this->tableExists(self::ADDRESS_TABLE)) {
            $this->createTable(self::ADDRESS_TABLE, [
                'id' => $this->primaryKey(),
                'company_id' => $this->integer()->notNull(),
                'address' => $this->string()->notNull(),
                'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
        }

        if (!$this->fkExists(self::FK_ADDRESS_COMPANY)) {
            $this->addForeignKey(
                name: self::FK_ADDRESS_COMPANY,
                table: self::ADDRESS_TABLE,
                columns: 'company_id',
                refTable: self::COMPANY_TABLE,
                refColumns: 'id'
            );
        }

        if (!$this->indexExists(self::ADDRESS_TABLE, self::IDX_ADDRESS_COMPANY)) {
            $this->createIndex(
                name: self::IDX_ADDRESS_COMPANY,
                table: self::ADDRESS_TABLE,
                columns: ['company_id']
            );
        }

        if (!$this->indexExists(self::ADDRESS_TABLE, self::UIDX_ADDRESS_COMPANY_ADDRESS)) {
            $this->createIndex(
                name: self::UIDX_ADDRESS_COMPANY_ADDRESS,
                table: self::ADDRESS_TABLE,
                columns: ['company_id', 'address'],
                unique: true
            );
        }

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

        if ($this->tableExists(self::ADDRESS_TABLE)) {
            $this->truncateTable(self::ADDRESS_TABLE);
        }

        $this->execute(
            "SELECT setval(pg_get_serial_sequence('" . self::COMPANY_TABLE . "', 'id'), COALESCE((SELECT MAX(id) FROM " . self::COMPANY_TABLE . "), 0))"
        );

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
            } else {
                $this->update(
                    self::COMPANY_TABLE,
                    [
                        'name' => $data['name'],
                        'commission_strategy' => $data['commission'],
                    ],
                    ['id' => $companyId]
                );
            }

            foreach ($addresses[$botKey] ?? [] as $address) {
                $this->insert(self::ADDRESS_TABLE, [
                    'company_id' => $companyId,
                    'address' => $address,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        if (!$this->columnExists(self::UPLOADED_CODE_TABLE, 'address_id')) {
            $this->addColumn(
                table: self::UPLOADED_CODE_TABLE,
                column: 'address_id',
                type: $this->integer()->null()
            );
        }

        if (!$this->fkExists(self::FK_UPLOADED_CODE_ADDRESS)) {
            $this->addForeignKey(
                name: self::FK_UPLOADED_CODE_ADDRESS,
                table: self::UPLOADED_CODE_TABLE,
                columns: 'address_id',
                refTable: self::ADDRESS_TABLE,
                refColumns: 'id'
            );
        }

        if (!$this->indexExists(self::UPLOADED_CODE_TABLE, self::IDX_UPLOADED_CODE_ADDRESS)) {
            $this->createIndex(
                name: self::IDX_UPLOADED_CODE_ADDRESS,
                table: self::UPLOADED_CODE_TABLE,
                columns: ['address_id']
            );
        }
    }

    public function safeDown()
    {
        if ($this->indexExists(self::UPLOADED_CODE_TABLE, self::IDX_UPLOADED_CODE_ADDRESS)) {
            $this->dropIndex(self::IDX_UPLOADED_CODE_ADDRESS, self::UPLOADED_CODE_TABLE);
        }

        if ($this->fkExists(self::FK_UPLOADED_CODE_ADDRESS)) {
            $this->dropForeignKey(self::FK_UPLOADED_CODE_ADDRESS, self::UPLOADED_CODE_TABLE);
        }

        if ($this->columnExists(self::UPLOADED_CODE_TABLE, 'address_id')) {
            $this->dropColumn(self::UPLOADED_CODE_TABLE, 'address_id');
        }

        if ($this->indexExists(self::ADDRESS_TABLE, self::UIDX_ADDRESS_COMPANY_ADDRESS)) {
            $this->dropIndex(self::UIDX_ADDRESS_COMPANY_ADDRESS, self::ADDRESS_TABLE);
        }

        if ($this->indexExists(self::ADDRESS_TABLE, self::IDX_ADDRESS_COMPANY)) {
            $this->dropIndex(self::IDX_ADDRESS_COMPANY, self::ADDRESS_TABLE);
        }

        if ($this->fkExists(self::FK_ADDRESS_COMPANY)) {
            $this->dropForeignKey(self::FK_ADDRESS_COMPANY, self::ADDRESS_TABLE);
        }

        if ($this->tableExists(self::ADDRESS_TABLE)) {
            $this->dropTable(self::ADDRESS_TABLE);
        }
    }

    private function tableExists(string $table): bool
    {
        return $this->db->schema->getTableSchema($table, true) !== null;
    }

    private function columnExists(string $table, string $column): bool
    {
        $schema = $this->db->schema->getTableSchema($table, true);
        if ($schema === null) {
            return false;
        }
        return isset($schema->columns[$column]);
    }

    private function indexExists(string $table, string $index): bool
    {
        $result = (new Query())
            ->from('pg_indexes')
            ->where(['tablename' => $table, 'indexname' => $index])
            ->exists();
        return $result;
    }

    private function fkExists(string $fkName): bool
    {
        $result = (new Query())
            ->from('pg_constraint')
            ->where(['conname' => $fkName])
            ->exists();
        return $result;
    }
}
