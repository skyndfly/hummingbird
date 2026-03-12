<?php

namespace app\controllers\Manager;

use app\controllers\Manager\abstracts\BaseManagerController;
use app\forms\ReturnRequest\CreateReturnRequestForm;
use app\forms\ReturnRequest\EditReturnRequestForm;
use app\repositories\ReturnRequest\ReturnRequestRepository;
use app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum;
use app\services\Bot\BotApi;
use app\services\Phone\PhoneNormalizer;
use app\services\ReturnRequest\ReturnRequestStoreService;
use Exception;
use Yii;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\helpers\Url;

class ReturnRequestController extends BaseManagerController
{
    public function __construct(
        $id,
        $module,
        private ReturnRequestRepository $repository,
        private ReturnRequestStoreService $storeService,
        private BotApi $botApi,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        $number = Yii::$app->request->get('number');
        $phone = Yii::$app->request->get('phone');
        $status = Yii::$app->request->get('status');
        $normalizedPhone = null;
        if (is_string($phone)) {
            $normalizedPhone = PhoneNormalizer::normalize($phone);
        }
        $statusFilter = null;
        if (is_string($status) && $status !== '') {
            $labels = $this->statusLabels();
            if (isset($labels[$status])) {
                $statusFilter = $status;
            }
        }

        $requests = $this->repository->getAll(
            number: is_string($number) ? $number : null,
            phone: $normalizedPhone,
            status: $statusFilter
        );

        return $this->render('return-request/index', [
            'requests' => $requests,
            'statusLabels' => $this->statusLabels(),
            'number' => $number,
            'phone' => $phone,
            'status' => $statusFilter,
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

            if ($form->validate()) {
                try {
                    $number = $this->storeService->execute(
                        phone: $form->phone,
                        returnType: $form->returnType,
                        photoOne: $form->photoOne,
                        createdBy: $this->getIdentity()->getId()
                    );
                    $this->notifyAccepted($form->phone, $number);
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
            $form->qrCode = UploadedFile::getInstance($form, 'qrCode');
            if ($form->qrCode === null) {
                $form->qrCode = UploadedFile::getInstanceByName($form->formName() . '[qrCode]');
            }
            if ($form->qrCode === null) {
                $form->qrCode = UploadedFile::getInstanceByName('qrCode');
            }

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
                    if ($form->qrCode instanceof UploadedFile) {
                        $photo = $this->storeService->storePhoto($form->qrCode, 'qr_');
                        $columns['qr_code_file'] = $photo['relative'];
                        $this->deleteOldPhoto((string) ($request['qr_code_file'] ?? ''));
                        $columns['status'] = ReturnRequestStatusEnum::QR_UPLOADED->value;
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

    public function actionRoad(int $id): Response
    {
        $request = $this->repository->getById($id);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/return-request']);
        }
        if (($request['status'] ?? '') !== ReturnRequestStatusEnum::ACCEPTED->value) {
            Yii::$app->session->setFlash('error', 'Нельзя изменить статус');
            return $this->redirect(['/return-request/view', 'id' => $id]);
        }
        $this->repository->updateStatus($id, ReturnRequestStatusEnum::ROAD->value);
        Yii::$app->session->setFlash('success', 'Статус обновлен');
        return $this->redirect(['/return-request/view', 'id' => $id]);
    }

    public function actionDelivered(int $id): Response
    {
        $request = $this->repository->getById($id);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/return-request']);
        }
        if (($request['status'] ?? '') !== ReturnRequestStatusEnum::ROAD->value) {
            Yii::$app->session->setFlash('error', 'Нельзя изменить статус');
            return $this->redirect(['/return-request/view', 'id' => $id]);
        }
        $this->repository->updateStatus($id, ReturnRequestStatusEnum::DELIVERED->value);
        $this->notifyDelivered($request);
        Yii::$app->session->setFlash('success', 'Статус обновлен');
        return $this->redirect(['/return-request/view', 'id' => $id]);
    }

    public function actionAccepted(int $id): Response
    {
        $request = $this->repository->getById($id);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/return-request']);
        }
        if (($request['status'] ?? '') !== ReturnRequestStatusEnum::RETURNING->value) {
            Yii::$app->session->setFlash('error', 'Нельзя изменить статус');
            return $this->redirect(['/return-request/view', 'id' => $id]);
        }
        $this->repository->updateStatus($id, ReturnRequestStatusEnum::ACCEPTED_RETURN->value);
        $this->notifyReturnClient($request);
        Yii::$app->session->setFlash('success', 'Статус обновлен');
        return $this->redirect(['/return-request/view', 'id' => $id]);
    }

    public function actionReturnClient(int $id): Response
    {
        $request = $this->repository->getById($id);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/return-request']);
        }
        if (($request['status'] ?? '') !== ReturnRequestStatusEnum::ACCEPTED_RETURN->value) {
            Yii::$app->session->setFlash('error', 'Нельзя изменить статус');
            return $this->redirect(['/return-request/view', 'id' => $id]);
        }
        $this->repository->updateStatus($id, ReturnRequestStatusEnum::RETURN_CLIENT->value);
        Yii::$app->session->setFlash('success', 'Статус обновлен');
        return $this->redirect(['/return-request/view', 'id' => $id]);
    }

    public function actionReturning(int $id): Response
    {
        $request = $this->repository->getById($id);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/return-request']);
        }
        if (($request['status'] ?? '') !== ReturnRequestStatusEnum::CANCELED->value) {
            Yii::$app->session->setFlash('error', 'Нельзя изменить статус');
            return $this->redirect(['/return-request/view', 'id' => $id]);
        }
        $this->repository->updateStatus($id, ReturnRequestStatusEnum::RETURNING->value);
        Yii::$app->session->setFlash('success', 'Статус обновлен');
        return $this->redirect(['/return-request/view', 'id' => $id]);
    }

    /**
     * @return array<string, string>
     */
    private function statusLabels(): array
    {
        return [
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

    private function notifyDelivered(array $request): void
    {
        $phone = (string) ($request['phone'] ?? '');
        if ($phone === '') {
            return;
        }
        $result = $this->botApi->getUsers($phone, null, 1, 50);
        if (empty($result['users'])) {
            return;
        }
        $id = (string) ($request['id'] ?? '');
        $link = Url::to(['/public-return', 'returnId' => $id, 'phone' => $phone], true);
        $message = '🟠Возврат по заявке ' . $id . ' доставлен на пункт.'. PHP_EOL . '‼️Перейдите по этой ссылке и загрузите QR код: ' . $link;
        foreach ($result['users'] as $user) {
            $chatId = (string) ($user['id'] ?? '');
            if ($chatId === '') {
                continue;
            }
            $this->botApi->sendMessage($chatId, $message);
        }
    }

    private function notifyReturnClient(array $request): void
    {
        $phone = (string) ($request['phone'] ?? '');
        if ($phone === '') {
            return;
        }
        $phone = PhoneNormalizer::normalize($phone);
        $result = $this->botApi->getUsers($phone, null, 1, 50);
        if (empty($result['users'])) {
            return;
        }
        $id = (string) ($request['id'] ?? '');
        $message = 'Нужно прийти и забрать возврат.';
        if ($id !== '') {
            $message = '🟣Возврат по заявке №' . $id . ' возвращён в 108к. Нужно прийти и забрать.';
        }
        foreach ($result['users'] as $user) {
            $chatId = (string) ($user['id'] ?? '');
            if ($chatId === '') {
                continue;
            }
            $this->botApi->sendMessage($chatId, $message);
        }
    }

    private function notifyAccepted(string $phone, string $id): void
    {
        if ($phone === '') {
            return;
        }
        $phone = PhoneNormalizer::normalize($phone);
        $result = $this->botApi->getUsers($phone, null, 1, 50);
        if (empty($result['users'])) {
            return;
        }
        $message = '🟡Возврат по заявке принят в 108к, ожидайте доставки на пункт.';
        if ($id !== '') {
            $message = '🟡Возврат по заявке №' . $id . ' принят в 108к, ожидайте доставки на пункт.';
        }
        foreach ($result['users'] as $user) {
            $chatId = (string) ($user['id'] ?? '');
            if ($chatId === '') {
                continue;
            }
            $this->botApi->sendMessage($chatId, $message);
        }
    }
}
