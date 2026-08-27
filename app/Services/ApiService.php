<?php

namespace App\Services;

use App\Models\ConnectorCommand;
use App\Models\Endpoint;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use RuntimeException;

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

        // Determine method, URL, headers, and parameters
        $httpMethod = $method ?? ($endpoint->request_type ?? 'GET');
        $endpointUrl = $url ?? ($endpoint->endpoint ?? '');
        $requestUrl = $this->buildRequestUrl($connector->base_url, $endpointUrl);

        // Build Guzzle client with retry
        $this->client = $this->createClientWithRetry($retryCount);
        $headers = json_decode($endpoint->headers ?? '[]', true) ?? [];
        $params = $payload ?? (json_decode($endpoint->params ?? '[]', true) ?? []);

        // Add auth headers if connector uses OAuth2 or API Key
        $headers = array_merge($headers, $this->buildAuthHeaders($connector));

        $options = [
            'headers' => $headers,
            'allow_redirects' => false,
        ];

        if (in_array(strtoupper($httpMethod), ['POST', 'PUT'])) {
            $options['json'] = $params;
        } else {
            $options['query'] = $params;
        }

        $response = $this->client->request($httpMethod, $requestUrl, $options);

        return $this->processResponse($response, $endpoint);
    }

    protected function createClientWithRetry(int $retryCount): Client
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push($this->retryMiddleware($retryCount));

        return new Client([
            'handler' => $handlerStack,
            'timeout' => 30,
        ]);
    }

    protected function buildRequestUrl(?string $baseUrl, ?string $endpointUrl): string
    {
        $baseUrl = trim((string) $baseUrl);
        $endpointUrl = trim((string) $endpointUrl);
        $url = $this->isAbsoluteUrl($endpointUrl)
            ? $endpointUrl
            : rtrim($baseUrl, '/').'/'.ltrim($endpointUrl, '/');

        $this->assertAllowedOutboundUrl($url);

        return $url;
    }

    protected function isAbsoluteUrl(string $url): bool
    {
        return in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
    }

    protected function assertAllowedOutboundUrl(string $url): void
    {
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower(trim($parts['host'] ?? '', '[]'));

        if (!in_array($scheme, ['http', 'https'], true) || $host === '') {
            throw new RuntimeException('Connector URL must be an absolute HTTP or HTTPS URL.');
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new RuntimeException('Connector URL cannot include embedded credentials.');
        }

        if ($host === 'localhost' || str_ends_with($host, '.localhost')) {
            throw new RuntimeException('Connector URL cannot target localhost.');
        }

        foreach ($this->resolveHostIps($host) as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw new RuntimeException('Connector URL cannot target private or reserved networks.');
            }
        }
    }

    protected function resolveHostIps(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return [$host];
        }

        $records = @dns_get_record($host, DNS_A + DNS_AAAA) ?: [];
        $ips = [];
        foreach ($records as $record) {
            if (!empty($record['ip'])) {
                $ips[] = $record['ip'];
            }
            if (!empty($record['ipv6'])) {
                $ips[] = $record['ipv6'];
            }
        }

        if (empty($ips)) {
            $ips = @gethostbynamel($host) ?: [];
        }

        if (empty($ips)) {
            throw new RuntimeException('Connector URL host could not be resolved.');
        }

        return array_unique($ips);
    }

    protected function retryMiddleware(int $retryCount)
    {
        return Middleware::retry(
            function ($retries, $request, $response = null, $exception = null) use ($retryCount) {
                // You can still check for Guzzle exceptions if needed
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
