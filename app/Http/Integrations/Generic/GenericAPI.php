<?php

namespace App\Http\Integrations\Generic;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class GenericAPI extends Connector
{
    use AcceptsJson;
    public $defaultBaseUrl = '';

    public $defaultConfig = [];
    public $defaultHeaders = [];


     public function __construct($baseUrl, $config=[], $headers=[]){
         $this->defaultBaseUrl=$baseUrl;
         $this->defaultConfig=$config;
         $this->defaultHeaders=$headers;
     }
    /**
     * The Base URL of the API
     *
     * @return string
     */
    public function resolveBaseUrl(): string
    {
        return $this->resolveBaseUrl();
    }

    /**
     * Default headers for every request
     *
     * @return string[]
     */
    protected function defaultHeaders(): array
    {
        return $this->defaultHeaders;
    }

    /**
     * Default HTTP client options
     *
     * @return string[]
     */
    protected function defaultConfig(): array
    {
        return $this->defaultConfig;
    }


}
