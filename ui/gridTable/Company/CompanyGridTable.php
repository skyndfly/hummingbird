<?php

namespace app\ui\gridTable\Company;

use app\services\Company\dto\CompanyDto;
use app\ui\gridTable\AbstractGridTable;
use app\ui\gridTable\GridColumn;

class CompanyGridTable extends AbstractGridTable
{
    #[GridColumn(label: 'ID', formatter: 'idFormatter', sortable: true)]
    public string $id;
    #[GridColumn(label: 'Служба Доставки')]
    public string $name;

    #[GridColumn(label: 'Тип комиссии')]
    public string $commissionStrategy;

    #[GridColumn(label: 'Ключ для бота')]
    public string $botKey;

    #[GridColumn('Действия', formatter: 'actionButtons')]
    public string $actions;

    public static function idFormatter(CompanyDto $dto): string
    {
        return '#' . $dto->id;
    }

    public static function actionButtons(CompanyDto $dto): string
    {
        return <<<HTML
            <a href="/company/$dto->id/edit" class="text-decoration-none">✏️</a>
        HTML;

    }
}