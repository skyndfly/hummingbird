<?php

namespace app\controllers\Manager;

use app\controllers\Manager\abstracts\BaseManagerController;
use app\forms\ReturnRequest\CreateReturnRequestForm;
use app\forms\ReturnRequest\EditReturnRequestForm;
use app\repositories\ReturnRequest\ReturnRequestRepository;
use app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum;
use app\services\Phone\PhoneNormalizer;
use app\services\ReturnRequest\ReturnRequestStoreService;
use Exception;
use Yii;
use yii\web\Response;
use yii\web\UploadedFile;

class ReturnRequestController extends BaseManagerController
{
    public function __construct(
        $id,
        $module,
        private ReturnRequestRepository $repository,
        private ReturnRequestStoreService $storeService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        $number = Yii::$app->request->get('number');
        $phone = Yii::$app->request->get('phone');
        $normalizedPhone = null;
        if (is_string($phone)) {
            $normalizedPhone = PhoneNormalizer::normalize($phone);
        }

        $requests = $this->repository->getAll(
            number: is_string($number) ? $number : null,
            phone: $normalizedPhone
        );

        return $this->render('return-request/index', [
            'requests' => $requests,
            'statusLabels' => $this->statusLabels(),
            'number' => $number,
            'phone' => $phone,
        ]);
    }

    public function actionCreate(): string
    {
        $formModel = new CreateReturnRequestForm();
        return $this->render('return-request/create', [
            'formModel' => $formModel,
        ]);
    }

    public function actionView(int $id): string|Response
    {
        $request = $this->repository->getById($id);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/return-request']);
        }
        return $this->render('return-request/view', [
            'request' => $request,
            'statusLabels' => $this->statusLabels(),
        ]);
    }

    public function actionEdit(int $id): string|Response
    {
        $request = $this->repository->getById($id);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/return-request']);
        }
        $formModel = new EditReturnRequestForm();
        $formModel->phone = (string) ($request['phone'] ?? '');

        return $this->render('return-request/edit', [
            'formModel' => $formModel,
            'request' => $request,
        ]);
    }

    public function actionStore(): Response
    {
        $form = new CreateReturnRequestForm();
        $post = Yii::$app->request->post();

        if ($form->load($post)) {
            $form->phone = PhoneNormalizer::normalize($form->phone);
            $form->photoOne = UploadedFile::getInstance($form, 'photoOne');
            $form->photoTwo = UploadedFile::getInstance($form, 'photoTwo');

            if ($form->validate()) {
                try {
                    $number = $this->storeService->execute(
                        phone: $form->phone,
                        returnType: $form->returnType,
                        photoOne: $form->photoOne,
                        photoTwo: $form->photoTwo,
                        createdBy: $this->getIdentity()->getId()
                    );
                    Yii::$app->session->setFlash('success', 'Заявка создана. Номер: ' . $number);
                    return $this->redirect(['/return-request/create']);
                } catch (Exception $e) {
                    Yii::$app->session->setFlash('error', $e->getMessage());
                    return $this->redirect(['/return-request/create']);
                }
            }
        }

        Yii::$app->session->setFlash('error', array_values($form->getFirstErrors())[0] ?? 'Ошибка при создании заявки');
        return $this->redirect(['/return-request/create']);
    }

    public function actionUpdate(int $id): Response
    {
        $request = $this->repository->getById($id);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/return-request']);
        }

        $form = new EditReturnRequestForm();
        $post = Yii::$app->request->post();
        if ($form->load($post)) {
            $form->phone = PhoneNormalizer::normalize($form->phone);
            $form->photoOne = UploadedFile::getInstance($form, 'photoOne');
            $form->photoTwo = UploadedFile::getInstance($form, 'photoTwo');

            if ($form->validate()) {
                try {
                    $columns = [
                        'phone' => $form->phone,
                        'updated_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                    ];

                    if ($form->photoOne instanceof UploadedFile) {
                        $photo = $this->storeService->storePhoto($form->photoOne, 'rr1_');
                        $columns['photo_one'] = $photo['relative'];
                        $this->deleteOldPhoto((string) ($request['photo_one'] ?? ''));
                    }
                    if ($form->photoTwo instanceof UploadedFile) {
                        $photo = $this->storeService->storePhoto($form->photoTwo, 'rr2_');
                        $columns['photo_two'] = $photo['relative'];
                        $this->deleteOldPhoto((string) ($request['photo_two'] ?? ''));
                    }

                    $this->repository->updateById($id, $columns);
                    Yii::$app->session->setFlash('success', 'Заявка обновлена');
                    return $this->redirect(['/return-request/' . $id]);
                } catch (Exception $e) {
                    Yii::$app->session->setFlash('error', $e->getMessage());
                    return $this->redirect(['/return-request/' . $id . '/edit']);
                }
            }
        }

        Yii::$app->session->setFlash('error', array_values($form->getFirstErrors())[0] ?? 'Ошибка при обновлении заявки');
        return $this->redirect(['/return-request/' . $id . '/edit']);
    }

    public function actionDelete(int $id): Response
    {
        $this->repository->softDelete($id);
        Yii::$app->session->setFlash('success', 'Заявка удалена');
        return $this->redirect(['/return-request']);
    }

    /**
     * @return array<string, string>
     */
    private function statusLabels(): array
    {
        return [
            ReturnRequestStatusEnum::CREATED->value => ReturnRequestStatusEnum::CREATED->label(),
            ReturnRequestStatusEnum::QR_UPLOADED->value => ReturnRequestStatusEnum::QR_UPLOADED->label(),
            ReturnRequestStatusEnum::COMPLETED->value => ReturnRequestStatusEnum::COMPLETED->label(),
            ReturnRequestStatusEnum::CANCELED->value => ReturnRequestStatusEnum::CANCELED->label(),
        ];
    }

    private function deleteOldPhoto(string $relativePath): void
    {
        if ($relativePath === '') {
            return;
        }
        $absolute = Yii::getAlias('@webroot/' . ltrim($relativePath, '/'));
        if (file_exists($absolute)) {
            @unlink($absolute);
        }
    }
}
