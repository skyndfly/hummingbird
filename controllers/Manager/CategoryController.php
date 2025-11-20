<?php

namespace app\controllers\Manager;

use app\controllers\Manager\abstracts\BaseManagerController;
use app\forms\Category\CreateCategoryForm;
use app\forms\Category\EditCategoryForm;
use app\repositories\Category\CategoryRepository;
use app\ui\gridTable\Category\CategoryGridTable;
use app\ui\gridTable\GridFactory;
use LogicException;
use Throwable;
use Yii;
use yii\helpers\Url;
use yii\web\Response;

class CategoryController extends BaseManagerController
{

    public function __construct(
        $id,
        $module,
        private readonly CategoryRepository $categoryRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        $formModel = new CreateCategoryForm();
        $grid = GridFactory::createGrid(
            models: $this->categoryRepository->getAll(),
            gridClass: CategoryGridTable::class
        );
        try {
            return $this->render(
                view: 'category/index',
                params: [
                    'formModel' => $formModel,
                    'grid' => $grid,
                ]
            );
        } catch (Throwable $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
            return $this->render(
                view: 'category/index',
                params: [
                    'formModel' => $formModel,
                    'grid' => $grid,
                ]
            );
        }

    }

    public function actionStore(): Response
    {
        try {
            $form = new CreateCategoryForm();
            $post = Yii::$app->getRequest()->getBodyParams();
            if ($form->load($post) && $form->validate()) {
                $this->categoryRepository->create($form->name);
                Yii::$app->getSession()->setFlash('success', 'Новое место хранения создано.');
            }
        } catch (Throwable $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Yii::$app->getRequest()->getReferrer());
    }

    public function actionEdit(int $categoryId): string|Response
    {
        try {
            $category = $this->categoryRepository->getById($categoryId);
            $form = new EditCategoryForm();
            $form->id = $category->id;
            $form->name = $category->name;


            return $this->render(
                view: 'category/edit',
                params: [
                    'formModel' => $form,
                ]
            );

        } catch (Throwable $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
            return $this->redirect(Url::to('/category'));
        }

    }

    public function actionUpdate(): Response
    {
        try {
            $post = Yii::$app->getRequest()->getBodyParams();
            $form = new EditCategoryForm();
            if ($form->load($post) && $form->validate()) {
                if ($this->categoryRepository->isNameExist(name: $form->name, id: $form->id)) {
                    throw new LogicException('Категория с таким именем уже существует');
                }
                $this->categoryRepository->updateName(name: $form->name, id: $form->id);
                Yii::$app->getSession()->setFlash('success', 'Название места хранения обновлено');
            }
        } catch (Throwable $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Url::to('/category'));
    }
}