<?php
include ("_gen/widget.gen.adresse.php");

class WidgetAdresse extends WidgetGenAdresse 
{
  private $app;
  function __construct($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::__construct($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {

    $action = $this->app->Secure->GetGET("action");
    $this->app->YUI->FirstField('typ');

    $this->app->YUI->DatePicker("geburtstag");
    $this->app->YUI->DatePicker("mandatsreferenzdatum");
    $this->app->YUI->DatePicker("liefersperredatum");
    $this->form->ReplaceFunction("geburtstag",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("mandatsreferenzdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("liefersperredatum",$this,"ReplaceDatum");

    $this->form->ReplaceFunction("arbeitszeitprowoche",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("zahlungszielskonto",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("zahlungszielskontolieferant",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("provision",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("portofreiab",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("portofreiablieferant",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("kreditlimit",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("kreditlimiteinmalig",$this,"ReplaceBetrag");

    $this->form->ReplaceFunction("lat",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("lng",$this,"ReplaceBetrag");
    $this->form->ReplaceFunction("name",$this,"ReplaceTrim");

    $this->app->YUI->CkEditor("sonstiges","internal");
    $this->app->YUI->CkEditor("infoauftragserfassung","internal");
    $this->app->YUI->CkEditor("rabattinformation","internal");
    $this->app->YUI->CkEditor("mandatsreferenzhinweis","basic");

    $this->app->YUI->AutoComplete("lieferbedingung","lieferbedingungen");

    $this->app->YUI->AutoComplete("kassiererprojekt","projektname",1);
    $this->form->ReplaceFunction("kassiererprojekt",$this,"ReplaceProjekt");

    $this->app->YUI->AutoComplete('fromshop', 'shopnameid');
    $this->form->ReplaceFunction('fromshop', $this, 'ReplaceShop');

    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->RunHook('address_widget',1, $id);
    $kassierernummer = $this->app->Secure->GetPOST("kassierernummer");
    $submit = $this->app->Secure->GetPOST("speichern");
    /* pruefung Artikel nummer doppel */
    if(is_numeric($id))
      $nummer_db = $this->app->DB->Select("SELECT kassierernummer FROM adresse WHERE id='$id' LIMIT 1");

    $kassiereraktiv = $this->app->DB->Select("SELECT kassiereraktiv FROM adresse WHERE id='$id' LIMIT 1");

    $anzahl_nummer = $this->app->DB->Select("SELECT count(id) FROM adresse WHERE kassierernummer='$nummer_db'");

    if($kassierernummer > 0)
      $fremde_anzahl_nummer = $this->app->DB->Select("SELECT count(id) FROM adresse WHERE kassierernummer='$kassierernummer' AND id!='$id' AND geloescht=0");
    $anzahl_nummer = $this->app->DB->Select("SELECT count(id) FROM adresse WHERE kassierernummer='$nummer_db' AND geloescht!=1");
    if(($anzahl_nummer > 1 || $fremde_anzahl_nummer > 0) && $action=="edit" && $kassiereraktiv=="1")
    {
      //$this->app->Tpl->Add(MESSAGE,"<div class=\"error\">Achtung Artikel Nr. doppelt vergeben!</div>");
      $this->app->YUI->Message("error","Achtung! Die Kassierernummer wurde doppelt vergeben!");
    }

    $this->app->YUI->AutoComplete('vertrieb','adressegruppevertriebbearbeiter', 0, '&typ=vertrieb');
    $this->app->YUI->AutoComplete('innendienst','adressegruppevertriebbearbeiter', 0, '&typ=bearbeiter');
    $this->form->ReplaceFunction("vertrieb",$this,"ReplaceAdresse");
    $this->form->ReplaceFunction("innendienst",$this,"ReplaceAdresse");

   
    if($this->app->erp->ModulVorhanden('kommissionskonsignationslager'))
    {
      $this->form->ReplaceFunction("kommissionskonsignationslager",$this,"ReplaceLagerPlatz");
      $this->app->YUI->AutoComplete("kommissionskonsignationslager","lagerplatz");
      
    }else{
      $this->app->Tpl->Set('VORKOMMISSIONSKONSIGNATIONSLAGER','<!--');
      $this->app->Tpl->Set('NACHKOMMISSIONSKONSIGNATIONSLAGER','-->');
    }

    if($action=="create")
    {

      $adresse_vorlage = strstr($this->app->erp->Firmendaten("adresse_vorlage"), ' ', true); 
      if($adresse_vorlage > 0)
      {
        $adresse_vorlage_value = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse_vorlage' LIMIT 1");
        foreach($adresse_vorlage_value[0] as $key=>$value)
          if(isset($this->form->HTMLList[$key]->htmlobject) && !$this->app->Secure->POST[$key] && $key!="kundennummer" && $key!="lieferantennummer" && $key!="mitarbeiternummer")
            $this->form->HTMLList[$key]->htmlobject->value = $value;
      }

      // liste zuweisen
      $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
      $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
      $this->form->NewField($field);
      if($this->app->erp->ModulVorhanden('vertriebscockpit') && !$this->app->Secure->GetPOST('speichern') && !$this->app->erp->GetKonfiguration('vertriebscockpit_kein_vertrieb_adresse_anlegen'))
      {
        $field = new HTMLInput("vertrieb","text",$this->app->User->GetAdresse());
        $field->value=$this->app->User->GetAdresse();
        $this->form->NewField($field);
      }

      if($this->app->Secure->POST["projekt"]=="")
      {
        $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");

        $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
        if($projekt_bevorzugt=="1")
        { 
          $projekt = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
        }
        $field = new HTMLInput("projekt","text",$projekt);
        $field->value=$projekt;
        $this->form->NewField($field);
      }

      $zahlungsweise = $this->app->erp->GetZahlungsweise();

      $field = new HTMLSelect("zahlungsweise",0);
      if($this->app->Secure->POST["zahlungsweise"]=="")
      {
        if(isset($adresse_vorlage_value) && $adresse_vorlage_value[0]['zahlungsweise']!="")
        $field->value=$adresse_vorlage_value[0]['zahlungsweise'];
        else $field->value=$this->app->erp->StandardZahlungsweise($projekt);
      }
      //$field->onchange="aktion_buchen(this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value);";
      $field->AddOptionsSimpleArray($zahlungsweise);
      $this->form->NewField($field);

      $zahlungsweise = $this->app->erp->GetZahlungsweise();

      $field = new HTMLSelect("zahlungsweiselieferant",0);
      if($this->app->Secure->POST["zahlungsweiselieferant"]=="")
      {
        if(isset($adresse_vorlage_value) && $adresse_vorlage_value[0]['zahlungsweiselieferant']!="")
        $field->value=$adresse_vorlage_value[0]['zahlungsweiselieferant'];
        else $field->value=$this->app->erp->StandardZahlungsweiseLieferant($projekt);
      }
      //$field->onchange="aktion_buchen(this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value);";
      $field->AddOptionsSimpleArray($zahlungsweise);
      $this->form->NewField($field);



      $versandart = $this->app->erp->GetVersandartAuftrag($projekt);
      array_unshift($versandart , '');
      $field = new HTMLSelect("versandart",0);
      if($this->app->Secure->POST["versandart"]=="")
        $field->value=$this->app->erp->StandardVersandart($projekt);
      $field->AddOptionsSimpleArray($versandart);
      $this->form->NewField($field);

    }
    else {

      $zahlungsweise = $this->app->erp->GetZahlungsweise('adresse', $id);
      $zahlungsweise['']="Bitte wählen ...";
       

      $field = new HTMLSelect("zahlungsweise",0);
      //$field->onchange="aktion_buchen(this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value);";
      $field->AddOptionsSimpleArray($zahlungsweise);
      $this->form->NewField($field);

      $field = new HTMLSelect("zahlungsweiselieferant",0);
      //$field->onchange="aktion_buchen(this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value);";
      $field->AddOptionsSimpleArray($zahlungsweise);
      $this->form->NewField($field);

      $versandart = $this->app->erp->GetVersandartAuftrag((int)$this->app->DB->Select("SELECT projekt FROM adresse WHERE id = '$id' LIMIT 1"));

      array_unshift($versandart , '');
      $field = new HTMLSelect("versandart",0);
      $field->AddOptionsSimpleArray($versandart);
      $this->form->NewField($field);
      if($submit != '') {
        if(($kundennummer = $this->app->Secure->POST['kundennummer'])
          != ($kundennummerdb = $this->app->DB->Select(
            sprintf('SELECT kundennummer FROM adresse WHERE id = %d', $id)))) {
          $check_double_doppeltekundennummer = $this->app->DB->SelectArr(
            sprintf("SELECT adr.kundennummer,count(adr.id) as NumOccurrences 
            FROM adresse adr 
            LEFT JOIN projekt pr ON adr.projekt = pr.id 
            WHERE adr.geloescht = 0 AND (adr.projekt = 0 OR pr.eigenernummernkreis = 0) AND adr.kundennummer <> ''
            AND adr.kundennummer IN ('%s', '%s')
            GROUP BY adr.kundennummer 
            HAVING COUNT(adr.kundennummer) > 0 
            LIMIT 100",
              $kundennummer, $kundennummerdb
            ));
          if(!empty($check_double_doppeltekundennummer)) {
            $oldCount = [];
            $newCount = [];
            foreach($check_double_doppeltekundennummer as $doppelteNummer) {
              if($doppelteNummer['kundennummer'] == $kundennummer) {
                $newCount[] = $doppelteNummer['NumOccurrences'] + 1;
              }
              elseif($doppelteNummer['kundennummer'] == $kundennummerdb) {
                $oldCount[] = $doppelteNummer['NumOccurrences'];
              }
            }
            $oldCount = implode(',', $oldCount);
            $newCount = implode(',', $newCount);
            if($newCount === '') {
              $newCount = '1';
            }
            if($oldCount !== $newCount) {
              $this->app->erp->ClearSqlCache('adresse');
            }
          }
        }
      }
    }

    $waehrung = $this->app->DB->Select("SELECT waehrung FROM adresse WHERE id='$id'");
    if($waehrung == "" && $action=="edit" && $submit=="")
    { 
      // erst platt machen
      $this->app->DB->Update("UPDATE adresse SET waehrung='".$this->app->erp->GetStandardWaehrung($projekt)."' WHERE id='$id'");
    } 


    $waehrungOptions = $this->app->erp->GetWaehrung();
    $field = new HTMLSelect("waehrung",0);
    $field->AddOptionsSimpleArray($waehrungOptions);
    if($field->value=="") {
      $field->value=$this->app->erp->GetStandardWaehrung($projekt);
    }
    $this->form->NewField($field);
 
    $field = new HTMLInput("land","hidden","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_land","hidden","");
    $this->form->NewField($field);

/*
    $versandart = $this->app->erp->GetVersandartAuftrag();
    $field = new HTMLSelect("versandart",0);
    $field->AddOptionsSimpleArray($versandart);
    $this->form->NewField($field);
*/

/*
    $field = new HTMLSelect("zahlungsweiselieferant",0);
    //$field->onchange="aktion_buchen(this.form.zahlungsweiselieferant.options[this.form.zahlungsweise.selectedIndex].value);";
    $field->AddOptionsSimpleArray($zahlungsweise);
    $this->form->NewField($field);
*/

    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");

    $field = new HTMLCheckbox("abweichende_rechnungsadresse","","","1","","19");
    $field->onclick="abweichend(this.form.abweichende_rechnungsadresse.value);";
    $this->form->NewField($field);



    $typOptions = $this->app->erp->GetTypSelect();
    $field = new HTMLSelect("typ",0,"typ",false,false,"1");
    //$field->onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($typOptions);
    $this->form->NewField($field);

    $field = new HTMLSelect("rechnung_typ",0);
    //$field->onchange="onchange_typ(this.form.typ.options[this.form.rechnung_typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($typOptions);
    $this->form->NewField($field);

    $sprachenOptions = $this->app->erp->GetSprachenSelect($id?$this->app->DB->Select("SELECT sprache FROM adresse WHERE id = '$id' LIMIT 1"):null);
    
    $field = new HTMLSelect("sprache",0,"sprache",false,false,"1");
    $field->AddOptionsSimpleArray($sprachenOptions);
    $this->form->NewField($field);

    $field = new HTMLInput("vorname","hidden","");
    $this->form->NewField($field);

    for($i = 1; $i <= 20; $i++)
    {
      if($this->app->erp->Firmendaten("adressefreifeld".$i)!="")
        $this->app->Tpl->SetText('FREIFELD'.$i.'BEZEICHNUNG',$this->app->erp->Firmendaten("adressefreifeld".$i));
      else
        $this->app->Tpl->SetText('FREIFELD'.$i.'BEZEICHNUNG',"Freifeld ".$i);
    }

    for($i = 0; $i <= 20; $i++)
    {
      $n1 = 'adressefreifeld'.$i.'typ';
      $n2 = 'adressefreifeld'.$i.'spalte';
      $n3 = 'adressefreifeld'.$i.'sort';
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
      }
    }
    $tmpi=0;
    for($s = 1; $s <= 2; $s++)
    {
      if(isset($spalte[$s]))
      {
        array_multisort($sort[$s], SORT_ASC, $spalte[$s]);
        $this->app->Tpl->Set('FREIFELDSPALTE'.$s,'<table class="mkTableFormular" width="100%">');
        foreach($spalte[$s] as $k => $v)
        {
          $tmpi++;
          $bez = $this->app->erp->Firmendaten('adressefreifeld'.$v['index']);
          if($freifeldtyp[$v['index']] == 'select')
          {
            $optionen = null;
            $beza = explode('|', $bez);
            $bez = trim($beza[0]);
            if($beza && count($beza) > 1)
            {
              $cbeza = count($beza);
              for($inds = 1; $inds < $cbeza; $inds++)$optionen[] = trim($beza[$inds]);
            }
          }
          if(empty($bez))$bez = 'Freifeld '.$v['index'];
          $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<tr><td>');
          $this->app->Tpl->Add('FREIFELDSPALTE'.$s,$bez.':</td><td>');
          switch($freifeldtyp[$v['index']])
          {
            case 'checkbox':
              $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<input  type="checkbox" name="freifeld'.$v['index'].'" id="freifeld'.$v['index'].'" value="1" '.($this->app->DB->Select("SELECT freifeld".$v['index']." FROM adresse WHERE id = '$id' LIMIT 1")?' checked="checked" ':'').' />');
            break;
            case 'mehrzeilig':
              $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<textarea  cols="40" name="freifeld'.$v['index'].'" id="freifeld'.$v['index'].'">'.$this->app->DB->Select("SELECT freifeld".$v['index']." FROM adresse WHERE id = '$id' LIMIT 1").'</textarea>');
            break;
            case 'datum':
              $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<input type="text" size="10" id="freifeld'.$v['index'].'" name="freifeld'.$v['index'].'" value="'.$this->app->DB->Select("SELECT freifeld".$v['index']." FROM adresse WHERE id = '$id' LIMIT 1").'" />');
              $this->app->YUI->DatePicker('freifeld'.$v['index']);
            break;
            case 'select':
              $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<select name="freifeld'.$v['index'].'" id="freifeld'.$v['index'].'">');
              $tmpv = $this->app->DB->Select("SELECT freifeld".$v['index']." FROM adresse WHERE id = '$id' LIMIT 1");
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
                if(!$found)$this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<option>'.$tmpv.'</option>');
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
              $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'<input type="text" size="30" id="freifeld'.$v['index'].'" name="freifeld'.$v['index'].'" value="'.$this->app->DB->Select("SELECT freifeld".$v['index']." FROM adresse WHERE id = '$id' LIMIT 1").'" />');
            break;
          }
          
          $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'</td></tr>');
        }
        $this->app->Tpl->Add('FREIFELDSPALTE'.$s,'</table>');
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

    if($this->app->erp->ModulVorhanden('proformarechnung'))
    {
      if($this->app->erp->Firmendaten("briefhtml")=="1")$this->app->YUI->CkEditor("zollinformationen","belege");
    }else{
      $this->app->Tpl->Set('VORPROFORMARECHNUNG','<!--');
      $this->app->Tpl->Set('NACHPROFORMATRECHNUNG','-->');
    }
      
    /*
       $id = $this->app->Secure->GetGET('id');
       if(is_numeric($id) && $id>0) {
       $vorname = $this->app->DB->Select("SELECT vorname FROM adresse WHERE id='$id' LIMIT 1");
       $typ = $this->app->DB->Select("SELECT typ FROM adresse WHERE id='$id' LIMIT 1");
       $this->app->Tpl->Set('ADRESSEVORNAME', $vorname);
       $this->app->Tpl->Set('ADRESSETYP', $typ);
       }
     */
    if($this->app->erp->ModulVorhanden('bundesstaaten'))
    {
      $typOptions = $this->app->erp->GetTypSelect();
      $field = new HTMLSelect("bundesstaat",0,"bundesstaat",false,false,"1");
      $this->form->NewField($field);
      $typOptions = $this->app->erp->GetTypSelect();
      $field = new HTMLSelect("rechnung_bundesstaat",0,"rechnung_bundesstaat",false,false,"1");
      $this->form->NewField($field);
      $this->app->YUI->BundeslaenderSelect('EPROO_SELECT_BUNDESSTAAT', 'land', 'bundesstaat',$this->app->DB->Select("SELECT land FROM adresse WHERE id = '$id' LIMIT 1"),$this->app->DB->Select("SELECT bundesstaat FROM adresse WHERE id = '$id' LIMIT 1"));
      $this->app->YUI->BundeslaenderSelect('EPROO_SELECT_BUNDESSTAAT_RECHNUNG', 'rechnung_land', 'rechnung_bundesstaat',$this->app->DB->Select("SELECT rechnung_land FROM adresse WHERE id = '$id' LIMIT 1"),$this->app->DB->Select("SELECT rechnung_bundesstaat FROM adresse WHERE id = '$id' LIMIT 1"));
    }else{
      $this->app->Tpl->Set('VORBUNDESSTAAT','<!--');
      $this->app->Tpl->Set('NACHBUNDESSTAAT','-->');
    }

  }

  function ReplaceDecimal($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDecimal($db,$value,$fromform);
  }

  function ReplaceAdresse($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceAdresse($db,$value,$fromform);
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }


  function ReplaceBetrag($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceBetrag($db,$value,$fromform);
  }

  function ReplaceLagerPlatz($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceLagerPlatz($db,$value,$fromform);
  }

  public function ReplaceTrim($db,$value,$fromform)
  {
    return trim($value);
  }

  /**
   * @param int|bool   $db
   * @param string|int $value
   * @param int|bool   $fromform
   *
   * @return int|string
   */
  public function ReplaceShop($db,$value,$fromform)
  {
    if(!$fromform) {
      $id = $value;
      if($id > 0){
        $abkuerzung = $this->app->DB->Select(
          sprintf(
            "SELECT CONCAT(id, ' ',bezeichnung) FROM shopexport WHERE id=%d LIMIT 1",
            $id
          )
        );
      }
      else{
        $abkuerzung = '';
      }
    }
    else {
      $value = explode(' ', $value);
      $value = reset($value);
      $id =  $this->app->DB->Select(
        sprintf(
          "SELECT id FROM shopexport WHERE id=%d LIMIT 1",
          $value
        )
      );
      if($id <=0) {
        $id=0;
      }
    }

    if($db) {
      return $id;
    }

    return $abkuerzung;
  }
}
