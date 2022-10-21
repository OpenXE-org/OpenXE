<?php 
abstract class Versanddienstleister {
  /** @var int $id */
  public $id;
  /** @var Application $app */
  public $app;

  public function isEtikettenDrucker(): bool {
    return false;
  }

  public function GetAdressdaten($id, $sid)
  {
    if($sid==='rechnung'){
      $rechnungId = $id;
    }
    else
    {
      $rechnungId ='';
    }

    if($sid==='versand')
    {
      $lieferscheinId = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");
      $rechnungId  = $this->app->DB->Select("SELECT rechnung FROM versand WHERE id='$id' LIMIT 1");
      $sid = 'lieferschein';
    } else {
      $lieferscheinId = $id;
      if($sid === 'lieferschein'){
        $rechnungId = $this->app->DB->Select("SELECT id FROM rechnung WHERE lieferschein = '$lieferscheinId' LIMIT 1");
      }
      if($rechnungId<=0) {
        $rechnungId = $this->app->DB->Select("SELECT rechnungid FROM lieferschein WHERE id='$lieferscheinId' LIMIT 1");
      }
    }
    $ret['lieferscheinId'] = $lieferscheinId;
    $ret['rechnungId'] = $rechnungId;

    if($rechnungId){
      $artikel_positionen = $this->app->DB->SelectArr("SELECT * FROM rechnung_position WHERE rechnung='$rechnungId'");
    } else {
      $artikel_positionen = $this->app->DB->SelectArr(sprintf('SELECT * FROM `%s` WHERE `%s` = %d',$sid.'_position',$sid,$lieferscheinId));
    }

    if($sid==='rechnung' || $sid==='lieferschein' || $sid==='adresse')
    {
      $docArr = $this->app->DB->SelectRow("SELECT * FROM `$sid` WHERE id = $lieferscheinId LIMIT 1");

      $name = trim($docArr['name']);
      $name2 = trim($docArr['adresszusatz']);
      $abt = 0;
      if($name2==='')
      {
        $name2 = trim($docArr['abteilung']);
        $abt=1;
      }
      $name3 = trim($docArr['ansprechpartner']);
      if($name3==='' && $abt!==1){
        $name3 = trim($docArr['abteilung']);
      }

      //unterabteilung versuchen einzublenden
      if($name2==='') {
        $name2 = trim($docArr['unterabteilung']);
      } else if ($name3==='') {
        $name3 = trim($docArr['unterabteilung']);
      }

      if($name3!=='' && $name2==='') {
        $name2=$name3;
        $name3='';
      }

      $ort = trim($docArr['ort']);
      $plz = trim($docArr['plz']);
      $land = trim($docArr['land']);
      $strasse = trim($docArr['strasse']);
      $strassekomplett = $strasse;
      $hausnummer = trim($this->app->erp->ExtractStreetnumber($strasse));

      $strasse = trim(str_replace($hausnummer,'',$strasse));
      $strasse = str_replace('.','',$strasse);

      if($strasse=='')
      {
        $strasse = trim($hausnummer);
        $hausnummer = '';
      }
      $telefon = trim($docArr['telefon']);
      $email = trim($docArr['email']);

      $ret['order_number'] = $docArr['auftrag'];
      $ret['addressId'] = $docArr['adresse'];
    }
    // wenn rechnung im spiel entweder durch versand oder direkt rechnung
    if($rechnungId >0)
    {
      $invoice_data =  $this->app->DB->SelectRow("SELECT zahlungsweise, soll, belegnr FROM rechnung WHERE id='$rechnungId' LIMIT 1");
      $ret['zahlungsweise'] = $invoice_data['zahlungsweise'];
      $ret['betrag'] = $invoice_data['soll'];
      $ret['invoice_number'] = $invoice_data['belegnr'];

      if($invoice_data['zahlungsweise']==='nachnahme'){
        $ret['nachnahme'] = true;
      }
    }

    if(isset($frei))$ret['frei'] = $frei;
    if(isset($inhalt))$ret['inhalt'] = $inhalt;
    if(isset($keinealtersabfrage))$ret['keinealtersabfrage'] = $keinealtersabfrage;
    if(isset($altersfreigabe))$ret['altersfreigabe'] = $altersfreigabe;
    if(isset($versichert))$ret['versichert'] = $versichert;
    if(isset($extraversichert))$ret['extraversichert'] = $extraversichert;
    $ret['name'] = $name;
    $ret['name2'] = $name2;
    $ret['name3'] = $name3;
    $ret['ort'] = $ort;
    $ret['plz'] = $plz;
    $ret['strasse'] = $strasse;
    $ret['strassekomplett'] = $strassekomplett;
    $ret['hausnummer'] = $hausnummer;
    $ret['land'] = $land;
    $ret['telefon'] = $telefon;
    $ret['phone'] = $telefon;
    $ret['email'] = trim($email," \t\n\r\0\x0B\xc2\xa0");

    $check_date = $this->app->DB->Select("SELECT date_format(now(),'%Y-%m-%d')");

    $ret['abholdaumt'] = date('d.m.Y', strtotime($check_date));

    $anzahl = $this->app->Secure->GetGET("anzahl");

    if($anzahl <= 0)
    {
      $anzahl=1;
    }

    if($sid==="lieferschein"){
      $standardkg = $this->app->erp->VersandartMindestgewicht($lieferscheinId);
    }
    else{
      $standardkg = $this->app->erp->VersandartMindestgewicht();
    }
    $ret['standardkg'] = $standardkg;
    $ret['anzahl'] = $anzahl;
    return $ret;
  }

