<?php

namespace App\Services\AI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use RuntimeException;

abstract class AbstractHttpProvider implements AIProvider
{
    protected Client $client;

    public function __construct(protected array $config = [])
    {
        $this->client = new Client([
            'base_uri' => rtrim((string) Arr::get($this->config, 'base_url'), '/').'/',
            'timeout' => (float) config('ai.request_timeout', 60),
        ]);
    }

    public function isConfigured(): bool
    {
        return !empty($this->config['api_key']);
    }

    public function image(string $prompt, ?string $model = null, array $options = []): string
    {
        throw new RuntimeException(class_basename(static::class).' does not support image generation.');
    }

    protected function postJson(string $uri, array $headers, array $payload): array
    {
        try {
            $response = $this->client->post(ltrim($uri, '/'), [
                'headers' => $headers,
                'json' => $payload,
            ]);
        } catch (GuzzleException $exception) {
            throw new RuntimeException($exception->getMessage(), 0, $exception);
        }

        $data = json_decode((string) $response->getBody(), true);
        if (!is_array($data)) {
            throw new RuntimeException('AI provider returned an invalid JSON response.');
        }

        return $data;
    }

    protected function requireApiKey(): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException(class_basename(static::class).' is missing an API key.');
        }

        return (string) $this->config['api_key'];
    }

    protected function configValue(string $key, mixed $fallback = null): mixed
    {
        $value = Arr::get($this->config, $key);

        return ($value === null || $value === '') ? $fallback : $value;
    }
}
