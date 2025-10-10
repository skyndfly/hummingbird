<?php

use yii\db\Migration;

class m250828_121616_create_users_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
            'first_name' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'birth_day' => $this->date()->notNull(),
            'number_phone' => $this->string()->notNull(),
            'access_token' => $this->string()->null(),
            'auth_key' => $this->string()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
