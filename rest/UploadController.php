<?php

namespace app\rest;

use app\services\UploadCode\dto\UploadedCodeDto;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use app\services\UploadCode\UploadedCodeStoreService;
use app\repositories\BotSettings\BotSettingsRepository;
use app\repositories\Address\AddressRepository;
use app\repositories\Company\CompanyRepository;
use LogicException;
use Throwable;
use Yii;
use yii\rest\Controller;
use yii\web\UploadedFile;

class UploadController extends Controller
{
    public function __construct(
        $id,
        $module,
        private UploadedCodeStoreService $storeService,
        private AddressRepository $addressRepository,
        private CompanyRepository $companyRepository,
        private BotSettingsRepository $botSettingsRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionStore()
    {
        try {
            $post = Yii::$app->getRequest()->getBodyParams();
            $code = UploadedFile::getInstanceByName('code');
            if ($code === null) {
                throw new LogicException('Отсутствует обязательный ключ code');
            }
            $addressId = null;
            if (!empty($post['addressId'])) {
                $addressId = (int) $post['addressId'];
            } elseif (!empty($post['address']) && !empty($post['companyKey'])) {
                $company = $this->companyRepository->getAllCompany();
                $companyId = null;
                foreach ($company as $item) {
                    if ($item->botKey === $post['companyKey']) {
                        $companyId = $item->id;
                        break;
                    }
                }
                if ($companyId !== null) {
                    $address = $this->addressRepository->findByCompanyAndAddress($companyId, $post['address']);
                    $addressId = $address?->id;
                }
            }
            if ($addressId === null && !empty($post['companyKey'])) {
                $priorityAddress = match ($post['companyKey']) {
                    'wb', 'ozon' => 'Молодогвардейцев 25',
                    default => null,
                };
                if ($priorityAddress !== null) {
                    $companyId = null;
                    foreach ($this->companyRepository->getAllCompany() as $company) {
                        if ($company->botKey === $post['companyKey']) {
                            $companyId = $company->id;
                            break;
                        }
                    }
                    if ($companyId !== null) {
                        $address = $this->addressRepository->findByCompanyAndAddress($companyId, $priorityAddress);
                        $addressId = $address?->id;
                    }
                }
            }
            $dto = new UploadedCodeDto(
                fileName: uniqid() . '.' . $code->extension,
                companyKey: $post['companyKey'],
                status: UploadedCodeStatusEnum::AWAIT,
                chatId: $post['chatId'] ?? null,
                addressId: $addressId,
            );
            $this->storeService->execute(
                dto: $dto,
                file: $code,
            );
            http_response_code(200);
            return true;
        } catch (Throwable $e) {
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }

    public function actionBotData(): array
    {
        $firms = [];
        foreach ($this->companyRepository->getAllCompany() as $company) {
            if (empty($company->botKey)) {
                continue;
            }
            $firms[$company->botKey] = $company->name;
        }

        $addresses = [];
        foreach ($this->addressRepository->getAllWithCompany() as $address) {
            if (empty($address->companyBotKey)) {
                continue;
            }
            $addresses[$address->companyBotKey][] = [
                'id' => $address->id,
                'address' => $address->address,
            ];
        }

        return [
            'firms' => $firms,
            'address' => $addresses,
        ];
    }

    public function actionBotSettings(): array
    {
        return [
            'cutoffHour' => $this->botSettingsRepository->getCutoffHour(),
        ];
    }
}
