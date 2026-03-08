<?php

namespace app\controllers\Point;

use app\controllers\Point\abstracts\BasePointController;
use app\repositories\ReturnRequest\ReturnRequestRepository;
use app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum;
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
            status: ReturnRequestStatusEnum::QR_UPLOADED->value,
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
            status: ReturnRequestStatusEnum::QR_UPLOADED->value,
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
        Yii::$app->session->setFlash('success', 'Заявка выполнена');
        return $this->redirectToNext($request);
    }

    public function actionCancel(int $id): Response
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
        $this->repository->updateStatus($id, ReturnRequestStatusEnum::CANCELED->value);
        Yii::$app->session->setFlash('success', 'Заявка отменена');
        return $this->redirectToNext($request);
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
}
