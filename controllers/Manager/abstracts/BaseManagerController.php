<?php

namespace app\controllers\Manager\abstracts;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\IdentityInterface;

/**
 * Базовый контроллер для всех контроллеров, требующих роль 'manager'
 */
class BaseManagerController extends Controller
{

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manager', 'owner'],
                    ]
                ],
                'denyCallback' => function($rule, $action) {
                    Yii::$app->session->setFlash('error', 'Доступ запрещен.');
                    return $this->redirect(Yii::$app->homeUrl);
                }
            ]
        ];
    }
    /**
     * return UserIdentityDto
     */
    public function getIdentity(): IdentityInterface
    {
        return Yii::$app->user->identity;
    }
    public function getViewPath(): false|string|null
    {
        return Yii::getAlias('@app/views/manager/code');
    }
}