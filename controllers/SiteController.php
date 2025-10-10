<?php

namespace app\controllers;

use app\auth\UserIdentity;
use app\repositories\User\UserRepository;
use DomainException;
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

    public function __construct(
        $id,
        $module,
        UserRepository $userRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->userRepository = $userRepository;
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
        return $this->render('index');
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
