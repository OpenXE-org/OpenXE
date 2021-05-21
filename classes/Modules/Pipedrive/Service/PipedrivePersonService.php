<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Service;

use Xentral\Modules\Pipedrive\Exception\PipedriveClientException;
use Xentral\Modules\Pipedrive\Exception\PipedriveConfigurationException;
use Xentral\Modules\Pipedrive\Exception\PipedriveHttpClientException;
use Xentral\Modules\Pipedrive\Exception\PipedriveMetaException;
use Xentral\Modules\Pipedrive\Exception\PipedrivePersonServiceException;
use Xentral\Modules\Pipedrive\Exception\PipedriveValidatorException;
use Xentral\Modules\Pipedrive\Validator\PipedrivePersonValidator;

final class PipedrivePersonService
{

    /** @var array $itemOption */
    private $itemOption = [
        'limit'           => 100,
        'items'           => 'person',
        'start'           => 0,
        'since_timestamp' => '1970-01-01 23:59:59',
    ];

    /** @var string[] $allowedSyncPeronOptions */
    private $allowedSyncPeronOptions = [
        'pipedrive_recently_updated' => 'getRecentlyUpdatedPersons',
        'pipedrive_all'              => 'getAllPersons',
    ];

    /** @var PipedriveClientService $client */
    private $client;

    /** @var PipedriveMetaReaderService $metaReaderService */
    private $metaReaderService;

    /** @var PipedrivePersonValidator $validator */
    private $validator;

    /**
     * PipedrivePersonService constructor.
     *
     * @param PipedriveClientService     $client
     * @param PipedrivePersonValidator   $validator
     * @param PipedriveMetaReaderService $metaReaderService
     */
    public function __construct(
        PipedriveClientService $client,
        PipedrivePersonValidator $validator,
        PipedriveMetaReaderService $metaReaderService
    ) {
        $this->client = $client;
        $this->validator = $validator;
        $this->metaReaderService = $metaReaderService;
    }

    /**
     * @param array $options
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function getAllPersons(array $options = []): PipedriveServerResponseInterface
    {
        return $this->client->read('allPersons', $options);
    }

    /**
     * @param array $options
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function getRecentlyUpdatedPersons(array $options = []): PipedriveServerResponseInterface
    {
        $options = array_merge($this->itemOption, $options);

        return $this->client->read('recentlyUpdatedPersons', $options);
    }

    /**
     * @param int $contactId
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function deleteContact(int $contactId = 0): PipedriveServerResponseInterface
    {
        return $this->client->delete('deleteContact', [$contactId]);
    }

    /**
     * @param array $data
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     * @throws PipedrivePersonServiceException
     * @throws PipedriveValidatorException
     *
     * @return PipedriveServerResponseInterface
     */
    public function createContact(array $data = []): PipedriveServerResponseInterface
    {
        if (!$this->validator->isValid($data)) {
            throw new PipedrivePersonServiceException(
                sprintf('%s::createContact Invalid contact data', get_class($this))
            );
        }
        $identity = $this->formatContactIdentity($this->validator->getData());

        return $this->client->post('createContact', $identity);
    }

    /**
     * @param int   $contactId
     * @param array $data
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     * @throws PipedrivePersonServiceException
     * @throws PipedriveValidatorException
     *
     * @return PipedriveServerResponseInterface
     */
    public function updateContactById(int $contactId, array $data = []): PipedriveServerResponseInterface
    {
        if (!$this->validator->isValid($data)) {
            throw new PipedrivePersonServiceException(
                sprintf('%s::updateContactById Invalid contact data', get_class($this))
            );
        }

        $identity = $this->formatContactIdentity($this->validator->getData());

        return $this->client->put('updateContact', $identity, [$contactId]);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function formatContactIdentity(array $data): array
    {
        $identity = [];
        foreach ($data as $property => $value) {
            if (in_array($property, ['email', 'phone'])) {
                $identity[$property][] = [
                    'label' => 'other',
                    'value' => $value,
                ];
            } else {
                $identity[$property] = $value;
            }
        }

        return $identity;
    }

    /**
     * @param string $type
     * @param array  $options
     *
     * @throws PipedrivePersonServiceException
     * @throws PipedriveMetaException
     *
     * @return PipedriveServerResponseInterface
     */
    public function pullPersons(string $type = 'pipedrive_all', array $options = []): PipedriveServerResponseInterface
    {
        $options = array_merge($this->itemOption, $options);

        if ('pipedrive_all' !== $type && !array_key_exists($type, $this->allowedSyncPeronOptions)) {
            throw new PipedrivePersonServiceException(sprintf('Sync Type %s not allowed', $type));
        }

        $metaFile = sprintf('%s.json', $type);

        if ($type !== 'pipedrive_all') {
            $options = $this->addMetaOption($metaFile, $options);
        }

        /** @var PipedriveServerResponseInterface $response */
        return $this->{$this->allowedSyncPeronOptions[$type]}($options);
    }

    /**
     * @param int $contactId
     *
     * @throws PipedriveClientException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     * @throws PipedriveConfigurationException
     *
     * @return PipedriveServerResponseInterface
     */
    public function getContactById(int $contactId): PipedriveServerResponseInterface
    {
        return $this->client->read('getContactById', [], [$contactId]);
    }

    /**
     * @param string $metaFile
     * @param array  $options
     *
     * @throws PipedriveMetaException
     *
     * @return array
     */
    private function addMetaOption(string $metaFile, array $options) : array
    {
        $metaInfo = $this->metaReaderService->readFromFile($metaFile);

        if (!empty($metaInfo)) {
            $timeOffset = $metaInfo['timeOffset'];
            $offset = strtotime($metaInfo['timeOffset']);
            if ($offset !== false) {
                $timeOffset = gmdate('Y-m-d H:i:s', $offset - 3600);
            }
            $options = array_merge($options, ['since_timestamp' => $timeOffset]);
        }
        return  $options;
    }
}
