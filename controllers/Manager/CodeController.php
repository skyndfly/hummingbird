<?php

namespace app\controllers\Manager;

use app\controllers\Manager\abstracts\BaseManagerController;
use app\filters\Code\CodeFilter;
use app\forms\Code\CreateCodeForm;
use app\forms\Code\EditCodeForm;
use app\forms\Code\IssuedCodeForm;
use app\repositories\Category\CategoryRepository;
use app\repositories\Category\dto\CategoryDto;
use app\repositories\Code\CodeRepository;
use app\repositories\Code\dto\CodeDto;
use app\repositories\Code\enums\CodeStatusEnum;
use app\repositories\Company\CompanyRepository;
use app\services\Code\CreateCodeService;
use app\services\Code\dto\IssuedCodeDto;
use app\services\Code\IssuedCodeService;
use app\services\Company\dto\CompanyDto;
use app\ui\gridTable\Code\AllCodeGridTable;
use app\ui\gridTable\GridFactory;
use Exception;
use Throwable;
use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\UrlManager;

class CodeController extends BaseManagerController
{

    public function __construct(
        $id,
        $module,
        private readonly CreateCodeService $createCodeService,
        private readonly CategoryRepository $categoryRepository,
        private readonly CompanyRepository $companyRepository,
        private readonly IssuedCodeService $issuedCodeService,
        private readonly CodeRepository $repository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * Вывод страницы добавления
     */
    public function actionCreate(): string
    {
        try {

            $formModel = new CreateCodeForm();
            $getParams = Yii::$app->request->getQueryParams();
            $filter = new CodeFilter();
            $filter->load($getParams);

            $codes = $this->repository->getAll($filter);
            $grid = GridFactory::createGrid(
                models: $codes,
                gridClass: AllCodeGridTable::class,
                pageSize: 50
            );
            return $this->render(view: 'code/create', params: [
                'formModel' => $formModel,
                'grid' => $grid,
                'categories' => $this->categoryRepository->getAllAsMap(),
                'filterModel' => $filter,
                'companies' => $this->companyRepository->getAllAsMap()
            ]);

        } catch (Throwable $e) {
            Yii::error([
                'type' => 'CodeController',
                'exception' => $e,
            ]);
            return $this->renderError($e);
        }
    }

    /**
     * Смена статуса коду
     */
    public function actionIssued(): Response
    {
        try {
            $modelForm = new IssuedCodeForm();
            $post = Yii::$app->request->post();
            if ($modelForm->load($post) && $modelForm->validate()) {
                $dto = new IssuedCodeDto(
                    ids: $modelForm->id,
                    status: CodeStatusEnum::from($modelForm->status)
                );
                $this->issuedCodeService->execute($dto);
                Yii::$app->session->setFlash('success', 'Код выдан');
                //TODO добавить логи кто выдал заказ
            }
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }

    /**
     * Сохранение в БД
     */
    public function actionStore(): Response
    {
        try {
            $modelForm = new CreateCodeForm();
            $post = Yii::$app->request->post();
            if ($modelForm->load($post) && $modelForm->validate()) {

                $this->createCodeService->execute($modelForm, $this->getIdentity()->getId());
                Yii::$app->session->setFlash('success', 'Код добавлен');
            }
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }

    public function actionChangeStatus(): Response
    {
        try {
            $modelForm = new IssuedCodeForm();
            $post = Yii::$app->request->post();

            if ($modelForm->load($post) && $modelForm->validate()) {
                $dto = new IssuedCodeDto(
                    ids: $modelForm->id,
                    status: CodeStatusEnum::from($modelForm->status)
                );
                $this->repository->changeStatus(
                    status: $dto->status,
                    id: $dto->ids
                );
                Yii::$app->session->setFlash('success', 'Статус изменен');
                //TODO добавить логи кто выдал заказ
            }
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }

    public function actionEdit(int $codeId): Response|string
    {
        try {
            $formModel = new EditCodeForm();
            $dto = $this->repository->getById($codeId);

            $formModel->setAttributes($dto->toArray());

            return $this->render(view: 'code/edit', params: [
                'formModel' => $formModel,
                'companies' => $this->companyRepository->getAllAsMap(),
                'categories' => $this->categoryRepository->getAllAsMap(),
            ]);

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->getReferrer());
        }

    }

    public function actionUpdate(): Response
    {
        try {
            $data = Yii::$app->getRequest()->getBodyParams();

            $form = new EditCodeForm();
            if ($form->load($data) && $form->validate()) {
                $old = $this->repository->getById((int) $form->id);
                $dto = new CodeDto(
                    code: $form->code,
                    id: $old->id,
                    price: $form->price,
                    userId: $old->userId,
                    quantity: $form->quantity,
                    category: new CategoryDto(id: $form->categoryId, name: $old->category->name),
                    company: new CompanyDto(id: $form->companyId, name: $old->company->name),
                    comment: $form->comment,
                    status: $old->status,

                );
                $this->repository->update($dto);
                Yii::$app->session->setFlash('success', 'Код успешно обновлен!');
                return $this->redirect(Url::to('/code/create?CodeFilter[code]=' . $form->code));
            }

            Yii::$app->session->setFlash('error', array_values($form->getErrors())[0]);
            return $this->redirect(Yii::$app->request->getReferrer());

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Url::to('/code/create'));
        }
    }


}