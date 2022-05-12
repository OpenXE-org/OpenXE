<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\SubscriptionCycle\Data\SubscriptionCycleAutoSubscriptionData;
use Xentral\Modules\SubscriptionCycle\Exception\AutoSubscriptionNotFoundException;

final class SubscriptionCycleAutoSubscriptionGateway
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
     * @param int $autoSubscriptionId
     *
     * @throws AutoSubscriptionNotFoundException
     *
     * @return SubscriptionCycleAutoSubscriptionData
     */
    public function getById(int $autoSubscriptionId): SubscriptionCycleAutoSubscriptionData
    {
        $sql =
            'SELECT 
                sca.id, 
                sca.project_id, 
                sca.article_id, 
                sca.price_cycle, 
                sca.document_type, 
                sca.subscription_group_id, 
                sca.position, 
                sca.first_date_type, 
                sca.prevent_auto_dispatch, 
                sca.auto_email_confirmation, 
                sca.business_letter_pattern_id, 
                sca.add_pdf 
            FROM `subscription_cycle_autosubscription` AS `sca`
            WHERE sca.id = :id';

        $data = $this->db->fetchRow($sql, ['id' => $autoSubscriptionId]);

        if (empty($data)) {
            throw new AutoSubscriptionNotFoundException('No data found for id: ' . $autoSubscriptionId);
        }

        return SubscriptionCycleAutoSubscriptionData::fromDbState($data);
    }

    /**
     * @param int $docId
     *
     * @return array
     */
    public function findAutoSubscriptionData(int $docId)
    {
        $sql =
            'SELECT 
            ap.artikel,
            art.nummer,
            art.name_de AS `bezeichnung`,
            ap.menge,
            ap.preis,
            ap.rabatt,
            (CASE sca.first_date_type
                WHEN \'monatsanfang\' THEN DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), \'%Y-%m-01\')
                WHEN \'monatsmitte\' THEN (
                    IF(
                        DAY(CURDATE()) > 15,
                        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), \'%Y-%m-15\'),
                        DATE_FORMAT(CURDATE(), \'%Y-%m-15\')
                    )
                )
                ELSE au.datum
            END) AS `startdatum`,
            1 AS `wiederholend`,
            \'angelegt\' AS `status`,
            (CASE sca.price_cycle
                WHEN \'monatspreis\' THEN \'monat\'
                WHEN \'jahrespreis\' THEN \'jahr\'
                ELSE sca.price_cycle
            END) AS `preisart`,
            sca.position AS `sort`,
            sca.project_id AS `projekt`,
            NOW() AS `angelegtam`,
            0 AS `experte`,
            au.waehrung,
            0 AS `beschreibungersetzten`,
            art.umsatzsteuer AS `steuerklasse`,
            1 AS `zahlzyklus`,
            0 AS `rechnung`,
            sca.document_type AS `dokument`,
            sca.subscription_group_id AS `gruppe`,
            0 AS `angelegtvon`,
            0 AS `abgerechnet`,
            sca.auto_email_confirmation,
            sca.business_letter_pattern_id,
            sca.add_pdf,
            sca.prevent_auto_dispatch,
            au.sprache,
            gba.subjekt,
            adr.email,
            adr.abweichendeemailab,
            au.adresse
            FROM `auftrag_position` AS `ap`
            INNER JOIN `auftrag` AS `au` ON au.id = ap.auftrag
            INNER JOIN `artikel` AS `art` ON art.id = ap.artikel
            INNER JOIN `adresse` AS `adr` ON au.adresse = adr.id
            INNER JOIN `subscription_cycle_autosubscription` AS `sca` ON sca.article_id = ap.artikel
            LEFT JOIN `geschaeftsbrief_vorlagen` AS `gba` ON gba.id = sca.business_letter_pattern_id
            LEFT JOIN `abrechnungsartikel` AS `ara` ON ara.artikel = sca.article_id AND ara.adresse = au.adresse
            WHERE au.id = :docId
            AND IF(sca.project_id = 0, 1, au.projekt = sca.project_id)
            AND ara.artikel IS NULL';

        return $this->db->fetchAll($sql, ['docId' => $docId]);
    }
}
