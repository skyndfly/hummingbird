<?php

namespace app\controllers\Owner;

use app\controllers\Owner\abstracts\BaseOwnerController;
use app\forms\Address\CreateAddressForm;
use app\forms\Address\EditAddressForm;
use app\repositories\Address\AddressRepository;
use app\repositories\Company\CompanyRepository;
use app\services\Bot\BotApi;
use app\ui\gridTable\Address\AddressGridTable;
use app\ui\gridTable\GridFactory;
use Throwable;
use Yii;
use yii\helpers\Url;
use yii\web\Response;

class AddressController extends BaseOwnerController
{
    public function __construct(
        $id,
        $module,
        private AddressRepository $addressRepository,
        private CompanyRepository $companyRepository,
        private BotApi $botApi,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string|Response
    {
        try {
            $grid = GridFactory::createGrid(
                models: $this->addressRepository->getAllWithCompany(includeDeleted: true),
                gridClass: AddressGridTable::class,
            );
            return $this->render('index', [
                'grid' => $grid,
            ]);
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->getReferrer());
        }
    }

    public function actionCreate(): string|Response
    {
        try {
            $form = new CreateAddressForm();
            return $this->render('create', [
                'formModel' => $form,
                'companies' => $this->companyRepository->getAllAsMap(),
            ]);
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Url::to('/owner-address'));
        }
    }

    public function actionStore(): Response
    {
        try {
            $post = Yii::$app->getRequest()->getBodyParams();
            $form = new CreateAddressForm();
            if ($form->load($post) && $form->validate()) {
                $this->addressRepository->create(
                    companyId: $form->companyId,
                    address: $form->address
                );
                $this->botApi->clearCache();
                Yii::$app->getSession()->setFlash('success', 'Адрес создан');
            } else {
                Yii::$app->getSession()->setFlash('error', array_values($form->getFirstErrors())[0]);
            }
        } catch (Throwable $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Url::to('/owner-address'));
    }

    public function actionEdit(int $addressId): string|Response
    {
        try {
            $address = $this->addressRepository->getByIdIncludingDeleted($addressId);
            $form = new EditAddressForm();
            $form->id = $address->id;
            $form->companyId = $address->companyId;
            $form->address = $address->address;
            return $this->render('edit', [
                'formModel' => $form,
                'companies' => $this->companyRepository->getAllAsMap(),
            ]);
        } catch (Throwable $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
            return $this->redirect(Url::to('/owner-address'));
        }
    }

    public function actionUpdate(): Response
    {
        try {
            $post = Yii::$app->getRequest()->getBodyParams();
            $form = new EditAddressForm();
            if ($form->load($post) && $form->validate()) {
                $this->addressRepository->update(
                    id: $form->id,
                    companyId: $form->companyId,
                    address: $form->address
                );
                $this->botApi->clearCache();
                Yii::$app->getSession()->setFlash('success', 'Адрес обновлен');
            } else {
                Yii::$app->getSession()->setFlash('error', array_values($form->getFirstErrors())[0]);
            }
        } catch (Throwable $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Url::to('/owner-address'));
    }

    public function actionDelete(): Response
    {
        try {
            $post = Yii::$app->getRequest()->getBodyParams();
            $addressId = isset($post['addressId']) ? (int) $post['addressId'] : 0;
            if ($addressId > 0) {
                $this->addressRepository->softDelete($addressId);
                $this->botApi->clearCache();
                Yii::$app->getSession()->setFlash('success', 'Адрес удален');
            }
        } catch (Throwable $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Url::to('/owner-address'));
    }

    public function actionRestore(): Response
    {
        try {
            $post = Yii::$app->getRequest()->getBodyParams();
            $addressId = isset($post['addressId']) ? (int) $post['addressId'] : 0;
            if ($addressId > 0) {
                $this->addressRepository->restore($addressId);
                $this->botApi->clearCache();
                Yii::$app->getSession()->setFlash('success', 'Адрес восстановлен');
            }
        } catch (Throwable $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Url::to('/owner-address'));
    }

    public function getViewPath(): string
    {
        return Yii::getAlias('@app/views/owner/address');
    }
}
