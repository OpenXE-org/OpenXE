<?php

namespace Xentral\Modules\Api\Resource;

use Aura\SqlQuery\Exception;
use Xentral\Components\Database\SqlQuery\DeleteQuery;
use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;
use Xentral\Modules\Api\Exception\InvalidArgumentException;

class DeliveryAddressResource extends AbstractResource
{
    const TABLE_NAME = 'lieferadressen';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'adresse' => 'l.adresse =',
            'typ' => 'l.typ =',
            'name' => 'l.name %LIKE%',
            'name_equals' => 'l.name LIKE',
            'name_startswith' => 'l.name LIKE%',
            'name_endswith' => 'l.name %LIKE',
            'standardlieferadresse' => 'l.standardlieferadresse =',
            'land' => 'l.land =',
            'id_ext' => 'am.id_ext =',
        ]);

        $this->registerSortingParams([
            'typ' => 'l.typ',
            'name' => 'l.name',
            'plz' => 'l.plz',
            'land' => 'l.land',
        ]);

        $this->registerValidationRules([
            'id' => 'not_present',
            'id_ext' => 'not_present',
            'name' => 'required',
            'adresse' => 'numeric|db_value:adresse,id',
            'typ' => 'db_value:adresse_typ,type',
            'land' => 'upper|length:2|db_value:laender,iso',
            'ust_befreit' => 'in:0,1,2,3',
            'standardlieferadresse' => 'in:0,1',
        ]);
    }

    /**
     * @return SelectQuery
     *
     * @throws Exception
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                'l.id',
                'l.typ',
                //'l.sprache', // Nicht änderbar über Formular
                'l.name',
                'l.abteilung',
                'l.unterabteilung',
                'l.strasse',
                'l.ort',
                'l.plz',
                'l.land',
                'l.telefon',
                'l.telefax',
                'l.email',
                //'l.sonstiges', // Nicht änderbar über Formular
                'l.adresszusatz',
                //'l.steuer', // Nicht änderbar über Formular
                'l.adresse',
                //'l.ansprechpartner', // Nicht änderbar über Formular
                'l.standardlieferadresse',
                'l.gln',
                'l.ustid',
                'l.lieferbedingung',
                'l.ust_befreit',
                'l.interne_bemerkung',
                'am.id_ext',
            ])
            ->from(self::TABLE_NAME . ' AS l')
            ->leftJoin(
                'api_mapping AS am',
                'am.id_int = l.id AND am.tabelle = ' . $this->db->escapeString(self::TABLE_NAME)
            );
    }

    /**
     * @return SelectQuery
     *
     * @throws Exception
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('l.id = :id');
    }

    /**
     * @return SelectQuery
     *
     * @throws Exception
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('l.id IN (:ids)');
    }

    /**
     * @return InsertQuery
     */
    protected function insertQuery()
    {
        return $this->db->insert()->into(self::TABLE_NAME);
    }

    /**
     * @inheritdoc
     */
    public function insert($inputVars)
    {
        // SQL-Fehler umgehen: Field 'sprache' doesn't have a default value
        if (!isset($inputVars['abteilung'])) { $inputVars['abteilung'] = ''; }
        if (!isset($inputVars['unterabteilung'])) { $inputVars['unterabteilung'] = ''; }
        if (!isset($inputVars['strasse'])) { $inputVars['strasse'] = ''; }
        if (!isset($inputVars['ort'])) { $inputVars['ort'] = ''; }
        if (!isset($inputVars['plz'])) { $inputVars['plz'] = ''; }
        if (!isset($inputVars['telefon'])) { $inputVars['telefon'] = ''; }
        if (!isset($inputVars['telefax'])) { $inputVars['telefax'] = ''; }
        if (!isset($inputVars['email'])) { $inputVars['email'] = ''; }
        if (!isset($inputVars['steuer'])) { $inputVars['steuer'] = ''; }
        if (!isset($inputVars['sprache'])) { $inputVars['sprache'] = ''; }
        if (!isset($inputVars['sonstiges'])) { $inputVars['sonstiges'] = ''; }
        if (!isset($inputVars['adresszusatz'])) { $inputVars['adresszusatz'] = ''; }
        if (!isset($inputVars['lieferbedingung'])) { $inputVars['lieferbedingung'] = ''; }

        // Angelegte Daten aus dem Result holen
        $result = parent::insert($inputVars);
        $data = $result->getData();

        // Es darf nur eine Standard-Lieferadresse pro Hauptadresse geben!
        if ((int)$data['standardlieferadresse'] === 1) {
            $addressId = (int)$data['adresse'];
            $deliveryAddressId = (int)$data['id'];

            if ($addressId === 0) {
                throw new InvalidArgumentException('AdressID can not be empty');
            }
            if ($deliveryAddressId === 0) {
                throw new InvalidArgumentException('ID can not be empty');
            }

            // Vorhandene Standard-Lieferadresse zur "nicht-Standard"-Lieferadresse machen
            $updateQuery = $this->db->update()
                ->table(self::TABLE_NAME)
                ->cols(['standardlieferadresse' => 0])
                ->where('standardlieferadresse = :eins')
                ->where('adresse = :adresse')
                ->where('id != :id')
                ->bindValues([
                    'eins' => 1,
                    'adresse' => $addressId,
                    'id' => $deliveryAddressId,
                ]);

            $this->db->perform(
                $updateQuery->getStatement(),
                $updateQuery->getBindValues()
            );
        }

        return $result;
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
