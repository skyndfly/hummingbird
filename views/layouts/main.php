<?php

/** @var yii\web\View $this */

/** @var string $content */

use app\assets\AppAsset;
use app\auth\UserIdentity;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

/** @var UserIdentity $userIdentity */
$userIdentity = Yii::$app->user->getIdentity();
AppAsset::register($this);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => [
                    !Yii::$app->user->isGuest ? ['label' => $userIdentity->user->userInfoDto->firstName . ' ' . $userIdentity->user->userInfoDto->name, 'url' => ['/site/index']] : '',
                    Yii::$app->user->isGuest
                            ? ['label' => 'Войти', 'url' => ['/site/login']]
                            : '<li class="nav-item">'

                            . Html::beginForm(['/site/logout'])
                            . Html::submitButton(
                                    'Выйти',
                                    ['class' => 'nav-link btn btn-link logout']
                            )
                            . Html::endForm()
                            . '</li>'
            ]
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <div class="row">
            <div class="col-3">
                <nav>
                    <ul id="menu" class="list-group">
                        <?php if (Yii::$app->user->can('owner')): ?>
                            <?= $this->render('owner-nav') ?>
                        <?php endif; ?>
                        <li class="list-group-item"><a href="/">Все кода</a></li>
                        <li class="list-group-item"><a href="/add-code/create">Добавить код</a></li>
                        <?php if(Yii::$app->session->has('original_user_id')):?>
                            <li class="list-group-item">
                                <form action="/site/return-to-user" method="post">
                                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                                    <input type="hidden" name="id" value="<?= Yii::$app->session->get('original_user_id') ?>">
                                    <button type="submit" class="btn btn-link p-0 m-0 d-flex align-items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door-fill" viewBox="0 0 16 16">
                                            <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5"/>
                                        </svg>
                                        Вернуться в свой аккаунт
                                    </button>
                                </form>
                            </li>
                        <?php endif;?>
                    </ul>
                </nav>
            </div>
            <div class="col-8">
                <?= $content ?>
            </div>
        </div>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; КолибриCRM <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
