<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Service;

use Xentral\Modules\Pipedrive\Exception\PipedriveConfigurationException;
use Xentral\Modules\Pipedrive\Exception\PipedriveMetaException;
use Xentral\Modules\Pipedrive\Gateway\PipedriveDealGateway;
use Xentral\Modules\Pipedrive\Gateway\PipedrivePersonPropertyGateway;
use Xentral\Modules\Pipedrive\Wrapper\PipedriveAddAddressRoleWrapper;
use Xentral\Modules\SystemConfig\SystemConfigModule;

final class PipedriveConfigurationService
{
    /** @var string */
    private const PIPEDRIVE_SETTINGS = 'pipedrive_settings';

    /** @var string */
    private const PIPEDRIVE_CONF_NAME = 'pipedrive_conf.json';

    /** @var array $_defaultSettings */
    private static $_defaultSettings = [
        'pd_sync_deals'     => true,
        'pd_sync_addresses' => true,
        'pd_api_key'        => null,
    ];

    /** @var SystemConfigModule $configWrapper */
    private $configWrapper;

    /** @var PipedriveMetaWriterService $metaWriterService */
    private $metaWriterService;

    /** @var PipedrivePersonPropertyGateway $propertyGateway */
    private $propertyGateway;

    /** @var PipedriveDealGateway $pipedriveDealGateway */
    private $pipedriveDealGateway;

    /** @var PipedriveMetaReaderService $metaReaderService */
    private $metaReaderService;

    /** @var PipedriveAddAddressRoleWrapper $addAddressRoleWrapper */
    private $addAddressRoleWrapper;

