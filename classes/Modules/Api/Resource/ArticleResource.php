<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

class ArticleResource extends AbstractResource
{
    const TABLE_NAME = 'artikel';

    /** @var \Api $legacyApi */
    private $legacyApi;

    /**
     * @param \Api $api
     *
     * @return void
     */
    public function setLegacyApi($api)
    {
        $this->legacyApi = $api;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'typ' => 'a.typ LIKE',
            'name_de' => 'a.name_de %LIKE%',
            'name_de_exakt' => 'a.name_de LIKE',
            'name_de_startswith' => 'a.name_de LIKE%',
            'name_de_endswith' => 'a.name_de %LIKE',
            'name_de_equals' => 'a.name_de LIKE',
            'name_en' => 'a.name_en %LIKE%',
            'name_en_exakt' => 'a.name_en LIKE',
            'name_en_startswith' => 'a.name_en LIKE%',
            'name_en_endswith' => 'a.name_en %LIKE',
            'name_en_equals' => 'a.name_en LIKE',
            'nummer' => 'a.nummer %LIKE%',
            'nummer_exakt' => 'a.nummer LIKE',
            'nummer_startswith' => 'a.nummer LIKE%',
            'nummer_endswith' => 'a.nummer %LIKE',
            'nummer_equals' => 'a.nummer LIKE',
            'projekt' => 'a.projekt =',
            'adresse' => 'a.adresse =',
            'katalog' => 'a.katalog =',
            'firma' => 'a.firma =',
            'ausverkauft' => 'a.ausverkauft =',
            'startseite' => 'a.startseite =',
            'topseller' => 'a.topseller =',
        ]);

        $this->registerSortingParams([
            'name_de' => 'a.name_de',
            'name_en' => 'a.name_en',
            'nummer' => 'a.nummer',
            'typ' => 'a.typ',
        ]);

        $this->registerValidationRules([
            'id' => 'not_present',
            'a.shop' => 'not_present',
            'a.shop2' => 'not_present',
            'a.shop3' => 'not_present',
            'a.usereditid' => 'not_present',
            'a.useredittimestamp' => 'not_present',
            'a.intern_gesperrtuser' => 'not_present',
            'a.inbearbeitunguser' => 'not_present',
            'nummer' => 'required|unique:artikel,nummer',
            'projekt' => 'numeric',
            'adresse' => 'numeric',
            'katalog' => 'numeric',
            'firma' => 'numeric',
            'ausverkauft' => 'in:0,1',
            'geloescht' => 'in:0,1',

            // Keine Default-Values
            /*'checksum' => 'present',
            'kurztext_de' => 'present',
            'kurztext_en' => 'present',
            'beschreibung_de' => 'present',
            'beschreibung_en' => 'present',
            'uebersicht_de' => 'present',
            'uebersicht_en' => 'present',
            'links_de' => 'present',
            'links_en' => 'present',
            'startseite_de' => 'present',
            'startseite_en' => 'present',*/
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
            'verkaufspreise' => [
                'key' => 'verkaufspreise',
                'filter' => [
                    ['property' => 'artikel', 'value' => ':id'],
                ],
                'sort' => ['menge' => 'ASC'],
                'resource' => SalesPriceResource::class,
            ],
            'dateien' => [
                'key' => 'dateien',
                'filter' => [
                    ['property' => 'artikel', 'value' => ':id'],
                ],
                'resource' => ArticleFileResource::class,
            ],
            'lagerbestand' => [
                /**
                 * Sonderfall
                 *
                 * @see ArticleResource::integrateIncludes
                 */
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function integrateIncludes(array $includes, array &$items, $isCollection = true)
    {
        // Ausnahme für "lagerbestand"-Include
        $lagerbestandIncludeKey = array_search('lagerbestand', $includes, true);
        if ($lagerbestandIncludeKey !== false) {

            // Mehrere Artikel
            if ($isCollection) {
                foreach ($items as &$item) {
                    $articleId = $item['id'];
                    $istLagerartikel = (int)$item['lagerartikel'] === 1;
                    $item['lagerbestand'] =
                        $istLagerartikel
                            ? $this->legacyApi->app->erp->ArtikelAnzahlVerkaufbar($articleId, 0, 0, 0, 0, true)
                            : [];
                }
                unset($item);
            }

            // Einzelner Artikel
            if (!$isCollection) {
                $articleId = $items['id'];
                $istLagerartikel = (int)$items['lagerartikel'] === 1;
                $items['lagerbestand'] =
                    $istLagerartikel
                        ? $this->legacyApi->app->erp->ArtikelAnzahlVerkaufbar($articleId, 0, 0, 0, 0, true)
                        : [];
            }

            unset($includes[$lagerbestandIncludeKey]);
        }

        // Andere Includes normal ausführen
        return parent::integrateIncludes($includes, $items, $isCollection);
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db
            ->select()
            ->cols([
                //'a.*',
                'a.id',
                'a.typ',
                'a.nummer',
                'a.checksum',
                'a.projekt',
                'a.inaktiv',
                'a.ausverkauft',
                'a.warengruppe',
                'a.name_de',
                'a.name_en',
                'a.kurztext_de',
                'a.kurztext_en',
                'a.beschreibung_de',
                'a.beschreibung_en',
                'a.uebersicht_de',
                'a.uebersicht_en',
                'a.links_de',
                'a.links_en',
                'a.startseite_de',
                'a.startseite_en',
                'a.standardbild',
                'a.herstellerlink',
                'a.hersteller',
                'a.teilbar',
                'a.nteile',
                'a.seriennummern',
                'a.lager_platz',
                'a.lieferzeit',
                'a.lieferzeitmanuell',
                'a.sonstiges',
                'a.gewicht',
                'a.endmontage',
                'a.funktionstest',
                'a.artikelcheckliste',
                'a.stueckliste',
                'a.juststueckliste',
                'a.barcode',
                'a.hinzugefuegt',
                'a.pcbdecal',
                'a.lagerartikel',
                'a.porto',
                'a.chargenverwaltung',
                'a.provisionsartikel',
                'a.gesperrt',
                'a.sperrgrund',
                'a.geloescht',
                'a.gueltigbis',
                'a.umsatzsteuer',
                'a.klasse',
                'a.adresse',
                'a.shopartikel',
                'a.unishopartikel',
                'a.journalshopartikel',
                'a.katalog',
                'a.katalogtext_de',
                'a.katalogtext_en',
                'a.katalogbezeichnung_de',
                'a.katalogbezeichnung_en',
                'a.neu',
                'a.topseller',
                'a.startseite',
                'a.wichtig',
                'a.mindestlager',
                'a.mindestbestellung',
                'a.partnerprogramm_sperre',
                'a.internerkommentar',
                'a.intern_gesperrt',
                //'a.intern_gesperrtuser',
                'a.intern_gesperrtgrund',
                'a.inbearbeitung',
                //'a.inbearbeitunguser',
                'a.cache_lagerplatzinhaltmenge',
                'a.internkommentar',
                'a.firma',
                'a.logdatei',
                'a.anabregs_text',
                'a.autobestellung',
                'a.produktion',
                'a.herstellernummer',
                'a.restmenge',
                'a.mlmdirektpraemie',
                'a.keineeinzelartikelanzeigen',
                'a.mindesthaltbarkeitsdatum',
                'a.letzteseriennummer',
                'a.individualartikel',
                'a.keinrabatterlaubt',
                'a.rabatt',
                'a.rabatt_prozent',
                'a.geraet',
                'a.serviceartikel',
                'a.autoabgleicherlaubt',
                'a.pseudopreis',
                'a.freigabenotwendig',
                'a.freigaberegel',
                'a.nachbestellt',
                'a.ean',
                'a.mlmpunkte',
                'a.mlmbonuspunkte',
                'a.mlmkeinepunkteeigenkauf',
                //'a.shop', // Altlasten; wird zukünftig über artikel_shop gemacht
                //'a.shop2',
                //'a.shop3',
                //'a.usereditid',
                //'a.useredittimestamp',
                'a.einheit',
                'a.webid',
                'a.lieferzeitmanuell_en',
                'a.variante',
                'a.variante_von',
                'a.produktioninfo',
                'a.sonderaktion',
                'a.sonderaktion_en',
                'a.autolagerlampe',
                'a.leerfeld',
                'a.zolltarifnummer',
                'a.herkunftsland',
                'a.laenge',
                'a.breite',
                'a.hoehe',
                'a.gebuehr',
                'a.pseudolager',
                'a.downloadartikel',
                'a.matrixprodukt',
                'a.steuer_erloese_inland_normal',
                'a.steuer_aufwendung_inland_normal',
                'a.steuer_erloese_inland_ermaessigt',
                'a.steuer_aufwendung_inland_ermaessigt',
                'a.steuer_erloese_inland_steuerfrei',
                'a.steuer_aufwendung_inland_steuerfrei',
                'a.steuer_erloese_inland_innergemeinschaftlich',
                'a.steuer_aufwendung_inland_innergemeinschaftlich',
                'a.steuer_erloese_inland_eunormal',
                'a.steuer_erloese_inland_nichtsteuerbar',
                'a.steuer_erloese_inland_euermaessigt',
                'a.steuer_aufwendung_inland_nichtsteuerbar',
                'a.steuer_aufwendung_inland_eunormal',
                'a.steuer_aufwendung_inland_euermaessigt',
                'a.steuer_erloese_inland_export',
                'a.steuer_aufwendung_inland_import',
                'a.steuer_art_produkt',
                'a.steuer_art_produkt_download',
                'a.metadescription_de',
                'a.metadescription_en',
                'a.metakeywords_de',
                'a.metakeywords_en',
                'a.anabregs_text_en',
                'a.externeproduktion',
                'a.bildvorschau',
                'a.inventursperre',
                'a.variante_kopie',
                'a.unikat',
                'a.generierenummerbeioption',
                'a.allelieferanten',
                'a.tagespreise',
                'a.rohstoffe',
                'a.ohnepreisimpdf',
                'a.provisionssperre',
                'a.dienstleistung',
                'a.inventurekaktiv',
                'a.inventurek',
                'a.hinweis_einfuegen',
                'a.etikettautodruck',
                'a.lagerkorrekturwert',
                'a.autodrucketikett',
                'a.steuertext_innergemeinschaftlich',
                'a.steuertext_export',
                'a.formelmenge',
                'a.formelpreis',
                'a.ursprungsregion',
                'a.bestandalternativartikel',
                'a.metatitle_de',
                'a.metatitle_en',
                'a.vkmeldungunterdruecken',
                'a.altersfreigabe',
                'a.unikatbeikopie',
                'a.steuergruppe',
                'a.keinskonto',
                'a.berechneterek',
                'a.verwendeberechneterek',
                'a.berechneterekwaehrung',
                'a.artikelautokalkulation',
                'a.artikelabschliessenkalkulation',
                'a.artikelfifokalkulation',
                'a.freifeld1',
                'a.freifeld2',
                'a.freifeld3',
                'a.freifeld4',
                'a.freifeld5',
                'a.freifeld6',
                'a.freifeld7',
                'a.freifeld8',
                'a.freifeld9',
                'a.freifeld10',
                'a.freifeld11',
                'a.freifeld12',
                'a.freifeld13',
                'a.freifeld14',
                'a.freifeld15',
                'a.freifeld16',
                'a.freifeld17',
                'a.freifeld18',
                'a.freifeld19',
                'a.freifeld20',
                'a.freifeld21',
                'a.freifeld22',
                'a.freifeld23',
                'a.freifeld24',
                'a.freifeld25',
                'a.freifeld26',
                'a.freifeld27',
                'a.freifeld28',
                'a.freifeld29',
                'a.freifeld30',
                'a.freifeld31',
                'a.freifeld32',
                'a.freifeld33',
                'a.freifeld34',
                'a.freifeld35',
                'a.freifeld36',
                'a.freifeld37',
                'a.freifeld38',
                'a.freifeld39',
                'a.freifeld40',
            ])
            ->from(self::TABLE_NAME . ' AS a')
            ->where('a.geloescht <> 1');
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('a.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('a.id IN (:ids)');
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
