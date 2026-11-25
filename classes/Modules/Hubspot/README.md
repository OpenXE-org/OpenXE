# HubSpot Pipelines API migration

HubSpot [retires Pipelines API v1 on December 4, 2026](https://developers.hubspot.com/changelog/pipelines-api-v1-sunset).
Deal stages are read from `GET /crm/pipelines/2026-09/deals`. The response contains
`results[].stages[]`, with each stage's internal identifier in `id`.
The integration continues to use the first returned pipeline. Stage identifiers
are stored unchanged in `hs_mapping_leads.value`; existing mappings are reused.

## Credentials

Configure a HubSpot access token in the existing encrypted credential setting
`hubspot_configuration_salt` (`HubspotConfigurationService::HUBSPOT_SALT_CONF_NAME`).
Replace an old HubSpot API key with an access token; an API key cannot be converted
into a token. Existing access tokens in this setting need no storage migration.
The optional `HubspotClientService` constructor argument retains its name
`apiKey` for compatibility and now also expects an access token.

All HubSpot requests send the token as `Authorization: Bearer <token>`.
For reading deal pipelines, grant the token `crm.objects.deals.read`.
Retain the scopes required by the other contact, company, deal, engagement and
owner operations enabled for the integration. The client reads the configured
token for each request, so credential rotation takes effect without restarting it.

See HubSpot's [authentication guide](https://developers.hubspot.com/docs/apps/legacy-apps/authentication/intro-to-auth)
and [Retrieve pipelines reference](https://developers.hubspot.com/docs/api-reference/latest/crm/pipelines/get-pipelines).

## Verification

Using PHPUnit 9.6 with the checked-in Composer autoloader:

```sh
phpunit --do-not-cache-result --testsuite Modules/Hubspot
```

The suite uses Guzzle's mock handler and a database adapter double. It requires
no HubSpot credentials, network requests, or database server.
