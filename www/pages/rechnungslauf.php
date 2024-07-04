<?php
/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleJobService;
use Xentral\Modules\SubscriptionCycle\SubscriptionModule;

class Rechnungslauf {
    /** @var Application $app */
    protected $app;

    /**
     * @param Application $app
     * @param string      $name
     * @param array       $erlaubtevars
     *
     * @return array
     */
    public static function TableSearch($app, $name, $erlaubtevars)
    {
        // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
        switch($name)
        {
            case 'rechnungslauf_invoices':
            case 'rechnungslauf_orders':
                if ($name == 'rechnungslauf_invoices')
                  $doctype = 'rechnung';
                else
                  $doctype = 'auftrag';
                $allowed['rechnungslauf'] = ['rechnungslauf'];

                $heading = array('', '', 'Kunden Nr.', 'Firma/Name', 'Anschreiben', 'E-Mail', 'Projekt', 'Von', 'Bis', 'Betrag netto', 'Men&uuml;');
                $width = ['1%', '1%', '10%', '20%', '10%', '10%', '10%', '10%', '10%', '10%', '1%'];

                $findcols = [
                    'adr.kundennummer',
                    'adr.kundennummer',
                    'adr.kundennummer',
                    'adr.name',
                    'adr.anschreiben',
                    "adr.email",
                    'p.abkuerzung',
                    null,
                    null,
                    null,
                    null
                ];
                $searchsql = ['adr.kundennummer', 'adr.name', 'adr.email'];

                $numbercols = [2, 10];
                $alignright = [3, 10];
                $datecols = [7, 8];

                $defaultorder = 3;
                $defaultorderdesc = 0;
                $sumcol = [10];

                $menu = '<table cellpadding="0" cellspacing="0">';
                $menu .= '<tr>';
                $menu .= '<td nowrap>';
                $menu .= '<a href="index.php?module=adresse&action=artikel&id=%value%">';
                $menu .= '<img src="themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/forward.svg" border="0">';
                $menu .= '</a>';
                $menu .= '</td>';
                $menu .= '</tr>';
                $menu .= '</table>';

              $calcdate = new \DateTimeImmutable('today');
              $scalcdate = $calcdate->format('Y-m-d');
                $where = " aa.id > 0
                    AND aa.dokument = '$doctype' AND aa.preisart IN ('monat', 'jahr', '30tage')
                    AND greatest(aa.startdatum, aa.abgerechnetbis) < '$scalcdate' 
                    AND (aa.enddatum = '0000-00-00' OR aa.abgerechnetbis < aa.enddatum)";

                $sql = "SELECT SQL_CALC_FOUND_ROWS
                    adr.id,
                    '<img src=\"./themes/new/images/details_open.png\" class=\"details\">' as open,
                    concat('<input type=\"checkbox\" name=\"selection[]\" value=\"',adr.id,'\" />') as auswahl,
                    adr.kundennummer,
                    adr.name,
                    adr.anschreiben,
                    adr.email,
                    p.abkuerzung,
                    GROUP_CONCAT(DATE_FORMAT(@start := GREATEST(aa.startdatum, aa.abgerechnetbis),'%d.%m.%Y') SEPARATOR '<br>') as start,
                    GROUP_CONCAT(DATE_FORMAT(
                        @end := CASE
                            WHEN aa.preisart = 'monat' THEN
                                DATE_ADD(@start, INTERVAL TIMESTAMPDIFF(MONTH, @start, IF(aa.enddatum = '0000-00-00' OR aa.enddatum > '$scalcdate', '$scalcdate', aa.enddatum))+1 MONTH)
                            WHEN aa.preisart = 'jahr' THEN
                                DATE_ADD(@start, INTERVAL TIMESTAMPDIFF(YEAR,  @start, IF(aa.enddatum = '0000-00-00' OR aa.enddatum > '$scalcdate', '$scalcdate', aa.enddatum))+1 YEAR)
                            WHEN aa.preisart = '30tage' THEN
                                DATE_ADD(@start, INTERVAL (FLOOR(TIMESTAMPDIFF(DAY, @start, IF(aa.enddatum = '0000-00-00' OR aa.enddatum > '$scalcdate', '$scalcdate', aa.enddatum)) / 30)+1)*30 DAY )
                        END, '%d.%m.%Y') SEPARATOR '<br>') as end,
                    SUM((100-aa.rabatt)/100 * aa.preis * aa.menge * 
                        (GREATEST(aa.zahlzyklus, CASE
                            WHEN aa.preisart = 'monat' THEN
                                TIMESTAMPDIFF(MONTH, @start, @end)
                            WHEN aa.preisart = 'jahr' THEN
                                TIMESTAMPDIFF(YEAR,  @start, @end)
                            WHEN aa.preisart = '30tage' THEN
                                FLOOR(TIMESTAMPDIFF(DAY, @start, @end) / 30)
                            END
                        ))
                    ) as amount,
                    adr.id
                    FROM abrechnungsartikel aa
                    JOIN artikel a ON aa.artikel = a.id
                    JOIN adresse adr ON aa.adresse = adr.id
                    LEFT JOIN projekt p ON aa.projekt = p.id";

                $groupby = " GROUP BY aa.adresse, aa.projekt";

                $count = "SELECT count(aa.id)
                    FROM `abrechnungsartikel` AS `aa`  
                    WHERE $where $groupby";
                $menucol = 10;
                $moreinfo = true;
                break;

            case 'rechnungslauf_abos':
                $allowed['rechnungslauf'] = ['abos'];
                $heading = array(
                    'Kunde',
                    'Kunden Nr.',
                    'Bezeichnung',
                    'Nummer',
                    'Abgerechnet bis',
                    'Enddatum',
                    'Preis',
                    'Rabatt',
                    'Menge',
                    'Art',
                    'Zahlperiode',
                    'Zahlweise',
                    'Dokument',
                    'Men&uuml;');
                $width = ['10%','5%','15%','1','1','1','1','1','1','1','1','1','1','1'];

                $findcols = [
                    'ad.name',
                    'ad.kundennummer',
                    'aa.bezeichnung',
                    'a.nummer',
                    "DATE_FORMAT(aa.abgerechnetbis, '%d.%m.%Y')",
                    'aa.enddatum',
                    'aa.preis',
                    'aa.rabatt',
                    'aa.menge',
                    'aa.preisart',
                    'aa.zahlzyklus',
                    '',
                    'aa.dokument'
                ];
                $searchsql = ['ad.name', 'aa.bezeichnung'];

                $numbercols = [0];
                $alignright = [7];
                $datecols = [5,6];

                $defaultorder = 1;
                $defaultorderdesc = 0;

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>"
                    . "<a href=\"index.php?module=adresse&action=artikel&id=%value%\">"
                    . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>"
                    . "&nbsp;</td></tr></table>";

                $where = " aa.id > 0 AND aa.preisart IN ('monat', 'jahr', '30tage') 
                          AND (aa.enddatum = '0000-00-00' OR aa.abgerechnetbis < aa.enddatum) ";

                $sql = "SELECT SQL_CALC_FOUND_ROWS aa.id, ad.name, ad.kundennummer, 
                        aa.bezeichnung, a.nummer, DATE_FORMAT(aa.abgerechnetbis, '%d.%m.%Y'),
                        DATE_FORMAT(aa.enddatum, '%d.%m.%Y'),
                        ".$app->erp->FormatPreis('aa.preis', 2).", aa.rabatt, aa.menge,
                        aa.preisart, aa.zahlzyklus, '', aa.dokument, ad.id
                        FROM `abrechnungsartikel` AS `aa`
                        LEFT JOIN `adresse` AS `ad` ON aa.adresse = ad.id
                        LEFT JOIN `artikel` AS `a` ON aa.artikel = a.id";

                $count = "SELECT count(aa.id)
                        FROM `abrechnungsartikel` AS `aa` 
                        WHERE $where";
                break;
        }

        $erg = [];

        foreach($erlaubtevars as $k => $v) {
            if(isset($$v)) {
                $erg[$v] = $$v;
            }
        }

        return $erg;
    }

