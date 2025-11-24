<?php

use yii\db\Migration;

class m251122_205734_add_unique_to_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('company', 'name', $this->string()->unique());
        $this->alterColumn('company', 'bot_key', $this->string()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('company', 'name', $this->string());
        $this->alterColumn('company', 'bot_key', $this->string());
    }

}
