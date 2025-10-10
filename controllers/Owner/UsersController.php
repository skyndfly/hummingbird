<?php

namespace app\controllers\Owner;

use app\auth\UserIdentity;
use app\controllers\Owner\abstracts\BaseOwnerController;
use app\repositories\User\UserRepository;
use app\ui\gridTable\GridFactory;
use app\ui\gridTable\User\UserGridTable;
use DomainException;
use Yii;
use yii\web\Response;

class UsersController extends BaseOwnerController
{
    private UserRepository $userRepository;

    public function __construct(
        $id,
        $module,
        UserRepository $userRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->userRepository = $userRepository;
    }

    public function actionIndex(): string
    {
        $grid = GridFactory::createGrid(
            $this->userRepository->getAll(),
            UserGridTable::class
        );
        return $this->render('index', [
            'grid' => $grid
        ]);
    }

    public function actionLoginAsUser(): Response
    {
        $post = Yii::$app->request->post();
        if (!isset($post['id'])) {
            throw new DomainException('Необходимо передать id');
        }
        $user = $this->userRepository->getById($post['id']);
        if ($user === null) {
            throw new DomainException('Пользователь не найден');
        }

        if (!Yii::$app->getSession()->has('original_user_id')) {
            Yii::$app->getSession()->set('original_user_id', $this->getIdentity()->getId());
            Yii::$app->getSession()->set('original_path', Yii::$app->getRequest()->getReferrer());
        }

        Yii::$app->user->switchIdentity(new UserIdentity($user));

        return $this->redirect('/');
    }

}