    /**
     * Rechnungslauf constructor.
     *
     * @param Application $app
     * @param bool        $intern
     */
    public function __construct($app, $intern = false) {
        $this->app = $app;
        if($intern) {
            return;
        }
        $this->app->ActionHandlerInit($this);

        // ab hier alle Action Handler definieren die das Modul hat
        $this->app->ActionHandler('list', 'ActionList');
        $this->app->ActionHandler('abos', 'ActionAbos');
        $this->app->ActionHandler('minidetail', 'ActionMinidetail');

        $this->app->ActionHandlerListen($app);
    }

    public function MenuList() {
        $this->app->erp->Headlines("Abolauf");
        $this->app->erp->MenuEintrag("index.php?module=rechnungslauf&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=rechnungslauf&action=abos", "gebuchte Abos");
        $this->app->erp->MenuEintrag("index.php?module=rechnungslauf&action=einstellungen", "Einstellungen");
    }

    public function ActionList() {
      /** @var SubscriptionModule $module */
      $this->MenuList();
      $this->app->YUI->TableSearch("TAB_INVOICES", 'rechnungslauf_invoices','show', '', '', basename(__FILE__), __CLASS__);
      $this->app->YUI->TableSearch("TAB_ORDERS", 'rechnungslauf_orders','show', '', '', basename(__FILE__), __CLASS__);
      if ($this->app->Secure->GetPOST('createInvoices') !== '') {
        $selection = $this->app->Secure->GetPOST('selection');
        /** @var SubscriptionCycleJobService $subscriptionCycleJobService */
        $subscriptionCycleJobService = $this->app->Container->get('SubscriptionCycleJobService');
        foreach ($selection as $value) {
          $subscriptionCycleJobService->deleteJobsByAddressIdAndDoctype($value, 'rechnung');
          $subscriptionCycleJobService->create($value, 'rechnung');
          $this->app->Tpl->addMessage('info', 'Die Rechnungen werden nun im Hintergrund erstellt', false, 'MESSAGE_INVOICES');
        }
      }
      else if ($this->app->Secure->GetPOST('createOrders') !== '') {
        $selection = $this->app->Secure->GetPOST('selection');
        /** @var SubscriptionCycleJobService $subscriptionCycleJobService */
        $subscriptionCycleJobService = $this->app->Container->get('SubscriptionCycleJobService');
        foreach ($selection as $value) {
          $subscriptionCycleJobService->deleteJobsByAddressIdAndDoctype($value, 'auftrag');
          $subscriptionCycleJobService->create($value, 'auftrag');
          $this->app->Tpl->addMessage('info', 'Die Aufträge werden nun im Hintergrund erstellt', false, 'MESSAGE_ORDERS');
        }
      }

        $cronjobActive = $this->app->DB->Select(
            "SELECT ps.id 
            FROM `prozessstarter` AS `ps` 
            WHERE ps.aktiv = 1 and (ps.parameter = 'rechnungslauf_manual') 
            LIMIT 1"
        );
        if(!$cronjobActive) {
            $this->app->Tpl->addMessage('warning', 'Der Prozessstarter \'rechnungslauf_manual\' ist nicht aktiv');
        }

      $this->app->Tpl->Parse('PAGE', 'rechnungslauf_list.tpl');
    }

