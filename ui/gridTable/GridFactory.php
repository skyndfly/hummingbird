<?php

namespace app\ui\gridTable;

use yii\data\ArrayDataProvider;
use yii\grid\GridView;

class GridFactory
{
    /**
     * @param class-string<AbstractGridTable> $gridClass
     */
    public static function createGrid(
        array $models,
        string $gridClass,
        int $pageSize = 20
    ): string {


        $dataProvider = new ArrayDataProvider([
            'allModels' => $models,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'attributes' => $gridClass::getSortAttributes(),
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        return GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridClass::getColumns(),
        ]);
    }
}