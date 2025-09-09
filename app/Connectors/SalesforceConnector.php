<?php

namespace App\Connectors;

use App\Models\Contact; // Your local contacts model

class SalesforceConnector extends BaseConnector
{
    // Optional: override headers if needed
    protected function getHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->connector->access_token,
            'Content-Type'  => 'application/json',
        ];
    }

    // Optional: override parameters if needed
    protected function getParameters()
    {
        return [];
    }

    // Salesforce endpoint for contacts
    protected function getEndpointUrl()
    {
        return $this->connector->base_url . '/services/data/v56.0/sobjects/Contact';
    }

    // Fetch contacts from Salesforce
    public function fetchContacts(int $limit = 100)
    {
        // Salesforce SOQL query to get contacts
        $url = $this->connector->base_url . "/services/data/v56.0/query?q=" 
            . urlencode("SELECT Id, FirstName, LastName, Email FROM Contact LIMIT {$limit}");

        $response = $this->execute(null, 'GET', $url);

        return $response['records'] ?? [];
    }

    // Map Salesforce contacts to your local Contacts module
    public function mapContactsToLocal(array $sfContacts)
    {
        foreach ($sfContacts as $sf) {
            Contact::updateOrCreate(
                ['salesforce_id' => $sf['Id']], // assuming you store SF Id
                [
                    'first_name' => $sf['FirstName'] ?? null,
                    'last_name'  => $sf['LastName'] ?? null,
                    'email'      => $sf['Email'] ?? null,
                ]
            );
        }

        return count($sfContacts) . ' contacts synced';
    }

    // Example helper method to pull and map in one call
    public function syncContacts(int $limit = 100)
    {
        $sfContacts = $this->fetchContacts($limit);
        return $this->mapContactsToLocal($sfContacts);
    }
}
