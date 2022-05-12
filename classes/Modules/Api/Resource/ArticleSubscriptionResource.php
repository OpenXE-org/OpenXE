<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\DeleteQuery;
use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

class ArticleSubscriptionResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'abrechnungsartikel';

    /** @var array $inputMapping */
    protected $inputMapping = [
        'reihenfolge'           => 'sort',
        'beschreibung_ersetzen' => 'beschreibungersetzten',
        'abgerechnet_bis'       => 'abgerechnetbis',
        'dokumenttyp'           => 'dokument',
    ];

    /**
     * @param array      $inputValues
     * @param array|null $inputMapping
     *
     * @return Result\ItemResult
     */
    public function insert($inputValues, $inputMapping = null)
    {
        return parent::insert($inputValues, $this->inputMapping);
    }

    /**
     * @param int        $id
     * @param array      $inputVars
     * @param array|null $inputMapping
     *
     * @return Result\ItemResult
     */
    public function edit($id, $inputVars, $inputMapping = null)
    {
        return parent::edit($id, $inputVars, $this->inputMapping);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'waehrung'               => 'aa.waehrung =',
            'preisart'               => 'aa.preisart =',
            'dokumenttyp'            => 'aa.dokument =',
            'gruppe'                 => 'aa.gruppe =',
            'artikel'                => 'aa.artikel =',
            'adresse'                => 'aa.adresse =',
            'kundennummer'           => 'ad.kundennummer =',
            'projekt'                => 'aa.projekt =',
            'bezeichnung'            => 'aa.beschreibung %LIKE%',
            'bezeichnung_equals'     => 'aa.beschreibung LIKE',
            'bezeichnung_startswith' => 'aa.beschreibung LIKE%',
            'bezeichnung_endswith'   => 'aa.beschreibung %LIKE',
            'rabatt'                 => 'aa.rabatt =',
            'rabatt_gt'              => 'aa.rabatt >',
            'rabatt_gte'             => 'aa.rabatt >=',
            'rabatt_lt'              => 'aa.rabatt <',
            'rabatt_lte'             => 'aa.rabatt <=',
            'preis'                  => 'aa.preis =',
            'preis_gt'               => 'aa.preis >',
            'preis_gte'              => 'aa.preis >=',
            'preis_lt'               => 'aa.preis <',
            'preis_lte'              => 'aa.preis <=',
            'menge'                  => 'aa.menge =',
            'menge_gt'               => 'aa.menge >',
            'menge_gte'              => 'aa.menge >=',
            'menge_lt'               => 'aa.menge <',
            'menge_lte'              => 'aa.menge <=',
            'startdatum'             => 'aa.startdatum LIKE',
            'startdatum_gt'          => 'aa.startdatum >',
            'startdatum_gte'         => 'aa.startdatum >=',
            'startdatum_lt'          => 'aa.startdatum <',
            'startdatum_lte'         => 'aa.startdatum <=',
            'enddatum'               => 'aa.enddatum LIKE',
            'enddatum_gt'            => 'aa.enddatum >',
            'enddatum_gte'           => 'aa.enddatum >=',
            'enddatum_lt'            => 'aa.enddatum <',
            'enddatum_lte'           => 'aa.enddatum <=',
            'abgerechnet_bis'        => 'aa.abgerechnetbis LIKE',
            'abgerechnet_bis_gt'     => 'aa.abgerechnetbis >',
            'abgerechnet_bis_gte'    => 'aa.abgerechnetbis >=',
            'abgerechnet_bis_lt'     => 'aa.abgerechnetbis <',
            'abgerechnet_bis_lte'    => 'aa.abgerechnetbis <=',
        ]);

        $this->registerSortingParams([
            'bezeichnung'     => 'aa.bezeichnung',
            'reihenfolge'     => 'aa.sort',
            'rabatt'          => 'aa.rabatt',
            'preis'           => 'aa.preis',
            'menge'           => 'aa.menge',
            'startdatum'      => 'aa.startdatum',
            'enddatum'        => 'aa.enddatum',
            'abgerechnet_bis' => 'aa.abgerechnetbis',
        ]);

        $this->registerValidationRules([
            'id'                    => 'not_present',
            'abgerechnet_bis'       => 'not_present',
            'beschreibung_ersetzen' => 'in:1,0',
            'startdatum'            => 'date:Y-m-d',
            'enddatum'              => 'date:Y-m-d',
            'zahlzyklus'            => 'numeric',
            'preis'                 => 'decimal',
            'rabatt'                => 'decimal',
            'menge'                 => 'decimal',
            'waehrung'              => 'upper|length:3',
            'preisart'              => 'in:monat,monatx,jahr,wochen,einmalig,30tage,360tage',
            'dokumenttyp'           => 'in:rechnung,auftrag',
            'projekt'               => 'numeric',
            'artikel'               => 'numeric|db_value:artikel,id',
            'adresse'               => 'numeric',
            'gruppe'                => 'numeric',
            'reihenfolge'           => 'numeric',
        ]);

        $this->registerIncludes([
            'artikel' => [
                'key'      => 'artikel',
                'resource' => ArticleResource::class,
                'columns'  => [
                    'a.id',
                    'a.nummer',
                    'a.name_de',
                    'a.name_en',
                ],
            ],
            'gruppe'  => [
                'key'      => 'gruppe',
                'resource' => ArticleSubscriptionGroupResource::class,
                'columns'  => [
                    'g.id',
                    'g.beschreibung AS bezeichnung',
                    'g.beschreibung2 AS beschreibung',
                    'g.rabatt',
                    'g.gruppensumme',
                    'g.sort AS reihenfolge',
                ],
            ],
            'adresse' => [
                'key'      => 'adresse',
                'resource' => AddressResource::class,
                'columns'  => [
                    'a.id',
                    'a.typ',
                    'a.name',
                    'a.ansprechpartner',
                    'a.kundennummer',
                ],
            ],
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
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                'aa.id',
                'aa.bezeichnung',
                'aa.beschreibung',
                'aa.beschreibungersetzten AS beschreibung_ersetzen',
                //'aa.nummer',
                'aa.startdatum',
                'aa.enddatum',
                'aa.abgerechnetbis AS abgerechnet_bis',
                'aa.zahlzyklus',
                'aa.preis',
                'aa.rabatt',
                'aa.waehrung',
                'aa.menge',
                'aa.preisart', // monat, monatx, jahr, wochen, einmalig
                'aa.dokument AS dokumenttyp', // rechnung, auftrag
                'aa.artikel',
                'aa.gruppe',
                'aa.adresse',
                'ad.kundennummer',
                'aa.sort AS reihenfolge',
                'aa.projekt',
            ])
            ->from(self::TABLE_NAME . ' AS aa')
            ->leftJoin('adresse AS ad', 'aa.adresse != 0 AND aa.adresse = ad.id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('aa.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('aa.id IN (:ids)');
    }

    /**
     * @return InsertQuery
     */
    protected function insertQuery()
    {
        return $this->db->insert()->into(self::TABLE_NAME);
    }

    /**
     * @return UpdateQuery
     */
    protected function updateQuery()
    {
        return $this->db->update()->table(self::TABLE_NAME)->where('id = :id');
    }

    /**
     * @return DeleteQuery
     */
    protected function deleteQuery()
    {
        return $this->db->delete()->from(self::TABLE_NAME)->where('id = :id');
    }
}
