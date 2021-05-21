<?php
include '_gen/widget.gen.artikel.php';

class WidgetArtikel extends WidgetGenArtikel 
{
  /** @var Application $app */
  private $app;

  /**
   * WidgetArtikel constructor.
   *
   * @param Application $app
   * @param string      $parsetarget
   */
  public function __construct($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::__construct($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
    //$this->app->YUI->AutoComplete(STANDARDLAGERAUTO,"lager_platz",array('kurzbezeichnung'),"kurzbezeichnung");
    //$this->app->YUI->AutoComplete(PROJEKTAUTO,"projekt",array('name','abkuerzung'),"abkuerzung");
    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->app->YUI->AutoComplete("adresse","lieferant");
    $this->app->YUI->AutoComplete("typ","artikelgruppe");
    $this->app->YUI->AutoComplete("shop","shopname");
    $this->app->YUI->AutoComplete("shop2","shopname");
    $this->app->YUI->AutoComplete("shop3","shopname");
    $this->app->YUI->AutoComplete("variante_von","artikelnummer");
    $this->app->YUI->AutoComplete("hersteller","hersteller");
    $this->app->YUI->AutoComplete("einheit","artikeleinheit");
    $this->app->YUI->AutoComplete("herstellerlink","herstellerlink");
    $this->app->YUI->AutoComplete("lager_platz","lagerplatz");
    $this->app->YUI->AutoComplete("zolltarifnummer","zolltarifnummer",1);
    $this->app->YUI->AutoComplete("bestandalternativartikel","artikelnummer");
    $this->app->YUI->AutoComplete("steuergruppe","steuergruppe");
    $this->app->YUI->AutoComplete("kostenstelle","kostenstelle",1);
    $this->app->YUI->AutoComplete("steuersatz","steuersatz",1);
    $this->app->YUI->AutoComplete("preproduced_partlist","lagerartikelnummer");
    $this->form->ReplaceFunction("adresse",$this,"ReplaceLieferant");
    $this->form->ReplaceFunction("steuergruppe",$this,"ReplaceSteuergruppe");
    $this->form->ReplaceFunction("gueltigbis",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("lager_platz",$this,"ReplaceLagerplatz");
    $this->form->ReplaceFunction("shop",$this,"ReplaceShopname");
    $this->form->ReplaceFunction("shop2",$this,"ReplaceShopname");
    $this->form->ReplaceFunction("shop3",$this,"ReplaceShopname");
    $this->form->ReplaceFunction("variante_von",$this,"ReplaceArtikel");
    $this->form->ReplaceFunction("preproduced_partlist",$this,"ReplaceArtikel");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->form->ReplaceFunction("pseudopreis",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("gewicht",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("steuersatz_erloese_normal",$this,"ReplaceSteuersatz");
    $this->form->ReplaceFunction("steuersatz_erloese_ermaessigt",$this,"ReplaceSteuersatz");
    $this->form->ReplaceFunction("steuersatz_erloese_innergemeinschaftlich",$this,"ReplaceSteuersatz");
    $this->form->ReplaceFunction("steuersatz_erloese_euermaessigt",$this,"ReplaceSteuersatz");
    $this->form->ReplaceFunction("steuersatz_erloese_export",$this,"ReplaceSteuersatz");
    $this->form->ReplaceFunction("bestandalternativartikel",$this,"ReplaceArtikel");
    $this->form->ReplaceFunction("pseudopreis",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("breite",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("hoehe",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("laenge",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("inventurek",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("berechneterek",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("nummer",$this,"ReplaceTrim");
    $this->form->ReplaceFunction("ean",$this,"ReplaceTrim");
    $this->form->ReplaceFunction("name_de",$this,"ReplaceTrim");
    $this->form->ReplaceFunction("steuersatz",$this,"ReplaceSteuersatz");
    $this->app->Tpl->Set('GEWICHTBEZEICHNUNG', $this->app->erp->GetGewichtbezeichnung());
    
    $this->app->YUI->CkEditor("uebersicht_de", "internal");
    $this->app->YUI->CkEditor("uebersicht_en", "internal");

    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("anabregs_text","belege");
      $this->app->YUI->CkEditor("anabregs_text_en","belege");
    }

    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->RunHook('article_widget',1, $id);
    $action = $this->app->Secure->GetGET("action");    
    $nummer = $this->app->Secure->GetPOST("nummer"); 
    $submit = $this->app->Secure->GetPOST("speichern"); 

    $projekt = $this->app->Secure->GetPOST("projekt"); 
    $projekttmp = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$id' LIMIT 1");
     
    // versuchen standardprojekt zu kriegen beim anlegen 
    if($action==='create')
    {    
      if($projekttmp <=0)
        $projekttmp = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' AND abkuerzung!='' LIMIT 1");
      if($projekttmp <=0)
        $projekttmp = $this->app->erp->GetCreateProjekt(); 
    } 

    $_artikelart = $this->app->erp->GetArtikelgruppe($projekttmp);

    if($id){

      $standardbild = $this->app->erp->GetArtikelStandardbild($id,true);

      if($standardbild > 0){
        //$this->app->Tpl->Set('ARTIKELBILD', "<img src=\"index.php?module=dateien&action=send&id=$standardbild\" align=\"left\" style=\"width: 200px; margin-right:10px; margin-bottom:10px;\">");
        $this->app->Tpl->Set('ARTIKELBILD',
          '<img alt="Artikelbild" src="index.php?module=artikel&action=thumbnail&id='.$id.'&fileid='.$standardbild.'&size=200&direkt=1" align="left" width="200" style="margin-right:10px; margin-bottom:10px;" />'
        );
      }
    
      $standardlieferant = $this->app->DB->Select("SELECT adresse FROM artikel WHERE id='$id' LIMIT 1");
      $hinweistextlieferant = $this->app->DB->Select("SELECT hinweistextlieferant FROM adresse WHERE id='$standardlieferant' LIMIT 1");
      if($hinweistextlieferant!='')
      {
        if($standardbild <=0) 
        {
          $this->app->Tpl->Set('ARTIKELBILD', "<img src=\"index.php?module=artikel&action=thumbnail&id=1&bildvorschau=KEINBILD\" align=\"left\" style=\"width: 200px; margin-right:10px; margin-bottom:10px;\">");
        }


        $this->app->YUI->CkEditor("readonlybox","none");
        $this->app->Tpl->Set('INFOFUERAUFTRAGSERFASSUNG',"<fieldset><legend>Info von Lieferant</legend>
              <textarea id=\"readonlybox\" rows>$hinweistextlieferant</textarea></fieldset>");
      }


//      $this->app->Tpl->Add('ARTIKELBILD',"<img src=\"index.php?module=artikel&action=thumbnail&cmd=artikel&id=$id&size=400&direkt=1\" style=\"max-width:400px;max-height:400px;\">");
      
      $kat = $this->app->DB->Select("SELECT typ FROM artikel WHERE id = '$id' LIMIT 1");
      if(!empty($kat) && !isset($_artikelart[$kat]))
      {
        $artikelart[$kat] = '';
        $this->app->Tpl->Add('MESSAGE','<div class="error">Es ist eine falsche Artikelkategorie ausgew&auml;hlt</div>');
      }
      if(empty($kat)){
        $artikelart[''] = '- Keine Kategorie zugewiesen -';
      }
    }
    foreach($_artikelart as $k => $v)
    {
      $artikelart[$k] = $v;
    }

    if($action==='create' &&
      $this->app->DB->Select("SELECT COUNT(id) FROM artikelkategorien WHERE geloescht!=1 AND projekt > 0") > 0 &&
      $this->app->DB->Select("SELECT COUNT(id) FROM artikelkategorien WHERE geloescht!=1 AND projekt <= 0") > 0
    )
    {
      $artikelart = array(''=>'')+$artikelart;
    } 

    $field = new HTMLSelect('typ',0);
    $field->AddOptionsSimpleArray($artikelart);
    $this->form->NewField($field);
    

    $land = $this->app->erp->GetSelectLaenderliste();

    $field = new HTMLSelect('herkunftsland',0);
    $field->AddOptionsSimpleArray($land);
    
    $chargensel = null;
    $chargensel['0'] = 'keine';
    $chargensel['1'] = 'aktivieren';
    $chargensel['2'] = 'aktivieren';
    if($this->app->DB->Select("SELECT id FROM artikel WHERE id = '$id' AND chargenverwaltung = '1' LIMIT 1"))
    {
      unset($chargensel['2']);
    }else{
      unset($chargensel['1']);
    }
    $field = new HTMLSelect('chargenverwaltung',0);
    $field->AddOptionsAsocSimpleArray($chargensel);
    
    
/* 18.05. heute ausgeblendet / kein herkunftsland als standard
    if($action=="create" && !$this->app->Secure->GetPOST('speichern'))
    {
      $landdefault = $this->app->erp->Firmendaten('land');
      if(!$landdefault)$landdefault = 'DE';
      $field->value=$landdefault;
    } 
*/
    $this->form->NewField($field);

    if($this->app->Secure->POST['projekt']=='')
    { 
      $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");

      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");        
      if($projekt_bevorzugt=='1') {
        $projekt = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      }
      $field = new HTMLInput("projekt","text",$projekt);
      $field->value=$projekt;
      $this->form->NewField($field);      
    }
   

    $field = new HTMLCheckbox("rabatt","","","1");
    $field->onclick="rabattevent();";
    $this->form->NewField($field);

    $field = new HTMLCheckbox("juststueckliste","","","1");
    $field->onclick="juststuecklisteevent(this.form.juststueckliste.value);";
    $this->form->NewField($field);

    $field = new HTMLCheckbox("stueckliste","","","1");
    $field->onclick="stuecklisteevent(this.form.stueckliste.value);";
    $this->form->NewField($field);

    $field = new HTMLCheckbox("porto","","","1");
    $field->onclick="portoevent(this.form.porto.value);";
    $this->form->NewField($field);

    $field = new HTMLCheckbox("lagerartikel","","","1");
    $field->onclick="lagerartikelevent(this.form.lagerartikel.value);";
    $this->form->NewField($field);

    /* pruefung Artikel nummer doppel */
    if(is_numeric($id)){
      $nummer_db = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
    }
    if(is_numeric($id)){
      //$artikelart = $this->app->DB->Select("SELECT typ FROM artikel WHERE id='$id' LIMIT 1");
    }

    //$anzahl_nummer = $this->app->DB->Select("SELECT count(id) FROM artikel WHERE firma='".$this->app->User->GetFirma()."' AND nummer='$nummer_db'");

    if($nummer !=''){
      $fremde_anzahl_nummer = $this->app->DB->Select("SELECT count(id) FROM artikel WHERE nummer='$nummer' AND id!='$id' AND geloescht=0");
    }
    else {
      $fremde_anzahl_nummer = 0;
    }

    //exec('echo "hallo ('.$submit.') nummer: ('.$nummer.') action:('.$action.')" >> /tmp/test');
    $neuenummervergeben=0;

    if($this->app->erp->Firmendaten('parameterundfreifelder')!=1)
    {
      $this->app->Tpl->Set('DISABLEOPENPARAMETER','<!--');
      $this->app->Tpl->Set('DISABLECLOSEPARAMETER','-->');


      //Workaround für Freifelder werden im Artikel gelöscht wenn Freifelder nicht angezeigt und Artikel gespeichert wird
      //Vielleicht besser bei Speicherfunktion direkt ansetzen
      for($i = 1; $i <= 40; $i++)
      {
        $this->app->Tpl->Set('VORFREIFELD'.$i, '<tr><td>');
        $this->app->Tpl->Set('NACHFREIFELD'.$i, '</tr>');
      }
      $this->app->Tpl->Set('DISABLEOPENPARAMETER2','<div style="display:none">');
      $this->app->Tpl->Set('DISABLECLOSEPARAMETER2','</div>');
    } else {
  
      $this->app->erp->ArtikelFreifeldBezeichnungen();
    }


    if(!$this->app->erp->ModulVorhanden('formeln'))
    {
      $this->app->Tpl->Set('VORFORMELN','<!--');
      $this->app->Tpl->Set('NACHFORMELN','-->');
    }
    $tmpi=0;
    if($this->app->erp->Firmendaten('parameterundfreifelder')){
      for($i = 1; $i <= 40; $i++)
      {
        if($this->app->erp->Firmendaten('freifeld'.$i)!=''){
          $this->app->Tpl->Set('FREIFELD' . $i . 'BEZEICHNUNG', $this->app->erp->Firmendaten('freifeld' . $i));
        }
        else{
          $this->app->Tpl->Set('FREIFELD' . $i . 'BEZEICHNUNG', 'Freifeld ' . $i);
        }
      }
      $aktind = 0;


      for($i = 1; $i <= 40; $i++)
      {
        $n1 = 'freifeld'.$i.'typ';
        $n2 = 'freifeld'.$i.'spalte';
        $n3 = 'freifeld'.$i.'sort';
        $freifeldtyp[$i] = $this->app->erp->Firmendaten($n1);
        $freifeldspalte[$i] = $this->app->erp->Firmendaten($n2);
        $freifeldsort[$i] = $this->app->erp->Firmendaten($n3);
        if($freifeldspalte[$i] > 0)
        {
          $this->app->Tpl->Set('VORFREIFELD'.$i,'<!--');
          $this->app->Tpl->Set('NACHFREIFELD'.$i,'-->');
          $spalte[$freifeldspalte[$i]][$i]['index'] = $i;
          $spalte[$freifeldspalte[$i]][$i]['sort'] = $freifeldsort[$i];
          $sort[$freifeldspalte[$i]][$i] = $freifeldsort[$i];
        }else{
          $aktind++;
          if($aktind % 2 == 1)
          {
            $this->app->Tpl->Set('VORFREIFELD'.$i,'<tr><td width="200">');
            $this->app->Tpl->Set('NACHFREIFELD'.$i,'</td>');
          }else{
            $this->app->Tpl->Set('VORFREIFELD'.$i,'<td width="20">&nbsp;</td><td width="150">');
            $this->app->Tpl->Set('NACHFREIFELD'.$i,'</td></tr>');
          }
        }
      }

      for($s = 1; $s <= 2; $s++)
      {
        if(isset($spalte[$s]))
        {
          array_multisort($sort[$s], SORT_ASC, $spalte[$s]);
          $this->app->Tpl->Set('FREIFELDSPALTE'.$s,'<table class="mkTableFormular" width="100%">');
          foreach($spalte[$s] as $k => $v)
          {
            $tmpi++;
            $bez = (String)$this->app->erp->Firmendaten('freifeld'.$v['index']);
            if($freifeldtyp[$v['index']] === 'select')
            {
              $optionen = null;
              $beza = explode('|', $bez);
              $bez = trim($beza[0]);
              $cbeza = count($beza);
              if($cbeza > 1)
              {
                for($inds = 1; $inds < $cbeza; $inds++){
                  $optionen[] = trim($beza[$inds]);
                }
              }
            }
            if(empty($bez)){
              $bez = 'Freifeld '.$v['index'];
            }
            $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<tr><td>');
            $this->app->Tpl->Add('FREIFELDSPALTE'.$s,$bez.':</td><td>');
            switch($freifeldtyp[$v['index']])
            {
              case 'checkbox':
                $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<input  type="checkbox" name="freifeld'.$v['index'].'" id="freifeld'.$v['index'].'" value="1" '.($this->app->DB->Select("SELECT freifeld".$v['index']." FROM artikel WHERE id = '$id' LIMIT 1")?' checked="checked" ':'').' />');
              break;
              case 'mehrzeilig':
                $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<textarea  cols="40" name="freifeld'.$v['index'].'" id="freifeld'.$v['index'].'">'.$this->app->DB->Select("SELECT freifeld".$v['index']." FROM artikel WHERE id = '$id' LIMIT 1").'</textarea>');
              break;
              case 'datum':
                $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<input type="text" size="10" id="freifeld'.$v['index'].'" name="freifeld'.$v['index'].'" value="'.$this->app->DB->Select("SELECT freifeld".$v['index']." FROM artikel WHERE id = '$id' LIMIT 1").'" />');
                $this->app->YUI->DatePicker('freifeld'.$v['index']);
              break;
              case 'select':
                $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<select name="freifeld'.$v['index'].'" id="freifeld'.$v['index'].'">');
                $tmpv = $this->app->DB->Select("SELECT freifeld".$v['index']." FROM artikel WHERE id = '$id' LIMIT 1");
                if(isset($optionen) && $optionen)
                {
                  $found = false;
                  foreach($optionen as $ov)
                  {
                    $ovvalue=$ov;
                    if(strpos($ov,'=>') !== false) {
                      list($ov, $ovvalue) = explode('=>', $ov);
                    }
                    if($ovvalue == $tmpv)
                    {
                      $found = true;
                      break;
                    }
                  }                
                  if(!$found){
                    $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<option>'.$tmpv.'</option>');
                  }
                  foreach($optionen as $ov)
                  {
                    $ovvalue=$ov;
                    if(strpos($ov,'=>') !== false) {
                      list($ov, $ovvalue) = explode('=>', $ov); 
                    }
                    $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<option'.($tmpv == $ovvalue?' selected':'').' value="'.$ovvalue.'">'.$ov.'</option>');
                  }
                }else{
                  $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<option>'.$tmpv.'</option>');
                }
                $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'</select>');
              break;
              default:
                $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<input type="text" size="30" id="freifeld'.$v['index'].'" name="freifeld'.$v['index'].'" value="'.$this->app->DB->Select("SELECT freifeld".$v['index']." FROM artikel WHERE id = '$id' LIMIT 1").'" />');
              break;
            }

            $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'</td></tr>');
          }
          $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'</table>');
        }
      }
    }
    if($tmpi>0)
    {
      $this->app->Tpl->Set('BENUTZERDEFINIERT','Weitere Felder');
    }
    else {
      $this->app->Tpl->Set('BENUTZERDEFINIERTSTART','<!--');
      $this->app->Tpl->Set('BENUTZERDEFINIERTENDE','-->');
    }
    
    if(!$this->app->erp->ModulVorhanden('steuerregeln'))
    {
      $this->app->Tpl->Set('VORSTEUERREGELN','<!--');
      $this->app->Tpl->Set('NACHSTEUERREGELN','-->');
    }
    

    if($nummer == '' && $action==='edit' && $submit!='')
    { 
      // erst platt machen
      $this->app->DB->Update("UPDATE artikel SET nummer='' WHERE id='$id'");
      $artikelart = $this->app->Secure->GetPOST('typ');
      $neue_nummer = $this->app->erp->GetNextArtikelnummer($artikelart,$this->app->User->GetFirma(),$projekttmp);
      $nummer_db = $neue_nummer;
      $this->app->Secure->POST['nummer']=$neue_nummer;

      $this->app->DB->Update("UPDATE artikel SET nummer='$neue_nummer' WHERE id='$id' LIMIT 1");

      $field = new HTMLInput('nummer','hidden',$neue_nummer);
      $this->form->NewField($field);

      $this->app->YUI->Message('info','Es wurde eine neue Artikelnummer vergeben.');

      $neuenummervergeben=1;
    }

    if($nummer == '' && $action==='create' && $submit!='')
    { 
      //exec('echo  "neu  '.$submit.' '.$nummer.' '.$action.' '.$artikelart.'" >> /tmp/test');
      // erst platt machen
      $artikelart = $this->app->Secure->GetPOST("typ");
      $neue_nummer = $this->app->erp->GetNextArtikelnummer($artikelart,$this->app->User->GetFirma(),$projekttmp);
      $nummer_db = $neue_nummer;
      $this->app->Secure->POST["nummer"]=$neue_nummer;

      $field = new HTMLInput("nummer","hidden",$neue_nummer);
      $this->form->NewField($field);

      $this->app->YUI->Message("info","Es wurde eine neue Artikelnummer vergeben.");

      if($this->app->Secure->POST["projekt"]=="")
      {
        $field = new HTMLInput("projekt","text",$projekttmp);
        $field->value=$projekttmp;
        $this->form->NewField($field);
      }

      $neuenummervergeben=1;
    } 

    if($action==='create')
    {
      if($this->app->erp->Version()==='stock')
      {
        $this->app->Secure->POST["lagerartikel"]=1;
        $field = new HTMLInput("lagerartikel","hidden",1);
        $this->form->NewField($field);
      }
    }
    if($action === 'edit' && $nummer != '' && ($nummer !== $nummer_db)) {

      $doppelteNummern = $this->app->DB->SelectArr(
        sprintf(
          "SELECT art.nummer, count(art.nummer) as NumOccurrences, if(ifnull(pr.eigenernummernkreis,0) = 0,0,pr.id) AS projekt
        FROM artikel art 
        LEFT JOIN projekt pr ON art.projekt = pr.id 
        WHERE art.geloescht <> '1' AND art.nummer <> '' AND art.nummer <> 'DEL' AND nummer in ('%s','%s')
        GROUP BY art.nummer,if(ifnull(pr.eigenernummernkreis,0) = 0,0,pr.id) 
        HAVING (COUNT(art.nummer) > 0) 
        LIMIT 101",
          $nummer_db, $nummer
        )
      );
      if(!empty($doppelteNummern)) {
        $oldCount = [];
        $newCount = [];
        foreach($doppelteNummern as $doppelteNummer) {
          if($doppelteNummer['nummer'] == $nummer) {
            $newCount[] = $doppelteNummer['NumOccurrences'] + 1;
          }
          elseif($doppelteNummer['nummer'] == $nummer_db) {
            $oldCount[] = $doppelteNummer['NumOccurrences'];
          }
        }
        $oldCount = implode(',', $oldCount);
        $newCount = implode(',', $newCount);
        if($newCount === '') {
          $newCount = '1';
        }
        if($oldCount !== $newCount) {
          $this->app->erp->ClearSqlCache('artikel');
        }
      }
    }

    //$already_set=0;
    $anzahl_nummer = $this->app->DB->Select("SELECT count(id) FROM artikel WHERE firma='".$this->app->User->GetFirma()."' AND nummer='$nummer_db' AND geloescht!=1");
    if(($anzahl_nummer > 1 || $fremde_anzahl_nummer > 0) && $neuenummervergeben==1) {
      $this->app->erp->ClearSqlCache('artikel');
    }
    if(($anzahl_nummer > 1 || $fremde_anzahl_nummer > 0) && $neuenummervergeben!=1 && $action==='edit') {
      $this->app->YUI->Message('error','Achtung! Die Artikelnummer wurde doppelt vergeben!');
    }

    $warengruppe = $this->app->erp->GetArtikelWarengruppe();

    $field = new HTMLSelect('warengruppe',0);
    $field->AddOptionsSimpleArray($warengruppe);
    $this->form->NewField($field);

    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput('firma','hidden',$this->app->User->GetFirma());
    $this->form->NewField($field);
    if($id && $this->app->DB->Select("SELECT id FROM artikel WHERE id = '$id' AND stueckliste = 1 LIMIT 1") &&
      !$this->app->DB->Select("SELECT id FROM stueckliste WHERE `stuecklistevonartikel` = '$id' LIMIT 1"))
    {
      $this->app->Tpl->Add('MESSAGE','<div class="warning">Der Artikel ist als St&uuml;ckliste markiert, enth&auml;lt aber keine St&uuml;cklistenelemente</div>');
    }
  }


  function ReplaceDecimal($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDecimal($db,$value,$fromform);
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(strpos($value,'-') > 0) {
      $dbformat = 1;
    }

    // wenn ziel datenbank
    if($db)
    { 
      if($dbformat) {
        return $value;
      }

      return $this->app->String->Convert($value,'%1.%2.%3','%3-%2-%1');

    }
    // wenn ziel formular

    if($dbformat){
      return $this->app->String->Convert($value, '%1-%2-%3', '%3.%2.%1');
    }

    return $value;
  }


  function ReplaceShopname($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      if(is_numeric($id)){
        $abkuerzung = $this->app->DB->Select("SELECT bezeichnung FROM shopexport WHERE id='$id' LIMIT 1");
      }else{
        $abkuerzung = '';
      }
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM shopexport WHERE bezeichnung='$value' LIMIT 1");
    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular

    return $abkuerzung;
  }

  function ReplaceBetrag($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceBetrag($db,$value,$fromform);
  }

  public function ReplaceTrim($db,$value,$fromform)
  {
    return trim($value);
  }

  function ReplaceLagerplatz($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      if(is_numeric($id)){
        $abkuerzung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$id' LIMIT 1");
      }else{
        $abkuerzung = '';
      }
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$value' LIMIT 1");
    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular

    return $abkuerzung;
  }


  function ReplaceLieferant($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceLieferant($db,$value,$fromform);
  }

  function ReplaceSteuergruppe($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceSteuergruppe($db,$value,$fromform);
  }

  function ReplaceArtikel($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceArtikel($db,$value,$fromform);
  }
  
  function ReplaceSteuersatz($db,$value,$fromform)
  {
    if($db)
    {
      if($value === '' || $value === null)
      {
        return -1;
      }
      return str_replace(',','.', $value);
    }
    if($value < 0){
      return '';
    }
    return $value;
  }

  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT nummer, name_de as name,barcode, id FROM artikel order by nummer");
    $table->Display($this->parsetarget);
  }



  public function Search()
  {
    //$this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }
}
