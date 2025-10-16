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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
            'brandLabel' => 'КолибриCRM',
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

<main id="main" class="flex-shrink-0 " role="main">
    <div class="container-fluid ">

        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <div class="mb-3">
                <?= Breadcrumbs::widget([
                        'links' => $this->params['breadcrumbs'],
                        'options' => ['class' => 'breadcrumb bg-transparent px-0'],
                ]) ?>
            </div>
        <?php endif; ?>

        <?= Alert::widget() ?>

        <div class="row g-3">
            <div class="col-12 col-md-3 order-2 order-md-1">
                <nav>
                    <ul id="menu" class="list-group mb-3">
                        <?php if (Yii::$app->user->can('owner')): ?>
                            <?= $this->render('owner-nav') ?>
                        <?php endif; ?>
                        <?= $this->render('nav') ?>
                    </ul>
                </nav>
            </div>

            <!-- Контент -->
            <div class="col-12 col-md-9 order-1 order-md-2">
                <div class="card shadow-sm border-0">
                    <div class="card-body overflow-auto">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; КолибриCRM <?= date('Y') ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
