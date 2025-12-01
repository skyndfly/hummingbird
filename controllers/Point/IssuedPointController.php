<?php

namespace app\controllers\Point;

use app\controllers\Point\abstracts\BasePointController;
use app\forms\UploadedCode\IssuedCodeForm;
use app\repositories\UploadedCode\UploadedCodeRepository;
use app\services\UploadCode\enums\UploadedCodeCompanyKeyEnum;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use Exception;
use Yii;
use yii\web\Response;

class IssuedPointController extends BasePointController
{
    public function __construct(
        $id,
        $module,
        private UploadedCodeRepository $uploadedCodeRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionWb(): string
    {
        $code = $this->uploadedCodeRepository->findAwaitCodeToday(UploadedCodeCompanyKeyEnum::WB);
        $form = new IssuedCodeForm();
        if ($code !== null) {
            $form->setAttributes($code->toArray());
        }
        $pendingCount =  $this->uploadedCodeRepository->getPendingTodayCount(UploadedCodeCompanyKeyEnum::WB);
        return $this->render('issued-point/wb', [
            'code' => $code,
            'formModel' => $form,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function actionOzon(): string
    {
        $code = $this->uploadedCodeRepository->findAwaitCodeToday(UploadedCodeCompanyKeyEnum::OZON);
        $form = new IssuedCodeForm();
        if ($code !== null) {
            $form->setAttributes($code->toArray());
        }
        $pendingCount =  $this->uploadedCodeRepository->getPendingTodayCount(UploadedCodeCompanyKeyEnum::OZON);
        return $this->render('issued-point/ozon', [
            'code' => $code,
            'formModel' => $form,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function actionIssued(): Response
    {
        try {
            $post = Yii::$app->getRequest()->getBodyParams();
            $form = new IssuedCodeForm();
            if ($form->load($post) && $form->validate()) {
                $this->uploadedCodeRepository->issuedCode(
                    id: $form->id,
                    status: UploadedCodeStatusEnum::from($form->status),
                );
                Yii::$app->getSession()->setFlash('success', 'Данные сохранены');
                return $this->redirect(Yii::$app->request->referrer);
            }
            Yii::$app->getSession()->setFlash('error', array_values($form->getFirstErrors())[0]);
        } catch (Exception $exception) {
            Yii::$app->getSession()->setFlash('error', $exception->getMessage());

        }
        return $this->redirect(Yii::$app->request->referrer);
    }
    public function actionWbPending(): string
    {
        $code = $this->uploadedCodeRepository->findPendingCodeToday(UploadedCodeCompanyKeyEnum::WB);
        $form = new IssuedCodeForm();
        if ($code !== null) {
            $form->setAttributes($code->toArray());
        }
        $pendingCount =  $this->uploadedCodeRepository->getPendingTodayCount(UploadedCodeCompanyKeyEnum::WB);
        return $this->render('issued-point/pending', [
            'code' => $code,
            'formModel' => $form,
            'company' => UploadedCodeCompanyKeyEnum::WB,
            'pendingCount' => $pendingCount,
        ]);
    }
    public function actionOzonPending(): string
    {
        $code = $this->uploadedCodeRepository->findPendingCodeToday(UploadedCodeCompanyKeyEnum::OZON);
        $form = new IssuedCodeForm();
        if ($code !== null) {
            $form->setAttributes($code->toArray());
        }
        $pendingCount =  $this->uploadedCodeRepository->getPendingTodayCount(UploadedCodeCompanyKeyEnum::OZON);
        return $this->render('issued-point/pending', [
            'code' => $code,
            'formModel' => $form,
            'company' => UploadedCodeCompanyKeyEnum::OZON,
            'pendingCount' => $pendingCount,
        ]);
    }
}