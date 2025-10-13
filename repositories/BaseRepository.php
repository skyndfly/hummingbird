<?php

namespace app\repositories;

use DateTimeImmutable;
use Yii;
use yii\db\Command;
use yii\db\Query;

class BaseRepository
{
    protected function getQuery(): Query
    {
        return new Query();
    }

    protected function getCommand(): Command
    {
        return Yii::$app->db->createCommand();
    }
    protected function getCurrentDate(): string
    {
        return new DateTimeImmutable()->format('Y-m-d H:i:s');
    }
}