<?php

namespace app\services\Bot;


use DateTimeImmutable;
use GuzzleHttp\Client;

class BotApi
{

    public function __construct(
        private Client $client
    ) {
        $this->client = new Client([
            'base_uri' => 'http://php-apache', // имя контейнера!
            'timeout' => 5.0,
        ]);
    }

    public function sendIssued(string $id, string $status, DateTimeImmutable $createdAt): void
    {
        $payload = [
            'chatId' => $id,
            'status' => $status,
            'createdAt' => $createdAt->format('d-m-Y H:i:s'),
        ];
        \Yii::info([
            'type' => 'SendBotApi',
            'action' => 'request',
            'payload' => $payload,
        ], 'bot');
        $this->client->post('/issued', [
            'json' => $payload,
        ]);
    }

    public function clearCache(): void
    {
        try {
            $this->client->post('/cache/clear', [
                'json' => ['ok' => true],
            ]);
        } catch (\Throwable $e) {
            \Yii::info([
                'type' => 'SendBotApi',
                'action' => 'clearCache',
                'error' => $e->getMessage(),
            ], 'bot');
        }
    }
}
