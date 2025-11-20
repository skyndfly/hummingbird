<?php

namespace app\ui\gridTable\Code;

use app\auth\enums\UserTypeEnum;
use app\auth\UserIdentity;
use app\forms\Code\IssuedCodeForm;
use app\repositories\Code\dto\CodeDto;
use app\repositories\Code\enums\CodeStatusEnum;
use app\ui\gridTable\AbstractGridTable;
use app\ui\gridTable\GridColumn;
use DateMalformedStringException;
use DateTimeImmutable;
use Throwable;
use Yii;

class AllCodeGridTable extends AbstractGridTable
{
    #[GridColumn(label: 'Код заказа')]
    public string $code;
    #[GridColumn(label: 'Дата прихода', formatter: 'dateFormatter', sortable: true)]
    public string $createdAt;
    #[GridColumn(label: 'Количество')]
    public string $quantity;
    #[GridColumn(label: 'Служба доставки', formatter: 'companyFormatter')]
    public string $company;
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

    public static function companyFormatter(CodeDto $dto): string
    {
        return $dto->company->name;
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

    public static function actionButtons(CodeDto $model): string
    {
        /** @var UserIdentity $identity */
        $identity = Yii::$app->user->getIdentity();
        if ($identity->user->type !== UserTypeEnum::OWNER) {
            $formHtml = Yii::$app->view->render('@app/views/manager/code/_manager_lost_form', [
                'model' => $model,
                'formModel' => new IssuedCodeForm(),
            ]);
            $deleteButton = '';
        } else {
            $formHtml = Yii::$app->view->render('@app/views/manager/code/_owner_lost_form', [
                'model' => $model,
                'formModel' => new IssuedCodeForm(),
            ]);
            $deleteButton = <<<HTML
                <li>
                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#staticBackdrop-$model->id">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle text-danger" viewBox="0 0 16 16">
                                  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                                Удалить
                            </button>
                         </li>
            HTML;

        }

        return <<<HTML
                <div class="dropdown">
                     <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                          <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                        </svg>
                     </button>
                     <ul class="dropdown-menu">
                         <li>
                            <button type="button" class="dropdown-item text-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop-$model->id">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                  <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                </svg>
                                Изменить статус
                            </button>
                         </li>
                        {$deleteButton}
                     </ul>
                     <div class="modal fade" id="staticBackdrop-$model->id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel-$model->id">Выдать</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">    
                                {$formHtml}
                          </div>
                        </div>
                      </div>
                    </div>
                </div>    
            HTML;
    }

    public static function mapBadge(CodeStatusEnum $statusEnum): string
    {
        $badges = [
            CodeStatusEnum::NEW->name => 'text-bg-success',
            CodeStatusEnum::ISSUED->name => 'text-bg-primary',
            CodeStatusEnum::ISSUED_CARD->name => 'text-bg-dark',
            CodeStatusEnum::ISSUED_FREE->name => 'text-bg-secondary',
            CodeStatusEnum::LOST->name => 'text-bg-danger',
        ];
        return $badges[$statusEnum->name] ?? '';
    }
}