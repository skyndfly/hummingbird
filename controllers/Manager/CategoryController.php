<?php

namespace app\controllers\Manager;

use app\controllers\Manager\abstracts\BaseManagerController;
use app\forms\Category\CreateCategoryForm;
use app\repositories\Category\CategoryRepository;
use Throwable;
use Yii;
use yii\web\Response;

class CategoryController extends BaseManagerController
{
    private CategoryRepository $categoryRepository;
    public function __construct(
        $id,
        $module,
        CategoryRepository $categoryRepository,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->categoryRepository = $categoryRepository;
    }

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

    public function actionStore(): Response
    {
        try {
            $form = new CreateCategoryForm();
            $post = Yii::$app->getRequest()->getBodyParams();
            if($form->load($post) && $form->validate()) {
                $this->categoryRepository->create($form->name);
                Yii::$app->getSession()->setFlash('success', 'Новое место хранения создано.');
            }
        }catch (Throwable $exception){
            Yii::$app->getSession()->setFlash('error', $exception->getMessage());
        }
        return $this->redirect(Yii::$app->getRequest()->getReferrer());
    }
}