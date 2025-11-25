<?php

namespace app\rest;

use app\services\UploadCode\dto\UploadedCodeDto;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use app\services\UploadCode\UploadedCodeStoreService;
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
            $dto = new UploadedCodeDto(
                fileName: uniqid() . '.' . $code->extension,
                companyKey: $post['companyKey'],
                status: UploadedCodeStatusEnum::AWAIT,
                chatId: $post['chatId'],
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
}