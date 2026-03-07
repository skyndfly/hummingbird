<?php

namespace app\ui\gridTable\Address;

use app\services\Address\dto\AddressDto;
use app\ui\gridTable\AbstractGridTable;
use app\ui\gridTable\GridColumn;

class AddressGridTable extends AbstractGridTable
{
    #[GridColumn(label: 'ID', formatter: 'idFormatter', sortable: true)]
    public string $id;

    #[GridColumn(label: 'Компания')]
    public string $companyName;

    #[GridColumn(label: 'Адрес')]
    public string $address;

    #[GridColumn(label: 'Статус', formatter: 'statusFormatter')]
    public string $status;

    #[GridColumn('Действия', formatter: 'actionButtons')]
    public string $actions;

    public static function idFormatter(AddressDto $dto): string
    {
        return '#' . $dto->id;
    }

    public static function statusFormatter(AddressDto $dto): string
    {
        return $dto->deletedAt === null
            ? '<span class="badge text-bg-success">Активен</span>'
            : '<span class="badge text-bg-secondary">Удален</span>';
    }

    public static function actionButtons(AddressDto $dto): string
    {
        $edit = '<a href="/owner-address/' . $dto->id . '/edit" class="text-decoration-none">✏️</a>';
        if ($dto->deletedAt === null) {
            $delete = '<form method="post" action="/owner-address/delete" class="d-inline ms-2">'
                . '<input type="hidden" name="_csrf" value="' . \Yii::$app->request->csrfToken . '">'
                . '<input type="hidden" name="addressId" value="' . $dto->id . '">'
                . '<button type="submit" class="btn btn-link p-0 text-decoration-none">🗑️</button>'
                . '</form>';
            return $edit . $delete;
        }
        $restore = '<form method="post" action="/owner-address/restore" class="d-inline ms-2">'
            . '<input type="hidden" name="_csrf" value="' . \Yii::$app->request->csrfToken . '">'
            . '<input type="hidden" name="addressId" value="' . $dto->id . '">'
            . '<button type="submit" class="btn btn-link p-0 text-decoration-none">↩️</button>'
            . '</form>';
        return $edit . $restore;
    }
}
