<?php

use yii\db\Migration;

class m260307_140000_create_owner_messages_table extends Migration
{
    private const string TABLE = 'owner_messages';

    public function safeUp()
    {
        if ($this->db->schema->getTableSchema(self::TABLE, true) === null) {
            $this->createTable(self::TABLE, [
                'id' => $this->primaryKey(),
                'owner_user_id' => $this->integer()->null(),
                'chat_id' => $this->bigInteger()->notNull(),
                'text' => $this->text()->notNull(),
                'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
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