  /**
   * @param string $target
   *
   * @return string
   */
  public function Einstellungen($target = 'return')
  {
    if(!$this->id)
    {
      return '';
    }
    //$id = $this->id;
    $struktur = $this->EinstellungenStruktur();
    if($this->app->Secure->GetPOST('speichern'))
    {
      $json = $this->app->DB->Select("SELECT einstellungen_json FROM versandarten WHERE id = '".$this->id."' LIMIT 1");
      $modul = $this->app->DB->Select("SELECT modul FROM versandarten WHERE id = '".$this->id."' LIMIT 1");
      if(!empty($json))
      {
        $json = @json_decode($json, true);
      }else{
        $json = array();
        foreach($struktur as $name => $val)
        {
          if(isset($val['default']))
          {
            $json[$name] = $val['default'];
          }
        }
      }
      if(empty($json))
      {
        $json = null;
      }
      foreach($struktur as $name => $val)
      {

        if($modul === $this->app->Secure->GetPOST('modul_name'))
        {
          $json[$name] = $this->app->Secure->GetPOST($name, '','', 1);
        }
       
        if(isset($val['replace']))
        {
          switch($val['replace'])
          {
            case 'lieferantennummer':
              $json[$name] = $this->app->erp->ReplaceLieferantennummer(1,$json[$name],1);
            break;
          }
        }
      }
      $json_str = $this->app->DB->real_escape_string(json_encode($json));
      $this->app->DB->Update("UPDATE versandarten SET einstellungen_json = '$json_str' WHERE id = '".$this->id."' LIMIT 1");
    }
    $id = $this->id;
    $html = '';
   
    $json = $this->app->DB->Select("SELECT einstellungen_json FROM versandarten WHERE id = '$id' LIMIT 1");
    if($json)
    {
      $json = json_decode($json, true);
    }else{
      $json = null;
    }

    $changed = false;
    foreach($struktur as $name => $val)
    {
      if(isset($val['default']) && !isset($json[$name]))
      {
        $changed = true;
        $json[$name] = $val['default'];
      }
    }
    if($changed)
    {
      $json_str = $this->app->DB->real_escape_string(json_encode($json));
      $this->app->DB->Update("UPDATE versandarten SET einstellungen_json = '$json_str' WHERE id = '".$this->id."' LIMIT 1");      
    }
    $first = true;
    foreach($struktur as $name => $val)
    {
      if(isset($val['heading']))
      {
        $html .= '<tr><td colspan="2"><b>'.html_entity_decode($val['heading']).'</b></td></tr>';
      }
      $html .= '<tr><td>'.($first?'<input type="hidden" name="modul_name" value="'.$this->app->DB->Select("SELECT modul FROM versandarten WHERE id = '$id' LIMIT 1").'" />':'').(empty($val['bezeichnung'])?$name:$val['bezeichnung']).'</td><td>';
      $typ = 'text';
      if(!empty($val['typ']))
      {
        $typ = $val['typ'];
      }
      if(isset($val['replace']))
      {
        switch($val['replace'])
        {
          case 'lieferantennummer':
            $json[$name] = $this->app->erp->ReplaceLieferantennummer(0,$json[$name],0);
            if($target !== 'return')
            {
              $this->app->YUI->AutoComplete($name, 'lieferant', 1);
            }
          break;
          case 'shop':
            $json[$name] .= ($json[$name]?' '.$this->app->DB->Select("SELECT bezeichnung FROM shopexport WHERE id = '".(int)$json[$name]."'"):'');
            if($target !== 'return')
            {
              $this->app->YUI->AutoComplete($name, 'shopnameid');
            }
          break;
          case 'etiketten':
            //$json[$name] = $this->app->erp->ReplaceLieferantennummer(0,$json[$name],0);
            if($target !== 'return')
            {
              $this->app->YUI->AutoComplete($name, 'etiketten');
            }
          break;
 
        }
      }
      /*if(!isset($json[$name]) && isset($val['default']))
      {
        $json[$name] = $val['default'];
      }*/
      switch($typ)
      {
        case 'textarea':
          $html .= '<textarea name="'.$name.'" id="'.$name.'">'.(!isset($json[$name])?'':$json[$name]).'</textarea>';
        break;
        case 'checkbox':
          $html .= '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="1" '.((isset($json[$name]) && $json[$name])?' checked="checked" ':'').' />';
        break;
        case 'select':
          $html .= '<select name="'.$name.'">';
          if(isset($val['optionen']) && is_array($val['optionen']))
          {
            foreach($val['optionen'] as $k => $v)
            {
              $html .= '<option value="'.$k.'"'.($k == (isset($json[$name])?$json[$name]:'')?' selected="selected" ':'').'>'.$v.'</option>';
            }
          }
          $html .= '</select>';
        break;
        case 'submit':
          if(isset($val['text']))
          {
            $html .= '<form method="POST"><input type="submit" name="'.$name.'" value="'.$val['text'].'"></form>';
          }
        break;
        case 'custom':
          if(isset($val['function']))
          {
            $tmpfunction = $val['function'];
            if(method_exists($this, $tmpfunction))
            {
              $html .= $this->$tmpfunction();
            }
          }
        break;
        default:

          $html .= '<input type="text" '.(!empty($val['size'])?' size="'.$val['size'].'" ':'').' '.(!empty($val['placeholder'])?' placeholder="'.$val['placeholder'].'" ':'').' name="'.$name.'" id="'.$name.'" value="'.(!isset($json[$name])?'':htmlspecialchars($json[$name])).'" />';
        break;
      }
      if(isset($val['info']) && $val['info'])$html .= ' <i>'.$val['info'].'</i>';
      
      $html .= '</td></tr>';
      $first = false;
    }
    
    if($target === 'return') {
      return $html;
    }
    $this->app->Tpl->Add($target, $html);
    return '';
  }

