<?php

namespace App\Http\Integrations;

use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Plugins\AcceptsJson;

class ApiCall extends SoloRequest
{
    protected Method $method = Method::GET;

    use AcceptsJson;

    public function __construct($baseUrl, $endpointUrl = '', $config = [], $headers = [])
    {
        $this->defaultBaseUrl = $baseUrl;
        $this->defaultEndpointUrl = $endpointUrl;
        $this->defaultConfig = $config;
        $this->defaultHeaders = $headers;
    }

    public function resolveEndpoint(): string
    {
        return $this->defaultBaseUrl;
    }
    /*
    public function resolveEndpoint(): string
    {
        return $this->defaultBaseUrl.'/'.'https://pokeapi.co/api/v2/pokemon';
    }

    public function resolveBaseUrl(): string
    {
        return $this->resolveBaseUrl();
    }


    protected function defaultHeaders(): array
    {
        return $this->defaultHeaders;
    }


    protected function defaultConfig(): array
    {
        return $this->defaultConfig;
    }

    protected function defineEndpoint(): array
    {
        return $this->defaultEndpointUrl;
    }

    */
}
