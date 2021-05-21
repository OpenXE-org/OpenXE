<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressource für Lieferscheine
 */
class DocumentDeliveryNoteResource extends AbstractResource
{
    /** @var string */
    const TABLE_NAME = 'lieferschein';

    /**
     * @param array $filter
     * @param array $sorting
     * @param array $columns
     * @param array $includes
     * @param int   $page
     * @param int   $paging
     *
     * @return Result\CollectionResult
     */
    public function getList(
        array $filter = [],
        array $sorting = [],
        array $columns = [],
        array $includes = [],
        $page = 1,
        $paging = 20
    ) {
        // Filter für Auftragsnummer über Auftrags-ID verknüpfen
        if (isset($filter['auftrag']) && !isset($filter['auftragid'])) {
            $select = $this->db->select()
                ->cols(['a.id'])
                ->from('auftrag AS a')
                ->where('a.belegnr = ?', $filter['auftrag']);
            if (isset($filter['projekt'])) {
                $select->where('a.projekt = ?', $filter['projekt']);
            }
            $orderId = $this->db->fetchValue($select->getStatement(), $select->getBindValues());
            if ($orderId > 0) {
                $filter['auftragid'] = $orderId;
                unset($filter['auftrag']);
            }
        }

        return parent::getList($filter, $sorting, $columns, $includes, $page, $paging);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'status'                  => 'li.status LIKE',
            'belegnr'                 => 'li.belegnr %LIKE%',
            'belegnr_equals'          => 'li.belegnr LIKE',
            'belegnr_startswith'      => 'li.belegnr LIKE%',
            'belegnr_endswith'        => 'li.belegnr %LIKE',
            'kundennummer'            => 'li.kundennummer %LIKE%',
            'kundennummer_equals'     => 'li.kundennummer LIKE',
            'kundennummer_startswith' => 'li.kundennummer LIKE%',
            'kundennummer_endswith'   => 'li.kundennummer %LIKE',
            'datum'                   => 'li.datum =',
            'datum_gt'                => 'li.datum >',
            'datum_gte'               => 'li.datum >=',
            'datum_lt'                => 'li.datum <',
            'datum_lte'               => 'li.datum <=',
            'auftrag'                 => 'li.auftrag LIKE',
            'auftragid'               => 'li.auftragid =',
            'projekt'                 => 'li.projekt =',
        ]);

        $this->registerSortingParams([
            'belegnr' => 'li.belegnr',
            'datum'   => 'li.datum',
        ]);

        $this->registerIncludes([
            'positionen' => [
                'key'      => 'positionen',
                'resource' => DocumentDeliveryNotePositionResource::class,
                'filter'   => [
                    [
                        'property' => 'lieferschein',
                        'value'    => ':id',
                    ],
                ],
                'sort'     => [
                    'sort' => 'ASC',
                ],
            ],
            'protokoll'  => [
                'key'      => 'protokoll',
                'resource' => DocumentDeliveryNoteProtocolResource::class,
                'filter'   => [
                    [
                        'property' => 'lieferschein',
                        'value'    => ':id',
                    ],
                ],
                'columns'  => [
                    'liproto.id',
                    'liproto.zeit',
                    'liproto.bearbeiter',
                    'liproto.grund',
                ],
                'sort'     => [
                    'zeit' => 'ASC',
                ],
            ],
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('li.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                'li.id',#
                'li.firma',#
                'li.projekt',# // Index
                'li.status',# // Index
                'li.lieferscheinart',#
                'li.belegnr',# // Index
                'li.kundennummer',#
                'li.ihrebestellnummer',#
                'li.datum',# // Index
                'li.auftrag',#
                'li.auftragid',# // Index
                'li.freitext',#

                'li.adresse',# // Index
                'li.typ',#
                'li.name',#
                'li.titel',#
                'li.ansprechpartnerid',#
                'li.ansprechpartner',#
                'li.abteilung',#
                'li.unterabteilung',#
                'li.adresszusatz',#
                'li.strasse',#
                'li.plz',#
                'li.ort',#
                'li.land',# // Index
                'li.bundesstaat',#
                'li.telefon',#
                'li.telefax',#
                'li.email',#
                'li.anschreiben',#

                //'li.betreff',#
                //'li.vertriebid',# // Index
                //'li.vertrieb',#
                'li.versandart',#
                'li.versand',#
                'li.versendet',#
                'li.versendet_am',#
                'li.versendet_per',#
                'li.versendet_durch',#
                //'li.inbearbeitung_user',#
                //'li.logdatei',#
                //'li.schreibschutz',#
                'li.ustid',#
                'li.ust_befreit',#
                'li.usereditid',# // Index
                'li.useredittimestamp',#
                'li.lieferantenretoure',#
                'li.lieferantenretoureinfo',#
                'li.lieferant',#
                'li.pdfarchiviert',#
                'li.pdfarchiviertversion',#
                'li.internebemerkung',#
                'li.ohne_briefpapier',#
                'li.lieferid',#
                'li.projektfiliale',#
                'li.projektfiliale_eingelagert',#
                'li.zuarchivieren',#
                'li.internebezeichnung',#
                'li.kommissionierung',#
                'li.sprache',#
                //'li.angelegtam',#
                //'li.bundesland',#
                'li.gln',#
                //'li.rechnungid',#
                //'li.bearbeiterid',#
                'li.bearbeiter',#
                'li.keinerechnung',# // Index
                'li.ohne_artikeltext',#
                'li.abweichendebezeichnung',#
                'li.kostenstelle',#
                'li.bodyzusatz',#
                'li.lieferbedingung',#
                'li.standardlager',#
                'li.kommissionskonsignationslager',#
                'li.teillieferungvon',#
                'li.teillieferungnummer',#
                'li.kiste',#
            ])->from(self::TABLE_NAME . ' AS li');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('li.id IN (:ids)');
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