  protected abstract function EinstellungenStruktur();

    /**
     * @param string $target
     *
     * @return bool
     */
  public function checkInputParameters(string $target = ''): bool
  {
      return true;
  }



  /**
   * @param string $tracking
   * @param int    $versand
   * @param int    $lieferschein
   */
  public function SetTracking($tracking,$versand=0,$lieferschein=0, $trackingLink = '')
  {
    //if($versand > 0) $this->app->DB->Update("UPDATE versand SET tracking=CONCAT(tracking,if(tracking!='',';',''),'".$tracking."') WHERE id='$versand' LIMIT 1");
    $this->app->User->SetParameter('versand_lasttracking',$tracking);
    $this->app->User->SetParameter('versand_lasttracking_link',$trackingLink);
    $this->app->User->SetParameter('versand_lasttracking_versand', $versand);
    $this->app->User->SetParameter('versand_lasttracking_lieferschein', $lieferschein);
  }

  /**
   * @param string $tracking
   */
  public function deleteTrackingFromUserdata($tracking)
  {
    if(empty($tracking)) {
      return;
    }
    $trackingUser = !empty($this->app->User) && method_exists($this->app->User,'GetParameter')?
      $this->app->User->GetParameter('versand_lasttracking'):'';
    if(empty($trackingUser) || $trackingUser !== $tracking) {
      return;
    }

    $this->app->User->SetParameter('versand_lasttracking','');
    $this->app->User->SetParameter('versand_lasttracking_link','');
    $this->app->User->SetParameter('versand_lasttracking_versand', '');
    $this->app->User->SetParameter('versand_lasttracking_lieferschein', '');
  }

  /**
   * @param string $tracking
   *
   * @return string mixed
   */
  public function TrackingReplace($tracking)
  {
    return $tracking;
  }

  /**
   * @param string $tracking
   * @param int    $notsend
   * @param string $link
   * @param string $rawlink
   *
   * @return bool
   */
  public function Trackinglink($tracking, &$notsend, &$link, &$rawlink)  {
    $notsend = 0;
    $rawlink = '';
    $link = '';
    return true;
  }

  public function Paketmarke(string $target, string $doctype, int $docid): void {

  }
  
}
