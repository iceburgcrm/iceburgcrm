<?php

namespace App\Http\Integrations\Connectors\Mappings;

class JokesEndpoint extends GenericEndpoint
{
    public function __construct($id)
    {
        parent::__construct($id);
    }

    public function header(): string
    {
        return parent::header();
    }

    public function run()
    {
        return parent::run();
    }

    public function mapping()
    {
        return parent::mapping();
    }
}
