<?php

namespace app\controllers\Owner;

use app\controllers\Owner\abstracts\BaseOwnerController;
use app\forms\BotSettings\BotSettingsForm;
use app\repositories\BotSettings\BotSettingsRepository;
use Throwable;
use Yii;
use yii\helpers\Url;
use yii\web\Response;

class BotSettingsController extends BaseOwnerController
{
    public function __construct(
        $id,
        $module,
        private BotSettingsRepository $botSettingsRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string|Response
    {
        try {
            $form = new BotSettingsForm();
            $form->cutoffHour = $this->botSettingsRepository->getCutoffHour();
            return $this->render('index', [
                'formModel' => $form,
            ]);
        } catch (Throwable $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
            return $this->redirect(Url::to('/admin'));
        }
    }

    public function actionUpdate(): Response
    {
        try {
            $post = Yii::$app->getRequest()->getBodyParams();
            $form = new BotSettingsForm();
            if ($form->load($post) && $form->validate()) {
                $this->botSettingsRepository->updateCutoffHour($form->cutoffHour);
                Yii::$app->getSession()->setFlash('success', 'Настройки сохранены');
            } else {
                Yii::$app->getSession()->setFlash('error', array_values($form->getFirstErrors())[0]);
            }
        } catch (Throwable $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Url::to('/owner-bot-settings'));
    }

    public function getViewPath(): string
    {
        return Yii::getAlias('@app/views/owner/bot-settings');
    }
}
