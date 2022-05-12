<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\SubscriptionCycle\Data\SubscriptionCycleAutoSubscriptionData;
use Xentral\Modules\SubscriptionCycle\Exception\AutoSubscriptionNotFoundException;
use Xentral\Modules\SubscriptionCycle\Exception\OrderNotFoundException;
use Xentral\Modules\SubscriptionCycle\Exception\RuntimeException;

final class SubscriptionCycleAutoSubscriptionService
{
    /** @var Database */
    private $db;

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param SubscriptionCycleAutoSubscriptionData $autoSubscription
     *
     * @return int
     */
    public function create(SubscriptionCycleAutoSubscriptionData $autoSubscription): int
    {
        $sql =
            'INSERT INTO `subscription_cycle_autosubscription` (
                `project_id`,
                `article_id`,
                `price_cycle`,
                `document_type`,
                `subscription_group_id`,
                `position`,
                `first_date_type`,
                `prevent_auto_dispatch`,
                `auto_email_confirmation`,
                `business_letter_pattern_id`,
                `add_pdf`
            ) 
            VALUES (
                :projectId,
                :articleId,
                :priceCycle,
                :documentType,
                :subscriptionGroupId,
                :position,
                :firstDateType,
                :preventAutoDispatch,
                :autoEmailConfirmation,
                :businessLetterPatternId,
                :addPdf    
            )';
        $values = [
            'projectId'               => $autoSubscription->getProjectId(),
            'articleId'               => $autoSubscription->getArticleId(),
            'priceCycle'              => $autoSubscription->getPriceCycle(),
            'documentType'            => $autoSubscription->getDocumentType(),
            'subscriptionGroupId'     => $autoSubscription->getSubscriptionGroupId(),
            'position'                => $autoSubscription->getPosition(),
            'firstDateType'           => $autoSubscription->getFirstDateType(),
            'preventAutoDispatch'     => $autoSubscription->getPreventAutoDispatch(),
            'autoEmailConfirmation'   => $autoSubscription->getAutoEmailConfirmation(),
            'businessLetterPatternId' => $autoSubscription->getBusinessLetterPatternId(),
            'addPdf'                  => $autoSubscription->getAddPdf(),
        ];

        $this->db->perform($sql, $values);

        return $this->db->lastInsertId();
    }

    /**
     * @param SubscriptionCycleAutoSubscriptionData $autoSubscription
     *
     * @throws AutoSubscriptionNotFoundException
     */
    public function edit(SubscriptionCycleAutoSubscriptionData $autoSubscription): void
    {
        if (empty($autoSubscription->getId())) {
            throw new AutoSubscriptionNotFoundException('No ID is found for an update');
        }

        $sql =
            'UPDATE `subscription_cycle_autosubscription` 
             SET
                `project_id` = :projectId, 
                `article_id` = :articleId,
                `price_cycle` = :priceCycle,
                `document_type` = :documentType,
                `subscription_group_id` = :subscriptionGroupId,
                `position` = :position,
                `first_date_type` = :firstDateType,
                `prevent_auto_dispatch` = :preventAutoDispatch,
                `auto_email_confirmation` = :autoEmailConfirmation,
                `business_letter_pattern_id` = :businessLetterPatternId,
                `add_pdf` = :addPdf
            WHERE `id` = :id';

        $values = [
            'id'                      => $autoSubscription->getId(),
            'projectId'               => $autoSubscription->getProjectId(),
            'articleId'               => $autoSubscription->getArticleId(),
            'priceCycle'              => $autoSubscription->getPriceCycle(),
            'documentType'            => $autoSubscription->getDocumentType(),
            'subscriptionGroupId'     => $autoSubscription->getSubscriptionGroupId(),
            'position'                => $autoSubscription->getPosition(),
            'firstDateType'           => $autoSubscription->getFirstDateType(),
            'preventAutoDispatch'     => $autoSubscription->getPreventAutoDispatch(),
            'autoEmailConfirmation'   => $autoSubscription->getAutoEmailConfirmation(),
            'businessLetterPatternId' => $autoSubscription->getBusinessLetterPatternId(),
            'addPdf'                  => $autoSubscription->getAddPdf(),
        ];
        $this->db->perform($sql, $values);
    }

    /**
     * @param int $autosubscriptionId
     *
     * @throws AutoSubscriptionNotFoundException
     * @throws RuntimeException
     */
    public function removeById(int $autosubscriptionId): void
    {
        $sql = 'SELECT s.id FROM `subscription_cycle_autosubscription` AS `s` WHERE s.id = :id';
        $data = $this->db->fetchRow($sql, ['id' => $autosubscriptionId]);

        if (empty($data)) {
            throw new AutoSubscriptionNotFoundException(
                'The autosubscription with the following id does not exist:' . $autosubscriptionId
            );
        }

        $sql = 'DELETE FROM `subscription_cycle_autosubscription` WHERE `id` = :id';
        $numAffected = (int)$this->db->fetchAffected($sql, ['id' => $autosubscriptionId]);

        if ($numAffected === 0) {
            throw new RuntimeException('Autosubscription could not be deleted, id: ' . $autosubscriptionId);
        }
    }

    /**
     * @param int $orderId
     */
    public function preventAutoDispatch(int $orderId): void
    {
        $sql = 'SELECT a.id FROM `auftrag` AS `a` WHERE a.id = :id';
        $data = $this->db->fetchRow($sql, ['id' => $orderId]);

        if (empty($data)) {
            throw new OrderNotFoundException('The order with the following id does not exist:' . $orderId);
        }

        $sql = 'UPDATE `auftrag` SET `autoversand` = 0 WHERE `id` = :id';
        $this->db->perform($sql, ['id' => (int)$orderId]);
    }
}
