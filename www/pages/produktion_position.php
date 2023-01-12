<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Produktion_position {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "produktion_position_list");        
        $this->app->ActionHandler("create", "produktion_position_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "produktion_position_edit");
        $this->app->ActionHandler("delete", "produktion_position_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "produktion_position_list":
                $allowed['produktion_position_list'] = array('list');
                $heading = array('','','produktion', 'artikel', 'projekt', 'bezeichnung', 'beschreibung', 'internerkommentar', 'nummer', 'menge', 'preis', 'waehrung', 'lieferdatum', 'vpe', 'sort', 'status', 'umsatzsteuer', 'bemerkung', 'geliefert', 'geliefert_menge', 'explodiert', 'explodiert_parent', 'logdatei', 'nachbestelltexternereinkauf', 'beistellung', 'externeproduktion', 'einheit', 'steuersatz', 'steuertext', 'erloese', 'erloesefestschreiben', 'freifeld1', 'freifeld2', 'freifeld3', 'freifeld4', 'freifeld5', 'freifeld6', 'freifeld7', 'freifeld8', 'freifeld9', 'freifeld10', 'freifeld11', 'freifeld12', 'freifeld13', 'freifeld14', 'freifeld15', 'freifeld16', 'freifeld17', 'freifeld18', 'freifeld19', 'freifeld20', 'freifeld21', 'freifeld22', 'freifeld23', 'freifeld24', 'freifeld25', 'freifeld26', 'freifeld27', 'freifeld28', 'freifeld29', 'freifeld30', 'freifeld31', 'freifeld32', 'freifeld33', 'freifeld34', 'freifeld35', 'freifeld36', 'freifeld37', 'freifeld38', 'freifeld39', 'freifeld40', 'stuecklistestufe', 'teilprojekt', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                $findcols = array('p.produktion', 'p.artikel', 'p.projekt', 'p.bezeichnung', 'p.beschreibung', 'p.internerkommentar', 'p.nummer', 'p.menge', 'p.preis', 'p.waehrung', 'p.lieferdatum', 'p.vpe', 'p.sort', 'p.status', 'p.umsatzsteuer', 'p.bemerkung', 'p.geliefert', 'p.geliefert_menge', 'p.explodiert', 'p.explodiert_parent', 'p.logdatei', 'p.nachbestelltexternereinkauf', 'p.beistellung', 'p.externeproduktion', 'p.einheit', 'p.steuersatz', 'p.steuertext', 'p.erloese', 'p.erloesefestschreiben', 'p.freifeld1', 'p.freifeld2', 'p.freifeld3', 'p.freifeld4', 'p.freifeld5', 'p.freifeld6', 'p.freifeld7', 'p.freifeld8', 'p.freifeld9', 'p.freifeld10', 'p.freifeld11', 'p.freifeld12', 'p.freifeld13', 'p.freifeld14', 'p.freifeld15', 'p.freifeld16', 'p.freifeld17', 'p.freifeld18', 'p.freifeld19', 'p.freifeld20', 'p.freifeld21', 'p.freifeld22', 'p.freifeld23', 'p.freifeld24', 'p.freifeld25', 'p.freifeld26', 'p.freifeld27', 'p.freifeld28', 'p.freifeld29', 'p.freifeld30', 'p.freifeld31', 'p.freifeld32', 'p.freifeld33', 'p.freifeld34', 'p.freifeld35', 'p.freifeld36', 'p.freifeld37', 'p.freifeld38', 'p.freifeld39', 'p.freifeld40', 'p.stuecklistestufe', 'p.teilprojekt');
                $searchsql = array('p.produktion', 'p.artikel', 'p.projekt', 'p.bezeichnung', 'p.beschreibung', 'p.internerkommentar', 'p.nummer', 'p.menge', 'p.preis', 'p.waehrung', 'p.lieferdatum', 'p.vpe', 'p.sort', 'p.status', 'p.umsatzsteuer', 'p.bemerkung', 'p.geliefert', 'p.geliefert_menge', 'p.explodiert', 'p.explodiert_parent', 'p.logdatei', 'p.nachbestelltexternereinkauf', 'p.beistellung', 'p.externeproduktion', 'p.einheit', 'p.steuersatz', 'p.steuertext', 'p.erloese', 'p.erloesefestschreiben', 'p.freifeld1', 'p.freifeld2', 'p.freifeld3', 'p.freifeld4', 'p.freifeld5', 'p.freifeld6', 'p.freifeld7', 'p.freifeld8', 'p.freifeld9', 'p.freifeld10', 'p.freifeld11', 'p.freifeld12', 'p.freifeld13', 'p.freifeld14', 'p.freifeld15', 'p.freifeld16', 'p.freifeld17', 'p.freifeld18', 'p.freifeld19', 'p.freifeld20', 'p.freifeld21', 'p.freifeld22', 'p.freifeld23', 'p.freifeld24', 'p.freifeld25', 'p.freifeld26', 'p.freifeld27', 'p.freifeld28', 'p.freifeld29', 'p.freifeld30', 'p.freifeld31', 'p.freifeld32', 'p.freifeld33', 'p.freifeld34', 'p.freifeld35', 'p.freifeld36', 'p.freifeld37', 'p.freifeld38', 'p.freifeld39', 'p.freifeld40', 'p.stuecklistestufe', 'p.teilprojekt');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',p.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=produktion_position&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=produktion_position&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS p.id, $dropnbox, p.produktion, p.artikel, p.projekt, p.bezeichnung, p.beschreibung, p.internerkommentar, p.nummer, p.menge, p.preis, p.waehrung, p.lieferdatum, p.vpe, p.sort, p.status, p.umsatzsteuer, p.bemerkung, p.geliefert, p.geliefert_menge, p.explodiert, p.explodiert_parent, p.logdatei, p.nachbestelltexternereinkauf, p.beistellung, p.externeproduktion, p.einheit, p.steuersatz, p.steuertext, p.erloese, p.erloesefestschreiben, p.freifeld1, p.freifeld2, p.freifeld3, p.freifeld4, p.freifeld5, p.freifeld6, p.freifeld7, p.freifeld8, p.freifeld9, p.freifeld10, p.freifeld11, p.freifeld12, p.freifeld13, p.freifeld14, p.freifeld15, p.freifeld16, p.freifeld17, p.freifeld18, p.freifeld19, p.freifeld20, p.freifeld21, p.freifeld22, p.freifeld23, p.freifeld24, p.freifeld25, p.freifeld26, p.freifeld27, p.freifeld28, p.freifeld29, p.freifeld30, p.freifeld31, p.freifeld32, p.freifeld33, p.freifeld34, p.freifeld35, p.freifeld36, p.freifeld37, p.freifeld38, p.freifeld39, p.freifeld40, p.stuecklistestufe, p.teilprojekt, p.id FROM produktion_position p";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM produktion_position WHERE $where";
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
    
    function produktion_position_list() {
/*        $this->app->erp->MenuEintrag("index.php?module=produktion_position&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=produktion_position&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'produktion_position_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "produktion_position_list.tpl");*/

        header("Location: index.php?module=produktion&action=list");
    }    


    // End edit process and return to previous page
    // Give pid in case of delete because position.id is already gone
    function produktion_position_edit_end(string $msg, bool $error, bool $go_to_production, int $pid = 0) {

        if ($error) {
            $msg = $this->app->erp->base64_url_encode("<div class=\"error\">".$msg."</div>");        
        } else {
            $msg = $this->app->erp->base64_url_encode("<div class=\"info\">".$msg."</div>");        
        }

        if ($go_to_production) {
            if ($pid == 0) {
                $id = (int) $this->app->Secure->GetGET('id');
                $sql = "SELECT p.status, p.id from produktion p INNER JOIN produktion_position pp ON pp.produktion = p.id WHERE pp.id = $id";
                $result = $this->app->DB->SelectArr($sql)[0];
                $pid = $result['id'];
            } 
            header("Location: index.php?module=produktion&action=edit&id=$pid&msg=$msg#tabs-3");
        } else {
            header("Location: index.php?module=produktion_position&action=list&msg=$msg");
        }
        exit();
    }    


    public function produktion_position_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $sql = "SELECT p.status, p.id from produktion p INNER JOIN produktion_position pp ON pp.produktion = p.id WHERE pp.id = $id";
        $result = $this->app->DB->SelectArr($sql)[0];
        $status = $result['status'];
        $pid = $result['id'];
        if (!in_array($status,array('angelegt','freigegeben'))) {
            $this->produktion_position_edit_end("Bearbeiten nicht möglich, Produktionsstatus ist '$status'",true, true);
        }

        $this->app->DB->Delete("DELETE FROM `produktion_position` WHERE `id` = '{$id}'");        

        // Remove reserved items

        $this->produktion_position_edit_end("Der Eintrag wurde gel&ouml;scht.", true, true, $pid);
    } 

    /*
    * Edit produktion_position item
    * If id is empty, create a new one
    */
        
    function produktion_position_edit() {
        $id = $this->app->Secure->GetGET('id');
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=produktion_position&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=produktion&action=edit%id=$pid", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');
                
        if (empty($id)) {
            // New item
            $id = 'NULL';
            $produktion_id = $this->app->Secure->GetGET('produktion');
            $sql = "SELECT p.status from produktion p WHERE p.id = $produktion_id";
            $result = $this->app->DB->SelectArr($sql)[0];
            $status = $result['status'];
        } else {
            $sql = "SELECT p.status, p.id from produktion p INNER JOIN produktion_position pp ON pp.produktion = p.id WHERE pp.id = $id";
            $result = $this->app->DB->SelectArr($sql)[0];
            $status = $result['status'];
            $produktion_id = $result['id'];
        }

        $input['produktion'] = $produktion_id;

        $sql = "SELECT FORMAT(menge,0) as menge FROM produktion_position WHERE produktion = $produktion_id AND stuecklistestufe = 1";
        $result = $this->app->DB->SelectArr($sql)[0];
        $planmenge = $result['menge'];

        if ($planmenge == 0) {
            $this->produktion_position_edit_end("Keine Planung vorhanden.",true, true, $produktion_id);
        }

        if ($submit != '')
        {

            // Write to database
            
            // Add checks here
            $input['artikel'] = $this->app->erp->ReplaceArtikel(true, $input['artikel'],true); // Convert from form to db

            // Only allowed when produktion is 'freigegeben or angelegt'
            if (!in_array($status,array('angelegt','freigegeben'))) {
                $this->produktion_position_edit_end("Bearbeiten nicht möglich, Produktionsstatus ist '$status'",true, true);
            }

            if ($input['menge'] < 0) {
                $this->produktion_position_edit_end("Ung&uuml;ltige Menge.",true, true);
            }

            // Only allow quantities that are a multiple of the target quantity
            if ($input['menge'] % $planmenge != 0) {
                $this->produktion_position_edit_end("Positionsmenge muss Vielfaches von $planmenge sein.",true, true, $produktion_id);
            }

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

            $sql = "INSERT INTO produktion_position (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = "Das Element wurde erfolgreich angelegt.";
            } else {
                $msg = "Die Einstellungen wurden erfolgreich &uuml;bernommen.";
            }
            $this->produktion_position_edit_end($msg,false,true,$produktion_id);

        }

    
        // Load values again from database
  
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS pp.id, pp.produktion, p.belegnr, pp.artikel, FORMAT(pp.menge,0) as menge, pp.id FROM produktion_position pp INNER JOIN produktion p ON pp.produktion = p.id "." WHERE pp.id=$id");

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

        $this->app->YUI->AutoComplete("artikel", "artikelnummer");
        //$this->app->YUI->AutoComplete("artikel", "lagerartikelnummer");
        $this->app->Tpl->Set('ARTIKEL',$this->app->erp->ReplaceArtikel(false, $result[0]['artikel'], false)); // Convert from form to db

        $this->app->Tpl->Set('PRODUKTIONID',$result[0]['produktion']);
        $this->app->Tpl->Set('PRODUKTIONBELEGNR',$result[0]['belegnr']);        

        $this->app->Tpl->Add('MESSAGE',"<div class=\"info\">Positionsmenge muss Vielfaches von $planmenge sein.</div>");

        $this->app->Tpl->Parse('PAGE', "produktion_position_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        
    	$input['artikel'] = $this->app->Secure->GetPOST('artikel');
    	$input['menge'] = $this->app->Secure->GetPOST('menge');

        return($input);	
    }

}
