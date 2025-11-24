<?php

namespace app\controllers\Point\abstracts;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\IdentityInterface;

/**
 * Базовый контроллер для всех контроллеров, требующих роль 'owner'
 */
class BasePointController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['owner'],
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
}