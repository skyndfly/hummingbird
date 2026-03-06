<?php

namespace app\controllers\Owner;


use app\controllers\Owner\abstracts\BaseOwnerController;
use app\forms\UploadedCode\ManualUploadForm;
use app\repositories\Address\AddressRepository;
use app\repositories\Company\CompanyRepository;
use app\repositories\UploadedCode\UploadedCodeRepository;
use app\services\UploadCode\dto\UploadedCodeDto;
use app\services\UploadCode\enums\UploadedCodeCompanyKeyEnum;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use app\services\UploadCode\UploadedCodeStoreService;
use app\ui\gridTable\GridFactory;
use app\ui\gridTable\UploadedCode\UploadedCodeGridTable;
use Exception;
use Yii;
use yii\web\Response;
use yii\web\UploadedFile;

class PointController extends BaseOwnerController
{

    public function __construct(
        $id,
        $module,
        private UploadedCodeStoreService $uploadedCodeStoreService,
        private UploadedCodeRepository $uploadedCodeRepository,
        private AddressRepository $addressRepository,
        private CompanyRepository $companyRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $addressId = Yii::$app->request->getQueryParam('addressId');
        $addressId = $addressId !== null ? (int) $addressId : null;
        $codes = $this->uploadedCodeRepository->findAllAwaitCodeToday($addressId);
        $grid = GridFactory::createGrid(models: $codes, gridClass: UploadedCodeGridTable::class, pageSize: 50);
        $addresses = $this->addressRepository->getAllWithCompany();


        return $this->render('index', [
            'grid' => $grid,
            'addresses' => $addresses,
            'addressId' => $addressId,
        ]);
    }

    public function actionWb(): string
    {
        return $this->render('wb');
    }

    public function actionOzon(): string
    {
        return $this->render('ozon');
    }

    public function actionWbAddCode(): string
    {
        $formModel = new ManualUploadForm();
        $address = $this->addressRepository->findByCompanyAndAddress(
            companyId: $this->getCompanyIdByKey('wb'),
            address: 'Молодогвардейцев 25'
        );
        $formModel->addressId = $address?->id;
        return $this->render('add-code', [
            'formModel' => $formModel,
            'companyKey' => UploadedCodeCompanyKeyEnum::WB
        ]);
    }

    public function actionOzonAddCode(): string
    {
        $formModel = new ManualUploadForm();
        $address = $this->addressRepository->findByCompanyAndAddress(
            companyId: $this->getCompanyIdByKey('ozon'),
            address: 'Молодогвардейцев 25'
        );
        $formModel->addressId = $address?->id;
        return $this->render('add-code', [
            'formModel' => $formModel,
            'companyKey' => UploadedCodeCompanyKeyEnum::OZON
        ]);
    }

    public function actionStoreCode(): Response
    {

        try {
            $post = Yii::$app->getRequest()->getBodyParams();
            $form = new ManualUploadForm();
            if ($form->load($post)) {
                $form->image = UploadedFile::getInstance($form, 'image');
                if ($form->validate()) {
                    if (empty($form->addressId)) {
                        throw new Exception('Не найден приоритетный адрес для выбранной службы доставки.');
                    }
                    $dto = new UploadedCodeDto(
                        fileName: uniqid() . '1535637656' . '.' . $form->image->extension,
                        companyKey: $form->companyName,
                        status: UploadedCodeStatusEnum::AWAIT,
                        chatId: '1535637656',
                        note: $form->note,
                        addressId: $form->addressId
                    );
                    $this->uploadedCodeStoreService->execute(
                        dto: $dto,
                        file: $form->image
                    );
                    Yii::$app->getSession()->setFlash('success', 'Код добавлен');
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
            Yii::$app->getSession()->setFlash('error', array_values($form->getFirstErrors())[0]);
        } catch (Exception $exception) {
            Yii::$app->getSession()->setFlash('error', $exception->getMessage());

        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    private function getCompanyIdByKey(string $key): int
    {
        $companies = $this->companyRepository->getAllCompany();
        foreach ($companies as $company) {
            if ($company->botKey === $key) {
                return $company->id;
            }
        }
        return 0;
    }

    public function getViewPath(): string
    {
        return Yii::getAlias('@app/views/owner/point');
    }


}
