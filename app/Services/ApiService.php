<?php

// app/Services/ApiService.php
namespace App\Services;

use App\Models\Connection;
use App\Models\ConnectorCommand;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

class ApiService
{
    protected $client;

    public function __construct(array $postData = null)
    {
        $this->postData = $postData;
    }


    public function makeRequest(ConnectorCommand $endpoint)
    {
        $connection = $endpoint->connection;

        // Handle token-based authentication
        if ($connection->auth_type === 'OAuth2' && $this->tokenExpired($connection)) {
            $this->refreshToken($connection);
        }

        $options = $this->buildRequestOptions($connection, $endpoint);

        if (in_array($endpoint->http_method, ['POST', 'PUT']) && $this->postData) {
            $options['json'] = $this->postData;  // Include postData in the request
        }

        $this->client = $this->createClientWithRetry($endpoint->retry_count);

        $response = $this->client->request(
            $endpoint->http_method,
            $connection->base_url . $endpoint->endpoint_url,
            $options
        );

        return $this->processResponse($response, $endpoint);
    }


    protected function createClientWithRetry($retryCount)
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push($this->retryMiddleware($retryCount));

        return new Client(['handler' => $handlerStack]);
    }

    protected function retryMiddleware($retryCount)
    {
        return Middleware::retry(
            function ($retries, Request $request, $response = null, RequestException $exception = null) use ($retryCount) {
                // Retry on server errors (5xx) or on network issues
                if ($retries < $retryCount && ($exception || ($response && $response->getStatusCode() >= 500))) {
                    return true;
                }
                return false;
            },
            function ($retries) {
                return 1000 * $retries;  // Exponential backoff, 1000ms (1 second) delay per retry
            }
        );
    }

    protected function tokenExpired(Connection $connection)
    {
        return Carbon::now()->greaterThan($connection->token_expires_at);
    }

    protected function refreshToken(Connection $connection)
    {
        $response = $this->client->post($connection->token_url, [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $connection->refresh_token,
                'client_id' => $connection->client_id,
                'client_secret' => $connection->client_secret,
            ],
        ]);

        $tokenData = json_decode($response->getBody(), true);

        $connection->update([
            'access_token' => $tokenData['access_token'],
            'token_expires_at' => Carbon::now()->addSeconds($tokenData['expires_in']),
        ]);
    }

    protected function buildRequestOptions(Connection $connection, ConnectorCommand $endpoint)
    {
        $options = [
            'headers' => json_decode($endpoint->headers, true) ?? [],
            'query' => json_decode($endpoint->parameters, true) ?? [],
        ];

        if ($connection->auth_type === 'OAuth2') {
            $options['headers']['Authorization'] = 'Bearer ' . $connection->access_token;
        } elseif ($connection->auth_type === 'API Key') {
            $options['headers']['Authorization'] = 'API Key ' . $connection->api_key;
        } elseif ($connection->auth_type === 'Basic Auth') {
            $options['auth'] = [$connection->username, $connection->password];
        }

        return $options;
    }

    protected function processResponse($response, ConnectorCommand $endpoint)
    {
        $data = json_decode($response->getBody(), true);

        // Check if a custom class is defined in the endpoint
        if ($endpoint->class_name) {
            $customClass = app($endpoint->class_name);
            if (method_exists($customClass, 'mapResponse')) {
                $data = $customClass->mapResponse($data, $endpoint);
            }
        }

        return $data;
    }
}
