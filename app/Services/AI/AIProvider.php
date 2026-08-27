<?php

namespace App\Services\AI;

interface AIProvider
{
    public function isConfigured(): bool;

    public function chat(string $content, ?string $model = null): string;

    public function image(string $prompt, ?string $model = null, array $options = []): string;
}
