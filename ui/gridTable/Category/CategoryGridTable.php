<?php

namespace app\ui\gridTable\Category;

use app\repositories\Category\dto\CategoryDto;
use app\ui\gridTable\AbstractGridTable;
use app\ui\gridTable\GridColumn;

class CategoryGridTable extends AbstractGridTable
{
    #[GridColumn(label: 'ID', formatter: 'idFormatter', sortable: true)]
    public string $id;
    #[GridColumn(label: 'Название места')]
    public string $name;

    #[GridColumn('Действия', formatter: 'actionButtons')]
    public string $actions;

    public static function idFormatter(CategoryDto $dto): string
    {
        return '#' . $dto->id;
    }

    public static function actionButtons(CategoryDto $dto): string
    {
        return <<<HTML
            <a href="/category/$dto->id/edit" class="text-decoration-none">✏️</a>
        HTML;

    }
}