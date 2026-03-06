<?php

namespace app\controllers\Point;

use app\controllers\Point\abstracts\BasePointController;
use app\forms\UploadedCode\IssuedCodeForm;
use app\repositories\Address\AddressRepository;
use app\repositories\Company\CompanyRepository;
use app\repositories\UploadedCode\UploadedCodeRepository;
use app\services\Bot\BotApi;
use app\services\UploadCode\enums\UploadedCodeCompanyKeyEnum;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use DateTimeImmutable;
use Exception;
use Throwable;
use Yii;
use yii\web\Response;

class IssuedPointController extends BasePointController
{
    public function __construct(
        $id,
        $module,
        private UploadedCodeRepository $uploadedCodeRepository,
        private BotApi $botApi,
        private AddressRepository $addressRepository,
        private CompanyRepository $companyRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    private function getPriorityAddressId(string $companyKey, string $address): ?int
    {
        $companies = $this->companyRepository->getAllCompany();
        $companyId = null;
        foreach ($companies as $company) {
            if ($company->botKey === $companyKey) {
                $companyId = $company->id;
                break;
            }
        }
        if ($companyId === null) {
            return null;
        }
        $addressDto = $this->addressRepository->findByCompanyAndAddress($companyId, $address);
        return $addressDto?->id;
    }

    public function actionIndex(): string
    {
        $addresses = $this->addressRepository->getAllWithCompany();
        $counts = [];
        foreach ($addresses as $address) {
            $counts[$address->id] = [
                'await' => $this->uploadedCodeRepository->getAwaitTodayCountByAddress($address->id),
                'pending' => $this->uploadedCodeRepository->getPendingTodayCountByAddress($address->id),
            ];
        }
        return $this->render('issued-point/index', [
            'addresses' => $addresses,
            'counts' => $counts,
        ]);
    }

    public function actionAddress(int $addressId): string
    {
        $address = $this->addressRepository->getById($addressId);
        $code = $this->uploadedCodeRepository->findAwaitCodeTodayByAddress($addressId);
        $form = new IssuedCodeForm();
        if ($code !== null) {
            $form->setAttributes($code->toArray());
        }
        $pendingCount = $this->uploadedCodeRepository->getPendingTodayCountByAddress($addressId);
        $allCount = $this->uploadedCodeRepository->getAllCodeTodayCountByAddress($addressId);
        $awaitCount = $this->uploadedCodeRepository->getAwaitCodeTodayCountByAddress($addressId);
        return $this->render('issued-point/issued-address', [
            'address' => $address,
            'code' => $code,
            'formModel' => $form,
            'pendingCount' => $pendingCount,
            'allCount' => $allCount,
            'awaitCount' => $awaitCount,
        ]);
    }

    public function actionAddressPending(int $addressId): string
    {
        $address = $this->addressRepository->getById($addressId);
        $code = $this->uploadedCodeRepository->findPendingCodeTodayByAddress($addressId);
        $form = new IssuedCodeForm();
        if ($code !== null) {
            $form->setAttributes($code->toArray());
        }
        $pendingCount = $this->uploadedCodeRepository->getPendingTodayCountByAddress($addressId);
        return $this->render('issued-point/pending-address', [
            'address' => $address,
            'code' => $code,
            'formModel' => $form,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function actionWb(): Response|string
    {
        $priorityAddressId = $this->getPriorityAddressId('wb', 'Молодогвардейцев 25');
        if ($priorityAddressId !== null) {
            return $this->redirect(['/issued-point/address/' . $priorityAddressId]);
        }
        $code = $this->uploadedCodeRepository->findAwaitCodeToday(UploadedCodeCompanyKeyEnum::WB);
        $form = new IssuedCodeForm();
        if ($code !== null) {
            $form->setAttributes($code->toArray());
        }
        $pendingCount = $this->uploadedCodeRepository->getPendingTodayCount(UploadedCodeCompanyKeyEnum::WB);
        $allCount = $this->uploadedCodeRepository->getAllCodeTodayCount(UploadedCodeCompanyKeyEnum::WB);
        $awaitCount = $this->uploadedCodeRepository->getAwaitCodeTodayCount(UploadedCodeCompanyKeyEnum::WB);
        return $this->render('issued-point/issued', [
            'point' => UploadedCodeCompanyKeyEnum::WB,
            'code' => $code,
            'formModel' => $form,
            'pendingCount' => $pendingCount,
            'allCount' => $allCount,
            'awaitCount' => $awaitCount
        ]);
    }

    public function actionOzon(): Response|string
    {
        $priorityAddressId = $this->getPriorityAddressId('ozon', 'Молодогвардейцев 25');
        if ($priorityAddressId !== null) {
            return $this->redirect(['/issued-point/address/' . $priorityAddressId]);
        }
        $code = $this->uploadedCodeRepository->findAwaitCodeToday(UploadedCodeCompanyKeyEnum::OZON);
        $form = new IssuedCodeForm();
        if ($code !== null) {
            $form->setAttributes($code->toArray());
        }
        $pendingCount = $this->uploadedCodeRepository->getPendingTodayCount(UploadedCodeCompanyKeyEnum::OZON);
        $allCount = $this->uploadedCodeRepository->getAllCodeTodayCount(UploadedCodeCompanyKeyEnum::OZON);
        $awaitCount = $this->uploadedCodeRepository->getAwaitCodeTodayCount(UploadedCodeCompanyKeyEnum::OZON);
        return $this->render('issued-point/issued', [
            'point' => UploadedCodeCompanyKeyEnum::OZON,
            'code' => $code,
            'formModel' => $form,
            'pendingCount' => $pendingCount,
            'allCount' => $allCount,
            'awaitCount' => $awaitCount
        ]);
    }

    public function actionIssued(): Response
    {
        try {
            $post = Yii::$app->getRequest()->getBodyParams();
            $form = new IssuedCodeForm();
            if ($form->load($post) && $form->validate()) {
                $status = UploadedCodeStatusEnum::from($form->status);
                $code = $this->uploadedCodeRepository->getById($form->id);

                $this->uploadedCodeRepository->issuedCode(
                    id: $form->id,
                    status: $status,
                );
                if ($status != UploadedCodeStatusEnum::PENDING && !empty($code->chatId)) {
                    try {
                        $this->botApi->sendIssued(
                            id: $code->chatId,
                            status: $status->value,
                            createdAt: new DateTimeImmutable($code->createdAt)
                        );
                    } catch (Throwable $exception) {
                        Yii::info([
                            'type' => 'SendBotApi',
                            'message' => $exception->getMessage(),
                        ]);
                    }
                }

                Yii::$app->getSession()->setFlash('success', 'Данные сохранены');
                return $this->redirect(Yii::$app->request->referrer);
            }
            Yii::$app->getSession()->setFlash('error', array_values($form->getFirstErrors())[0]);
        } catch (Exception $exception) {
            Yii::$app->getSession()->setFlash('error', $exception->getMessage());

        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionWbPending(): Response|string
    {
        $priorityAddressId = $this->getPriorityAddressId('wb', 'Молодогвардейцев 25');
        if ($priorityAddressId !== null) {
            return $this->redirect(['/issued-point/address/' . $priorityAddressId . '/pending']);
        }
        $code = $this->uploadedCodeRepository->findPendingCodeToday(UploadedCodeCompanyKeyEnum::WB);
        $form = new IssuedCodeForm();
        if ($code !== null) {
            $form->setAttributes($code->toArray());
        }
        $pendingCount = $this->uploadedCodeRepository->getPendingTodayCount(UploadedCodeCompanyKeyEnum::WB);
        return $this->render('issued-point/pending', [
            'code' => $code,
            'formModel' => $form,
            'company' => UploadedCodeCompanyKeyEnum::WB,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function actionOzonPending(): Response|string
    {
        $priorityAddressId = $this->getPriorityAddressId('ozon', 'Молодогвардейцев 25');
        if ($priorityAddressId !== null) {
            return $this->redirect(['/issued-point/address/' . $priorityAddressId . '/pending']);
        }
        $code = $this->uploadedCodeRepository->findPendingCodeToday(UploadedCodeCompanyKeyEnum::OZON);
        $form = new IssuedCodeForm();
        if ($code !== null) {
            $form->setAttributes($code->toArray());
        }
        $pendingCount = $this->uploadedCodeRepository->getPendingTodayCount(UploadedCodeCompanyKeyEnum::OZON);
        return $this->render('issued-point/pending', [
            'code' => $code,
            'formModel' => $form,
            'company' => UploadedCodeCompanyKeyEnum::OZON,
            'pendingCount' => $pendingCount,
        ]);
    }
}
