<?php

namespace app\controllers\Owner;

use app\auth\enums\UserTypeEnum;
use app\controllers\Owner\abstracts\BaseOwnerController;
use app\filters\User\ManagerFilter;
use app\forms\User\CreateManagerForm;
use app\repositories\User\dto\UserSearchDto;
use app\services\User\UserCreateService;
use app\services\User\UserPaginateService;
use app\ui\gridTable\GridFactory;
use app\ui\gridTable\User\ManagerGridTable;
use Exception;
use Yii;
use yii\web\Response;

class OperationsOnManagerController extends BaseOwnerController
{
    private UserCreateService $userCreateService;
    private UserPaginateService $userPaginateService;

    public function __construct(
        $id,
        $module,
        UserCreateService $userCreateService,
        UserPaginateService $userPaginateService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->userCreateService = $userCreateService;
        $this->userPaginateService = $userPaginateService;
    }

    public function actionIndex(): string
    {
        $qet = Yii::$app->request->getQueryParams();
        $filter = new ManagerFilter();
        $filter->load($qet);

        $models = $this->userPaginateService->execute(
            new UserSearchDto(
                type: UserTypeEnum::MANAGER,
                username: $filter->username,
                fio: $filter->fio,
            )
        );
        $grid = GridFactory::createGrid($models, ManagerGridTable::class);
        return $this->render('index', [
            'grid' => $grid,
            'filterModel' => $filter,
        ]);
    }

    public function actionCreate(): string
    {
        $createForm = new CreateManagerForm();
        return $this->render('create', [
            'formModel' => $createForm,
        ]);
    }

    public function actionStore(): Response
    {
        try {
            $form = new CreateManagerForm();
            $post = Yii::$app->request->post();
            if ($form->load($post) && $form->validate()) {
                $this->userCreateService->execute($form->toDto());
            }
            Yii::$app->session->setFlash('success', 'Менеджер по продажам создан');
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }
}