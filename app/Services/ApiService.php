<?php

namespace App\Services;

use App\Models\ConnectorCommand;
use App\Models\Endpoint;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

class ApiService
{
    protected Client $client;

    public function __construct()
    {
        // client will be initialized per request
    }

    /**
     * Make an API request using an Endpoint or ConnectorCommand.
     * Supports optional payload, method, and URL overrides.
     */
    public function makeRequest($endpoint, array $payload = null, string $method = null, string $url = null)
    {
        // Determine connector and retry count
        $connector = $endpoint->connector ?? $endpoint->command->connector;
        $retryCount = $endpoint->retry_count ?? 3;

        // Build Guzzle client with retry
        $this->client = $this->createClientWithRetry($retryCount, $connector);

        // Determine method, URL, headers, and parameters
        $httpMethod = $method ?? ($endpoint->request_type ?? 'GET');
        $endpointUrl = $url ?? ($endpoint->endpoint ?? '');
        $headers = json_decode($endpoint->headers ?? '[]', true) ?? [];
        $params = $payload ?? (json_decode($endpoint->params ?? '[]', true) ?? []);

        // Add auth headers if connector uses OAuth2 or API Key
        $headers = array_merge($headers, $this->buildAuthHeaders($connector));

        $options = [
            'headers' => $headers,
        ];

        if (in_array(strtoupper($httpMethod), ['POST', 'PUT'])) {
            $options['json'] = $params;
        } else {
            $options['query'] = $params;
        }

        $response = $this->client->request($httpMethod, $connector->base_url . $endpointUrl, $options);

        return $this->processResponse($response, $endpoint);
    }

    protected function createClientWithRetry(int $retryCount, $connector): Client
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push($this->retryMiddleware($retryCount));

        return new Client([
            'handler' => $handlerStack,
            'base_uri' => $connector->base_url,
            'timeout' => 30,
        ]);
    }

    protected function retryMiddleware(int $retryCount)
    {
        return Middleware::retry(
            function ($retries, $request, $response = null, RequestException $exception = null) use ($retryCount) {
                return $retries < $retryCount && ($exception || ($response && $response->getStatusCode() >= 500));
            },
            function ($retries) {
                return 1000 * $retries; // exponential backoff
            }
        );
    }

    protected function buildAuthHeaders($connector): array
    {
        switch ($connector->auth_type ?? '') {
            case 'Basic Auth':
                return ['Authorization' => 'Basic ' . base64_encode($connector->username . ':' . $connector->password)];
            case 'API Key':
                return ['Authorization' => 'Bearer ' . $connector->api_key];
            case 'OAuth2':
                return ['Authorization' => 'Bearer ' . $connector->access_token];
            default:
                return [];
        }
    }

    protected function processResponse($response, $endpoint)
    {
        $data = json_decode($response->getBody(), true);

        if (!empty($endpoint->class_name) && class_exists($endpoint->class_name)) {
            $customClass = app($endpoint->class_name);
            if (method_exists($customClass, 'mapResponse')) {
                $data = $customClass->mapResponse($data, $endpoint);
            }
        }

        return $data;
    }
}
