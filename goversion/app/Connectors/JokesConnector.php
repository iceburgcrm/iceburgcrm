<?php

namespace App\Connectors;

class JokesConnector extends BaseConnector {

    protected function getHeaders(): array {
        return ['Content-Type' => 'application/json'];
    }

    protected function getParameters(): array {
        return [];
    }

    protected function getEndpointUrl(): ?string {
        return $this->endpoint ? $this->endpoint->endpoint : null;
    }
    public function joke_without_endpoint() {
        return $this->execute(null, 'GET', '/random_joke');
    }

    public function random_ten() {
        return $this->execute();
    }

    public function random_ten_with_mapping(){
        // Call the API
        $jokes = $this->execute(); // array of jokes

        // Get module info dynamically using accounts in this example
        $module = Module::where('name', 'accounts')->first(); // 
        if (!$module) {
            throw new \Exception("Module not found");
        }

        $moduleName = $module->name;

        // Loop through jokes and save each one
        foreach ($jokes as $joke) {
            $requestData = [
                "{$moduleName}__title" => $joke['setup'] ?? '',
                "{$moduleName}__description" => $joke['punchline'] ?? '',
            ];

            // Save the record
            Module::saveRecord($module->id, $requestData);
        }

        return $jokes; 
    }
}
