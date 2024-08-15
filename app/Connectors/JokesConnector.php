<?php

namespace App\Connectors;

use App\Models\ConnectorCommand;

class JokesConnector extends BaseConnector {

    protected function getHeaders() {
        // No authentication headers needed, just return Content-Type
        return [
            'Content-Type' => 'application/json',
        ];
    }

    protected function getParameters() {
        // Generate parameters specific to JokesConnector
        // Adjust based on the command or endpoint
        return [
            'param1' => $this->command->some_param1, // Example parameter
            'param2' => $this->command->some_param2, // Example parameter
            // Add more parameters as needed
        ];
    }

    protected function getEndpointUrl() {
        // Return the endpoint URL specific to JokesConnector
        $connector = $this->command->connector;
        return $connector->base_url . '/api/jokes'; // Customize as needed
    }

    public function get_random_joke() {
        $endpoint = $this->command->connector->base_url . '/random/joke';
        return $this->executeRequest('GET', $endpoint);
    }

    protected function executeRequest($method, $url, $payload = null) {
        $response = $this->api_service->makeRequest(new ConnectorCommand([
            'http_method' => $method,
            'headers' => $this->getHeaders(),
            'parameters' => $payload,
            'endpoint_url' => $url,
            'retry_count' => $this->command->retry_count,
        ]));

        return $response;
    }
}
