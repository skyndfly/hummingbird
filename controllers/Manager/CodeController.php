<?php

namespace app\controllers\Manager;

use app\controllers\Manager\abstracts\BaseManagerController;
use app\forms\Code\CreateCodeForm;
use app\forms\Code\IssuedCodeForm;
use app\repositories\Category\CategoryRepository;
use app\repositories\Code\CodeRepository;
use app\repositories\Code\enums\CodeStatusEnum;
use app\services\Code\CreateCodeService;
use app\ui\gridTable\Code\CodeGridTableForCreatePage;
use app\ui\gridTable\GridFactory;
use Exception;
use Throwable;
use Yii;
use yii\web\Response;

class CodeController extends BaseManagerController
{
    private CreateCodeService $createCodeService;
    private CodeRepository $repository;
    private CategoryRepository $categoryRepository;

    public function __construct(
        $id,
        $module,
        CreateCodeService $createCodeService,
        CodeRepository $repository,
        CategoryRepository $categoryRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);

        $this->createCodeService = $createCodeService;
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
    }


    public function actionCreate(): string
    {
        try {

            $formModel = new CreateCodeForm();

            $codes = $this->repository->getAll();
            $grid = GridFactory::createGrid(
                models: $codes,
                gridClass: CodeGridTableForCreatePage::class,
                pageSize: 50
            );
            return $this->render(view: 'code/create', params: [
                'formModel' => $formModel,
                'grid' => $grid,
                'categories' => $this->categoryRepository->getAllAsMap()
            ]);

        } catch (Throwable $e) {
            Yii::error([
                'type' => 'CodeController',
                'exception' => $e,
            ]);
            return $this->renderError($e);
        }
    }

    public function actionIssued(): Response
    {
        try {
            $modelForm = new IssuedCodeForm();
            $post = Yii::$app->request->post();
            if ($modelForm->load($post) && $modelForm->validate()) {
                $this->repository->issuedCode(
                    status: CodeStatusEnum::from($modelForm->status),
                    id: (int) $modelForm->id,
                    comment: $modelForm->comment
                );
                Yii::$app->session->setFlash('success', 'Код выдан');
                //TODO добавить логи кто выдал заказ
            }
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }

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