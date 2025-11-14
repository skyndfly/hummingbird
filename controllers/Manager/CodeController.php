<?php

namespace app\controllers\Manager;

use app\controllers\Manager\abstracts\BaseManagerController;
use app\filters\Code\CodeFilter;
use app\forms\Code\CreateCodeForm;
use app\forms\Code\IssuedCodeForm;
use app\repositories\Category\CategoryRepository;
use app\repositories\Code\CodeRepository;
use app\repositories\Code\enums\CodeStatusEnum;
use app\repositories\Company\CompanyRepository;
use app\services\Code\CreateCodeService;
use app\ui\gridTable\Code\AllCodeGridTable;
use app\ui\gridTable\GridFactory;
use Exception;
use Throwable;
use Yii;
use yii\web\Response;

class CodeController extends BaseManagerController
{

    public function __construct(
        $id,
        $module,
        private readonly CreateCodeService $createCodeService,
        private readonly CodeRepository $repository,
        private readonly CategoryRepository $categoryRepository,
        private readonly CompanyRepository $companyRepository,
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
                $this->repository->issuedCode(
                    status: CodeStatusEnum::from($modelForm->status),
                    id: $modelForm->id,
                );
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


}