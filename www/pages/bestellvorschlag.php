<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Bestellvorschlag {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "bestellvorschlag_list");
//        $this->app->ActionHandler("create", "bestellvorschlag_edit"); // This automatically adds a "New" button
//        $this->app->ActionHandler("edit", "bestellvorschlag_edit");
//        $this->app->ActionHandler("delete", "bestellvorschlag_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    public function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "bestellvorschlag_list":
                $allowed['bestellvorschlag_list'] = array('list');

                $artikelkategorieid = 'SUBSTRING_INDEX(SUBSTRING_INDEX(art.typ, \'kat\', 1), \'_\', 1)';
                $kategorienfilter = $this->app->User->GetParameter('bestellvorschlag_kategorienfilter');

                if (!empty($kategorienfilter)) {
                    $kategorienwhere = " AND $artikelkategorieid IN (".$kategorienfilter.")";
                }

                $monate_absatz = $this->app->User->GetParameter('bestellvorschlag_monate_absatz');
                if (empty($monate_absatz)) {
                     $monate_absatz = 0;
                }
                $monate_voraus = $this->app->User->GetParameter('bestellvorschlag_monate_voraus');
                if (empty($monate_voraus)) {
                     $monate_voraus = 0;
                }


                // Toggle filters
                $this->app->Tpl->Add('JQUERYREADY', "$('#mindestlager').click( function() { fnFilterColumn1( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#entwuerfe').click( function() { fnFilterColumn2( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#reserviersperre').click( function() { fnFilterColumn3( 0 ); } );");

                for ($r = 1;$r <= 3;$r++) {
                  $this->app->Tpl->Add('JAVASCRIPT', '
                                         function fnFilterColumn' . $r . ' ( i )
                                         {
                                         if(oMoreData' . $r . $name . '==1)
                                         oMoreData' . $r . $name . ' = 0;
                                         else
                                         oMoreData' . $r . $name . ' = 1;

                                         $(\'#' . $name . '\').dataTable().fnFilter(
                                           \'\',
                                           i,
                                           0,0
                                           );
                                         }
                                         ');
                }

                $more_data1 = $this->app->Secure->GetGET("more_data1");
                if ($more_data1 == 1) {
                    $mindestlager = 'art.mindestlager';
                  } else {
                    $mindestlager = '0';
                }

                $more_data2 = $this->app->Secure->GetGET("more_data2");
                if ($more_data2 == 1) {
                    $entwuerfe = ",'angelegt'";
                  } else {
                    $entwuerfe = '';
                }
                
                $more_data3 = $this->app->Secure->GetGET("more_data3");
                if ($more_data3 == 1) {
                      $reserviersperre = '1';
                  } else {
                      $reserviersperre = 'COALESCE(auf.nicht_reservieren, 0) <> 1';
                }

                $heading = array('',  '',  'Nr.', 'Artikel','Bestell-Nr.','Kategorie','Lieferant','Mindestlager','Lager','Bestellt','Auftrag','Absatz','Voraus','Vorschlag','Eingabe','');
                $width =   array('1%','1%','1%',  '15%',    '15%',           '10%',       '10%',      '1%',          '1%',   '1%',      '1%',     '1%',    '1%',    '1%',       '1%',     '1%');

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8);

                $artikelkategorie = '(select bezeichnung from artikelkategorien where id=(select SUBSTRING_INDEX(SUBSTRING_INDEX(art.typ, \'kat\', 1), \'_\', 1) as type from artikel where id=art.id))';

                $bestellnummer = '(SELECT bestellnummer FROM einkaufspreise WHERE adresse = art.adresse AND artikel=art.id AND geloescht <> 1 AND (gueltig_bis > NOW() OR gueltig_bis = \'0000-00-00\') ORDER BY id DESC LIMIT 1)';

                $findcols = array('art.id','art.id','art.nummer','art.name_de',$bestellnummer,$artikelkategorie,'l.name','mindestlager','lager','bestellt','auftrag','absatz','voraus','vorschlag_ber_form','vorschlag','art.id');
                $searchsql = array('art.name_de');

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $numbercols = array(8,9,10,11,12,13,14);
//                $sumcol = array(6);
                $alignright = array(8,9,10,11,12,13,14);

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',art.id,'\" />') AS `auswahl`";

