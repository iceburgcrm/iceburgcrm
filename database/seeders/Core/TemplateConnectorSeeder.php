<?php

namespace Database\Seeders\Core;

use App\Models\Connector;
use App\Models\Endpoint;
use Illuminate\Database\Seeder;

class TemplateConnectorSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->templates() as $template) {
            $connectorId = Connector::insertGetId([
                'name' => $template['name'],
                'description' => $template['description'],
                'auth_type' => $template['auth_type'],
                'auth_key' => $template['auth_key'] ?? null,
                'base_url' => $template['base_url'],
                'token_url' => $template['token_url'] ?? null,
                'client_id' => $template['client_id'] ?? null,
                'client_secret' => $template['client_secret'] ?? null,
                'username' => $template['username'] ?? null,
                'password' => $template['password'] ?? null,
                'access_token' => null,
                'refresh_token' => null,
                'token_expires_at' => null,
                'status' => 1,
                'type' => 1,
            ]);

            Endpoint::insert($this->endpointRows($connectorId, $template['endpoints']));
        }
    }

    private function endpointRows(int $connectorId, array $endpoints): array
    {
        return array_map(function (array $endpoint) use ($connectorId) {
            return [
                'connector_id' => $connectorId,
                'name' => $endpoint['name'],
                'description' => $endpoint['description'] ?? null,
                'endpoint' => $endpoint['endpoint'],
                'request_type' => $endpoint['request_type'] ?? 'GET',
                'params' => null,
                'headers' => null,
                'response_mapping' => $endpoint['response_mapping'] ?? null,
                'status' => 1,
                'retry_count' => 0,
                'last_run_status' => null,
                'last_run_message' => null,
                'last_run_data' => null,
                'last_ran' => null,
            ];
        }, $endpoints);
    }

    private function templates(): array
    {
        return [
            [
                'name' => 'Salesforce',
                'description' => 'Salesforce CRM template. Replace {instance} with your Salesforce subdomain.',
                'auth_type' => 'OAuth2',
                'base_url' => 'https://{instance}.salesforce.com/services/data/v52.0/',
                'token_url' => 'https://login.salesforce.com/services/oauth2/token',
                'client_id' => 'your_client_id',
                'client_secret' => 'your_client_secret',
                'endpoints' => [
                    ['name' => 'Accounts', 'endpoint' => 'sobjects/Account', 'response_mapping' => 'Map Salesforce accounts'],
                    ['name' => 'Contacts', 'endpoint' => 'sobjects/Contact', 'response_mapping' => 'Map Salesforce contacts'],
                    ['name' => 'Leads', 'endpoint' => 'sobjects/Lead', 'response_mapping' => 'Map Salesforce leads'],
                ],
            ],
            [
                'name' => 'HubSpot',
                'description' => 'HubSpot CRM template using OAuth2.',
                'auth_type' => 'OAuth2',
                'base_url' => 'https://api.hubapi.com/',
                'token_url' => 'https://api.hubapi.com/oauth/v1/token',
                'client_id' => 'your_client_id',
                'client_secret' => 'your_client_secret',
                'endpoints' => [
                    ['name' => 'Contacts', 'endpoint' => 'crm/v3/objects/contacts', 'response_mapping' => 'Map HubSpot contacts'],
                    ['name' => 'Companies', 'endpoint' => 'crm/v3/objects/companies', 'response_mapping' => 'Map HubSpot companies'],
                    ['name' => 'Deals', 'endpoint' => 'crm/v3/objects/deals', 'response_mapping' => 'Map HubSpot deals'],
                ],
            ],
            [
                'name' => 'Zoho CRM',
                'description' => 'Zoho CRM template using OAuth2.',
                'auth_type' => 'OAuth2',
                'base_url' => 'https://www.zohoapis.com/crm/v2/',
                'token_url' => 'https://accounts.zoho.com/oauth/v2/token',
                'client_id' => 'your_client_id',
                'client_secret' => 'your_client_secret',
                'endpoints' => [
                    ['name' => 'Leads', 'endpoint' => 'Leads', 'response_mapping' => 'Map Zoho leads'],
                    ['name' => 'Contacts', 'endpoint' => 'Contacts', 'response_mapping' => 'Map Zoho contacts'],
                    ['name' => 'Accounts', 'endpoint' => 'Accounts', 'response_mapping' => 'Map Zoho accounts'],
                ],
            ],
            [
                'name' => 'Microsoft Dynamics 365',
                'description' => 'Microsoft Dynamics 365 Dataverse template using OAuth2.',
                'auth_type' => 'OAuth2',
                'base_url' => 'https://{org}.crm.dynamics.com/api/data/v9.2/',
                'token_url' => 'https://login.microsoftonline.com/{tenant}/oauth2/v2.0/token',
                'client_id' => 'your_client_id',
                'client_secret' => 'your_client_secret',
                'endpoints' => [
                    ['name' => 'Accounts', 'endpoint' => 'accounts', 'response_mapping' => 'Map Dynamics accounts'],
                    ['name' => 'Contacts', 'endpoint' => 'contacts', 'response_mapping' => 'Map Dynamics contacts'],
                    ['name' => 'Opportunities', 'endpoint' => 'opportunities', 'response_mapping' => 'Map Dynamics opportunities'],
                ],
            ],
            [
                'name' => 'Pipedrive',
                'description' => 'Pipedrive sales CRM template using API key authentication.',
                'auth_type' => 'ApiKey',
                'auth_key' => 'your_api_key',
                'base_url' => 'https://api.pipedrive.com/v1/',
                'endpoints' => [
                    ['name' => 'Persons', 'endpoint' => 'persons', 'response_mapping' => 'Map Pipedrive persons'],
                    ['name' => 'Organizations', 'endpoint' => 'organizations', 'response_mapping' => 'Map Pipedrive organizations'],
                    ['name' => 'Deals', 'endpoint' => 'deals', 'response_mapping' => 'Map Pipedrive deals'],
                ],
            ],
            [
                'name' => 'Freshsales',
                'description' => 'Freshsales CRM template using API key authentication.',
                'auth_type' => 'ApiKey',
                'auth_key' => 'your_api_key',
                'base_url' => 'https://{domain}.myfreshworks.com/crm/sales/api/',
                'endpoints' => [
                    ['name' => 'Contacts', 'endpoint' => 'contacts', 'response_mapping' => 'Map Freshsales contacts'],
                    ['name' => 'Accounts', 'endpoint' => 'sales_accounts', 'response_mapping' => 'Map Freshsales accounts'],
                    ['name' => 'Deals', 'endpoint' => 'deals', 'response_mapping' => 'Map Freshsales deals'],
                ],
            ],
            [
                'name' => 'SugarCRM',
                'description' => 'SugarCRM REST template using OAuth2.',
                'auth_type' => 'OAuth2',
                'base_url' => 'https://{domain}/rest/v11_20/',
                'token_url' => 'https://{domain}/rest/v11_20/oauth2/token',
                'client_id' => 'your_client_id',
                'client_secret' => 'your_client_secret',
                'endpoints' => [
                    ['name' => 'Accounts', 'endpoint' => 'Accounts', 'response_mapping' => 'Map SugarCRM accounts'],
                    ['name' => 'Contacts', 'endpoint' => 'Contacts', 'response_mapping' => 'Map SugarCRM contacts'],
                    ['name' => 'Leads', 'endpoint' => 'Leads', 'response_mapping' => 'Map SugarCRM leads'],
                ],
            ],
            [
                'name' => 'Insightly',
                'description' => 'Insightly CRM template using API key authentication.',
                'auth_type' => 'ApiKey',
                'auth_key' => 'your_api_key',
                'base_url' => 'https://api.insightly.com/v3.1/',
                'endpoints' => [
                    ['name' => 'Contacts', 'endpoint' => 'Contacts', 'response_mapping' => 'Map Insightly contacts'],
                    ['name' => 'Organizations', 'endpoint' => 'Organisations', 'response_mapping' => 'Map Insightly organizations'],
                    ['name' => 'Opportunities', 'endpoint' => 'Opportunities', 'response_mapping' => 'Map Insightly opportunities'],
                ],
            ],
            [
                'name' => 'Copper CRM',
                'description' => 'Copper CRM template using API key authentication.',
                'auth_type' => 'ApiKey',
                'auth_key' => 'your_api_key',
                'base_url' => 'https://api.copper.com/developer_api/v1/',
                'endpoints' => [
                    ['name' => 'People', 'endpoint' => 'people/search', 'request_type' => 'POST', 'response_mapping' => 'Map Copper people'],
                    ['name' => 'Companies', 'endpoint' => 'companies/search', 'request_type' => 'POST', 'response_mapping' => 'Map Copper companies'],
                    ['name' => 'Opportunities', 'endpoint' => 'opportunities/search', 'request_type' => 'POST', 'response_mapping' => 'Map Copper opportunities'],
                ],
            ],
            [
                'name' => 'Close CRM',
                'description' => 'Close CRM template using API key authentication.',
                'auth_type' => 'ApiKey',
                'auth_key' => 'your_api_key',
                'base_url' => 'https://api.close.com/api/v1/',
                'endpoints' => [
                    ['name' => 'Leads', 'endpoint' => 'lead/', 'response_mapping' => 'Map Close leads'],
                    ['name' => 'Contacts', 'endpoint' => 'contact/', 'response_mapping' => 'Map Close contacts'],
                    ['name' => 'Opportunities', 'endpoint' => 'opportunity/', 'response_mapping' => 'Map Close opportunities'],
                ],
            ],
            [
                'name' => 'Monday.com',
                'description' => 'Monday.com template using API key authentication.',
                'auth_type' => 'ApiKey',
                'auth_key' => 'your_api_key',
                'base_url' => 'https://api.monday.com/v2/',
                'endpoints' => [
                    ['name' => 'Boards', 'endpoint' => '', 'request_type' => 'POST', 'response_mapping' => 'GraphQL query for boards'],
                    ['name' => 'Items', 'endpoint' => '', 'request_type' => 'POST', 'response_mapping' => 'GraphQL query for items'],
                ],
            ],
            [
                'name' => 'Airtable',
                'description' => 'Airtable template using API key authentication. Replace {base_id}.',
                'auth_type' => 'ApiKey',
                'auth_key' => 'your_api_key',
                'base_url' => 'https://api.airtable.com/v0/{base_id}/',
                'endpoints' => [
                    ['name' => 'Contacts', 'endpoint' => 'Contacts', 'response_mapping' => 'Map Airtable contacts table'],
                    ['name' => 'Companies', 'endpoint' => 'Companies', 'response_mapping' => 'Map Airtable companies table'],
                ],
            ],
            [
                'name' => 'Mailchimp',
                'description' => 'Mailchimp marketing template using API key authentication. Replace {dc} with your data center.',
                'auth_type' => 'ApiKey',
                'auth_key' => 'your_api_key',
                'base_url' => 'https://{dc}.api.mailchimp.com/3.0/',
                'endpoints' => [
                    ['name' => 'Lists', 'endpoint' => 'lists', 'response_mapping' => 'Map Mailchimp lists'],
                    ['name' => 'Campaigns', 'endpoint' => 'campaigns', 'response_mapping' => 'Map Mailchimp campaigns'],
                ],
            ],
            [
                'name' => 'Slack API',
                'description' => 'Slack workspace template using OAuth2 bearer token authentication.',
                'auth_type' => 'OAuth2',
                'base_url' => 'https://slack.com/api/',
                'token_url' => 'https://slack.com/api/oauth.v2.access',
                'client_id' => 'your_client_id',
                'client_secret' => 'your_client_secret',
                'endpoints' => [
                    ['name' => 'Channels', 'endpoint' => 'conversations.list', 'response_mapping' => 'Map Slack channels'],
                    ['name' => 'Users', 'endpoint' => 'users.list', 'response_mapping' => 'Map Slack users'],
                ],
            ],
            [
                'name' => 'Stripe API',
                'description' => 'Stripe payments template using API key authentication.',
                'auth_type' => 'ApiKey',
                'auth_key' => 'your_api_key',
                'base_url' => 'https://api.stripe.com/v1/',
                'endpoints' => [
                    ['name' => 'Customers', 'endpoint' => 'customers', 'response_mapping' => 'Map Stripe customers'],
                    ['name' => 'Charges', 'endpoint' => 'charges', 'response_mapping' => 'Map Stripe charges'],
                ],
            ],
        ];
    }
}