    /**
     * @param SystemConfigModule             $configWrapper
     * @param PipedriveMetaWriterService     $metaWriterService
     * @param PipedrivePersonPropertyGateway $propertyGateway
     * @param PipedriveDealGateway           $pipedriveDealGateway
     * @param PipedriveMetaReaderService     $metaReaderService
     * @param PipedriveAddAddressRoleWrapper $addAddressRoleWrapper
     */
    public function __construct(
        SystemConfigModule $configWrapper,
        PipedriveMetaWriterService $metaWriterService,
        PipedrivePersonPropertyGateway $propertyGateway,
        PipedriveDealGateway $pipedriveDealGateway,
        PipedriveMetaReaderService $metaReaderService,
        PipedriveAddAddressRoleWrapper $addAddressRoleWrapper
    ) {
        $this->configWrapper = $configWrapper;
        $this->metaWriterService = $metaWriterService;
        $this->propertyGateway = $propertyGateway;
        $this->pipedriveDealGateway = $pipedriveDealGateway;
        $this->metaReaderService = $metaReaderService;
        $this->addAddressRoleWrapper = $addAddressRoleWrapper;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @throws PipedriveConfigurationException
     *
     * @return void
     */
    public function trySetConfiguration(string $name, string $value): void
    {
        if (empty($name)) {
            throw new PipedriveConfigurationException('Cannot set Configuration');
        }

        $this->configWrapper->setValue(self::PIPEDRIVE_SETTINGS, $name, $value);
    }

    /**
     * @param string $name
     *
     * @throws PipedriveConfigurationException
     *
     * @return string|null
     */
    public function tryGetConfiguration(string $name): ?string
    {
        if (empty($name)) {
            throw new PipedriveConfigurationException('Cannot get configuration on Empty');
        }

        return $this->configWrapper->tryGetValue(self::PIPEDRIVE_SETTINGS, $name);
    }

    /**
     * @param array  $settings
     * @param string $value
     *
     * @throws PipedriveConfigurationException
     * @throws PipedriveMetaException
     *
     * @return array
     */
    public function getEncryptedConfiguration(array $settings, string $value): array
    {
        if (empty($settings)) {
            throw new PipedriveConfigurationException('Cannot set Configuration');
        }
        $encValue = $this->encrypt($value);
        $settings['pd_api_key'] = $encValue;

        return $settings;
    }

    /**
     * @throws PipedriveConfigurationException
     * @throws PipedriveMetaException
     *
     * @return string|null
     */
    public function getDecryptedConfiguration(): ?string
    {
        $settings = $this->getSettings();

        return $settings['pd_api_key'] ?? null;
    }

    /**
     * @param string $plaintext
     * @param string $sCipher
     *
     * @throws PipedriveConfigurationException
     * @throws PipedriveMetaException
     *
     * @return string
     */
    protected function encrypt(string $plaintext, string $sCipher = 'aes-128-gcm'): string
    {
        if (!in_array($sCipher, openssl_get_cipher_methods(), true)) {
            throw new PipedriveConfigurationException(sprintf('Cipher method %s does not exist', $sCipher));
        }
        if (null === $this->getNonceSalt()) {
            return $plaintext;
        }

        $key = hash('sha256', $this->getNonceSalt());
        $ivLen = openssl_cipher_iv_length($sCipher);
        $iv = openssl_random_pseudo_bytes($ivLen, $crypto_strong);

        if ($iv === false || $crypto_strong === false) {
            throw new PipedriveConfigurationException('Bad Random length');
        }
        $cipherTextRaw = openssl_encrypt($plaintext, $sCipher, $key, $options = 0, $iv, $tag);

        return base64_encode($iv . $cipherTextRaw . '..' . $tag);
    }

    /**
     * @param string $string
     * @param string $sCipher
     *
     * @throws PipedriveMetaException
     *
     * @return false|string|null
     */
    protected function decrypt(string $string, string $sCipher = 'aes-128-gcm')
    {
        if (empty($string) || !$this->isBase64Encoded($string)) {
            return '';
        }
        if (null === $this->getNonceSalt()) {
            return $this->isBase64Encoded($string) ? null : $string;
        }
        $stringDecode = base64_decode($string);
        $encExploded = explode('..', $stringDecode);
        $enc = array_shift($encExploded);
        $tag = implode('', $encExploded);
        $key = hash('sha256', $this->getNonceSalt());
        $ivLen = openssl_cipher_iv_length($sCipher);
        $iv = substr($enc, 0, $ivLen);
        $cipherTextRaw = substr($enc, $ivLen);

        return openssl_decrypt($cipherTextRaw, $sCipher, $key, $options = 0, $iv, $tag);
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    private function isBase64Encoded(string $string): bool
    {
        return base64_encode(base64_decode($string)) === $string;
    }

    /**
     * @return false|string|null
     */
    private function generateSecureSalt()
    {
        $rand = sprintf('%s', mt_rand());

        return password_hash(uniqid($rand, true), PASSWORD_BCRYPT);
    }

    /**
     * @param bool $force
     *
     * @throws PipedriveMetaException
     *
     * @return false|int
     */
    public function createSalt(bool $force = false)
    {
        if ($force === true) {
            $this->metaWriterService->delete(self::PIPEDRIVE_CONF_NAME);
        }
        if ($this->metaReaderService->exists(self::PIPEDRIVE_CONF_NAME) && $this->metaReaderService->hasKey(
                'nonce_salt',
                self::PIPEDRIVE_CONF_NAME
            )) {
            return -1;
        }

        return $this->metaWriterService->save(self::PIPEDRIVE_CONF_NAME, ['nonce_salt' => $this->generateSecureSalt()]);
    }

    /**
     * @throws PipedriveMetaException
     *
     * @return string|null
     */
    private function getNonceSalt(): ?string
    {
        $data = $this->metaReaderService->readFromFile(self::PIPEDRIVE_CONF_NAME);

        return $data['nonce_salt'] ?? null;
    }

    /**
     * @param array $hContact
     *
     * @throws PipedriveConfigurationException
     *
     * @return array
     */
    public function formatAddressByResponse(array $hContact): array
    {
        if (empty($hContact)) {
            throw new PipedriveConfigurationException('Invalid contact');
        }

        $leadFields = $this->matchSelectedAddressFreeField();
        $lsField = $leadFields['pipedrive_ls_field'];

        $ahEmail = array_map(
            static function ($email) {
                if (!array_key_exists('primary', $email) && $email['primary'] === true) {
                    return null;
                }

                return $email;
            },
            $hContact['email']
        );
        $email = array_filter($ahEmail);
        $primaryEmail = is_array($email[0]) && array_key_exists('value', $email[0]) ? $email[0]['value'] : '';

        $ahPhone = array_map(
            static function ($phone) {
                if (!array_key_exists('primary', $phone) && $phone['primary'] === true) {
                    return null;
                }

                return $phone;
            },
            $hContact['phone']
        );

        $phone = array_filter($ahPhone);
        $primaryPhone = is_array($phone[0]) && array_key_exists('value', $phone[0]) ? $phone[0]['value'] : '';

        return [
            'lead'            => 1,
            'typ'             => !empty($hContact['org_name']) ? 'firma' : 'herr',
            'sprache'         => 'deutsch',
            'name'            => $hContact['org_name'] ?? $hContact['name'],
            'vorname'         => empty($hContact['first_name']) ? 'Pipedrive - ' : $hContact['first_name'],
            'nachname'        => empty($hContact['last_name']) ? $primaryEmail : $hContact['last_name'],
            'land'            => empty($hContact['country']) ? 'DE' : $hContact['country'],
            'telefon'         => $primaryPhone ?? '',
            'email'           => $primaryEmail,
            'kundenfreigabe'  => 1,
            'waehrung'        => 'EUR',
            'ansprechpartner' => !empty($hContact['org_name']) ? $hContact['name'] : '',
            $lsField          => empty($hContact['label']) ? '' : $hContact['label'],
        ];
    }

    /**
     * @throws PipedriveConfigurationException
     *
     * @return array
     */
    public function matchSelectedAddressFreeField(): array
    {
        $hFields = [];
        $asAddressFreeFieldValues = $this->propertyGateway->getConfiguredFreeAddressFieldValues();
        $pdConfFields = [
            'pipedrive_ls_field' => $this->tryGetConfiguration('pipedrive_ls_field'),
        ];

        foreach ($asAddressFreeFieldValues as $fieldName) {
            $addrField = 'adresse' . $fieldName;
            if (in_array($addrField, $pdConfFields, true)) {
                $indexField = array_search($addrField, $pdConfFields, true);
                $hFields[$indexField] = $fieldName;
            }
        }

        if (empty($hFields)) {
            throw new PipedriveConfigurationException('Pipedrive Label-Status fields cannot be matched');
        }

        return $hFields;
    }

    /**
     * @param array $address
     *
     * @throws PipedriveConfigurationException
     *
     * @return array
     */
    public function formatAddressToPipedriveContact(array $address): array
    {
        if (empty($address)) {
            throw new PipedriveConfigurationException('Address is invalid');
        }

        $leadFields = $this->matchSelectedAddressFreeField();
        $lsField = $leadFields['pipedrive_ls_field'];

        return [
            'email'      => $address['email'],
            'first_name' => empty($address['vorname']) ? $address['name'] : $address['vorname'],
            'last_name'  => empty($address['nachname']) ? $address['name'] : $address['nachname'],
            'name'       => $address['name'],
            'phone'      => $address['telefon'],
            'label'      => $address[$lsField],
        ];
    }

    /**
     * @param array $hDeal
     *
     * @throws PipedriveConfigurationException
     *
     * @return array
     */
    public function formatDealToInternal(array $hDeal): array
    {
        if (empty($hDeal)) {
            throw new PipedriveConfigurationException('Error! Deal cannot be formatted for Xentral');
        }

        $dealStage = $this->propertyGateway->getMappingByValueAndType($hDeal['stage_id'], 'deals');
        $asAddTime = [];
        $addTime = $hDeal['add_time'] ?? null;
        if ($addTime !== null) {
            $asAddTime = explode(' ', $addTime);
        }

        return [
            'bezeichnung'      => $hDeal['title'],
            'datum_angelegt'   => !empty($asAddTime) ? $asAddTime[0] : null,
            'zeit_angelegt'    => !empty($asAddTime) ? $asAddTime[1] : null,
            'datum_erinnerung' => null,
            'zeit_erinnerung'  => null,
            'betrag'           => array_key_exists('value', $hDeal) ? (float)$hDeal['value'] : 0.00,
            'stages'           => !empty($dealStage['wiedervorlage_stage_id']) ?
                $dealStage['wiedervorlage_stage_id'] : 0,
            'chance'           => !empty($hDeal['probability']) ? $hDeal['probability'] : 0,
        ];
    }

    /**
     * @param array $resubmission
     *
     * @throws PipedriveConfigurationException
     *
     * @return array
     */
    public function formatResubmissionToPipedriveDeal(array $resubmission): array
    {
        if (empty($resubmission)) {
            throw new PipedriveConfigurationException('Resubmission is invalid');
        }

        $status = 'open';
        if (!empty($resubmission['abgeschlossen'])) {
            if ($resubmission['status'] === 'gewonnen') {
                $status = 'won';
            } elseif ($resubmission['status'] === 'verloren') {
                $status = 'lost';
            }
        }

        $mapping = $this->pipedriveDealGateway->getMappingStageByResubmissionStageId($resubmission['stages']);

        return [
            'title'       => $resubmission['bezeichnung'],
            'stage_id'    => !empty($mapping) ? $mapping['value'] : null,
            'value'       => empty($resubmission['betrag']) ? 0.00 : $resubmission['betrag'],
            'probability' => $resubmission['chance'],
            'status'      => $status,
        ];
    }

    /**
     * @param array $settings
     *
     * @throws PipedriveConfigurationException
     */
    public function setSettings($settings = []): void
    {
        $this->trySetConfiguration(
            self::PIPEDRIVE_SETTINGS,
            json_encode($settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)
        );
    }

    /**
     * @throws PipedriveConfigurationException
     * @throws PipedriveMetaException
     *
     * @return array
     */
    public function getSettings(): array
    {
        $settingsRaw = $this->tryGetConfiguration(self::PIPEDRIVE_SETTINGS);
        if (empty($settingsRaw)) {
            return static::$_defaultSettings;
        }

        $settings = json_decode($settingsRaw, true);
        if ($settings === null && json_last_error() === JSON_ERROR_NONE) {
            throw new PipedriveConfigurationException(json_last_error_msg());
        }

        if (empty($settings)) {
            return static::$_defaultSettings;
        }

        if (array_key_exists('pd_api_key', $settings) && !empty($settings['pd_api_key'])) {
            $settings['pd_api_key'] = $this->decrypt($settings['pd_api_key']);
        }

        return $settings;
    }

    /**
     * @param int $contactId
     *
     * @throws PipedriveConfigurationException
     * @throws PipedriveMetaException
     *
     * @return void
     */
    public function addContactToGroup(int $contactId = 0): void
    {
        $defaultSettings = $this->getSettings();
        $contactGrpId = array_key_exists('pd_contact_grp', $defaultSettings) ? $defaultSettings['pd_contact_grp'] : 0;
        if (!empty($contactGrpId)) {
            $this->addAddressRoleWrapper->add($contactId, $contactGrpId);
        }
    }

}
