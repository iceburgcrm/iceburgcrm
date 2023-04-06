<?php
namespace App\Http\Integrations;

use App\Models\Connector;
use Saloon\Http\SoloRequest;
use Saloon\Enums\Method;
use Saloon\Traits\Plugins\AcceptsJson;

class GenericEndpoint
{

    public function __construct($id){
        Connector::with('endpoints')->first();
        $this->defaultBaseUrl=$baseUrl;
        $this->defaultEndpointUrl=$endpointUrl;
        $this->defaultConfig=$config;
        $this->defaultHeaders=$headers;
    }

    public function resolveEndpoint(): string
    {
        return $this->defaultBaseUrl;
    }

}
