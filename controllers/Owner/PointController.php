<?php

namespace app\controllers\Owner;


use app\controllers\Owner\abstracts\BaseOwnerController;
use app\forms\UploadedCode\ManualUploadForm;
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
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $codes = $this->uploadedCodeRepository->findAllAwaitCodeToday();
        $grid = GridFactory::createGrid(models: $codes, gridClass: UploadedCodeGridTable::class, pageSize: 50);


        return $this->render('index', [
            'grid' => $grid,
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
        return $this->render('add-code', [
            'formModel' => $formModel,
            'companyKey' => UploadedCodeCompanyKeyEnum::WB
        ]);
    }

    public function actionOzonAddCode(): string
    {
        $formModel = new ManualUploadForm();
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
                    $dto = new UploadedCodeDto(
                        fileName: uniqid() . '1535637656' . '.' . $form->image->extension,
                        companyKey: $form->companyName,
                        status: UploadedCodeStatusEnum::AWAIT,
                        chatId: '1535637656',
                        note: $form->note
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


    public function getViewPath(): string
    {
        return Yii::getAlias('@app/views/owner/point');
    }


}