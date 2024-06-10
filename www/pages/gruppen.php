<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Gruppen {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "gruppen_list");        
        $this->app->ActionHandler("create", "gruppen_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "gruppen_edit");
        $this->app->ActionHandler("delete", "gruppen_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "gruppen_list":
                $allowed['gruppen_list'] = array('list');
                $heading = array(
                    '',
                    '',
                    'Kennziffer',
                    'Name',
                    'Art',
                    'Internebemerkung',                    
                    'Projekt',
                    'Aktiv',
                    'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array(
                'g.id',
                'g.id',
                'g.kennziffer',
                'g.name',
                'g.art',
                'g.internebemerkung', 
                'p.abkuerzung',                 
                'g.aktiv',
                'g.id'
                );
                $searchsql = array('g.name', 'g.art', 'g.kennziffer', 'g.internebemerkung'); 

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $aligncenter = array();
                $alignright = array();
                $numbercols = array();
                $sumcol = array();

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',g.id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $menucol = 11; // Set id col for moredata/menu

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=gruppen&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=gruppen&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS g.id, $dropnbox, g.kennziffer, g.name, g.art, g.internebemerkung, p.abkuerzung, g.aktiv, g.id FROM gruppen g LEFT JOIN projekt p ON g.projekt = p.id";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM gruppen WHERE $where";
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
    
    function gruppen_list() {
        $this->app->erp->MenuEintrag("index.php?module=gruppen&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=gruppen&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'gruppen_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "gruppen_list.tpl");
    }    

    public function gruppen_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `gruppen` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->gruppen_list();
    } 

    /*
     * Edit gruppen item
     * If id is empty, create a new one
     */
        
    function gruppen_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
/*        if($this->app->erp->DisableModul('gruppen',$id))
        {
          return;
        }   */
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=gruppen&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=gruppen&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();
        
        // Convert here
    	$input['aktiv'] = !empty($this->app->Secure->GetPOST('aktiv'))?"1":"0";        
                
        $submit = $this->app->Secure->GetPOST('submit');
                
        if (empty($id)) {
            // New item
            $id = 'NULL';
        } 

        if ($submit != '')
        {

            // Write to database
            
            // Add checks here

            $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$input['projekt'],true); // Parameters: Target db?, value, from form?

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

            $sql = "INSERT INTO gruppen (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=gruppen&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
	    $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',g.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS g.id, $dropnbox, g.name, g.art, g.kennziffer, g.internebemerkung, g.grundrabatt, g.rabatt1, g.rabatt2, g.rabatt3, g.rabatt4, g.rabatt5, g.sonderrabatt_skonto, g.provision, g.kundennummer, g.partnerid, g.dta_aktiv, g.dta_periode, g.dta_dateiname, g.dta_mail, g.dta_mail_betreff, g.dta_mail_text, g.dtavariablen, g.dta_variante, g.bonus1, g.bonus1_ab, g.bonus2, g.bonus2_ab, g.bonus3, g.bonus3_ab, g.bonus4, g.bonus4_ab, g.bonus5, g.bonus5_ab, g.bonus6, g.bonus6_ab, g.bonus7, g.bonus7_ab, g.bonus8, g.bonus8_ab, g.bonus9, g.bonus9_ab, g.bonus10, g.bonus10_ab, g.zahlungszieltage, g.zahlungszielskonto, g.zahlungszieltageskonto, g.portoartikel, g.portofreiab, g.erweiterteoptionen, g.zentralerechnung, g.zentralregulierung, g.gruppe, g.preisgruppe, g.verbandsgruppe, g.rechnung_name, g.rechnung_strasse, g.rechnung_ort, g.rechnung_plz, g.rechnung_abteilung, g.rechnung_land, g.rechnung_email, g.rechnung_periode, g.rechnung_anzahlpapier, g.rechnung_permail, g.webid, g.portofrei_aktiv, g.projekt, g.objektname, g.objekttyp, g.parameter, g.objektname2, g.objekttyp2, g.parameter2, g.objektname3, g.objekttyp3, g.parameter3, g.kategorie, g.aktiv, g.id FROM gruppen g"." WHERE id=$id");        

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }

        if (!empty($result)) {
            $gruppen_from_db = $result[0];
        } else {

        }
             
        /*
         * Add displayed items later
         * 

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);         

        $this->app->YUI->AutoComplete("artikel", "artikelnummer");

         */
                 
        $this->app->YUI->AutoComplete("kennziffer", "gruppekennziffer");
        
        if ($gruppen_from_db['art'] != 'preisgruppe') {
            $this->app->Tpl->Set('PREISGRUPPEHIDDEN','hidden');     
        }
        
        $art_select = Array( 
            'gruppe' => 'Gruppe',
            'preisgruppe' => 'Preisgruppe'
        );       
        $art_select = $this->app->erp->GetSelectAsso($art_select,$gruppen_from_db['art']);
        $this->app->Tpl->Set('ARTSELECT',$art_select);             
        
        $this->app->YUI->AutoComplete("projekt","projektname",1);           
        $this->app->Tpl->Set('PROJEKT',$this->app->erp->ReplaceProjekt(false,$gruppen_from_db['projekt'],false));     

      	$this->app->Tpl->Set('AKTIV', $gruppen_from_db['aktiv']==1?"checked":"");

        $this->app->Tpl->Parse('PAGE', "gruppen_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['name'] = $this->app->Secure->GetPOST('name');
	$input['art'] = $this->app->Secure->GetPOST('art');
	$input['kennziffer'] = $this->app->Secure->GetPOST('kennziffer');
	$input['internebemerkung'] = $this->app->Secure->GetPOST('internebemerkung');
	$input['grundrabatt'] = $this->app->Secure->GetPOST('grundrabatt');
	$input['rabatt1'] = $this->app->Secure->GetPOST('rabatt1');
	$input['rabatt2'] = $this->app->Secure->GetPOST('rabatt2');
	$input['rabatt3'] = $this->app->Secure->GetPOST('rabatt3');
	$input['rabatt4'] = $this->app->Secure->GetPOST('rabatt4');
	$input['rabatt5'] = $this->app->Secure->GetPOST('rabatt5');
	$input['sonderrabatt_skonto'] = $this->app->Secure->GetPOST('sonderrabatt_skonto');
	$input['provision'] = $this->app->Secure->GetPOST('provision');
	$input['kundennummer'] = $this->app->Secure->GetPOST('kundennummer');
	$input['partnerid'] = $this->app->Secure->GetPOST('partnerid');
	$input['dta_aktiv'] = $this->app->Secure->GetPOST('dta_aktiv');
	$input['dta_periode'] = $this->app->Secure->GetPOST('dta_periode');
	$input['dta_dateiname'] = $this->app->Secure->GetPOST('dta_dateiname');
	$input['dta_mail'] = $this->app->Secure->GetPOST('dta_mail');
	$input['dta_mail_betreff'] = $this->app->Secure->GetPOST('dta_mail_betreff');
	$input['dta_mail_text'] = $this->app->Secure->GetPOST('dta_mail_text');
	$input['dtavariablen'] = $this->app->Secure->GetPOST('dtavariablen');
	$input['dta_variante'] = $this->app->Secure->GetPOST('dta_variante');
	$input['bonus1'] = $this->app->Secure->GetPOST('bonus1');
	$input['bonus1_ab'] = $this->app->Secure->GetPOST('bonus1_ab');
	$input['bonus2'] = $this->app->Secure->GetPOST('bonus2');
	$input['bonus2_ab'] = $this->app->Secure->GetPOST('bonus2_ab');
	$input['bonus3'] = $this->app->Secure->GetPOST('bonus3');
	$input['bonus3_ab'] = $this->app->Secure->GetPOST('bonus3_ab');
	$input['bonus4'] = $this->app->Secure->GetPOST('bonus4');
	$input['bonus4_ab'] = $this->app->Secure->GetPOST('bonus4_ab');
	$input['bonus5'] = $this->app->Secure->GetPOST('bonus5');
	$input['bonus5_ab'] = $this->app->Secure->GetPOST('bonus5_ab');
	$input['bonus6'] = $this->app->Secure->GetPOST('bonus6');
	$input['bonus6_ab'] = $this->app->Secure->GetPOST('bonus6_ab');
	$input['bonus7'] = $this->app->Secure->GetPOST('bonus7');
	$input['bonus7_ab'] = $this->app->Secure->GetPOST('bonus7_ab');
	$input['bonus8'] = $this->app->Secure->GetPOST('bonus8');
	$input['bonus8_ab'] = $this->app->Secure->GetPOST('bonus8_ab');
	$input['bonus9'] = $this->app->Secure->GetPOST('bonus9');
	$input['bonus9_ab'] = $this->app->Secure->GetPOST('bonus9_ab');
	$input['bonus10'] = $this->app->Secure->GetPOST('bonus10');
	$input['bonus10_ab'] = $this->app->Secure->GetPOST('bonus10_ab');
	$input['zahlungszieltage'] = $this->app->Secure->GetPOST('zahlungszieltage');
	$input['zahlungszielskonto'] = $this->app->Secure->GetPOST('zahlungszielskonto');
	$input['zahlungszieltageskonto'] = $this->app->Secure->GetPOST('zahlungszieltageskonto');
	$input['portoartikel'] = $this->app->Secure->GetPOST('portoartikel');
	$input['portofreiab'] = $this->app->Secure->GetPOST('portofreiab');
	$input['erweiterteoptionen'] = $this->app->Secure->GetPOST('erweiterteoptionen');
	$input['zentralerechnung'] = $this->app->Secure->GetPOST('zentralerechnung');
	$input['zentralregulierung'] = $this->app->Secure->GetPOST('zentralregulierung');
	$input['gruppe'] = $this->app->Secure->GetPOST('gruppe');
	$input['preisgruppe'] = $this->app->Secure->GetPOST('preisgruppe');
	$input['verbandsgruppe'] = $this->app->Secure->GetPOST('verbandsgruppe');
	$input['rechnung_name'] = $this->app->Secure->GetPOST('rechnung_name');
	$input['rechnung_strasse'] = $this->app->Secure->GetPOST('rechnung_strasse');
	$input['rechnung_ort'] = $this->app->Secure->GetPOST('rechnung_ort');
	$input['rechnung_plz'] = $this->app->Secure->GetPOST('rechnung_plz');
	$input['rechnung_abteilung'] = $this->app->Secure->GetPOST('rechnung_abteilung');
	$input['rechnung_land'] = $this->app->Secure->GetPOST('rechnung_land');
	$input['rechnung_email'] = $this->app->Secure->GetPOST('rechnung_email');
	$input['rechnung_periode'] = $this->app->Secure->GetPOST('rechnung_periode');
	$input['rechnung_anzahlpapier'] = $this->app->Secure->GetPOST('rechnung_anzahlpapier');
	$input['rechnung_permail'] = $this->app->Secure->GetPOST('rechnung_permail');
	$input['webid'] = $this->app->Secure->GetPOST('webid');
	$input['portofrei_aktiv'] = $this->app->Secure->GetPOST('portofrei_aktiv');
	$input['projekt'] = $this->app->Secure->GetPOST('projekt');
	$input['objektname'] = $this->app->Secure->GetPOST('objektname');
	$input['objekttyp'] = $this->app->Secure->GetPOST('objekttyp');
	$input['parameter'] = $this->app->Secure->GetPOST('parameter');
	$input['objektname2'] = $this->app->Secure->GetPOST('objektname2');
	$input['objekttyp2'] = $this->app->Secure->GetPOST('objekttyp2');
	$input['parameter2'] = $this->app->Secure->GetPOST('parameter2');
	$input['objektname3'] = $this->app->Secure->GetPOST('objektname3');
	$input['objekttyp3'] = $this->app->Secure->GetPOST('objekttyp3');
	$input['parameter3'] = $this->app->Secure->GetPOST('parameter3');
	$input['kategorie'] = $this->app->Secure->GetPOST('kategorie');
	$input['aktiv'] = $this->app->Secure->GetPOST('aktiv');
	

        return $input;
    }
 }
