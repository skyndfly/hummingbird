<?php

namespace app\controllers\Manager;

use app\controllers\Manager\abstracts\BaseManagerController;
use app\forms\Company\EditCompanyForm;
use app\repositories\Company\CompanyRepository;
use app\ui\gridTable\Company\CompanyGridTable;
use app\ui\gridTable\GridFactory;
use Exception;
use LogicException;
use Throwable;
use Yii;
use yii\helpers\Url;
use yii\web\Response;

class CompanyController extends BaseManagerController
{

    public function __construct(
        $id,
        $module,
        private CompanyRepository $companyRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        try {
            $grid = GridFactory::createGrid(
                models: $this->companyRepository->getAllCompany(),
                gridClass: CompanyGridTable::class,
            );
            return $this->render(
                view: 'company/index',
                params: [
                    'grid' => $grid,
                ]
            );
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Yii::$app->request->getReferrer());

    }

    public function actionEdit(int $companyId): string|Response
    {
        try {
            $category = $this->companyRepository->getById($companyId);
            $form = new EditCompanyForm();
            $form->id = $category->id;
            $form->name = $category->name;
            $form->botKey = $category->botKey;


            return $this->render(
                view: 'company/edit',
                params: [
                    'formModel' => $form,
                ]
            );

        } catch (Throwable $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
            return $this->redirect(Url::to('/company'));
        }

    }

    public function actionUpdate(): Response
    {
        try {
            $post = Yii::$app->getRequest()->getBodyParams();
            $form = new EditCompanyForm();
            if ($form->load($post) && $form->validate()) {
                $this->companyRepository->update(name: $form->name, botKey: $form->botKey, id: $form->id);
                Yii::$app->getSession()->setFlash('success', 'Служба доставки обновлена');
            }
        } catch (Throwable $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Url::to('/company'));
    }

}