<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\SubscriptionCycle\Data\SubscriptionCycleArticleData;

final class SubscriptionCycleArticleService
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
     * @param SubscriptionCycleArticleData $article
     *
     * @return int
     */
    public function create(SubscriptionCycleArticleData $article): int
    {
        $sql =
            'INSERT INTO `abrechnungsartikel` (
                `sort`,
                `artikel`,
                `bezeichnung`,
                `nummer`,
                `menge`,
                `preis`,
                `steuerklasse`,
                `rabatt`,
                `abgerechnet`,
                `startdatum`,
                `lieferdatum`,
                `abgerechnetbis`,
                `wiederholend`,
                `zahlzyklus`,
                `abgrechnetam`,
                `rechnung`,
                `projekt`,
                `adresse`,
                `status`,
                `bemerkung`,
                `beschreibung`,
                `dokument`,
                `preisart`,
                `enddatum`,
                `angelegtvon`,
                `angelegtam`,
                `experte`,
                `waehrung`,
                `beschreibungersetzten`,
                `gruppe`                  
            ) VALUES (
                :sort,
                :articleId,
                :articleName,
                :articleNumber,
                :amount,
                :price,
                :taxClass,
                :discount,
                :cleared,
                :startDate,
                :deliveranceDate,
                :clearedTill,
                :repeating,
                :payCycle,
                :clearedOn,
                :invoiceId,
                :projectId,
                :adressId,
                :status,
                :text,
                :description,
                :document,
                :priceType,
                :endDate,
                :createdBy,
                :createdDate,
                :expert,
                :currency,
                :replaceDescription,
                :subscriptionCycleGroupId 
            )';

        $values = [
            'sort'                     => $article->getSort(),
            'articleId'                => $article->getArticleId(),
            'articleName'              => $article->getArticleName(),
            'articleNumber'            => $article->getArticleNumber(),
            'amount'                   => $article->getAmount(),
            'price'                    => $article->getPrice(),
            'taxClass'                 => $article->getTaxClass(),
            'discount'                 => $article->getDiscount(),
            'cleared'                  => $article->isCleared(),
            'startDate'                => $article->getStartDate(),
            'deliveranceDate'          => $article->getDeliveranceDate(),
            'clearedTill'              => $article->getClearedTill(),
            'repeating'                => $article->isRepeating(),
            'payCycle'                 => $article->getPayCycle(),
            'clearedOn'                => $article->getClearedOn(),
            'invoiceId'                => $article->getInvoiceId(),
            'projectId'                => $article->getProjectId(),
            'adressId'                 => $article->getAdressId(),
            'status'                   => $article->getStatus(),
            'text'                     => $article->getText(),
            'description'              => $article->getDescription(),
            'document'                 => $article->getDocument(),
            'priceType'                => $article->getPriceType(),
            'endDate'                  => $article->getEndDate(),
            'createdBy'                => $article->getCreatedBy(),
            'createdDate'              => $article->getCreatedDate(),
            'expert'                   => $article->isExpert(),
            'currency'                 => $article->getCurrency(),
            'replaceDescription'       => $article->isReplaceDescription(),
            'subscriptionCycleGroupId' => $article->getSubscriptionCycleGroupId(),
        ];
        $this->db->perform($sql, $values);

        return $this->db->lastInsertId();
    }
}
