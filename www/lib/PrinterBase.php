<?php

class PrinterBase
{
  /** @var int $id */
  public $id;
  /** @var ApplicationCore $app */
  public $app;

  /** @var array */
  protected $printerdata;
  /** @var array */
  protected $stucture;
  /** @var array */
  protected $settings;

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
   * @return mixed|null
   */
  public function getSettings()
  {
    if($this->id <= 0) {
      return null;
    }
    $this->printerdata = $this->app->DB->SelectRow('SELECT * FROM drucker WHERE id = ' . $this->id);
    if(!empty($this->printerdata['json'])) {
      $this->settings = @json_decode($this->printerdata['json'], true);
    }
    elseif(!empty($this->stucture)){
      foreach($this->stucture as $name => $val) {
        if(isset($val['default'])) {
          $this->settings[$name] = $val['default'];
        }
      }
    }
    if(empty($this->settings)) {
      $this->settings = null;
    }

    return $this->settings;
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
    $this->app->DB->Update(
      sprintf(
        'UPDATE drucker SET json = \'%s\' WHERE id = %d LIMIT 1',
        $this->app->DB->real_escape_string(json_encode($this->settings)), $this->id
      )
    );

    return $this->getSettings();
  }


  /**
   * @return array
   */
  public function getPrinterData()
  {
    if(empty($this->printerdata)) {
      $this->getSettings();
    }

    return $this->printerdata;
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

    return $this->stucture;
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
    if($this->app->Secure->GetPOST('anbindung')) {
      $printer = $this->printerdata;
      $json = $this->settings;
      $modul = $printer['anbindung'];
      if(!empty($struktur)) {
        foreach ($struktur as $name => $val) {
          if($modul === $this->app->Secure->GetPOST('anbindung')){
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
                    $rest, $this->printerdata['projekt']
                  )
                );
                break;
            }
          }
        }
      }
      $json_str = $this->app->DB->real_escape_string(json_encode($json));
      $this->app->DB->Update("UPDATE drucker SET json = '$json_str' WHERE id = '".$this->id."' LIMIT 1");
    }
    $id = $this->id;
    if(!empty($struktur)) {
      $html = '</table>
  </fieldset><fieldset class="modulespecific"><legend>{|Druckerspezifische Einstellungen|}</legend>
    <table width="100%">
      <tr><td width="300">';
    }else{
      $html = '';
    }
    $json = $this->app->DB->Select("SELECT json FROM drucker WHERE id = '$id' LIMIT 1");
    if(!empty($json)) {
      $json = json_decode($json, true);
    }
    else {
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
      $this->app->DB->Update("UPDATE drucker SET json = '$json_str' WHERE id = '".$this->id."' LIMIT 1");
    }
    $first = true;
    foreach($struktur as $name => $val) {
      $tdtag = !empty($val['tdtag'])?$val['tdtag']:'';
      $oneCol = !empty($val['onecol']);
      $typ = 'text';
      if(!empty($val['typ'])) {
        $typ = $val['typ'];
      }
      if($typ === 'hidden') {

      }
      else{
        if(isset($val['heading'])){
          $html .= '<tr><td colspan="2"><b>' . html_entity_decode($val['heading']) . '</b></td></tr>';
        }
        if($oneCol){
          $html .= '<tr><td valign="top" colspan="2"' . (!empty($tdtag) ? ' ' . $tdtag . ' ' : '') . '>' . ($first ? '<input type="hidden" name="modul_name" value="' . $this->app->DB->Select("SELECT modul FROM versandarten WHERE id = '$id' LIMIT 1") . '" />' : '') . (empty($val['bezeichnung']) ? $name : $val['bezeichnung']) . '<br />';
        }else{
          $html .= '<tr><td valign="top">' . ($first ? '<input type="hidden" name="modul_name" value="' . $this->app->DB->Select("SELECT modul FROM versandarten WHERE id = '$id' LIMIT 1") . '" />' : '') . (empty($val['bezeichnung']) ? $name : $val['bezeichnung']) . '</td><td' . (!empty($tdtag) ? ' ' . $tdtag . ' ' : '') . '>';
        }
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
          case 'etiketten':
            if($target !== 'return')
            {
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
        case 'hidden':
          $html .= '<input '.(!empty($placeholder)?' placeholder="'.$placeholder.'" ':'').' type="hidden" '.(!empty($val['size'])?' size="'.$val['size'].'" ':'').' name="'.$name.'" id="'.$name.'" value="'.(!isset($json[$name])?'':$json[$name]).'" />';
          break;
        default:
          if($typ === 'time' || $typ === 'zeit') {
            $this->app->YUI->TimePicker($name);
          }
          elseif($typ === 'date' || $typ === 'datum') {
            $this->app->YUI->DatePicker($name);
          }
          $html .= '<input '.(!empty($placeholder)?' placeholder="'.$placeholder.'" ':'').' type="text" '.(!empty($val['size'])?' size="'.$val['size'].'" ':'').' name="'.$name.'" id="'.$name.'" value="'.(!isset($json[$name])?'':$json[$name]).'" />';
          break;
      }
      if(isset($val['info']) && $val['info']) {
        $html .= ' <i>'.$val['info'].'</i>';
      }
      if($typ === 'hidden') {
        continue;
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