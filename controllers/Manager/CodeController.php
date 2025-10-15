<?php

namespace app\controllers\Manager;

use app\controllers\Manager\abstracts\BaseManagerController;
use app\forms\Code\CreateCodeForm;
use app\forms\Code\IssuedCodeForm;
use app\repositories\Code\CodeRepository;
use app\repositories\Code\enums\CodeStatusEnum;
use app\services\Code\CreateCodeService;
use app\ui\gridTable\Code\AllCodeGridTable;
use app\ui\gridTable\GridFactory;
use Exception;
use Yii;
use yii\web\Response;

class CodeController extends BaseManagerController
{
    private CreateCodeService $createCodeService;
    private CodeRepository $repository;

    public function __construct(
        $id,
        $module,
        CreateCodeService $createCodeService,
        CodeRepository $repository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);

        $this->createCodeService = $createCodeService;
        $this->repository = $repository;
    }


    public function actionCreate(): string
    {
        $formModel = new CreateCodeForm();
        return $this->render('create', [
            'formModel' => $formModel,
        ]);
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
    {        try {
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