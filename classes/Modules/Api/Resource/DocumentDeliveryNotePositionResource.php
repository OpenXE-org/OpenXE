<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressoure für Lieferschein-Positionen
 *
 * Ressource hat keinen eigenen Endpunkt; Ressource wird nur für Includes verwendet.
 */
class DocumentDeliveryNotePositionResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'lieferschein_position';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerSortingParams([
            'sort' => 'lipos.sort',
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('lipos.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db
            ->select()
            ->cols([
                'lipos.id',
                //'lipos.lieferschein', // Index
                'lipos.projekt',
                'lipos.artikel', // Index
                'lipos.bezeichnung',
                'lipos.beschreibung',
                //'lipos.internerkommentar',
                'lipos.nummer',
                'lipos.menge',
                'lipos.einheit',
                'lipos.vpe',
                'lipos.lieferdatum',
                'lipos.lieferdatumkw',
                'lipos.artikelnummerkunde',
                'lipos.kostenlos',
                //'lipos.sort',
                //'lipos.status',
                //'lipos.ausblenden_im_pdf ',
                'lipos.bemerkung',
                'lipos.geliefert',
                'lipos.abgerechnet',
                //'lipos.logdatei',
                //'lipos.lagertext',
                //'lipos.auftrag_position_id', // Index
                //'lipos.teilprojekt',
                //'lipos.freifeld1',
                //'lipos.freifeld2',
                //'lipos.freifeld3',
                //'lipos.freifeld4',
                //'lipos.freifeld5',
                //'lipos.freifeld6',
                //'lipos.freifeld7',
                //'lipos.freifeld8',
                //'lipos.freifeld9',
                //'lipos.freifeld10',
                //'lipos.freifeld11',
                //'lipos.freifeld12',
                //'lipos.freifeld13',
                //'lipos.freifeld14',
                //'lipos.freifeld15',
                //'lipos.freifeld16',
                //'lipos.freifeld17',
                //'lipos.freifeld18',
                //'lipos.freifeld19',
                //'lipos.freifeld20',
                //'lipos.freifeld21',
                //'lipos.freifeld22',
                //'lipos.freifeld23',
                //'lipos.freifeld24',
                //'lipos.freifeld25',
                //'lipos.freifeld26',
                //'lipos.freifeld27',
                //'lipos.freifeld28',
                //'lipos.freifeld29',
                //'lipos.freifeld30',
                //'lipos.freifeld31',
                //'lipos.freifeld32',
                //'lipos.freifeld33',
                //'lipos.freifeld34',
                //'lipos.freifeld35',
                //'lipos.freifeld36',
                //'lipos.freifeld37',
                //'lipos.freifeld38',
                //'lipos.freifeld39',
                //'lipos.freifeld40',
                'lipos.seriennummer',
                'lipos.herkunftsland',
                'lipos.zolltarifnummer',
                'lipos.zolleinzelwert',
                'lipos.zollgesamtwert',
                'lipos.zollwaehrung',
                'lipos.zolleinzelgewicht',
                'lipos.zollgesamtgewicht',
                'lipos.nve',
                'lipos.packstueck',
                'lipos.vpemenge',
                'lipos.einzelstueckmenge',
                //'lipos.explodiert_parent',
                //'lipos.explodiert_parent_artikel',
            ])
            ->from(self::TABLE_NAME . ' AS lipos');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('lipos.id IN (:ids)');
    }

    /**
     * @return false
     */
    protected function insertQuery()
    {
        return false;
    }

    /**
     * @return false
     */
    protected function updateQuery()
    {
        return false;
    }

    /**
     * @return false
     */
    protected function deleteQuery()
    {
        return false;
    }
}
