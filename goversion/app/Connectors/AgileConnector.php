<?php

namespace App\Connectors;

use App\Models\Module;
use App\Connectors\BaseConnector;
use Carbon\Carbon;
use Faker\Factory;

class AgileConnector extends BaseConnector
{
    protected function getHeaders()
    {
        return [
            'Authorization' => 'Basic ' . base64_encode($this->connector->username . ':' . $this->connector->auth_key),
            'Content-Type' => 'application/json'
        ];
    }

    protected function getEndpointUrl()
    {
        return rtrim($this->connector->base_url, '/') . '/dev/api/contacts';
    }

    /**
     * Fetch contacts from Agile CRM.
     */
    public function fetchContacts(int $limit = 100): array
    {
        $url = $this->getEndpointUrl() . '?pageSize=' . $limit;
        $response = $this->execute(null, 'GET', $url);

        if (!is_array($response)) {
            throw new \Exception("Unexpected response from Agile CRM API");
        }

        return $response;
    }

    /**
     * Save Agile contacts into a local module dynamically.
     */
    public function saveContactsToModule(string $moduleName = 'contacts', int $limit = 100): array
    {
        $contacts = $this->fetchContacts($limit);
        $module = Module::where('name', $moduleName)->firstOrFail();

        $faker = Factory::create();
        $report = [
            'created' => 0,
            'updated' => 0
        ];

        foreach ($contacts as $ac) {
            $requestData = [
                "{$moduleName}__first_name" => $ac['firstName'] ?? '',
                "{$moduleName}__last_name" => $ac['lastName'] ?? '',
                "{$moduleName}__email" => $ac['email'] ?? '',
                "{$moduleName}__phone" => $ac['phone'] ?? '',
                "{$moduleName}__company" => $ac['company'] ?? '',
                "{$moduleName}__active" => 1,
            ];

            $id = Module::saveRecord($module->id, $requestData);

            $id ? $report['created']++ : $report['updated']++;
        }

        return $report;
    }

    /**
     * Public command for your connector commands table.
     */
    public function get_contacts(): array
    {
        return $this->saveContactsToModule('contacts', 200);
    }
}
