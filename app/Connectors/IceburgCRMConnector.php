<?php

namespace App\Connectors;

class IceburgCRMConnector extends BaseConnector {

    protected function getHeaders() {
        return array_merge(
            ['Content-Type' => 'application/json'],
            $this->buildAuthHeaders($this->command->connector)
        );
    }

    protected function getParameters() {
        return []; // default, or derive from $this->command
    }

    protected function getEndpointUrl() {
        return $this->command->connector->base_url . '/api/backup_contacts';
    }

    public function backup_contacts() {
        $allModules = collect($this->getAllModules());
        $moduleId   = $allModules->firstWhere('label', 'Contacts')['id'] ?? null;

        $accounts = Account::orderBy('created', 'desc')->take(10)->get()->toArray();
        return $this->updateOrAddCRMRecord($moduleId, $accounts);
    }

    public function backup_accounts() {
        $allModules = collect($this->getAllModules());
        $moduleId   = $allModules->firstWhere('label', 'Contacts')['id'] ?? null;

        $contacts = Contact::orderBy('created', 'desc')->take(10)->get()->toArray();
        return $this->updateOrAddCRMRecord($moduleId, $contacts);
    }

    protected function getAllModules() {
        return $this->execute(null, 'GET', $this->command->connector->base_url . '/api/crm');
    }

    protected function searchCRMData(array $searchCriteria) {
        return $this->execute(json_encode($searchCriteria), 'GET', $this->command->connector->base_url . '/api/crm/search');
    }

    protected function getModuleById($moduleId) {
        return $this->execute(null, 'GET', $this->command->connector->base_url . "/api/crm/{$moduleId}");
    }

    protected function updateOrAddCRMRecord($moduleId, array $data) {
        return $this->execute(json_encode($data), 'PUT', $this->command->connector->base_url . "/api/crm/{$moduleId}");
    }

    protected function deleteCRMRecord($moduleId, array $recordIds, $type = 'module') {
        return $this->execute(
            json_encode(['record_ids' => $recordIds]),
            'DELETE',
            $this->command->connector->base_url . "/api/crm/{$moduleId}/{$type}"
        );
    }
}
