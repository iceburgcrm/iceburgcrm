<?php

namespace App\Services\AI;

use InvalidArgumentException;

class AIManager
{
    public function chat(string $content, ?string $model = null, ?string $provider = null): string
    {
        return $this->provider($provider)->chat($content, $model);
    }

    public function image(string $prompt, ?string $model = null, ?string $provider = null, array $options = []): string
    {
        return $this->provider($provider ?: config('ai.image_provider'))->image($prompt, $model, $options);
    }

    public function enabled(?string $provider = null): bool
    {
        return $this->provider($provider)->isConfigured();
    }

    public function provider(?string $provider = null): AIProvider
    {
        $provider = strtolower($provider ?: config('ai.provider', 'openai'));
        $provider = $provider === 'claude' ? 'anthropic' : $provider;
        $config = config("ai.providers.$provider", []);

        return match ($provider) {
            'openai' => new OpenAIProvider($config),
            'anthropic' => new AnthropicProvider($config),
            'openrouter' => new OpenRouterProvider($config),
            default => throw new InvalidArgumentException("Unsupported AI provider [$provider]."),
        };
    }
}
