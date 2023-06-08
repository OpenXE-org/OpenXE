<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Mahnwesen {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "mahnwesen_list");        
        $this->app->ActionHandler("create", "mahnwesen_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "mahnwesen_edit");
        $this->app->ActionHandler("delete", "mahnwesen_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    public function TableSearch($app, $name, $erlaubtevars) {
        switch ($name) {
            case "mahnwesen_list":

                $extended_mysql55 = ",'de_DE'";

                $allowed['mahnwesen_list'] = array('list');
                $heading = array('', '', 'Rechnung', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Zahlung', 'Betrag (brutto)', 'W&auml;hrung', 'Zahlstatus', 'Differenz', 'Status','Mahnstufe','Mahn-Datum','Gemahnt','Sperre','Interne Bemerkung','Men&uuml;');
                $width = array('1%','1%','01%',      '01%', '01%',    '05%',   '01%',  '01%',     '01%',     '01%',             '01%',          '01%',        '01%',       '01%',   '01%',      '01%',       '01%',     '01%',   '20%',              '1%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('r.id','r.id','r.belegnr', $app->erp->FormatDateShort('r.datum'), 'r.kundennummer','r.name', 'r.land','p.abkuerzung','r.zahlungsweise','r.soll','r.waehrung','r.zahlungsstatus','r.soll','r.status','r.mahnwesen');
                $searchsql = array('belegnr', 'kunde', 'datum');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		        $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',r.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                    r.id,
                    $dropnbox,                    
                    r.belegnr, 
                    ".$app->erp->FormatDateShort('r.datum')." as vom,
                    if(r.kundennummer <> '',r.kundennummer,adr.kundennummer),
                    CONCAT(" . $app->erp->MarkerUseredit("r.name", "r.useredittimestamp") . ", if(r.internebezeichnung!='',CONCAT('<br><i style=color:#999>',r.internebezeichnung,'</i>'),'')) as kunde,
                    r.land as land,
                    p.abkuerzung as projekt,
                    r.zahlungsweise as zahlungsweise,
                    FORMAT(r.soll,2{$extended_mysql55} ) as soll,
                    ifnull(r.waehrung,'EUR'),
                    r.zahlungsstatus as zahlung, 
                    if(r.soll-r.ist!=0 AND r.ist > 0,FORMAT(r.ist-r.soll,2{$extended_mysql55}),FORMAT((r.soll-r.ist)*-1,2{$extended_mysql55})) as fehlt,
                    if(r.status = 'storniert' AND r.teilstorno = 1,'TEILSTORNO',UPPER(r.status))  as status,
                    r.mahnwesen,
                    ".$app->erp->FormatDateShort('mahnwesen_datum')." as datum,
                    if(r.versendet_mahnwesen,'Ja',''),
                    if(r.mahnwesen_gesperrt,'Ja',''),
                    REPLACE(r.mahnwesen_internebemerkung,'\r\n','<br> '),
                    r.id
                    FROM rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id LEFT JOIN auftrag au ON au.id = r.auftragid ";

                $where = " r.belegnr <> ''";

                // Toggle filters
                $this->app->Tpl->Add('JQUERYREADY', "$('#inkl_bezahlte').click( function() { fnFilterColumn1( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#inkl_gesperrte').click( function() { fnFilterColumn2( 0 ); } );");

                for ($r = 1;$r <= 2;$r++) {
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


                $more_data1 = $app->Secure->GetGET("more_data1");
                if ($more_data1 == 1) {
                } else {
                    $where .= " AND r.zahlungsstatus <> 'bezahlt' ";
                }

                $more_data2 = $app->Secure->GetGET("more_data2");
                if ($more_data2 == 1) {
                }
                else {
                    $where .= " AND NOT r.mahnwesen_gesperrt ";
                }                
                // END Toggle filters


                $count = "SELECT count(DISTINCT id) FROM rechnung r WHERE $where";
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
    
    function mahnwesen_list() {
        $this->app->erp->MenuEintrag("index.php?module=mahnwesen&action=list", "&Uuml;bersicht");
//        $this->app->erp->MenuEintrag("index.php?module=mahnwesen&action=create", "Neu anlegen");
        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        if($this->app->Secure->GetPOST('zahlungsstatus_berechnen') && $this->app->erp->RechteVorhanden('rechnung', 'edit')) {
            $this->app->erp->rechnung_zahlstatus_berechnen();   
        }

        if($this->app->Secure->GetPOST('sel_aktion') && $this->app->erp->RechteVorhanden('rechnung', 'edit'))
        {
          $drucker = $this->app->Secure->GetPOST('seldrucker');
          $aktion = $this->app->Secure->GetPOST('sel_aktion');
          $auswahl = $this->app->Secure->GetPOST('auswahl');
          if($drucker > 0) {
            $this->app->erp->BriefpapierHintergrundDisable($drucker);
          }
          if(is_array($auswahl)) {
            foreach($auswahl as $auswahlKey => $auswahlValue) {
              if((int)$auswahlValue > 0) {
                $auswahl[$auswahlKey] = (int)$auswahlValue;
              }
              else {
                unset($auswahl[$auswahlKey]);
              }
            }
            switch($aktion)
            {
              case 'bezahlt':
                $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='bezahlt', bezahlt_am = now(), mahnwesenfestsetzen='1',mahnwesen_internebemerkung=CONCAT(mahnwesen_internebemerkung,'\r\n','Manuell als bezahlt markiert am ".date('d.m.Y')."')  WHERE id IN (".implode(', ',$auswahl).')');
              break;
              case 'offen':
                $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='offen',bezahlt_am = NULL, mahnwesen_internebemerkung=CONCAT(mahnwesen_internebemerkung,'\r\n','Manuell als bezahlt entfernt am ".date('d.m.Y')."') WHERE id IN (".implode(', ',$auswahl).')');
              break;
              case 'mail':
                $auswahl = $this->app->DB->SelectFirstCols(
                  sprintf(
                    "SELECT id FROM rechnung WHERE belegnr <> '' AND id IN (%s)",
                    implode(', ', $auswahl)
                  )
                );
                foreach($auswahl as $v) {
                  if(!$v) {
                    continue;
                  }
                  $checkpapier = $this->app->DB->Select(
                    "SELECT a.rechnung_papier FROM rechnung AS r 
                    LEFT JOIN adresse AS a ON r.adresse=a.id 
                    WHERE r.id='$v' 
                    LIMIT 1"
                  );
                  if($checkpapier!=1 &&
                    $this->app->DB->Select(
                      "SELECT r.id 
                      FROM rechnung AS r 
                      INNER JOIN adresse AS a ON r.adresse = a.id 
                      WHERE r.id = '$v' AND r.email <> '' OR a.email <> '' 
                      LIMIT 1"
                    )
                  ) {
                    $this->app->erp->PDFArchivieren('rechnung', $v, true);
                    $this->app->erp->Rechnungsmail($v);
                  }
                  else if($checkpapier && $drucker) {
                    $this->app->erp->PDFArchivieren('rechnung', $v, true);
                    $projekt = $this->app->DB->Select(
                      "SELECT projekt FROM rechnung WHERE id='$v' LIMIT 1"
                    );
                    if(class_exists('RechnungPDFCustom')) {
                      $Brief = new RechnungPDFCustom($this->app,$projekt);
                    }
                    else {
                      $Brief = new RechnungPDF($this->app,$projekt);
                    }
                    $Brief->GetRechnung($v);
                    $tmpfile = $Brief->displayTMP();
                    $Brief->ArchiviereDocument();
                    $this->app->printer->Drucken($drucker,$tmpfile);
                    unlink($tmpfile);
                  }
                }
              break;
              case 'versendet':
                $auswahl = $this->app->DB->SelectFirstCols(
                  sprintf(
                    "SELECT id FROM rechnung WHERE belegnr <> '' AND id IN (%s)",
                    implode(', ', $auswahl)
                  )
                );
                foreach($auswahl as $v) {
                  if($v) {
                    $reArr = $this->app->DB->SelectRow(
                      sprintf(
                        "SELECT projekt,belegnr,status,usereditid,
                        DATE_SUB(NOW(), INTERVAL 30 SECOND) < useredittimestamp AS `open` 
                        FROM rechnung WHERE id=%d LIMIT 1",
                        $v
                      )
                    );
                    if($reArr['belegnr'] === '' || ($reArr['open'] && $reArr['status'] === 'freigegeben')) {
                      continue;
                    }
                    $this->markInvoiceAsClosed($v);
                  }
                }
              break;
              case 'drucken':
                if($drucker) {
                  $auswahl = $this->app->DB->SelectFirstCols(
                    sprintf(
                      "SELECT id FROM rechnung WHERE belegnr <> '' AND id IN (%s)",
                      implode(', ', $auswahl)
                    )
                  );
                  foreach($auswahl as $v) {
                    $reArr = $this->app->DB->SelectRow(
                      sprintf(
                        "SELECT projekt,belegnr,status,usereditid,adresse,
                        DATE_SUB(NOW(), INTERVAL 30 SECOND) < useredittimestamp AS `open` 
                        FROM rechnung WHERE id=%d LIMIT 1",
                        $v
                      )
                    );
                    if($reArr['belegnr'] === '' || ($reArr['open'] && $reArr['status'] === 'freigegeben')) {
                      continue;
                    }
                    if($reArr['status'] === 'freigegeben') {
                      $this->app->erp->RechnungNeuberechnen($v);
                    }
                    $projekt = $reArr['projekt'];//$this->app->DB->Select("SELECT projekt FROM rechnung WHERE id='$v' LIMIT 1");
                    $this->app->erp->RechnungProtokoll($v,'Rechnung gedruckt');
                    $this->app->DB->Update("UPDATE rechnung SET schreibschutz=1, versendet = 1  WHERE id = '$v' LIMIT 1");
                    $this->app->DB->Update("UPDATE rechnung SET status='versendet' WHERE id = '$v' AND status!='storniert' LIMIT 1");
                    $this->app->erp->PDFArchivieren('rechnung', $v, true);
                    if(class_exists('RechnungPDFCustom')) {
                      $Brief = new RechnungPDFCustom($this->app,$projekt);
                    }
                    else{
                      $Brief = new RechnungPDF($this->app,$projekt);
                    }
                    $Brief->GetRechnung($v);
                    $tmpfile = $Brief->displayTMP();
                    $Brief->ArchiviereDocument();
                    $this->app->printer->Drucken($drucker,$tmpfile);
                    $doctype = 'rechnung';
                    $this->app->erp->RunHook('dokumentsend_ende', 5, $doctype, $v, $projekt, $reArr['adresse'], $aktion);
                    @unlink($tmpfile);
                  }
                }
              break;
              case 'pdf':
                $tmpfile = [];
                foreach($auswahl as $v) {
                  $projekt = $this->app->DB->Select("SELECT projekt FROM rechnung WHERE id=$v LIMIT 1");
                  if(class_exists('RechnungPDFCustom')) {
                    $Brief = new RechnungPDFCustom($this->app,$projekt);
                  }
                  else {
                    $Brief = new RechnungPDF($this->app,$projekt);
                  }
                  $Brief->GetRechnung($v);
                  $tmpfile[] = $Brief->displayTMP();
                }

                if((!empty($tmpfile)?count($tmpfile):0) > 0) {
                  try {
                    /** @var PdfMerger $pdfMerger */
                    $pdfMerger = $this->app->Container->get('PdfMerger');
                    $mergeOutputPath = realpath($this->app->erp->GetTMP()) . '/' . uniqid('sammelpdf_', true) . '.pdf';
                    $pdfMerger->merge($tmpfile, $mergeOutputPath);

                    foreach($tmpfile as $key=>$value) {
                      unlink($value);
                    }

                    header('Content-type:application/pdf');
                    header('Content-Disposition: attachment;filename='.md5(microtime(true)).'.pdf');
                    readfile($mergeOutputPath);
                    $this->app->ExitXentral();
                  } catch (PdfComponentExceptionInterface $exception) {
                    echo 'Fehler beim Generieren der Sammelpdf: ' . htmlspecialchars($exception->getMessage());
                    $this->app->ExitXentral();
                  }
                }
              break;
            }
          }      
        } // ende ausfuehren

        if($this->app->erp->RechteVorhanden('rechnung', 'manuellbezahltmarkiert')){
            $this->app->Tpl->Set('ALSBEZAHLTMARKIEREN', '<option value="bezahlt">{|als bezahlt markieren|}</option>');
        }

        $this->app->Tpl->Set('SELDRUCKER', $this->app->erp->GetSelectDrucker($this->app->User->GetParameter('rechnung_list_drucker')));

        $this->app->YUI->TableSearch('TAB1', 'mahnwesen_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "mahnwesen_list.tpl");
    }    

    public function mahnwesen_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `mahnwesen` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->mahnwesen_list();
    } 

    /*
     * Edit mahnwesen item
     * If id is empty, create a new one
     */
        
    function mahnwesen_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=mahnwesen&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=mahnwesen&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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

            $sql = "INSERT INTO mahnwesen (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=mahnwesen&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',m.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS m.id, $dropnbox, m.name, m.rest, m.test, m.id FROM mahnwesen m"." WHERE id=$id");

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
        $this->app->Tpl->Parse('PAGE', "mahnwesen_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['name'] = $this->app->Secure->GetPOST('name');
	$input['rest'] = $this->app->Secure->GetPOST('rest');
	$input['test'] = $this->app->Secure->GetPOST('test');
	

        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('NAME', $input['name']);
	$this->app->Tpl->Set('REST', $input['rest']);
	$this->app->Tpl->Set('TEST', $input['test']);
	
    }

}
