<?php

namespace app\services\Disk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use RuntimeException;

class YandexDiskApi
{
    private const string BASE_FOLDER = 'delivery_bot';
    private Client $client;

    public function __construct(private readonly string $token)
    {
        $this->client = new Client([
            'headers' => [
                'Authorization' => 'OAuth ' . $this->token,
            ],
        ]);
        $this->createBaseFolder();
    }

    public function ensurePath(array $segments): void
    {
        $path = '';
        foreach ($segments as $segment) {
            $segment = trim((string) $segment);
            if ($segment === '') {
                continue;
            }
            $path = $path === '' ? $segment : $path . '/' . $segment;
            $this->createFolder($path);
        }
    }

    public function uploadFile(string $localPath, string $remotePath): void
    {
        $href = $this->getUploadHref($remotePath);
        $this->client->request('PUT', $href, [
            'body' => fopen($localPath, 'rb'),
        ]);
    }

    private function getUploadHref(string $path): string
    {
        $response = $this->client->request(
            'GET',
            'https://cloud-api.yandex.net/v1/disk/resources/upload?path=' . $this->encodePath(self::BASE_FOLDER . '/' . $path) . '&overwrite=true'
        );
        $payload = json_decode((string) $response->getBody(), true);
        if (!is_array($payload) || empty($payload['href'])) {
            throw new RuntimeException('Invalid Yandex Disk upload response');
        }
        return $payload['href'];
    }

    private function createFolder(string $folderName): void
    {
        try {
            $this->client->request(
                'PUT',
                'https://cloud-api.yandex.net/v1/disk/resources?path=' . $this->encodePath(self::BASE_FOLDER . '/' . $folderName)
            );
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode !== 409) {
                throw $e;
            }
        }
    }

    private function createBaseFolder(): void
    {
        try {
            $this->client->request(
                'GET',
                'https://cloud-api.yandex.net/v1/disk/resources?path=' . $this->encodePath(self::BASE_FOLDER)
            );
        } catch (ClientException $e) {
            $status = $e->getResponse()->getStatusCode();
            if ($status == 404) {
                $this->client->request(
                    'PUT',
                    'https://cloud-api.yandex.net/v1/disk/resources?path=' . $this->encodePath(self::BASE_FOLDER)
                );
                return;
            }
            throw $e;
        }
    }

    private function encodePath(string $path): string
    {
        $parts = array_map('rawurlencode', explode('/', $path));
        return implode('/', $parts);
    }
}
