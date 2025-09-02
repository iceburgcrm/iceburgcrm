<?php

namespace App\Connectors;

use App\Services\ApiService;
use App\Models\ConnectorCommand;
use App\Models\Endpoint;

abstract class BaseConnector {
    protected $api_service;
    protected $command;
    protected $endpoint;
    protected $connector;

    public function __construct(ConnectorCommand $command) {
        $this->command   = $command;
        $this->endpoint  = $command->endpoint;   // may be null if command is connector-level
        $this->connector = $command->connector;  // always exists
        $this->api_service = new ApiService($this->connector);
    }

    protected function getHeaders() {
        return $this->endpoint ? json_decode($this->endpoint->headers, true) ?? [] : [];
    }

    protected function getParameters() {
        return $this->endpoint ? json_decode($this->endpoint->params, true) ?? [] : [];
    }

    protected function getEndpointUrl() {
        return $this->endpoint ? $this->endpoint->endpoint : null;
    }

    protected function getHttpMethod() {
        return $this->endpoint ? ($this->endpoint->request_type ?? 'GET') : 'GET';
    }

    public function execute(array $payload = null, string $method = null, string $url = null) {
        if (!$this->endpoint) {
            throw new \Exception("Command {$this->command->name} has no endpoint attached");
        }
        return $this->api_service->makeRequest($this->endpoint, $payload, $method, $url);
    }
}
