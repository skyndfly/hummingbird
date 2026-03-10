<?php

namespace app\controllers\Point;

use app\controllers\Point\abstracts\BasePointController;
use app\repositories\ReturnRequest\ReturnRequestRepository;
use app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum;
use app\services\Bot\BotApi;
use app\services\Phone\PhoneNormalizer;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use yii\web\Response;

class ReturnRequestController extends BasePointController
{
    public function __construct(
        $id,
        $module,
        private ReturnRequestRepository $repository,
        private BotApi $botApi,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionWb(): string
    {
        $tz = new DateTimeZone(Yii::$app->timeZone ?? 'Europe/Moscow');
        $now = new DateTimeImmutable('now', $tz);
        $todayStart = $now->format('Y-m-d 00:00:00');
        $todayEnd = $now->format('Y-m-d 23:59:59');

        $requests = $this->repository->getForPointToday(
            statuses: [
                ReturnRequestStatusEnum::QR_UPLOADED->value,
                ReturnRequestStatusEnum::DELIVERED->value,
            ],
            returnType: 'wb',
            from: $todayStart,
            to: $todayEnd
        );

        return $this->render('return-request/index', [
            'requests' => $requests,
            'title' => 'Возвраты WB',
        ]);
    }

    public function actionOzon(): string
    {
        $tz = new DateTimeZone(Yii::$app->timeZone ?? 'Europe/Moscow');
        $now = new DateTimeImmutable('now', $tz);
        $todayStart = $now->format('Y-m-d 00:00:00');
        $todayEnd = $now->format('Y-m-d 23:59:59');

        $requests = $this->repository->getForPointToday(
            statuses: [
                ReturnRequestStatusEnum::QR_UPLOADED->value,
                ReturnRequestStatusEnum::DELIVERED->value,
            ],
            returnType: 'ozon',
            from: $todayStart,
            to: $todayEnd
        );

        return $this->render('return-request/index', [
            'requests' => $requests,
            'title' => 'Возвраты OZON',
        ]);
    }

    public function actionView(int $id): string
    {
        $request = $this->repository->getById($id);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/point-returns']);
        }

        return $this->render('return-request/view', [
            'request' => $request,
        ]);
    }

