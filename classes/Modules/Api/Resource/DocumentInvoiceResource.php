<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\DeleteQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;
use Xentral\Modules\Api\Resource\Result\ItemResult;

/**
 * Ressource für Rechnungen
 */
class DocumentInvoiceResource extends AbstractResource
{
    /** @var string */
    const TABLE_NAME = 'rechnung';
    /** @var string */
    const POSITION_TABLE_NAME = 'rechnung_position';
    /** @var string */
    const PROTOCOL_TABLE_NAME = 'rechnung_protokoll';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'status'                  => 're.status LIKE',
            'belegnr'                 => 're.belegnr %LIKE%',
            'belegnr_equals'          => 're.belegnr LIKE',
            'belegnr_startswith'      => 're.belegnr LIKE%',
            'belegnr_endswith'        => 're.belegnr %LIKE',
            'kundennummer'            => 're.kundennummer %LIKE%',
            'kundennummer_equals'     => 're.kundennummer LIKE',
            'kundennummer_startswith' => 're.kundennummer LIKE%',
            'kundennummer_endswith'   => 're.kundennummer %LIKE',
            'datum'                   => 're.datum =',
            'datum_gt'                => 're.datum >',
            'datum_gte'               => 're.datum >=',
            'datum_lt'                => 're.datum <',
            'datum_lte'               => 're.datum <=',
            'auftrag'                 => 're.auftrag LIKE',
            'auftragid'               => 're.auftragid =',
            'projekt'                 => 're.projekt =',
        ]);

        $this->registerSortingParams([
            'belegnr' => 're.belegnr',
            'datum'   => 're.datum',
        ]);

        $this->registerIncludes([
            'positionen' => [
                'key'      => 'positionen',
                'resource' => DocumentInvoicePositionResource::class,
                'filter'   => [
                    [
                        'property' => 'rechnung',
                        'value'    => ':id',
                    ],
                ],
                'sort'     => [
                    'sort' => 'ASC',
                ],
            ],
            'protokoll'  => [
                'key'      => 'protokoll',
                'resource' => DocumentInvoiceProtocolResource::class,
                'columns'  => [
                    'reproto.id',
                    'reproto.zeit',
                    'reproto.bearbeiter',
                    'reproto.grund',
                ],
                'filter'   => [
                    [
                        'property' => 'rechnung',
                        'value'    => ':id',
                    ],
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
        return $this->selectAllQuery()->where('re.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                're.id',
                're.firma',
                're.projekt', // Index
                're.status', // Index
                're.belegnr', // Index
                're.anlegeart',
                're.datum', // Index
                're.auftrag',
                're.auftragid', // Index
                're.aborechnung',
                're.kundennummer',
                're.bearbeiterid',
                're.bearbeiter',
                're.freitext',
                're.aktion',
                //'re.internebemerkung',

                're.adresse', // Index
                're.typ',
                're.name',
                're.titel',
                're.ansprechpartnerid',
                're.ansprechpartner',
                're.abteilung',
                're.unterabteilung',
                're.adresszusatz',
                're.strasse',
                're.plz',
                're.ort',
                're.land',
                're.bundesstaat',
                're.telefon',
                're.telefax',
                're.email',
                're.anschreiben',

                //'re.betreff',
                //'re.lieferschein',
                're.versandart',
                're.lieferdatum',
                're.buchhaltung',
                're.zahlungsweise',
                're.mahnwesenfestsetzen',
                're.zahlungsstatus', // Index
                're.ist',
                're.soll', // Index
                're.skonto_gegeben',
                're.zahlungszieltage',
                're.zahlungszieltageskonto',
                're.zahlungszielskonto',
                're.versendet',
                're.versendet_am',
                're.versendet_per',
                're.versendet_durch',
                're.versendet_mahnwesen',
                're.mahnwesen',
                're.mahnwesen_datum',
                're.mahnwesen_gesperrt',
                're.mahnwesen_internebemerkung',
                're.datev_abgeschlossen',
                //'re.inbearbeitung',
                //'re.logdatei',
                //'re.doppel',
                //'re.autodruck_rz',
                //'re.autodruck_periode',
                //'re.autodruck_done',
                //'re.autodruck_anzahlverband',
                //'re.autodruck_anzahlkunde',
                //'re.autodruck_mailverband',
                //'re.autodruck_mailkunde',
                //'re.dta_datei_verband',
                //'re.dta_datei',
                //'re.deckungsbeitragcalc',
                //'re.deckungsbeitrag',
                're.umsatz_netto',
                're.erloes_netto',
                //'re.vertriebid', // Index
                //'re.vertrieb',
                're.provision',
                're.provision_summe',
                //'re.gruppe', // Index
                //'re.punkte',
                //'re.bonuspunkte',
                're.provdatum', // Index
                're.ihrebestellnummer',
                //'re.usereditid', // Index
                //'re.useredittimestamp',
                //'re.realrabatt',
                're.rabatt',
                're.einzugsdatum',
                //'re.rabatt1',
                //'re.rabatt2',
                //'re.rabatt3',
                //'re.rabatt4',
                //'re.rabatt5',
                're.forderungsverlust_datum',
                're.forderungsverlust_betrag',
                're.steuersatz_normal',
                're.steuersatz_zwischen',
                're.steuersatz_ermaessigt',
                're.steuersatz_starkermaessigt',
                're.steuersatz_dienstleistung',
                're.ustid',
                're.ust_befreit',
                're.ustbrief',
                're.ustbrief_eingang',
                're.ustbrief_eingang_am',
                're.waehrung',
                're.keinsteuersatz',
                //'re.schreibschutz',
                //'re.pdfarchiviert',
                //'re.pdfarchiviertversion',
                //'re.ohne_briefpapier',
                //'re.lieferid',
                //'re.systemfreitext',
                //'re.projektfiliale',
                //'re.zuarchivieren',
                're.internebezeichnung',
                //'re.angelegtam',
                're.abweichendebezeichnung',
                're.bezahlt_am',
                're.sprache',
                //'re.bundesland',
                're.gln',
                //'re.deliverythresholdvatid',
                're.kurs',
                're.ohne_artikeltext',
                're.anzeigesteuer',
                're.kostenstelle',
                're.bodyzusatz',
                're.lieferbedingung',
                're.skontobetrag',
                're.skontoberechnet',
                're.extsoll',
                're.teilstorno',
            ])->from(self::TABLE_NAME . ' AS re');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('re.id IN (:ids)');
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
     * @return false|DeleteQuery
     */
    protected function deleteQuery()
    {
        return $this->db->delete()->from(self::TABLE_NAME)->where("id = :id AND (belegnr = '' OR belegnr = '0')");
    }

    /**
     * @return false|DeleteQuery
     */
    protected function deleteProtocolQuery()
    {
        return $this->db->delete()->from(self::PROTOCOL_TABLE_NAME)->where('rechnung = :id');
    }

    /**
     * @return false|DeleteQuery
     */
    protected function deletePositionQuery()
    {
        return $this->db->delete()->from(self::POSITION_TABLE_NAME)->where('rechnung = :id');
    }

    /**
     * @param int $id
     *
     * @return ItemResult
     */
    public function delete($id)
    {
        $deleteQuery = $this->deleteQuery();
        if (!$deleteQuery) {
            throw new EndpointNotAvailableException();
        }
        if (!$deleteQuery instanceof DeleteQuery && !$deleteQuery instanceof UpdateQuery) {
            throw new InvalidArgumentException(sprintf(
                'deleteQuery() must return an instance of %s or %s', DeleteQuery::class, UpdateQuery::class
            ));
        }

        try {
            $affectedRow = (int)$this->db->fetchAffected($deleteQuery->getStatement(), ['id' => $id]);
            if($affectedRow <= 0) {
                throw new InvalidArgumentException(sprintf('Invoice can not deleted'));
            }
            $deleteQuery = $this->deletePositionQuery();
            $this->db->perform($deleteQuery->getStatement(), ['id' => $id]);
            $deleteQuery = $this->deleteProtocolQuery();
            $this->db->perform($deleteQuery->getStatement(), ['id' => $id]);
            $success = true;
        } catch (Exception $e) {
            $success = false;
        }

        $result = new ItemResult(['id' => $id]);
        $result->setSuccess($success);

        return $result;
    }
}
