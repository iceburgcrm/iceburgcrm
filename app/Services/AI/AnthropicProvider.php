<?php

namespace App\Services\AI;

use Illuminate\Support\Arr;
use RuntimeException;

class AnthropicProvider extends AbstractHttpProvider
{
    public function chat(string $content, ?string $model = null): string
    {
        $payload = [
            'model' => $model ?: $this->configValue('chat_model'),
            'max_tokens' => (int) $this->configValue('max_tokens', 4096),
            'messages' => [
                ['role' => 'user', 'content' => $content],
            ],
        ];

        $data = $this->postJson('messages', [
            'x-api-key' => $this->requireApiKey(),
            'anthropic-version' => $this->configValue('version', '2023-06-01'),
            'Content-Type' => 'application/json',
        ], $payload);

        $text = collect(Arr::get($data, 'content', []))
            ->where('type', 'text')
            ->pluck('text')
            ->implode('');

        if ($text === '') {
            throw new RuntimeException('Anthropic response did not include text content.');
        }

        return $text;
    }
}
