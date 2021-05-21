<?php

declare(strict_types=1);

namespace Xentral\Modules\PaymentMethod\Service;

use Xentral\Modules\PaymentMethod\Exception\InvalidArgumentException;
use Xentral\Components\Database\Database;
use Xentral\Modules\PaymentMethod\Data\PaymentMethodData;
use Xentral\Modules\PaymentMethod\Exception\PaymentMethodNotFoundException;

final class PaymentMethodService
{
    /** @var Database $database */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param PaymentMethodData $paymentMethod
     *
     * @return int
     */
    public function create(PaymentMethodData $paymentMethod): int
    {
        $this->db->perform(
            'INSERT INTO `zahlungsweisen` 
            (`modul`,`type`, `bezeichnung`, `einstellungen_json`, `freitext`, `aktiv`, `geloescht`, `projekt`,
            `automatischbezahlt`, `automatischbezahltverbindlichkeit`, `verhalten`) 
            VALUES (:module, :type, :name, :json, :text, :active, 0, :project_id,
             :auto_payed, :auto_payed_liability, :payment_behavior)',
            [
                'module'               => $paymentMethod->getModule(),
                'type'                 => $paymentMethod->getType(),
                'name'                 => $paymentMethod->getName(),
                'active'               => (int)$paymentMethod->isActive(),
                'json'                 => json_encode($paymentMethod->getSettings()),
                'text'                 => $paymentMethod->getText(),
                'project_id'           => $paymentMethod->getProjectId(),
                'auto_payed'           => (int)$paymentMethod->isAutoPayed(),
                'auto_payed_liability' => (int)$paymentMethod->isAutoPayedLiability(),
                'payment_behavior'     => $paymentMethod->getPaymentBehavior(),
            ]
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param PaymentMethodData $paymentMethod
     *
     * @throws InvalidArgumentException
     * @throws PaymentMethodNotFoundException
     */
    public function update(PaymentMethodData $paymentMethod): void
    {
        $paymentMethodId = $paymentMethod->getId();
        if ($paymentMethodId === 0) {
            throw new InvalidArgumentException('id is empty');
        }
        $this->get($paymentMethodId);
        $this->db->perform(
            'UPDATE `zahlungsweisen` 
            SET `modul` = :module, 
                `type` = :type,
                `bezeichnung` = :name,
                `aktiv` = :active,
                `einstellungen_json` = :json,
                `freitext` = :text,
                `projekt` = :project_id,
                `automatischbezahlt` = :auto_payed,
                `automatischbezahltverbindlichkeit` = :auto_payed_liability,
                `verhalten` = :payment_behavior,
                `geloescht` = :deleted
            WHERE `id` = :id',
            [
                'module'               => $paymentMethod->getModule(),
                'type'                 => $paymentMethod->getType(),
                'name'                 => $paymentMethod->getName(),
                'active'               => (int)$paymentMethod->isActive(),
                'json'                 => json_encode($paymentMethod->getSettings()),
                'text'                 => $paymentMethod->getText(),
                'project_id'           => $paymentMethod->getProjectId(),
                'auto_payed'           => (int)$paymentMethod->isAutoPayed(),
                'auto_payed_liability' => (int)$paymentMethod->isAutoPayedLiability(),
                'payment_behavior'     => $paymentMethod->getPaymentBehavior(),
                'deleted'              => (int)$paymentMethod->isDeleted(),
                'id'                   => $paymentMethodId,
            ]
        );
    }

    /**
     * @param int $id
     *
     * @throws PaymentMethodNotFoundException
     * @throws InvalidArgumentException
     *
     * @return PaymentMethodData
     */
    public function getFromId(int $id): PaymentMethodData
    {
        return PaymentMethodData::fromDbState($this->get($id));
    }

    /**
     * @param int $id
     *
     * @throws PaymentMethodNotFoundException
     *
     * @return void
     */
    public function delete($id): void
    {
        $this->get($id);
        $this->db->perform('DELETE FROM `zahlungsweisen` WHERE `id` = :id', ['id' => (int)$id]);
    }

    /**
     * @param int $id
     *
     * @throws PaymentMethodNotFoundException
     *
     * @return array
     */
    public function get($id): array
    {
        $ret = $this->db->fetchRow('SELECT * FROM `zahlungsweisen` WHERE `id` = :id', ['id' => (int)$id]);
        if (empty($ret)) {
            throw new PaymentMethodNotFoundException(sprintf('Payment method with id %d not found', $id));
        }

        return $ret;
    }
}