    public function ActionAbos() {
        $this->MenuList();
        $this->app->YUI->TableSearch("TAB1", 'rechnungslauf_abos', 'show', '', '', basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', 'rechnungslauf_abos.tpl');
    }

    public function ActionMinidetail() {
      /** @var SubscriptionModule $module */
      $module = $this->app->Container->get('SubscriptionModule');
      $address = $this->app->Secure->GetGET('id');
      $pos = $module->GetPositions($address, 'rechnung');
      foreach ($pos as $p) {
        $row = '<tr>';
        $row .= sprintf('<td>%s</td>', $p['bezeichnung']);
        $row .= sprintf('<td>%s</td>', $p['menge']);
        $row .= sprintf('<td style="text-align: right">%s</td>',
            $this->app->erp->number_format_variable($p['preis'], 2));
        $row .= sprintf('<td>%s</td>', $p['rabatt']);
        $row .= sprintf('<td>%s</td>', $p['cycles']);
        $row .= sprintf('<td style="text-align: right">%s</td>',
            $this->app->erp->number_format_variable($p['preis']*$p['menge']*$p['cycles'], 2));
          $row .= sprintf('<td>%s</td>', $p['waehrung']);
        $row .= '</tr>';
        $this->app->Tpl->Add('INHALT', $row);
      }
      $this->app->Tpl->Set('SUBHEADING', 'Kunde');
      $this->app->Tpl->Output('rechnungslauf_minidetail.tpl');
      $this->app->ExitXentral();
    }
}
