<?php

return [
    'provider' => env('AI_PROVIDER', 'openrouter'),
    'chat_model' => env('AI_CHAT_MODEL', env('OPENROUTER_CHAT_MODEL', 'openai/gpt-4o-mini')),
    'image_provider' => env('AI_IMAGE_PROVIDER', 'openrouter'),
    'image_model' => env('AI_IMAGE_MODEL', env('OPENROUTER_IMAGE_MODEL', 'openai/gpt-image-1')),
    'image_size' => env('AI_IMAGE_SIZE', '1024x1024'),
    'request_timeout' => env('AI_REQUEST_TIMEOUT', env('OPENAI_REQUEST_TIMEOUT', 60)),

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'organization' => env('OPENAI_ORGANIZATION'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'chat_model' => env('OPENAI_CHAT_MODEL', env('AI_CHAT_MODEL', 'gpt-3.5-turbo')),
            'image_model' => env('OPENAI_IMAGE_MODEL', env('AI_IMAGE_MODEL', 'gpt-image-2')),
        ],

        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com/v1'),
            'version' => env('ANTHROPIC_VERSION', '2023-06-01'),
            'chat_model' => env('ANTHROPIC_CHAT_MODEL', env('AI_CHAT_MODEL', 'claude-sonnet-4-20250514')),
            'max_tokens' => env('ANTHROPIC_MAX_TOKENS', 4096),
        ],

        'openrouter' => [
            'api_key' => env('OPENROUTER_API_KEY'),
            'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
            'chat_model' => env('OPENROUTER_CHAT_MODEL', env('AI_CHAT_MODEL', 'openai/gpt-4o-mini')),
            'image_model' => env('OPENROUTER_IMAGE_MODEL', 'openai/gpt-image-1'),
            'referer' => env('OPENROUTER_HTTP_REFERER', env('APP_URL')),
            'title' => env('OPENROUTER_APP_TITLE', env('APP_NAME', 'Iceburg CRM')),
        ],
    ],
];
