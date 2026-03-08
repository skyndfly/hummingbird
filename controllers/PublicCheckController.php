<?php

namespace app\controllers;

use app\forms\PublicCheckForm;
use app\repositories\UploadedCode\UploadedCodeRepository;
use app\services\Phone\PhoneNormalizer;
use Yii;
use yii\web\Controller;

class PublicCheckController extends Controller
{
    public $layout = false;

    public function __construct(
        $id,
        $module,
        private UploadedCodeRepository $uploadedCodeRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        $form = new PublicCheckForm();
        $results = [];

        $post = Yii::$app->request->post();
        if ($form->load($post)) {
            $form->phone = PhoneNormalizer::normalize($form->phone);
            if ($form->validate()) {
            $results = $this->uploadedCodeRepository->findAllTodayByNote($form->phone);
            }
        }

        return $this->render('index', [
            'formModel' => $form,
            'results' => $results,
        ]);
    }
}
