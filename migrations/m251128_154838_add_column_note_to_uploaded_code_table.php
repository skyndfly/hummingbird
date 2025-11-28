<?php

use yii\db\Migration;

class m251128_154838_add_column_note_to_uploaded_code_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('uploaded_code', 'note', $this->text()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('uploaded_code', 'note');
    }
}
