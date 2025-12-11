<?php

namespace app\ui\gridTable\UploadedCode;

use app\services\Company\dto\CompanyDto;
use app\services\UploadCode\dto\UploadedCodeDto;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use app\ui\gridTable\AbstractGridTable;
use app\ui\gridTable\GridColumn;
use DateTimeImmutable;

class UploadedCodeGridTable extends AbstractGridTable
{
    #[GridColumn(label: 'ID', formatter: 'idFormatter', sortable: true)]
    public string $id;
    #[GridColumn(label: 'Статус', formatter: 'statusFormater')]
    public string $status;

    #[GridColumn(label: 'Дата загрузки', formatter: 'dateFormater')]
    public string $createdAt;

    #[GridColumn(label: 'Служба доставки')]
    public string $companyKey;

    #[GridColumn(label: 'ТГ чат id')]
    public string $chatId;
    #[GridColumn(label: 'Примечание')]
    public string $note;


    public static function idFormatter(UploadedCodeDto $dto): string
    {
        return '#' . $dto->id;
    }
    public static function statusFormater(UploadedCodeDto $dto): string
    {
        return match ($dto->status) {
            UploadedCodeStatusEnum::AWAIT => '<span class="badge text-bg-success">'.$dto->status->label().'</span>',
            UploadedCodeStatusEnum::NOT_PAID => '<span class="badge text-bg-warning">'.$dto->status->label().'</span>',
            UploadedCodeStatusEnum::OUTDATED => '<span class="badge text-bg-danger">'.$dto->status->label().'</span>',
            UploadedCodeStatusEnum::ISSUED => '<span class="badge text-bg-primary">'.$dto->status->label().'</span>',
            UploadedCodeStatusEnum::PENDING => '<span class="badge text-bg-dark">'.$dto->status->label().'</span>',
        };
    }

    public static function dateFormater(UploadedCodeDto $dto): string
    {
        return new DateTimeImmutable($dto->createdAt)->format('d-m-Y H:i:s');
    }

}