<?php

namespace app\controllers\Manager;

use app\controllers\Manager\abstracts\BaseManagerController;
use app\forms\Category\CreateCategoryForm;
use Throwable;
use Yii;

class CategoryController extends BaseManagerController
{
    public function actionIndex(): string
    {
        $formModel = new CreateCategoryForm();
        try {
            return $this->render(
                view: 'category/index',
                params: [
                    'formModel' => $formModel,
                ]
            );
        } catch (Throwable $exception) {
            Yii::$app->getSession()->setFlash('error', $exception->getMessage());
            return $this->render(
                view: 'category/index',
                params: [
                    'formModel' => $formModel,
                ]
            );
        }

    }
}