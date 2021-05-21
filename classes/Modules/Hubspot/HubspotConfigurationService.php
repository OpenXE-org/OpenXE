<?php

namespace Xentral\Modules\Hubspot;

use DateInterval;
use DateTime;
use erpAPI;
use Exception;
use Xentral\Modules\Country\Gateway\CountryGateway;
use Xentral\Modules\Country\Gateway\StateGateway;
use Xentral\Modules\Hubspot\Exception\HubspotConfigurationServiceException;
use Xentral\Modules\Hubspot\Exception\HubspotException;

class HubspotConfigurationService
{
    public const HUBSPOT_SALT_CONF_NAME = 'hubspot_configuration_salt';
    public const HUBSPOT_SETTING_CONF_NAME = 'hubspot_settings';

    private static $_defaultSettings = [
        'hs_sync_deals'     => true,
        'hs_sync_addresses' => true,
    ];

    /** @var erpAPI $erp */
    private $erp;
    /**
     * @var HubspotMetaService
     */
    private $meta;
    /**
     * @var HubspotContactPropertyGateway
     */
    private $propertyGateway;

    /** @var HubspotDealGateway $hubspotDealGateway */
    private $hubspotDealGateway;

    /** @var CountryGateway $countryGateway */
    private $countryGateway;

    /** @var StateGateway $stateGateway */
    private $stateGateway;

