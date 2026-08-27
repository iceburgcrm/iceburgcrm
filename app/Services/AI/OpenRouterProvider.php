<?php

namespace App\Services\AI;

use Illuminate\Support\Arr;
use RuntimeException;

class OpenRouterProvider extends AbstractHttpProvider
{
    public function chat(string $content, ?string $model = null): string
    {
        $data = $this->postJson('chat/completions', $this->headers(), [
            'model' => $model ?: $this->configValue('chat_model'),
            'messages' => [
                ['role' => 'user', 'content' => $content],
            ],
        ]);

        $message = Arr::get($data, 'choices.0.message.content');

        if (!is_string($message)) {
            throw new RuntimeException('OpenRouter chat response did not include message content.');
        }

        return $message;
    }

    public function image(string $prompt, ?string $model = null, array $options = []): string
    {
        $data = $this->postJson('images', $this->headers(), array_filter([
            'model' => $model ?: $this->configValue('image_model', 'openai/gpt-image-1'),
            'prompt' => $prompt,
            'size' => Arr::get($options, 'size', config('ai.image_size', '1024x1024')),
            'n' => Arr::get($options, 'n', 1),
        ], fn ($value) => $value !== null && $value !== ''));

        $image = Arr::get($data, 'data.0.b64_json');

        if (!is_string($image)) {
            throw new RuntimeException('OpenRouter image response did not include base64 image data.');
        }

        return $image;
    }

    protected function headers(): array
    {
        $headers = [
            'Authorization' => 'Bearer '.$this->requireApiKey(),
            'Content-Type' => 'application/json',
        ];

        if (!empty($this->config['referer'])) {
            $headers['HTTP-Referer'] = $this->config['referer'];
        }

        if (!empty($this->config['title'])) {
            $headers['X-OpenRouter-Title'] = $this->config['title'];
        }

        return $headers;
    }
}
