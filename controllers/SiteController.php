<?php

namespace app\controllers;

use app\auth\UserIdentity;
use app\forms\Code\CreateCodeForm;
use app\repositories\Code\CodeRepository;
use app\repositories\User\UserRepository;
use app\ui\gridTable\Code\AllCodeGridTable;
use app\ui\gridTable\GridFactory;
use DomainException;
use Throwable;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    private UserRepository $userRepository;
    private CodeRepository $codeRepository;

    public function __construct(
        $id,
        $module,
        UserRepository $userRepository,
        CodeRepository $codeRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->userRepository = $userRepository;
        $this->codeRepository = $codeRepository;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'index'],
                'rules' => [
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex(): string
    {
        $formModel = new CreateCodeForm();
        try {
            $codes = $this->codeRepository->getAll();

            $grid = GridFactory::createGrid(
                models: $codes,
                gridClass: AllCodeGridTable::class,
                pageSize: 5
            );

            return $this->render(view: 'index', params: [
                'formModel' => $formModel,
                'grid' => $grid,
            ]);
        }catch (Throwable $e){
            Yii::error([
                'type' => 'SiteControllerError',
                'exception' => $e,
            ]);
            file_put_contents('log.txt', $e);
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
            return $this->render('index', params: [
                'formModel' => $formModel
            ]);
        }
    }

    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact(): Response|string
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout(): string
    {
        return $this->render('about');
    }


    public function actionReturnToUser(): Response
    {
        $post = Yii::$app->request->post();
        if ($post['id'] === null) {
            throw new DomainException('Необходимо передать id');
        }
        $user = $this->userRepository->getById($post['id']);
        if ($user === null) {
            throw new DomainException('Пользователь не найден');
        }
        $path = Yii::$app->getSession()->get('original_path');
        Yii::$app->session->remove('original_user_id');
        Yii::$app->session->remove('original_path');
        Yii::$app->user->switchIdentity(new UserIdentity($user));
        return $this->redirect($path ?? '/');

    }
}
