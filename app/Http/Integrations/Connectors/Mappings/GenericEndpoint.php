<?php
namespace App\Http\Integrations\Connectors\Mappings;

use App\Http\Integrations\ApiCall;
use App\Models\Connector;
use App\Models\Endpoint;

class GenericEndpoint
{
    public Connector|null $connector;
    public \App\Http\Integrations\ApiCall|null $apiCall;
    public function __construct($id){
        $this->connector= Endpoint::where('id', $id)->with('connector')->first();
        $this->apiCall=new ApiCall();
    }

    public function header(): string
    {
        return $this->defaultBaseUrl;
    }

    public function run() {

    }

    public function mapping() {

    }


}
