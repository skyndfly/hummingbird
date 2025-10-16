<?php

namespace app\ui\gridTable\Code;

use app\auth\enums\UserTypeEnum;
use app\auth\UserIdentity;
use app\repositories\Code\dto\CodeDto;
use app\repositories\Code\enums\CodeStatusEnum;
use app\ui\gridTable\AbstractGridTable;
use app\ui\gridTable\GridColumn;
use DateMalformedStringException;
use DateTimeImmutable;
use Throwable;
use Yii;

class CodeGridTableForCreatePage extends AbstractGridTable
{
    #[GridColumn(label: 'Код заказа')]
    public string $code;
    #[GridColumn(label: 'Дата прихода', formatter: 'dateFormatter', sortable: true)]
    public string $createdAt;
    #[GridColumn(label: 'Количество')]
    public string $quantity;
    #[GridColumn(label: 'Место хранения', formatter: 'categoryFormatter')]
    public string $category;
    #[GridColumn(label: 'Цена', formatter: 'priceFormatter')]
    public string $price;
    #[GridColumn(label: 'Статус', formatter: 'statusFormatter')]
    public string $status;
    #[GridColumn(label: 'Комментарий', formatter: 'commentFormatter')]
    public string $comment;
    #[GridColumn(label: 'ID', formatter: 'idFormatter', sortable: true)]
    public string $id;
    #[GridColumn('Действия', formatter: 'actionButtons')]
    public string $actions;

    public static function idFormatter(CodeDto $dto): string
    {
        return '#' . $dto->id;
    }

    public static function commentFormatter(CodeDto $dto): string
    {
        return $dto->comment ?? '';
    }

    public static function categoryFormatter(CodeDto $dto): string
    {
        return $dto->category->name;
    }

    public static function statusFormatter(CodeDto $dto)
    {
        return '<span class="badge ' . self::mapBadge($dto->status) . '">' . $dto->status->value . '</span>';
    }

    public static function priceFormatter(CodeDto $dto): string
    {
        return '<strong>' . ($dto->price / 100) . ' ₽</strong>';
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function dateFormatter(CodeDto $dto): string
    {
        return new DateTimeImmutable($dto->createdAt)->format('d.m.Y');
    }

    /**
     * @throws Throwable
     */
    public static function actionButtons(CodeDto $model): string
    {
        /** @var UserIdentity $identity */
        $identity = Yii::$app->user->getIdentity();
        if ($identity->user->type === UserTypeEnum::OWNER) {
            return <<<HTML
            <div class="dropdown">
                 <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                      <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                    </svg>
                 </button>
                 <ul class="dropdown-menu">
                     </li>
                     <li>
                        <a class="dropdown-item text-primary" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                              <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                            </svg>
                            Редактировать
                        </a>
                     </li>
                     <li>
                         <a class="dropdown-item text-danger" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                              <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                            </svg>
                            Удалить
                         </a>
                     </li>
                 </ul>
            </div>
            
        HTML;
        }
        return '';
    }

    private static function mapBadge(CodeStatusEnum $statusEnum): string
    {
        $badges = [
            CodeStatusEnum::NEW->name => 'text-bg-success',
            CodeStatusEnum::ISSUED->name => 'text-bg-primary',
            CodeStatusEnum::ISSUED_FREE->name => 'text-bg-secondary',
            CodeStatusEnum::LOST->name => 'text-bg-danger',
        ];
        return $badges[$statusEnum->name] ?? '';
    }
}