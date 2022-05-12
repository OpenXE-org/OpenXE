<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

class ArticleSubscriptionGroupResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'abrechnungsartikel_gruppe';

    /** @var array $inputMapping */
    protected $inputMapping = [
        'beschreibung' => 'beschreibung2',
        'bezeichnung'  => 'beschreibung',
        'rabatt'       => 'rabatt',
        'gruppensumme' => 'gruppensumme',
        'projekt'      => 'projekt',
        'reihenfolge'  => 'sort',
    ];

    /**
     * @param array      $inputValues
     * @param array|null $inputMapping
     *
     * @return Result\ItemResult
     */
    public function insert($inputValues, $inputMapping = null)
    {
        $inputValues['extrarechnung'] = 0;

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
            'bezeichnung'            => 'g.beschreibung %LIKE%',
            'bezeichnung_equals'     => 'g.beschreibung LIKE',
            'bezeichnung_startswith' => 'g.beschreibung LIKE%',
            'bezeichnung_endswith'   => 'g.beschreibung %LIKE',
            'gruppensumme'           => 'g.gruppensumme =',
            'rabatt'                 => 'g.rabatt =',
            'rabatt_gt'              => 'g.rabatt >',
            'rabatt_gte'             => 'g.rabatt >=',
            'rabatt_lt'              => 'g.rabatt <',
            'rabatt_lte'             => 'g.rabatt <=',
        ]);

        $this->registerSortingParams([
            'bezeichnung' => 'g.beschreibung',
            'reihenfolge' => 'g.sort',
            'rabatt'      => 'g.rabatt',
        ]);

        $this->registerValidationRules([
            'id'           => 'not_present',
            'bezeichnung'  => 'required',
            'rabatt'       => 'decimal',
            'reihenfolge'  => 'numeric',
            'projekt'      => 'numeric',
            'gruppensumme' => 'boolean',
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
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                'g.id',
                'g.beschreibung AS bezeichnung',
                'g.beschreibung2 AS beschreibung',
                'g.rabatt',
                'g.gruppensumme',
                'g.projekt',
                'g.sort AS reihenfolge',
            ])
            ->from(self::TABLE_NAME . ' AS g')
            ->where('g.extrarechnung = 0'); // 0 = Gemeinsame Rechnung; 1 = Eigene Rechnung; 2 = Sammelrechnung
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('g.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('g.id IN (:ids)');
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
     * @return false
     */
    protected function deleteQuery()
    {
        return false;
    }
}
