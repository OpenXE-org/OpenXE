<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php
class Dsgvo{
  var $app;


  static function TableSearch(&$app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case "dsgvo_auskunft_list":
      $allowed['dsgvo_auskunft'] = array('list');

      $heading = array('', 'Name', 'Kunde', 'Lieferant', 'Land', 'PLZ', 'Ort', 'E-Mail', 'L&ouml;schauftrag vom', 'Projekt', 'Men&uuml;');
      $width = array('1%', '20%', '10%', '10%', '10%', '10%', '15%', '15%', '10%', '15%', '1%');

      $findcols = array('open', 'a.name', 'a.kundennummer', 'a.lieferantennummer', 'a.land', 'a.plz', 'a.ort', 'a.email', "DATE_FORMAT(d.loeschauftrag_vom, '%d.%m.%Y')", 'p.abkuerzung', 'a.id');
      $searchsql = array('a.name', 'a.kundennummer', 'a.lieferantennummer', 'a.land', 'a.plz', 'a.ort', 'a.email', 'd.loeschauftrag_vom', 'd.kommentar', 'p.abkuerzung');

      $defaultorder = 1;
      $defaultorderdesc = 0;

      $moreinfo = true;
      $menucol = 10;

      $datecols = array(7);

      $menu = "<table cellpadding=0 cellspacing=0>";
        $menu .= "<tr>";
          $menu .= "<td nowrap>";
            $menu .= "<a href=\"index.php?module=dsgvo&action=pdf&id=%value%\">";
              $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
            $menu .= "</a>&nbsp;";
            $menu .= '<a href="javascript:;" onclick="DSGVOEdit(%value%);">';
              $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
            $menu .= "</a>";
          $menu .= "</td>";
        $menu .= "</tr>";
      $menu .= "</table>";

      $subwhere = "";

      $fnurloeschauftrag = $app->YUI->TableSearchFilter($name, 5, 'nurloeschauftrag', '0', 0, 'checkbox');

      if($fnurloeschauftrag == 1){
        $subwhere .= " AND (d.loeschauftrag_vom != '' OR d.loeschauftrag_vom != '0000-00-00') ";
      }else{
        $subwhere .= "";
      }

      $where = " a.id > 0 AND a.geloescht = 0 ".$subwhere;

      $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, '<img src=./themes/".$app->Conf->WFconf['defaulttheme']."/images/details_open.png class=details>' as open, a.name, a.kundennummer, a.lieferantennummer, a.land, a.plz, a.ort, a.email, IF(d.loeschauftrag_vom != '0000-00-00', DATE_FORMAT(d.loeschauftrag_vom, '%d.%m.%Y'), '-'), p.abkuerzung, a.id FROM adresse a LEFT JOIN projekt p ON a.projekt = p.id LEFT JOIN dsgvo_loeschauftrag d ON a.id = d.adresse";

      $count = "SELECT count(a.id) FROM adresse a WHERE $where";
      break;

    }

