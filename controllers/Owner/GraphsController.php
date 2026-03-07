<?php

namespace app\controllers\Owner;

use app\controllers\Owner\abstracts\BaseOwnerController;
use app\repositories\Address\AddressRepository;
use app\repositories\UploadedCode\UploadedCodeRepository;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use DateTimeImmutable;
use DateTimeZone;
use Yii;

class GraphsController extends BaseOwnerController
{
    public function __construct(
        $id,
        $module,
        private AddressRepository $addressRepository,
        private UploadedCodeRepository $uploadedCodeRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        $addresses = $this->addressRepository->getAllWithCompany();
        $tz = new DateTimeZone('Europe/Moscow');

        $addressId = Yii::$app->request->getQueryParam('addressId');
        $addressId = $addressId !== null ? (int) $addressId : null;
        if ($addressId === null && !empty($addresses)) {
            $addressId = $addresses[0]->id;
        }

        $startParam = Yii::$app->request->getQueryParam('start');
        $endParam = Yii::$app->request->getQueryParam('end');

        $startDate = $this->parseDateOrToday($startParam, $tz);
        $endDate = $this->parseDateOrToday($endParam, $tz);

        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $startDateTime = $startDate->setTime(0, 0, 0);
        $endDateTime = $endDate->setTime(23, 59, 59);

        $counts = [];
        $totalCount = 0;
        if ($addressId !== null) {
            $counts = $this->uploadedCodeRepository->getStatusCountsByAddressAndRange(
                addressId: $addressId,
                startDateTime: $startDateTime->format('Y-m-d H:i:s'),
                endDateTime: $endDateTime->format('Y-m-d H:i:s')
            );
            $totalCount = array_sum($counts);
        }

        $statusLabels = [];
        $statusValues = [];
        foreach (UploadedCodeStatusEnum::cases() as $status) {
            $statusLabels[] = $status->label();
            $statusValues[] = $counts[$status->value] ?? 0;
        }

        return $this->render('index', [
            'addresses' => $addresses,
            'addressId' => $addressId,
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
            'statusLabels' => $statusLabels,
            'statusValues' => $statusValues,
            'totalCount' => $totalCount,
            'counts' => $counts,
        ]);
    }

    public function getViewPath(): string
    {
        return Yii::getAlias('@app/views/owner/graphs');
    }

    private function parseDateOrToday(?string $value, DateTimeZone $tz): DateTimeImmutable
    {
        if (is_string($value) && $value !== '') {
            $parsed = DateTimeImmutable::createFromFormat('Y-m-d', $value, $tz);
            if ($parsed !== false) {
                return $parsed;
            }
        }
        return new DateTimeImmutable('now', $tz);
    }
}
