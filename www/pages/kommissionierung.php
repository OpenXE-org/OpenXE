<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Kommissionierung {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "kommissionierung_list");        
//        $this->app->ActionHandler("create", "kommissionierung_edit"); // This automatically adds a "New" button
//        $this->app->ActionHandler("edit", "kommissionierung_edit");
//        $this->app->ActionHandler("delete", "kommissionierung_delete");
        $this->app->ActionHandler("print", "kommissionierung_print");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "kommissionierung_list":
                $allowed['kommissionierung_list'] = array('list');
                $heading = array('','','Nummer','Datum', 'Adresse', 'Auftrag', 'Lieferschein','Bearbeiter', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('k.id','k.id','k.id','k.zeitstempel', 'ad.name', 'a.belegnr', 'l.belegnr','k.bearbeiter','k.id');
                $searchsql = array('k.id','k.zeitstempel', 'k.bearbeiter', 'a.belegnr', 'ad.name', 'k.kommentar', 'l.belegnr');

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $aligncenter = array();
                $alignright = array();
                $numbercols = array();
                $sumcol = array();

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',k.id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $menucol = 11; // Set id col for moredata/menu

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=kommissionierung&action=print&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS k.id,
                            $dropnbox,
                            k.id as nummer,
                            ".$app->erp->FormatDate('k.zeitstempel').",
                            ad.name,
                            a.belegnr as auftragnr,
                            l.belegnr as lieferscheinnr,
                            k.bearbeiter,
                            k.id
                        FROM
                            kommissionierung k
                        LEFT JOIN lieferschein l ON
                            l.id = k.lieferschein
                        LEFT JOIN auftrag a ON
                            a.id = k.auftrag
                        LEFT JOIN adresse ad ON
                            ad.id = k.adresse
";
                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM kommissionierung WHERE $where";
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
    
    function kommissionierung_list() {
        $this->app->erp->MenuEintrag("index.php?module=kommissionierung&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=kommissionierung&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'kommissionierung_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "kommissionierung_list.tpl");
    }    

    public function kommissionierung_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `kommissionierung` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->kommissionierung_list();
    } 

    /*
     * Edit kommissionierung item
     * If id is empty, create a new one
     */
        
    function kommissionierung_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('kommissionierung',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=kommissionierung&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=kommissionierung&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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

    //        $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$input['projekt'],true); // Parameters: Target db?, value, from form?

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

            $sql = "INSERT INTO kommissionierung (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=kommissionierung&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
	    $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',k.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS k.id, $dropnbox, k.zeitstempel, k.bearbeiter, k.user, k.kommentar, k.abgeschlossen, k.improzess, k.bezeichnung, k.skipconfirmboxscan, k.id FROM kommissionierung k"." WHERE id=$id");        

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }

        if (!empty($result)) {
            $kommissionierung_from_db = $result[0];
        } else {
            return;
        }
             
        /*
         * Add displayed items later
         * 

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);         

        $this->app->YUI->AutoComplete("artikel", "artikelnummer");

         */

        $this->app->Tpl->Parse('PAGE', "kommissionierung_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');       
        $input['zeitstempel'] = $this->app->Secure->GetPOST('zeitstempel');
	    $input['bearbeiter'] = $this->app->Secure->GetPOST('bearbeiter');
	    $input['user'] = $this->app->Secure->GetPOST('user');
	    $input['kommentar'] = $this->app->Secure->GetPOST('kommentar');
	    $input['abgeschlossen'] = $this->app->Secure->GetPOST('abgeschlossen');
	    $input['improzess'] = $this->app->Secure->GetPOST('improzess');
	    $input['bezeichnung'] = $this->app->Secure->GetPOST('bezeichnung');
	    $input['skipconfirmboxscan'] = $this->app->Secure->GetPOST('skipconfirmboxscan');
        return $input;
    }

    public function kommissionierung_print() {
        $id = $this->app->Secure->GetGET('id');
        $Brief = new KommissionierungPDF($this->app, styleData: array('ohne_steuer' => true, 'artikeleinheit' => false, 'abstand_boxrechtsoben' => -70, 'abstand_artikeltabelleoben' => -70, 'abstand_betreffzeileoben' => -70, 'preise_ausblenden' => true, 'hintergrund' => 'none'));
        $Brief->GetKommissionierung($id);
        $Brief->displayDocument(false);
        exit();
    }


 }
