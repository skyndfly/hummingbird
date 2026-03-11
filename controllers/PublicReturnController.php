<?php

namespace app\controllers;

use app\forms\PublicReturnCheckForm;
use app\repositories\ReturnRequest\ReturnRequestRepository;
use app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum;
use app\services\Phone\PhoneNormalizer;
use app\services\ReturnRequest\ReturnRequestStoreService;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class PublicReturnController extends Controller
{
    public $layout = false;

    public function __construct(
        $id,
        $module,
        private ReturnRequestRepository $returnRequestRepository,
        private ReturnRequestStoreService $storeService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        $form = new PublicReturnCheckForm();
        $request = null;
        $showResult = false;

        $post = Yii::$app->request->post();
        if ($form->load($post)) {
            $showResult = true;
            $form->phone = PhoneNormalizer::normalize($form->phone);
            if ($form->validate()) {
            $id = $this->normalizeId($form->returnId);
            if ($id !== null) {
                $request = $this->returnRequestRepository->getByIdAndPhone($id, $form->phone);
            }
            }
        }

        if (!$showResult) {
            $returnId = Yii::$app->request->get('returnId');
            $phone = Yii::$app->request->get('phone');
            if (is_string($returnId) || is_string($phone)) {
                $showResult = true;
                $form->returnId = is_string($returnId) ? $returnId : '';
                $form->phone = is_string($phone) ? $phone : '';
                $form->phone = PhoneNormalizer::normalize($form->phone);
                if ($form->validate()) {
                    $id = $this->normalizeId($form->returnId);
                    if ($id !== null) {
                        $request = $this->returnRequestRepository->getByIdAndPhone($id, $form->phone);
                    }
                }
            }
        }

        return $this->render('index', [
            'formModel' => $form,
            'request' => $request,
            'showResult' => $showResult,
            'statusLabels' => [
                ReturnRequestStatusEnum::CREATED->value => ReturnRequestStatusEnum::CREATED->label(),
                ReturnRequestStatusEnum::ACCEPTED->value => ReturnRequestStatusEnum::ACCEPTED->label(),
                ReturnRequestStatusEnum::ROAD->value => ReturnRequestStatusEnum::ROAD->label(),
                ReturnRequestStatusEnum::DELIVERED->value => ReturnRequestStatusEnum::DELIVERED->label(),
                ReturnRequestStatusEnum::QR_UPLOADED->value => ReturnRequestStatusEnum::QR_UPLOADED->label(),
                ReturnRequestStatusEnum::COMPLETED->value => ReturnRequestStatusEnum::COMPLETED->label(),
                ReturnRequestStatusEnum::CANCELED->value => ReturnRequestStatusEnum::CANCELED->label(),
                ReturnRequestStatusEnum::RETURNING->value => ReturnRequestStatusEnum::RETURNING->label(),
                ReturnRequestStatusEnum::ACCEPTED_RETURN->value => ReturnRequestStatusEnum::ACCEPTED_RETURN->label(),
                ReturnRequestStatusEnum::RETURN_CLIENT->value => ReturnRequestStatusEnum::RETURN_CLIENT->label(),
            ],
        ]);
    }

    public function actionUpload(): Response
    {
        $returnId = Yii::$app->request->post('returnId');
        $phone = Yii::$app->request->post('phone');
        if (!is_string($returnId) || !is_string($phone)) {
            Yii::$app->session->setFlash('error', 'Неверные данные формы');
            return $this->redirect(['/public-return']);
        }
        $normalizedPhone = PhoneNormalizer::normalize($phone);
        $id = $this->normalizeId($returnId);
        if ($id === null || $normalizedPhone === '') {
            Yii::$app->session->setFlash('error', 'Неверные данные формы');
            return $this->redirect(['/public-return']);
        }

        $request = $this->returnRequestRepository->getByIdAndPhone($id, $normalizedPhone);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/public-return']);
        }
        $status = (string) ($request['status'] ?? '');
        if ($status !== ReturnRequestStatusEnum::DELIVERED->value) {
            Yii::$app->session->setFlash('error', 'Загрузка QR кода недоступна для этого статуса');
            return $this->redirect(['/public-return']);
        }

        $file = UploadedFile::getInstanceByName('qrImage');
        if ($file === null) {
            Yii::$app->session->setFlash('error', 'Необходимо загрузить изображение QR кода');
            return $this->redirect(['/public-return']);
        }

        $photo = $this->storeService->storePhoto($file, 'qr_');
        $this->returnRequestRepository->updateQrUploaded($id, $photo['relative']);

        Yii::$app->session->setFlash('success', 'QR код загружен');
        return $this->redirect(['/public-return']);
    }

    private function normalizeId(string $value): ?int
    {
        $digits = preg_replace('/\\D+/', '', $value) ?? '';
        if ($digits === '') {
            return null;
        }
        return (int) $digits;
    }
}
