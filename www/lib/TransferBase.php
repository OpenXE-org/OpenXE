<?php 
class TransferBase {
  /**
   * @var int $id
   */
  public $id;
  /**
   * @var ApplicationCore $app
   */
  public $app;
  /** @var array */
  protected $transferdata;
  /** @var array */
  protected $stucture;
  /** @var array */
  protected $settings;
  /** @var Uebertragungen|null */
  protected $transferModule;

  /**
   * TransferBase constructor.
   *
   * @param Application $app
   * @param int         $id
   */
  public function __construct($app, $id)
  {
    $this->app = $app;
    $this->id = $id;
    $this->getSettings();
  }

  /**
   * @param array $settings
   */
  public function setSettings($settings)
  {
    $this->settings = $settings;
  }

  /**
   * @return array
   */
  public function capability()
  {
    return [
      'functions' => [
        'file_transfer'         => true,
        'send_documents'        => true,
        'send_articles'         => true,
        'send_storage'          => true,
        'send_tracking'         => true,
        'send_sales_report'     => true,
        'receive_documents'     => true,
        'receive_articles'      => true,
        'receive_storage'       => true,
        'receive_tracking'      => true,
        'receive_sales_report'  => true,
      ],
    ];
  }

  /**
   * @param null|Uebertragungen $transferModul
   *
   * @return Uebertragungen|null
   */
  public function getTransferModul($transferModul = null)
  {
    if($transferModul !== null && $transferModul instanceof Uebertragungen) {
      $this->transferModule = $transferModul;
      return $this->transferModule;
    }
    if($this->transferModule === null) {
      $this->transferModule = $this->app->erp->LoadModul('uebertragungen');
    }

    return $this->transferModule;
  }