//                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=bestellvorschlag&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=bestellvorschlag&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                 $artikellink = array (
                    '<a href="index.php?module=artikel&action=edit&id=',
                    ['sql' => 'art.id'],
                    '">',
                    ['sql' => 'art.nummer'],
                    '</a>'
                );

                $input_for_menge = "CONCAT(
                        '<input type = \"number\" min=\"0\"',
                        ' name=\"menge_',
                        art.id,
                        '\" value=\"',
                        ROUND((SELECT mengen.vorschlag)),
                        '\" style=\"text-align:right; width:100%\">',
                        '</input>'
                    )";

                $user = $app->User->GetID();

        		$sql_artikel_mengen = "
 SELECT
    art.id,
    (
    SELECT
        COALESCE(SUM(menge),0)
    FROM
        lager_platz_inhalt lpi
    INNER JOIN lager_platz lp ON
        lp.id = lpi.lager_platz
    WHERE
        lpi.artikel = art.id AND lp.sperrlager = 0
) AS lager,
(
    SELECT
        COALESCE(SUM(menge - geliefert),0)
    FROM
        bestellung_position bp
    INNER JOIN bestellung b ON
        bp.bestellung = b.id
    WHERE
        bp.artikel = art.id AND b.status IN(
            'versendet',
            'freigegeben'
            $entwuerfe
        )
) AS bestellt,
(
    SELECT
        COALESCE(SUM(menge - geliefert),0)
    FROM
        auftrag_position aufp
    INNER JOIN auftrag auf ON
        aufp.auftrag = auf.id
    WHERE
        aufp.artikel = art.id AND auf.status IN(
            'versendet',
            'freigegeben'
            $entwuerfe
        ) AND NOT (auf.zahlungsweise = 'vorkasse' AND auf.vorabbezahltmarkieren <> 1)
        AND $reserviersperre
) AS auftrag,
(
    SELECT
        COALESCE(SUM(menge),0)
    FROM
       rechnung_position rp
    INNER JOIN rechnung r ON
        rp.rechnung = r.id
    WHERE
        rp.artikel = art.id AND r.status IN(
            'versendet',
            'freigegeben'
            $entwuerfe
        ) AND r.datum > LAST_DAY(CURDATE() - INTERVAL ('$monate_absatz'+1) MONTH) AND r.datum <= LAST_DAY(CURDATE() - INTERVAL 1 MONTH)
) AS absatz,
ROUND (
(
    select absatz
) / '$monate_absatz' * '$monate_voraus') AS voraus,
(
    SELECT
        COALESCE(menge,0)
    FROM
        bestellvorschlag bv
    WHERE
        bv.artikel = art.id AND bv.user = '$user'
) AS vorschlag_save,
$mindestlager -(
SELECT
    lager
) - COALESCE((
SELECT
    bestellt
),
0)
 + COALESCE((
SELECT
    auftrag
),
0)
 + COALESCE((
SELECT
    voraus
),
0)
 AS vorschlag_ber_raw,
IF(
    (
SELECT
    vorschlag_ber_raw
) > 0,
(
SELECT
    vorschlag_ber_raw
),
0
) AS vorschlag_ber,
COALESCE(
    (
SELECT
    vorschlag_save
),
(
SELECT
    vorschlag_ber
)
) AS vorschlag,
FORMAT(art.mindestlager, 0, 'de_DE') AS mindestlager_form,
FORMAT((
SELECT
    lager
),
0,
'de_DE') AS lager_form,
FORMAT(
    COALESCE((
SELECT
    bestellt
),
0),
    0,
    'de_DE'
) AS bestellt_form,
FORMAT(
    COALESCE((
SELECT
    auftrag
),
0),
    0,
    'de_DE'
) AS auftrag_form,
FORMAT(
    COALESCE((
SELECT
    absatz
),
0),
    0,
    'de_DE'
) AS absatz_form,
FORMAT(
    COALESCE((
SELECT
    voraus
),
0),
    0,
    'de_DE'
) AS voraus_form,
FORMAT(
    (
SELECT
    vorschlag_ber
),
'0',
'de_DE'
) AS vorschlag_ber_form
,
FORMAT(
    (
SELECT
    vorschlag
),
'0',
'de_DE'
) AS vorschlag_form
FROM
    artikel art
                    ";


