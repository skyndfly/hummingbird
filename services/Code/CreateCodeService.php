<?php

namespace app\services\Code;

use app\forms\Code\CreateCodeForm;
use app\repositories\Code\CodeRepository;
use app\repositories\Code\enums\CodeStatusEnum;

class CreateCodeService
{
    private CodeRepository $repository;


    public function __construct(CodeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CreateCodeForm $modelForm, int $userId): void
    {
        /**
         * Проверяем был ли код в такой категории, если нет то создаем его иначе обновляем стоимость и количество
         */
        $code = $this->repository->findCodeByIdAndCategory($modelForm->code, $modelForm->categoryId);

        if ($code === null) {
            $this->repository->create(
                code: $modelForm->code,
                user_id: $userId,
                status: CodeStatusEnum::NEW,
                price: (int) $modelForm->price * 100,
                comment: $modelForm->comment,
                categoryId: (int) $modelForm->categoryId,
                quantity: $modelForm->quantity,
                companyId: $modelForm->companyId,
            );
            //TODO добавить логи кто добавил заказ
            return;
        }
        $code->price = ($modelForm->price * 100) + $code->price;
        $code->quantity = $modelForm->quantity + $code->quantity;
        if (!empty($modelForm->comment)){
            $code->comment = $modelForm->comment;
        }
        $this->repository->update($code);

    }
}