<?php

use yii\db\Migration;

class m260310_000001_make_return_request_photo_two_nullable extends Migration
{
    private const string TABLE = 'return_request';

    public function safeUp(): void
    {
        $this->alterColumn(self::TABLE, 'photo_two', $this->string()->null());
    }

    public function safeDown(): void
    {
        $this->update(self::TABLE, ['photo_two' => ''], ['photo_two' => null]);
        $this->alterColumn(self::TABLE, 'photo_two', $this->string()->notNull());
    }
}
