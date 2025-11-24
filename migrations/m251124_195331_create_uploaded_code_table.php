<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%uploaded_code}}`.
 */
class m251124_195331_create_uploaded_code_table extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            '{{%uploaded_code}}', [
            'id' => $this->primaryKey(),
            'company_key' => $this->string()->notNull(),
            'file_name' => $this->string()->notNull()->unique(),
            'status' => $this->string()->notNull(),
            'chat_id' => $this->string()->null(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%uploaded_code}}');
    }
}
