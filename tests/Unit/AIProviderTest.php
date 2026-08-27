<?php

namespace Tests\Unit;

use App\Services\AI\AnthropicProvider;
use App\Services\AI\AIManager;
use App\Services\AI\OpenAIProvider;
use App\Services\AI\OpenRouterProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use ReflectionProperty;
use Tests\TestCase;

class AIProviderTest extends TestCase
{
    public function test_manager_maps_claude_alias_to_anthropic_config(): void
    {
        config([
            'ai.providers.anthropic' => [
                'api_key' => 'test-key',
                'base_url' => 'https://api.anthropic.test/v1',
                'version' => '2023-06-01',
                'chat_model' => 'claude-test',
            ],
        ]);

        $provider = (new AIManager())->provider('claude');

        $this->assertInstanceOf(AnthropicProvider::class, $provider);
        $this->assertTrue($provider->isConfigured());
    }

    public function test_openai_chat_returns_message_content(): void
    {
        $history = [];
        $provider = new OpenAIProvider([
            'api_key' => 'test-key',
            'base_url' => 'https://api.openai.test/v1',
            'chat_model' => 'gpt-test',
        ]);
        $this->mockProviderClient($provider, [
            new Response(200, [], json_encode([
                'choices' => [
                    ['message' => ['content' => '{"ok":true}']],
                ],
            ])),
        ], $history);

        $this->assertSame('{"ok":true}', $provider->chat('hello'));

        $payload = json_decode((string) $history[0]['request']->getBody(), true);
        $this->assertSame('gpt-test', $payload['model']);
        $this->assertSame('hello', $payload['messages'][0]['content']);
    }

    public function test_openai_image_returns_base64_image_data(): void
    {
        $history = [];
        $provider = new OpenAIProvider([
            'api_key' => 'test-key',
            'base_url' => 'https://api.openai.test/v1',
            'image_model' => 'gpt-image-2',
        ]);
        $this->mockProviderClient($provider, [
            new Response(200, [], json_encode([
                'data' => [
                    ['b64_json' => 'base64-image'],
                ],
            ])),
        ], $history);

        $this->assertSame('base64-image', $provider->image('make a logo'));

        $payload = json_decode((string) $history[0]['request']->getBody(), true);
        $this->assertSame('gpt-image-2', $payload['model']);
        $this->assertSame('make a logo', $payload['prompt']);
    }

    public function test_anthropic_chat_returns_text_blocks(): void
    {
        $provider = new AnthropicProvider([
            'api_key' => 'test-key',
            'base_url' => 'https://api.anthropic.test/v1',
            'version' => '2023-06-01',
            'chat_model' => 'claude-test',
        ]);
        $this->mockProviderClient($provider, [
            new Response(200, [], json_encode([
                'content' => [
                    ['type' => 'text', 'text' => '{"ok":true}'],
                ],
            ])),
        ]);

        $this->assertSame('{"ok":true}', $provider->chat('hello'));
    }

    public function test_openrouter_chat_returns_message_content(): void
    {
        $provider = new OpenRouterProvider([
            'api_key' => 'test-key',
            'base_url' => 'https://openrouter.test/api/v1',
            'chat_model' => 'openai/gpt-test',
        ]);
        $this->mockProviderClient($provider, [
            new Response(200, [], json_encode([
                'choices' => [
                    ['message' => ['content' => '{"ok":true}']],
                ],
            ])),
        ]);

        $this->assertSame('{"ok":true}', $provider->chat('hello'));
    }

    public function test_openrouter_image_returns_base64_image_data(): void
    {
        $history = [];
        $provider = new OpenRouterProvider([
            'api_key' => 'test-key',
            'base_url' => 'https://openrouter.test/api/v1',
            'image_model' => 'openai/gpt-image-1',
        ]);
        $this->mockProviderClient($provider, [
            new Response(200, [], json_encode([
                'data' => [
                    ['b64_json' => 'openrouter-base64-image'],
                ],
            ])),
        ], $history);

        $this->assertSame('openrouter-base64-image', $provider->image('make a logo'));

        $payload = json_decode((string) $history[0]['request']->getBody(), true);
        $this->assertSame('openai/gpt-image-1', $payload['model']);
        $this->assertSame('make a logo', $payload['prompt']);
    }

    private function mockProviderClient(object $provider, array $responses, array &$history = []): void
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        $client = new Client([
            'handler' => $stack,
            'base_uri' => 'https://example.test/',
        ]);

        $property = new ReflectionProperty($provider, 'client');
        $property->setAccessible(true);
        $property->setValue($provider, $client);
    }
}
