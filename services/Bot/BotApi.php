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

    public function sendIssued(string $id, string $status, DateTimeImmutable $createdAt, ?string $companyName = null, ?string $address = null): void
    {
        $payload = [
            'chatId' => $id,
            'status' => $status,
            'createdAt' => $createdAt->format('d-m-Y H:i'),
            'companyName' => $companyName,
            'address' => $address,
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

    /**
     * @return array{users: array<int, array{id:int, username:string|null, phone:string|null, name:string|null}>, total:int, page:int, pageSize:int}
     */
    public function getUsers(?string $phone, ?string $chatId, int $page = 1, int $pageSize = 50): array
    {
        try {
            $query = [];
            if ($phone !== null && $phone !== '') {
                $query['phone'] = $phone;
            }
            if ($chatId !== null && $chatId !== '') {
                $query['chatId'] = $chatId;
            }
            $query['page'] = $page;
            $query['pageSize'] = $pageSize;
            $response = $this->client->get('/users', [
                'query' => $query,
            ]);
            $payload = json_decode((string) $response->getBody(), true);
            if (!is_array($payload) || !isset($payload['users']) || !is_array($payload['users'])) {
                return [
                    'users' => [],
                    'total' => 0,
                    'page' => $page,
                    'pageSize' => $pageSize,
                ];
            }
            return [
                'users' => $payload['users'],
                'total' => (int) ($payload['total'] ?? 0),
                'page' => (int) ($payload['page'] ?? $page),
                'pageSize' => (int) ($payload['pageSize'] ?? $pageSize),
            ];
        } catch (\Throwable $e) {
            \Yii::info([
                'type' => 'SendBotApi',
                'action' => 'getUsers',
                'error' => $e->getMessage(),
            ], 'bot');
            return [
                'users' => [],
                'total' => 0,
                'page' => $page,
                'pageSize' => $pageSize,
            ];
        }
    }

    public function sendMessage(string $chatId, string $text): void
    {
        try {
            $this->client->post('/message', [
                'json' => [
                    'chatId' => $chatId,
                    'text' => $text,
                ],
            ]);
        } catch (\Throwable $e) {
            \Yii::info([
                'type' => 'SendBotApi',
                'action' => 'sendMessage',
                'error' => $e->getMessage(),
            ], 'bot');
        }
    }

    public function syncUsers(): void
    {
        try {
            $this->client->post('/users/sync', [
                'json' => ['ok' => true],
            ]);
        } catch (\Throwable $e) {
            \Yii::info([
                'type' => 'SendBotApi',
                'action' => 'syncUsers',
                'error' => $e->getMessage(),
            ], 'bot');
        }
    }
}
