<?php

namespace app\controllers;

use app\forms\PublicUploadForm;
use app\repositories\Address\AddressRepository;
use app\repositories\BotSettings\BotSettingsRepository;
use app\repositories\Company\CompanyRepository;
use app\services\Phone\PhoneNormalizer;
use app\services\UploadCode\dto\UploadedCodeDto;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use app\services\UploadCode\UploadedCodeStoreService;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class PublicUploadController extends Controller
{
    public $layout = false;

    public function __construct(
        $id,
        $module,
        private CompanyRepository $companyRepository,
        private AddressRepository $addressRepository,
        private BotSettingsRepository $botSettingsRepository,
        private UploadedCodeStoreService $uploadedCodeStoreService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        $companies = $this->companyRepository->getAllCompany();
        $addresses = $this->addressRepository->getAll();
        $cutoffHour = $this->botSettingsRepository->getCutoffHour();
        $currentHour = $this->getCurrentHour();
        $isUploadClosed = $currentHour >= $cutoffHour;

        return $this->render('index', [
            'companies' => $companies,
            'addresses' => $addresses,
            'cutoffHour' => $cutoffHour,
            'isUploadClosed' => $isUploadClosed,
        ]);
    }

    public function actionStore(): Response
    {
        try {
            $cutoffHour = $this->botSettingsRepository->getCutoffHour();
            if ($this->getCurrentHour() >= $cutoffHour) {
                Yii::$app->getSession()->setFlash(
                    'error',
                    sprintf('Прием кодов закрыт после %02d:00. Попробуйте снова завтра.', $cutoffHour)
                );
                return $this->redirect(['/public-upload']);
            }

            $post = Yii::$app->getRequest()->getBodyParams();
            $form = new PublicUploadForm();
            if ($form->load($post)) {
                $form->phone = PhoneNormalizer::normalize($form->phone);
                $form->image = UploadedFile::getInstance($form, 'image');
                if ($form->validate()) {
                    $company = $this->companyRepository->getById($form->companyId);
                    $address = $this->addressRepository->getById($form->addressId);
                    if ($address->companyId !== $company->id) {
                        throw new Exception('Выбранный адрес не относится к выбранной компании.');
                    }
                    if (empty($company->botKey)) {
                        throw new Exception('Для выбранной компании не задан bot_key.');
                    }

                    $dto = new UploadedCodeDto(
                        fileName: uniqid() . '.' . $form->image->extension,
                        companyKey: $company->botKey,
                        status: UploadedCodeStatusEnum::AWAIT,
                        chatId: null,
                        addressId: $address->id,
                        note: $form->phone
                    );
                    $this->uploadedCodeStoreService->execute(
                        dto: $dto,
                        file: $form->image
                    );
                    Yii::$app->getSession()->setFlash('success', 'Код успешно отправлен.');
                    return $this->redirect(['/public-upload']);
                }
            }
            Yii::$app->getSession()->setFlash('error', array_values($form->getFirstErrors())[0]);
        } catch (Exception $exception) {
            Yii::$app->getSession()->setFlash('error', $exception->getMessage());
        }
        return $this->redirect(['/public-upload']);
    }

    private function getCurrentHour(): int
    {
        $timezone = new DateTimeZone(Yii::$app->timeZone);
        return (int) (new DateTimeImmutable('now', $timezone))->format('G');
    }
}