    public function actionComplete(int $id): Response
    {
        $request = $this->repository->getById($id);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/point-returns/wb']);
        }
        if (($request['status'] ?? '') !== ReturnRequestStatusEnum::QR_UPLOADED->value) {
            Yii::$app->session->setFlash('error', 'Нельзя изменить статус');
            return $this->redirect(['/point-returns/' . $id]);
        }
        $this->repository->updateStatus($id, ReturnRequestStatusEnum::COMPLETED->value);
        $this->notifyCompleted($request);
        Yii::$app->session->setFlash('success', 'Заявка выполнена');
        return $this->redirectToList($request);
    }

    public function actionCancel(int $id): Response
    {
        $request = $this->repository->getById($id);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/point-returns/wb']);
        }
        $reason = Yii::$app->request->post('cancelReason');
        if (!is_string($reason) || trim($reason) === '') {
            Yii::$app->session->setFlash('error', 'Необходимо указать причину отмены');
            return $this->redirect(['/point-returns/' . $id]);
        }
        if (($request['status'] ?? '') !== ReturnRequestStatusEnum::QR_UPLOADED->value) {
            Yii::$app->session->setFlash('error', 'Нельзя изменить статус');
            return $this->redirect(['/point-returns/' . $id]);
        }
        $this->repository->updateById($id, [
            'status' => ReturnRequestStatusEnum::CANCELED->value,
            'cancel_reason' => trim($reason),
            'updated_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);
        $this->notifyCanceled($request, trim($reason));
        Yii::$app->session->setFlash('success', 'Заявка отменена');
        return $this->redirectToList($request);
    }

    public function actionDelivered(int $id): Response
    {
        $request = $this->repository->getById($id);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/point-returns/wb']);
        }
        if (($request['status'] ?? '') !== ReturnRequestStatusEnum::QR_UPLOADED->value) {
            Yii::$app->session->setFlash('error', 'Нельзя изменить статус');
            return $this->redirect(['/point-returns/' . $id]);
        }
        $this->repository->updateStatus($id, ReturnRequestStatusEnum::DELIVERED->value);
        Yii::info([
            'type' => 'ReturnRequest',
            'action' => 'delivered',
            'id' => $id,
        ], 'bot');
        $this->notifyUpdateQr($request);
        Yii::$app->session->setFlash('success', 'Статус обновлен');
        return $this->redirectToList($request);
    }

    private function redirectToList(array $currentRequest): Response
    {
        $returnType = (string) ($currentRequest['return_type'] ?? 'wb');
        return $this->redirect(['/point-returns/' . $returnType]);
    }

    private function redirectToNext(array $currentRequest): Response
    {
        $tz = new DateTimeZone(Yii::$app->timeZone ?? 'Europe/Moscow');
        $now = new DateTimeImmutable('now', $tz);
        $todayStart = $now->format('Y-m-d 00:00:00');
        $todayEnd = $now->format('Y-m-d 23:59:59');
        $returnType = (string) ($currentRequest['return_type'] ?? 'wb');
        $currentId = (int) ($currentRequest['id'] ?? 0);

        $next = $this->repository->getNextForPointToday(
            status: ReturnRequestStatusEnum::QR_UPLOADED->value,
            returnType: $returnType,
            from: $todayStart,
            to: $todayEnd,
            excludeId: $currentId
        );

        if ($next !== null) {
            return $this->redirect(['/point-returns/' . (int) $next['id']]);
        }

        Yii::$app->session->setFlash('success', 'Активных заявок больше нет');
        return $this->redirect(['/point-returns/' . $returnType]);
    }

    private function notifyCompleted(array $request): void
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
        $message = 'Заявка на возврат выполнена. Ожидайте поступление средств.';
        if ($id !== '') {
            $message = 'Заявка на возврат номер ' . $id . ' выполнена. Ожидайте поступление средств.';
        }
        foreach ($result['users'] as $user) {
            $chatId = (string) ($user['id'] ?? '');
            if ($chatId === '') {
                continue;
            }
            $this->botApi->sendMessage($chatId, $message);
        }
    }

    private function notifyUpdateQr(array $request): void
    {
        $phone = (string) ($request['phone'] ?? '');
        if ($phone === '') {
            Yii::info([
                'type' => 'ReturnRequest',
                'action' => 'notifyUpdateQr',
                'error' => 'empty_phone',
            ], 'bot');
            return;
        }
        $phone = PhoneNormalizer::normalize($phone);
        $result = $this->botApi->getUsers($phone, null, 1, 50);
        if (empty($result['users'])) {
            Yii::info([
                'type' => 'ReturnRequest',
                'action' => 'notifyUpdateQr',
                'error' => 'no_users',
                'phone' => $phone,
            ], 'bot');
            return;
        }
        $id = (string) ($request['id'] ?? '');
        $link = \yii\helpers\Url::to(['/public-return', 'returnId' => $id, 'phone' => $phone], true);
        $message = 'Нужно обновить код по заявке. Перейдите по ссылке: ' . $link;
        if ($id !== '') {
            $message = 'Нужно обновить код по заявке №' . $id . '. Перейдите по ссылке: ' . $link;
        }
        foreach ($result['users'] as $user) {
            $chatId = (string) ($user['id'] ?? '');
            if ($chatId === '') {
                continue;
            }
            Yii::info([
                'type' => 'ReturnRequest',
                'action' => 'notifyUpdateQr',
                'chatId' => $chatId,
            ], 'bot');
            $this->botApi->sendMessage($chatId, $message);
        }
    }

    private function notifyCanceled(array $request, string $reason): void
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
        $message = 'Заявка на возврат отменена. Причина: ' . $reason . '.';
        if ($id !== '') {
            $message = 'Заявка на возврат №' . $id . ' отменена. Причина: ' . $reason . '.';
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
