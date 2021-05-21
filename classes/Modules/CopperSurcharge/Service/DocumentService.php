<?php

declare(strict_types=1);

namespace Xentral\Modules\CopperSurcharge\Service;

use Xentral\Components\Database\Database;

final class DocumentService
{

    /** @var Database $db */
    private $db;

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $docType
     * @param int    $docId
     * @param int    $precedingPositionId
     * @param int    $followingPositionId
     */
    public function updatePositionSort(
        string $docType,
        int $docId,
        int $precedingPositionId,
        int $followingPositionId
    ): void {
        $sql =
            "SELECT `sort`
            FROM `" . $docType . "_position` 
            WHERE `id` = :preceding_position_id
            LIMIT 1";

        $precedingSort = $this->db->fetchValue($sql, ['preceding_position_id' => $precedingPositionId]);

        $this->db->perform(
            "UPDATE `" . $docType . "_position`
            SET `sort` = `sort` + 1
            WHERE `sort` > :preceding_sort
            AND " . $docType . " = :doc_id",
            ['preceding_sort' => $precedingSort, 'doc_id' => $docId]
        );

        $this->db->perform(
            "UPDATE `beleg_zwischenpositionen`
            SET `pos` = `pos` + 1
            WHERE `doctype` = :doc_type
            AND `doctypeid` = :doc_id
            AND `pos` >= :preceding_sort",
            [
                'doc_type'       => $docType,
                'doc_id'         => $docId,
                'preceding_sort' => $precedingSort,
            ]
        );

        $this->db->perform(
            "UPDATE `" . $docType . "_position`
            SET `sort` = :preceding_sort + 1
            WHERE `id` = :following_position_id",
            ['following_position_id' => $followingPositionId, 'preceding_sort' => $precedingSort]
        );
    }

    /**
     * @param int $betweenId
     * @param int $betweenSort
     */
    public function updateBetweenSort(int $betweenId, int $betweenSort): void
    {
        $this->db->perform(
            "UPDATE `beleg_zwischenpositionen` 
            SET `pos` = :between_pos
            WHERE `id` = :between_id",
            [
                'between_pos' => $betweenSort,
                'between_id'  => $betweenId,

            ]
        );
    }

    /**
     * @param string $docType
     * @param int    $docId
     * @param int    $copperSurchargeArticleId
     */
    public function deleteCopperSurchargePositions(string $docType, int $docId, int $copperSurchargeArticleId): void
    {
        $sql =
            "DELETE 
            FROM `" . $docType . "_position` 
            WHERE `artikel` = :copper_surcharge_article_id
            AND `" . $docType . "` = :doc_id";

        $this->db->perform($sql, ['copper_surcharge_article_id' => $copperSurchargeArticleId, 'doc_id' => $docId]);
    }

    /**
     * @param string $docType
     * @param int    $docId
     */
    public function updatePositionSorts(string $docType, int $docId): void
    {
        $sql =
            "SELECT pos.id, pos.sort
            FROM `" . $docType . "_position` AS pos
            WHERE " . $docType . " = :doc_id
            ORDER BY pos.sort";
        $positions = $this->db->fetchAll($sql, ['doc_id' => $docId]);
        if (!empty($positions)) {
            foreach ($positions as $key => $position) {
                $sql =
                    "UPDATE `" . $docType . "_position` SET `sort` = :sort WHERE `id` = :pos_id";
                $this->db->perform($sql, ['sort' => $key + 1, 'pos_id' => $position['id']]);
            }
        }
    }

    /**
     * @param string $docType
     * @param        $positionId
     * @param float  $contributionMargin
     */
    public function updatePositionContributionMargin(string $docType, $positionId, float $contributionMargin)
    {
        $sql =
            "UPDATE `" . $docType . "_position` 
            SET `deckungsbeitrag` = :contribution_margin
            WHERE `id` = :position_id";

        $this->db->perform($sql, ['contribution_margin' => $contributionMargin, 'position_id' => $positionId]);
    }

    /**
     * @param string $docType
     * @param int    $posId
     * @param float  $purchasePrice
     */
    public function updatePositionPurchasePrice(string $docType, int $posId, float $purchasePrice): void
    {
        $sql =
            "UPDATE `" . $docType . "_position` 
            SET `einkaufspreis` = :purchase_price
            WHERE `id` = :position_id";

        $this->db->perform($sql, ['purchase_price' => $purchasePrice, 'position_id' => $posId]);
    }

}
