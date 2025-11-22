<?php

namespace app\controllers\Manager;

use app\controllers\Manager\abstracts\BaseManagerController;
use app\repositories\Company\CompanyRepository;
use app\ui\gridTable\Company\CompanyGridTable;
use app\ui\gridTable\GridFactory;
use Exception;
use Yii;

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


}