//echo($sql_artikel_mengen);


                $sql = "SELECT SQL_CALC_FOUND_ROWS
                    art.id,
                    $dropnbox,
                    ".$app->erp->ConcatSQL($artikellink).",
                    art.name_de,
                    $bestellnummer,
                    $artikelkategorie as artikelkategorie,
                    l.name,
        		    mengen.mindestlager_form,
		            mengen.lager_form,
    	            mengen.bestellt_form,
                    mengen.auftrag_form,
                    mengen.absatz_form,
                    mengen.voraus_form,
		            mengen.vorschlag_ber_form,"
        		    .$input_for_menge
                    ."FROM
			artikel art
		    INNER JOIN
			adresse l ON l.id = art.adresse
		    INNER JOIN
			(SELECT * FROM ($sql_artikel_mengen) mengen_inner WHERE mengen_inner.vorschlag > 0) as mengen ON mengen.id = art.id";

                $where = "art.adresse != '' AND art.geloescht != 1 AND art.inaktiv != 1";

                $where .= $kategorienwhere;

                $count = "SELECT count(DISTINCT art.id) FROM artikel art WHERE $where";
//                $groupby = "";

                break;
        }

        $erg = false;

        foreach ($erlaubtevars as $k => $v) {
            if (isset($$v)) {
                $erg[$v] = $$v;
            }
        }
        return $erg;
    }

    function bestellvorschlag_list() {


        $submit = $this->app->Secure->GetPOST('submit');
        $user = $this->app->User->GetID();

        $monate_absatz = $this->app->Secure->GetPOST('monate_absatz');
        if (empty($monate_absatz)) {
             $monate_absatz = 0;
        }
        $monate_voraus = $this->app->Secure->GetPOST('monate_voraus');
        if (empty($monate_voraus)) {
             $monate_voraus = 0;
        }

        $kategorienfilter = $this->app->Secure->GetPOST('kategorien');

        // For transfer to tablesearch
        if (!empty($kategorienfilter)) {
            $this->app->User->SetParameter('bestellvorschlag_kategorienfilter', implode(', ',$kategorienfilter));
        } else {
            $this->app->User->SetParameter('bestellvorschlag_kategorienfilter', '');
        }

        $this->app->User->SetParameter('bestellvorschlag_monate_absatz', $monate_absatz);
        $this->app->User->SetParameter('bestellvorschlag_monate_voraus', $monate_voraus);

        switch ($submit) {
            case 'loeschen':
                $sql = "DELETE FROM bestellvorschlag where user = $user";
                $this->app->DB->Delete($sql);
            break;
            case 'speichern':

                $menge_input = $this->app->Secure->GetPOSTArray();
                $mengen = array();
                foreach ($menge_input as $key => $menge) {
                    if ((strpos($key,'menge_') === 0) && ($menge !== '')) {
                        $artikel = substr($key,'6');
                        if ($menge >= 0) {
                            $sql = "INSERT INTO bestellvorschlag (artikel, user, menge) VALUES($artikel,$user,$menge) ON DUPLICATE KEY UPDATE menge = $menge";
                            $this->app->DB->Insert($sql);
                        }
                    }
                }
            break;
            case 'bestellungen_erzeugen':

                $auswahl = $this->app->Secure->GetPOST('auswahl');
                $selectedIds = [];

                if(empty($auswahl)) {
                    $msg = '<div class="error">Bitte Artikel ausw&auml;hlen.</div>';
                    break;
                }

                if(!empty($auswahl)) {
                    foreach ($auswahl as $selectedId) {
                        $selectedId = (int) $selectedId;
                        if ($selectedId > 0) {
                          $selectedIds[] = $selectedId;
                        }
                    }
                }

                $menge_input = $this->app->Secure->GetPOSTArray();
                $mengen = array();

                foreach ($selectedIds as $artikel_id) {
                    foreach ($menge_input as $key => $menge) {
                        if ((strpos($key,'menge_') === 0) && ($menge !== '')) {
                            $artikel = substr($key,'6');
                            if ($menge > 0 && $artikel == $artikel_id) {
                              $mengen[] = array('id' => $artikel,'menge' => $menge);
                            }
                        }
                    }
                }

                $mengen_pro_adresse = array();
                foreach ($mengen as $menge) {
                    $sql = "SELECT adresse FROM artikel WHERE id = ".$menge['id'];
                    $adresse = $this->app->DB->Select($sql);
                    if (!empty($adresse)) {
                        $index = array_search($adresse, array_column($mengen_pro_adresse,'adresse'));
                        if ($index !== false) {
                            $mengen_pro_adresse[$index]['positionen'][] = $menge;
                        } else {
                            $mengen_pro_adresse[] = array('adresse' => $adresse,'positionen' => array($menge));
                        }
                    }
                }

                $angelegt = 0;

                foreach ($mengen_pro_adresse as $bestelladresse) {
                    $bestellid = $this->app->erp->CreateBestellung($bestelladresse);
                    if (!empty($bestellid)) {

                        $angelegt++;

                        $this->app->erp->LoadBestellungStandardwerte($bestellid,$bestelladresse['adresse']);
                        $this->app->erp->BestellungProtokoll($bestellid,"Bestellung angelegt");
                        foreach ($bestelladresse['positionen'] as $position) {
                            $preisid = $this->app->erp->Einkaufspreis($position['id'], $position['menge'], $bestelladresse['adresse']);

                            if ($preisid == null) {
                                $artikelohnepreis = $position['id'];
                            } else {
                                $artikelohnepreis = null;
                            }

                            $this->app->erp->AddBestellungPosition(
                                $bestellid,
                                $preisid,
                                $position['menge'],
                                $datum,
                                '',
                                $artikelohnepreis
                            );
                        }
                        $this->app->erp->BestellungNeuberechnen($bestellid);
                    }
                }
                $msg .= "<div class=\"success\">Es wurden $angelegt Bestellungen angelegt.</div>";
            break;
        }

        $this->app->erp->MenuEintrag("index.php?module=bestellvorschlag&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=bestellvorschlag&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->Tpl->Set('MONATE_ABSATZ',$monate_absatz);
        $this->app->Tpl->Set('MONATE_VORAUS',$monate_voraus);

        $this->app->Tpl->Set('MESSAGE',$msg);

        $kategorien = $this->app->DB->SelectArr("SELECT id, bezeichnung name FROM artikelkategorien WHERE geloescht <> 1 ORDER by bezeichnung");

        $this->app->Tpl->Set('KATEGORIENFILTER',$this->app->erp->GetCheckboxes($kategorien, 'kategorien'));

        $this->app->YUI->TableSearch('TAB1', 'bestellvorschlag_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "bestellvorschlag_list.tpl");
    }

    public function bestellvorschlag_delete() {
        $id = (int) $this->app->Secure->GetGET('id');

        $this->app->DB->Delete("DELETE FROM `bestellvorschlag` WHERE `id` = '{$id}'");
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");

        $this->bestellvorschlag_list();
    }

    /*
     * Edit bestellvorschlag item
     * If id is empty, create a new one
     */

    function bestellvorschlag_edit() {
        $id = $this->app->Secure->GetGET('id');

        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
        {
          return;
        }

        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=bestellvorschlag&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=bestellvorschlag&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');

        if (empty($id)) {
            // New item
            $id = 'NULL';
        }

        if ($submit != '')
        {

            // Write to database

            // Add checks here

            $columns = "id, ";
            $values = "$id, ";
            $update = "";

            $fix = "";

            foreach ($input as $key => $value) {
                $columns = $columns.$fix.$key;
                $values = $values.$fix."'".$value."'";
                $update = $update.$fix.$key." = '$value'";

                $fix = ", ";
            }

//            echo($columns."<br>");
//            echo($values."<br>");
//            echo($update."<br>");

            $sql = "INSERT INTO bestellvorschlag (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=bestellvorschlag&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }


        // Load values again from database
	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',b.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS b.id, $dropnbox, b.artikel, b.adresse, b.lager, b.id FROM bestellvorschlag b"." WHERE id=$id");

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);
        }

        /*
         * Add displayed items later
         *

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);
         */

//        $this->SetInput($input);
        $this->app->Tpl->Parse('PAGE', "bestellvorschlag_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');

        $input['artikel'] = $this->app->Secure->GetPOST('artikel');
	$input['adresse'] = $this->app->Secure->GetPOST('adresse');
	$input['lager'] = $this->app->Secure->GetPOST('lager');
	

        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);

        $this->app->Tpl->Set('ARTIKEL', $input['artikel']);
	$this->app->Tpl->Set('ADRESSE', $input['adresse']);
	$this->app->Tpl->Set('LAGER', $input['lager']);
	
    }

}
