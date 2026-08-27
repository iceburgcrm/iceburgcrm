<?php

namespace App\Services\AI;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use RuntimeException;

class OpenAIProvider extends AbstractHttpProvider
{
    public function chat(string $content, ?string $model = null): string
    {
        $headers = $this->headers();
        $payload = [
            'model' => $model ?: $this->configValue('chat_model', config('ai.chat_model')),
            'messages' => [
                ['role' => 'user', 'content' => $content],
            ],
        ];

        $data = $this->postJson('chat/completions', $headers, $payload);
        $message = Arr::get($data, 'choices.0.message.content');

        if (!is_string($message)) {
            throw new RuntimeException('OpenAI chat response did not include message content.');
        }

        return $message;
    }

    public function image(string $prompt, ?string $model = null, array $options = []): string
    {
        $payload = array_filter([
            'model' => $model ?: $this->configValue('image_model', config('ai.image_model')),
            'prompt' => $prompt,
            'size' => Arr::get($options, 'size', config('ai.image_size', '1024x1024')),
            'n' => Arr::get($options, 'n', 1),
        ], fn ($value) => $value !== null && $value !== '');

        $data = $this->postJson('images/generations', $this->headers(), $payload);
        $image = Arr::get($data, 'data.0.b64_json');

        if (is_string($image)) {
            return $image;
        }

        $url = Arr::get($data, 'data.0.url');
        if (is_string($url)) {
            try {
                return base64_encode((string) $this->client->get($url)->getBody());
            } catch (GuzzleException $exception) {
                throw new RuntimeException($exception->getMessage(), 0, $exception);
            }
        }

        throw new RuntimeException('OpenAI image response did not include base64 image data.');
    }

    protected function headers(): array
    {
        $headers = [
            'Authorization' => 'Bearer '.$this->requireApiKey(),
            'Content-Type' => 'application/json',
        ];

        if (!empty($this->config['organization'])) {
            $headers['OpenAI-Organization'] = $this->config['organization'];
        }

        return $headers;
    }
}
