<?php

namespace App\Connectors;

use App\Services\ApiService;
use App\Models\ConnectorCommand;
use App\Models\Endpoint;

abstract class BaseConnector {
    protected ApiService $api_service;
    protected ConnectorCommand $command;
    protected ?Endpoint $endpoint;
    protected $connector;

    public function __construct(ConnectorCommand $command) {
        $this->command   = $command;
        $this->endpoint  = $command->endpoint;   // may be null if connector-level
        $this->connector = $command->connector;  // always exists
        $this->api_service = new ApiService();
    }

    protected function getHeaders(): array {
        return $this->endpoint ? json_decode($this->endpoint->headers, true) ?? [] : [];
    }

    protected function getParameters(): array {
        return $this->endpoint ? json_decode($this->endpoint->params, true) ?? [] : [];
    }

    protected function getEndpointUrl(): ?string {
        return $this->endpoint ? $this->endpoint->endpoint : null;
    }

    protected function getHttpMethod(): string {
        return $this->endpoint ? ($this->endpoint->request_type ?? 'GET') : 'GET';
    }

    public function execute(array $payload = null, string $method = null, string $url = null)
    {
        // Pass the Endpoint model (or connector-level endpoint) to ApiService
        return $this->api_service->makeRequest(
            $this->endpoint ?? $this->command,
            $payload,
            $method ?? $this->getHttpMethod(),
            $url ?? $this->getEndpointUrl()
        );
    }
}
