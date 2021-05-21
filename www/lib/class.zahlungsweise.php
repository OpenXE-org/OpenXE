<?php 
class Zahlungsweisenmodul {
  public $id;
  public $app;

  /**
   * @param string $target
   *
   * @return string|void
   */
  public function Einstellungen($target = 'return')
  {
    if(!$this->id) {
      return;
    }
    $struktur = $this->EinstellungenStruktur();
    if($this->app->Secure->GetPOST('speichern')) {
      foreach($struktur as $name => $val) {
        $json[$name] = $this->app->Secure->GetPOST($name, '','', 1);
        if(isset($val['replace'])) {
          switch($val['replace']) {
            case 'lieferantennummer':
              $json[$name] = $this->app->erp->ReplaceLieferantennummer(1,$json[$name],1);
            break;
          }
        }
      }
      $json_str = $this->app->DB->real_escape_string(json_encode($json));
      $this->app->DB->Update(
        sprintf(
          "UPDATE `zahlungsweisen` SET `einstellungen_json` = '%s' WHERE `id` = %d LIMIT 1",
          $json_str, $this->id
        )
      );
    }
    $modul = $this->app->DB->Select(
      sprintf(
        'SELECT z.modul FROM `zahlungsweisen` AS `z` WHERE z.id = %d LIMIT 1',
        $this->id
      )
    );

    $id = $this->id;
    $html = '</table></fieldset>';
    $html .= '<fieldset><legend>'.$modul.' Einstellungen</legend><table class=mkTableFormular>';
   
    $json = $this->app->DB->Select(
      sprintf(
        'SELECT z.einstellungen_json FROM `zahlungsweisen` AS `z` WHERE z.id = %d LIMIT 1',
        $id
      )
    );
    if(!empty($json)) {
      $json = json_decode($json, true);
    }
    foreach($struktur as $name => $val) {
      $html .= '<tr><td>'.(empty($val['bezeichnung'])?$name:$val['bezeichnung']).'</td><td>';
      $typ = 'text';
      if(!empty($val['typ'])) {
        $typ = $val['typ'];
      }
      if(isset($val['replace'])) {
        switch($val['replace']) {
          case 'lieferantennummer':
            $json[$name] = $this->app->erp->ReplaceLieferantennummer(0,$json[$name],0);
            if($target !== 'return') {
              $this->app->YUI->AutoComplete($name, 'lieferant', 1);
            }
          break;
          case 'etiketten':
            //$json[$name] = $this->app->erp->ReplaceLieferantennummer(0,$json[$name],0);
            if($target !== 'return') {
              $this->app->YUI->AutoComplete($name, 'etiketten');
            }
          break;
        }
      }
      switch($typ) {
        case 'textarea':
          $lang = '';
          if(!empty($val['lang'])) {
            $lang = ' data-lang="'.htmlspecialchars($val['lang']).'" ';
          }
          $html .= '<textarea name="'.$name.'" id="'.$name.'"'.$lang.'>'
            .(!isset($json[$name])?'':$json[$name]).'</textarea>';
        break;
        case 'checkbox':
          $html .= '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="1" '
            .((isset($json[$name]) && $json[$name])?' checked="checked" ':'').' />';
        break;
        case 'select':
          $html .= '<select name="'.$name.'">';
          if(isset($val['optionen']) && is_array($val['optionen']))
          {
            foreach($val['optionen'] as $k => $v)
            {
              $html .= '<option value="'.$k.'"'
                .($k == (isset($json[$name])?$json[$name]:'')?' selected="selected" ':'').'>'
                .$v.'</option>';
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
          $lang = '';
          if(!empty($val['lang'])) {
            $lang = ' data-lang="'.htmlspecialchars($val['lang']).'" ';
          }
          $html .= '<input type="text" name="'.$name.'" id="'.$name.'" value="'
            .(!isset($json[$name])?'':$json[$name]).'" size="'.$val['size'].'" '.$lang.' />';
        break;
      }
      if(isset($val['info']) && $val['info']) {
        $html .= ' <i>'.$val['info'].'</i>';
      }
      
      $html .= '</td></tr>';
    }
    
    if($target === 'return') {
      return $html;
    }
    $this->app->Tpl->Add($target, $html);
  }
  
}