    /**
     * @param erpAPI                        $erp
     * @param HubspotMetaService            $meta
     * @param HubspotContactPropertyGateway $propertyGateway
     * @param HubspotDealGateway            $hubspotDealGateway
     * @param CountryGateway                $countryGateway
     * @param StateGateway                  $stateGateway
     */
    public function __construct(
        erpAPI $erp,
        HubspotMetaService $meta,
        HubspotContactPropertyGateway $propertyGateway,
        HubspotDealGateway $hubspotDealGateway,
        CountryGateway $countryGateway,
        StateGateway $stateGateway
    ) {
        $this->erp = $erp;
        $this->meta = $meta;
        $this->meta->setName('conf');
        $this->propertyGateway = $propertyGateway;
        $this->hubspotDealGateway = $hubspotDealGateway;
        $this->countryGateway = $countryGateway;
        $this->stateGateway = $stateGateway;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function trySetConfiguration($name, $value)
    {
        if (empty($name) || !is_string($value)) {
            throw new HubspotConfigurationServiceException('Cannot set Configuration');
        }

        $this->erp->SetKonfigurationValue($name, $value);
    }

    /**
     * @param $name
     *
     * @return array|mixed|string|null
     */
    public function tryGetConfiguration($name)
    {
        if (empty($name)) {
            throw new HubspotConfigurationServiceException('Cannot Get Configuration');
        }

        return $this->erp->GetKonfiguration($name);
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setEncryptedConfiguration($name, $value)
    {
        if (empty($name) || !is_string($value)) {
            throw new HubspotConfigurationServiceException('Cannot set Configuration');
        }
        $encValue = $this->encrypt($value);
        $this->trySetConfiguration($name, $encValue);
    }

    /**
     * @param string $name
     *
     * @return false|string|null
     */
    public function getDecryptedConfiguration($name)
    {
        if (empty($name)) {
            throw new HubspotConfigurationServiceException('Cannot Get Configuration');
        }

        return $this->decrypt($this->tryGetConfiguration($name));
    }

    /**
     * @param string $string
     * @param string $sCipher
     *
     * @return string
     */
    protected function encrypt($string, $sCipher = 'AES-256-CBC')
    {
        if (empty($string)) {
            return '';
        }
        if (null === $this->getNonceSalt()) {
            return $string;
        }
        $key = hash('sha256', $this->getNonceSalt());
        $ivlen = openssl_cipher_iv_length($sCipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($string, $sCipher, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);

        return base64_encode($iv . $hmac . $ciphertext_raw);
    }

    /**
     * @param string $string
     * @param string $sCipher
     *
     * @return false|string|null
     */
    protected function decrypt($string, $sCipher = 'AES-256-CBC')
    {
        if (empty($string) || !$this->isBase64Encoded($string)) {
            return '';
        }
        if (null === $this->getNonceSalt()) {
            return $this->isBase64Encoded($string) ? null : $string;
        }
        $enc = base64_decode($string);
        $key = hash('sha256', $this->getNonceSalt());
        $ivlen = openssl_cipher_iv_length($sCipher);
        $iv = substr($enc, 0, $ivlen);
        $hmac = substr($enc, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($enc, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $sCipher, $key, OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, true);

        return hash_equals($hmac, $calcmac) ? $original_plaintext : '';
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    private function isBase64Encoded($string)
    {
        return base64_encode(base64_decode($string)) === $string;
    }

    /**
     * @return false|string|null
     */
    private function generateSecureSalt()
    {
        return password_hash(uniqid(mt_rand(), true), PASSWORD_BCRYPT);
    }

    /**
     * @param bool $force
     *
     * @return false|int
     */
    public function createSalt($force = false)
    {
        if ($force === true) {
            $this->meta->delete();
        }
        if ($this->meta->exists() && $this->meta->keyExists('nonce_salt')) {
            return -1;
        }

        return $this->meta->save(['nonce_salt' => $this->generateSecureSalt()]);
    }

    /**
     * @return string|null
     */
    private function getNonceSalt()
    {
        $data = $this->meta->get();

        return array_key_exists('nonce_salt', $data) ? $data['nonce_salt'] : null;
    }

    /**
     * @param HubspotHttpResponseService $response
     *
     * @throws HubspotException
     *
     * @return array
     */
    public function formatAddressByResponse(HubspotHttpResponseService $response): array
    {
        if ($response->getStatusCode() !== 200) {
            throw new HubSpotException($response->getError());
        }
        $contact = $response->getJson();
        $properties = $contact['properties'];
        $hubspotContact = array_combine(array_keys($properties), array_column($properties, 'value'));
        $hubspotOwnerId = (int)array_key_exists(
            'hubspot_owner_id',
            $hubspotContact
        ) ? $hubspotContact['hubspot_owner_id'] : 0;

        $data = [
            'lead'             => 1,
            'typ'              => 'herr',
            'sprache'          => 'deutsch',
            'name'             => sprintf(
                '%s %s',
                empty($hubspotContact['firstname']) ? 'Hubspot - ' : $hubspotContact['firstname'],
                empty($hubspotContact['lastname']) ? $hubspotContact['email'] : $hubspotContact['lastname']
            ),
            'vorname'          => empty($hubspotContact['firstname']) ? 'Hubspot - ' : $hubspotContact['firstname'],
            'nachname'         => empty($hubspotContact['lastname']) ? $hubspotContact['email'] : $hubspotContact['lastname'],
            'ort'              => empty($hubspotContact['city']) ? '' : $hubspotContact['city'],
            'plz'              => empty($hubspotContact['zip']) ? '' : $hubspotContact['zip'],
            'telefon'          => empty($hubspotContact['phone']) ? '' : $hubspotContact['phone'],
            'email'            => $hubspotContact['email'],
            'kundenfreigabe'   => 1,
            'waehrung'         => 'EUR',
            'strasse'          => empty($hubspotContact['address']) ? '' : $hubspotContact['address'],
            'internetseite'    => empty($hubspotContact['website']) ? '' : $hubspotContact['website'],
            'hubspot_owner_id' => $hubspotOwnerId,
        ];
        $country = empty($hubspotContact['country']) ? 'DE' : $hubspotContact['country'];
        if (!empty($country)) {
            $countryDb = $this->countryGateway->findByName($country);
            if (!empty($countryDb)) {
                $country = $countryDb['iso2_code'];
            }
        }
        $data['land'] = $country;

        $state = empty($hubspotContact['state']) ? '' : $hubspotContact['state'];
        if (!empty($state) && strlen($country) === 2) {
            $stateDb = $this->stateGateway->findByNameAndIso2CountryCode($state, $country);
            if (!empty($stateDb)) {
                $state = $stateDb['iso2_code'];
            }
        }
        $data['bundesstaat'] = $state;

        try {
            $leadFields = $this->matchSelectedAddressFreeField();
            $lrField = $leadFields['hubspot_lr_field'];
            $lsField = $leadFields['hubspot_ls_field'];
            $data[$lsField] = empty($hubspotContact['hs_lead_status']) ? '' : $hubspotContact['hs_lead_status'];
            $data[$lrField] = empty($hubspotContact['lifecyclestage']) ? '' : $hubspotContact['lifecyclestage'];
        } catch (HubspotException $exception) {
        }

        return $data;
    }

    /**
     * @throws HubspotException
     * @return array
     */
    public function matchSelectedAddressFreeField()
    {
        $hFields = [];
        $asAddressFreeFieldValues = $this->propertyGateway->getConfiguredFreeAddressFieldValues();
        $hsConfFields = [
            'hubspot_lr_field' => $this->tryGetConfiguration('hubspot_lr_field'),
            'hubspot_ls_field' => $this->tryGetConfiguration('hubspot_ls_field'),
        ];
        foreach ($asAddressFreeFieldValues as $fieldName) {
            if (in_array('adresse' . $fieldName, $hsConfFields)) {
                $hFields[array_search('adresse' . $fieldName, $hsConfFields)] = $fieldName;
            }
        }

        if (empty($hFields)) {
            throw new HubSpotException('Lead-Status/Lifecycle fields cannot be matched');
        }

        return $hFields;
    }

    /**
     * @param $address
     *
     * @throws HubspotException
     *
     * @return array
     */
    public function formatAddressToHubspotContact(array $address): array
    {
        if (empty($address)) {
            throw new HubSpotException('Address is invalid');
        }
        $leadFields = $this->matchSelectedAddressFreeField();
        $lrField = $leadFields['hubspot_lr_field'];
        $lsField = $leadFields['hubspot_ls_field'];

        $firstName = empty($address['vorname']) ? '' : $address['vorname'];
        $lastName = empty($address['nachname']) ? '' : $address['nachname'];

        if (empty($lastName) && (empty($firstName) || $firstName !== $address['name'])) {
            $lastName = $address['name'];
        }

        $data = [
            'email'          => $address['email'],
            'firstname'      => $firstName,
            'lastname'       => $lastName,
            'website'        => $address['internetseite'],
            'phone'          => $address['telefon'],
            'address'        => $address['strasse'],
            'city'           => $address['ort'],
            'state'          => $address['bundesstaat'],
            'zip'            => $address['plz'],
            'hs_lead_status' => $address[$lsField],
            'lifecyclestage' => $address[$lrField],
        ];

        $iso2CountryCode = $address['land'];
        if (!empty($iso2CountryCode)) {
            $countryDb = $this->countryGateway->findByIso2Code($iso2CountryCode);
            if (!empty($countryDb)) {
                $country = $countryDb['name_de'];
                $data['country'] = $country;
            }
        }

        $iso2State = $address['bundesstaat'];
        if (!empty($iso2State) && strlen($iso2CountryCode) === 2) {
            $stateDb = $this->stateGateway->findByIso2CodeAndIso2CountryCode($iso2State, $iso2CountryCode);
            if (!empty($stateDb)) {
                $iso2State = $stateDb['name_de'];
            }
        }
        $data['state'] = $iso2State;

        if (array_key_exists('typ', $address) && !empty($address['typ'])) {
            $data['salutation'] = $address['typ'];

            if ($address['typ'] === 'firma') {
                if (array_key_exists('numberofemployees', $address) &&
                    !empty($address['numberofemployees'])) {
                    $data['numberofemployees'] = $address['numberofemployees'];
                }

                $settings = $this->getSettings();
                $defaultCustomFields = array_key_exists('hubspot_address_free_fields', $settings) ?
                    $settings['hubspot_address_free_fields'] : [];
                if (!empty($defaultCustomFields)) {
                    foreach ($defaultCustomFields as $property => $systemField) {
                        $data[$property] = $address[sprintf('xthubspot_%s', $property)];
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param HubspotHttpResponseService $response
     *
     * @throws Exception
     * @return array
     */
    public function formatDealByResponse(HubspotHttpResponseService $response)
    {
        if ($response->getStatusCode() === 200) {
            $deal = $response->getJson();
            $properties = $deal['properties'];

            $hDeal = array_combine(array_keys($properties), array_column($properties, 'value'));
            $dealStage = $this->propertyGateway->getMappingByValueAndType($hDeal['dealstage'], 'deals');

            return [
                'bezeichnung'      => $hDeal['dealname'],
                'datum_angelegt'   => date('Y-m-d', $hDeal['createdate'] / 1000),
                'zeit_angelegt'    => date('H:i:s', $hDeal['createdate'] / 1000),
                'datum_erinnerung' => $this->getTimeByDays($hDeal['days_to_close'])->format('Y-m-d'),
                'zeit_erinnerung'  => $this->getTimeByDays($hDeal['days_to_close'])->format('H:i:s'),
                'betrag'           => array_key_exists('amount', $hDeal) ? (float)$hDeal['amount'] : 0.00,
                'stages'           => !empty($dealStage['wiedervorlage_stage_id']) ? $dealStage['wiedervorlage_stage_id'] : 0,
            ];
        }
        throw new HubSpotException($response->getError());
    }

    /**
     * @param array $resubmission
     *
     * @throws Exception
     * @return array
     */
    public function formatResubmissionToHubspotDeal($resubmission)
    {
        if (is_array($resubmission) && !empty($resubmission)) {
            $oCloseDate = null;
            if (!empty($resubmission['datum_erinnerung']) && !empty($resubmission['zeit_erinnerung'])) {
                $closeDate = $resubmission['datum_erinnerung'] . ' ' . $resubmission['zeit_erinnerung'];
                $oCloseDate = new DateTime($closeDate);
            }
            $mapping = $this->hubspotDealGateway->getMappingStageByResubmissionStageId($resubmission['stages']);

            return [
                'dealname'  => $resubmission['bezeichnung'],
                'dealstage' => !empty($mapping) ? $mapping['value'] : null,
                'amount'    => empty($resubmission['betrag']) ? 0.00 : $resubmission['betrag'],
                'pipeline'  => 'default',
                'closedate' => null !== $oCloseDate ? $oCloseDate->getTimestamp() * 1000 : 0,
            ];
        }
        throw new HubSpotException('Resubmission is invalid');
    }

    /**
     * @param int $days
     *
     * @throws Exception
     * @return DateTime
     */
    private function getTimeByDays($days)
    {
        $date = new DateTime('now');
        $interval = sprintf('P%dD', (int)$days);
        $date->add(new DateInterval($interval));

        return $date;
    }

    /**
     * @param array $settings
     */
    public function setSettings($settings = [])
    {
        $this->trySetConfiguration(
            static::HUBSPOT_SETTING_CONF_NAME,
            json_encode($settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)
        );
    }

    /**
     * @throws HubspotConfigurationServiceException
     * @return array
     */
    public function getSettings()
    {
        $settingsRaw = $this->tryGetConfiguration(static::HUBSPOT_SETTING_CONF_NAME);
        if (empty($settingsRaw)) {
            return static::$_defaultSettings;
        }
        if (($settings = json_decode($settingsRaw, true)) !== null
            && (json_last_error() === JSON_ERROR_NONE)) {
            if (empty($settings)) {
                return static::$_defaultSettings;
            }

            return $settings;
        }
        throw new HubspotConfigurationServiceException(json_last_error_msg());
    }

    /**
     * @param int $contactId
     *
     * @throws HubspotConfigurationServiceException
     *
     * @return void
     */
    public function addContactToGroup(int $contactId = 0): void
    {
        $defaultSettings = $this->getSettings();

        $contactGrpId = array_key_exists('hs_contact_grp', $defaultSettings) ? $defaultSettings['hs_contact_grp'] : 0;
        if (!empty($contactGrpId)) {
            $this->erp->AddRolleZuAdresse($contactId, 'Mitglied', 'von', 'Gruppe', $contactGrpId);
        }
    }

    /**
     * @param HubspotHttpResponseService $response
     *
     * @throws HubspotException
     *
     * @return array
     */
    public function formatCompanyByResponse(HubspotHttpResponseService $response): array
    {
        if ($response->getStatusCode() !== 200) {
            throw new HubSpotException($response->getError());
        }
        $contact = $response->getJson();
        $properties = $contact['properties'];

        $hubspotContact = array_combine(array_keys($properties), array_column($properties, 'value'));
        $hubspotOwnerId = (int)array_key_exists(
            'hubspot_owner_id',
            $hubspotContact
        ) ? $hubspotContact['hubspot_owner_id'] : 0;

        $data = [
            'typ'              => 'firma',
            'sprache'          => 'deutsch',
            'name'             => empty($hubspotContact['name']) ? 'Hubspot - Company' : $hubspotContact['name'],
            'ort'              => empty($hubspotContact['city']) ? '' : $hubspotContact['city'],
            'plz'              => empty($hubspotContact['zip']) ? '' : $hubspotContact['zip'],
            'telefon'          => empty($hubspotContact['phone']) ? '' : $hubspotContact['phone'],
            'kundenfreigabe'   => 1,
            'waehrung'         => 'EUR',
            'strasse'          => empty($hubspotContact['address']) ? '' : $hubspotContact['address'],
            'internetseite'    => empty($hubspotContact['website']) ? '' : $hubspotContact['website'],
            'hubspot_owner_id' => $hubspotOwnerId,
        ];
        $country = empty($hubspotContact['country']) ? 'DE' : $hubspotContact['country'];

        if (!empty($country)) {
            $countryDb = $this->countryGateway->findByName($country);
            if (!empty($countryDb)) {
                $country = $countryDb['iso2_code'];
            }
        }
        $data['land'] = $country;

        $state = empty($hubspotContact['state']) ? '' : $hubspotContact['state'];
        if (!empty($state) && strlen($country) === 2) {
            $stateDb = $this->stateGateway->findByNameAndIso2CountryCode($state, $country);
            if (!empty($stateDb)) {
                $state = $stateDb['iso2_code'];
            }
        }
        $data['bundesstaat'] = $state;

        try {
            $leadFields = $this->matchSelectedAddressFreeField();
            $lrField = $leadFields['hubspot_lr_field'];
            $lsField = $leadFields['hubspot_ls_field'];
            $data[$lsField] = empty($hubspotContact['hs_lead_status']) ? '' : $hubspotContact['hs_lead_status'];
            $data[$lrField] = empty($hubspotContact['lifecyclestage']) ? '' : $hubspotContact['lifecyclestage'];
        } catch (HubspotException $exception) {
        }

        $numberOfEmployeesField = $this->tryGetConfiguration('hubspot_numberofemployees_field');
        if (!empty($numberOfEmployeesField)) {
            $fieldName = str_replace('adresse', '', $numberOfEmployeesField);
            $numberOfEmployees = empty($hubspotContact['numberofemployees']) ? 0 : $hubspotContact['numberofemployees'];
            $data[$fieldName] = $numberOfEmployees;
        }

        $settings = $this->getSettings();
        $defaultCustomFields = array_key_exists('hubspot_address_free_fields', $settings) ?
            $settings['hubspot_address_free_fields'] : [];
        if (!empty($defaultCustomFields)) {
            foreach ($defaultCustomFields as $property => $systemField) {
                $fieldName = str_replace('adresse', '', $systemField);
                $data[$fieldName] = $hubspotContact[$property];
            }
        }

        return $data;
    }

    /**
     * @param string $customFreeField
     * @param array  $fieldConfig
     *
     * @return void
     */
    public function setSystemFreeField(string $customFreeField, array $fieldConfig): void
    {
        $label = $fieldConfig['label'];
        $type = $fieldConfig['fieldType'];
        $value = $fieldConfig['options'];
        $customFreeFieldValue = $label . '|' . implode('|', $value);
        $this->erp->FirmendatenSet($customFreeField, $customFreeFieldValue);
        $this->erp->FirmendatenSet($customFreeField . 'typ', $type);
        $this->erp->FirmendatenSet($customFreeField . 'spalte', '1');
        $this->erp->Firmendaten($customFreeField);
    }

    /**
     * @param string $customFreeField
     *
     * @return void
     */
    public function unsetSystemFreeField(string $customFreeField): void
    {
        $this->erp->FirmendatenSet($customFreeField, '');
        $this->erp->FirmendatenSet($customFreeField . 'typ', '');
        $this->erp->FirmendatenSet($customFreeField . 'spalte', '');
    }
}
