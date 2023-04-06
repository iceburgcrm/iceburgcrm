<?php

namespace App\Http\Integrations\Generic\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ApiRequest extends Request
{
    /**
     * Define the HTTP method
     *
     * @var Method
     */
    public String $defaultEndpointUrl = '';

    protected Method $method = Method::GET;

    public function __construct($endpointUrl){
        $this->defaultEndpointUrl=$endpointUrl;
    }
    /**
     * Define the endpoint for the request
     *
     * @return string
     */
    public function resolveEndpoint(): string
    {
        return $this->defaultEndpointUrl;
    }
}
