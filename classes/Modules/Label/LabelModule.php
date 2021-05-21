<?php

namespace Xentral\Modules\Label;

use Xentral\Modules\Label\Exception\InvalidArgumentException;
use Xentral\Modules\Label\Exception\LabelAssignException;
use Xentral\Modules\Label\Exception\LabelTypeNotFoundException;

/**
 * Simple Facade for accessing LabelService and LabelGateway
 */
final class LabelModule
{
    /** @var LabelService $service */
    private $service;

    /** @var LabelGateway $gateway */
    private $gateway;

    /**
     * @param LabelService $service
     * @param LabelGateway $gateway
     */
    public function __construct(LabelService $service, LabelGateway $gateway)
    {
        $this->service = $service;
        $this->gateway = $gateway;
    }

    /**
     * @param string $referenceTable
     * @param int    $referenceId
     * @param string $labelType
     *
     * @throws InvalidArgumentException
     * @throws LabelTypeNotFoundException
     * @throws LabelAssignException If assignment fails
     *
     * @return int Created ID from label_reference table
     */
    public function assignLabel($referenceTable, $referenceId, $labelType)
    {
        return $this->service->assignLabel($referenceTable, $referenceId, $labelType);
    }

    /**
     * @param string $referenceTable
     * @param int    $referenceId
     * @param string $labelType
     *
     * @throws InvalidArgumentException
     * @throws LabelAssignException If assignment fails
     *
     * @return void
     */
    public function unassignLabel($referenceTable, $referenceId, $labelType)
    {
        $this->service->unassignLabel($referenceTable, $referenceId, $labelType);
    }

    /**
     * @param string $referenceTable
     * @param int    $referenceId
     *
     * @return array
     */
    public function findLabelsByReference($referenceTable, $referenceId)
    {
        return $this->gateway->findLabelsByReference($referenceTable, $referenceId);
    }

    /**
     * @param string      $referenceTable
     * @param int[]|array $referenceIds
     *
     * @return array
     */
    public function findLabelsByReferences($referenceTable, $referenceIds)
    {
        return $this->gateway->findLabelsByReferences($referenceTable, $referenceIds);
    }

    /**
     * @param string $referenceTable
     * @param int    $referenceId
     *
     * @return array
     */
    public function findLabelTypesByReference($referenceTable, $referenceId)
    {
        return $this->gateway->findLabelTypesByReference($referenceTable, $referenceId);
    }
}
