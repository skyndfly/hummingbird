<?php

namespace app\controllers\Point;

use app\controllers\Point\abstracts\BasePointController;
use app\repositories\ReturnRequest\ReturnRequestRepository;
use app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum;
use app\services\Bot\BotApi;
use app\services\Phone\PhoneNormalizer;
use Yii;
use yii\helpers\Url;
use yii\data\Pagination;
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
        $status = Yii::$app->request->get('status');
        $statusFilter = $this->normalizePointStatus($status);
        $id = Yii::$app->request->get('id');
        $idFilter = $this->normalizeId($id);
        $date = Yii::$app->request->get('date');
        [$dateFrom, $dateTo] = $this->normalizeDateRange($date);
        $pageSize = 50;
        $total = $this->repository->countForPoint(
            statuses: [
                ReturnRequestStatusEnum::QR_UPLOADED->value,
                ReturnRequestStatusEnum::DELIVERED->value,
            ],
            returnType: 'wb',
            status: $statusFilter,
            id: $idFilter,
            dateFrom: $dateFrom,
            dateTo: $dateTo
        );
        $pagination = new Pagination([
            'totalCount' => $total,
            'pageSize' => $pageSize,
            'pageSizeParam' => false,
            'pageParam' => 'page',
            'params' => array_filter([
                'status' => $statusFilter,
                'id' => $idFilter,
                'date' => $date instanceof \Stringable ? (string) $date : (is_string($date) ? $date : null),
            ], static fn ($value) => $value !== null && $value !== ''),
        ]);
        $requests = $this->repository->getForPoint(
            statuses: [
                ReturnRequestStatusEnum::QR_UPLOADED->value,
                ReturnRequestStatusEnum::DELIVERED->value,
            ],
            returnType: 'wb',
            offset: $pagination->offset,
            limit: $pagination->limit,
            status: $statusFilter,
            id: $idFilter,
            dateFrom: $dateFrom,
            dateTo: $dateTo
        );

        return $this->render('return-request/index', [
            'requests' => $requests,
            'title' => 'Возвраты WB',
            'pagination' => $pagination,
            'status' => $statusFilter,
            'id' => $idFilter,
            'date' => is_string($date) ? $date : null,
            'statusLabels' => $this->pointStatusLabels(),
        ]);
    }

    public function actionOzon(): string
    {
        $status = Yii::$app->request->get('status');
        $statusFilter = $this->normalizePointStatus($status);
        $id = Yii::$app->request->get('id');
        $idFilter = $this->normalizeId($id);
        $date = Yii::$app->request->get('date');
        [$dateFrom, $dateTo] = $this->normalizeDateRange($date);
        $pageSize = 50;
        $total = $this->repository->countForPoint(
            statuses: [
                ReturnRequestStatusEnum::QR_UPLOADED->value,
                ReturnRequestStatusEnum::DELIVERED->value,
            ],
            returnType: 'ozon',
            status: $statusFilter,
            id: $idFilter,
            dateFrom: $dateFrom,
            dateTo: $dateTo
        );
        $pagination = new Pagination([
            'totalCount' => $total,
            'pageSize' => $pageSize,
            'pageSizeParam' => false,
            'pageParam' => 'page',
            'params' => array_filter([
                'status' => $statusFilter,
                'id' => $idFilter,
                'date' => $date instanceof \Stringable ? (string) $date : (is_string($date) ? $date : null),
            ], static fn ($value) => $value !== null && $value !== ''),
        ]);
        $requests = $this->repository->getForPoint(
            statuses: [
                ReturnRequestStatusEnum::QR_UPLOADED->value,
                ReturnRequestStatusEnum::DELIVERED->value,
            ],
            returnType: 'ozon',
            offset: $pagination->offset,
            limit: $pagination->limit,
            status: $statusFilter,
            id: $idFilter,
            dateFrom: $dateFrom,
            dateTo: $dateTo
        );

        return $this->render('return-request/index', [
            'requests' => $requests,
            'title' => 'Возвраты OZON',
            'pagination' => $pagination,
            'status' => $statusFilter,
            'id' => $idFilter,
            'date' => is_string($date) ? $date : null,
            'statusLabels' => $this->pointStatusLabels(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function pointStatusLabels(): array
    {
        return [
            ReturnRequestStatusEnum::DELIVERED->value => 'Доставлен на пункт',
            ReturnRequestStatusEnum::QR_UPLOADED->value => 'QR код загружен',
        ];
    }

    private function normalizePointStatus(mixed $status): ?string
    {
        if (!is_string($status) || $status === '') {
            return null;
        }
        $labels = $this->pointStatusLabels();
        return isset($labels[$status]) ? $status : null;
    }

    private function normalizeId(mixed $id): ?int
    {
        if (!is_string($id) || $id === '') {
            return null;
        }
        if (!ctype_digit($id)) {
            return null;
        }
        return (int) $id;
    }

    /**
     * @return array{0: string|null, 1: string|null}
     */
    private function normalizeDateRange(mixed $date): array
    {
        if (!is_string($date) || $date === '') {
            return [null, null];
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return [null, null];
        }
        return [$date . ' 00:00:00', $date . ' 23:59:59'];
    }

    public function actionView(int $id): string|Response
    {
        $request = $this->repository->getById($id);
        if ($request === null) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена');
            return $this->redirect(['/point-returns']);
        }
        $status = (string) ($request['status'] ?? '');
        if (!in_array($status, [
            ReturnRequestStatusEnum::QR_UPLOADED->value,
            ReturnRequestStatusEnum::DELIVERED->value,
        ], true)) {
            Yii::$app->session->setFlash('error', 'Доступ к заявке ограничен');
            return $this->redirectToList($request);
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
            'updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
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
            $message = '🟢Заявка на возврат номер ' . $id . ' выполнена. Ожидайте поступление средств.';
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
        $link = Url::to(['/public-return', 'returnId' => $id, 'phone' => $phone], true);
        $message = 'Нужно обновить код по заявке. Перейдите по ссылке: ' . $link;
        if ($id !== '') {
            $message = '🔵Нужно обновить код по заявке №' . $id. '.' . PHP_EOL .'‼️Перейдите по ссылке: ' . $link;
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
            $message = '🔴Заявка на возврат №' . $id . ' отменена. Причина: ' . $reason . '.';
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
