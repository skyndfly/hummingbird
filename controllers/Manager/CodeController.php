<?php

namespace app\controllers\Manager;

use app\controllers\Manager\abstracts\BaseManagerController;
use app\forms\Code\CreateCodeForm;
use app\repositories\Code\CodeRepository;
use app\repositories\Code\enums\CodeStatusEnum;
use app\ui\gridTable\Code\AllCodeGridTable;
use app\ui\gridTable\GridFactory;
use Exception;
use Yii;
use yii\web\Response;

class CodeController extends BaseManagerController
{
    private CodeRepository $repository;

    public function __construct($id, $module, CodeRepository $repository, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->repository = $repository;
    }


    public function actionCreate(): string
    {
        $formModel = new CreateCodeForm();
        return $this->render('create', [
            'formModel' => $formModel,
        ]);
    }

    public function actionStore(): Response
    {
        try {
            $modelForm = new CreateCodeForm();
            $post = Yii::$app->request->post();
            if ($modelForm->load($post) && $modelForm->validate()) {
                $this->repository->create(
                    code: (int) $modelForm->code,
                    user_id: $this->getIdentity()->getId(),
                    status: CodeStatusEnum::NEW,
                    price: (int) $modelForm->price * 100,
                    comment: $modelForm->comment,
                    place: $modelForm->place,
                );
                Yii::$app->session->setFlash('success', 'Код добавлен');
            }
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }


}