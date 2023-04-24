<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Konten {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "konten_list");        
        $this->app->ActionHandler("create", "konten_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "konten_edit");
        $this->app->ActionHandler("delete", "konten_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "konten_list":
                $allowed['konten_list'] = array('list');
                $heading = array('','','Bezeichnung', 'Kurzbezeichnung', 'Typ', 'Projekt', 'Aktiv','Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('k.id','k.id','k.bezeichnung', 'k.kurzbezeichnung', 'k.type', 'p.abkuerzung', ' k.aktiv','k.id');
                $searchsql = array('k.bezeichnung', 'k.kurzbezeichnung', 'k.datevkonto', 'k.blz', 'k.konto', 'k.swift', 'k.iban', 'k.inhaber', 'k.firma','p.abkuerzung');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',k.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=konten&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=konten&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                            k.id,
                            $dropnbox,
                            k.bezeichnung,
                            k.kurzbezeichnung,
                            k.type,                            
                            p.abkuerzung,
                            k.aktiv,
                            k.id 
                        FROM 
                            konten k
                        LEFT JOIN 
                            projekt p
                        ON 
                            p.id = k.projekt    ";

                $where = " k.aktiv = 1 ";

                // Toggle filters
                $app->Tpl->Add('JQUERYREADY', "$('#archiv').click( function() { fnFilterColumn1( 0 ); } );");

                for ($r = 1;$r <= 1;$r++) {
                $app->Tpl->Add('JAVASCRIPT', '
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


                $more_data1 = $app->Secure->GetGET("more_data1");
                if ($more_data1 == 1) {
                   $where .= "  OR k.aktiv <> 1";
                } else {
                }


                $count = "SELECT count(DISTINCT id) FROM konten k WHERE $where";
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
    
    function konten_list() {
        $this->app->erp->MenuEintrag("index.php?module=konten&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=konten&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'konten_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "konten_list.tpl");
    }    

    public function konten_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("UPDATE `konten` SET `aktiv` = 0 WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"info\">Der Eintrag wurde deaktiviert.</div>");        

        $this->konten_list();
    } 

    /*
     * Edit konten item
     * If id is empty, create a new one
     */
        
    function konten_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=konten&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=konten&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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
            $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$input['projekt'],true);

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

            $sql = "INSERT INTO konten (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=konten&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',k.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS k.id, $dropnbox, k.bezeichnung, k.kurzbezeichnung, k.type, k.erstezeile, k.datevkonto, k.blz, k.konto, k.swift, k.iban, k.lastschrift, k.hbci, k.hbcikennung, k.inhaber, k.aktiv, k.keineemail, k.firma, k.schreibbar, k.importletztenzeilenignorieren, k.liveimport, k.liveimport_passwort, k.liveimport_online, k.importtrennzeichen, k.codierung, k.importerstezeilenummer, k.importdatenmaskierung, k.importnullbytes, k.glaeubiger, k.geloescht, k.projekt, k.saldo_summieren, k.saldo_betrag, k.saldo_datum, k.importfelddatum, k.importfelddatumformat, k.importfelddatumformatausgabe, k.importfeldbetrag, k.importfeldbetragformat, k.importfeldbuchungstext, k.importfeldbuchungstextformat, k.importfeldwaehrung, k.importfeldwaehrungformat, k.importfeldhabensollkennung, k.importfeldkennunghaben, k.importfeldkennungsoll, k.importextrahabensoll, k.importfeldhaben, k.importfeldsoll, k.cronjobaktiv, k.cronjobverbuchen, k.last_import, k.importperiode_in_hours, k.id FROM konten k"." WHERE id=$id");

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

        $this->app->YUI->AutoComplete('projekt','projektname',1);
        $this->app->Tpl->Set('PROJEKT',$this->app->erp->ReplaceProjekt(false,$result[0]['projekt'],false));

        $this->app->Tpl->Set('AKTIV',$result[0]['aktiv']==1?'checked':'');
        $this->app->Tpl->Set('KEINEEMAIL',$result[0]['keineemail']==1?'checked':'');
        $this->app->Tpl->Set('SCHREIBBAR',$result[0]['schreibbar']==1?'checked':'');
        $this->app->Tpl->Set('LASTSCHRIFT',$result[0]['lastschrift']==1?'checked':'');
        $this->app->Tpl->Set('SALDO_SUMMIEREN',$result[0]['saldo_summieren']==1?'checked':'');

        $this->app->Tpl->Parse('PAGE', "konten_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['bezeichnung'] = $this->app->Secure->GetPOST('bezeichnung');
	    $input['kurzbezeichnung'] = $this->app->Secure->GetPOST('kurzbezeichnung');
	    $input['type'] = $this->app->Secure->GetPOST('type');
	    $input['erstezeile'] = $this->app->Secure->GetPOST('erstezeile');
	    $input['datevkonto'] = $this->app->Secure->GetPOST('datevkonto');
	    $input['blz'] = $this->app->Secure->GetPOST('blz');
	    $input['konto'] = $this->app->Secure->GetPOST('konto');
	    $input['swift'] = $this->app->Secure->GetPOST('swift');
	    $input['iban'] = $this->app->Secure->GetPOST('iban');
	    $input['lastschrift'] = $this->app->Secure->GetPOST('lastschrift');
	    $input['hbci'] = $this->app->Secure->GetPOST('hbci');
	    $input['hbcikennung'] = $this->app->Secure->GetPOST('hbcikennung');
	    $input['inhaber'] = $this->app->Secure->GetPOST('inhaber');
	    $input['aktiv'] = $this->app->Secure->GetPOST('aktiv');
	    $input['keineemail'] = $this->app->Secure->GetPOST('keineemail');
	    $input['firma'] = $this->app->Secure->GetPOST('firma');
	    $input['schreibbar'] = $this->app->Secure->GetPOST('schreibbar');
	    $input['importletztenzeilenignorieren'] = $this->app->Secure->GetPOST('importletztenzeilenignorieren');
	    $input['liveimport'] = $this->app->Secure->GetPOST('liveimport');
	    $input['liveimport_passwort'] = $this->app->Secure->GetPOST('liveimport_passwort');
	    $input['liveimport_online'] = $this->app->Secure->GetPOST('liveimport_online');
	    $input['importtrennzeichen'] = $this->app->Secure->GetPOST('importtrennzeichen');
	    $input['codierung'] = $this->app->Secure->GetPOST('codierung');
	    $input['importerstezeilenummer'] = $this->app->Secure->GetPOST('importerstezeilenummer');
	    $input['importdatenmaskierung'] = $this->app->Secure->GetPOST('importdatenmaskierung');
	    $input['importnullbytes'] = $this->app->Secure->GetPOST('importnullbytes');
	    $input['glaeubiger'] = $this->app->Secure->GetPOST('glaeubiger');
	    $input['geloescht'] = $this->app->Secure->GetPOST('geloescht');
	    $input['projekt'] = $this->app->Secure->GetPOST('projekt');
	    $input['saldo_summieren'] = $this->app->Secure->GetPOST('saldo_summieren');
	    $input['saldo_betrag'] = $this->app->Secure->GetPOST('saldo_betrag');
	    $input['saldo_datum'] = $this->app->Secure->GetPOST('saldo_datum');
	    $input['importfelddatum'] = $this->app->Secure->GetPOST('importfelddatum');
	    $input['importfelddatumformat'] = $this->app->Secure->GetPOST('importfelddatumformat');
	    $input['importfelddatumformatausgabe'] = $this->app->Secure->GetPOST('importfelddatumformatausgabe');
	    $input['importfeldbetrag'] = $this->app->Secure->GetPOST('importfeldbetrag');
	    $input['importfeldbetragformat'] = $this->app->Secure->GetPOST('importfeldbetragformat');
	    $input['importfeldbuchungstext'] = $this->app->Secure->GetPOST('importfeldbuchungstext');
	    $input['importfeldbuchungstextformat'] = $this->app->Secure->GetPOST('importfeldbuchungstextformat');
	    $input['importfeldwaehrung'] = $this->app->Secure->GetPOST('importfeldwaehrung');
	    $input['importfeldwaehrungformat'] = $this->app->Secure->GetPOST('importfeldwaehrungformat');
	    $input['importfeldhabensollkennung'] = $this->app->Secure->GetPOST('importfeldhabensollkennung');
	    $input['importfeldkennunghaben'] = $this->app->Secure->GetPOST('importfeldkennunghaben');
	    $input['importfeldkennungsoll'] = $this->app->Secure->GetPOST('importfeldkennungsoll');
	    $input['importextrahabensoll'] = $this->app->Secure->GetPOST('importextrahabensoll');
	    $input['importfeldhaben'] = $this->app->Secure->GetPOST('importfeldhaben');
	    $input['importfeldsoll'] = $this->app->Secure->GetPOST('importfeldsoll');
	    $input['cronjobaktiv'] = $this->app->Secure->GetPOST('cronjobaktiv');
	    $input['cronjobverbuchen'] = $this->app->Secure->GetPOST('cronjobverbuchen');
	    $input['last_import'] = $this->app->Secure->GetPOST('last_import');
	    $input['importperiode_in_hours'] = $this->app->Secure->GetPOST('importperiode_in_hours');
	    return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('BEZEICHNUNG', $input['bezeichnung']);
	    $this->app->Tpl->Set('KURZBEZEICHNUNG', $input['kurzbezeichnung']);
	    $this->app->Tpl->Set('TYPE', $input['type']);
	    $this->app->Tpl->Set('ERSTEZEILE', $input['erstezeile']);
	    $this->app->Tpl->Set('DATEVKONTO', $input['datevkonto']);
	    $this->app->Tpl->Set('BLZ', $input['blz']);
	    $this->app->Tpl->Set('KONTO', $input['konto']);
	    $this->app->Tpl->Set('SWIFT', $input['swift']);
	    $this->app->Tpl->Set('IBAN', $input['iban']);
	    $this->app->Tpl->Set('LASTSCHRIFT', $input['lastschrift']);
	    $this->app->Tpl->Set('HBCI', $input['hbci']);
	    $this->app->Tpl->Set('HBCIKENNUNG', $input['hbcikennung']);
	    $this->app->Tpl->Set('INHABER', $input['inhaber']);
	    $this->app->Tpl->Set('AKTIV', $input['aktiv']);
	    $this->app->Tpl->Set('KEINEEMAIL', $input['keineemail']);
	    $this->app->Tpl->Set('FIRMA', $input['firma']);
	    $this->app->Tpl->Set('SCHREIBBAR', $input['schreibbar']);
	    $this->app->Tpl->Set('IMPORTLETZTENZEILENIGNORIEREN', $input['importletztenzeilenignorieren']);
	    $this->app->Tpl->Set('LIVEIMPORT', $input['liveimport']);
	    $this->app->Tpl->Set('LIVEIMPORT_PASSWORT', $input['liveimport_passwort']);
	    $this->app->Tpl->Set('LIVEIMPORT_ONLINE', $input['liveimport_online']);
	    $this->app->Tpl->Set('IMPORTTRENNZEICHEN', $input['importtrennzeichen']);
	    $this->app->Tpl->Set('CODIERUNG', $input['codierung']);
	    $this->app->Tpl->Set('IMPORTERSTEZEILENUMMER', $input['importerstezeilenummer']);
	    $this->app->Tpl->Set('IMPORTDATENMASKIERUNG', $input['importdatenmaskierung']);
	    $this->app->Tpl->Set('IMPORTNULLBYTES', $input['importnullbytes']);
	    $this->app->Tpl->Set('GLAEUBIGER', $input['glaeubiger']);
	    $this->app->Tpl->Set('GELOESCHT', $input['geloescht']);
	    $this->app->Tpl->Set('PROJEKT', $input['projekt']);
	    $this->app->Tpl->Set('SALDO_SUMMIEREN', $input['saldo_summieren']);
	    $this->app->Tpl->Set('SALDO_BETRAG', $input['saldo_betrag']);
	    $this->app->Tpl->Set('SALDO_DATUM', $input['saldo_datum']);
	    $this->app->Tpl->Set('IMPORTFELDDATUM', $input['importfelddatum']);
	    $this->app->Tpl->Set('IMPORTFELDDATUMFORMAT', $input['importfelddatumformat']);
	    $this->app->Tpl->Set('IMPORTFELDDATUMFORMATAUSGABE', $input['importfelddatumformatausgabe']);
	    $this->app->Tpl->Set('IMPORTFELDBETRAG', $input['importfeldbetrag']);
	    $this->app->Tpl->Set('IMPORTFELDBETRAGFORMAT', $input['importfeldbetragformat']);
	    $this->app->Tpl->Set('IMPORTFELDBUCHUNGSTEXT', $input['importfeldbuchungstext']);
	    $this->app->Tpl->Set('IMPORTFELDBUCHUNGSTEXTFORMAT', $input['importfeldbuchungstextformat']);
	    $this->app->Tpl->Set('IMPORTFELDWAEHRUNG', $input['importfeldwaehrung']);
	    $this->app->Tpl->Set('IMPORTFELDWAEHRUNGFORMAT', $input['importfeldwaehrungformat']);
	    $this->app->Tpl->Set('IMPORTFELDHABENSOLLKENNUNG', $input['importfeldhabensollkennung']);
	    $this->app->Tpl->Set('IMPORTFELDKENNUNGHABEN', $input['importfeldkennunghaben']);
	    $this->app->Tpl->Set('IMPORTFELDKENNUNGSOLL', $input['importfeldkennungsoll']);
	    $this->app->Tpl->Set('IMPORTEXTRAHABENSOLL', $input['importextrahabensoll']);
	    $this->app->Tpl->Set('IMPORTFELDHABEN', $input['importfeldhaben']);
	    $this->app->Tpl->Set('IMPORTFELDSOLL', $input['importfeldsoll']);
	    $this->app->Tpl->Set('CRONJOBAKTIV', $input['cronjobaktiv']);
	    $this->app->Tpl->Set('CRONJOBVERBUCHEN', $input['cronjobverbuchen']);
	    $this->app->Tpl->Set('LAST_IMPORT', $input['last_import']);
	    $this->app->Tpl->Set('IMPORTPERIODE_IN_HOURS', $input['importperiode_in_hours']);
	
    }

}
