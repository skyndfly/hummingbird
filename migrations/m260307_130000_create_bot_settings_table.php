<?php

use yii\db\Migration;

class m260307_130000_create_bot_settings_table extends Migration
{
    private const string TABLE = 'bot_settings';

    public function safeUp()
    {
        if ($this->db->schema->getTableSchema(self::TABLE, true) === null) {
            $this->createTable(self::TABLE, [
                'id' => $this->primaryKey(),
                'cutoff_hour' => $this->integer()->notNull()->defaultValue(16),
                'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
            $this->insert(self::TABLE, [
                'cutoff_hour' => 16,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function safeDown()
    {
        if ($this->db->schema->getTableSchema(self::TABLE, true) !== null) {
            $this->dropTable(self::TABLE);
        }
    }
}
