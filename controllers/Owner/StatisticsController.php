<?php

namespace app\controllers\Owner;

use app\auth\enums\UserTypeEnum;
use app\controllers\Owner\abstracts\BaseOwnerController;
use app\filters\User\ManagerFilter;
use app\forms\User\CreateManagerForm;
use app\repositories\UploadedCode\UploadedCodeRepository;
use app\repositories\User\dto\UserSearchDto;
use app\services\UploadCode\enums\UploadedCodeCompanyKeyEnum;
use app\services\User\UserCreateService;
use app\services\User\UserPaginateService;
use app\ui\gridTable\GridFactory;
use app\ui\gridTable\User\ManagerGridTable;
use Exception;
use Yii;
use yii\web\Response;

class StatisticsController extends BaseOwnerController
{

    public function __construct(
        $id,
        $module,
        private UploadedCodeRepository $uploadedCodeRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
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
        ]);
    }

    public function getViewPath(): string
    {
        return Yii::getAlias('@app/views/owner/statistics');
    }


}