<?php

namespace Xentral\Modules\Api\Resource;

use Aura\SqlQuery\Exception;
use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;
use Xentral\Modules\Api\Controller\Version1\TrackingNumberController;

/**
 * Ressource für Trackingnummern
 */
class TrackingNumberResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'versand';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'tracking'                => 'v.tracking %LIKE%',
            'tracking_equals'         => 'v.tracking LIKE',
            'tracking_startswith'     => 'v.tracking LIKE%',
            'tracking_endswith'       => 'v.tracking %LIKE',
            'lieferschein'            => 'l.belegnr %LIKE%',
            'lieferschein_equals'     => 'l.belegnr LIKE',
            'lieferschein_startswith' => 'l.belegnr LIKE%',
            'lieferschein_endswith'   => 'l.belegnr %LIKE',
            'auftrag'                 => 'au.belegnr %LIKE%',
            'auftrag_equals'          => 'au.belegnr LIKE',
            'auftrag_startswith'      => 'au.belegnr LIKE%',
            'auftrag_endswith'        => 'au.belegnr %LIKE',
            'internet'                => 'au.internet %LIKE%',
            'internet_equals'         => 'au.internet LIKE',
            'internet_startswith'     => 'au.internet LIKE%',
            'internet_endswith'       => 'au.internet %LIKE',
            'versandart'              => 'l.versandart LIKE',
            'versendet_am'            => 'v.versendet_am LIKE',
            'versendet_am_gt'         => 'v.versendet_am >',
            'versendet_am_gte'        => 'v.versendet_am >=',
            'versendet_am_lt'         => 'v.versendet_am <',
            'versendet_am_lte'        => 'v.versendet_am <=',
            'abgeschlossen'           => 'v.abgeschlossen =',
            'adresse'                 => 'v.adresse =',
            'projekt'                 => 'v.projekt =',
            'land'                    => 'l.land =',
        ]);

        $this->registerSortingParams([
            'tracking'      => 'v.tracking',
            'auftrag'       => 'au.belegnr',
            'lieferschein'  => 'l.belegnr',
            'versandart'    => 'l.versandart',
            'versendet_am'  => 'v.versendet_am',
            'abgeschlossen' => 'v.abgeschlossen',
        ]);

        /** Minimale Validation-Rules; die eigentliche Prüfung findet im Controller statt */
        /** @see TrackingNumberController */
        $this->registerValidationRules([
            'tracking' => 'required',
        ]);

        $this->registerIncludes([
            'projekt' => [
                'key'      => 'projekt',
                'resource' => ProjectResource::class,
                'columns'  => [
                    'p.id',
                    'p.name',
                    'p.abkuerzung',
                    'p.beschreibung',
                    'p.farbe',
                ],
            ],
        ]);
    }

    /**
     * @throws Exception
     *
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                'v.id',
                'v.tracking',
                'v.adresse',
                //'v.auftrag',
                'au.internet',
                'au.belegnr AS auftrag',
                //'v.lieferschein',
                'l.belegnr AS lieferschein',
                //'v.rechnung',
                //'r.belegnr AS rechnung',
                'v.projekt',
                //'v.versandart',
                'l.versandart',
                'l.land',
                'v.gewicht',
                //'v.freigegeben',
                //'v.bearbeiter',
                //'v.versender',
                'v.abgeschlossen',
                'v.versendet_am',
                //'v.versandunternehmen',
                //'v.download',
                //'v.firma',
                //'v.logdatei',
                //'v.keinetrackingmail',
                //'v.versendet_am_zeitstempel',
                //'v.weitererlieferschein',
                'v.anzahlpakete',
                //'v.gelesen',
                //'v.paketmarkegedruckt',
                //'v.papieregedruckt',
                //'v.versandzweigeteilt',
                //'v.improzess',
                //'v.improzessuser',
                //'v.cronjob',
                //'v.adressvalidation',
                'v.retoure',
                //'v.bundesstaat',
                'v.klaergrund',
            ])
            ->from(self::TABLE_NAME . ' AS v')
            ->leftJoin('lieferschein AS l', 'v.lieferschein = l.id')
            ->leftJoin('auftrag AS au', 'l.auftragid = au.id');
    }

    /**
     * @throws Exception
     *
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('v.id = :id');
    }

    /**
     * @return false
     */
    protected function selectIdsQuery()
    {
        return false;
    }

    /**
     * Insert-Action hat speziellen Controller
     *
     * @see TrackingNumberController::createAction()
     *
     * @return InsertQuery
     */
    protected function insertQuery()
    {
        return $this->db->insert()->into(self::TABLE_NAME);
    }

    /**
     * Update-Action hat speziellen Controller
     *
     * @see TrackingNumberController::updateAction()
     *
     * @return UpdateQuery
     */
    protected function updateQuery()
    {
        return $this->db->update()->table(self::TABLE_NAME)->where('id = :id');
    }

    /**
     * @return false
     */
    protected function deleteQuery()
    {
        return false;
    }
}
