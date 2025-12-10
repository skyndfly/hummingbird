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
        $this->client->post('/issued', [
            'json' => [
                'chatId' => $id,
                'status' => $status,
                'createdAt' => $createdAt->format('d-m-Y H:i:s'),
            ],
        ]);
    }
}