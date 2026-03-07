<?php

namespace app\controllers;

use app\auth\dto\UserIdentityDto;
use app\auth\UserIdentity;
use app\filters\Code\CodeFilter;
use app\forms\Code\CreateCodeForm;
use app\forms\User\ChangeLoginForm;
use app\repositories\Category\CategoryRepository;
use app\repositories\User\UserRepository;
use app\services\Code\FindCodeService;
use DomainException;
use Throwable;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\IdentityInterface;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\forms\User\ChangePasswordForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    private UserRepository $userRepository;
    private FindCodeService $findCodeService;
    private CategoryRepository $categoryRepository;

    public function __construct(
        $id,
        $module,
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        FindCodeService $findCodeService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->findCodeService = $findCodeService;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'index', 'change-password', 'change-login', 'settings'],
                'rules' => [
                    [
                        'actions' => ['logout', 'index', 'change-password', 'change-login', 'settings'],
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

    /**
     * Группированный поиск кодов
     */
    public function actionIndex(): Response|string
    {
        try {
            $identity = Yii::$app->user;
            if ($identity->can('point')){
                return $this->redirect('/issued-point/wb');
            }

            $formModel = new CreateCodeForm();
            $getParams = Yii::$app->request->getQueryParams();
            $filter = new CodeFilter();
            $filter->load($getParams);
            $codes = $this->findCodeService->execute($filter);


            return $this->render(view: 'index', params: [
                'formModel' => $formModel,
                'filterModel' => $filter,
                'codes' => $codes,
                'searchText' => empty($codes) && !empty($filter->code) ? 'Код не найден 😿' : '',
                'categories' => $this->categoryRepository->getAllAsMap()
            ]);

        } catch (Throwable $e) {
            Yii::error([
                'type' => 'SiteControllerError',
                'exception' => $e,
            ]);
            return $this->render(
                view: 'error',
                params: ['message' => $e]
            );
        }
    }

    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/admin']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/admin']);
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

    public function actionChangePassword(): Response|string
    {
        $form = new ChangePasswordForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $identity = Yii::$app->user->identity;
            if ($identity === null) {
                return $this->redirect(['/site/login']);
            }
            $user = $this->userRepository->getById($identity->getId());
            if ($user === null) {
                Yii::$app->session->setFlash('error', 'Пользователь не найден');
                return $this->redirect(['/site/change-password']);
            }
            if (!Yii::$app->security->validatePassword($form->oldPassword, $user->password)) {
                Yii::$app->session->setFlash('error', 'Неверный старый пароль');
                return $this->redirect(['/site/change-password']);
            }
            $this->userRepository->updatePasswordById($user->id, $form->newPassword);
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('success', 'Пароль изменен. Войдите снова.');
            return $this->redirect(['/site/login']);
        }

        return $this->render('change-password', [
            'model' => $form,
        ]);
    }

    public function actionSettings(): string
    {
        return $this->render('settings');
    }

    public function actionChangeLogin(): Response|string
    {
        $form = new ChangeLoginForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $identity = Yii::$app->user->identity;
            if ($identity === null) {
                return $this->redirect(['/site/login']);
            }
            $user = $this->userRepository->getById($identity->getId());
            if ($user === null) {
                Yii::$app->session->setFlash('error', 'Пользователь не найден');
                return $this->redirect(['/site/change-login']);
            }
            if ($form->oldUsername !== $user->username) {
                Yii::$app->session->setFlash('error', 'Неверный старый логин');
                return $this->redirect(['/site/change-login']);
            }
            if ($form->newUsername === $user->username) {
                Yii::$app->session->setFlash('error', 'Новый логин совпадает со старым');
                return $this->redirect(['/site/change-login']);
            }
            $existing = $this->userRepository->getByUsername($form->newUsername);
            if ($existing !== null && $existing->id !== $user->id) {
                Yii::$app->session->setFlash('error', 'Логин уже занят');
                return $this->redirect(['/site/change-login']);
            }
            $this->userRepository->updateUsernameById($user->id, $form->newUsername);
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('success', 'Логин изменен. Войдите снова.');
            return $this->redirect(['/site/login']);
        }

        return $this->render('change-login', [
            'model' => $form,
        ]);
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
