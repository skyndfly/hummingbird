<?php

namespace app\controllers\Owner;

use app\controllers\Owner\abstracts\BaseOwnerController;
use app\repositories\Sale\SaleRepository;
use app\repositories\UploadedCode\UploadedCodeRepository;
use app\services\Code\StockStatisticsService;
use app\services\UploadCode\enums\UploadedCodeCompanyKeyEnum;
use Yii;

class StatisticsController extends BaseOwnerController
{

    public function __construct(
        $id,
        $module,
        private UploadedCodeRepository $uploadedCodeRepository,
        private SaleRepository $saleRepository,
        private StockStatisticsService $stockStatisticsService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        $saleStats = $this->saleRepository->getIssuedStats();
        if ($saleStats !== false) {
            $totalCodes = $saleStats['total_codes'];
            $totalAmount = $saleStats['total_amount'];
        } else {
            $totalCodes = 0;
            $totalAmount = 0;
        }
        $statistics = $this->stockStatisticsService->getStatistics();


        return $this->render('index', [
            'allWbCount' => $this->uploadedCodeRepository->getAllCodeTodayCount(UploadedCodeCompanyKeyEnum::WB),
            'allOzonCount' => $this->uploadedCodeRepository->getAllCodeTodayCount(UploadedCodeCompanyKeyEnum::OZON),
            'issuedOzonCount' => $this->uploadedCodeRepository->getIssuedCodeTodayCount(UploadedCodeCompanyKeyEnum::OZON),
            'issuedWbCount' => $this->uploadedCodeRepository->getIssuedCodeTodayCount(UploadedCodeCompanyKeyEnum::WB),
            'awaitWbCount' => $this->uploadedCodeRepository->getAwaitCodeTodayCount(UploadedCodeCompanyKeyEnum::WB),
            'awaitOzonCount' => $this->uploadedCodeRepository->getAwaitCodeTodayCount(UploadedCodeCompanyKeyEnum::OZON),
            'notPaidWbCount' => $this->uploadedCodeRepository->getNotpaidCodeTodayCount(UploadedCodeCompanyKeyEnum::WB),
            'notPaidOzonCount' => $this->uploadedCodeRepository->getNotpaidCodeTodayCount(UploadedCodeCompanyKeyEnum::OZON),
            'outdatedWbCount' => $this->uploadedCodeRepository->getOutdatedCodeTodayCount(UploadedCodeCompanyKeyEnum::WB),
            'outdatedOzonCount' => $this->uploadedCodeRepository->getOutdatedCodeTodayCount(UploadedCodeCompanyKeyEnum::OZON),
            'totalCodes' => $totalCodes,
            'totalAmount' => $totalAmount,
            'statistics' => $statistics,
        ]);
    }

    public function getViewPath(): string
    {
        return Yii::getAlias('@app/views/owner/statistics');
    }


}