<?php

namespace Xentral\Modules\Label;

use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\DatabaseExceptionInterface;
use Xentral\Modules\Label\Exception\InvalidArgumentException;
use Xentral\Modules\Label\Exception\LabelAssignException;
use Xentral\Modules\Label\Exception\LabelTypeNotFoundException;

final class LabelService
{
    /** @var Database $db */
    private $db;

    /** @var LabelGateway $gateway */
    private $gateway;

    /**
     * @param Database     $db
     * @param LabelGateway $gateway
     */
    public function __construct(Database $db, LabelGateway $gateway)
    {
        $this->db = $db;
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
        $referenceTable = (string)$referenceTable;
        $referenceId = (int)$referenceId;
        $labelType = (string)$labelType;

        if ($referenceId <= 0) {
            throw new InvalidArgumentException('Could not assign label. Argument "referenceId" is empty.');
        }
        if (empty($referenceTable)) {
            throw new InvalidArgumentException('Could not assign label. Argument "referenceTable" is empty.');
        }
        if (empty($referenceTable)) {
            throw new InvalidArgumentException('Could not assign label. Argument "labelType" is empty.');
        }

        $labelTypeId = $this->gateway->getLabelTypeId($labelType);

        try {
            $this->db->perform(
                'INSERT INTO label_reference (reference_table, reference_id, label_type_id, created_at) 
                 VALUES (:reference_table, :reference_id, :label_type_id, CURRENT_TIMESTAMP)',
                [
                    'reference_table' => $referenceTable,
                    'reference_id'    => $referenceId,
                    'label_type_id'   => $labelTypeId,
                ]
            );
        } catch (DatabaseExceptionInterface $exception) {
            throw new LabelAssignException(
                sprintf(
                    'Could not assign label. Data: reference_table "%s", reference_id "%s", label_type "%s"',
                    $referenceTable, $referenceId, $labelType
                ), 0, $exception
            );
        }

        return $this->db->lastInsertId();
    }

    /**
     * @param string $referenceTable
     * @param int    $referenceId
     * @param string $labelType
     *
     * @throws InvalidArgumentException
     * @throws LabelAssignException If deletion of assignment fails
     *
     * @return void
     */
    public function unassignLabel($referenceTable, $referenceId, $labelType)
    {
        $referenceTable = (string)$referenceTable;
        $referenceId = (int)$referenceId;
        $labelType = (string)$labelType;

        if ($referenceId <= 0) {
            throw new InvalidArgumentException('Could not unassign label. Argument "referenceId" is empty.');
        }
        if (empty($referenceTable)) {
            throw new InvalidArgumentException('Could not unassign label. Argument "referenceTable" is empty.');
        }
        if (empty($referenceTable)) {
            throw new InvalidArgumentException('Could not unassign label. Argument "labelType" is empty.');
        }

        try {
            $labelReferenceId = (int)$this->db->fetchValue(
                'SELECT lr.id FROM label_reference AS lr 
                 INNER JOIN label_type AS lt ON lr.label_type_id = lt.id 
                 WHERE lt.type = :label_type
                   AND lr.reference_table = :reference_table
                   AND lr.reference_id = :reference_id',
                [
                    'reference_table' => $referenceTable,
                    'reference_id'    => $referenceId,
                    'label_type'      => $labelType,
                ]
            );

            $this->db->perform(
                'DELETE FROM label_reference WHERE id = :label_reference_id LIMIT 1',
                ['label_reference_id' => $labelReferenceId]
            );
        } catch (DatabaseExceptionInterface $exception) {
            throw new LabelAssignException(
                sprintf(
                    'Could not unassign label. Data: reference_table "%s", reference_id "%s", label_type "%s"',
                    $referenceTable, $referenceId, $labelType
                ), 0, $exception
            );
        }
    }
}
