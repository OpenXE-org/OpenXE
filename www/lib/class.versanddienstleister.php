<?php 
abstract class Versanddienstleister {
  protected int $id;
  protected Application $app;
  protected string $type;
  protected int $projectId;
  protected ?int $labelPrinterId;
  protected ?int $documentPrinterId;
  protected bool $shippingMail;
  protected ?int $businessLetterTemplateId;
  protected object $settings;

  public function __construct(Application $app, ?int $id)
  {
    $this->app = $app;
    if ($id === null || $id === 0)
      return;
    $this->id = $id;
    $row = $this->app->DB->SelectRow("SELECT * FROM versandarten WHERE id=$this->id");
    $this->type = $row['type'];
    $this->projectId = $row['projekt'];
    $this->labelPrinterId = $row['paketmarke_drucker'];
    $this->documentPrinterId = $row['export_drucker'];
    $this->shippingMail = $row['versandmail'];
    $this->businessLetterTemplateId = $row['geschaeftsbrief_vorlage'];
    $this->settings = json_decode($row['einstellungen_json']);
  }

  public function isEtikettenDrucker(): bool {
    return false;
  }

  public function GetAdressdaten($id, $sid)
  {
    $auftragId = $lieferscheinId = $rechnungId = $versandId = 0;
    if($sid==='rechnung')
      $rechnungId = $id;
    if($sid==='lieferschein') {
      $lieferscheinId = $id;
      $auftragId = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id=$lieferscheinId LIMIT 1");
      $rechnungId = $this->app->DB->Select("SELECT id FROM rechnung WHERE lieferschein = '$lieferscheinId' LIMIT 1");
      if($rechnungId <= 0)
        $rechnungId = $this->app->DB->Select("SELECT rechnungid FROM lieferschein WHERE id='$lieferscheinId' LIMIT 1");
    }
    if($sid==='versand')
    {
      $versandId = $id;
      $lieferscheinId = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$versandId' LIMIT 1");
      $rechnungId  = $this->app->DB->Select("SELECT rechnung FROM versand WHERE id='$versandId' LIMIT 1");
      $sid = 'lieferschein';
    }

    if ($auftragId <= 0 && $rechnungId > 0)
      $auftragId = $this->app->DB->Select("SELECT auftragid FROM rechnung WHERE id=$rechnungId LIMIT 1");

    if($sid==='rechnung' || $sid==='lieferschein' || $sid==='adresse')
    {
      $docArr = $this->app->DB->SelectRow("SELECT * FROM `$sid` WHERE id = $id LIMIT 1");

      $addressfields = ['name', 'adresszusatz', 'abteilung', 'ansprechpartner', 'unterabteilung', 'ort', 'plz',
          'strasse', 'land', 'telefon', 'email'];
      $ret = array_filter($docArr, fn($key)=>in_array($key, $addressfields), ARRAY_FILTER_USE_KEY);

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
      $ret['name2'] = $name2;
      $ret['name3'] = $name3;

      $strasse = trim($docArr['strasse']);
      $ret['streetwithnumber'] = $strasse;
      $hausnummer = trim($this->app->erp->ExtractStreetnumber($strasse));
      $strasse = trim(str_replace($hausnummer,'',$strasse));
      $strasse = str_replace('.','',$strasse);

      if($strasse=='')
      {
        $strasse = trim($hausnummer);
        $hausnummer = '';
      }
      $ret['strasse'] = $strasse;
      $ret['hausnummer'] = $hausnummer;
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

    $sql = "SELECT
        lp.bezeichnung,
        lp.menge,
        coalesce(nullif(lp.zolltarifnummer, ''), nullif(rp.zolltarifnummer, ''), nullif(a.zolltarifnummer, '')) zolltarifnummer,
        coalesce(nullif(lp.herkunftsland, ''), nullif(rp.herkunftsland, ''), nullif(a.herkunftsland, '')) herkunftsland,
        coalesce(nullif(lp.zolleinzelwert, '0'), rp.preis *(1-rp.rabatt/100)) zolleinzelwert,
        coalesce(nullif(lp.zolleinzelgewicht, 0), a.gewicht) zolleinzelgewicht,
        lp.zollwaehrung
      FROM lieferschein_position lp
      JOIN artikel a on lp.artikel = a.id
      LEFT JOIN auftrag_position ap on lp.auftrag_position_id = ap.id
      LEFT JOIN rechnung_position rp on ap.id = rp.auftrag_position_id
      WHERE lp.lieferschein = $lieferscheinId
      ORDER BY lp.sort";
    $ret['positions'] = $this->app->DB->SelectArr($sql);

    if($sid==="lieferschein"){
      $standardkg = $this->app->erp->VersandartMindestgewicht($lieferscheinId);
    }
    else{
      $standardkg = $this->app->erp->VersandartMindestgewicht();
    }
    $ret['weight'] = $standardkg;
    return $ret;
  }

  /**
   * Returns an array of additional field definitions to be stored for this module:
   * [
   *   'field_name' => [
   *     'typ' => text(default)|textarea|checkbox|select
   *     'default' => default value
   *     'optionen' => just for selects [key=>value]
   *     'size' => size attribute for text fields
   *     'placeholder' => placeholder attribute for text fields
   *   ]
   * ]
   *
   * @return array
   */
  public function AdditionalSettings(): array {
    return [];
  }

  /**
   * Renders all additional settings as fields into $target
   * @param string $target template placeholder for rendered output
   * @param array $form data for form values (from database or form submit)
   * @return void
   */
  public function RenderAdditionalSettings(string $target, array $form): void
  {
    $fields = $this->AdditionalSettings();
    if($this->app->Secure->GetPOST('speichern'))
    {
      $json = $this->app->DB->Select("SELECT einstellungen_json FROM versandarten WHERE id = '".$this->id."' LIMIT 1");
      $modul = $this->app->DB->Select("SELECT modul FROM versandarten WHERE id = '".$this->id."' LIMIT 1");
      if(!empty($json))
      {
        $json = @json_decode($json, true);
      }else{
        $json = array();
        foreach($fields as $name => $val)
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
      foreach($fields as $name => $val)
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
    $html = '';
   
    foreach($fields as $name => $val) // set missing default values
    {
      if(isset($val['default']) && !isset($form[$name]))
      {
        $form[$name] = $val['default'];
      }
    }
    foreach($fields as $name => $val)
    {
      if(isset($val['heading']))
        $html .= '<tr><td colspan="2"><b>'.html_entity_decode($val['heading']).'</b></td></tr>';

      $html .= '<tr><td>'.($val['bezeichnung'] ?? $name).'</td><td>';
      if(isset($val['replace']))
      {
        switch($val['replace'])
        {
          case 'lieferantennummer':
            $form[$name] = $this->app->erp->ReplaceLieferantennummer(0,$form[$name],0);
            $this->app->YUI->AutoComplete($name, 'lieferant', 1);
            break;
          case 'shop':
            $form[$name] .= ($form[$name]?' '.$this->app->DB->Select("SELECT bezeichnung FROM shopexport WHERE id = '".(int)$form[$name]."'"):'');
            $this->app->YUI->AutoComplete($name, 'shopnameid');
            break;
          case 'etiketten':
            $this->app->YUI->AutoComplete($name, 'etiketten');
            break;
        }
      }
      switch($val['typ'] ?? 'text')
      {
        case 'textarea':
          $html .= '<textarea name="'.$name.'" id="'.$name.'">'.($form[$name] ?? '').'</textarea>';
          break;
        case 'checkbox':
          $html .= '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="1" '.($form[$name] ?? false ? ' checked="checked" ':'').' />';
          break;
        case 'select':
          $html .= $this->app->Tpl->addSelect('return', $name, $name, $val['optionen'], $form[$name]);
          break;
        case 'submit':
          if(isset($val['text']))
            $html .= '<form method="POST"><input type="submit" name="'.$name.'" value="'.$val['text'].'"></form>';
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
          $html .= '<input type="text"'
              .(!empty($val['size'])?' size="'.$val['size'].'"':'')
              .(!empty($val['placeholder'])?' placeholder="'.$val['placeholder'].'"':'')
              .' name="'.$name.'" id="'.$name.'" value="'.(isset($form[$name])?htmlspecialchars($form[$name]):'').'" />';
        break;
      }
      if(isset($val['info']) && $val['info'])
        $html .= ' <i>'.$val['info'].'</i>';
      
      $html .= '</td></tr>';
    }
    $this->app->Tpl->Add($target, $html);
  }

  /**
   * Validate form data for this module
   * Form data is passed by reference so replacements (id instead of text) can be performed here as well
   * @param array $form submitted form data
   * @return array
   */
  public function ValidateSettings(array &$form): array
  {
      return [];
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

  public function Paketmarke(string $target, string $docType, int $docId): void {

  }
  
}