  /**
   * @param int    $requestId
   * @param string $status
   * @param string $message
   * @param string $doctype
   * @param int    $doctypeId
   * @param int    $fileId
   * @param int    $monitorId
   *
   * @return int
   */
  public function setRequestItemStatus(
    $requestId, $status, $message = '', $doctype = '', $doctypeId = 0, $fileId = 0, $monitorId = 0
  )
  {
    $requestItem = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM api_request WHERE id = %d',
        $requestId
      )
    );
    if(empty($requestItem)) {
      return 0;
    }
    $requestStatus = stripos($status, 'error')!==false?'error':$status;
    $this->app->DB->Update(
      sprintf(
        "UPDATE api_request SET status = '%s' WHERE id = %d",
        $this->app->DB->real_escape_string($requestStatus),
        $requestId
      )
    );
    $transferModule = $this->getTransferModul();
    if($transferModule === null) {
      return 0;
    }

    return $transferModule->AddUbertragungMonitorLog(
      $this->id,
      $fileId,
      $requestId,
      $status,
      $message,
      '',
      '',
      '',
      $doctype,
      $doctypeId,
      $monitorId
    );
  }

  /**
   * @param string $name
   * @param mixed  $value
   *
   * @return mixed|null
   */
  public function setSetting($name, $value) {
    $this->getSettings();
    $this->settings[$name] = $value;
    $oldSettingsString = $this->app->DB->Select(
      sprintf(
        'SELECT `einstellungen_json` FROM `uebertragungen_account` WHERE `id` = %d LIMIT 1',
        $this->id
      )
    );
    $isOldSettingValid = false;
    if(!empty($oldSettingsString)) {
      $oldSettings = json_decode($oldSettingsString, true);
      if(is_array($oldSettings)) {
        $isOldSettingValid = true;
      }
    }
    $this->app->erp->StartChangeLog('uebertragungen_account', $this->id);
    $this->app->DB->Update(
      sprintf(
        "UPDATE `uebertragungen_account` SET `einstellungen_json` = '%s' WHERE `id` = %d LIMIT 1",
        $this->app->DB->real_escape_string(json_encode($this->settings)), $this->id
      )
    );
    $this->app->erp->WriteChangeLog();
    if(!$isOldSettingValid) {
      return $this->getSettings();
    }

    $newSettings = $this->app->DB->Select(
      sprintf(
        'SELECT `einstellungen_json` FROM `uebertragungen_account` WHERE `id` = %d LIMIT 1',
        $this->id
      )
    );

    if(!empty($newSettings)) {
      $newSettings = json_decode($newSettings, true);
      if(is_array($newSettings)) {
        return $this->getSettings();
      }
    }
    $this->app->erp->StartChangeLog('uebertragungen_account', $this->id);
    $this->app->DB->Update(
      sprintf(
        "UPDATE `uebertragungen_account` SET `einstellungen_json` = '%s' WHERE `id` = %d LIMIT 1",
        $this->app->DB->real_escape_string($oldSettingsString), $this->id
      )
    );
    $this->app->erp->WriteChangeLog();

    return $this->getSettings();
  }

  /**
   * @return mixed|null
   */
  public function getSettings()
  {
    if($this->id <= 0)
    {
      return null;
    }
    $this->transferdata = $this->app->DB->SelectRow(
      'SELECT a.*, o.uebertragungen_account_id, o.client_id, o.client_secret, o.expiration_date, o.url, o.access_token 
      FROM `uebertragungen_account` AS `a` 
      LEFT JOIN `uebertragungen_account_oauth` AS `o` ON o.uebertragungen_account_id = a.id 
      WHERE a.id = ' . $this->id
    );
    if(!empty($this->app->DB->error())) {
      $this->transferdata = $this->app->DB->SelectRow(
        'SELECT a.*
        FROM `uebertragungen_account` AS `a`
        WHERE a.id = ' . $this->id
      );
    }
    if(!empty($this->transferdata['einstellungen_json']))
    {
      $this->settings = @json_decode($this->transferdata['einstellungen_json'], true);
    }elseif(!empty($this->stucture)){
      foreach($this->stucture as $name => $val)
      {
        if(isset($val['default']))
        {
          $this->settings[$name] = $val['default'];
        }
      }
    }
    if(empty($this->settings))
    {
      $this->settings = null;
    }

    return $this->settings;
  }

  /**
   * @return array
   */
  public function getTransferData()
  {
    if(empty($this->transferdata)) {
      $this->getSettings();
    }

    return $this->transferdata;
  }

  /**
   * @return null
   */
  public function SettingsStructure()
  {
    return null;
  }

  /**
   * @return array|null
   */
  public function getStructure()
  {
    $this->stucture = $this->SettingsStructure();
    if($this->app->erp->ModulVorhanden('TransferSmarty')) {
      $this->stucture = array_merge($this->stucture, [
        'useincommingconverter' => [
          'typ' => 'checkbox',
          'bezeichnung' => 'Eingangskonverter verwenden',
        ],
        'incommingtemplate' => [
          'typ' => 'textarea',
          'size'=> 40,
          'rows'=> 6,
          'bezeichnung'=>'Eingang Template:',
          'tag' => 'disabled="disabled" style="background-color:#ccc; " ',
          'info' => '<input style="vertical-align: top" type="button" value="bearbeiten" id="editincommingtemplate" />',
        ],
        'incommingdata' => [
          'typ' => 'hidden',
        ],
      ]);
    }

    return $this->stucture;
  }

  /**
   * @param string         $file
   * @param Uebertragungen $transferModul
   *
   * @return string
   */
  public function convertIncomming($file, $transferModul)
  {
    if(!empty($this->settings['useincommingconverter']) && !empty($this->settings['incommingtemplate'])) {
      $obj = $this->app->loadModule('TranferSmarty');
      if(empty($obj) || !method_exists($obj, 'convertIncomingFile')) {
        return $file;
      }
      $obj->setSettings($this->settings);
      try {
        $file = $this->convertIncomingFile($file);
      }
      catch(Exception $e) {
        $fileId = $transferModul->GetFileId($this->id, $file, false);
        $transferModul->AddUbertragungMonitorLog($this->id, $fileId,0,'xml_parseerror', '');
      }
    }

    return $file;
  }

  /**
   * @param string     $target
   * @param null|array $struktur
   *
   * @return string|null
   */
  public function Settings($target = 'return', $struktur = null)
  {
    if(!$this->id)
    {
      return null;
    }
    if($struktur === null){
      $struktur = $this->getStructure();
    } else {
      $this->stucture = $struktur;
    }
    $this->getSettings();
    if($this->app->Secure->GetPOST('speichern'))
    {
      $uebertragungen_account = $this->transferdata;
      $json = $this->settings;
      $modul = $uebertragungen_account['xml_pdf'];
      if(!empty($struktur)){
        foreach ($struktur as $name => $val) {
          if($modul === $this->app->Secure->GetPOST('xml_pdf')){
            $json[$name] = $this->app->Secure->GetPOST($name, '', '', 1);
          }

          if(isset($val['replace'])){
            switch ($val['replace']) {
              case 'lieferantennummer':
                $json[$name] = $this->app->erp->ReplaceLieferantennummer(1, $json[$name], 1);
                break;
              case 'shop':
                $json[$name] = explode(' ', $json[$name]);
                $json[$name] = reset($json[$name]);
                break;
              case 'lagerplatz':
                $json[$name] = $this->app->erp->ReplaceLagerPlatz(1, $json[$name], 1);
                break;
              case 'Layoutvorlage':
                $json[$name] = explode(' ', $json[$name]);
                $json[$name] = (int)reset($json[$name]);
                break;
              case 'artikelnummer':
                $tmp = trim($json[$name]);
                $rest = explode(' ',$tmp);
                $rest = $rest[0];
                $json[$name] =  $this->app->DB->Select(
                  sprintf(
                    "SELECT id 
                    FROM artikel 
                    WHERE nummer='%s' AND nummer!='' AND geloescht=0
                    ORDER BY projekt = %d DESC
                    LIMIT 1",
                    $rest, $this->transferdata['projekt']
                  )
                );
                break;
            }
          }
        }
      }
      $json_str = $this->app->DB->real_escape_string(json_encode($json));
      $this->app->erp->StartChangeLog('uebertragungen_account', $this->id);
      $this->app->DB->Update("UPDATE `uebertragungen_account` SET `einstellungen_json` = '$json_str' WHERE `id` = '".$this->id."' LIMIT 1");
      $this->app->erp->WriteChangeLog();
    }
    $id = $this->id;

    $json = $this->app->DB->Select("SELECT einstellungen_json FROM uebertragungen_account WHERE id = '$id' LIMIT 1");
    if(!empty($json))
    {
      $json = json_decode($json, true);
    }else{
      $json = null;
    }
    
    foreach($struktur as $name => $val) {
      $changed = false;
      if(isset($val['default']) && !isset($json[$name])) {
        $changed = true;
        $json[$name] = $val['default'];
      }
    }
    if(!empty($changed)) {
      $json_str = $this->app->DB->real_escape_string(json_encode($json));
      $this->app->erp->StartChangeLog('uebertragungen_account', $this->id);
      $this->app->DB->Update("UPDATE `uebertragungen_account` SET `einstellungen_json` = '$json_str' WHERE `id` = '".$this->id."' LIMIT 1");
      $this->app->erp->WriteChangeLog();
    }

    if(!empty($struktur)) {
      $html = '</table>
  </fieldset><fieldset class="modulespecific"><legend>{|&Uuml;bertragungenspezifische Einstellungen|}</legend>
    ';
      foreach($struktur as $name => $val) {
        if(empty($val['typ']) || $val['typ'] !== 'hidden') {
          continue;
        }

        $html .= '<input type="hidden" name="'.$name.'" id="'.$name.'" value="'
          .(!isset($json[$name])?'':htmlspecialchars($json[$name], ENT_QUOTES | ENT_HTML5)).'" />';

      }
      $html .= '<table width="100%">
      <tr><td width="300">';
    }
    else{
      $html = '';
    }

    $first = true;
    foreach($struktur as $name => $val) {
      $tdtag = !empty($val['tdtag'])?$val['tdtag']:'';
      $oneCol = !empty($val['onecol']);
      $typ = 'text';
      if(!empty($val['typ'])) {
        $typ = $val['typ'];
        if($typ === 'hidden') {
          continue;
        }
      }
      if(isset($val['heading'])) {
        $html .= '<tr><td colspan="2"><b>'.html_entity_decode($val['heading']).'</b></td></tr>';
      }
      if($oneCol) {
        $html .= '<tr><td valign="top" colspan="2"'.(!empty($tdtag)?' '.$tdtag.' ':'').'>' . ($first ? '<input type="hidden" name="modul_name" value="' . $this->app->DB->Select("SELECT modul FROM versandarten WHERE id = '$id' LIMIT 1") . '" />' : '') . (empty($val['bezeichnung']) ? $name : $val['bezeichnung']) . '<br />';
      }
      else{
        $html .= '<tr><td valign="top">' . ($first ? '<input type="hidden" name="modul_name" value="' . $this->app->DB->Select("SELECT modul FROM versandarten WHERE id = '$id' LIMIT 1") . '" />' : '') . (empty($val['bezeichnung']) ? $name : $val['bezeichnung']) . '</td><td'.(!empty($tdtag)?' '.$tdtag.' ':'').'>';
      }

      $tag = !empty($val['tag'])?$val['tag']:'';

      $placeholder = !empty($val['placeholder'])?$val['placeholder']:'';
      $size = !empty($val['size'])?$val['size']:'';
      $rows = !empty($val['rows'])?$val['rows']:'';

      if(isset($val['replace'])) {
        switch($val['replace']) {
          case 'lieferantennummer':
            $json[$name] = $this->app->erp->ReplaceLieferantennummer(0,$json[$name],0);
            if($target !== 'return')
            {
              $this->app->YUI->AutoComplete($name, 'lieferant', 1);
            }
          break;
          case 'lagerplatz':
            $json[$name] = $this->app->erp->ReplaceLagerPlatz(0, $json[$name], 0);
            if($target !== 'return') {
              $this->app->YUI->AutoComplete($name, 'lagerplatz', 0);
            }
            break;
          case 'artikelnummer':
            $json[$name] = $this->app->erp->ReplaceArtikel(0,$json[$name],0);
            if($target !== 'return') {
              $this->app->YUI->AutoComplete($name, 'artikelnummer', 1);
            }
            break;
          case 'shop':
            $json[$name] .= ($json[$name]?' '.$this->app->DB->Select("SELECT bezeichnung FROM shopexport WHERE id = '".(int)$json[$name]."'"):'');
            if($target !== 'return') {
              $this->app->YUI->AutoComplete($name, 'shopnameid');
            }
          break;
          case 'Layoutvorlage':
            $json[$name] = (int)$json[$name];
            if($json[$name] <= 0) {
              $json[$name] = '';
            }
            else {
              $json[$name] = (string)$this->app->DB->Select(
                sprintf(
                  "SELECT CONCAT(`id`,' ', `name`) FROM `layoutvorlagen` WHERE `id` = %d ",
                  $json[$name]
                )
              );
            }
            if($target !== 'return') {
              $this->app->YUI->AutoComplete($name, 'layoutvorlage');
            }
            break;
          case 'etiketten':
            if($target !== 'return') {
              $this->app->YUI->AutoComplete($name, 'etiketten');
            }
          break;
        }
      }

      switch($typ) {
        case 'textarea':
          $html .= '<textarea '.(!empty($tag)?' '.$tag.' ':'').(!empty($size)?' cols="'.$size.'" ':'').' '.(!empty($rows)?' rows="'.$rows.'" ':'').' '.(!empty($placeholder)?' placeholder="'.$placeholder.'" ':'').' name="'.$name.'" id="'.$name.'">'.(!isset($json[$name])?'':$json[$name]).'</textarea>';
        break;
        case 'checkbox':
          $html .= '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="1" '.((isset($json[$name]) && $json[$name])?' checked="checked" ':'').' />';
        break;
        case 'select':
          $html .= '<select name="'.$name.'">';
          if(isset($val['optionen']) && is_array($val['optionen'])) {
            foreach($val['optionen'] as $k => $v) {
              $html .= '<option value="'.$k.'"'.($k == (isset($json[$name])?$json[$name]:'')?' selected="selected" ':'').'>'.$v.'</option>';
            }
          }
          $html .= '</select>';
        break;
        case 'submit':
          if(isset($val['text'])) {
            $html .= '<form method="POST"><input type="submit" name="'.$name.'" value="'.$val['text'].'"></form>';
          }
        break;
        case 'custom':
          if(isset($val['function'])) {
            $tmpfunction = $val['function'];
            if(method_exists($this, $tmpfunction)) {
              $html .= $this->$tmpfunction();
            }
          }
        break;
        default:
          if($typ === 'time' || $typ === 'zeit') {
            $this->app->YUI->TimePicker($name);
          }elseif($typ === 'date' || $typ === 'datum') {
            $this->app->YUI->DatePicker($name);
          }
          $html .= '<input '.(!empty($placeholder)?' placeholder="'.$placeholder.'" ':'').' type="text" '.(!empty($val['size'])?' size="'.$val['size'].'" ':'').' name="'.$name.'" id="'.$name.'" value="'.(!isset($json[$name])?'':$json[$name]).'" />';
        break;
      }
      if(isset($val['info']) && $val['info'])
      {
        $html .= ' <i>'.$val['info'].'</i>';
      }
      
      $html .= '</td></tr>';
      $first = false;
    }
    
    if($target !== 'return'){
      $this->app->Tpl->Add($target, $html);
    }
    return $html;
  }
}
