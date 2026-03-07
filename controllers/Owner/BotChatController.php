<?php

namespace app\controllers\Owner;

use app\controllers\Owner\abstracts\BaseOwnerController;
use app\repositories\OwnerMessage\OwnerMessageRepository;
use app\services\Bot\BotApi;
use Throwable;
use Yii;
use yii\helpers\Url;
use yii\web\Response;

class BotChatController extends BaseOwnerController
{
    public function __construct(
        $id,
        $module,
        private BotApi $botApi,
        private OwnerMessageRepository $ownerMessageRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string|Response
    {
        try {
            $phone = Yii::$app->request->getQueryParam('phone');
            $chatId = Yii::$app->request->getQueryParam('chatId');
            $page = Yii::$app->request->getQueryParam('page');
            $page = $page !== null ? max(1, (int) $page) : 1;
            $pageSize = 50;
            $result = $this->botApi->getUsers($phone, $chatId, $page, $pageSize);
            return $this->render('index', [
                'users' => $result['users'],
                'total' => $result['total'],
                'page' => $result['page'],
                'pageSize' => $result['pageSize'],
                'phone' => $phone,
                'chatId' => $chatId,
            ]);
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Url::to('/admin'));
        }
    }

    public function actionSend(): Response
    {
        try {
            $post = Yii::$app->getRequest()->getBodyParams();
            $chatId = $post['chatId'] ?? '';
            $text = $post['text'] ?? '';
            if ($chatId === '' || $text === '') {
                Yii::$app->session->setFlash('error', 'Укажите chat_id и текст сообщения');
                return $this->redirect(Url::to('/owner-chat'));
            }
            $this->botApi->sendMessage((string) $chatId, (string) $text);
            $ownerId = Yii::$app->user->identity?->getId();
            $this->ownerMessageRepository->create((int) $chatId, (string) $text, $ownerId);
            Yii::$app->session->setFlash('success', 'Сообщение отправлено');
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Url::to('/owner-chat/' . $chatId));
    }

    public function actionSync(): Response
    {
        try {
            $this->botApi->syncUsers();
            Yii::$app->session->setFlash('success', 'Синхронизация запущена');
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Url::to('/owner-chat'));
    }

    public function actionChat(int $chatId): string|Response
    {
        try {
            $messages = $this->ownerMessageRepository->getByChatId($chatId);
            return $this->render('chat', [
                'chatId' => $chatId,
                'messages' => $messages,
            ]);
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Url::to('/owner-chat'));
        }
    }

    public function getViewPath(): string
    {
        return Yii::getAlias('@app/views/owner/bot-chat');
    }
}
