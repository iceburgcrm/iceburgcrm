<?php

namespace App\Connectors;

use App\Models\ConnectorCommand;

class IceburgCRMConnector extends BaseConnector {

    protected function getHeaders() {
        // Generate headers specific to IceburgCRMConnector
        $connector = $this->command->connector;
        $headers = [
            'Content-Type' => 'application/json',
        ];

        if ($connector->auth_type === 'Basic Auth') {
            $headers['Authorization'] = 'Basic ' . base64_encode($connector->username . ':' . $connector->password);
        }

        return $headers;
    }

    protected function getParameters() {
        // Generate parameters specific to IceburgCRMConnector
        // This is an example; you can adjust based on the command or endpoint
        return [
            'param1' => $this->command->some_param1, // Example parameter
            'param2' => $this->command->some_param2, // Example parameter
            // Add more parameters as needed
        ];
    }

    protected function getEndpointUrl() {
        // Return the endpoint URL specific to IceburgCRMConnector
        $connector = $this->command->connector;
        return $connector->base_url . '/api/backup_contacts'; // Customize as needed
    }

    public function backup_contacts()
    {
        $allModules=collect($this->getAllModules());
        $moduleId = $allModules->firstWhere('label', 'Contacts')['id'] ?? null;
        $accounts=Account::orderBy('created', 'desc')->take(10)->get()->toArray();
        return $this->updateOrAddCRMRecord($moduleId, $accounts);
    }

    public function backup_accounts(){

        $allModules=collect($this->getAllModules());
        $moduleId = $allModules->firstWhere('label', 'Contacts')['id'] ?? null;
        $contacts=Contact::orderBy('created', 'desc')->take(10)->get()->toArray();
        return $this->updateOrAddCRMRecord($moduleId, $contacts);
    }

    // 1. Get All CRM Modules
    protected function getAllModules() {
        $endpoint = $this->command->connector->base_url . '/api/crm';
        return $this->executeRequest('GET', $endpoint);
    }

    // 2. Search CRM Data
    protected function searchCRMData(array $searchCriteria) {
        $endpoint = $this->command->connector->base_url . '/api/crm/search';
        $payload = json_encode($searchCriteria);
        return $this->executeRequest('GET', $endpoint, $payload);
    }

    // 3. Get a Specific CRM Module
    protected function getModuleById($moduleId) {
        $endpoint = $this->command->connector->base_url . "/api/crm/{$moduleId}";
        return $this->executeRequest('GET', $endpoint);
    }

    // 4. Update or Add a CRM Record
    protected function updateOrAddCRMRecord($moduleId, array $data) {
        $endpoint = $this->command->connector->base_url . "/api/crm/{$moduleId}";
        return $this->executeRequest('PUT', $endpoint, json_encode($data));
    }

    // 5. Delete a Record in a CRM Module
    protected function deleteCRMRecord($moduleId, array $recordIds, $type = 'module') {
        $endpoint = $this->command->connector->base_url . "/api/crm/{$moduleId}/{$type}";
        $payload = json_encode(['record_ids' => $recordIds]);
        return $this->executeRequest('DELETE', $endpoint, $payload);
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
