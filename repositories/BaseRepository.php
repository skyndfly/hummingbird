<?php

namespace app\repositories;

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
}