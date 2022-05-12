<?php

namespace Xentral\Modules\Hubspot;

use Xentral\Components\Database\Database;
use Xentral\Modules\Hubspot\Exception\HubspotException;

final class HubspotContactPropertyService
{

    /** @var HubspotClientService $client */
    private $client;

    /** @var HubspotContactPropertyGateway $gateway */
    private $gateway;

    /** @var Database $db */
    private $db;

    /**
     * @param HubspotClientService          $client
     * @param HubspotContactPropertyGateway $gateway
     * @param Database                      $db
     */
    public function __construct(HubspotClientService $client, HubspotContactPropertyGateway $gateway, Database $db)
    {
        $this->client = $client;
        $this->gateway = $gateway;
        $this->db = $db;
    }

    /**
     * @return HubspotHttpResponseService
     */
    public function getProperties()
    {
        return $this->client->setResource('getAllContactProperties')->read();
    }

    /**
     * @param string $name
     * @param string $type
     *
     * @throws HubspotException
     *
     * @return HubspotHttpResponseService
     */
    public function getProperty(string $name, string $type = 'contact')
    {
        if (empty($name)) {
            throw new HubspotException('Property name is missing');
        }
        $resource = 'getContactProperty';
        if ($type === 'company') {
            $resource = 'getCompanyProperty';
        }

        return $this->client->setResource($resource)->read([], [$name]);
    }

    /**
     * @param string $type
     * @param bool   $withLabel
     *
     * @throws HubspotException
     *
     * @return array
     */
    public function getHsLeadStatus(string $type = 'contact', bool $withLabel = true)
    {
        $response = $this->getProperty('hs_lead_status', $type);
        if ($response->getStatusCode() !== 200) {
            throw new HubspotException($response->getError());
        }

        if (($data = $response->getJson()) && array_key_exists('options', $data)) {
            if ($withLabel === false) {
                return array_column($data['options'], 'value');
            }
            $response = [];
            $options = $data['options'];
            foreach ($options as $option) {
                $response[$option['value']] = $option['label'];
            }

            return $response;
        }

        return [];
    }

    public function getHsLeadRating()
    {
        $response = $this->getProperty('lifecyclestage');
        if ($response->getStatusCode() !== 200) {
            throw new HubspotException($response->getError());
        }

        if (($data = $response->getJson()) && array_key_exists('options', $data)) {
            return array_column($data['options'], 'value');
        }

        return [];
    }

    /**
     * @param string $scope
     *
     * @throws HubspotException
     *
     * @return array
     */
    public function getUpdatedLeadStatuses(string $scope): array
    {
        $customFreeFieldValue = [];
        $leadStatus = $this->gateway->getLeadsByType('status', false, $scope);
        $dbStatuses = array_column($leadStatus, 'value');

        $remoteStatusContact = $this->getHsLeadStatus($scope);
        $remoteStatusContactKey = array_keys($remoteStatusContact);

        $statusContactGone = array_diff($dbStatuses, $remoteStatusContactKey);
        if (!empty($statusContactGone)) {
            foreach ($statusContactGone as $valueGone) {
                $delGone = "DELETE FROM hs_mapping_leads
                                WHERE value = :value AND type = 'status' AND setting_scope = 'contact'";
                $this->db->perform($delGone, ['value' => $valueGone]);
            }
        }

        $statusContactNew = array_diff($remoteStatusContactKey, $dbStatuses);
        if (!empty($statusContactNew)) {
            foreach ($remoteStatusContact as $status => $label) {
                if (!in_array($status, $statusContactNew, true)) {
                    continue;
                }
                $customFreeFieldValue[] = sprintf('%s=>%s', $label, $status);

                $newStatus = 'INSERT INTO hs_mapping_leads (label, value, type, setting_scope)
                                VALUES(:label, :value, "status", :scope)';
                $this->db->perform(
                    $newStatus,
                    [
                        'label' => $label,
                        'value' => $status,
                        'scope' => $scope,
                    ]
                );
            }
        }

        return $customFreeFieldValue;
    }

    /**
     * @param string      $propertyName
     * @param string|null $type
     *
     * @return array|null
     */
    public function getCustomPropertyByName(string $propertyName, ?string $type = null): ?array
    {
        try {
            $type = $type ?? 'company';
            $response = $this->getProperty(strtolower($propertyName), $type);
        } catch (HubspotException $e) {
            // Do nothing
        }

        if (!isset($response)) {
            try {
                $response = $this->getProperty(strtolower($propertyName));
            } catch (HubspotException $e) {
                return null;
            }
        }

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $data =  $response->getJson();
        $options = [];
        $fieldType = $data['fieldType'];
        $fieldLabel = $data['label'];
        $name = $data['name'];
        if (array_key_exists('options', $data)) {
            $options = !empty($data['options'])? array_column($data['options'], 'value') : [];
        }
        return ['fieldName' => $name, 'fieldType' => $fieldType, 'label' => $fieldLabel, 'options' => $options];
    }
}
