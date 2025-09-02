<?php

namespace App\Connectors;

class JokesConnector extends BaseConnector {

    protected function getHeaders() {
        return ['Content-Type' => 'application/json'];
    }

    protected function getParameters() {
        return [];
    }

    protected function getEndpointUrl() {
        return $this->command->connector->base_url . '/api/jokes';
    }

    public function get_random_joke() {
        return $this->execute(null, 'GET', $this->command->connector->base_url . '/random/joke');
    }
}
