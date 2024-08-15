<?php

namespace App\Connectors;

use App\Services\ApiService;
use App\Models\ConnectorCommand;

abstract class BaseConnector {
    protected $api_service;
    protected $command;

    public function __construct(ConnectorCommand $command) {
        $this->api_service = new ApiService();
        $this->command = $command;
    }

    abstract protected function getHeaders();
    abstract protected function getParameters();
    abstract protected function getEndpointUrl();

    public function execute() {
        // Fetch the connector associated with this command
        $connector = $this->command->connector;

        // Prepare the data required for the API request
        $endpoint = [
            'http_method' => $this->getHttpMethod(), // Method specific to the subclass
            'headers' => $this->getHeaders(),
            'parameters' => $this->getParameters(),
            'endpoint_url' => $this->getEndpointUrl(),
            'retry_count' => $this->command->retry_count,
            'class_name' => get_class($this),  // Or specify a different class name if needed
        ];

        // Make the API request using ApiService
        $response = $this->api_service->makeRequest(new ConnectorCommand($endpoint));

        return $response;
    }

    protected function getHttpMethod() {
        // Default HTTP method, can be overridden by subclasses
        return 'POST';
    }
}
