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

    public static function idFormatter(CategoryDto $dto): string
    {
        return '#' . $dto->id;
    }
}