<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sale}}`.
 */
class m251218_050840_create_sale_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sale}}', [
            'id' => $this->primaryKey(),
            'amount' => $this->integer()->notNull()->defaultValue(0),
            'code' => $this->string()->notNull(),
            'item_ids' => $this->json()->notNull(),
            'quantity' => $this->integer()->notNull()->defaultValue(0),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp(),
            'update_at' => $this->timestamp(),
        ]);
        $this->addForeignKey(
            name: sprintf('%s-%s-%s', 'fk', 'sale', 'user_id' ),
            table: 'sale',
            columns: 'user_id',
            refTable: 'users',
            refColumns: 'id'
        );

        $this->createIndex(
            name: sprintf('%s-%s-%s', 'idx', 'sale', 'user_id' ),
            table: 'sale',
            columns: 'user_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            name: sprintf('%s-%s-%s', 'fk', 'sale', 'user_id' ),
            table: 'sale',
        );
        $this->dropIndex(
            name: sprintf('%s-%s-%s', 'idx', 'sale', 'user_id' ),
            table: 'sale'
        );
        $this->dropTable('{{%sale}}');
    }
}