    $erg = false;

    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v))$erg[$v] = $$v;
    }
    return $erg;
  }


  function __construct($app, $intern = false) {
    $this->app=&$app;
    if($intern)return;
    $this->app->ActionHandlerInit($this);

    // ab hier alle Action Handler definieren die das Modul hat
    $this->app->ActionHandler("list", "DSGVOList");
    $this->app->ActionHandler("edit", "DSGVOEdit");
    $this->app->ActionHandler("save", "DSGVOSave");
    $this->app->ActionHandler("pdf", "DSGVOPDF");
    $this->app->ActionHandler("minidetail", "DSGVOMinidetail");
    
    $this->app->ActionHandlerListen($app);

  }


  function Install(){
    $this->app->erp->CheckTable("dsgvo_loeschauftrag");
    $this->app->erp->CheckColumn("id", "int(11)", "dsgvo_loeschauftrag", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("adresse", "int(11)", "dsgvo_loeschauftrag", "NOT NULL DEFAULT 0");
    $this->app->erp->CheckColumn("loeschauftrag_vom", "date", "dsgvo_loeschauftrag", "NOT NULL");
    $this->app->erp->CheckColumn("kommentar", "varchar(512)", "dsgvo_loeschauftrag", "NOT NULL");
  }


  function DSGVOMenu()
  {
    $this->app->erp->MenuEintrag("index.php?module=dsgvo&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=dsgvo&action=list","Details");
  }

  function DSGVOList()
  {
    $this->DSGVOMenu();
    $this->app->Tpl->Set("KURZUEBERSCHRIFT","DSGVO Auskunft");

    $this->app->YUI->DatePicker('e_loeschauftrag_vom');
    $this->app->YUI->TableSearch('TAB1','dsgvo_auskunft_list', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse("PAGE","dsgvo_auskunft_list.tpl");
  }

  function DSGVOEdit()
  {
    if($this->app->Secure->GetGET('cmd')=='get'){
      $id = (int)$this->app->Secure->GetPOST('id');
      
      $data = $this->app->DB->SelectArr("SELECT d.adresse, d.loeschauftrag_vom, d.kommentar FROM dsgvo_loeschauftrag d WHERE d.adresse = '$id' LIMIT 1");
      
      if($data){
        $data = reset($data);

        $data['id'] = $data['adresse'];

        if($data['loeschauftrag_vom'] == "0000-00-00"){
          $data['loeschauftrag_vom'] = "";
        }else{
          $data['loeschauftrag_vom'] = date('d.m.Y', strtotime($data['loeschauftrag_vom']));
        }

      }else{
        $data['id'] = 0;
        $data['loeschauftrag_vom'] = '';
        $data['kommentar'] = '';
      }
      echo json_encode($data);
      exit;
    }

    $this->app->Tpl->Parse('PAGE', "dsgvo_auskunft_list.tpl");
  }

  function DSGVOSave()
  { 
    $adresse = (int)trim($this->app->Secure->GetPOST('id'));
    $loeschauftrag_vom = trim($this->app->Secure->GetPOST('loeschauftrag_vom'));
    $kommentar = trim($this->app->Secure->GetPOST('kommentar'));

    $error = "";

    if($loeschauftrag_vom == ""){
      $error .= "Bitte Löschauftrag vom ausfüllen"."\n";
    }else{
      $loeschauftrag_vom =  date('Y-m-d', strtotime($loeschauftrag_vom)); 
    }

    if($adresse <= 0 || $adresse == ""){
      $error .= "Keine gültige Adresse gewählt"."\n";
    }else{
      $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE id = '$adresse' LIMIT 1");
      if($adresse != "" && $adresse > 0){
      }else{
        $error .= "Keine gültige Adresse gewählt"."\n";
      }
    }

    if($error == ""){
      $id = $this->app->DB->Select("SELECT id FROM dsgvo_loeschauftrag WHERE adresse = '$adresse' LIMIT 1");
      if($id){
        $this->app->DB->Update("UPDATE dsgvo_loeschauftrag SET loeschauftrag_vom = '$loeschauftrag_vom', kommentar = '$kommentar' WHERE id = '$id'");

        echo json_encode(array('status'=>1));
        exit;
      }else{
        $this->app->DB->Insert("INSERT INTO dsgvo_loeschauftrag (adresse, loeschauftrag_vom, kommentar) VALUES ('$adresse', '$loeschauftrag_vom', '$kommentar')");

        echo json_encode(array('status'=>1));
        exit;      
      } 
    }else{
      echo json_encode(array('status'=>0,'statusText'=>$error));
      exit;

    }
    
  }  

  function DSGVOPDF(){

    $id = $this->app->Secure->GetGET('id');
    
    $pdf=new SuperFPDF('P','mm','A4',$this->app);
    $pdf->AddPage();
  
    $pdf->SetDisplayMode("real","single");

    $pdf->SetMargins(15,50);
    $pdf->SetAutoPageBreak(true,40);
    $pdf->AliasNbPages('{nb}');
    

    // Bei Adressstammblatt immer oben beginnen
    $pdf->abstand_betreffzeileoben=0;
    $pdf->logofile = "";//$this->app->erp->GetTMP()."/".$this->app->Conf->WFdbname."_logo.jpg";
    $pdf->briefpapier="";


    $schrift =  $this->app->erp->Firmendaten('schriftgroesse');

    $pdf->SetFontSize($schrift);
    $pdf->SetFont('Arial','','10');
    $pdf->SetX($pdf->GetX()+160);
    $pdf->Cell(10,0,date("d.m.Y"),"","","L");


    $kunde= $this->app->DB->SelectArr("SELECT name,kundennummer,lieferantennummer FROM adresse WHERE id='$id' LIMIT 1");
    $kunde = reset($kunde);

    //$pdf->renderDoctype();
    $pdf->doctype="adresse";
    $pdf->doctypeOrig="Adresse: ".$kunde['name'];

    if($pdf->doctype=="brief")
      $betreffszeile  = $this->app->erp->Firmendaten('brieftext');
    else
      $betreffszeile  = $this->app->erp->Firmendaten('betreffszeile');

    $pdf->SetY(20);//+$this->abstand_artikeltabelleoben); //Hoehe Box
    //$this->SetY(80+$this->abstand_artikeltabelleoben); //Hoehe Box
    $pdf->SetFont('Arial','B',$betreffszeile);
    $pdf->SetY(40);//+$this->abstand_artikeltabelleoben); //Hoehe Box
    $pdf->SetY($pdf->GetY()+$pdf->abstand_betreffzeileoben);
    $pdf->Cell(85,6,$this->app->erp->ReadyForPDF($pdf->doctypeOrig));
    $pdf->SetY($pdf->GetY()-$pdf->abstand_betreffzeileoben);

    //$this->SetY($this->GetY()+$this->abstand_betreffzeileoben);
    $pdf->SetY($pdf->GetY()+$pdf->abstand_artikeltabelleoben); //Hoehe Box

    $pdf->SetY(30);//+$this->abstand_artikeltabelleoben); //Hoehe Box

    $dokumententext  = $this->app->erp->Firmendaten('dokumententext');
    $pdf->SetFont('Arial','',$dokumententext);
    $pdf->SetY(50);//+$this->abstand_artikeltabelleoben); //Hoehe Box*/





    $adresse = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='".$id."'");
    $adresse = reset($adresse);

    if($adresse['typ']=="firma")
    {
      $infofields[]=array("Firma",$adresse['name']);
      if($adresse['ansprechpartner']!="")
      $infofields[]=array("Ansprechpartner",$adresse['ansprechpartner']);
    } else {
      $infofields[]=array("Name",$adresse['name']);
    }

    $infofields[]=array("Anschrift",$adresse['land']."-".$adresse['plz']." ".$adresse['ort'].", ".$adresse['strasse'].", ".$adresse['adresszusatz']);

    $felder = array('telefon','telefax','mobil','email','web');
    foreach($felder as $feldname)
    {
      $infofields[]=array(ucfirst($feldname),$adresse[$feldname=='web'?'internetseite':$feldname]);
    }


    if($this->app->erp->Firmendaten("modul_mlm")==1)
    {
      $mlmvertragsbeginn = $this->app->DB->Select("SELECT DATE_FORMAT(mlmvertragsbeginn,'%d.%m.%Y') FROM adresse WHERE id='".$adresse['id']."' LIMIT 1");
      if($mlmvertragsbeginn=="00.00.0000") $mlmvertragsbeginn = "kein Vertragsbeginn eingestellt";
      $sponsorid = $this->app->DB->Select("SELECT sponsor FROM adresse WHERE id='".$adresse['id']."' LIMIT 1");
      if($sponsorid> 0)
        $sponsor = $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name) FROM adresse WHERE id='$sponsorid' LIMIT 1");
      else
        $sponsor = "Kein Sponsor vorhanden";

      

      $infofields[]=array("Sponsor",$sponsor);      
      $infofields[]=array("Vertragsbeginn am",$mlmvertragsbeginn);
    }

    
    $erfasstam = $this->app->DB->Select("SELECT DATE_FORMAT(zeitstempel,'%d.%m.%Y') FROM objekt_protokoll WHERE objekt='adresse' AND objektid='".$adresse['id']."'                         AND action_long='adresse_create' LIMIT 1");
    $infofields[]=array("Erfasst am",$erfasstam);


    $infofields[]=array("UST-ID",$adresse['ustid']);
    $infofields[]=array("Steuernummer",$adresse['steuernummer']);
    if(is_null($adresse['geburtstag']) || $adresse['geburtstag'] == "0000-00-00"){
      $infofields[]=array("Geburtstag","");
    }else{
      $infofields[]=array("Geburtstag",date('d.m.Y', strtotime($adresse['geburtstag'])));
    }
    $infofields[]=array("Bankname",$adresse['bank']);
    $infofields[]=array("Kontoinhaber",$adresse['inhaber']);
    $infofields[]=array("IBAN",$adresse['iban']);
    $infofields[]=array("BIC",$adresse['swift']);
    $infofields[]=array("Paypal Kontoinhaber",$adresse['paypalinhaber']);
    $infofields[]=array("Paypal Account",$adresse['paypal']);
    $infofields[]=array("Paypal Währung",$adresse['paypalwaehrung']);
    $infofields[]=array("Mandatsreferenz",$adresse['Mandatsreferenz']);
    if(is_null($adresse['mandatsreferenzdatum']) || $adresse['mandatsreferenzdatum'] == "0000-00-00"){
      $infofields[]=array("Mandatsreferenzdatum","");
    }else{
      $infofields[]=array("Mandatsreferenzdatum",date('d.m.Y', strtotime($adresse['mandatsreferenzdatum'])));
    }


    if($adresse['kundennummer']!="")
      $numbers[] = array("Kunden Nr.",$adresse['kundennummer']);  

    if($adresse['lieferantennummer']!="")
      $numbers[] = array("Lieferanten Nr.",$adresse['lieferantennummer']);  

    if($adresse['mitarbeiternummer']!="")
      $numbers[] = array("Mitarbeiter Nr.",$adresse['mitarbeiternummer']);  

    if(count($numbers)>0){
      //$pdf->renderInfoBox($numbers);
      $height = 5;
      for($i=0;$i<count($numbers);$i++)
      {
        $pdf->MultiCell(50,$height,$this->app->erp->ReadyForPDF($numbers[$i][0]).":","BTL",'L');
        $pdf->SetY($pdf->GetY()-$height); $pdf->SetX(50); $pdf->MultiCell(140,$height,$this->app->erp->ReadyForPDF($numbers[$i][1]),"BTR",'L');
      }


    }

    $pdf->Ln(5);
    //$pdf->renderHeading("Adressstammblatt",8);
    $height = 8;
    $betreffszeile  = $this->app->erp->Firmendaten('betreffszeile');

    $pdf->SetFont('Arial','B',$betreffszeile);
    $pdf->Cell(85,6,$this->app->erp->ReadyForPDF("Adressstammblatt"));
    $pdf->SetFont('Arial','',$betreffszeile);
    $pdf->Ln($height);



    //$pdf->renderInfoBox($infofields);
    $height = 5;
    for($i=0;$i<count($infofields);$i++)
    {
      $pdf->MultiCell(50,$height,$this->app->erp->ReadyForPDF($infofields[$i][0]).":","BTL",'L');
      $pdf->SetY($pdf->GetY()-$height); $pdf->SetX(50); $pdf->MultiCell(140,$height,$this->app->erp->ReadyForPDF($infofields[$i][1]),"BTR",'L');
    }


    $accounts_tmp = $this->app->DB->SelectArr("SELECT CONCAT('Bezeichnung: ', bezeichnung, ', ', 'Art: ', art, ', URL: ', url, ', Benutzername: ', benutzername) as 'value' FROM adresse_accounts WHERE adresse='".$adresse['id']."'");

    for($i=0;$i<count($accounts_tmp);$i++) $accounts[] = $accounts_tmp[$i]['value'];
    if(count($accounts) > 0){
      $pdf->Ln(5);
      $height = 8;
      $betreffszeile = $this->app->erp->Firmendaten('betreffszeile');

      $pdf->SetFont('Arial','B',$betreffszeile);
      $pdf->Cell(85,6,$this->app->erp->ReadyForPDF("Accounts"));
      $pdf->SetFont('Arial','',$betreffszeile);
      $pdf->Ln($height);
      

      $height = 5;
      if(is_array($accounts))
      {
        for($i=0;$i<count($accounts);$i++)
        {
          $pdf->MultiCell(175,$height,$pdf->WriteHTML($this->app->erp->ReadyForPDF($accounts[$i])),"",'L');
        }
      } else {
        $pdf->MultiCell(175,$height,$pdf->WriteHTML($this->app->erp->ReadyForPDF($accounts)),"BTLR",'L');
      }

    }


    
    $ansprechpartner_tmp = $this->app->DB->SelectArr("SELECT CONCAT(name,
      ', Titel: ',titel, ', ',if(bereich='','Bereich: -', CONCAT('Bereich: ', bereich)),
      ', Abteilung: ',abteilung,
      ', Unterabteilung: ',unterabteilung,
      ', Adresszusatz: ',adresszusatz,
      ', Straße: ',strasse,
      ', PLZ: ',plz,
      ', Ort: ',ort,
      ', Land: ',land,
      ', Telefon: ',telefon,
      ', Telefax: ',telefax,
      ', Mobil: ',mobil,
      ', E-Mail: ',email
      ) as 'value' FROM ansprechpartner WHERE adresse='".$adresse['id']."'");

    for($i=0;$i<count($ansprechpartner_tmp);$i++) $ansprechpartner[] = $ansprechpartner_tmp[$i]['value'];
    if(count($ansprechpartner) > 0)
    { 
      $pdf->Ln(5);
      //$pdf->renderHeading("Ansprechpartner",8);
      $height = 8;
      $betreffszeile  = $this->app->erp->Firmendaten('betreffszeile');

      $pdf->SetFont('Arial','B',$betreffszeile);
      $pdf->Cell(85,6,$this->app->erp->ReadyForPDF("Ansprechpartner"));
      $pdf->SetFont('Arial','',$betreffszeile);
      $pdf->Ln($height);


      //$pdf->renderInfoBoxSingle($ansprechpartner);
      $height = 5;
      if(is_array($ansprechpartner))
      {
        for($i=0;$i<count($ansprechpartner);$i++)
        {
          $pdf->MultiCell(175,$height,$pdf->WriteHTML($this->app->erp->ReadyForPDF($ansprechpartner[$i])),"",'L');
        }
      } else {
        $pdf->MultiCell(175,$height,$pdf->WriteHTML($this->app->erp->ReadyForPDF($ansprechpartner)),"BTLR",'L');
      }
    }


    $lieferadressen_tmp = $this->app->DB->SelectArr("SELECT name,abteilung,unterabteilung,adresszusatz,strasse,plz,ort,land,
      telefon,email
      FROM lieferadressen WHERE adresse='".$adresse['id']."' ORDER by standardlieferadresse DESC");

    for($i=0;$i<count($lieferadressen_tmp);$i++) {
      $lieferadressen_tmp[$i]['value']=""; 
      foreach($lieferadressen_tmp[$i] as $key=>$value)
      {
        switch($key)
        {
          case "email": 
            $lieferadressen_tmp[$i]['value'] .= "E-Mail: $value, ";
          break;
       
          case "telefon": 
            $lieferadressen_tmp[$i]['value'] .= "Telefon: $value, ";
          break;
          case "telefax": 
            $lieferadressen_tmp[$i]['value'] .= "Telefax: $value, ";
          break;

          default:
            if($value!="")
              $lieferadressen_tmp[$i]['value'] .= "$value, ";
        }
      }

      $lieferadressen_tmp[$i]['value'] = trim($lieferadressen_tmp[$i]['value'],', ');
      if($i==0) $standard = " (Standard)"; else $standard="";

      $lieferadressen[] = $lieferadressen_tmp[$i]['value'].$standard;
    }
   
    if(count($lieferadressen) > 0)
    { 
      $pdf->Ln(5);
      //$pdf->renderHeading("Lieferadressen",8);
      $height = 8;
      $betreffszeile  = $this->app->erp->Firmendaten('betreffszeile');

      $pdf->SetFont('Arial','B',$betreffszeile);
      $pdf->Cell(85,6,$this->app->erp->ReadyForPDF("Lieferadressen"));
      $pdf->SetFont('Arial','',$betreffszeile);
      $pdf->Ln($height);

      //$pdf->renderInfoBoxSingle($lieferadressen);
      $height = 8;
      if(is_array($lieferadressen))
      {
        for($i=0;$i<count($lieferadressen);$i++)
        {
          $pdf->MultiCell(175,$height,$pdf->WriteHTML($this->app->erp->ReadyForPDF($lieferadressen[$i])),"",'L');
        }
      } else {
        $pdf->MultiCell(175,$height,$pdf->WriteHTML($this->app->erp->ReadyForPDF($lieferadressen)),"BTLR",'L');
      }


    }


    $pdf->Ln(5);

    if($adresse['sonstiges']!="")
    {
      //$pdf->renderHeading("Sonstiges",8);
      $height = 8;
      $betreffszeile  = $this->app->erp->Firmendaten('betreffszeile');

      $pdf->SetFont('Arial','B',$betreffszeile);
      $pdf->Cell(85,6,$this->app->erp->ReadyForPDF($heading));
      $pdf->SetFont('Arial','',$betreffszeile);
      $pdf->Ln($height);
      //$this->SetFont($this->GetFont(),'',7);
      $pdf->MultiCell(180,4,$pdf->WriteHTML($adresse['sonstiges']));
    }

    //$pdf->renderFooter();

    $pdf->Output(date('Ymd')."_"."DSGVO_Auskunft".".pdf",'D');

  }


  function DSGVOMinidetail(){
    $id = $this->app->Secure->GetGET('id');

    $table = new EasyTable($this->app);

    $table->Query("SELECT kommentar FROM dsgvo_loeschauftrag WHERE adresse = '$id' LIMIT 1");
    
    $table->DisplayNew("TABELLE","Grund","noAction");

    $this->app->Tpl->Output("dsgvo_minidetail.tpl");
    exit;
  }




}
