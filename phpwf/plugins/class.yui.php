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

use Xentral\Modules\SystemConfig\SystemConfigModule;

class YUI {
  var $anzusersaves = 0;
  var $tageditorloaded = false;
  /** @var Application $app */
  var $app;

  /** @propery Application $app */
  function __construct($app) {

    $this->app = $app;
  }

  function PasswordCheck($passwordFieldID, $repassFieldID, $accountNameFieldID, $submitButtonID, $extra = ''){
        $this->app->Tpl->Add('JQUERYREADY', "
                function checkPassword(){
      var password = $('#$passwordFieldID').val();
                        var message = '';
                        if(password.length < 8) message += '{|Mindestens 8 Zeichen|}<br>';
      $extra
      if('$accountNameFieldID' != '')
        if(password.includes($('#$accountNameFieldID').val())) message += 'Darf nicht den Benutzernamen enthalten<br>';

      var equal = comparePasswords();
      var secure = (message == '') && equal;
      setButtonActive(secure);
      if(message != ''){
        setSemaphore(0);
      }else if(  /[0-9]/.test(password) &&
           /[A-Z]/.test(password) &&
           /[a-z]/.test(password)){
        setSemaphore(2);
      }else{
        setSemaphore(1);
      }

      $('#passwordInfo').html(message);

                }

    function runCheck(){
      checkPassword();
    }

    function setSemaphore(level){
      var colors = ['red', 'yellow', 'green'];
      $('#semaphore').prop('src', './themes/new/images/semaphore_' + colors[level] + '.png');
    }
    
    function setButtonActive(active){
      if('$submitButtonID' == '') return;
      var button = $('#$submitButtonID');
      button.css('opacity', (active ? '1.0' : '0.5'));
      if(!active){
        button.prop('disabled','disabled');
      }else{
        button.removeAttr('disabled');
      }
    }

    function comparePasswords(){
      if('$repassFieldID' == '') return true;
      var equal = $('#$passwordFieldID').val() == $('#$repassFieldID').val();
      
      $('#repasswordInfo').html(equal ? '' : 'Passw&oumlrter stimmen nicht &uumlberein');
      return equal;
      
    }

    var passwordField = $('#$passwordFieldID');
    passwordField.css('display: inline-block');
                passwordField.bind('input propertychange', runCheck);
    passwordField.parent().append('<img height=\"10\" id=\"semaphore\">');
    passwordField.parent().append('<p id=\"passwordInfo\" style=\"color: red; display: inline-block\"></p>');
  
    if('$repassFieldID' != ''){ 
      var repassField = $('#$repassFieldID');
      repassField.parent().append('<p id=\"repasswordInfo\" style=\"color: red; display: inline-block\"></p>');
                  repassField.bind('input propertychange', runCheck);
    }

    
    if('$accountNameFieldID' != ''){  
      $('#$accountNameFieldID').bind('input propertychange', runCheck);
    }

    

    runCheck();
                        ");
  }


  function BundeslaenderSelect($target, $landid, $bundeslandid, $valueland = '', $valuebundesland = '', $disabled = false)
  {
    $obj = $this->app->erp->LoadModul('bundesstaaten');
    if($obj && method_exists($obj, 'BundeslaenderSelect'))
      return $obj->BundeslaenderSelect($target, $landid, $bundeslandid, $valueland, $valuebundesland, $disabled);
  }
  
  function Stroke($fieldstroke, $field) {
    return "if(" . $fieldstroke . ",CONCAT('<s>'," . $field . ",'</s>')," . $field . ")";
  }

  function Redify($decisionField, $field){
    return "if(" . $decisionField . ",CONCAT('<a style=\"color:red\">'," . $field . ",'</a>')," . $field . ")";
  }

  function TagEditor($element = '', $optionen = null)
  {
    if(!$this->tageditorloaded)
    {
      $this->tageditorloaded = true;
      $this->app->Tpl->Add('SCRIPTJAVASCRIPT','<script type="text/javascript" language="javascript" src="./js/jquery.tag-editor.min.js"></script>');
      $this->app->Tpl->Add('SCRIPTJAVASCRIPT','<link rel="stylesheet" type="text/css" href="./css/jquery.tag-editor.css">');
    }
    if($element != '')
    $this->app->Tpl->Add('AUTOCOMPLETE','
    $(\'#'.$element.'\').tagEditor();
    ');
    if($optionen)
    {
      if(isset($optionen['width']))
      {
        $this->app->Tpl->Add('YUICSS', '
        input#'.$element.' + ul.tag-editor { width: '.$optionen['width'].(is_numeric($optionen['width'])?'px':'').'; }
        ');
      }
    }
  }

  function DateiPopup($target, $typ, $parameter, $optionen = null)
  {
    if($optionen) {
      if(isset($optionen['onopen']))$this->app->Tpl->Set('ONOPEN', $optionen['onopen']);
      if(isset($optionen['onclose']))$this->app->Tpl->Set('ONCLOSE', $optionen['onclose']);

      if(isset($optionen['frompopup']))
      {
        $this->app->Tpl->Add('ONOPEN','$(\'#'.$optionen['frompopup'].'\').dialog(\'close\');');
        $this->app->Tpl->Add('ONCLOSE','$(\'#'.$optionen['frompopup'].'\').dialog(\'open\');');
        $this->app->Tpl->Set('FROMPOPUP', $optionen['frompopup']);
      }
      if(isset($optionen['afteropen']))$this->app->Tpl->Add($optionen['afteropen'],'updatefilecount();');
    }
    $this->app->Tpl->Set('TYP', $typ);
    
    if(is_numeric($parameter))
    {
      $this->app->Tpl->Set('VORTYPID1','//');
    }else{
      $this->app->Tpl->Set('VORTYPID2','//');
    }
    $this->app->Tpl->Set('TYPID', $parameter);
    if(isset($optionen['openbuttontarget'])) {
      $this->app->Tpl->Set('VORDATEIENBUTTON', '<!--');
      $this->app->Tpl->Set('NACHDATEIENBUTTON', '-->');
      $this->app->Tpl->Set($optionen['openbuttontarget'], '\'{|DATEIEN|}\': function() {
        opendateipopup();
      },');
      $this->app->Tpl->Set('ISPOPUPBUTTON','true');
    }else $this->app->Tpl->Set('ISPOPUPBUTTON','false');

    $this->app->Tpl->Parse($target, 'datei_neudirekt_popup_iframe.tpl');
  }

  function ContentTooltip($template, $daten, $typ = 'html')
  {
    $tmpid = md5(microtime(true).mt_rand(0,1000));
    $ret = "<img class=\"imgtooltip\" onmouseover=\"mouseover_".$tmpid."();\" onmouseleave=\"mouseleave_".$tmpid."();\" src=\"./themes/".$this->app->Conf->WFconf['defaulttheme']."/images/tooltip_grau.png\" />";
    if($typ == 'html')
    {
      $ret .= "<div style=\"display:none;\" id=\"contentbox_".$tmpid."\">'.$daten.'</div>";
    }elseif($typ == 'url')
    {
      $ret .= "<div style=\"display:none;\" id=\"contentbox_".$tmpid."\"></div>";
    }
    $ret .= '<style>
    #contentbox_'.$tmpid.'{
      display: none;
      position: fixed;
    }
    .inlinetooltiptable {
      z-index: 9999;
      position: absolute;
      top: 0;
      left: -20px;
      background-color: #fff;
      border: 2px solid rgb(166, 201, 226);
      margin: 10px;
      padding: 10px;
      min-width: 160px;
    }
    </style><script>'."\r\n".'
    function mouseover_'.$tmpid.'()
    {
      var $box = $(\'#contentbox_'.$tmpid.'\').show().css(\'display\',\'inline-block\');
      
      /* Abstand von oben berechnen; CSS-Lösung funktioniert nicht, da position:fixed notwendig */
      var $image = $box.parent();
      var windowTop = $(window).scrollTop();
      var imageTop = $image.offset().top;
      var correctedTopPos = imageTop - windowTop + 20;
      $box.css(\'top\', correctedTopPos + \'px\');
      
      /* Tooltip am rechten Rand ausrichten, wenn Tooltip aus dem Viewport ragt. */
      var $table = $box.find(\'.inlinetooltiptable\');
      var boxWidth = $table.outerWidth();
      var boxPosLeft = $box.offset().left;
      var boxPosRight = boxPosLeft + boxWidth;
      var windowWidth = $(window).width();
      if (boxPosRight + 20 > windowWidth) {
        var boxOffCanvas = parseInt(boxPosRight - windowWidth);
        if (boxOffCanvas > 0) {
          var correctedLeftPos = (boxOffCanvas * -1) - 22;
          $table.css(\'left\', correctedLeftPos + \'px\');
        }
      }
    }
    function mouseleave_'.$tmpid.'()
    {
      $(\'#contentbox_'.$tmpid.'\').hide();
    }
    ';
    if($typ == 'html')
    {
      
    }elseif($typ == 'url')
    {
      $ret .= '
      $(document).ready(function() {
        $.ajax({
          url: \''.$daten.'\',
          data: {},
          method: \'post\',
          dataType: \'json\',
          success: function(data) {
          $(\'#contentbox_'.$tmpid.'\').html(data.inhalt);
          }
        });
      });
      ';
    }
    $ret .= '</script>';
    if($template == 'return')return $ret;
    $this->app->Tpl->Add($template, $ret);
  }
 
  
  function MassenbearbeitungsWidget($name,$typ = 'artikel',$parameter = null, $target = 'PAGE')
  {
    if($this->app->erp->RechteVorhanden('massenbearbeitung','edit'))
    {
      if(!class_exists('Massenbearbeitung'))
      {
        if(is_file(dirname(dirname(__DIR__)).'/www/pages/massenbearbeitung_custom.php'))
        {
          include_once(dirname(dirname(__DIR__)).'/www/pages/massenbearbeitung_custom.php');
          if(class_exists('MassenbearbeitungCustom'))
            $obj = new MassenbearbeitungCustom($this->app,true);
        }elseif(is_file(dirname(dirname(__DIR__)).'/www/pages/massenbearbeitung.php'))
        {
          include_once(dirname(dirname(__DIR__)).'/www/pages/massenbearbeitung.php');
          if(class_exists('Massenbearbeitung'))
            $obj = new Massenbearbeitung($this->app,true);
        }
      }
      if(isset($obj) && $obj)
      {
        if(method_exists($obj,'Widget'))
          $obj->Widget($name, $typ, $parameter, $target);
      }
    }
  }
  
  function SaveReally($formid = '')
  {
    if($formid)
    {
      $selector = '#'.$formid;
    }else{
      $selector = 'form';
      $formid = 'all';
    }
    
    $this->app->Tpl->Add('JQUERYREADY','
    var changed'.$formid.' = false;
    $(\''.$selector.' input[type="checkbox"]\').bind(\'change\',function(){
      changed'.$formid.' = true;
    });
    $(\''.$selector.' input[type="text"]\').bind(\'change\',function(){
      changed'.$formid.' = true;
    });
    $(\''.$selector.' input[type="radio"]\').bind(\'change\',function(){
      changed'.$formid.' = true;
    });
    $(\''.$selector.' input[type="textarea"]\').bind(\'change\',function(){
      changed'.$formid.' = true;
    });
    $(\''.$selector.' select\').bind(\'change\',function(){
      changed'.$formid.' = true;
    });

    $(\''.$selector.' input[type="submit"]\').bind(\'click\',function(){
      changed'.$formid.' = false;
    });
    
    $(\''.$selector.'\').bind(\'submit\',function(){
      changed'.$formid.' = false;
    });
    
    $(window).bind(\'beforeunload\', function(){
      if(changed'.$formid.')return confirm(\'Sie haben die Daten geändert, wollen Sie diese Seite wirklich ohne speichern verlassen\');
    });');
  }
  
  function AddDialog($element,  $template,$name = '', $target = 'PAGE', $optionen = null)
  {
    $weiter = 'SPEICHERN';
    $afterclose = '';
    $onweiter = '$(this).dialog(\'close\');';
    $width = '90%';
    $left = '5%';
    if(!empty($optionen))
    {
      if(isset($optionen['weiter']))$weiter = $optionen['weiter'];
      if(!empty($optionen['afterclose']))$weiter = $optionen['afterclose'];
      if(!empty($optionen['onweiter']))$onweiter = $optionen['onweiter'];
      if(isset($optionen['left']))$left = $optionen['left'];
      if(isset($optionen['width']))$width = $optionen['width'];
    }
    
    $this->app->Tpl->Add($target,'<div id="'.$element.'">');
    if($template)$this->app->Tpl->Parse($target,$template);
    $this->app->Tpl->Add($target,'</div>');
    $this->app->Tpl->Add('JQUERYREADY','    
     $(\'#'.$element.'\').dialog(
        {
          modal: true,
          autoOpen: false,
          minWidth: 1200,
          title:\''.$name.'\',
          buttons: {
            ');
            if($weiter)
            {
              $this->app->Tpl->Add('JQUERYREADY','     '.$weiter.': function() {
                  '.$onweiter.'
                },');
            }
        $this->app->Tpl->Add('JQUERYREADY',' ABBRECHEN: function() {
              $(this).dialog(\'close\');
            }
          },
          close: function(event, ui){
            '.$afterclose.'
          }
        });
        $(\'#'.$element.'\').on("dialogopen",function(){
         ');
        if($width)
        {
          $this->app->Tpl->Add('JQUERYREADY', '$(\'#'.$element.'\').parent().css(\'width\',\''.$width.'\');');
        }
        if($left)
        {
          $this->app->Tpl->Add('JQUERYREADY', '$(\'#'.$element.'\').parent().css(\'left\',\''.$left.'\');');
        }
          
        $this->app->Tpl->Add('JQUERYREADY',   '
        });
    ');
    
    
  }

  function AARLGEditable() {

    $module = $this->app->Secure->GetGET("module");
    $table = $this->AARLGPositionenModule2Tabelle();
    $id = $this->app->Secure->GetPOST("id", 'nohtml'); //ACHTUNG auftrag_positions tabelle id
    
    $tmp = explode('split', $id);
    $id = $tmp[0];
    $column = $tmp[1];
    $value = $this->app->Secure->GetPOST("value");
    $cmd = $this->app->Secure->GetGET("cmd");
    $column = $column - 1;
    $this->app->erp->RunHook('AARLGEditable', 5,$module, $id, $value, $cmd, $column);
    if ($module == "arbeitsnachweis") {
      
      switch ($column) {
        case 1: // ort

          $this->app->DB->Update("UPDATE $table SET ort='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT ort FROM $table WHERE id='$id' LIMIT 1");
        break;
        case 2: // Datum

          $value = $this->app->String->Convert($value, "%1.%2.%3", "%3-%2-%1");
          $this->app->DB->Update("UPDATE $table SET datum='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT datum FROM $table WHERE id='$id' LIMIT 1");
          $result = $this->app->String->Convert($result, "%3-%2-%1", "%1.%2.%3");
        break;
        case 3: // von

          $this->app->DB->Update("UPDATE $table SET von='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT von FROM $table WHERE id='$id' LIMIT 1");
        break;
        case 4: // bis

          $this->app->DB->Update("UPDATE $table SET bis='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT bis FROM $table WHERE id='$id' LIMIT 1");
        break;
        case 5: //bezeichnung

          $this->app->DB->Update("UPDATE $table SET bezeichnung='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT bezeichnung FROM $table WHERE id='$id' LIMIT 1");
        break;
        default:;
      }
    } else 
    if ($module == "reisekosten") {
      
      switch ($column) {
        case 0: //Datum

          $value = $this->app->String->Convert($value, "%1.%2.%3", "%3-%2-%1");
          $this->app->DB->Update("UPDATE $table SET datum='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT datum FROM $table WHERE id='$id' LIMIT 1");
          $result = $this->app->String->Convert($result, "%3-%2-%1", "%1.%2.%3");
        break;
        case 2: // Betrag
          $value = $this->app->erp->FromFormatZahlToDB($value);
          if(strpos($value,','))
          {
            $value = str_replace(",", ".",str_replace('.','', $value));
          }else{           
            $value = str_replace(",", ".", $value);
          }
          $this->app->DB->Update("UPDATE $table SET betrag='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT ".$this->FormatPreis('betrag')." FROM $table WHERE id='$id' LIMIT 1");
        break;
        case 6: // bezeichnung

          $this->app->DB->Update("UPDATE $table SET bezeichnung='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT bezeichnung FROM $table WHERE id='$id' LIMIT 1");
        break;
        default:;
      }
    } else  
    if ($module == "kalkulation") {
      
      switch ($column) {
        case 1: // bezeichnung
          $this->app->DB->Update("UPDATE $table SET bezeichnung='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT bezeichnung FROM $table WHERE id='$id' LIMIT 1");
        break;

        case 2: // Menge
          $value = $this->app->erp->FromFormatZahlToDB($value);
          $value = str_replace(",", ".", $value);
          $this->app->DB->Update("UPDATE $table SET menge='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT ".$this->app->erp->FormatMenge("menge")." FROM $table WHERE id='$id' LIMIT 1");
        break;
       
        case 3: // Betrag
          $value = $this->app->erp->FromFormatZahlToDB($value);
          if(strpos($value,','))
          {
            $value = str_replace(",", ".",str_replace('.','', $value));
          }else{           
            $value = str_replace(",", ".", $value);
          }
          $this->app->DB->Update("UPDATE $table SET betrag='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT ".$this->FormatPreis('betrag')." FROM $table WHERE id='$id' LIMIT 1");
        break;
        default:;
      }
    } 


    else 
    if ($module == "inventur") {
      
      switch ($column) {
        case 0: //Bezeichnung

          $this->app->DB->Update("UPDATE $table SET bezeichnung='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT bezeichnung FROM $table WHERE id='$id' LIMIT 1");
        break;
        case 2: // Nummer

          $this->app->DB->Update("UPDATE $table SET nummer='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT nummer FROM $table WHERE id='$id' LIMIT 1");
        break;
        case 3: // Menge
          $value = $this->app->erp->FromFormatZahlToDB($value);
          if($value < 0 ) $value=1;
          $this->app->DB->Update("UPDATE $table SET menge='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT  ".$this->app->erp->FormatMenge("menge")."  FROM $table WHERE id='$id' LIMIT 1");
        break;
        case 4: // preis
          $value = $this->app->erp->FromFormatZahlToDB($value);
          if(strpos($value,','))
          {
            $value = str_replace(",", ".",str_replace('.','', $value));
          }else{           
            $value = str_replace(",", ".", $value);
          }
          $this->app->DB->Update("UPDATE $table SET preis='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT ".$this->FormatPreis('preis')." FROM $table WHERE id='$id' LIMIT 1");
        break;
        default:;
      }
    } else 
    if ($module == "produktion") {
    } else {
      
      switch ($column) {
        case 3: // Datum

          $value = $this->app->String->Convert($value, "%1.%2.%3", "%3-%2-%1");
          $this->app->DB->Update("UPDATE $table SET lieferdatum='$value' WHERE id='$id' LIMIT 1");
          $result = $this->app->DB->Select("SELECT lieferdatum FROM $table WHERE id='$id' LIMIT 1");
          $result = $this->app->String->Convert($result, "%3-%2-%1", "%1.%2.%3");
        break;
        case 4: // Menge
          $value = $this->app->erp->FromFormatZahlToDB($value);
          $value = str_replace(',', '.', $value);
          if($value < 0 ) {
            $value=1;
          }
          $value = $this->app->erp->PruefeMengeVPE($table, $id, $value);
          //if ($table === 'bestellung_position') {

            // schau was mindestmenge bei diesem lieferant ist
            
            //$tmpartikel = $this->app->DB->Select("SELECT artikel FROM $table WHERE id='$id' LIMIT 1");
          //}
          $tmptable_value = $this->app->DB->Select(
            sprintf(
              'SELECT `%s` FROM `%s` WHERE id = %d LIMIT 1',
              $module, $table, $id
            )
          );
          // bei stuecklisten auch unterpositionen anmelden
          $altemenge = $this->app->DB->Select(
            sprintf(
              'SELECT menge FROM `%s` WHERE id=%d LIMIT 1',
              $table, $id
            )
          );
          $this->app->DB->Update(
            sprintf(
              "UPDATE `%s` SET menge=%f WHERE id=%d LIMIT 1",
              $table, (float)$value, $id
            )
          );
          $changed = $this->app->DB->affected_rows() > 0;
          $this->app->DB->Update(
            sprintf(
              'UPDATE `%s` 
              SET menge=menge * %f 
              WHERE explodiert_parent=%d AND `%s` = %d',
              $table, ($value/$altemenge), $id, $module, $tmptable_value
            )
          );

          $changed = $changed || $this->app->DB->affected_rows() > 0;
          $result = $this->app->DB->Select(
            sprintf(
              'SELECT %s FROM `%s` WHERE id = %d LIMIT 1',
              $this->app->erp->FormatMenge('menge'),$table, $id
            )
          );

          $changePrice = $this->app->erp->Firmendaten('position_quantity_change_price_update');
          if($changed && $changePrice
            && in_array($module, array('auftrag','rechnung','gutschrift','angebot'))) {
            $tableArr = $this->app->DB->SelectRow(
              sprintf('SELECT * FROM `%s` WHERE id = %d LIMIT 1', $table, $id)
            );
            $docArr = $this->app->DB->SelectRow(
              sprintf('SELECT * FROM `%s` WHERE id = %d LIMIT 1', $module, $tableArr[$module])
            );
            $waehrung = !empty($tableArr['waehrung'])?$tableArr['waehrung']:$docArr['waehrung'];
            if(empty($waehrung)) {
              $waehrung = 'EUR';
            }
            if($module==='inventur'){
              $newPrice = $this->app->erp->GetEinkaufspreis($tableArr['artikel'], $value, $docArr['adresse']);
            }
            else {
              if(method_exists($this->app->erp, 'GetVerkaufspreisMitWaehrung')) {
                $newPrice = $this->app->erp->GetVerkaufspreisMitWaehrung(
                  $tableArr['artikel'],
                  $value,
                  $docArr['adresse'],
                  $waehrung
                );
              }
              else{
                $newPrice = $this->app->erp->GetVerkaufspreis(
                  $tableArr['artikel'],
                  $value,
                  $docArr['adresse'],
                  $waehrung
                );
              }
            }

            if(!empty($newPrice)) {
              $this->app->DB->Update(
                sprintf(
                  'UPDATE `%s` SET preis = %f WHERE id = %d LIMIT 1',
                  $table, $newPrice, $id
                )
              );
            }
          }


          if(in_array($module, array('auftrag','rechnung','gutschrift'))) {
            $this->app->DB->Update(
              sprintf(
                'UPDATE `%s` SET extsoll = 0 WHERE id = %d LIMIT 1',
                $module, $tmptable_value
              )
            );
          }
          // Menge im Lager reserviert anpassen
                break;
        case 5: //preis
          $value = $this->app->erp->FromFormatZahlToDB($value);
          if(strpos($value,','))
          {
            $value = str_replace(",", ".",str_replace('.','', $value));
          }else{           
            $value = str_replace(",", ".", $value);
          }
          $join = "";
          $preiscell = 'b.preis';
          if($module == 'auftrag' || $module == 'rechnung' || $module == 'gutschrift' || $module == 'angebot' || $module == 'proformarechnung')
          {
            $parent = $this->app->DB->Select("SELECT $module FROM $table WHERE id='$id' LIMIT 1");
            $anrede = $this->app->DB->Select("SELECT typ FROM $module WHERE id = '$parent' LIMIT 1");
            $projekt = $this->app->DB->Select("SELECT projekt FROM $module WHERE id = '$parent' LIMIT 1");
            $adresse = $this->app->DB->Select("SELECT adresse FROM $module WHERE id = '$parent' LIMIT 1");
            $funktion = ucfirst($module).'MitUmsatzeuer';
            $anrede = 'firma';
            if(!$this->app->erp->AnzeigeBelegNettoAdresse($anrede, $module, $projekt, $adresse,$parent) && $this->app->erp->$funktion($parent))
            {
              $steuersatz = $this->app->erp->GetSteuersatzBelegpos($module, $parent, $id);
              $value = $value / (1+(float)$steuersatz / 100);
              $umsatzsteuer_ermaessigt = (float)$this->app->DB->Select("SELECT steuersatz_ermaessigt FROM $module WHERE id = '$parent' LIMIT 1");
              $umsatzsteuer_normal = (float)$this->app->DB->Select("SELECT steuersatz_normal FROM $module WHERE id = '$parent' LIMIT 1");
              $preiscell = " round(10000000 * b.preis*(1+ if(isnull(b.steuersatz) OR b.steuersatz < 0,
              if(b.umsatzsteuer = 'befreit',0,
                if(b.umsatzsteuer = 'ermaessigt', $umsatzsteuer_ermaessigt,
                  if(ifnull(b.umsatzsteuer,'') <> '', $umsatzsteuer_normal,
                    if(a.umsatzsteuer = 'befreit',0,
                      if(a.umsatzsteuer = 'ermaessigt', $umsatzsteuer_ermaessigt,$umsatzsteuer_normal)
                    )
                  )
                )
              )
              ,b.steuersatz)  /100)) / 10000000 ";
              $join = " LEFT JOIN artikel a ON b.artikel = a.id ";
              $anzeigebrutto = true;
            }
          }

          $this->app->DB->Update("UPDATE $table SET preis='$value' WHERE id='$id' LIMIT 1");
          $this->app->DB->Update("UPDATE $table SET keinrabatterlaubt='1' WHERE id='$id' LIMIT 1");
          if(in_array($module, array('auftrag','rechnung','gutschrift')))
          {
            $tmptable_value = $this->app->DB->Select("SELECT $module FROM $table WHERE id = '$id' LIMIT 1");
            $this->app->DB->Update("UPDATE $module SET extsoll = 0 WHERE id = '$tmptable_value' LIMIT 1");
          }
          $result = $this->app->DB->Select("SELECT ".$this->FormatPreis($preiscell)." FROM $table b $join WHERE  b.id='$id' LIMIT 1");
        break;
        case 6:
          if($module == 'auftrag' || $module == 'rechnung' || $module == 'angebot' || $module == 'gutschrift' || $module == 'bestellung' || $module == 'proformarechnung')
          {
            if($value == '')$value = 'EUR';
            if($value!="") { $this->app->DB->Update("UPDATE $table SET waehrung='$value' WHERE id='$id' LIMIT 1"); } // waehrung hier geloeshct
            $result = $this->app->DB->Select("SELECT waehrung FROM $table WHERE id='$id' LIMIT 1");
            $parent = $this->app->DB->Select("SELECT $module FROM $table WHERE id='$id' LIMIT 1");

            if($parent)
            {
              if(!$this->app->DB->Select("SELECT id FROM $table WHERE waehrung <> '".$value."' AND $module = '$parent' LIMIT 1"))
              {
                if($value!="") { $this->app->DB->Update("UPDATE $module SET waehrung = '$value' WHERE id = '$parent' AND waehrung='' LIMIT 1");}
              }
            }
          }
        break;
        case 7:
          if($module == 'auftrag' || $module == 'rechnung' || $module == 'angebot' || $module == 'gutschrift' || $module == 'proformarechnung')
          {
            $value = $this->app->erp->FromFormatZahlToDB($value);
            if(strpos($value,','))
            {
              $value = str_replace(",", ".",str_replace('.','', $value));
            }else{           
              $value = str_replace(",", ".", $value);
            }
            if($value == '')$value = '0';
            $this->app->DB->Update("UPDATE $table SET rabatt='$value',keinrabatterlaubt=1 WHERE id='$id' LIMIT 1");
            $result = $this->app->DB->Select("SELECT ".$this->FormatPreis('rabatt')." FROM $table WHERE id='$id' LIMIT 1");
            $sort = $this->app->DB->Select("SELECT sort FROM $table WHERE id='$id' LIMIT 1");
            $parent = $this->app->DB->Select("SELECT $module FROM $table WHERE id='$id' LIMIT 1");
            if($parent && $sort == 1)$this->app->DB->Update("UPDATE $module SET rabatt = '$value',keinrabatterlaubt=1 WHERE id = '$parent' LIMIT 1");
            if(in_array($module, array('auftrag','rechnung','gutschrift')))
            {
              $tmptable_value = $this->app->DB->Select("SELECT $module FROM $table WHERE id = '$id' LIMIT 1");
              $this->app->DB->Update("UPDATE $module SET extsoll = 0 WHERE id = '$tmptable_value' LIMIT 1");
            }
          }
        break;
        case 8:
          if($module == 'auftrag' || $module == 'rechnung' || $module == 'angebot' || $module == 'gutschrift' )
          {
            $value = $this->app->erp->FromFormatZahlToDB($value);
            if(strpos($value,','))
            {
              $value = str_replace(",", ".",str_replace('.','', $value));
            }else{           
              $value = str_replace(",", ".", $value);
            }
            if($value == '')$value = '0';
            $this->app->DB->Update("UPDATE $table SET einkaufspreis='$value' WHERE id='$id' LIMIT 1");
            $result = $this->app->DB->Select("SELECT ".$this->FormatPreis('einkaufspreis')." FROM $table WHERE id='$id' LIMIT 1");
          }
        break;
        case 9:
        if($module === 'retoure') {
          $value = (int)$this->app->erp->ReplaceLagerPlatz(1, $value, 1);
          $this->app->DB->Update(
            sprintf(
              'UPDATE retoure_position SET default_storagelocation = %d WHERE id = %d',
              $value, $id
            )
          );
          $result = $this->app->erp->ReplaceLagerPlatz(0, $value, 0);
        }

        break;
        default:;
      }

      $this->app->erp->RunHook('AARLGPosEnde', 5, $module, $id, $cmd, $column,$result);

      if ($table == "auftrag_position") {
        $tmpartikel = $this->app->DB->Select("SELECT artikel FROM $table WHERE id='$id' LIMIT 1");
        $tmptable_value = $this->app->DB->Select("SELECT auftrag FROM $table WHERE id='$id' LIMIT 1");
        $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE artikel='$tmpartikel' AND objekt='auftrag' AND parameter='$tmptable_value'");
        $this->app->erp->AuftragEinzelnBerechnen($tmptable_value);
        $this->app->DB->Update(
          sprintf(
            'UPDATE `artikel` SET `laststorage_changed` = NOW() WHERE `id` = %d',
            $tmpartikel
          )
        );
        $this->app->erp->ANABREGSNeuberechnen($tmptable_value,"auftrag");
      }
      if ($table == "angebot_position") {
        $tmptable_value = $this->app->DB->Select("SELECT angebot FROM $table WHERE id='$id' LIMIT 1");
        $this->app->erp->ANABREGSNeuberechnen($tmptable_value,"angebot");
      }
      if ($table == "rechnung_position") {
        $tmptable_value = $this->app->DB->Select("SELECT rechnung FROM $table WHERE id='$id' LIMIT 1");
        $this->app->erp->ANABREGSNeuberechnen($tmptable_value,"rechnung");
      }
      if ($table == "gutschrift_position") {
        $tmptable_value = $this->app->DB->Select("SELECT gutschrift FROM $table WHERE id='$id' LIMIT 1");
        $this->app->erp->ANABREGSNeuberechnen($tmptable_value,"gutschrift");
      }
      if ($table == "kalkulation_position") {
        $tmptable_value = $this->app->DB->Select("SELECT gutschrift FROM $table WHERE id='$id' LIMIT 1");
        $this->app->erp->KalkulationNeuberechnen($tmptable_value);
      }
      if ($table == "proformarechnung_position") {
        $tmptable_value = $this->app->DB->Select("SELECT proformarechnung FROM $table WHERE id='$id' LIMIT 1");
        $this->app->erp->ANABREGSNeuberechnen($tmptable_value,"proformarechnung");
      }
       if ($table == "retoure_position") {
        $tmptable_value = $this->app->DB->Select("SELECT retoure FROM $table WHERE id='$id' LIMIT 1");
        $this->app->erp->ANABREGSNeuberechnen($tmptable_value,"retoure");
      }
    }

    if ($cmd === 'load') {
      echo 'Load';
    }
    else {
      echo $result;
    }
    $this->app->ExitXentral();
  }
  
  function AARLGPositionenModule2Tabelle() {

    $module = $this->app->Secure->GetGET("module");
    
    if ($module == "auftrag") $table = "auftrag_position";
    else 
    if ($module == "angebot") $table = "angebot_position";
    else 
    if ($module == "retoure") $table = "retoure_position";
    else 
    if ($module == "lieferschein") $table = "lieferschein_position";
    else 
    if ($module == "rechnung") $table = "rechnung_position";
    else 
    if ($module == "gutschrift") $table = "gutschrift_position";
    else 
    if ($module == "bestellung") $table = "bestellung_position";
    else 
    if ($module == "produktion") $table = "produktion_position";
    else 
    if ($module == "arbeitsnachweis") $table = "arbeitsnachweis_position";
    else 
    if ($module == "reisekosten") $table = "reisekosten_position";
    else 
    if ($module == "kalkulation") $table = "kalkulation_position";
    else 
    if ($module == "inventur") $table = "inventur_position";
    else 
    if ($module == "anfrage") $table = "anfrage_position";
    else 
    if ($module == "proformarechnung") $table = "proformarechnung_position";
    else 
    if ($module == "preisanfrage") $table = "preisanfrage_position";
    else 
    if ($module == "verbindlichkeit") $table = "verbindlichkeit_position";
    else {
      $table = '';
      $this->app->erp->RunHook('yui_positionmodule2table', 1, $table);
      if(empty($table)) {
        $this->app->ExitXentral();
      }
    }
    return $table;
  }
  
  function DownDrawItem($module, $id, $sid)
  {
    $check = $this->app->DB->SelectArr("SELECT id,pos,sort FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND id = '$sid' LIMIT 1");
    if($check)
    {
      $sort = $check[0]['sort'];
      $pos = $check[0]['pos'];
      $check2 = $this->app->DB->SelectArr("SELECT id,pos, sort FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos= '$pos' AND sort > '$sort' ORDER BY sort DESC LIMIT 1");
      if($check2)
      {
        $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = '".$check2[0]['sort']."' WHERE id = '$sid' LIMIT 1");
        $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = '$sort' WHERE id = '".$check2[0]['id']."' LIMIT 1");
      }else{
        $newpos = $pos + 1;
        $check3 = $this->app->DB->SelectArr("SELECT id,pos, sort FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos= '$newpos' ORDER BY sort LIMIT 1");
        if($check3)
        {
          if($check3[0]['sort'] < 1)
          {
            $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos= '$newpos'");
            $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = 0, pos = '$newpos' WHERE id = '$sid' LIMIT 1");
          }else{
            $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = 0, pos = '$newpos' WHERE id = '$sid' LIMIT 1");
          }
        }else{
          $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = 0, pos = '$newpos' WHERE id = '$sid' LIMIT 1");
        }
      }
      return true;
    }
    return false;
  }

  function UpDrawItem($module, $id, $sid)
  {
    $check = $this->app->DB->SelectArr("SELECT id,pos,sort FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND id = '$sid' LIMIT 1");
    if($check)
    {
      $sort = $check[0]['sort'];
      $pos = $check[0]['pos'];
      $check2 = $this->app->DB->SelectArr("SELECT id,pos, sort FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos= '$pos' AND sort < '$sort' ORDER BY sort DESC LIMIT 1");
      if($check2)
      {
        $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = '".$check2[0]['sort']."' WHERE id = '$sid' LIMIT 1");
        $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = '$sort' WHERE id = '".$check2[0]['id']."' LIMIT 1");
      }elseif($pos > 0){
        $newpos = $pos - 1;
        $check3 = $this->app->DB->SelectArr("SELECT id,pos, sort FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos= '$newpos' ORDER BY sort DESC LIMIT 1");
        if($check3)
        {
          $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = '".($check3[0]['sort']+1)."', pos = '$newpos' WHERE id = '$sid' LIMIT 1");
        }else{
          $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = 0, pos = '$newpos' WHERE id = '$sid' LIMIT 1");
        }
      }
      return true;
    }
    return false;
  }

  function CopyDrawItem($module, $id, $sid)
  {
    $check = $this->app->DB->SelectArr("SELECT * FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND id = '$sid' LIMIT 1");
    if(!$check)return false;
    $pos = $this->app->DB->Select("SELECT max(sort) FROM $module"."_position WHERE $module = '$id'");
    $lastzwischen = $this->app->DB->SelectArr("SELECT sort FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '$pos' ORDER BY sort DESC LIMIT 1");
    if($lastzwischen)
    {
      $sort = (int)$lastzwischen[0]['sort'] + 1;
    }else{
      $sort = 0;
    }
    $check[0]['pos'] = $pos;
    $check[0]['sort'] = $sort;
    unset($check[0]['id']);
    $this->app->DB->Insert("INSERT INTO beleg_zwischenpositionen (doctype) VALUES ('$module')");
    $new = $this->app->DB->GetInsertID();
    $this->app->DB->UpdateArr('beleg_zwischenpositionen',$new,'id',$check[0], true);
    return true;
  }
  
  function DeleteDrawItem($module, $id, $sid)
  {
    $check = $this->app->DB->Select("SELECT id FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND id = '$sid' LIMIT 1");
    if(!$check)return false;
    if($check)$this->app->DB->Delete("DELETE FROM beleg_zwischenpositionen WHERE id = '$check' LIMIT 1");
    return true;
  }
  
  function FirstField($name)
  {
    if(strpos($name,'.') === false && strpos($name,'#') === false && strpos($name,'>') === false && strpos($name,',') === false && strpos($name,' ') === false && strpos($name,':') === false)
    {
      $name = 'input[name=\''.$name.'\'], select[name=\''.$name.'\'],textarea[name=\''.$name.'\'],a[name=\''.$name.'\']';
    }
    
    $this->app->Tpl->Add('JAVASCRIPT', '
      if(typeof firstfocuselement == \'undefined\')
      {
        var firstfocuselement = "'.$name.'";
      }
    ');
  }
  
  function getStandardDrawJSON($typ)
  {
    $data = new StdClass();
    if($typ == 'seitenumbruch')return $data;
    $data->name = null;
    $data->kurztext = null;
    $data->Abstand_Oben = 0;
    $data->Abstand_Unten = 5;
    $data->Schriftgroesse = 8;
    $data->Fett = true;
    $data->Unterstrichen = false;
    
    switch($typ)
    {
      case 'gruppe':
        $data->Abstand_Links = 0;
        $data->Kurztext_Abstand_Links = 0;
        $data->Kurztext_Unterstrichen = false;
      break;
      case 'zwischensumme':
        $data->Rahmen_Links = false;
        $data->Rahmen_Rechts = false;
        $data->Rahmen_Oben = false;
        $data->Rahmen_Unten = false;
        $data->Text_Ausrichtung = 'R';
      break;
      case 'gruppensumme':
        $data->Rahmen_Links = false;
        $data->Rahmen_Rechts = false;
        $data->Rahmen_Oben = false;
        $data->Rahmen_Unten = false;
        $data->Text_Ausrichtung = 'R';
      break;

    }
    
    return $data;
  }
  
  function EditDrawItem($module, $id,$sid, $postype, $data, $bezeichnung, $text)
  {
    if(!$id || !$sid)return;
    $element = $this->app->DB->SelectArr("SELECT * FROM beleg_zwischenpositionen WHERE doctype = '$module' AND doctypeid = '$id' AND id = '$sid' LIMIT 1");
    if(!$element)return;
    if($data)
    {
      $data = json_decode($data);
    }else{
      $data = json_decode($element[0]['wert']);
    }
    if(!$data)$data = new stdClass();
    $update = false;
    switch($postype)
    {
      case 'gruppe':
        if(is_object($data))$data->name = $bezeichnung;
        if(is_object($data))$data->kurztext = $text;
        $update = true;
      break;
      case 'zwischensumme':
        if(is_object($data))$data->name = $bezeichnung;
        $update = true;
      break;
      case 'gruppensumme':
      case 'gruppensummemitoptionalenpreisen':
        if(is_object($data))$data->name = $bezeichnung;
        $update = true;
      break;
      case 'seitenumbruch':
        $update = true;
      break;
      case 'bild':      
        if(is_object($data))$data->name = $bezeichnung;
        if(is_object($data))$data->kurztext = $text;
        if(is_object($data))$data->bildbreite = $this->app->Secure->GetGET('bildbreite');
        if(is_object($data))$data->bildhoehe = $this->app->Secure->GetGET('bildhoehe');
        
        if($_FILES['bild']['tmp_name'])
        {
          $md5alt = false;
          if(!empty($data->bild) && (int)$data->bild > 0)
          {
            $path = $this->app->Conf->WFuserdata."/dms/".$this->app->Conf->WFdbname."/".(int)$data->bild;
            if(is_file($path))$md5alt = md5_file($path);
          }
          if($md5alt === false || $md5alt != md5_file($_FILES['bild']['tmp_name']))
          {
            $fileid = $this->app->erp->CreateDatei($_FILES['bild']['name'], $bezeichnung, $text, "", $_FILES['bild']['tmp_name'], $this->app->User->GetName());
            $this->app->erp->AddDateiStichwort($fileid, $module, $module, $id);
            $data->bild = $fileid;
          }
        }  
        $update = true;        
      break;
    }
    $data = $this->app->DB->real_escape_string(json_encode($data));
    if($update)$this->app->DB->Update("UPDATE beleg_zwischenpositionen set postype = '$postype', wert = '$data' WHERE id = '".$element[0]['id']."' LIMIT 1");
  }
  
  function AddDrawItem($module, $id, $postype, $pos, $sort, $data, $bezeichnung, $text)
  {
    if($data)$data = json_decode($data);
    if(!$data)
    {
      $data = $this->getStandardDrawJSON($postype);
    }
    if(empty($data->name) && $bezeichnung != '')$data->name = $bezeichnung;
    if(empty($data->kurztext) && $text != '')$data->kurztext = $text;
    
    $postypes = array('zwischensumme','gruppe','seitenumbruch','gruppensumme','bild','gruppensummemitoptionalenpreisen');
    if(!in_array($postype, $postypes))return false;
    $id = (int)$id;
    if(!$id)return false;
    $id = $this->app->DB->Select("SELECT id FROM $module WHERE id = '$id' AND schreibschutz <> 1 LIMIT 1");
    if(!$id)return false;
    
    if($_FILES['bild']['tmp_name'])
    {
      $fileid = $this->app->erp->CreateDatei($_FILES['bild']['name'], $bezeichnung, $text, "", $_FILES['bild']['tmp_name'], $this->app->User->GetName());
      $this->app->erp->AddDateiStichwort($fileid, $module, $module, $id);
      $data->bild = $fileid;
    }
    $bildhoehe = $this->app->Secure->GetGET('bildhoehe');
    $bildbreite = $this->app->Secure->GetGET('bildbreite');

    if(!empty($bildhoehe)){
      $data->bildhoehe = $bildhoehe;
    }
    if(!empty($bildbreite)){
      $data->bildbreite = $bildbreite;
    }
    $pos = (int)$pos;
    $sort = (int)$sort;
    $data = $this->app->DB->real_escape_string(json_encode($data));
    $check = $this->app->DB->Select("SELECT id FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '$pos' AND sort = '$sort' LIMIT 1");
    if($check)
    {
      $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '$pos' AND sort >= '$sort'");
    }
    $this->app->DB->Insert("INSERT INTO beleg_zwischenpositionen (doctype, doctypeid, pos, sort,  wert, postype) VALUES ('$module','$id','$pos','$sort','$data','$postype')");
    $newid = $this->app->DB->GetInsertID();
    $this->ReSortDrawItem($module, $id);
    return $newid;
  }
  
  function ReSortDrawItem($module, $id)
  {
    $items = $this->app->DB->SelectArr("SELECT id, pos, sort FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' ORDER BY pos, sort");
    $maxpos = $this->app->DB->Select("SELECT max(sort) FROM $module"."_position WHERE $module = '$id'");
    if($items)
    {
      $oldpos = false;
      $oldsort = false;
      $aktsort = 0;
      foreach($items as $item)
      {
        if($oldpos !== $item['pos'])
        {
          if($item['pos'] <= $maxpos)
          {
            $oldpos = $item['pos'];
            $aktsort = 0;
          }elseif($oldpos < $maxpos)
          {
            $oldpos = $maxpos;
            $aktsort = 0;
          }
        }
        if($aktsort != $item['sort'])
        {
          $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = '$aktsort' WHERE id = '".$item['id']."' LIMIT 1");
        }
        if($oldpos != $item['pos'])
        {
          $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = '$oldpos' WHERE id = '".$item['id']."' LIMIT 1");
        }
        $aktsort++;
      }
    }
  }
  
  function MoveDrawItem($module, $id, $styp, $sid, $styp2, $sid2)
  {
    $this->ReSortDrawItem($module, $id);
    if(!$styp || !$styp2 || !$sid || (!$sid2 && $styp2 != 'oben'))return false;
    if($styp2 == 'oben')
    {
      if($styp == 'pos')
      {
        $check = $this->app->DB->SelectArr("SELECT id, sort FROM ".$module."_position WHERE $module = '$id' AND id = '$sid' LIMIT 1");
        if($check)
        {
          if($check[0]['explodiert_parent'])return false;
          $anz = $this->app->DB->Select("SELECT count(id) FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check[0]['sort']-1)."'");
          if($anz > 0)$this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort + $anz WHERE doctype = '".$module."' AND doctypeid = '$id' AND id = '$sid' AND pos = '".($check[0]['sort'])."'");
          $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check[0]['sort']-1)."'");
          $this->app->DB->Update("UPDATE ".$module."_position SET sort = sort + 1 WHERE $module = '$id' AND sort < '".$check[0]['sort']."'");
          $this->app->DB->Update("UPDATE ".$module."_position SET sort = 0 WHERE $module = '$id' AND id = '".$check[0]['id']."' LIMIT 1");
          $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos < '".($check[0]['sort'])."'");
          return true;
        }
      }else{
        $check = $this->app->DB->SelectArr("SELECT id, pos, sort FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND id = '$sid' LIMIT 1");
        if($check)
        {
          if($check[0]['sort'] == 0 && $check[0]['pos'] == 0)return true;
          $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort - 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".$check[0]['pos']."' AND sort > '".$check[0]['sort']."'");
          $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = 0");
          $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = 0, pos = 0 WHERE id = '".$check[0]['id']."' LIMIT 1");
          return true;
        }
      }
      return false;
    }

    if($styp != 'pos')
    {
      $check = $this->app->DB->SelectArr("SELECT id, pos, sort FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND id = '$sid' LIMIT 1");
      if($check)
      {
        if($styp2 != 'pos')
        {
          $check2 = $this->app->DB->SelectArr("SELECT id, pos, sort FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND id = '$sid2' LIMIT 1");
          if($check2)
          {
            if($check[0]['pos'] == $check2[0]['pos'])
            {
              if($check[0]['sort'] < $check2[0]['sort'])
              {
                $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort - 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".$check[0]['pos']."' AND sort > '".$check[0]['sort']."' AND sort <= '".$check2[0]['sort']."' ");
                $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = '".$check2[0]['sort']."' WHERE id = '".$check[0]['id']."' LIMIT 1");
                return true;
              }elseif($check[0]['sort'] > $check2[0]['sort'])
              {
                $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".$check[0]['pos']."' AND sort < '".$check[0]['sort']."' AND sort > '".$check2[0]['sort']."' "); //Test >
                $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = '".$check2[0]['sort']."' WHERE id = '".$check[0]['id']."' LIMIT 1");
                return true;
              }
              return true;
            }else{
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort - 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".$check[0]['pos']."' AND sort > '".$check[0]['sort']."' ");
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".$check2[0]['pos']."' AND sort > '".$check2[0]['sort']."' ");
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = '".($check2[0]['sort']+1)."',pos = '".($check2[0]['pos'])."' WHERE id = '".$check[0]['id']."' LIMIT 1");
              return true;
            }
          }          
        }else{
          $check2 = $this->app->DB->SelectArr("SELECT id, sort FROM ".$module."_position WHERE $module = '$id' AND id = '$sid2' LIMIT 1");
          if($check2)
          {
            $check2[0]['explodiert_parent'] = $this->app->DB->Select("SELECT explodiert_parent FROM ".$module."_position WHERE $module = '$id' AND id = '$sid2' LIMIT 1");
            if($check2[0]['explodiert_parent'] > 0){
              //Nur nicht verschieben wenn in die Stückliste verschoben wird.
              $sorttmp = $this->app->DB->Select("SELECT sort FROM ".$module."_position WHERE $module = '$id' AND id = '$sid2' LIMIT 1 ");
              $parent2 = $this->app->DB->Select("SELECT explodiert_parent FROM ".$module."_position WHERE $module = '$id' AND sort = '".($sorttmp+1)."' LIMIT 1 ");
               if($parent2 == $check2[0]['explodiert_parent'])return false;
            }
            if($check[0]['pos'] == $check2[0]['sort'])
            {
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".$check[0]['pos']."' AND sort < '".$check[0]['sort']."' ");
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = 0 WHERE id = '".$check[0]['id']."' LIMIT 1");
              return true;
            }else{
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort - 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".$check[0]['pos']."' AND sort > '".$check[0]['sort']."' ");
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".$check2[0]['sort']."'");
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = '0',pos = '".($check2[0]['sort'])."' WHERE id = '".$check[0]['id']."' LIMIT 1");
              return true;
            }
            return true;
          }
        }
      }
    }else{
      $check = $this->app->DB->SelectArr("SELECT id, sort FROM ".$module."_position WHERE $module = '$id' AND id = '$sid' LIMIT 1");
      if($check)
      {
        $check2[0]['explodiert_parent'] = $this->app->DB->Select("SELECT explodiert_parent FROM ".$module."_position WHERE $module = '$id' AND id = '$sid' LIMIT 1");
        if($check[0]['explodiert_parent'] > 0)return false;
        if($styp2 != 'pos')
        {
          $check2 = $this->app->DB->SelectArr("SELECT id, pos, sort FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND id = '$sid2' LIMIT 1");
          if($check2)
          {
            if($check[0]['sort'] == $check2[0]['pos'])
            {
              $anz = $this->app->DB->Select("SELECT count(id) FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check[0]['sort']-1)."'");
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos - 1, sort = sort + $anz WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check[0]['sort'])."' AND sort < '".$check2[0]['sort']."'");
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos - 1, sort = sort - $anz WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check[0]['sort'])."' AND sort >= '".$check2[0]['sort']."'");
              return true;              
            }elseif($check[0]['sort'] < $check2[0]['pos'])
            {
              $kinderartikel = $this->app->DB->SelectArr("SELECT id,sort FROM ".$module."_position WHERE explodiert_parent ='".$check[0]['id']."' ORDER BY sort ASC");
              if(count($kinderartikel)){
                $maxsortkind = $this->app->DB->Select("SELECT max(sort) FROM ".$module."_position WHERE explodiert_parent ='".$check[0]['id']."'");
                $zuverschiebendezwischenpositionen = $this->app->DB->SelectArr("SELECT ID,pos FROM beleg_zwischenpositionen WHERE doctype='$module' AND doctypeid='$id' AND pos <='$maxsortkind' AND pos >= '".$check[0]['sort']."'");
                $zwipos = "(".implode(",", $zuverschiebendezwischenpositionen).")";
              }
              $anz = $this->app->DB->Select("SELECT count(id) FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check[0]['sort']-1)."'");
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos - 1, sort = sort + $anz WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check[0]['sort'])."'");
              $this->app->DB->Update("UPDATE ".$module."_position SET sort = sort - 1 WHERE $module = '$id' AND sort <= '".$check2[0]['pos']."' AND sort > '".$check[0]['sort']."'");
              $anz2 = $this->app->DB->Select("SELECT count(id) FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check2[0]['pos'])."' AND sort > '".$check2[0]['sort']."'");
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos - 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos > '".$check[0]['sort']."' AND pos <= '".($check2[0]['pos'])."'");
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos + 1, sort = sort - $anz2 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check2[0]['pos']-1)."' AND sort > '".($check2[0]['sort'])."'");
              $this->app->DB->Update("UPDATE ".$module."_position SET sort = ".$check2[0]['pos']." WHERE $module = '$id' AND id = '".$check[0]['id']."'");
              if(!empty($kinderartikel)){
                $elternsort = $check[0]['sort'];
                $ckinderartikel = count($kinderartikel);
                for ($i=0; $i < $ckinderartikel; $i++) {
                  $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos - 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos < '".($check2[0]['pos'])."' AND pos >= '".$check[0]['sort']."'");
                  $this->app->DB->Update("UPDATE ".$module."_position SET sort = sort - 1 WHERE $module = '$id' AND sort > '$elternsort' AND sort <= '".$check2[0]['pos']."'");
                  $this->app->DB->Update("UPDATE ".$module."_position SET sort = '".($check2[0]['pos']-$i)."' WHERE $module = '$id' AND id = '".$kinderartikel[$i]['id']."' LIMIT 1");
                }
                if(count($zuverschiebendezwischenpositionen)){
                  $positionsanzahl = $this->app->DB->Select("SELECT sort-".$check[0]['sort']." FROM auftrag_position WHERE id = '".$check[0]['id']."'");
                  foreach ($zuverschiebendezwischenpositionen as $zkey => $zvalue) {
                    $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = ".$zvalue['pos']." + $positionsanzahl WHERE id = '".$zvalue['ID']."'");
                  }
                }
              }
              return true;
            }elseif($check[0]['sort'] > $check2[0]['pos'])
            {
              if($check[0]['sort'] > $check2[0]['pos'] + 1)
              {
                $kinderartikel = $this->app->DB->SelectArr("SELECT id,sort FROM ".$module."_position WHERE explodiert_parent ='".$check[0]['id']."' ORDER BY sort ASC");
                $zuverschiebendezwischenpositionen= array(0);
                if(count($kinderartikel)){
                  $maxsortkind = $this->app->DB->Select("SELECT max(sort) FROM ".$module."_position WHERE explodiert_parent ='".$check[0]['id']."'");
                  $zuverschiebendezwischenpositionentmp = $this->app->DB->SelectArr("SELECT ID FROM beleg_zwischenpositionen WHERE doctype='$module' AND doctypeid='$id' AND pos <='$maxsortkind' AND pos >= '".$check[0]['sort']."'");
                  $zuverschiebendezwischenpositionen = array(0);
                  if(is_array($zuverschiebendezwischenpositionentmp)){
                    foreach ($zuverschiebendezwischenpositionentmp as $key => $value) {
                      $zuverschiebendezwischenpositionen[] = $value['ID'];
                    }
                  }
                }
                $zwipos = "(".implode(",", $zuverschiebendezwischenpositionen).")";
                $anz1 = $this->app->DB->Select("SELECT count(id) FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check[0]['sort']-1)."' ");
                $anz = $this->app->DB->Select("SELECT count(id) FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check2[0]['pos'])."' AND sort > '".$check2[0]['sort']."'");
                if($anz1)$this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort + $anz1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check[0]['sort'])."' AND NOT id IN $zwipos");
                $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos < '".($check[0]['sort'])."' AND pos > ".$check2[0]['pos']." AND NOT id IN $zwipos");
                $this->app->DB->Update("UPDATE ".$module."_position SET sort = sort + 1 WHERE $module = '$id' AND sort > '".$check2[0]['pos']."' AND sort < '".$check[0]['sort']."'");
                $this->app->DB->Update("UPDATE ".$module."_position SET sort = '".($check2[0]['pos']+1)."' WHERE $module = '$id' AND id = '".$check[0]['id']."' LIMIT 1");                
                
                if($anz)
                {
                  $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort - $anz, pos = pos + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check2[0]['pos'])."' AND sort > '".$check2[0]['sort']."' AND NOT id IN $zwipos");
                }

                if(!empty($kinderartikel)){
                  $elternsort = $check2[0]['pos']+1;
                  $zielpos = $elternsort+1;
                  $ckinderartikel = count($kinderartikel);
                  for ($i=0; $i < $ckinderartikel; $i++) {
                    $this->app->DB->Update("UPDATE ".$module."_position SET sort = sort + 1 WHERE $module = '$id' AND sort < '".$kinderartikel[$i]['sort']."' AND sort > '".($elternsort+$i)."'");
                    $this->app->DB->Update("UPDATE ".$module."_position SET sort = '".($zielpos+$i)."' WHERE $module = '$id' AND id =  '".$kinderartikel[$i]['id']."' LIMIT 1");
                  }
                  $differenzwert = ($check[0]['sort'] - $check2[0]['pos']) -1 ;
                  $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos=pos-$differenzwert WHERE id IN $zwipos");
                }

              }else{
                $anz = $this->app->DB->Select("SELECT count(id) FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check2[0]['pos'])."' AND sort > '".$check2[0]['sort']."'");
                if($anz)
                {
                  $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort + $anz WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".$check[0]['sort']."'");
                  $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort - $anz, pos = pos + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check2[0]['pos'])."' AND sort > '".$check2[0]['sort']."'");
                }
              }
              return true;
            }
            return true;
          }
        }else{
          $check2 = $this->app->DB->SelectArr("SELECT id, sort FROM ".$module."_position WHERE $module = '$id' AND id = '$sid2' LIMIT 1");
          if($check2)
          {
            $check2[0]['explodiert_parent'] = $this->app->DB->Select("SELECT explodiert_parent FROM ".$module."_position WHERE $module = '$id' AND id = '$sid2' LIMIT 1");
            if($check2[0]['explodiert_parent'] > 0){
              //Nur nicht verschieben wenn in die Stückliste verschoben wird.
              $sorttmp = $this->app->DB->Select("SELECT sort FROM ".$module."_position WHERE $module = '$id' AND id = '$sid2' LIMIT 1 ");
              $parent2 = $this->app->DB->Select("SELECT explodiert_parent FROM ".$module."_position WHERE $module = '$id' AND sort = '".($sorttmp+1)."' LIMIT 1 ");
              if($parent2 == $check2[0]['explodiert_parent'])return false;
            }

            if($check[0]['sort'] < $check2[0]['sort'])
            {
              $maxsort = $check[0]['sort'];
              $zuverschiebendezwischenpositionen = '';
              $kinderartikel = $this->app->DB->SelectArr("SELECT id,sort FROM ".$module."_position WHERE explodiert_parent ='".$check[0]['id']."' ORDER BY sort ASC");
              if(count($kinderartikel)){
                $maxsortkind = $this->app->DB->Select("SELECT max(sort) FROM ".$module."_position WHERE explodiert_parent ='".$check[0]['id']."'");
                $zuverschiebendezwischenpositionentmp = $this->app->DB->SelectArr("SELECT ID FROM beleg_zwischenpositionen WHERE doctype='$module' AND doctypeid='$id' AND pos >='$maxsort' AND pos <= '$maxsortkind'");
                $zuverschiebendezwischenpositionen = array();
                if(is_array($zuverschiebendezwischenpositionentmp)){
                  foreach ($zuverschiebendezwischenpositionentmp as $key => $value) {
                    $zuverschiebendezwischenpositionen[] = $value['ID'];
                  }
                }
              }
              $zwipos = "(".implode(",", $zuverschiebendezwischenpositionen).")";
              $anz = $this->app->DB->Select("SELECT count(id) FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check[0]['sort']-1)."'");
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos  - 1, sort = sort + $anz WHERE  doctype = '".$module."' AND doctypeid = '$id' AND pos = '".$check[0]['sort']."' AND NOT id IN $zwipos");           
              $this->app->DB->Update("UPDATE ".$module."_position SET sort = sort - 1 WHERE $module = '$id' AND sort <= '".$check2[0]['sort']."' AND sort > '".$check[0]['sort']."'");

              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos  - 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos < '".$check2[0]['sort']."' AND pos > '".$check[0]['sort']."' AND NOT id IN $zwipos");
              $this->app->DB->Update("UPDATE ".$module."_position SET sort = '".$check2[0]['sort']."' WHERE $module = '$id' AND id =  '".$check[0]['id']."' LIMIT 1");

              if(!empty($kinderartikel)){
                $differenzwert = $check2[0]['sort'] - $check[0]['sort']-count($kinderartikel);
                $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos=pos+$differenzwert WHERE id IN $zwipos");
                $elternsort = $check2[0]['sort'];
                $ckinderartikel = count($kinderartikel);
                for ($i=0; $i < $ckinderartikel; $i++) {
                  $this->app->DB->Update("UPDATE ".$module."_position SET sort = sort - 1 WHERE $module = '$id' AND sort > '".$kinderartikel[$i]['sort']."' AND sort < '$elternsort'");
                  $this->app->DB->Update("UPDATE ".$module."_position SET sort = '$elternsort' WHERE $module = '$id' AND id =  '".$kinderartikel[$i]['id']."' LIMIT 1");
                  $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos=pos-1 WHERE doctype='$module' AND doctypeid='$id' AND pos >= '".$check[0]['sort']."' AND pos < '$elternsort' AND NOT id IN $zwipos");
                }
              }

              return true;
            }elseif($check[0]['sort'] > $check2[0]['sort'])
            {
              $zuverschiebendezwischenpositionen = array(0);
              $kinderartikel = $this->app->DB->SelectArr("SELECT id,sort FROM ".$module."_position WHERE explodiert_parent ='".$check[0]['id']."' ORDER BY sort DESC");
              if(!empty($kinderartikel)){
                $maxsortkind = $this->app->DB->Select("SELECT max(sort) FROM ".$module."_position WHERE explodiert_parent ='".$check[0]['id']."'");
                $zuverschiebendezwischenpositionentmp = $this->app->DB->SelectArr("SELECT ID FROM beleg_zwischenpositionen WHERE doctype='$module' AND doctypeid='$id' AND pos <='$maxsortkind' AND pos > '".$check2[0]['sort']."'");
                $zuverschiebendezwischenpositionen = array(0);
                if(is_array($zuverschiebendezwischenpositionentmp)){
                  foreach ($zuverschiebendezwischenpositionentmp as $key => $value) {
                    $zuverschiebendezwischenpositionen[] = $value['ID'];
                  }
                }
              }
              $zwipos = "(".implode(",", $zuverschiebendezwischenpositionen).")";
              $anz = $this->app->DB->Select("SELECT count(id) FROM beleg_zwischenpositionen WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos = '".($check[0]['sort']-1)."'");
              if($anz > 0)$this->app->DB->Update("UPDATE beleg_zwischenpositionen SET sort = sort + $anz WHERE  doctype = '".$module."' AND doctypeid = '$id' AND pos = '".$check[0]['sort']."'");
              $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos  + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos >= '".$check2[0]['sort']."' AND pos < '".$check[0]['sort']."' AND NOT id IN $zwipos");
              $this->app->DB->Update("UPDATE ".$module."_position SET sort = sort + 1 WHERE $module = '$id' AND sort > '".$check2[0]['sort']."' AND sort < '".$check[0]['sort']."'");
              $this->app->DB->Update("UPDATE ".$module."_position SET sort = '".($check2[0]['sort'] + 1)."' WHERE $module = '$id' AND id =  '".$check[0]['id']."' LIMIT 1");
              if(!empty($kinderartikel)){
                $differenzwert = $check[0]['sort'] - ($check2[0]['sort'] + 1);
                $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos=pos-$differenzwert WHERE id IN $zwipos");
                $elternsort = $check2[0]['sort']+1;
                $ckinderartikel = count($kinderartikel);
                for ($i=0; $i < $ckinderartikel; $i++) {
                  $this->app->DB->Update("UPDATE beleg_zwischenpositionen SET pos = pos  + 1 WHERE doctype = '".$module."' AND doctypeid = '$id' AND pos >= '".$check2[0]['sort']."' AND pos <= '$maxsortkind' AND NOT id IN $zwipos");
                  $this->app->DB->Update("UPDATE ".$module."_position SET sort = sort + 1 WHERE $module = '$id' AND sort < '".$maxsortkind."' AND sort > '$elternsort'");
                  $this->app->DB->Update("UPDATE ".$module."_position SET sort = '".($elternsort+1)."' WHERE $module = '$id' AND id = '".$kinderartikel[$i]['id']."' LIMIT 1");
                }
              }
              return true;
            }
          }
        }
      }
    }
    return false;
  }
  
  function AARLGPositionen($iframe = true, $cmd = null, $sid = null) {

    $module = $this->app->Secure->GetGET("module");
    $fmodul = $this->app->Secure->GetGET("fmodul");

    if($this->app->erp->Firmendaten("erweiterte_positionsansicht")=="1")
      $erweiterte_ansicht=",'<br><div style=\"width:300px; white-space:normal;\">',b.beschreibung,'</div>'";

    if($this->app->erp->Firmendaten("erweiterte_positionsansicht")=="1")
      $hersteller_ansicht="if(a.hersteller!='',CONCAT('<strong>',a.hersteller,'</strong><br>'),''),";
    else $hersteller_ansicht="";



    $extended_mysql55 = ",'de_DE'";
    $id = $this->app->Secure->GetGET("id");

    $this->app->DB->Select("set @order = 0;");
    $this->app->DB->Update("update " . $module . "_position set sort=@order:= @order + 1 WHERE " . $module . "='$id' order by sort asc");
    
    $intern = true;
    if(is_null($cmd))
    {
      $intern = false;
      $cmd = $this->app->Secure->GetGET('cmd');
    }
    
    if($cmd === 'getpreise') {
      $ret = null;
      //$scol = $this->app->Secure->GetPOST('scol');
      $prices = explode(';', $this->app->Secure->GetPOST('preise'));
      $quantities = explode(';', $this->app->Secure->GetPOST('quantities'));
      if($module === 'auftrag' || $module === 'rechnung' || $module === 'gutschrift' || $module === 'angebot'
        || $module === 'proformarechnung') {
        $moduleArr = $this->app->DB->SelectRow(
          sprintf(
            'SELECT * FROM `%s` WHERE `id` = %d LIMIT 1',
            $module, $id
          )
        );
        //$anrede = $moduleArr['typ'];// $this->app->DB->Select("SELECT typ FROM $module WHERE id = '$id' LIMIT 1");
        $projekt = $moduleArr['projekt'];//$this->app->DB->Select("SELECT projekt FROM $module WHERE id = '$id' LIMIT 1");
        $adresse = $moduleArr['adresse'];//$this->app->DB->Select("SELECT adresse FROM $module WHERE id = '$id' LIMIT 1");
        $funktion = ucfirst($module).'MitUmsatzeuer';
        $priceCol = 'b.preis';
        $join = '';
        $anrede = 'firma';
        if(!$this->app->erp->AnzeigeBelegNettoAdresse($anrede, $module, $projekt, $adresse, $id)
          && $this->app->erp->$funktion($id)) {
          //$steuersatz = $this->app->erp->GetSteuersatzBelegpos($module, $id, $sid);
          //$value /= $value / (1+(float)$steuersatz / 100);
          /*$umsatzsteuer_ermaessigt = (float)$this->app->DB->Select(
            "SELECT steuersatz_ermaessigt FROM $module WHERE id = '$id' LIMIT 1"
          );
          $umsatzsteuer_normal = (float)$this->app->DB->Select(
            "SELECT steuersatz_normal FROM $module WHERE id = '$id' LIMIT 1"
          );*/
          $umsatzsteuer_ermaessigt = (float)$moduleArr['steuersatz_ermaessigt'];
          $umsatzsteuer_normal = (float)$moduleArr['steuersatz_normal'];
          $priceCol = "round(10000000* b.preis*(1+ if(isnull(b.steuersatz) OR b.steuersatz < 0,
          if(b.umsatzsteuer = 'befreit',0,
            if(b.umsatzsteuer = 'ermaessigt', $umsatzsteuer_ermaessigt,
              if(ifnull(b.umsatzsteuer,'') <> '', $umsatzsteuer_normal,
                if(a.umsatzsteuer = 'befreit',0,
                  if(a.umsatzsteuer = 'ermaessigt', $umsatzsteuer_ermaessigt,$umsatzsteuer_normal)
                )
              )
            )
          )
          ,b.steuersatz)  /100)) / 10000000 ";
          $join = ' LEFT JOIN `artikel` AS `a` ON b.artikel = a.id ';
          //$anzeigebrutto = true;
        }
      }
      $idToPrice = [];
      $idToQuantity = [];
      $positionsIds = [];
      foreach($prices as $priceInForm) {
        $priceSplit = explode(':', $priceInForm);
        if(count($priceSplit) == 2) {
          $elementId = $priceSplit[0];
          $price = trim($priceSplit[1]);
          $idSplit = explode('split', $elementId);
          $positionId = (int)$idSplit[0];
          $arr[$positionId] = ['position_id' => $positionId, 'value' => $price, 'price_id' => $elementId];
          $idToPrice[$positionId] = $price;
          $positionsIds[] = $positionId;
        }
      }
      foreach($quantities as $quantityInForm) {
        $quantitySplittet = explode(':', $quantityInForm);
        if(count($quantitySplittet) == 2) {
          $elementId = $quantitySplittet[0];
          $quantity = $this->app->erp->ReplaceMenge(1, trim($quantitySplittet[1]), 1);
          if(!is_numeric($quantity)) {
            continue;
          }
          $idSplit = explode('split', $elementId);
          $positionId = (int)$idSplit[0];
          if(!isset($arr[$positionId])){
            $arr[$positionId] = ['position_id' => $positionId, 'qty' => $quantity, 'quantity_id' => $elementId];
          }
          else {
            $arr[$positionId]['quantity_id'] = $elementId;
            $arr[$positionId]['qty'] = $quantity;
          }
          $idToQuantity[$positionId] = $quantity;
          $positionsIds[] = $positionId;
        }
      }
      if(!empty($positionsIds)) {
        $positions = $this->app->DB->SelectArr(
          sprintf(
            "SELECT b.id, %s AS `preis`, b.menge  
            FROM `%s` AS `b` 
            %s 
            WHERE b.`%s` = %d AND b.id IN (%s)",
            $priceCol,
            $module.'_position',
            $join,
            $module, $id, implode(', ', array_unique($positionsIds))
          )
        );

        if(!empty($positions)) {
          foreach($positions as $position)  {
            $positionId = $position['id'];
            if(isset($idToPrice[$positionId])
              && $position['preis'] != $idToPrice[$positionId]
            ) {
              $price = rtrim(number_format($position['preis'], 8, ',', '.'), '0');
              $priceSplit = explode(',', $price);
              if(strlen($priceSplit[count($priceSplit)-1]) < 2) {
                $price .= str_repeat('0',2-strlen($priceSplit[count($priceSplit)-1]));
              }
              $ret[] = ['elid' => $arr[$positionId]['price_id'], 'value' => $price];
            }
            if(isset($idToQuantity[$positionId])
              && $position['menge'] != $idToQuantity[$positionId]
            ) {
              $quantity = rtrim(number_format($position['menge'], 8, ',', ''), '0');
              $quantitySplit = explode(',', $quantity);
              if(isset($quantitySplit[1]) && $quantitySplit[1] === '') {
                $quantity = $quantitySplit[0];
              }

              $ret[] = ['elid'=> $arr[$positionId]['quantity_id'], 'value' => $quantity];
            }
          }
        }
      }
      echo json_encode($ret);
      $this->app->ExitXentral();
    }
    
    if($cmd == 'getelements')
    {
      $id = (int)$id;
      $erweiterte_positionsansicht = (int)$this->app->erp->Firmendaten('erweiterte_positionsansicht');
      $ecol = 0;
      $dcol = 0;
      $beleg_zwischenpositionen = $this->app->DB->SelectArr("SELECT * FROM beleg_zwischenpositionen WHERE doctype = '$module' AND doctypeid = '$id' ORDER by pos, sort DESC");
      //TODO ecol
      if($beleg_zwischenpositionen)
      {
        foreach($beleg_zwischenpositionen as $k => $v)
        {
          $beleg_zwischenpositionen[$k]['id'] = $v['id'];
          $beleg_zwischenpositionen[$k]['erweiterte_positionsansicht'] = $erweiterte_positionsansicht;
          $beleg_zwischenpositionen[$k]['name'] = '';
          $beleg_zwischenpositionen[$k]['kurztext'] = '';
          $beleg_zwischenpositionen[$k]['bildbreite'] = '';
          $beleg_zwischenpositionen[$k]['bildhoehe'] = '';
          $beleg_zwischenpositionen[$k]['dcol'] = $dcol;
          $beleg_zwischenpositionen[$k]['ecol'] = $ecol;
          if($v['wert'])
          {
            $json = json_decode($v['wert']);
            if($json)
            {
              $beleg_zwischenpositionen[$k]['wert'] = $json;
              if(isset($json->name))
              {
                $beleg_zwischenpositionen[$k]['name'] = (String)$json->name;
              }
              if(isset($json->kurztext))$beleg_zwischenpositionen[$k]['kurztext'] = (String)$json->kurztext;
              if(isset($json->bildbreite))$beleg_zwischenpositionen[$k]['bildbreite'] = (String)$json->bildbreite;
              if(isset($json->bildhoehe))$beleg_zwischenpositionen[$k]['bildhoehe'] = (String)$json->bildhoehe;
            }
          }
        }
      }
      echo json_encode(array('result'=>json_decode(json_encode($beleg_zwischenpositionen))));
      exit;
    }
    
    if($cmd == 'deldrawitem')
    {
      if($this->app->erp->RechteVorhanden($module,'del'.$module.'position'))
      {
        $id = (int)$id;
        $sid = (int)$this->app->Secure->GetGET("sid");
        $this->DeleteDrawItem($module, $id, $sid);
      }
      header('Location: index.php?module='.$module.'&action=positionen&id='.$id.($fmodul?'&fmodul='.$fmodul:''));
      exit;
    }
    if($cmd == 'copydrawitem')
    {
      if($this->app->erp->RechteVorhanden($module,'copy'.$module.'position'))
      {
        $id = (int)$id;
        $sid = (int)$this->app->Secure->GetGET("sid");
        $this->CopyDrawItem($module, $id, $sid);
      }
      header('Location: index.php?module='.$module.'&action=positionen&id='.$id.($fmodul?'&fmodul='.$fmodul:''));
      exit;
    }
    if($cmd == 'downdrawitem')
    {

      $id = (int)$id;
      $sid = (int)$this->app->Secure->GetGET("sid");
      $this->DownDrawItem($module, $id, $sid);
      header('Location: index.php?module='.$module.'&action=positionen&id='.$id.($fmodul?'&fmodul='.$fmodul:''));
      exit;
    }
    
    if($cmd == 'updrawitem')
    {
      $id = (int)$id;
      $sid = (int)$this->app->Secure->GetGET("sid");
      $this->UpDrawItem($module, $id, $sid);
      header('Location: index.php?module='.$module.'&action=positionen&id='.$id.($fmodul?'&fmodul='.$fmodul:''));
      exit;
    }
    
    if($cmd == 'drawmove')
    {
      $id = (int)$id;
      $sid = (int)$this->app->Secure->GetGET("sid");
      $sid2 = (int)$this->app->Secure->GetGET("sid2");
      $styp = $this->app->Secure->GetGET("styp");
      $styp2 = $this->app->Secure->GetGET("styp2");
      
      $this->MoveDrawItem($module, $id, $styp, $sid, $styp2, $sid2);
      
      header('Location: index.php?module='.$module.'&action=positionen&id='.$id.($fmodul?'&fmodul='.$fmodul:''));
      exit;
    }
    
    if($cmd == 'adddraw')
    {
      $id = (int)$id;
      $pos = $this->app->Secure->GetGET("pos");
      $sort = $this->app->Secure->GetGET("sort");
      $data = '';
      $bezeichnung = $this->app->Secure->GetPOST('grbez');
      $text = $this->app->Secure->GetPOST('grtext');
      $postype = $this->app->Secure->GetPOST('postype');
      $this->AddDrawItem($module, $id, $postype, $pos, $sort, $data, $bezeichnung, $text);

      echo json_encode(['success' => true]);
      $this->app->ExitXentral();
    }
    
    if($cmd == 'editdraw')
    {
      $id = (int)$id;
      $sid = (int)$this->app->Secure->GetGET("sid");
      $data = $this->app->Secure->GetGET("data");
      $bezeichnung = $_POST['grbez'];
      $text = isset($_POST['grtext'])?$_POST['grtext']:'';
      $postype = $this->app->Secure->GetPOST('postype');
      $this->EditDrawItem($module, $id,$sid, $postype, $data, $bezeichnung, $text);

      header('Location: index.php?module='.$module.'&action=positionen&id='.$id.($fmodul?'&fmodul='.$fmodul:''));
      $this->app->ExitXentral();
    }
        
    $this->app->erp->RunHook("AARLGPositionen_cmds_end", 1, $id);
    
    if ($iframe) {
      if($module=="angebot" || $module =="auftrag" || $module=="rechnung" || $module=="gutschrift" || $module=="lieferschein" || $module=="retoure" || $module=="bestellung" || $module=="anfrage" || $module=="preisanfrage" || $module=="proformarechnung" || $module=="produktion" || $module=="reisekosten")
      $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM $module WHERE id='$id' LIMIT 1");
      if($schreibschutz=="1") 
        $this->app->Tpl->Set('POS', "<iframe name=\"framepositionen\" id=\"framepositionen\" style=\"\" src=\"index.php?module=$module&action=positionen&id=$id\" frameborder=\"no\" width=\"100%\" height=\"600\" ></iframe>");
      else
        $this->app->Tpl->Set('POS', "<iframe name=\"framepositionen\" id=\"framepositionen\" onload=\"resizePositionenIframe(this.id);\" src=\"index.php?module=$module&action=positionen&id=$id".($fmodul?'&fmodul='.$fmodul:'')."&rand=".time()."\" frameborder=\"no\" width=\"100%\" height=\"600\" ></iframe>");
      
      $this->app->Tpl->Add('POS','<script>
      function savescrollpos() {                    
        var wert = $.base64Encode($(window).scrollTop());
        
        $.ajax({
          type: "POST",
          url: "index.php?module=ajax&action=autosaveuserparameter",
          data:  { name: "positioneniframe_savescrollpos_'.$module.'", value: wert }
        }) .done(function( data ) {
          
        });
      }
      
      function resizePositionenIframe(iframeId) {
        var $secondTab = $(\'#tabs-2\');
        var styleProps = $secondTab.css([\'display\', \'visibility\']);
          
        // Zweiten Tab sichtbar machen, ansonsten kann die Höhe nicht bestimmt werden
        if (styleProps.display === \'none\') {
          $secondTab.css({ display:\'block\', visibility:\'collapse\' });
        }
        
        // Höhe vom Iframe-Inhalt bestimmen und Iframe-Höhe setzen 
        var iframeHeight = getIframeContentHeight(iframeId) + 100;
        if (iframeHeight < 1000) {
          iframeHeight = 1000;
        }
        $(\'#\' + iframeId).height(iframeHeight + \'px\');
            
        // Sichtbarkeit vom zweiten Tab wieder zurückstellen
        if (styleProps.display === \'none\') {
          $secondTab.css(styleProps);
        }
      }
      
      function getIframeContentHeight(iframeId) {
          var iframe = document.getElementById(iframeId);
          var iframeWin = iframe.contentWindow || iframe.contentDocument.parentWindow;
          var iframeHeight = iframeWin.document.body.scrollHeight;
          
          return iframeHeight;
      }
      </script>
      ');
      
    } else {
      $table = $this->AARLGPositionenModule2Tabelle();

      /* neu anlegen formular */
      $artikelart = $this->app->Secure->GetPOST("artikelart");
      $bezeichnung = $this->app->Secure->GetPOST("bezeichnung");
      if($bezeichnung != '')
      {
        if($module=="auftrag" || $module=="rechnung" || $module=="gutschrift")$this->app->DB->Update("UPDATE $module SET extsoll = 0 WHERE id = '$id'");
      }
      $vpe = $this->app->Secure->GetPOST("vpe");
      $umsatzsteuerklasse = $this->app->Secure->GetPOST("umsatzsteuerklasse");
      $waehrung = $this->app->Secure->GetPOST("waehrung");
      $projekt = $this->app->Secure->GetPOST("projekt");
      $preis = $this->app->Secure->GetPOST("preis");
      $preis = $this->app->erp->FromFormatZahlToDB($preis);
      $menge = $this->app->Secure->GetPOST("menge");
      //$menge = str_replace(',', '.', $menge);
      $menge= $this->app->erp->FromFormatZahlToDB($menge);

      if($menge < 0) $menge = 1;

      $ort = $this->app->Secure->GetPOST("ort");
      $lieferdatum = $this->app->Secure->GetPOST("lieferdatum");
      $lieferdatum = $this->app->String->Convert($lieferdatum, "%1.%2.%3", "%3-%2-%1");
      $datum = $this->app->Secure->GetPOST("datum");
      $datum = $this->app->String->Convert($datum, "%1.%2.%3", "%3-%2-%1");
      $rabatt = $this->app->Secure->GetPOST("rabatt");
      $rabatt = str_replace(',', '.', $rabatt);
      if($rabatt > 0 || $rabatt=="0") $keinrabatterlaubt=1; else $keinrabatterlaubt=0;
      
      if ($lieferdatum == "") $lieferdatum = "00.00.0000";
      $ajaxbuchen = $this->app->Secure->GetPOST("ajaxbuchen");
      
      if ($ajaxbuchen != "") {
        $artikel = $this->app->Secure->GetPOST('artikel');
        $bezeichnung = $this->app->Secure->GetPOST('artikel',null,'',1);
        $nummer = $this->app->Secure->GetPOST("nummer");
        $projekt = $this->app->Secure->GetPOST("projekt");
        $docArr = $this->app->DB->SelectRow(
          sprintf(
            'SELECT * FROM `%s` WHERE id = %d',
            $module, $id
          )
        );
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
        $sort = $this->app->DB->Select("SELECT MAX(sort) FROM $table WHERE $module='$id' LIMIT 1");
        $sort = $sort + 1;
        $adresse = $docArr['adresse'];// $this->app->DB->Select("SELECT adresse FROM $module WHERE id='$id' LIMIT 1");
        $sprache = $docArr['sprache'];//$this->app->DB->Select("SELECT sprache FROM $module WHERE id='$id' LIMIT 1");
        if($sprache=='') {
          $sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");
        }
        $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");
        $articleArr = $this->app->DB->SelectRow(sprintf('SELECT * FROM artikel WHERE id = %d', $artikel_id));
        if($module == 'auftrag' || $module == 'rechnung' || $module == 'gutschrift' || $module == 'angebot' || $module == 'proformarechnung') {
          $_anrede = $docArr['typ'];//$this->app->DB->Select("SELECT typ FROM $module WHERE id = '$id' LIMIT 1");
          $_projekt = $docArr['projekt'];//$this->app->DB->Select("SELECT projekt FROM $module WHERE id = '$id' LIMIT 1");
          $funktion = ucfirst($module).'MitUmsatzeuer';
          $_anrede = 'firma';
          if($this->app->erp->AnzeigePositionenBrutto($_anrede, $module, $_projekt, $adresse,$id) && $this->app->erp->$funktion($id)) {
            $umsatzsteuer = $articleArr['umsatzsteuer'];// $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id = '".$artikel_id."' LIMIT 1");
            if($umsatzsteuer === 'ermaessigt') {
              //$preis = round($preis / (1+ (float)$this->app->DB->Select("SELECT steuersatz_ermaessigt FROM $module WHERE id = '$id' LIMIT 1")/100),8);
              $preis = round($preis / (1+ (float)$docArr['steuersatz_ermaessigt']/100),8);
            }
            elseif($umsatzsteuer !== 'befreit') {
              //$preis = round($preis / (1+ (float)$this->app->DB->Select("SELECT steuersatz_normal FROM $module WHERE id = '$id' LIMIT 1")/100),8);
              $preis = round($preis / (1+ (float)$docArr['steuersatz_normal']/100),8);
            }
          }
        }

        $this->app->erp->RunHook('AARLGPositionenPreis', 3, $module, $id, $preis);
        if(in_array($module, array('auftrag','rechnung','gutschrift'))) {
          $this->app->DB->Update("UPDATE $module SET extsoll = 0 WHERE id = '$id' LIMIT 1");
        }
        //$bezeichnung = $artikel;
        if($sprache=="englisch")
        {
          $name_en = $articleArr['name_en'];//$this->app->DB->Select("SELECT name_en FROM artikel WHERE id='$artikel_id' LIMIT 1");
          if($name_en!='') {
            $bezeichnung = $name_en;
          }
        }

        $neue_nummer = $nummer;
        $waehrung = $docArr['waehrung'];//$this->app->DB->Select("SELECT waehrung FROM $module WHERE id='$id' LIMIT 1");

        $hinweis_einfuegen = $articleArr['hinweis_einfuegen'];//$this->app->DB->Select("SELECT hinweis_einfuegen FROM artikel WHERE id='$artikel_id' LIMIT 1");
        if($hinweis_einfuegen!="")
        {
          $this->app->erp->InternesEvent($this->app->User->GetID(),$hinweis_einfuegen,"warning",1);
        }
  
        $standardlieferant = $articleArr['adresse'];//$this->app->DB->Select("SELECT adresse FROM artikel WHERE id='$artikel_id' LIMIT 1");
        $hinweistextlieferant = $this->app->DB->Select("SELECT hinweistextlieferant FROM adresse WHERE id='$standardlieferant' LIMIT 1");
        if($hinweistextlieferant!="")
        {
          $this->app->erp->InternesEvent($this->app->User->GetID(),$hinweistextlieferant,"warning",1);
        }



        if($waehrung=="")
        {
          // schaue ob es gebuchte positionen gibt dann diese waehrung
          $waehrung = $this->app->DB->Select("SELECT waehrung FROM $table WHERE $module='$id' LIMIT 1");

          if($waehrung==""){
            $waehrung = $this->app->erp->GetStandardWaehrung($projekt);
          }

          if($waehrung!="") $this->app->DB->Update("UPDATE $module SET waehrung='$waehrung' WHERE id='$id' AND waehrung='' LIMIT 1");
        }
        
        $umsatzsteuer = $articleArr['umsatzsteuer'];//$this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id='$artikel_id' LIMIT 1");
        $variante_von = $articleArr['variante_von'];//$this->app->DB->Select("SELECT variante_von FROM artikel WHERE id='$artikel_id' LIMIT 1");

        if ($sprache === 'englisch') {
          $beschreibung = $articleArr['anabregs_text_en'];//$this->app->DB->Select("SELECT anabregs_text_en FROM artikel WHERE id='$artikel_id' LIMIT 1");
        }
        else {
          $beschreibung = $articleArr['anabregs_text'];//$this->app->DB->Select("SELECT anabregs_text FROM artikel WHERE id='$artikel_id' LIMIT 1");
        }
        if($beschreibung == '' && $articleArr['variante'])// $this->app->DB->Select("SELECT variante FROM artikel WHERE id='$artikel_id' LIMIT 1"))
        {
          
          if($variante_von)
          {
            if ($sprache === 'englisch') {
              $beschreibung = $this->app->DB->Select("SELECT anabregs_text_en FROM artikel WHERE id='$variante_von' LIMIT 1");
            }
            else {
              $beschreibung = $this->app->DB->Select("SELECT anabregs_text FROM artikel WHERE id='$variante_von' LIMIT 1");
            }
          }
        }


        $this->app->erp->RunHook('AARLGPositionenSprache', 6, $module, $id, $artikel_id, $sprache, $bezeichnung, $beschreibung);
        $bezeichnung = $this->app->DB->real_escape_string($bezeichnung);
        $beschreibung = $this->app->DB->real_escape_string($beschreibung);

        $mlmpunkte = $articleArr['mlmpunkte'];//$this->app->DB->Select("SELECT mlmpunkte FROM artikel WHERE id='$artikel_id' LIMIT 1");
        $mlmbonuspunkte = $articleArr['mlmbonuspunkte'];//$this->app->DB->Select("SELECT mlmbonuspunkte FROM artikel WHERE id='$artikel_id' LIMIT 1");
        $mlmdirektpraemie = $articleArr['mlmdirektpraemie'];//$this->app->DB->Select("SELECT mlmdirektpraemie FROM artikel WHERE id='$artikel_id' LIMIT 1");


        $artikelnummerkunde = $this->app->DB->real_escape_string($this->app->DB->Select("SELECT kundenartikelnummer FROM verkaufspreise WHERE adresse='$adresse' AND artikel='$artikel_id' AND kundenartikelnummer!='' AND ab_menge <='$menge' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') ORDER by ab_menge DESC LIMIT 1"));

        // Anzeige Artikel Nummer von Gruppe aus Verkaufspreis
        $gruppevkresult = $this->app->erp->GetVerkaufspreis($artikel_id,$menge,$adresse,$waehrung, $returnwaehrung ,true);
        if($gruppevkresult['kundenartikelnummer']!="" && $artikelnummerkunde=="") $artikelnummerkunde = $gruppevkresult['kundenartikelnummer'];

        $zolltarifnummer = $this->app->DB->real_escape_string($articleArr['zolltarifnummer']);//$this->app->DB->real_escape_string($this->app->DB->Select("SELECT zolltarifnummer FROM artikel WHERE id='$artikel_id' LIMIT 1"));
        $kostenstelle = $this->app->DB->real_escape_string($articleArr['kostenstelle']);//$this->app->DB->real_escape_string($this->app->DB->Select("SELECT kostenstelle FROM artikel WHERE id='$artikel_id' LIMIT 1"));
        $herkunftsland = $this->app->DB->real_escape_string($articleArr['herkunftsland']);//$this->app->DB->real_escape_string($this->app->DB->Select("SELECT herkunftsland FROM artikel WHERE id='$artikel_id' LIMIT 1"));
        $einheit = $this->app->DB->real_escape_string($articleArr['einheit']);//$this->app->DB->real_escape_string($this->app->DB->Select("SELECT einheit FROM artikel WHERE id='$artikel_id' LIMIT 1"));
        $ohnepreisimpdf = $articleArr['ohnepreisimpdf'];//$this->app->DB->Select("SELECT ohnepreisimpdf FROM artikel WHERE id='$artikel_id' LIMIT 1");


        if($variante_von)
        {
          if((String)$zolltarifnummer === "")$zolltarifnummer = $this->app->DB->real_escape_string($this->app->DB->Select("SELECT zolltarifnummer FROM artikel WHERE id='$variante_von' LIMIT 1"));
          if((String)$kostenstelle === "")$kostenstelle = $this->app->DB->real_escape_string($this->app->DB->Select("SELECT kostenstelle FROM artikel WHERE id='$variante_von' LIMIT 1"));
          if((String)$herkunftsland === "")$herkunftsland = $this->app->DB->real_escape_string($this->app->DB->Select("SELECT herkunftsland FROM artikel WHERE id='$variante_von' LIMIT 1"));
          if((String)$einheit === "")$einheit = $this->app->DB->real_escape_string($this->app->DB->Select("SELECT einheit FROM artikel WHERE id='$variante_von' LIMIT 1"));
          if((String)$ohnepreisimpdf === "")$ohnepreisimpdf = $this->app->DB->real_escape_string($this->app->DB->Select("SELECT ohnepreisimpdf FROM artikel WHERE id='$variante_von' LIMIT 1"));
        }
        if ($vpe < 1 || !is_numeric($vpe)) $vpe = '1';

        if($this->app->erp->Firmendaten("lieferdatumkw")=="1")
          $lieferdatumkw=1;
        else $lieferdatumkw=0;
        
        if (($module == "lieferschein" || $module == "retoure") && $artikel_id > 0) {
          $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum, status,projekt,vpe,artikelnummerkunde,zolltarifnummer,herkunftsland,einheit,lieferdatumkw)
              VALUES ('','$id','$artikel_id','$bezeichnung','$beschreibung','$neue_nummer','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe','$artikelnummerkunde','$zolltarifnummer','$herkunftsland','$einheit','$lieferdatumkw')");
          $newposid = $this->app->DB->GetInsertID();
        } else 
        if ($module == "arbeitsnachweis") {
          $bezeichnung = $this->app->Secure->GetPOST("bezeichnung");
          $von = $this->app->Secure->GetPOST("von");
          $bis = $this->app->Secure->GetPOST("bis");
          $adresse = $this->app->Secure->GetPOST("adresse");
          $adresse = explode(' ', $adresse);
          $adresse = $adresse[0];
          $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer='$adresse' LIMIT 1");
          $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,bezeichnung,nummer,menge,sort,datum, status,projekt,ort,von,bis,adresse)
              VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$sort','$datum','angelegt','$projekt','$ort','$von','$bis','$adresse')");
          $newposid = $this->app->DB->GetInsertID();
        } else 
        if ($module == "kalkulation") {
          $bezeichnung = $this->app->Secure->GetPOST("bezeichnung");
          $artikel = $this->app->Secure->GetPOST("artikel");
          $stueckliste = $this->app->Secure->GetPOST("stueckliste");
          $beschreibung = $this->app->Secure->GetPOST("beschreibung");
          $betrag = $this->app->Secure->GetPOST("betrag");
          $betrag = str_replace(',', '.', $betrag);
          $kalkulationart = $this->app->Secure->GetPOST("kalkulationart");

          //$projekt = $this->app->DB->Select("SELECT projekt FROM kalkulation WHERE mitarbeiternummer='$adresse' LIMIT 1");
          $adresse = $this->app->DB->Select("SELECT adresse FROM kalkulation WHERE id='$id' LIMIT 1");

          if($artikel > 0)
          {
            $tmp = explode(' ', $artikel);
            $neue_nummer = $tmp[0];
            $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$neue_nummer' LIMIT 1");// AND projekt='$projekt'");
          }

          if($stueckliste > 0)
          {
            $tmp = explode(' ', $stueckliste);
            $neue_nummer = $tmp[0];
            $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$neue_nummer' LIMIT 1");// AND projekt='$projekt'");
          }

          if($bezeichnung=="") $bezeichnung = $this->app->DB->Select("SELECT CONCAT(nummer,' ',name_de) FROM artikel WHERE id='$artikel_id' LIMIT 1");

          if($menge=="" && $artikel_id > 0) $menge=1; 
          if($artikel_id > 0 && $betrag=="") $betrag = $this->app->erp->GetVerkaufspreis($artikel_id,$menge,$adresse);
          if($stueckliste > 0 && $betrag=="") $betrag = $this->app->erp->GetEinkaufspreisStueckliste($artikel_id);
          
          $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,bezeichnung,nummer,menge,sort,datum, status,projekt,betrag,kalkulationart)
              VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$sort','$datum','angelegt','$projekt','$betrag','$kalkulationart')");
          $newposid = $this->app->DB->GetInsertID();
        } else 

        if ($module == "reisekosten") {
          $bezeichnung = $this->app->Secure->GetPOST("bezeichnung");
          $betrag = $this->app->Secure->GetPOST("betrag");
          $betrag = str_replace(',', '.', $betrag);
          $reisekostenart = $this->app->Secure->GetPOST("reisekostenart");
          $abrechnen = $this->app->Secure->GetPOST("abrechnen");
          $keineust = $this->app->Secure->GetPOST("keineust");
          $uststeuersatz = $this->app->Secure->GetPOST("uststeuersatz");
          $bezahlt_wie = $this->app->Secure->GetPOST("bezahlt_wie");

          /*adresse = $this->app->Secure->GetPOST("adresse");
            $adresse =explode(' ',$adresse);
            $adresse = $adresse[0];
            $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer='$adresse' LIMIT 1");
          */
          $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,bezeichnung,nummer,menge,sort,datum, status,projekt,ort,von,bis,betrag,bezahlt_wie,reisekostenart,abrechnen,keineust,uststeuersatz)
              VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$sort','$datum','angelegt','$projekt','$ort','$von','$bis','$betrag','$bezahlt_wie','$reisekostenart','$abrechnen','$keineust','$uststeuersatz')");
          $newposid = $this->app->DB->GetInsertID();
        } else 
        if ($module == "inventur" && $artikel_id > 0) {
          $bezeichnung = $this->app->Secure->GetPOST("artikel");
          $preis = $this->app->Secure->GetPOST("preis");
          $preis = str_replace(',', '.', $preis);
          $nummer = $this->app->Secure->GetPOST("nummer");

          /*adresse = $this->app->Secure->GetPOST("adresse");
            $adresse =explode(' ',$adresse);
            $adresse = $adresse[0];
            $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer='$adresse' LIMIT 1");
          */
          $projekt = $this->app->Secure->GetPOST("projekt");
          $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
          $sort = $this->app->DB->Select("SELECT MAX(sort) FROM $table WHERE $module='$id' LIMIT 1");
          $sort = $sort + 1;
          $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");
          $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,bezeichnung,nummer,menge,sort,projekt,preis)
              VALUES ('','$id','$artikel_id','$bezeichnung','$nummer','$menge','$sort','$projekt','$preis')");

          $newposid = $this->app->DB->GetInsertID();
        } else 
        if (($module == "anfrage" || $module == "preisanfrage") && $artikel_id > 0) {

          /*
             $bezeichnung = $this->app->Secure->GetPOST("artikel");
             $preis = $this->app->Secure->GetPOST("preis");
             $preis = str_replace(',','.',$preis);
             $nummer = $this->app->Secure->GetPOST("nummer");
          */

          /*adresse = $this->app->Secure->GetPOST("adresse");
            $adresse =explode(' ',$adresse);
            $adresse = $adresse[0];
            $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer='$adresse' LIMIT 1");
          */
          $projekt = $this->app->Secure->GetPOST("projekt");
          $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
          $sort = $this->app->DB->Select("SELECT MAX(sort) FROM $table WHERE $module='$id' LIMIT 1");
          $sort = $sort + 1;
          $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");
          $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,bezeichnung,beschreibung,nummer,menge,sort,projekt,preis)
              VALUES ('','$id','$artikel_id','$bezeichnung','$beschreibung','$nummer','$menge','$sort','$projekt','$preis')");
          $newposid = $this->app->DB->GetInsertID();
        } else 
        if ($module == "bestellung" && $artikel_id > 0) {
          $bestellnummer = $this->app->Secure->GetPOST("bestellnummer");
          $bezeichnunglieferant = $this->app->Secure->GetPOST("bezeichnunglieferant");
          $waehrung = $this->app->Secure->GetPOST("waehrung");

          if($waehrung=="") 
            $waehrung = $this->app->DB->Select("SELECT waehrung FROM $module WHERE id='$id' LIMIT 1");

          if($this->app->erp->Firmendaten("bestellungmitartikeltext")!="1")
            $beschreibung = "";


          //hier muesste man beeichnung bei lieferant auch noch speichern .... oder beides halt
          $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,bezeichnunglieferant,beschreibung,bestellnummer,menge,sort,lieferdatum, status,projekt,vpe,preis,waehrung,umsatzsteuer,einheit,kostenstelle)
              VALUES ('','$id','$artikel_id','$bezeichnunglieferant','$beschreibung','$bestellnummer','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe','$preis','$waehrung','$umsatzsteuer','$einheit','$kostenstelle')");

          $newposid = $this->app->DB->GetInsertID();
          $menge2 = $this->app->erp->PruefeMengeVPE($table, $newposid, $menge);
          if($menge != $menge2)$this->app->DB->Update("UPDATE $table SET menge = '$menge2' WHERE id = '$newposid' LIMIT 1");
          
            $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM $module WHERE id = '$id' LIMIT 1");
            $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id = '$artikel_id' LIMIT 1");
            $kategorie = str_replace('_kat','',$this->app->DB->Select("SELECT typ FROM artikel WHERE id = '$artikel_id' LIMIT 1"));
            if($kategorie && is_numeric($kategorie))
            {
              $kategorie = (int)$kategorie;
              $kategorie = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
            }else $kategorie = false;
            if($umsatzsteuer !== 'ermaessigt' && $umsatzsteuer !== 'befreit') {
              $umsatzsteuer = 'normal';
            }
            $steuersatz = false;
            if($ust_befreit == 1)
            {
              $steuertext = $this->app->DB->Select("SELECT steuertext_innergemeinschaftlich FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              if(!$steuertext && $kategorie)$steuertext = $this->app->DB->Select("SELECT steuertext_innergemeinschaftlich FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              if($umsatzsteuer === 'ermaessigt')
              {
                $erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_euermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_euermaessigt FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
                $steuersatz = null;//$this->app->DB->Select("SELECT steuersatz_erloese_euermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                //if(($steuersatz === false || is_null($steuersatz)) && $kategorie)$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_euermaessigt FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              }elseif($umsatzsteuer === 'befreit'){
                $erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_nichtsteuerbar FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_nichtsteuerbar FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
                $steuersatz = 0;
              }else{
                $erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_eunormal FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_eunormal FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
                $steuersatz = null;//$this->app->DB->Select("SELECT steuersatz_erloese_eunormal FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                //if(($steuersatz === false || is_null($steuersatz)) && $kategorie)$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_eunormal FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              }
            }elseif($ust_befreit == 2)
            {
              $steuertext = $this->app->DB->Select("SELECT steuertext_export FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              if(!$steuertext && $kategorie)$steuertext = $this->app->DB->Select("SELECT steuertext_export FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              $erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_euermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_euermaessigt FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
            }elseif($ust_befreit == 3)
            {
              $erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_nichtsteuerbar FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_nichtsteuerbar FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              $steuersatz = 0;
            }else{
              $steuertext = '';
              if($umsatzsteuer === 'ermaessigt')
              {
                $erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_ermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_ermaessigt FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
                $steuersatz = null;//$this->app->DB->Select("SELECT steuersatz_aufwendung_ermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                //if(($steuersatz === false || is_null($steuersatz)) && $kategorie)$steuersatz = $this->app->DB->Select("SELECT steuersatz_aufwendung_ermaessigt FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              }elseif($umsatzsteuer === 'befreit')
              {
                $erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_nichtsteuerbar FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_nichtsteuerbar FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
                $steuersatz = 0;
              }else{
                $erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_normal FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_aufwendung_inland_normal FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
                $steuersatz = null;//$this->app->DB->Select("SELECT steuersatz_aufwendung_normal FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                //if(($steuersatz === false || is_null($steuersatz)) && $kategorie)$steuersatz = $this->app->DB->Select("SELECT steuersatz_aufwendung_normal FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              }
            }


            if($steuersatz !== false && !is_null($steuersatz))
            {
              //$this->app->DB->Update("UPDATE $table SET steuersatz = '$steuersatz' WHERE id = '$newposid' LIMIT 1");
            }
            //if($erloese)$this->app->DB->Update("UPDATE $table SET erloese = '$erloese' WHERE id = '$newposid' LIMIT 1");
            ///if($steuertext)$this->app->DB->Update("UPDATE $table SET steuertext = '".$this->app->DB->real_escape_string($steuertext)."' WHERE id = '$newposid' LIMIT 1");
          
        } else 
        if ($module == "produktion" && $artikel_id > 0) {
        } else 
        if ($module == "gutschrift" && $artikel_id > 0) {
          $waehrung = $this->app->Secure->GetPOST("waehrung");

          if($waehrung=="") 
            $waehrung = $this->app->DB->Select("SELECT waehrung FROM $module WHERE id='$id' LIMIT 1");
          // mlm punkte bei angebot, auftrag und rechnung
          $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,beschreibung,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe,artikelnummerkunde,zolltarifnummer,kostenstelle,herkunftsland,einheit,rabatt,keinrabatterlaubt,lieferdatumkw)
              VALUES ('','$id','$artikel_id','$beschreibung','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuer','angelegt','$projekt','$vpe','$artikelnummerkunde','$zolltarifnummer','$kostenstelle','$herkunftsland','$einheit','$rabatt','$keinrabatterlaubt','$lieferdatumkw')");

          $newposid = $this->app->DB->GetInsertID();
          $menge2 = $this->app->erp->PruefeMengeVPE($table, $newposid, $menge);
          if($menge != $menge2)$this->app->DB->Update("UPDATE $table SET menge = '$menge2' WHERE id = '$newposid' LIMIT 1");
            $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM $module WHERE id = '$id' LIMIT 1");
            $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id = '$artikel_id' LIMIT 1");
            $kategorie = str_replace('_kat','',$this->app->DB->Select("SELECT typ FROM artikel WHERE id = '$artikel_id' LIMIT 1"));
            if($kategorie && is_numeric($kategorie))
            {
              $kategorie = (int)$kategorie;
              $kategorie = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
            }else $kategorie = false;
            if($umsatzsteuer != 'ermaessigt' && $umsatzsteuer != 'befreit')$umsatzsteuer = 'normal';
            $steuersatz = false;
            if($ust_befreit == 1)
            {
              $steuertext = $this->app->DB->Select("SELECT steuertext_innergemeinschaftlich FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              if(!$steuertext && $kategorie)$steuertext = $this->app->DB->Select("SELECT steuertext_innergemeinschaftlich FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              if($umsatzsteuer == 'ermaessigt')
              {
                $erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_euermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_euermaessigt FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
                //$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_euermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              }elseif($umsatzsteuer == 'befreit'){
                $erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_nichtsteuerbar FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_nichtsteuerbar FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              }else{
                $erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_eunormal FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_eunormal FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
                //$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_eunormal FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              }
            }elseif($ust_befreit == 2)
            {
              $steuertext = $this->app->DB->Select("SELECT steuertext_export FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              if(!$steuertext && $kategorie)$steuertext = $this->app->DB->Select("SELECT steuertext_export FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              $erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_euermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_euermaessigt FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
            }elseif($ust_befreit == 3)
            {
              $erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_nichtsteuerbar FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_nichtsteuerbar FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              $steuersatz = 0;
            }else{
              $steuertext = '';
              if($umsatzsteuer == 'ermaessigt')
              {
                $erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_ermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_ermaessigt FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
                //$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_ermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              }elseif($umsatzsteuer == 'befreit'){
                $erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_nichtsteuerbar FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_nichtsteuerbar FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              }else{
                $erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_normal FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_normal FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");                
                //$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_normal FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              }
            }
            
            if($steuersatz !== false && !is_null($steuersatz))
            {
              //$this->app->DB->Update("UPDATE $table SET steuersatz = '$steuersatz' WHERE id = '$newposid' LIMIT 1");
            }
            //if($erloese)$this->app->DB->Update("UPDATE $table SET erloese = '$erloese' WHERE id = '$newposid' LIMIT 1");
            //($steuertext)$this->app->DB->Update("UPDATE $table SET steuertext = '".$this->app->DB->real_escape_string($steuertext)."' WHERE id = '$newposid' LIMIT 1");
          
          $this->app->erp->GutschriftNeuberechnen($id);
        } else 
        if ($module == "auftrag" || $module == "rechnung" || $module == "angebot" || $module == "proformarechnung") {

          $waehrung = $this->app->Secure->GetPOST("waehrung");
          if($waehrung=="") 
            $waehrung = $this->app->DB->Select("SELECT waehrung FROM $module WHERE id='$id' LIMIT 1");
          if ($artikel_id > 0) {
            $articleRow = $this->app->DB->SelectRow(sprintf('SELECT * FROM artikel WHERE id = %d', $artikel_id));
            $istportoartikel = !empty($articleRow['porto']);// $this->app->DB->Select("SELECT id FROM artikel WHERE id='$artikel_id' AND porto='1'");
            $istdienstleistungsartikel = !empty($articleRow['dienstleistung']);//$this->app->DB->Select("SELECT id FROM artikel WHERE id='$artikel_id' AND dienstleistung='1'");
            $istindividualartikel = !empty($articleRow['individualartikel']);//$this->app->DB->Select("SELECT id FROM artikel WHERE id='$artikel_id' AND individualartikel='1'");
            if($module == "angebot" && $this->app->erp->Firmendaten('staffelpreiseanzeigen') && !$istportoartikel && !$istdienstleistungsartikel && !$istindividualartikel){
              $id = $this->app->Secure->GetGET("id");
              $adresse = $this->app->DB->Select("SELECT adresse FROM angebot WHERE id ='$id'");
              $allestaffelpreise = array();
              $allestaffelpreise = $this->app->DB->SelectArr("SELECT * FROM verkaufspreise WHERE artikel='$artikel_id' AND geloescht = 0 AND (gueltig_bis >= '".date("Y-m-d")."' OR gueltig_bis = '0000-00-00') AND (gueltig_ab <= '".date("Y-m-d")."' OR gueltig_ab = '0000-00-00') AND (adresse='0' OR adresse='$adresse') AND (gruppe='0') AND inbelegausblenden=0");
              $anzeigepreise = array();
              for ($i=0; $i < count($allestaffelpreise); $i++) {
                if(!isset($anzeigepreise[$allestaffelpreise[$i]['ab_menge']])){
                  $anzeigepreise[$allestaffelpreise[$i]['ab_menge']] = $allestaffelpreise[$i];
                }else{
                  if($allestaffelpreise[$i]['adresse'] != '0' || ($anzeigepreise[$allestaffelpreise[$i]['ab_menge']]['gruppe'] == '0' && $anzeigepreise[$allestaffelpreise[$i]['ab_menge']]['adresse'] == '0')){
                    $anzeigepreise[$allestaffelpreise[$i]['ab_menge']] = $allestaffelpreise[$i];
                  }
                }
              }
              if(count($anzeigepreise)>1){
                ksort($anzeigepreise);
                $staffelpreistext = '';
                $belegsprache = $this->app->DB->Select("SELECT sprache FROM $module WHERE id='$id' LIMIT 1");
                $praefixwort = $this->app->erp->Beschriftung("dokument_staffelpreis_von", $belegsprache).' ';
                $beschriftungeinheit = $this->app->erp->Beschriftung("dokument_staffelpreis_stueck", $belegsprache);
                $artikeleinheit = $this->app->DB->Select("SELECT einheit FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                $standardeinheit = $this->app->erp->Firmendaten("artikeleinheit_standard");

                if($praefixwort === ' '){
                  $praefixwort = 'from ';
                }
                if($beschreibung){
                  $staffelpreistext = '<br />';
                }
                if($standardeinheit){
                  $einheit = $standardeinheit;
                }
                if($beschriftungeinheit){
                  $einheit = $beschriftungeinheit;
                }
                if($artikeleinheit){
                  $einheit = $artikeleinheit;
                }

                foreach ($anzeigepreise as $key => $value) {
                  $praefix = $praefixwort;
                  if($key == 1){
                    $praefix = '';
                  }
                  $staffelpreistext .= $praefix.number_format($key,($key == (int)$key?0:2),",",".")." $einheit ".$this->app->DB->real_escape_string($this->app->erp->formatMoney($value['preis'],$value['waehrung'],2))." ".$value['waehrung']."<br />";
                }

                if($staffelpreistext != ''){
                  $beschreibung .= $staffelpreistext;
                }
              }
            }
            $steuersatzartikel = $this->app->DB->Select("SELECT IFNULL(steuersatz,'NULL') FROM artikel WHERE id='$artikel_id' LIMIT 1");

            // mlm punkte bei angebot, auftrag und rechnung
            $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,beschreibung,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe,punkte,bonuspunkte,mlmdirektpraemie,artikelnummerkunde,zolltarifnummer,kostenstelle,herkunftsland,einheit,rabatt,keinrabatterlaubt,lieferdatumkw,steuersatz)
                VALUES ('','$id','$artikel_id','$beschreibung','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuer','angelegt','$projekt','$vpe','$mlmpunkte','$mlmbonuspunkte','$mlmdirektpraemie','$artikelnummerkunde','$zolltarifnummer','$kostenstelle','$herkunftsland','$einheit','$rabatt','$keinrabatterlaubt','$lieferdatumkw',$steuersatzartikel)");
            $newposid = $this->app->DB->GetInsertID();
            $menge2 = $this->app->erp->PruefeMengeVPE($table, $newposid, $menge);
            if($menge != $menge2)$this->app->DB->Update("UPDATE $table SET menge = '$menge2' WHERE id = '$newposid' LIMIT 1");
            $originalpreis = $preis;
            $originalwaehrung = $waehrung;
            $ekpreisp =  $this->app->erp->GetEinkaufspreisWaehrung($artikel_id, $menge, $waehrung, $originalwaehrung, $originalpreis);
            $dbeitrag = 1;
            if($ekpreisp && $preis*(100-$rabatt)/100 != 0)
            {
              $dbeitrag = ($preis*(100-$rabatt)/100-$ekpreisp)/($preis*(100-$rabatt)/100);
            }
            $this->app->DB->Update("UPDATE $table SET einkaufspreis = '$ekpreisp', einkaufspreisurspruenglich = '$originalpreis',ekwaehrung = '$originalwaehrung', deckungsbeitrag = '$dbeitrag' WHERE id = '$newposid' LIMIT 1");
            $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM $module WHERE id = '$id' LIMIT 1");
            $umsatzsteuer = $articleRow['umsatzsteuer'];//$this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id = '$artikel_id' LIMIT 1");

            //$kategorie = str_replace('_kat','',$this->app->DB->Select("SELECT typ FROM artikel WHERE id = '$artikel_id' LIMIT 1"));
            $kategorie = str_replace('_kat','',$articleRow['typ']);
            if($kategorie && is_numeric($kategorie))
            {
              $kategorie = (int)$kategorie;
              $kategorie = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
            }else {
              $kategorie = false;
            }
            if($umsatzsteuer != 'ermaessigt' && $umsatzsteuer != 'befreit') {
              $umsatzsteuer = 'normal';
            }
            $steuersatz = false;
            if($ust_befreit == 1)
            {
              //$steuertext = $articleRow['steuertext_innergemeinschaftlich'];//$this->app->DB->Select("SELECT steuertext_innergemeinschaftlich FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              if(!$steuertext && $kategorie) {
                ///$steuertext = $this->app->DB->Select("SELECT steuertext_innergemeinschaftlich FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              }
              if($umsatzsteuer === 'ermaessigt')
              {
                //$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_euermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                if(!$erloese && $kategorie) {
                  //$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_euermaessigt FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
                }
                //$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_euermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                //if(($steuersatz === false || is_null($steuersatz)) && $kategorie)$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_euermaessigt FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              }elseif($umsatzsteuer === 'befreit'){
                //$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_nichtsteuerbar FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                //$steuersatz = 0;
              }else{
                ///$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_eunormal FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                //if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_eunormal FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
                //$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_eunormal FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                //if(($steuersatz === false || is_null($steuersatz)) && $kategorie)$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_eunormal FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              }
            }elseif($ust_befreit == 2)
            {
              //$steuertext = $this->app->DB->Select("SELECT steuertext_export FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              //if(!$steuertext && $kategorie)$steuertext = $this->app->DB->Select("SELECT steuertext_export FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              //$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_euermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              //if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_euermaessigt FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
            }elseif($ust_befreit == 3)
            {
              //$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_nichtsteuerbar FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              $steuersatz = 0;
            }else{
              //$steuertext = '';
              if($umsatzsteuer === 'ermaessigt')
              {
                //$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_ermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                //if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_ermaessigt FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
                //$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_ermaessigt FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                //if(($steuersatz === false || is_null($steuersatz)) && $kategorie)$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_ermaessigt FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              }elseif($umsatzsteuer === 'befreit')
              {
                //$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_nichtsteuerbar FROM artikel WHERE id = '$artikel_id' LIMIT 1");
              }else{
                //$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_normal FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                //if(!$erloese && $kategorie)$erloese = $this->app->DB->Select("SELECT steuer_erloese_inland_normal FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
                //$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_normal FROM artikel WHERE id = '$artikel_id' LIMIT 1");
                //if(($steuersatz === false || is_null($steuersatz)) && $kategorie)$steuersatz = $this->app->DB->Select("SELECT steuersatz_erloese_normal FROM artikelkategorien WHERE id = '$kategorie' LIMIT 1");
              }
            }
            if($steuersatz !== false && !is_null($steuersatz))
            {
              //$this->app->DB->Update("UPDATE $table SET steuersatz = '$steuersatz' WHERE id = '$newposid' LIMIT 1");
            }
            //if($erloese)$this->app->DB->Update("UPDATE $table SET erloese = '$erloese' WHERE id = '$newposid' LIMIT 1");
            //if($steuertext)$this->app->DB->Update("UPDATE $table SET steuertext = '".$this->app->DB->real_escape_string($steuertext)."' WHERE id = '$newposid' LIMIT 1");
            
            switch ($module) {
              case "angebot":
                $this->app->erp->AngebotNeuberechnen($id);
              break;
              case "auftrag":
                $this->app->DB->Update(
                  sprintf(
                    'UPDATE `artikel` SET `laststorage_changed` = NOW() WHERE `id` = %d',
                    $artikel_id
                  )
                );
                $this->app->erp->AuftragNeuberechnen($id);
                $this->app->erp->AuftragEinzelnBerechnen($id);
              break;
              case "rechnung":
                $this->app->erp->RechnungNeuberechnen($id);
              break;
            }
          }
        } elseif($module == "verbindlichkeit") {
          if ($artikel_id > 0) {
            $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,beschreibung,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe)
                VALUES ('','$id','$artikel_id','$beschreibung','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuer','angelegt','$projekt','$vpe')");
            $newposid = $this->app->DB->GetInsertID();            
          }
        } else {
          if ($artikel_id > 0) {
            $newposid = null;
            $inputArr = [
              'article_id'  => $artikel_id,
              'description' => $beschreibung,
              'name'        => $bezeichnung,
              'number'      => $neue_nummer,
              'quantity'    => $menge,
              'price'       => $preis,
              'currency'    => $waehrung,
              'sort'        => $sort,
              'document_id' => $id
            ];
            $this->app->erp->RunHook('yui_insert_position', 3, $table, $inputArr, $newposid);
            // mlm punkte bei angebot, auftrag und rechnung
            if($newposid === null) {
              $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,beschreibung,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe,punkte,bonuspunkte,mlmdirektpraemie)
                VALUES ('','$id','$artikel_id','$beschreibung','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuer','angelegt','$projekt','$vpe','$mlmpunkte','$mlmbonuspunkte','$mlmdirektpraemie')");
              $newposid = $this->app->DB->GetInsertID();
            }
          }
        }

        $module = $this->app->Secure->GetGET("module");

        if($module=="angebot" || $module=="auftrag" || $module=="rechnung" || $module=="gutschrift"){
          $this->app->erp->RunHook("beleg_afterinsertposition", 5, $module, $id, $artikel_id, $menge, $newposid);
        }

        // Wenn Unikat-Artikel (Individualartikel) > direkt nach dem Einfügen das Bearbeiten-Popup öffnen
        if($newposid > 0){
          $istUnikat = (bool)$this->app->DB->Select(sprintf("SELECT a.unikat FROM artikel AS a WHERE a.id = '%s'", $artikel_id));
          if ($istUnikat) {
            $this->app->Tpl->Add('TAB1',"<script>window.setTimeout(function(){ $('a.popup[data-position-id={$newposid}]').trigger('click');}, 650);</script>");
          }
        }

        if($newposid > 0 && $module!="reisekosten")
        {
          $tmpfreifelder = $this->app->DB->SelectArr("SELECT * FROM artikel WHERE id='".$artikel_id."'");
          if($variante_von)$tmpfreifelder_variante_von = $this->app->DB->SelectArr("SELECT * FROM artikel WHERE id='".$variante_von."'");
          switch($module)
          { 
            case "angebot": $kurz="an"; break;
            case "auftrag": $kurz="ab"; break;
            case "rechnung": $kurz="re"; break;
            case "gutschrift": $kurz="gs"; break;
            case "lieferschein": $kurz="ls"; break;
            case "retoure": $kurz="rt"; break;
            case "bestellung": $kurz="be"; break;
            case "proformarechnung": $kurz="pr"; break;
            case "preisanfrage": $kurz="pa"; break;
          }   

          $language = $this->app->erp->GetSpracheBelegISO($module,$id);

          for($ifreifeld=1;$ifreifeld<=40;$ifreifeld++)
          {
            if($this->app->erp->Firmendaten("freifeld".$ifreifeld.$kurz)=="1")
            {
              $check_language = $this->app->DB->Select("SELECT wert FROM artikel_freifelder WHERE artikel=$artikel_id AND sprache='$language' LIMIT 1");
              if($check_language!="") $tmpfreifelder[0]['freifeld'.$ifreifeld] = $check_language ;

              if((String)$tmpfreifelder[0]['freifeld'.$ifreifeld] !== "")
              {
                $this->app->DB->Update("UPDATE $table SET freifeld".$ifreifeld."='".$this->app->DB->real_escape_string($tmpfreifelder[0]['freifeld'.$ifreifeld])."' WHERE id='".$newposid."'");
              }elseif($variante_von){
                $this->app->DB->Update("UPDATE $table SET freifeld".$ifreifeld."='".$this->app->DB->real_escape_string($tmpfreifelder_variante_von[0]['freifeld'.$ifreifeld])."' WHERE id='".$newposid."'");
              }
            }
          } 

          // ohne preis im PDF
          if($module=="angebot" || $module=="auftrag" || $module=="rechnung" || $module=="gutschrift")
            $this->app->DB->Update("UPDATE $table SET ohnepreis='$ohnepreisimpdf' WHERE id='".$newposid."'");
        }
        if($module=="auftrag" || $module=="rechnung" || $module=="gutschrift")$this->app->DB->Update("UPDATE $module SET extsoll = 0 WHERE id = '$id'");
      }
      
      if ($module == "produktion") {
      }
      
      if ($module == "auftrag") {
        $this->app->erp->AuftragExplodieren($id, "auftrag");
      }
      
      $this->app->erp->RunHook("AARLGPositionen_cmds_end2", 1, $id);

      /* ende neu anlegen formular */
      $this->app->Tpl->Set('SUBSUBHEADING', "Positionen");
      if($module == 'produktion')
      {
        $menu = array("edit" => "positioneneditpopup","copy"=>"copy{$module}position", "del" => "del{$module}position");        
      } else {
      
      $menu = array("up" => "up{$module}position", "down" => "down{$module}position",

      //"add"=>"addstueckliste",
      "edit" => "positioneneditpopup","copy"=>"copy{$module}position", "del" => "del{$module}position");
      }

      $anzeigebrutto = false;
      $preiscell = 'b.preis';
      if($module == 'auftrag' || $module == 'rechnung' || $module == 'gutschrift' || $module == 'angebot' || $module == 'proformarechnung')
      {
        $docArr = $this->app->DB->SelectRow(
          sprintf(
            'SELECT typ,projekt,adresse,steuersatz_normal,steuersatz_ermaessigt,schreibschutz 
            FROM `%s` 
            WHERE id = %d 
            LIMIT 1',
            $module, $id
          )
        );
        $_anrede = !empty($docArr)?$docArr['typ']: $this->app->DB->Select("SELECT typ FROM $module WHERE id = '$id' LIMIT 1");
        $_projekt = !empty($docArr)?$docArr['projekt']:$this->app->DB->Select("SELECT projekt FROM $module WHERE id = '$id' LIMIT 1");
        $_adresse = !empty($docArr)?$docArr['adresse']:$this->app->DB->Select("SELECT adresse FROM $module WHERE id = '$id' LIMIT 1");
        $funktion = ucfirst($module).'MitUmsatzeuer';
        $_anrede = 'firma';
        if($this->app->erp->AnzeigePositionenBrutto($_anrede, $module, $_projekt, $_adresse,$id) && $this->app->erp->$funktion($id))
        {
          $umsatzsteuer_ermaessigt = !empty($docArr)?(float)$docArr['steuersatz_ermaessigt']:(float)$this->app->DB->Select("SELECT steuersatz_ermaessigt FROM $module WHERE id = '$id' LIMIT 1");
          $umsatzsteuer_normal = !empty($docArr)?(float)$docArr['steuersatz_normal']:(float)$this->app->DB->Select("SELECT steuersatz_normal FROM $module WHERE id = '$id' LIMIT 1");
          $preiscell = " round(10000000 * b.preis*(1+ if(isnull(b.steuersatz) OR b.steuersatz < 0,
          if(b.umsatzsteuer = 'befreit',0,
            if(b.umsatzsteuer = 'ermaessigt', $umsatzsteuer_ermaessigt,
              if(ifnull(b.umsatzsteuer,'') <> '', $umsatzsteuer_normal,
                if(a.umsatzsteuer = 'befreit',0,
                  if(a.umsatzsteuer = 'ermaessigt', $umsatzsteuer_ermaessigt,$umsatzsteuer_normal)
                )
              )
            )
          )
          ,b.steuersatz)  /100)) / 10000000 ";
          $anzeigebrutto = true;
        }
      }
      
      $sortcol = ' b.sort ';
      $schreibschutz = !empty($docArr)?$docArr['schreibschutz']:$this->app->DB->Select("SELECT schreibschutz FROM $module WHERE id='$id'");
      if(!$schreibschutz)$sortcol = " concat('<input type=\"checkbox\" name=\"belegsort[]\" value=\"',b.id,'\" />',b.sort) as sort ";
      if ($module == "auftrag") {
        $check_waehrung = $this->app->DB->Select("SELECT COUNT(DISTINCT waehrung) FROM auftrag_position WHERE auftrag='$id'");
        if($check_waehrung >=2)
        {
          $this->app->Tpl->Set('MESSAGE',"<div class=\"warning\">Achtung es ist mehr als eine W&auml;hrungsart angegeben. In dem Auftrag darf nur eine W&auml;hrungsart angeben sein!</div>");
        } else {
          $waehrung_bestellung = $this->app->DB->Select("SELECT waehrung FROM auftrag_position WHERE auftrag='$id' LIMIT 1");
          $this->app->DB->Update("UPDATE auftrag SET waehrung='$waehrung_bestellung' WHERE id='$id' AND waehrung='' LIMIT 1");
        }
        
        $sql = "SELECT 
          $sortcol,
          CONCAT($hersteller_ansicht if(b.explodiert_parent,if(b.beschreibung!='',
                if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT('<i>',SUBSTR(CONCAT(b.bezeichnung,' *'),1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...','</i>'),CONCAT('<i>',b.bezeichnung,' *','</i>')),
                if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT('<i>',SUBSTR(b.bezeichnung,1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...','</i>'),CONCAT('<i>',b.bezeichnung,' (zu St&uuml;ckliste ',(SELECT ba.nummer FROM $table ba WHERE ba.id=b.explodiert_parent LIMIT 1),')</i>'))),
              if(b.beschreibung!='',
                if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),CONCAT(b.bezeichnung,' *')),
                if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(b.bezeichnung,1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),b.bezeichnung))
            ) $erweiterte_ansicht)
            as Artikel,



               p.abkuerzung as projekt, b.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, ".$this->app->erp->FormatMenge('b.menge')." as menge, ".$this->FormatPreis($preiscell)." as preis,b.waehrung, ".$this->FormatPreis('b.rabatt')." as rabatt, ";
        
               
        $sql .= "b.id as id
                 FROM $table b
                 LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                 WHERE b.$module='$id' ";

        //WHERE b.$module='$id' AND b.explodiert_parent='0'";
        
      } else 
      if ($module == "lieferschein") {
        $sql = "SELECT 
          $sortcol,
          CONCAT($hersteller_ansicht if(b.beschreibung!='',
              if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),CONCAT(b.bezeichnung,' *')),
              if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(b.bezeichnung,1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),b.bezeichnung)) $erweiterte_ansicht)
            as Artikel,


               p.abkuerzung as projekt, b.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, ".$this->app->erp->FormatMenge('b.menge')." as menge, if(b.geliefert, ".$this->app->erp->FormatMenge('b.geliefert')." ,'-') as geliefert, b.id as id
                 FROM $table b
                 LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                 WHERE b.$module='$id'";
      } else 
      if ($module === 'retoure') {
        $projekte = $this->app->User->getUserProjects();
        $projekte[] = 0;
        $projekte = implode(', ', $projekte);

        $colgrund = '';
        $rmavorlagen = $this->app->DB->SelectPairs(
          sprintf(
            'SELECT rvg.bezeichnung, lp.kurzbezeichnung 
            FROM rma_vorlagen_grund AS rvg
            LEFT JOIN lager_platz AS lp ON rvg.default_storagelocation = lp.id AND lp.geloescht <> 1
            WHERE rvg.ausblenden = 0 AND (rvg.projekt IN (%s)) 
            ORDER BY rvg.bezeichnung',
            $projekte
          )
        );


        $bezeichnungenArr = ["''"];
        if($rmavorlagen) {
          foreach($rmavorlagen as $v => $lag) {
            $bezeichnungenArr[] = sprintf(
              "'%s'",
              $this->app->DB->real_escape_string($v)
            );
          }
        }

        $colgrund = sprintf(
          "CONCAT('<select class=\"selgrund\" data-id=\"',b.id,'\">',IF(b.grund NOT IN (%s), CONCAT('<option>',b.grund,'</option>'),'')",
          implode(',',$bezeichnungenArr)
        );
        foreach($rmavorlagen as $v => $lag) {
          $colgrund .= sprintf(",CONCAT('<option data-lager=\"%s\" ',IF(b.grund = '%s',' selected ',''),'>%s</option>')",
           $this->app->DB->real_escape_string($lag), $this->app->DB->real_escape_string($v), $v
          );
        }

        $colgrund .= ",'</select>')";


        $sql = "SELECT 
          $sortcol,
          CONCAT($hersteller_ansicht if(b.beschreibung!='',
              if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),CONCAT(b.bezeichnung,' *')),
              if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(b.bezeichnung,1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),b.bezeichnung)) $erweiterte_ansicht)
            as Artikel,


               p.abkuerzung as projekt, b.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, 
               ".$this->app->erp->FormatMenge('b.menge')." as menge, 
               if(b.geliefert, ".$this->app->erp->FormatMenge('b.geliefert')." ,'-') as geliefert,
               if(b.menge_eingang, ".$this->app->erp->FormatMenge('b.menge_eingang')." ,'-') as `Eingang`,
               if(b.menge_gutschrift, ".$this->app->erp->FormatMenge('b.menge_gutschrift')." ,'-') as `Menge Gutschrift`,
               $colgrund AS `Grund`, 
               IFNULL(lp.kurzbezeichnung,'') as `Lager`,
               b.id as id
                 FROM $table AS b
                 LEFT JOIN lager_platz AS lp ON b.default_storagelocation = lp.id
                 LEFT JOIN artikel AS a ON a.id=b.artikel 
                 LEFT JOIN projekt AS p ON b.projekt=p.id
                 WHERE b.$module='$id'";
      } else 
      if ($module == "inventur") {
        $sql = "SELECT 
          $sortcol,
          if(b.beschreibung!='',
              if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),CONCAT(b.bezeichnung,' *')),
              if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(b.bezeichnung,1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),b.bezeichnung))
            as Artikel,


               p.abkuerzung as projekt, b.nummer as nummer, ".$this->app->erp->FormatMenge('b.menge')."  as menge, 
               ".$this->FormatPreis(' b.preis')." as preis,

               b.id as id
                 FROM $table b
                 LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                 WHERE b.$module='$id'";
      } else 
      if ($module == "anfrage" || $module == "preisanfrage") {
        $sql = "SELECT 
          $sortcol,
          if(b.beschreibung!='',
              if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),CONCAT(b.bezeichnung,' *')),
              if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(b.bezeichnung,1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),b.bezeichnung))
            as Artikel,


               p.abkuerzung as projekt, b.nummer as nummer, DATE_FORMAT(b.lieferdatum,'%d.%m.%Y') as lieferdatum, ".$this->app->erp->FormatMenge('b.menge')." as menge,

               b.id as id
                 FROM $table b
                 LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                 WHERE b.$module='$id'";
      } else 
      if ($module == "bestellung") {
        
        $check_waehrung = $this->app->DB->Select("SELECT COUNT(DISTINCT waehrung) FROM bestellung_position WHERE bestellung='$id'");
        if($check_waehrung >=2)
        {
          $this->app->Tpl->Set('MESSAGE',"<div class=\"warning\">Achtung es ist mehr als eine W&auml;hrungsart angegeben. In der Bestellung darf nur eine W&auml;hrungsart angeben sein!</div>");
        } else {
          $waehrung_bestellung = $this->app->DB->Select("SELECT waehrung FROM bestellung_position WHERE bestellung='$id' LIMIT 1");
          $this->app->DB->Update("UPDATE bestellung SET waehrung='$waehrung_bestellung' WHERE id='$id' AND waehrung='' LIMIT 1");
        }

        $sql = "SELECT $sortcol,CONCAT($hersteller_ansicht if(b.beschreibung!='',
          if(CHAR_LENGTH(b.bezeichnunglieferant)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(CONCAT(b.bezeichnunglieferant,' *'),1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),CONCAT(b.bezeichnunglieferant,' *')),
            if(CHAR_LENGTH(b.bezeichnunglieferant)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(b.bezeichnunglieferant,1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),b.bezeichnunglieferant)))
              as Artikel,
                 p.abkuerzung as projekt,  a.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum,".$this->app->erp->FormatMenge('b.menge')." as menge, ".$this->FormatPreis(' b.preis')."  as preis, b.waehrung, b.id as id
                   FROM $table b
                   LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                   WHERE b.$module='$id'";
      } else 
      if ($module == "arbeitsnachweis") {
        $sql = "SELECT $sortcol,
          adr.name as name,
          b.ort as ort,
          DATE_FORMAT(datum,'%d.%m.%Y') as rdatum,
          b.von as von,
          b.bis as bis,

          if(b.beschreibung!='',
              if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),CONCAT(b.bezeichnung,' *')),
              if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(b.bezeichnung,1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),b.bezeichnung))
            as Artikel,
               b.id as id
                 FROM $table b
                 LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN adresse adr ON adr.id=b.adresse LEFT JOIN projekt p ON b.projekt=p.id
                 WHERE b.$module='$id'";
      } else 
      if ($module == "kalkulation") {

         $this->app->erp->KalkulationNeuberechnen($id);
          
         $tmp_when = $this->app->erp->GetKalkulationartAssoc();
         foreach($tmp_when as $key=>$value) $str_when .=" WHEN '$key' THEN '$value' "; 


        $sql = "SELECT $sortcol,
          CASE b.kalkulationart 
          $str_when
          END,
      if(b.beschreibung!='',
                    if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung(-20) . ",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1," . $this->app->erp->MaxArtikelbezeichnung(-20) . "),'...'),CONCAT(b.bezeichnung,' *')),
                    if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung(-20) . ",CONCAT(SUBSTR(b.bezeichnung,1," . $this->app->erp->MaxArtikelbezeichnung(-20) . "),'...'),b.bezeichnung))
                  as Artikel,
          trim(b.menge)+0 as menge,
          FORMAT(b.betrag,2{$extended_mysql55}) as betrag,
          FORMAT(b.gesamt,2{$extended_mysql55}) as gesamt,

                               b.id as id
                       FROM $table b
                       LEFT JOIN projekt p ON b.projekt=p.id 
                       WHERE b.$module='$id'";
      } else 

      if ($module == "reisekosten") {
        $sql = "SELECT $sortcol,
          DATE_FORMAT(datum,'%d.%m.%Y') as rdatum,
          CONCAT(rk.nummer,'- ',rk.beschreibung) as kostenart,
          FORMAT(b.betrag,2{$extended_mysql55}) as betrag,
          if(b.abrechnen,'ja','') as abrechnen,
            if(b.keineust,'keine MwSt','') as keine,
              CONCAT(b.uststeuersatz,' %') as uststeuersatz,

                if(b.beschreibung!='',
                    if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung(-20) . ",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1," . $this->app->erp->MaxArtikelbezeichnung(-20) . "),'...'),CONCAT(b.bezeichnung,' *')),
                    if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung(-20) . ",CONCAT(SUBSTR(b.bezeichnung,1," . $this->app->erp->MaxArtikelbezeichnung(-20) . "),'...'),b.bezeichnung))
                  as Artikel,
                     b.bezahlt_wie as bezahlt,
                     b.id as id
                       FROM $table b
                       LEFT JOIN projekt p ON b.projekt=p.id LEFT JOIN reisekostenart rk ON rk.id=b.reisekostenart
                       WHERE b.$module='$id'";
      } else 
      if ($module == "produktion") {
      } else 
      if ($module == "rechnung" || $module == "angebot" || $module == "gutschrift" || $module == "proformarechnung") {
        $check_waehrung = $this->app->DB->Select("SELECT COUNT(DISTINCT waehrung) FROM ".$module."_position WHERE ".$module."='$id'");
        if($check_waehrung >=2)
        {
          $this->app->Tpl->Set('MESSAGE',"<div class=\"warning\">Achtung es ist mehr als eine W&auml;hrungsart angegeben. In ".($module=="angebot"?'dem':'der').' '.ucfirst($module)." darf nur eine W&auml;hrungsart angeben sein!</div>");
        } else {
          $waehrung_bestellung = $this->app->DB->Select("SELECT waehrung FROM ".$module."_position WHERE ".$module."='$id' LIMIT 1");
          $this->app->DB->Update("UPDATE ".$module." SET waehrung='$waehrung_bestellung' WHERE id='$id' AND waehrung='' LIMIT 1");
        }

        if($module=="angebot") {
          $erweiterte_ansicht = " ,if(b.optional,' <strong>(Optional)</strong>','') ".$erweiterte_ansicht;
        }

         

        //$sql = "SELECT if(b.beschreibung!='',if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),CONCAT(b.bezeichnung,' *'),SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung().")) as Artikel,
        
        if($module == 'angebot')
        {
        $sql = "SELECT $sortcol, CONCAT($hersteller_ansicht if(b.explodiert_parent,if(b.beschreibung!='',
                if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT('<i>',SUBSTR(CONCAT(b.bezeichnung,' *'),1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...','</i>'),CONCAT('<i>',b.bezeichnung,' *','</i>')),
                if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT('<i>',SUBSTR(b.bezeichnung,1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...','</i>'),CONCAT('<i>',b.bezeichnung,' (zu St&uuml;ckliste ',(SELECT ba.nummer FROM $table ba WHERE ba.id=b.explodiert_parent LIMIT 1),')</i>'))),
              if(b.beschreibung!='',
                if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),CONCAT(b.bezeichnung,' *')),
                if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(b.bezeichnung,1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),b.bezeichnung))
            ) $erweiterte_ansicht)
            as Artikel,
        
        
        
                              p.abkuerzung as projekt, a.nummer as nummer, b.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, trim(b.menge)+0 as menge,  ".$this->FormatPreis($preiscell)." as preis
                              
                              
                              ,b.waehrung, b.rabatt as rabatt,";          
        }else{
        $sql = "SELECT $sortcol, CONCAT($hersteller_ansicht if(b.beschreibung!='',
                       if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),CONCAT(b.bezeichnung,' *')),
                         if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(b.bezeichnung,1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),b.bezeichnung)) $erweiterte_ansicht)
                           as Artikel,
                              p.abkuerzung as projekt, a.nummer as nummer, b.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, trim(b.menge)+0 as menge,  ".$this->FormatPreis($preiscell)." as preis
                              
                              
                              ,b.waehrung, b.rabatt as rabatt,";
        }
                            $sql .=  "b.id as id
                                FROM $table b
                                LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                                WHERE b.$module='$id'";

      } else {
        $sql = null;
        $this->app->erp->RunHook('yui_position_sql', 3, $table, $id, $sql);
        if($sql === null){
          //$sql = "SELECT if(b.beschreibung!='',if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),CONCAT(b.bezeichnung,' *'),SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung().")) as Artikel,
          $sql = "SELECT $sortcol, if(b.beschreibung!='',
          if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),CONCAT(b.bezeichnung,' *')),
            if(CHAR_LENGTH(b.bezeichnung)>" . $this->app->erp->MaxArtikelbezeichnung() . ",CONCAT(SUBSTR(b.bezeichnung,1," . $this->app->erp->MaxArtikelbezeichnung() . "),'...'),b.bezeichnung))
              as Artikel,
                 p.abkuerzung as projekt, a.nummer as nummer, b.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, trim(b.menge)+0 as menge, " . $this->FormatPreis($preiscell) . " as preis, b.id as id
                   FROM $table b
                   LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                   WHERE b.$module='$id'";
        }
      }
      //$this->app->Tpl->Add(EXTEND,"<input type=\"submit\" value=\"Gleiche Positionen zusammenf&uuml;gen\">");
      $this->app->YUI->SortListAdd('TAB1', $this, $menu, $sql);
      
      
      if ($schreibschutz != "1") {

        if($module=='angebot' || $module=='auftrag' || $module=='rechnung')
          $tplrabatt = "&nbsp;<input type=\"button\" value=\"Rabatt in % auf alle Positionen\" onclick=\"var rabatt =  prompt('Rabatt in % (wird auf jede Position angewendet):',''); if(parseFloat(rabatt.replace(',','.')) >= 0) window.location.href='index.php?module=artikel&action=rabatt&id=$id&cmd={$module}&fmodul={$fmodul}&rabatt='+rabatt;\">&nbsp;";

        $this->app->Tpl->Add('TAB1', "<div class='aarlg-toolbar'>");
        $this->app->Tpl->Add('TAB1', "<div class=\"aarlg-toolbar-left\">");
        if ($module != "arbeitsnachweis")
        {
          $aktionoptionen = '';
          $aktionjs = '';
          $this->app->erp->RunHook('AARLGPositionen_aktion', 4, $module, $id, $aktionoptionen, $aktionjs);
          $this->app->Tpl->Add('AUFTAG_POSITIONUEBERSICHT_HOOK1', $aktionjs);
          $this->app->Tpl->Add('TAB1' ,"<input type=\"checkbox\" onclick=\"alleauswaehlen(this);\"> {|alle ausw&auml;hlen|} <select id=\"aktion_positionen\" onchange=\"aktionpositionen(this);\"><option value=\"\">{|- Bitte ausw&auml;hlen -|}</option><option value=\"loeschen\">{|L&ouml;schen|}</option>".$aktionoptionen."</select>");
        }
        $this->app->Tpl->Add('TAB1', "</div>"); // ENDE .aarlg-toolbar-left

        $this->app->Tpl->Add('TAB1', "<div class=\"aarlg-toolbar-right\">"); // ENDE .aarlg-toolbar-left
        if(in_array($module ,array('auftrag', 'rechnung', 'gutschrift', 'angebot', 'lieferschein', 'bestellung','proformarechnung')))
        {
          $extraoption = '';
          if($module === 'angebot'){
            $extraoption = '<option value="gruppensummemitoptionalenpreisen">Gruppensumme mit optionalen Preisen</option>';
          }
          $this->app->Tpl->Add('TAB1', "<!--<input type=\"button\" value=\"Gleiche Positionen zusammenf&uuml;gen\">&nbsp;-->
          <select name=\"feldart\" id=\"feldart\">
            <option value=\"gruppe\">Gruppen&uuml;berschrift</option>
            <option value=\"zwischensumme\">Zwischensumme</option>
            <option value=\"gruppensumme\">Gruppensumme</option>
            $extraoption
            <option value=\"seitenumbruch\">Seitenumbruch</option>
            <option value=\"bild\">Bild</option>
            </select> <input type=\"button\" id=\"spezialfeldeinfuegen\" value=\"Spezialfeld einf&uuml;gen\">&nbsp;"
            ."&nbsp;");
          $target = 'TAB1';
          $this->app->erp->RunHook("BelegPositionenButtons", 3, $target, $module, $id);

          $this->app->Tpl->Add('TAB1', $tplrabatt);
        } else if (in_array($module ,array('preisanfrage'))) {
          $this->app->Tpl->Add('TAB1', "
          <select name=\"feldart\" id=\"feldart\">
            <option value=\"gruppe\">Gruppen&uuml;berschrift</option>
            <option value=\"seitenumbruch\">Seitenumbruch</option>
            <option value=\"bild\">Bild</option>
            </select> <input type=\"button\" id=\"spezialfeldeinfuegen\" value=\"Spezialfeld einf&uuml;gen\">&nbsp;"
            ."&nbsp;");
          $target = 'TAB1';
          $this->app->erp->RunHook("BelegPositionenButtons", 3, $target, $module, $id);

          $this->app->Tpl->Add('TAB1', $tplrabatt);

        }
        $this->app->Tpl->Add('TAB1', "<input type=\"button\" value=\"Artikel manuell suchen / neu anlegen\" id=\"artikel-profisuche-button\" data-location=\"index.php?module=artikel&action=profisuche&cmd={$module}&fmodul={$fmodul}&id=$id\" onclick=\"window.location.href='index.php?module=artikel&action=profisuche&cmd={$module}&fmodul={$fmodul}&id=$id';\">[EXTRAPOSBUTTONS]");
        $this->app->Tpl->Add('TAB1', "</div>"); // ENDE .aarlg-toolbar-right
        $this->app->Tpl->Add('TAB1', "</div>"); // ENDE .aarlg-toolbar
      }
      $this->app->BuildNavigation = false;
      $this->app->Tpl->Add('EXTRAPOSBUTTONS','');

      $this->app->Tpl->Add('PAGE', "<br><fieldset>");
      
      $scrollpos = $this->app->User->GetParameter('positioneniframe_savescrollpos_'.$module);

      
      $this->DrawPositionExtras($module, $id, $schreibschutz);
      if ($module == "arbeitsnachweis") $this->app->Tpl->Parse('PAGE', "arbeitsnachweis_positionuebersicht.tpl");
      else 
      {
        if((String)$scrollpos !== '')
        {
          $this->app->User->SetParameter('positioneniframe_savescrollpos_'.$module, '');
          $this->app->Tpl->Add('PAGE','<script>
          $( document ).ready(function() {
            $(window).scrollTop('.(int)$scrollpos.');
          });
          </script>');
        }else{
          $this->app->Tpl->Add('PAGE','<script>
            /*if(document.getElementById(\'artikel\') != null)
            {
              document.getElementById(\'artikel\').value = ""; 
              document.getElementById(\'artikel\').focus();
            }*/
            var artel = $(\'#artikel\');
            var iframe = window.parent.document.getElementById(\'framepositionen\');
            if(iframe && $(iframe).is(\':visible\'))
            {
              if(artel != null)$(\'#artikel\').val(\'\'); 
              if(artel != null)$(\'#artikel\').focus();
            }
            
            
          </script>');
        }
        $this->app->Tpl->Parse('PAGE', "auftrag_positionuebersicht.tpl");
      }
      $this->app->Tpl->Add('PAGE', "</fieldset>");
    }
  }
  
  function FormatPreis($spalte)
  {
    return "if(trim(round( $spalte *100))+0 <> trim($spalte*100)+0, format($spalte,  length( trim($spalte)+0)-length(round($spalte))-1  ,'de_DE'),format($spalte,2,'de_DE'))";
  }
  
  
  function DrawPositionExtras($module, $id, $schreibschutz = 0)
  {
    $modules = array('auftrag', 'rechnung', 'gutschrift', 'angebot', 'lieferschein', 'retoure', 'bestellung', 'produktion','proformarechnung','preisanfrage');
    if(!in_array($module, $modules))return;
    $this->ReSortDrawItem($module, $id);
    /*$drawpositionen = $this->app->DB->SelectArr("SELECT * FROM beleg_zwischenpositionen WHERE doctype like '".$module."' AND doctypeid = '$id' ORDER BY pos, sort DESC");
    foreach($drawpositionen as $k => $v)
    {
      if($v['wert'])
      {
        $j = json_decode($v['wert']);
        if($j !== false)
        {
          $drawpositionen[$k]['wert'] = $j;
        }
      }
    }*/
    
    //$this->app->Tpl->Add('DRAWPOSITIONEN',json_encode($drawpositionen, JSON_HEX_APOS));
    $this->app->Tpl->Add('DRAWPOSITIONEN','');
    if($schreibschutz)
    {
      $this->app->Tpl->Set('SCHREIBSCHUTZ','true');
    }else{
      $this->app->Tpl->Set('SCHREIBSCHUTZ','false');
      if($this->app->erp->Firmendaten("briefhtml")=="1")
        $this->CkEditor('grtext',"belege");
    }
    if($module === 'angebot'){
      $this->app->Tpl->Set('GRUPPENSUMMEMITOPTIONALENPREISEN','<option value="gruppensummemitoptionalenpreisen">Gruppensumme mit optionalen Preisen</option>');
    }
    $this->app->Tpl->Set('ID',$this->app->Secure->GetGET('id'));
    $this->app->Tpl->Set('DOCTYP',$this->app->Secure->GetGET('module'));
    $this->app->Tpl->Set('COLOURODD','#e0e0e0');
    $this->app->Tpl->Set('COLOUREVEN','#fff');
    $this->app->Tpl->Set('DRAWTHEME',$this->app->Conf->WFconf['defaulttheme']);
    $this->app->Tpl->Set('FMODUL',$this->app->Secure->GetGET('fmodul'));
    $this->app->Tpl->Parse('TAB1','auftrag_position_extra.tpl');
  }
  
  function FormatTableSmartphone($tableselector, $column)
  {
    $column = (int)$column;
    $name = 'x'.md5($tableselector);
    $this->app->Tpl->Add('JQUERYREADY', '
    var '.$name.'anzth = $(\''.$tableselector.'\').find(\'tr\').first().find(\'th\').length;
    var '.$name.'anztd = $(\''.$tableselector.'\').find(\'tr\').first().find(\'td\').length;
    var '.$name.'anz = '.$name.'anzth;
    if('.$name.'anz <= 0)'.$name.'anz = '.$name.'anztd;
    var '.$name.'column = '.$column.';
    var '.$name.'even = false;
    $(\''.$tableselector.'\').find(\'td\:nth-child('.$column.')\').each(function(){
      '.$name.'even = !'.$name.'even;
      $(this).toggleClass(\'hide480\', true);
      var '.$name.'html = $(this).html();
      $(this).parent().after(\'<tr class="hidegr480"><td style="background:\'+('.$name.'even?\'#e0e0e0\':\'#fff\')+\'" colspan="\'+'.$name.'anz+\'">\'+'.$name.'html+\'</td></tr>\');
      $(this).parent().find(\'td\').css(\'background\',('.$name.'even?\'#e0e0e0\':\'#fff\'));
    });    
    ');
  }
  
  function ParserVarIf($parsvar, $choose) {

    
    if ($choose == 0) {
      $this->app->Tpl->Set($parsvar . "IF", "<!--");
      $this->app->Tpl->Set($parsvar . "ELSE", "-->");
      $this->app->Tpl->Set($parsvar . "ENDIF", "");
    } else {
      $this->app->Tpl->Set($parsvar . "IF", "");
      $this->app->Tpl->Set($parsvar . "ELSE", "<!--");
      $this->app->Tpl->Set($parsvar . "ENDIF", "-->");
    }
  }
  
  function ColorPicker($name,$withhex=false) {
    if($withhex)
    {
      $this->app->Tpl->Add('JQUERY', '$( "#' . $name . '" ).colorPicker({showHexField:true});');
    } else {
      $this->app->Tpl->Add('JQUERY', '$( "#' . $name . '" ).colorPicker();');
    }
  }

  function CkEditor($name,$configuration="basic", $options = null, $target = 'JQUERY')
  {
    $useVersion5 = false;
    if(!empty($options['ckeditor5'])) {
      $this->app->Tpl->Set('CKEDITORJS',
        '<script src="./js/ckeditor5/ckeditor.js"></script>
         <script src="./js/ckeditor5/xentral_uploadadapter.js"></script>
	   ');
      unset($options['ckeditor5']);
      $useVersion5 = true;
    }


    switch($configuration)
    {
      case "none":
        $config[]=     " toolbar:
            [
            ]";
        $config[]  = " allowedContent:true ";
        $config[]  = " readOnly:true ";
        break;
      case "minimal":

        if(!$useVersion5){
          $config[] = " extraPlugins: 'colorbutton,font,removeformat' ";
          $config[]=     " toolbar: [] ";
        }
        else {
          $config[]=     "\"toolbar\":[]";
        }
        break;


      case "basic":
        $config[]=     " toolbar:
            [
                ['Bold', 'Italic', 'Underline','RemoveFormat', '-', 'Undo', 'Redo', '-', 'SelectAll'],
                ['Source']
            ]";
        $config[]  = " extraPlugins: 'removeformat' ";
        break;
      case "belege":
        $config[]=     " toolbar:
            [
                ['Bold', 'Italic', 'Underline','RemoveFormat', '-', 'Undo', 'Redo'],['NumberedList','BulletedList'],
                ['Font','FontSize','TextColor'],['Source']
            ]";
        $config[]  = " allowedContent:true ";
        $config[]  = " extraPlugins: 'colorbutton,font' ";
        break;
      case "all":
        $config[]=     " toolbar:
            [
                ['Bold', 'Italic', 'Underline','RemoveFormat', '-', 'Undo', 'Redo'],['NumberedList','BulletedList'],
                ['Font','FontSize','TextColor'],['Source']
            ]";
        $config[]  = " allowedContent:true ";
        $config[]  = " extraPlugins: 'colorbutton,font,removeformat' ";
        break;
      case "wiki":
        $wikiname = (is_array($options) && array_key_exists('wikiname', $options)) ? $options['wikiname'] : '';

        if(!$useVersion5){
          $config[] = " allowedContent:true ";
          $config[] = " extraPlugins: 'colorbutton,font,removeformat,' ";
          $config[]  = " filebrowserBrowseUrl: 'index.php?module=wiki&action=dateien&cmd={$wikiname}&subcmd=browse' ";
          $config[]  = " filebrowserWindowWidth: '60%' ";
          $config[]  = " filebrowserWindowHeight: '60%' ";
        }
        else {
          $config[] = "\"allowedContent\":true";
          $config[]=     "\"toolbar\":[\"heading\",\"removeFormat\"]";
          //$config[] = "\"extraPlugins\":[\"XentralUploadAdapterPlugin\"]";
        }
        /** @see https://ckeditor.com/docs/ckeditor4/latest/guide/dev_file_browser_api.html#example-2 */

        break;
      case "internal": // interne bemerkungen
        $config[]  = " allowedContent:true ";
        $config[]  = " extraPlugins: 'colorbutton,font,removeformat' ";
        break;

    }
    if(is_array($options)) {
      foreach($options as $k => $v) {
        if($useVersion5 && in_array($k, ['width','height','min-width','min-height','max-width','max-height'])) {
          $this->app->Tpl->Add('YUICSS',' 
          #'.$name.' + div.ck-editor div.ck-editor__editable_inline 
          {
           '.$k.': '.(is_numeric($v)?$v.'px':$v).';
          }');
          continue;
        }
        if($v=="false" || $v=="true"){
          if($useVersion5) {
            $config[] = '"'.$k.'"' . ": " . $v . "";
          }
          else{
            $config[] = $k . ": " . $v . "";
          }
        }
        else{
          if($useVersion5) {
            $config[] = '"'.$k.'"' . ": \"" . $v . "\"";
          }
          else{
            $config[] = $k . ": '" . $v . "'";
          }
        }
      }
    }

    $config_str = '';
    if(isset($config)) {
      $config_str = implode(",",$config);
    }

    $html = '
    var ckdata_' . $name . ' = $("#' . $name . '").val();
    if(typeof ckdata_' . $name . ' != \'undefined\' &&  ckdata_' . $name . '.indexOf("<") < 0)
    {
      var ckdataanz_' . $name . ' = 0;
      while(ckdataanz_' . $name . ' < 100 && ckdata_' . $name . '.indexOf("\r\n") > -1)
      {
        ckdataanz_' . $name . '++;
        ckdata_' . $name . ' = ckdata_' . $name . '.replace(/\r\n/g,"<br />");
      }
      ckdataanz_' . $name . ' = 0;
      while(ckdataanz_' . $name . ' < 100 && ckdata_' . $name . '.indexOf("\n") > -1)
      {
        ckdataanz_' . $name . '++;
        ckdata_' . $name . ' = ckdata_' . $name . '.replace(/\n/g,"<br />");
      }
      $("#' . $name . '").val(ckdata_' . $name . ');
    }
    '.($useVersion5?'
    
    ClassicEditor.create( document.querySelector( \'#'.$name.'\' ), {' . $config_str . '}).then(editor => {
        editor'.$name.'=editor;
    });
    ':
        '
    
    
    $( "#' . $name . '" ).ckeditor({' . $config_str . '});'
      ).
      '
    ';

    if($useVersion5 && $target !== 'return') {
      $html = '<script type="application/json" class="json_ckeditor5">{"element":"'.$name.'","data":{'.str_replace(["\n","\r"],'',$config_str).'}}</script>';
      $this->app->Tpl->Add(
        'ADDITIONALJAVASCRIPT',
        $html
      );
      return $html;
    }

    if($useVersion5) {
      $this->app->Tpl->Add('JAVASCRIPT', 'var editor'.$name.'= null;');
    }

    if($target === 'return') {
      return $html;
    }
    $this->app->Tpl->Add($target, $html);
    $this->app->Tpl->Add('MSG'.strtoupper($name),' <input type="hidden" value="1" name="ishtml_cke_'.$name.'" />');
  }

  function DatePicker($name) {

    $this->app->Tpl->Add('JQUERY', '$( "#' . $name . '" ).datepicker({ dateFormat: \'dd.mm.yy\',dayNamesMin: [\'SO\', \'MO\', \'DI\', \'MI\', \'DO\', \'FR\', \'SA\'], firstDay:1,
          showWeek: true, monthNames: [\'Januar\', \'Februar\', \'März\', \'April\', \'Mai\', 
          \'Juni\', \'Juli\', \'August\', \'September\', \'Oktober\',  \'November\', \'Dezember\'], });');
  }
  
  function TimePicker($name) {

    $this->app->Tpl->Add('JQUERY', '$( "#' . $name . '" ).timepicker();');
  }
  
  function Message($class, $msg) {

    $this->app->Tpl->Add('MESSAGE', "<div class=\"$class\">$msg</div>");
  }
  
  function IconsSQLAll() {


    //  $go_lager = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/lagergo.png\" border=\"0\">";
    
    //  $stop_lager = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/lagerstop.png\" border=\"0\">";
    $tmp = '';
    $tmpblue = '';
    $tmpstorno = '';
    $stop_lager = '';
    $abgeschlossen = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/grey.png\" border=\"0\">";
    $angelegt = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/dokumentoffen.png\" border=\"0\">";
    $storniert = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/storno.png\" border=\"0\">";
    $go_lager = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/dokumentok.png\" border=\"0\">";
    for ($i = 0;$i < 1;$i++) $tmp.= $abgeschlossen;
    for ($i = 0;$i < 1;$i++) $tmpblue.= $angelegt;
    for ($i = 0;$i < 1;$i++) $tmpstorno.= $storniert;
    return "if(a.status='angelegt','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpblue</td></tr></table>',
           if(a.status='abgeschlossen' or a.status='storniert',
               if(a.status='abgeschlossen','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmp</td></tr></table>','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpstorno</td></tr></table>'),

               CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap>',
                 if(1,'$go_lager','$stop_lager'),'</td></tr></table>'
                 )))";
  }

  /**
   * @return string
   */
  public function IconsSQLReturnOrder()
  {
    $closed = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/produktion_usn_gut.png\" style=\"margin-right:1px\" title=\"Ausgeglichen\" border=\"0\">";
    $open = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/produktion_usn_gut.png\" style=\"margin-right:1px\" title=\"Offen\" border=\"0\">";
    $opened = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/vorkassegostop.png\" style=\"margin-right:1px\" title=\"Zahlung angelegt\" border=\"0\">";
    $failed = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/vorkassestop.png\" style=\"margin-right:1px\" title=\"Zahlung fehlgeschlagen\" border=\"0\">";
    $ok = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/vorkassego.png\" style=\"margin-right:1px\" title=\"Zahlung ausgeführt\" border=\"0\">";
    $payed = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/abgeschlossen.png\" style=\"margin-right:1px\" title=\"Zahlung verbucht\" border=\"0\">";

    if(false) {
      return "'".$closed."'";
    }

    return
      "IF(
        ISNULL(pt.id),
        '$open',
        IF(
          pt.payment_status = 'abgeschlossen',
          '$closed',
          IF(
            pt.payment_status = '' OR pt.payment_status = 'angelegt' OR pt.payment_status = 'created',
            '$opened',
            IF(
              pt.payment_status = 'fehlgeschlagen' OR pt.payment_status = 'failed' OR pt.payment_status = 'error',
              '$failed',
              IF(pt.payment_status = 'verbucht' OR pt.payment_status = 'payed',
                '$payed',
                '$ok'
              )
            )
          )
        )
      )";
  }

  function IconsSQL() {

    $anzahl = 0;
    if($this->app->erp->Firmendaten("ampellager")!="1")
    {
      $go_lager = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/lagergo.png\" style=\"margin-right:1px\" title=\"Artikel ist im Lager\" border=\"0\">";
      $stop_lager = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/lagerstop.png\" style=\"margin-right:1px\" title=\"Artikel fehlt im Lager\" border=\"0\">";
      $anzahl++;
    } else { $go_lager=""; $stop_lager=""; }

    if($this->app->erp->Firmendaten("ampelporto")!="1")
    {
      $go_porto = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/portogo.png\" style=\"margin-right:1px\" title=\"Porto Check OK\" border=\"0\">";
      $stop_porto = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/portostop.png\" style=\"margin-right:1px\" title=\"Porto fehlt!\" border=\"0\">";
      $anzahl++;
    }

    if($this->app->erp->Firmendaten("ampelust")!="1")
    {
      $go_ust = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/ustgo.png\" title=\"UST Check OK\" border=\"0\" style=\"margin-right:1px\">";
      $stop_ust = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/uststop.png\" title=\"UST-Pr&uuml;fung fehlgeschlagen!\" border=\"0\" style=\"margin-right:1px\">";
      $anzahl++;
    }

    if($this->app->erp->Firmendaten("ampelzahlung")!="1")
    {
      $go_vorkasse = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/vorkassego.png\" title=\"Zahlungscheck OK\" border=\"0\" style=\"margin-right:1px\">";
      $stop_vorkasse = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/vorkassestop.png\" title=\"Zahlungseingang bei Vorkasse fehlt!\" border=\"0\" style=\"margin-right:1px\">";
      $gostop_vorkasse = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/vorkassegostop.png\" title=\"Teilzahlung vorhanden!\" border=\"0\" style=\"margin-right:1px\">";
      $anzahl++;
    }

    if($this->app->erp->Firmendaten("ampelnachnahme")!="1")
    {
      $go_nachnahme = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/nachnahmego.png\" title=\"Nachnahme Check OK\" border=\"0\" style=\"margin-right:1px\">";
      $stop_nachnahme = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/nachnahmestop.png\" title=\"Nachnahmegeb&uuml;hr fehlt!\" border=\"0\" style=\"margin-right:1px\">";
      $anzahl++;
    }

    if($this->app->erp->Firmendaten("ampelautoversand")!="1")
    {
      $go_autoversand = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/autoversandgo.png\" title=\"Autoversand erlaubt\" border=\"0\" style=\"margin-right:1px\">";
      $stop_autoversand = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/autoversandstop.png\" title=\"Kein Autoversand\" border=\"0\" style=\"margin-right:1px\">";
      $anzahl++;
    }

    if($this->app->erp->Firmendaten("ampelkunde")!="1")
    {
      $go_check = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/checkgo.png\" title=\"Kundencheck OK\" border=\"0\" style=\"margin-right:1px\">";
      $stop_check = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/checkstop.png\" title=\"Kundencheck fehlgeschlagen\" border=\"0\" style=\"margin-right:1px\">";
      $anzahl++;
    }

    if($this->app->erp->Firmendaten("ampelliefertermin")!="1")
    {
      $go_liefertermin = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/termingo.png\" title=\"Liefertermin OK\" border=\"0\" style=\"margin-right:1px\">";
      $stop_liefertermin = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/terminstop.png\" title=\"Liefertermin in Zukunft\" border=\"0\" style=\"margin-right:1px\">";
      $anzahl++;
    }

    if($this->app->erp->Firmendaten("ampelkreditlimit")!="1")
    {
      $go_kreditlimit = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/kreditlimitgo.png\" title=\"Kreditlimit OK\" border=\"0\" style=\"margin-right:1px\">";
      $stop_kreditlimit = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/kreditlimitstop.png\" title=\"Kein Kreditlimit mehr verf&uuml;gbar!\" border=\"0\" style=\"margin-right:1px\">";
      $anzahl++;
    }

    if($this->app->erp->Firmendaten("ampelliefersperre")!="1")
    {
      $go_liefersperre = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/liefersperrego.png\" title=\"Liefersperre OK\" border=\"0\" style=\"margin-right:1px\">";
      $stop_liefersperre = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/liefersperrestop.png\" title=\"Liefersperre gesetzt\" border=\"0\" style=\"margin-right:1px\">";
      $anzahl++;
    }


    $reserviert = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/reserviert.png\" border=\"0\" style=\"margin-right:1px\">";
    $check = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/mail-mark-important.png\" border=\"0\" style=\"margin-right:1px\">";
    $abgeschlossen = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/grey.png\" title=\"Auftrag abgeschlossen\" border=\"0\" style=\"margin-right:1px\">";
    $angelegt = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/blue.png\" title=\"Auftrag noch nicht freigegeben!\" border=\"0\" style=\"margin-right:1px\">";
    $storniert = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/storno.png\" title=\"Auftrag storniert!\" border=\"0\" style=\"margin-right:1px\">";
   
    if($this->app->erp->Firmendaten("ampelproduktion")!="1" && $this->app->erp->ModulVorhanden('produktion'))
    { 
      $produktiongestartet = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/produkton_laeuft.png\" title=\"Produktion gestartet\" border=\"0\" style=\"margin-right:1px\">";
      $produktionangelegt = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/produkton_start.png\" title=\"Produktion angelegt\" border=\"0\" style=\"margin-right:1px\">";
      $produktionabgeschlossen = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/produkton_erledigt.png\" title=\"Produktion abgeschlossen\" border=\"0\" style=\"margin-right:1px\">";
      $produktionnichtvorhanden = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/produktion_usn_gut.png\" title=\"Keine Produktion vorhanden\" border=\"0\" style=\"margin-right:1px\">";
      $anzahl++;
    }

    $extra = '';
    $extra2 = '';
    $extra3 = '';
    $ifextra2 = '';
    $tmp = '';
    $tmp2 = '';
    //$anzahl = 10;
    $anzahl2 = $anzahl;
    $tmpstorno = '';
    $tmpstorno2 = '';
    $anzahl3 = 0;
    $tmpblue = '';
    $_extra2a = '';
    $_extra2e = '';
    $_extra2icon = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/abgeschlossen.png\" title=\"Auftragsampel deaktivert\" border=\"0\" style=\"margin-right:1px\">";
    if($this->app->erp->ModulVorhanden('produktion'))
    {
      //$anzahl++;
      $anzahl2 = $anzahl;
      $extra .= ",IF((SELECT count(produ.id) FROM produktion produ WHERE produ.auftragid = a.id AND produ.status = 'gestartet' ),'$produktiongestartet',IF((SELECT count(produ.id) FROM produktion produ WHERE produ.auftragid = a.id AND (produ.status = 'angelegt' OR produ.status = 'freigegeben') ),'$produktionangelegt',IF((SELECT count(produ.id) FROM produktion produ WHERE produ.auftragid = a.id AND produ.status = 'abgeschlossen' ),'$produktionabgeschlossen','$produktionnichtvorhanden')))";
    }
    $auftragsampel_abgeschlossenanzeigen = $this->app->erp->GetKonfiguration('auftragsampel_abgeschlossenanzeigen');
    if($this->app->erp->ModulVorhanden('auftragsampel'))
    {
      $auftragsampel = $this->app->DB->SelectArr("SELECT * FROM auftragsampel WHERE aktiv = 1 AND icon != '' ORDER by sort, icon, id");
      if($auftragsampel)
      {
        $check = $this->app->DB->Select("SELECT id FROM auftragsampel_auftrag_cache LIMIT 1");
        if(!$this->app->DB->error())
        {
          $check = $this->app->DB->Select("SELECT id FROM `auftragsampel_auftrageinstellungen` LIMIT 1");
          $deaktivertok = false;
          if(!$this->app->DB->error())$deaktivertok = true;
          $anzahl3 = count($auftragsampel);
          $anzahl += $anzahl3;
          $ifextra2a[] = " a.status = 'abgeschlossen' ";
          foreach($auftragsampel as $k => $ampel)
          {
            $k2 = $k+1;
            $ampel['beschriftung'] = str_replace(array("'",'"'),array('&#39;','&quot;'),$ampel['beschriftung']);
            $extra2 .= ",";
            if($deaktivertok) $_extra2a = ",if(
            isnull((SELECT aae.id FROM auftragsampel_auftrageinstellungen aae WHERE aae.auftrag = a.id AND aae.deaktiviert = 1 LIMIT 1))
            ,concat(''";
            $extra2 .= "if(
            substring(
            ifnull(aac.status,'".str_repeat('0', $anzahl3)."'),
            $k2,1) = '0', "
              ."'<img src=\"./pages/icons/".str_replace('_go_','_stop_',$ampel['icon'])."\" title=\"".$ampel['beschriftung']."\" border=\"0\" style=\"margin-right:1px\">'".",
              "."'<img src=\"./pages/icons/".$ampel['icon']."\" title=\"".$ampel['beschriftung']."\" border=\"0\" style=\"margin-right:1px\">'". ") ";
            
            if($deaktivertok) $_extra2e = "),'<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/abgeschlossen.png\" title=\"Auftragsampel deaktivert\" border=\"0\" style=\"margin-right:1px\">'
            
            )";
            if($deaktivertok) $_extra2e = "),'".str_repeat($_extra2icon,count($auftragsampel))."')";
            $extra3 .= ",'".$abgeschlossen."'";
            $ifextra2a[] = " 
            substring(ifnull(aac.status,'".str_repeat('0', $anzahl3)."'),$k2,1) = '1' ";
          }
        }else{
          $check = $this->app->DB->Select("SELECT id FROM auftragsampel_auftrag LIMIT 1");
          if(!$this->app->DB->error())
          {
            $check = $this->app->DB->Select("SELECT id FROM `auftragsampel_auftrageinstellungen` LIMIT 1");
            $deaktivertok = false;
            if(!$this->app->DB->error())$deaktivertok = true;
            $anzahl3 = count($auftragsampel);
            $anzahl += $anzahl3;
            $ifextra2a[] = " a.status = 'abgeschlossen' ";
            foreach($auftragsampel as $ampel)
            {
              $ampel['beschriftung'] = str_replace(array("'",'"'),array('&#39;','&quot;'),$ampel['beschriftung']);
              $extra2 .= ",";
              if($deaktivertok) $_extra2a = ",if(isnull((SELECT aae.id FROM auftragsampel_auftrageinstellungen aae WHERE aae.auftrag = a.id AND aae.deaktiviert = 1 LIMIT 1)),concat(''";
              $extra2 .= "IF(isnull( (SELECT aampa.id FROM auftragsampel_auftrag aampa WHERE aampa.auftrag = a.id AND aampa.auftragsampel = '".$ampel['id']."' AND aampa.erledigt = 1) ), "."'<img src=\"./pages/icons/".str_replace('_go_','_stop_',$ampel['icon'])."\" title=\"".$ampel['beschriftung']."\" border=\"0\" style=\"margin-right:1px\">'".","."'<img src=\"./pages/icons/".$ampel['icon']."\" title=\"".$ampel['beschriftung']."\" border=\"0\" style=\"margin-right:1px\">'". ") ";
              if($deaktivertok) $_extra2e = "),'<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/abgeschlossen.png\" title=\"Auftragsampel deaktivert\" border=\"0\" style=\"margin-right:1px\">')";
              if($deaktivertok) $_extra2e = "),'".str_repeat($_extra2icon,count($auftragsampel))."')";
              $extra3 .= ",'".$abgeschlossen."'";
              $ifextra2a[] = " 
              not isnull( (SELECT aampa.id FROM auftragsampel_auftrag aampa WHERE aampa.auftrag = a.id AND aampa.auftragsampel = '".$ampel['id']."' AND aampa.erledigt = 1) ) ";
            }
          }
        }
      }      
    }
    if(!empty($ifextra2a))$ifextra2 = implode(' AND ', $ifextra2a);
    if($ifextra2=='')$ifextra2 = ' 0 ';
    for ($i = 0;$i < $anzahl;$i++) $tmp.= $abgeschlossen;
    for ($i = 0;$i < $anzahl2;$i++) $tmp2.= $abgeschlossen;
    for ($i = 0;$i < $anzahl;$i++) $tmpblue.= $angelegt;
    for ($i = 0;$i < $anzahl;$i++) $tmpstorno.= $storniert;
    for ($i = 0;$i < $anzahl2;$i++) $tmpstorno2.= $storniert;
    for ($i = 0;$i < $anzahl3;$i++) $tmpstorno3.= $storniert;
    if($auftragsampel_abgeschlossenanzeigen)
    {
      return "if(a.status='angelegt','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpblue</td></tr></table>',
           
         concat( if(a.status='abgeschlossen' or a.status='storniert',
               if(a.status='abgeschlossen',
               '<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmp2',
               '<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpstorno2'),

               CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap>',
                 if(a.lager_ok,'$go_lager','$stop_lager'),if(a.porto_ok,'$go_porto','$stop_porto'),if(a.ust_ok,'$go_ust',CONCAT('<a href=\"/index.php?module=adresse&action=ustprf&id=',a.adresse,'\">','$stop_ust','</a>')),
                 if(a.vorkasse_ok=1,'$go_vorkasse',if(a.vorkasse_ok=2,'$gostop_vorkasse','$stop_vorkasse')),if(a.nachnahme_ok,'$go_nachnahme','$stop_nachnahme'),if(a.autoversand,'$go_autoversand','$stop_autoversand'),
                 if(a.check_ok,'$go_check','$stop_check'),if(a.liefertermin_ok,'$go_liefertermin','$stop_liefertermin'),if(a.kreditlimit_ok,'$go_kreditlimit','$stop_kreditlimit'),if(a.liefersperre_ok,'$go_liefersperre','$stop_liefersperre')$extra,''
                 )),
                  if(
                  
                  $ifextra2
                  
                  ,concat(''$extra3)
                  
                  , if( a.status='storniert','$tmpstorno3',  concat(''$_extra2a $extra2 $_extra2e))
                  
                  
                  ) ,'</td></tr></table>')
                 
                 
                 )";
    }
    
    return "if(a.status='angelegt','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpblue</td></tr></table>',
           if(a.status='abgeschlossen' or a.status='storniert',
               if(a.status='abgeschlossen','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmp</td></tr></table>','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpstorno</td></tr></table>'),

               CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap>',
                 if(a.lager_ok,'$go_lager','$stop_lager'),if(a.porto_ok,'$go_porto','$stop_porto'),if(a.ust_ok,'$go_ust',CONCAT('<a href=\"/index.php?module=adresse&action=ustprf&id=',a.adresse,'\">','$stop_ust','</a>')),
                 if(a.vorkasse_ok=1,'$go_vorkasse',if(a.vorkasse_ok=2,'$gostop_vorkasse','$stop_vorkasse')),if(a.nachnahme_ok,'$go_nachnahme','$stop_nachnahme'),if(a.autoversand,'$go_autoversand','$stop_autoversand'),
                 if(a.check_ok,'$go_check','$stop_check'),if(a.liefertermin_ok,'$go_liefertermin','$stop_liefertermin'),if(a.kreditlimit_ok,'$go_kreditlimit','$stop_kreditlimit'),if(a.liefersperre_ok,'$go_liefersperre','$stop_liefersperre')$extra $extra2,'</td></tr></table>'
                 )))";
  }
  
  function IconsSQLVerbindlichkeit() {

    $go_ware = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/ware_go.png\" style=\"margin-right:1px\" title=\"Wareneingangspr&uuml;fung OK\" border=\"0\">";
    $stop_ware = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/ware_stop.png\" style=\"margin-right:1px\" title=\"Wareneingangspr&uuml;fung fehlt\" border=\"0\">";
    $go_summe = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/summe_go.png\" style=\"margin-right:1px\" title=\"Rechnungseingangspr&uuml;fung OK\" border=\"0\">";
    $stop_summe = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/summe_stop.png\" style=\"margin-right:1px\" title=\"Rechnungseingangspr&uuml;fung fehlt\" border=\"0\">";

    $go_zahlung = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/bank_go.svg\" style=\"margin-right:1px\" title=\"Kontoverkn&uuml;pfung OK\" border=\"0\">";
    $stop_zahlung = "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/bank_stop.svg\" style=\"margin-right:1px\" title=\"Kontoverkn&uuml;pfung fehlt\" border=\"0\">";

    $stop_betragbezahlt = "<img alt=\"Zahlung fehlt\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/vorkassestop.png\" style=\"margin-right:1px\" title=\"Zahlung fehlt\" border=\"0\">";
    $gostop_betragbezahlt = "<img alt=\"teilweise bezahlt\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/vorkassegostop.png\" style=\"margin-right:1px\" title=\"teilweise bezahlt\" border=\"0\">";
    $go_betragbezahlt = "<img alt=\"nicht bezahlt\"  src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/vorkassego.png\" style=\"margin-right:1px\" title=\"komplett bezahlt\" border=\"0\">";
    return "CONCAT('<table><tr><td nowrap>',
    if(v.freigabe,'$go_ware','$stop_ware'),
    if(v.rechnungsfreigabe,'$go_summe','$stop_summe'),
    IF( v.betragbezahlt = 0 OR (v.betrag > 0 AND v.betragbezahlt < 0),'$stop_betragbezahlt',
      IF(v.betrag > 0 AND (v.betragbezahlt + v.skonto_erhalten) >= v.betrag, '$go_betragbezahlt',
        IF(v.betrag - v.betragbezahlt <= v.betrag-((v.betrag/100.0)*v.skonto),
          '$gostop_betragbezahlt',
          '$go_betragbezahlt'
        )
      )
    ),     
    if((
    (SELECT COUNT(ka.id) 
    FROM kontoauszuege_zahlungsausgang ka WHERE ka.parameter=v.id AND ka.objekt='verbindlichkeit') + 
    (SELECT COUNT(ke.id) FROM kontoauszuege_zahlungseingang ke WHERE ke.parameter=v.id AND ke.objekt='verbindlichkeit')) > 0,
    '$go_zahlung','$stop_zahlung'
    ),
    '</td></tr></table>')";
  }
  
  function TablePositionSearch($parsetarget, $name, $callback = "show", $gener) {

    $id = $this->app->Secure->GetGET("id");
    
    switch ($name) {
      case "auftragpositionen":

        /*
        // headings
        $heading =  array('Nummer','Artikel','Projekt','Menge','Einzelpreis','Men&uuml;');
        $width   =  array('10%','45%','15%','10%','10%','10%');
        $findcols = array('nummer','name_de','projekt','menge','preis','id');
        $searchsql = array('a.bezeichnung','a.nummer','p.abkuerzung');
        
        $menu =  "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>".
        "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>".
        "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>";
        
        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer as nummer, a.bezeichnung as name_de, p.abkuerzung as projekt, a.menge as menge, a.preis as preis, a.id as menu
        FROM  auftrag_position a LEFT JOIN projekt p ON p.id=a.projekt ";
        
        // fester filter
        $w;h;ere = " a.auftrag='$id'";
        
        $count = "SELECT COUNT(id) FROM auftrag_position WHERE auftrag='$id'";
        */
      break;
      default:
      break;
    }
    
    if ($callback == "show") {
      $this->app->Tpl->Add('ADDITIONALCSS', "

          .ex_highlight #$name tbody tr.even:hover, #example tbody tr.even td.highlighted {
          background-color: [TPLFIRMENFARBEHELL]; 
          }

          .ex_highlight_row #$name tr.even:hover {
          background-color: [TPLFIRMENFARBEHELL];
          }

          .ex_highlight_row #$name tr.even:hover td.sorting_1 {
          background-color: [TPLFIRMENFARBEHELL];
          }

          .ex_highlight_row #$name tr.odd:hover {
          background-color: [TPLFIRMENFARBEHELL];
          }

          .ex_highlight_row #$name tr.odd:hover td.sorting_1 {
          background-color: [TPLFIRMENFARBEHELL];
          }
          ");

      //"sPaginationType": "full_numbers",
      
      //"aLengthMenu": [[10, 25, 50, 200, 10000], [10, 25, 50, 200, "All"]],

      
      if ($name == "versandoffene") {
        $bStateSave = "false";
        $cookietime = 0;
      } else {
        $cookietime = 365 * 24 * 60 * 60; // 1 Jahr
        $bStateSave = "true";
      }


      $this->app->Tpl->Add('JAVASCRIPT', " var oTable" . $name . "; var oMoreData1" . $name . "=0; var oMoreData2" . $name . "=0; var oMoreData3" . $name . "=0; var oMoreData4" . $name . "=0; var oMoreData5" . $name . "=0;  var aData;
              ");
      $iframe = $this->app->Secure->GetGET("iframe");
      $this->app->Tpl->Add('DATATABLES', '
                  oTable' . $name . ' = $(\'#' . $name . '\').dataTable( {
                    "bAutoWidth": false,
                    "bProcessing": true,
                    "oLanguage":{"sProcessing":" "},
                    "iDisplayLength": 10,
                    "bStateSave": ' . $bStateSave . ',
                    "iCookieDuration": ' . (int)$cookietime . ',
                    "bServerSide": true,
                    "fnInitComplete": function (){
                    $(oTable' . $name . '.fnGetNodes()).click(function (){
                      alert(\'Demo\');// my js window....
                      });},
                    "fnServerData": function ( sSource, aoData, fnCallback ) {
                    /* Add some extra data to the sender */
                    aoData.push( { "name": "more_data1", "value": oMoreData1' . $name . ' } );
                    aoData.push( { "name": "more_data2", "value": oMoreData2' . $name . ' } );
                    aoData.push( { "name": "more_data3", "value": oMoreData3' . $name . ' } );
                    aoData.push( { "name": "more_data4", "value": oMoreData4' . $name . ' } );
                    aoData.push( { "name": "more_data5", "value": oMoreData5' . $name . ' } );
                    $.getJSON( sSource, aoData, function (json) { 
                      /* Do whatever additional processing you want on the callback, then tell DataTables */
                      fnCallback(json)
                      } );
                    },
                    "sAjaxSource": "./index.php?module=ajax&action=tableposition&cmd=' . $name . '&id=' . $id . '&iframe=' . $iframe . '"
                  } );



              ');
      
      if ($moreinfo) {
        $this->app->Tpl->Add('DATATABLES', '
                    $(document).on( \'click\',\'#' . $name . ' tbody td img.details\', function () {
                      var nTr = this.parentNode.parentNode;
                      aData =  oTable' . $name . '.fnGetData( nTr );

                      if ( this.src.match(\'details_close\') )
                      {
                      /* This row is already open - close it */
                      this.src = "./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/details_open.png";
                      oTable' . $name . '.fnClose( nTr );
                      }
                      else
                      {
                      /* Open this row */
                      this.src = "./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/details_close.png";
                      oTable' . $name . '.fnOpen( nTr, ' . $name . 'fnFormatDetails(nTr), \'details\' );
                      }
                      });
                    ');

        /*  $.get("index.php?module=auftrag&action=minidetail&id=2", function(text){
                    spin=0; 
                    miniauftrag = text;
                    });
        */
        $module = $this->app->Secure->GetGET("module");
        $this->app->Tpl->Add('JAVASCRIPT', 'function ' . $name . 'fnFormatDetails ( nTr ) {
                    //var aData =  oTable' . $name . '.fnGetData( nTr );
                    var str = aData[' . (isset($menucol)?$menucol:count($heading)-1) . '];

                    var match = str.match(/[1-9]{1}[0-9]*/);

                    var auftrag = parseInt(match[0], 10);

                    var miniauftrag;
                    var strUrl = "index.php?module=' . $module . '&action=minidetail&id="+auftrag; //whatever URL you need to call
                    var strReturn = "";

                    jQuery.ajax({
url:strUrl, success:function(html){strReturn = html;}, async:false
});

                    miniauftrag = strReturn;

                    var sOut = \'<table cellpadding="0" cellspacing="0" border="0" align="center" style="padding-left: 15px; padding-right:15px; width:calc(100% - 30px);">\';
                    sOut += \'<tr><td>\'+miniauftrag+\'</td></tr>\';
                    sOut += \'</table>\';
                    return sOut;
                }
');
      }
      $colspan = count($heading);

    //<tr><th colspan="' . $colspan . '"><br></th></tr>
      $this->app->Tpl->Add($parsetarget, '
    <table cellpadding="0" cellspacing="0" border="0" class="display" id="' . $name . '">
    <thead>
    <tr>');
      for ($i = 0;$i < count($heading);$i++) {
        $this->app->Tpl->Add($parsetarget, '<th width="' . $width[$i] . '">' . $heading[$i] . '</th>');
      }
      $this->app->Tpl->Add($parsetarget, '</tr>
    </thead>
    <tbody>
    <tr>
    <td colspan="' . $colspan . '" class="dataTables_empty">Lade Daten</td>
    </tr>
    </tbody>

    <tfoot>
    <tr>
    ');
      for ($i = 0;$i < count($heading);$i++) {
        $this->app->Tpl->Add($parsetarget, '<th>' . $heading[$i] . '</th>');
      }
      $this->app->Tpl->Add($parsetarget, '
    </tr>
    </tfoot>
    </table>
    <br>
    <br>
    <br>
    ');
    } else 
    if ($callback == "sql") return $sql;
    else 
    if ($callback == "searchsql") return $searchsql;
    else 
    if ($callback == "searchsql_dir") return $searchsql_dir;
    else 
    if ($callback == "searchfulltext") return $searchfulltext;
    else 
    if ($callback == "heading") return $heading;
    else 
    if ($callback == "menu") return $menu;
    else 
    if ($callback == "findcols") return $findcols;
    else 
    if ($callback == "where") return $where;
    else 
    if ($callback == "count") return $count;
  }
  
  
  function EnterSearch($target, $name)
  {
    $this->app->Tpl->Add($target,'
    <fieldset><legend>{|Schnellsuche|}</legend><input type="text" class="schnellsuche" size="50" onchange="'.$name.'_enterfilterchange(this);" id="'.$name.'_enterfilter" />&nbsp;<input onclick="'.$name.'_enterfilterchange(this);" type="button" value="{|Suche|}">
    <script>
      function '.$name.'_enterfilterchange(searchel)
      {
        if($(searchel).is("[type=\'button\']"))searchel = $(searchel).prev().first();
        var el = $(\'#'.$name.'_filter\').find(\'input\').first();
        var oTableL = $(\'#'.$name.'\').dataTable( );
        oTableL.fnFilter($(searchel).val());
      }
    </script>
    </fieldset>
    '
    );
  }
  
  /*
  Parameter name: Tablesearchname
  Parameter moredata: Nr 1 - 18
  Parameter feldid: html id für Input Element
  Parameter vorbelegung: Standardwert beim ersten Laden der Seite
  Parameter typ: checkbox für Checkboxen sonst wird der Value direkt übernommen
  return: aktueller Wert aus Ajax
  */
  function TableSearchFilter($name, $moredata, $feldid, $vorbelegung = 0,$column = 0, $typ = '')
  {
    $moredatavorbelegung = 'oMoreData'.$moredata;
    $this->$moredatavorbelegung = $vorbelegung;
    if($typ == 'checkbox')
    {
      $this->app->Tpl->Add('JQUERYREADY', "
      $('#".$feldid."').change( function() { 
      fnFilterColumn".$moredata."( $('#".$feldid."').prop('checked')?1:0 ); 
      } 
      );
      ");
      $this->app->Tpl->Add('JAVASCRIPT', '
        function fnFilterColumn' . $moredata . ' ( i )
        {
        if(oMoreData' . $moredata . $name . '==1)
          oMoreData' . $moredata . $name . ' = 0;
        else
          oMoreData' . $moredata . $name . ' = 1;

        $(\'#' . $name . '\').dataTable().fnFilter( 
          \'\',
          i, 
          0,0
          );
        }
      ');
    }else{
      $this->app->Tpl->Add('JQUERYREADY', "$('#".$feldid."').on('change', function() { fnFilterColumn".$moredata."(  $('#".$feldid."').val() ); } );$('#".$feldid."').on('focusout', function() { fnFilterColumn".$moredata."(  $('#".$feldid."').val() ); } );$('#".$feldid."').on('click', function() { fnFilterColumn".$moredata."(  $('#".$feldid."').val() ); } );");
      $this->app->Tpl->Add('JAVASCRIPT', '
        function fnFilterColumn' . $moredata . ' ( i )
        {
          oMoreData' . $moredata . $name . ' = i;

          $(\'#' . $name . '\').dataTable().fnFilter( 
            \'\',
            0, 
            0,0
            );
          }
      ');
    }
    return $this->app->Secure->GetGET('more_data'.$moredata);
  }
  
  /*
  falls $name nicht vorhanden wird das Modul included und die Funktion
    static function TableSearch(&$app, $name, $erlaubtevars)
  aufgerufen und dort nach $name gesucht und die callbackresults als array zurückgegeben
  */

  /**
   * @param $parsetarget
   * @param $name
   * @param string $callback
   * @param string $generic_sql
   * @param string $generic_menu
   * @param string $frommodule Modulname
   * @param string $fromclass Klassenname
   */

  function TableSearch($parsetarget, $name, $callback = "show", $generic_sql = "", $generic_menu = "", $frommodule = "", $fromclass = "") {
    $moreDataMaxNr = '';
    for($i = 1; $i <= 31; $i++) {
      $_name = 'oMoreData'.$i;
      $this->$_name = 0;
    }
    $defferloading = null;
  
    $id = (int)$this->app->Secure->GetGET("id");
    $groupby = "";
    $allowed = array();
    $searchfulltext = "";

    $extended_mysql55 = ",'de_DE'";

    $allowed_row_clicks = array('artikeltabellebilder','auftraege','adressetabelle','artikeltabelle','projekttabelle','anfrage','proformarechnung','preisanfrage','angebote','angeboteinbearbeitung','auftraegeoffene','auftraegeinbearbeitung','auftragoffenepositionenlist','bestellungen','bestellungeninbearbeitung','produktionoffeneauto','produktioninbearbeitung','paketannahme','rechnungenoffene','rechnungeninbearbeitung','reisekosten','reisekostenoffene','reisekosteninbearbeitung','arbeitsnachweiseoffene','arbeitsnachweiseinbearbeitung','gutschriftenoffene','gutschriften','gutschrifteninbearbeitung','ueberweisung','ueberweisungarchiv','dta_datei_ueberweisung','lastschriften','lastschriften_gutschriften','lastschriftenarchiv','zahlungsavis','dta_datei_lastschrift','kassenbuecher','kassenbuecher_archiv','importvorlage','exportvorlage','service_list','service_list_freigabe','service_list_meine','service_list_abgeschlossen','lieferscheineoffene','lieferscheine','lieferscheineinbearbeitung','lagertabelle','versandoffene','versandfertig','userlist','ticket_vorlagenlist','emailbackuplist','warteschlangenlist','artikelkategorienlist','uservorlagelist','kontenlist','kostenstellenlist','verrechnungsartlist','waehrungumrechnung_list','zolltarifnummerlist','versandartenlist','onlineshopslist','druckerlist','adapterbox_list','etikettenlist','arbeitsfreietage_list','datei_stichwortvorlagen','rechnungen','verbindlichkeiten','produktion');

    if(in_array($name,$allowed_row_clicks)) $rowclick=true;

    switch ($name) {

      case "datei_list_referer":
        $allowed['artikel'] = array('dateien');
        $allowed['adresse'] = array('dateien');
        $allowed['angebot'] = array('dateien');
        $allowed['auftrag'] = array('dateien');
        $allowed['rechnung'] = array('dateien');
        $allowed['gutschrift'] = array('dateien');
        $allowed['lieferschein'] = array('dateien');
        $allowed['bestellung'] = array('dateien');
        $allowed['projekt'] = array('dateien');
        $allowed['produktion'] = array('dateien');
        $allowed['anfrage'] = array('dateien');
        $allowed['preisanfrage'] = array('dateien');
        $allowed['proformarechnung'] = array('dateien');
        $allowed['reisekosten'] = array('dateien');
        $allowed['kalkulation'] = array('dateien');
        $allowed['wiki'] = array('dateien');
        $allowed['geschaeftsbrief_vorlagen'] = array('dateien');
        $allowed['kasse'] = array('dateien');

        $id = $this->app->Secure->GetGET("id");
        $sid = $this->app->Secure->GetGET("sid");
        if($sid > 0) {
          $id = $sid;
        }

        parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $queries);
        switch($queries['module'])
        {
          case "adresse": $objekt="adressen"; break;
          default: $objekt=$queries['module'];
        }

        //if(!ctype_alpha($objekt))$objekt="";

        if(!preg_match('/[A-Za-z_]/', $objekt)) {
          $objekt='';
        }
        // alle artikel die ein Kunde kaufen kann mit preisen netto brutto
        $cmd = $this->app->Secure->GetGET('smodule');
        $adresse = 0;
        if(!empty($cmd) && $id > 0){
          $adresse = $this->app->DB->Select(
            sprintf(
              'SELECT adresse FROM `%s` WHERE id=%d LIMIT 1',
              $cmd, (int)$id
            )
          );
        }
        $sortmodus = $this->TableSearchFilter($name, 1, 'sortmodus',  0,0,  'checkbox');
        // headings
        $heading = array('','','','Titel', 'Stichwort', 'Version','Gr&ouml;&szlig;e', 'Ersteller','Version','Datum','Sortierung','Men&uuml;');
        $width = array('1%','1%','10','40%', '15%', '5%','10%','15%', '10%', '10%','15%', '10%','5%','1%');
        $findcols = array('open','d.id','d.id',"CONCAT(d.titel,' ',v.dateiname)", 's.subjekt', 'v.version',"if(v.size!='',if(v.size > 1024*1024,CONCAT(ROUND(v.size/1024/1024,2),' MB'),CONCAT(ROUND(v.size/1024,2),' KB')),'')", 'v.ersteller','v.bemerkung','v.datum', 's.sort','s.id');
        $searchsql = array('d.titel', 's.subjekt', 'v.version',"if(v.size!='',if(v.size > 1024*1024,CONCAT(ROUND(v.size/1024/1024,2),' MB'),CONCAT(ROUND(v.size/1024,2),' KB')),'')", 'v.ersteller','v.bemerkung','v.dateiname',"DATE_FORMAT(v.datum, '%d.%m.%Y')");

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"#\" onclick=editdatei(%value%,\"$cmd\")><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=dateien&action=send&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/download.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=dateien&action=delete&cmd=".urlencode($objekt)."&id=%value%\")><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\" ></a></td></tr></table>";
        $menucol = 11;
        $alignright=array(6,7,11);

        if(!function_exists('imagejpeg'))
        {
          $img = "'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/icon_img_error.png title=\"Keine GD-Erweiterung installiert\" />'";
        }else{
          $img = "concat('<span style=\"width:100px;text-align:center;display:block;\"><a href=\"index.php?module=dateien&action=send&id=',d.id,'\"><img src=\"index.php?module=ajax&action=thumbnail&cmd=$cmd&id=',d.id,'\" style=\"border:0;max-width:100px;max-height:100px;\" /></a></span>')";
        }
        
        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS d.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,concat('<input type=\"checkbox\" id=\"auswahl_',d.id,'\"  onchange=\"chauswahl();\" value=\"1\" />'),
        $img, 
        
        if(d.titel!='',CONCAT(d.titel,'<br><i style=color:#999>',v.dateiname,'</i>'),v.dateiname), s.subjekt, v.version, if(v.size!='',if(v.size > 1024*1024,CONCAT(ROUND(v.size/1024/1024,2),' MB'),CONCAT(ROUND(v.size/1024,2),' KB')),''), v.ersteller, v.bemerkung, DATE_FORMAT(v.datum, '%d.%m.%Y'),s.sort,".($sortmodus?"s.id": "d.id")." 
            FROM `datei` AS `d` 
            INNER JOIN `datei_stichwoerter` AS `s` ON d.id=s.datei
            LEFT JOIN (
              SELECT `datei`, max(`version`) AS `version` 
              FROM `datei_version` 
              GROUP BY `datei` 
            ) AS `v2`  ON v2.datei=d.id
            LEFT JOIN `datei_version` AS `v` ON v.datei=v2.datei AND v.version = v2.version ";
        $parameter=$id;
        $moreinfo = true;
        $moreinfomodule = 'dateien';
        // fester filter
        $where = "s.objekt LIKE '$objekt' AND s.parameter='$parameter' AND d.geloescht=0";
        if($sortmodus) {
          $this->app->erp->CheckFileSort($objekt, $parameter);
          $orderby = ' ORDER BY s.sort ';
          $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"#\" onclick=dateidown(%value%)><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/up.png\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=dateiup(%value%)><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/down.png\" border=\"0\"></a></td></tr></table>";
        }

        $count = "SELECT COUNT(d.id) FROM `datei` AS `d` INNER JOIN `datei_stichwoerter` AS `s` ON d.id=s.datei
        WHERE $where";
        break;

      case "lagerplatzinventurtabelle":

        // headings
        $heading = array('Bezeichnung', 'Men&uuml;');
        $width = array('30%', '20%', '20%', '20%');
        $findcols = array('kurzbezeichnung', 'id');
        $searchsql = array('kurzbezeichnung');
        $menu = "<a href=\"index.php?module=lager&action=platzeditpopup&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS id, kurzbezeichnung, id as menu FROM lager_platz ";

        // fester filter
        $where = " geloescht=0 AND id!=0";
        $count = "SELECT COUNT(id) FROM lager_platz WHERE geloescht=0";
        break;
      case 'adresse_gruppen':
        $allowed['adresse'] = array('gruppen');
        $allowed['auftragscockpit'] = array('edit');
        $this->app->Tpl->Add('JQUERYREADY', "$('#inaktiv').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#nuraktivierte').click( function() { fnFilterColumn2( 0 ); } );");

        for ($r = 1;$r < 3;$r++) {
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



        // headings

        $heading = array('', 'Gruppe','Kennziffer','Kategorie','Projekt');
        $width = array('10%', '10%', '15%','15%', '10%', '10%');
        $findcols = array('g.id','g.name','g.kennziffer','k.bezeichnung','p.abkuerzung');
        $searchsql = array('g.name','g.kennziffer','k.bezeichnung','p.abkuerzung');

        $defaultorderdesc = 0; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)
        $defaultorder = 2; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $menu = "%value%";
        //<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=drucker&action=spoolerdownload&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/download.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=drucker&action=spoolerdelete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS g.id, 
                           CONCAT('<input type=checkbox ',if(
                             (
                               SELECT ar.id 
                               FROM adresse_rolle ar 
                               WHERE ar.adresse='$id' AND (ar.subjekt='Mitglied' OR ar.subjekt='Kunde') AND ar.objekt='Gruppe' AND ar.parameter=g.id AND (ar.bis='0000-00-00' OR ar.bis >= NOW()) 
                               LIMIT 1
                             ) > 0  ,'checked',''),' onclick=adresse_gruppen($id,',g.id,',this.checked)>'),g.name,g.kennziffer,k.bezeichnung,p.abkuerzung 
          FROM gruppen g LEFT JOIN gruppen_kategorien k ON g.kategorie = k.id LEFT JOIN projekt p ON p.id=g.projekt";

        if($this->app->Secure->GetGET("more_data1")) {
          $subwhere = " AND g.aktiv!=1 ";
        }
        else {
          $subwhere = " AND g.aktiv=1 ";
        }


        if($this->app->Secure->GetGET("more_data2")) $subwhere2 = " AND (SELECT ar.id FROM adresse_rolle ar WHERE ar.adresse='$id' AND (ar.subjekt='Mitglied' OR ar.subjekt='Kunde') AND ar.objekt='Gruppe' AND ar.parameter=g.id AND (ar.bis='0000-00-00' OR ar.bis >= NOW()) LIMIT 1) > 0 ";

        $where = " g.id!=0 $subwhere $subwhere2 ".$this->app->erp->ProjektRechte();

        $count = "SELECT COUNT(g.id) FROM gruppen g LEFT JOIN projekt p ON p.id=g.projekt WHERE $where ";
        break;
      case "adresse_artikel_gebuehr":
        $allowed['adresse'] = array('kundeartikel');

        // headings
        $heading = array('Nummer', 'Artikel', 'Rechnung', 'Datum', 'Menge', 'Einzelpreis', 'Rabatt', 'Men&uuml;');
        $width = array('10%', '45%', '15%', '10%', '10%', '10%', '10%', '10%');
        $findcols = array('nummer', 'name_de', 'rechnung', 'belegnr', 'menge', 'preis', 'rabatt', 'id');
        $searchsql = array('a.bezeichnung', 'a.nummer', 'auf.belegnr', "DATE_FORMAT(auf.datum,'%d.%m.%Y')", 'a.preis', 'a.rabatt');
        $menu = "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer as nummer, a.bezeichnung as name_de, auf.belegnr as rechnung, DATE_FORMAT(auf.datum,'%d.%m.%Y'), a.menge as menge, a.preis as preis, 
              a.rabatt as rabatt, a.rechnung as menu
              FROM rechnung_position a LEFT JOIN rechnung auf ON a.rechnung=auf.id LEFT JOIN artikel art ON art.id=a.artikel";

        // fester filter
        $where = " auf.adresse='$id' AND art.gebuehr=1";
        $count = "SELECT COUNT(a.id) FROM rechnung_position a LEFT JOIN rechnung auf ON a.rechnung=auf.id WHERE auf.adresse='$id'";
        break;

      case "adresse_artikel_serviceartikel":
        $allowed['adresse'] = array('kundeartikel');

        // headings
        $heading = array('Nummer', 'Artikel', 'Rechnung', 'Datum', 'Menge', 'Einzelpreis', 'Rabatt', 'Men&uuml;');
        $width = array('10%', '45%', '15%', '10%', '10%', '10%', '10%', '10%');
        $findcols = array('nummer', 'name_de', 'rechnung', 'belegnr', 'menge', 'preis', 'rabatt', 'id');
        $searchsql = array('a.bezeichnung', 'a.nummer', 'auf.belegnr', "DATE_FORMAT(auf.datum,'%d.%m.%Y')", 'a.preis', 'a.rabatt');
        $menu = "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer as nummer, a.bezeichnung as name_de, auf.belegnr as rechnung, DATE_FORMAT(auf.datum,'%d.%m.%Y'), a.menge as menge, a.preis as preis, 
              a.rabatt as rabatt, a.rechnung as menu
              FROM rechnung_position a LEFT JOIN rechnung auf ON a.rechnung=auf.id LEFT JOIN artikel art ON art.id=a.artikel";

        // fester filter
        $where = " auf.adresse='$id' AND art.serviceartikel=1";
        $count = "SELECT COUNT(a.id) FROM rechnung_position a LEFT JOIN rechnung auf ON a.rechnung=auf.id WHERE auf.adresse='$id'";
        break;
      case "adresse_artikel_geraet":
        $allowed['adresse'] = array('kundeartikel');

        // headings
        $heading = array('Nummer', 'Artikel', 'Rechnung', 'Datum', 'Menge', 'Einzelpreis', 'Rabatt', 'Men&uuml;');
        $width = array('10%', '45%', '15%', '10%', '10%', '10%', '10%', '10%');
        $findcols = array('nummer', 'name_de', 'rechnung', 'belegnr', 'menge', 'preis', 'rabatt', 'id');
        $searchsql = array('a.bezeichnung', 'a.nummer', 'auf.belegnr', "DATE_FORMAT(auf.datum,'%d.%m.%Y')", 'a.preis', 'a.rabatt');
        $menu = "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer as nummer, a.bezeichnung as name_de, auf.belegnr as rechnung, DATE_FORMAT(auf.datum,'%d.%m.%Y'), a.menge as menge, a.preis as preis, 
              a.rabatt as rabatt, a.rechnung as menu
              FROM rechnung_position a LEFT JOIN rechnung auf ON a.rechnung=auf.id LEFT JOIN artikel art ON art.id=a.artikel";

        // fester filter
        $where = " auf.adresse='$id' AND art.geraet=1";
        $count = "SELECT COUNT(a.id) FROM rechnung_position a LEFT JOIN rechnung auf ON a.rechnung=auf.id WHERE auf.adresse='$id'";
        break;
      case "adressebelege":
        $id = $this->app->Secure->GetGET('id');
        $allowed['adresse'] = array('belege');
        if($this->app->erp->IsAdresseSubjekt($id,"Lieferant")){
          $heading = array('Belegnr./Lf-Nr.', 'Belegart', 'vom', 'Anfrage', 'Kommission/Bestellnr.', 'Tracking', 'Projekt', 'Zahlungsstatus', 'Betrag', 'Status', 'Men&uuml;', '');
        }else{
          $heading = array('Belegnr.', 'Belegart', 'vom', 'Anfrage', 'Kommission/Bestellnr.', 'Tracking', 'Projekt', 'Zahlungsstatus', 'Betrag', 'Status', 'Men&uuml;', '');
        }
        
        $width = array('10%', '10%', '10%', '10%', '10%', '10%', '10%', '9%', '10%', '10%', '1%', '1%');
        $findcols = array('b.belegnr', 'b.belegart', 'b.vom', 'b.anfrage', 'b.name', 'b.tracking', 'b.projekt', 'b.zahlungsstatus', 'CAST(b.betrag as DECIMAL(10,2))', 'b.status', 'b.menux', 'b.menu');
        $searchsql = array('b.belegnr', 'b.belegart', "DATE_FORMAT(b.vom, '%d.%m.%Y')", 'b.anfrage', 'b.name', 'b.tracking', 'b.projekt', 'b.zahlungsstatus', 'b.betrag', 'b.status');

        $numbercols = array(8);

        $datecols = array(2);

        //$sumcol = 8;

        $alignright = array(9);

        $menu = "";

        $tmp = explode('_',$name,2);
        $prefix = $tmp[1];

        $von = $this->TableSearchFilter($name, 13, 'von', $this->app->User->GetParameter("adresse_belege_von"));
        $bis = $this->TableSearchFilter($name, 14, 'bis', $this->app->User->GetParameter("adresse_belege_bis"));

        $subwhere = "";

        if($von != "" && $von != 0 && strlen($von) >= 8){ // strlen 8 = minimum 1.1.2018
          try {
            $vonDateTime = new DateTimeImmutable($von);
            $von = $vonDateTime->format("Y-m-d");
          } catch (Exception $e) {
            $von = "";
          }
        } else {
          $von = "";
        }
        if($bis != "" && $bis != 0 && strlen($bis) >= 8){ // strlen 8 = minimum 1.1.2018
          try {
            $bisDateTime = new DateTimeImmutable($bis);
            $bis = $bisDateTime->format("Y-m-d");
          } catch (Exception $e) {
            $bis = "";
          }
        } else {
          $bis = "";
        }
        
        if($von !="" && $von != 0) $subwhere .= " AND DATE_FORMAT(b.vom, '%Y-%m-%d') >= '$von' ";
        if($bis !="" && $bis != 0) $subwhere .= " AND DATE_FORMAT(b.vom, '%Y-%m-%d') <= '$bis' ";    


        $fauftrag = $this->TableSearchFilter($name, 5, 'auftrag', '0', 0, 'checkbox');
        $frechnung = $this->TableSearchFilter($name, 6, 'rechnung', '0', 0, 'checkbox');
        $fgutschrift = $this->TableSearchFilter($name, 7, 'gutschrift', '0', 0, 'checkbox');
        $fangebot = $this->TableSearchFilter($name, 8, 'angebot', '0', 0, 'checkbox');
        $flieferschein = $this->TableSearchFilter($name, 10, 'lieferschein', '0', 0, 'checkbox');
        $fbestellung = $this->TableSearchFilter($name, 11, 'bestellung', '0', 0, 'checkbox');
        $fverbindlichkeit = $this->TableSearchFilter($name, 12, 'verbindlichkeit', '0', 0, 'checkbox');

        if($this->app->erp->IsAdresseSubjekt($id,"Lieferant")){
          if(!$fangebot && !$fauftrag && !$frechnung && !$fgutschrift && !$flieferschein && !$fbestellung && !$fverbindlichkeit){
            $fangebot = 1;
            $fauftrag = 1;
            $frechnung = 1;
            $fgutschrift = 1;          
            $flieferschein = 1;
            $fbestellung = 1;
            $fverbindlichkeit = 1;
          }
        }else{
          if(!$fangebot && !$fauftrag && !$frechnung && !$fgutschrift && !$flieferschein){
            $fangebot = 1;
            $fauftrag = 1;
            $frechnung = 1;
            $fgutschrift = 1;          
            $flieferschein = 1;
          }
        }

        //angebot
        if($fangebot && $this->app->erp->RechteVorhanden('angebot','list')){
          $sqla[] = "(SELECT a.id, if(a.belegnr='',CONCAT('<b>ENTWURF</b>'), CONCAT('<b><a target=\"_blank\" href=\"index.php?module=angebot&action=edit&id=',a.id,'\">',a.belegnr,'</a></b>')) as belegnr, 'Angebot' as belegart, a.datum as vom, 
                a.anfrage as anfrage, if(a.internebezeichnung!='',CONCAT('<i style=color:#999>',a.internebezeichnung,'</i>'),'') as name, ' ' as tracking, LEFT(UPPER(p.abkuerzung),10) as projekt, ' ' as zahlungsstatus, ".$this->app->erp->FormatPreis("a.gesamtsumme",2)." as betrag, a.status as status, CONCAT('<a target=\"_blank\" href=\"index.php?module=angebot&action=edit&id=',a.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=angebot&action=pdf&id=',a.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>') as menux, a.id as menu FROM angebot a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id WHERE a.adresse='$id' AND a.status!='angelegt' " . $this->app->erp->ProjektRechte() . ")";
        }

        //auftrag
        if($fauftrag && $this->app->erp->RechteVorhanden('auftrag','list')){
          $sqla[] = "(SELECT au.id, if(au.belegnr='',CONCAT('<b>ENTWURF</b>'), CONCAT('<b><a target=\"_blank\" href=\"index.php?module=auftrag&action=edit&id=',au.id,'\">',au.belegnr,'</a></b>')) as belegnr, 'Auftrag' as belegart, au.datum as vom, ' ' as anfrage, 
                CONCAT(au.ihrebestellnummer,if(au.internebezeichnung!='',CONCAT(IF(au.ihrebestellnummer!='','<br>',''),'<i style=color:#999>',au.internebezeichnung,'</i>'),'')) as name, ' ' as tracking, LEFT(UPPER(p.abkuerzung),10) as projekt, ' ' as zahlungsstatus, ".$this->app->erp->FormatPreis("au.gesamtsumme",2)." as betrag, au.status as status, CONCAT('<a target=\"_blank\" href=\"index.php?module=auftrag&action=edit&id=',au.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=auftrag&action=pdf&id=',au.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>') as menux, au.id as menu FROM auftrag au LEFT JOIN projekt p ON p.id=au.projekt LEFT JOIN adresse adr ON au.adresse=adr.id WHERE au.adresse='$id' AND au.status!='angelegt' " . $this->app->erp->ProjektRechte() . ")";
        }

        //rechnung
        if($frechnung && $this->app->erp->RechteVorhanden('rechnung','list')){
          $sqla[] = "(SELECT r.id, if(r.belegnr='',CONCAT('<b>ENTWURF</b>'), CONCAT('<b><a target=\"_blank\" href=\"index.php?module=rechnung&action=edit&id=',r.id,'\">',r.belegnr,'</a></b>')) as belegnr, 'Rechnung' as belegart, 
                CONCAT(r.datum,'%d.%m.%Y',' ',if(r.zahlungsstatus='offen', if(DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day) >= NOW(),CONCAT('<br><font color=blue>f&auml;llig in ',DATEDIFF(DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day),NOW()),' Tagen</font>'),CONCAT('<br><font color=red>f&auml;llig seit ',DATEDIFF(NOW(),DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day)),' Tagen</font>'))
                      ,'')) as vom, 
                ' ' as anfrage, CONCAT(a.ihrebestellnummer,if(r.internebezeichnung!='',CONCAT(IF(a.ihrebestellnummer!='','<br>',''),'<i style=color:#999>',r.internebezeichnung,'</i>'),'')) as name, ' ' as tracking, LEFT(UPPER(p.abkuerzung),10) as projekt, 
                if(r.zahlungsstatus='offen',
                  if(DATEDIFF(NOW(),DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day)) > 0,
                    CONCAT('<font color=red>',upper(substring(r.mahnwesen,1,1)),lower(substring(r.mahnwesen,2)),'</font>'),
                    'offen')

                  ,if(r.zahlungsstatus='','offen',r.zahlungsstatus)) as zahlungsstatus,
                ".$this->app->erp->FormatPreis("r.soll",2)." as betrag, r.status as status, CONCAT('<a target=\"_blank\" href=\"index.php?module=rechnung&action=edit&id=',r.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=',r.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>') as menux, r.id as menu FROM rechnung r LEFT JOIN auftrag a ON r.auftragid=a.id LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id WHERE r.adresse='$id' AND r.status!='angelegt' " . $this->app->erp->ProjektRechte() . ")";
        }

        //gutschrift
        if($fgutschrift && $this->app->erp->RechteVorhanden('gutschrift','list')){
          $sqla[] = "(SELECT g.id, if(g.belegnr='',CONCAT('<b>ENTWURF</b>'), CONCAT('<b><a target=\"_blank\" href=\"index.php?module=gutschrift&action=edit&id=',g.id,'\">',g.belegnr,'</a></b>')) as belegnr, 'Gutschrift' as belegart, g.datum as vom, 
                ' ' as anfrage, if(g.internebezeichnung!='',CONCAT('<i style=color:#999>',g.internebezeichnung,'</i>'),'') as name, ' ' as tracking, LEFT(UPPER(p.abkuerzung),10) as projekt, g.zahlungsstatus as zahlungsstatus, CONCAT('-', ".$this->app->erp->FormatPreis("g.soll",2).") as betrag, g.status as status, CONCAT('<a target=\"_blank\" href=\"index.php?module=gutschrift&action=edit&id=',g.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=gutschrift&action=pdf&id=',g.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>') as menux, g.id as menu FROM gutschrift g LEFT JOIN projekt p ON p.id=g.projekt LEFT JOIN adresse adr ON g.adresse=adr.id WHERE g.adresse='$id' AND g.status!='angelegt' " . $this->app->erp->ProjektRechte() . ")";
        }

        //lieferschein
        if($flieferschein && $this->app->erp->RechteVorhanden('lieferschein','list')){
          $sqla[] = "(SELECT l.id, if(l.belegnr='',CONCAT('<b>ENTWURF</b>'), CONCAT('<b><a target=\"_blank\" href=\"index.php?module=lieferschein&action=edit&id=',l.id,'\">',l.belegnr,'</a></b>')) as belegnr, 'Lieferschein' as belegart, l.datum as vom, 
                ' ' as anfrage, CONCAT(a.ihrebestellnummer,if(l.internebezeichnung!='',CONCAT(IF(a.ihrebestellnummer!='','<br>',''),'<i style=color:#999>',l.internebezeichnung,'</i>'),'')) as name, if(v.tracking,v.tracking,'-') as tracking, LEFT(UPPER(p.abkuerzung),10) as projekt, ' ' as zahlungsstatus, '0,00' as betrag, l.status as status, CONCAT('<a target=\"_blank\" href=\"index.php?module=lieferschein&action=edit&id=',l.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=',l.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>') as menux, l.id as menu FROM lieferschein l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.adresse=adr.id LEFT JOIN auftrag a ON l.auftragid=a.id LEFT JOIN versand v ON v.lieferschein=l.id WHERE l.adresse='$id' AND l.status!='angelegt' " . $this->app->erp->ProjektRechte() . ")";
        }

        if($this->app->erp->IsAdresseSubjekt($id,"Lieferant")){
          //bestellung
          if($fbestellung && $this->app->erp->RechteVorhanden('bestellung','list')){
            $sqla[] = "(SELECT be.id, if(be.belegnr='',CONCAT('<b>ENTWURF</b>'), CONCAT('<b><a target=\"_blank\" href=\"index.php?module=bestellung&action=edit&id=',be.id,'\">',be.belegnr,'</a></b>')) as belegnr, 'Bestellung' as belegart, be.datum as vom,
                  ' ' as anfrage, if(be.internebezeichnung!='',CONCAT('<i style=color:#999>',be.internebezeichnung,'</i>'),'') as name, ' ' as tracking, LEFT(UPPER(p.abkuerzung),10) as projekt, ' ' as zahlungsstatus, ".$this->app->erp->FormatPreis("be.gesamtsumme",2)." as betrag, be.status as status, CONCAT('<a target=\"_blank\" href=\"index.php?module=bestellung&action=edit&id=',be.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=bestellung&action=pdf&id=',be.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>') as menux, be.id as menu FROM bestellung be LEFT JOIN projekt p ON p.id=be.projekt LEFT JOIN adresse adr ON be.adresse=adr.id WHERE be.adresse='$id' AND be.status !='angelegt' " . $this->app->erp->ProjektRechte() . ")";
          }

          //verbindlichkeit
          if($fverbindlichkeit && $this->app->erp->RechteVorhanden('verbindlichkeit','list')){
            $sqla[] = "(SELECT v.id, CONCAT('<b><a target=\"_blank\" href=\"index.php?module=verbindlichkeit&action=edit&id=',v.id,'\">',v.id,'</a></b>') as belegnr, 'Verbindlichkeit' as belegart, v.rechnungsdatum as vom,
                  ' ' as anfrage, '' as name, ' ' as tracking, LEFT(UPPER(p.abkuerzung),10) as projekt, v.status as zahlungsstatus, CONCAT('-', ".$this->app->erp->FormatPreis("v.betrag",2).") as betrag, ' ' as status, CONCAT('<a target=\"_blank\" href=\"index.php?module=verbindlichkeit&action=edit&id=',v.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=verbindlichkeit&action=pdfanhang&id=',v.id,'\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>') as menux, v.id as menu FROM verbindlichkeit v LEFT JOIN projekt p ON p.id=v.projekt LEFT JOIN adresse adr ON v.adresse=adr.id WHERE v.adresse='$id' AND v.status != 'angelegt'
                  " . $this->app->erp->ProjektRechte() . ")";
          }

        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS b.id, b.belegnr, b.belegart, DATE_FORMAT(b.vom,'%d.%m.%Y'), b.anfrage, b.name, b.tracking, b.projekt, b.zahlungsstatus, b.betrag, b.status, b.menux, b.menu
            FROM (            
              ".implode(" UNION ALL ", $sqla)."
            )b
        ";

        $where = "b.id > 0".$subwhere;

        break;

      case "artikelfreifelder_list":
        $allowed['artikel'] = array('list');

        $heading = array('Freifeld-Nr.', 'Freifeldname', 'Sprache', 'Wert', 'Wert Deutsch', 'Men&uuml;', '');
        $width = array('10%', '20%', '20%', '30%', '30%', '10%', '1%');

        $findcols = array('CAST(s.nummer AS SIGNED)', 's.bezeichnung', 's.sprache', 's.wert', 's.freifeld', 'menux', 's.id');
        $searchsql = array('s.nummer', 's.bezeichnung', 's.sprache', 's.wert', 's.freifeld');

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $freifelder = array();
        $freifelderfirmendaten = array();
        $freifelderde = array();

        $artikelid = $this->app->Secure->GetGET('id');

        for($i=1; $i<=40; $i++){
          $freifeldname = $this->app->erp->Firmendaten('freifeld'.$i);
          if($freifeldname == '')$freifeldname = 'Freifeld '.$i;
          $freifelder[] = "(SELECT a.id, ".$i." AS nummer, a.freifeld".$i." AS freifeld FROM artikel a WHERE id = '$artikelid')";
          $freifelderfirmendaten[] = "(SELECT ".$i." AS nummer, '$freifeldname' AS bezeichnung )";
          $freifelderde[] = "(SELECT a.id, ".$i." AS nummer, '$freifeldname' AS bezeichnung, 'DE' AS sprache, freifeld".$i." AS wert, freifeld".$i." AS freifeld FROM artikel a WHERE id ='$artikelid')";
        }
        
        $where = "";

        $menu = "";

        $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, s.nummer, s.bezeichnung, s.sprache, s.wert, s.freifeld, 
        CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap>',
        '<a href=\"javascript:;\" onclick=\'ArtikelfreifelderEdit(\"',s.id,':', s.sprache,':', s.nummer,'\");\'><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;',IF(s.sprache != 'DE',CONCAT(
        '<a href=\"javascript:;\" onclick=\"ArtikelfreifelderDelete(',s.id,');\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>'),''),'</td></tr></table>') as menux

        , s.id FROM (SELECT af.id, af.nummer, f.bezeichnung, af.sprache, af.wert, a.freifeld FROM (".implode(" UNION ALL ", $freifelder).") a LEFT JOIN artikel_freifelder af ON a.id = af.artikel AND a.nummer = af.nummer LEFT JOIN (".implode(" UNION ALL ", $freifelderfirmendaten).") f ON af.nummer = f.nummer WHERE af.id > 0 AND af.artikel = '$artikelid'
        UNION ALL ".implode(" UNION ALL ", $freifelderde).") AS s"; 

        break;


      case "adressebestellungen":
        $allowed['adresse'] = array('offenebestellungen');

        $heading = array('Bestelldatum', 'Belegnr.', 'Name', 'Nummer', 'Status', 'Versendet am', 'Versendet durch', 'Versendet per', 'Men&uuml;');
        $width = array('5%', '10%', '20%', '10%', '15%', '5%', '15%', '15%', '15%');
        $findcols = array('datum', "if(belegnr != '',belegnr,'ohne Nummer')", 'name', 'lieferantennummer', 'status', 'versendet_am', 'versendet_durch', 'versendet_per', 'id');
        $searchsql = array("DATE_FORMAT(datum, '%d.%m.%Y')", "if(belegnr != '',belegnr,'ohne Nummer')", 'name', 'lieferantennummer', 'status', "DATE_FORMAT(versendet_am,'%d.%m.%Y')", 'versendet_durch', 'versendet_per');

        $menu = "<a href=\"index.php?module=bestellung&action=edit&id=%value%\">";
          $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>"."&nbsp;";
        $menu .= "<a href=\"index.php?module=bestellung&action=pdf&id=%value%\">";
          $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>"."&nbsp;";
        $menu .= '<a href="javascript:;" onclick="Kopieren(%value%);">';
          $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
        $menu .= "</a>";

        $datecols = array(0,5);

        $fabgeschlossen = $this->TableSearchFilter($name, 4, 'abgeschlossen', '0', 0, 'checkbox');
        
        $subwhere = "";

        if($fabgeschlossen == 1){
          $subwhere .= " OR status = 'abgeschlossen') ";
        }else{
          $subwhere .= ")";
        }
         
        $sql = "SELECT SQL_CALC_FOUND_ROWS id, DATE_FORMAT(datum, '%d.%m.%Y'), if(belegnr != '',belegnr,'ohne Nummer') as beleg, name, lieferantennummer, status, DATE_FORMAT(versendet_am, '%d.%m.%Y') as versendet_am, versendet_durch, versendet_per, id FROM bestellung";

        $where = "id > 0 AND adresse = '$id' AND (status = 'angelegt' OR status = 'freigegeben' OR status = 'storniert' OR status = 'versendet'".$subwhere;
        
        break;

      case "adressebestellungen_artikel":
        $allowed['adresse'] = array('offenebestellungen');

        $heading = array('Bestelldatum', 'Belegnr.', 'Name', 'Nummer', 'Bestellnummer', 'Status', 'Lieferdatum', 'Projekt', 'Menge', 'Geliefert', 'Preis', 'Men&uuml;', '');
        $width = array('10%', '10%', '10%', '10%', '10%', '10%', '10%', '10%', '10%', '10%', '10%', '10%', '5%');
        $findcols = array('datum', "if(b.belegnr != '', belegnr, 'ohne Nummer')", 'bp.bezeichnunglieferant', 'a.nummer', 'bp.bestellnummer', "if(bp.geliefert < bp.menge, 'offen', 'abgeschlossen')", "if(bp.lieferdatum, DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort')", 'p.abkuerzung', 'bp.menge', 'bp.geliefert', 'bp.preis', 'menux', 'bp.id');
        $searchsql = array("DATE_FORMAT(datum, '%d.%m.%Y')", "if(b.belegnr != '', belegnr, 'ohne Nummer')", 'bp.bezeichnunglieferant','bp.bezeichnunglieferant', 'a.nummer', 'bp.bestellnummer', "if(bp.geliefert < bp.menge, 'offen', 'abgeschlossen')", "if(bp.lieferdatum,DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort')", 'p.abkuerzung', $this->app->erp->FormatMenge('bp.menge'), 'bp.geliefert', $this->app->erp->FormatPreis('bp.preis', 2));

        $numbercols = array(8,10);
        $alignright = array(9,10,11);

        $datecols = array(0);

        $fabgeschlossen = $this->TableSearchFilter($name, 7, 'abgeschlossenartikel', '0', 0, 'checkbox');

        $subwhere = "";

        if($fabgeschlossen == 1){
          $subwhere .= " OR bp.geliefert >= bp.menge )";
        }else{
          $subwhere .= ")";
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS bp.id, DATE_FORMAT(b.datum, '%d.%m.%Y'), if(b.belegnr != '', belegnr, 'ohne Nummer') as belegnr, LEFT(bp.bezeichnunglieferant, 20) as name, a.nummer, bp.bestellnummer, if(bp.geliefert < bp.menge, 'offen', 'abgeschlossen') as status, if(bp.lieferdatum, DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum, p.abkuerzung, ".$this->app->erp->FormatMenge('bp.menge').", ".$this->app->erp->FormatMenge('bp.geliefert').", ".$this->app->erp->FormatPreis('bp.preis', 2)." as preis, if(bp.geliefert < bp.menge, CONCAT('<a href=\"javascript:;\" onclick=\"Geliefert(', bp.id, ',', b.adresse,');\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/right.png\"  width=\"18\"border=\"0\"></a>'), 'geliefert') as menux, bp.id FROM bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung = b.id LEFT JOIN artikel a ON a.id = bp.artikel LEFT JOIN projekt p ON p.id = bp.projekt";

        $where = "b.id > 0 AND b.adresse = '$id' AND (bp.geliefert < bp.menge".$subwhere;
        
        break;
      
      case "adresseartikel":
        $allowed['adresse'] = array('kundeartikel');
        // headings
        $heading = array('Nummer', 'Artikel', 'Belegart', 'Belegnr.', 'Datum', 'Menge', 'Einzelpreis', 'Rabatt', 'Gesamt', 'Men&uuml;', '','');
        $width = array('10%', '45%', '10%', '15%', '10%', '10%', '10%', '10%', '10%', '1%','1%','1%');
        $findcols = array('b.nummer', 'b.name_de', 'b.typ', 'b.belegnr', 'b.datum', 'b.menge', 'CAST(b.preis AS DECIMAL(10,2))', 'CAST(b.rabatt AS DECIMAL(10,2))', 'CAST(b.gesamt AS DECIMAL(10,2))', 'b.menu');
        $searchsql = array('b.nummer', 'b.name_de', 'b.typ', 'b.belegnr', "DATE_FORMAT(b.datum,'%d.%m.%Y')", $this->app->erp->FormatMenge('b.menge'), $this->app->erp->FormatPreis('b.preis',2), $this->app->erp->FormatPreis('b.rabatt',2), $this->app->erp->FormatPreis('b.gesamt',2));
        if($this->app->erp->Firmendaten("artikel_artikelnummer_suche") == "1"){


          $maxEinkauf = $this->app->DB->Select(
            "SELECT MAX(ct) as mx FROM(
                SELECT artikel, COUNT(bestellnummer) as ct FROM einkaufspreise
                WHERE bestellnummer IS NOT NULL 
                AND bestellnummer !=''
                GROUP BY artikel
            ) as data");

          $maxVerkauf = $this->app->DB->Select(
            "SELECT MAX(ct) as mx FROM(
                SELECT artikel, COUNT(kundenartikelnummer) as ct FROM verkaufspreise
                WHERE kundenartikelnummer IS NOT NULL 
                AND kundenartikelnummer !=''
                GROUP BY artikel
            ) as data");

          for($i=0;$i<$maxEinkauf;$i++){
            $searchsql[] = "(SELECT bestellnummer FROM einkaufspreise e WHERE e.artikel=b.id LIMIT ".$i.",1)";
          }

          for($i=0;$i<$maxVerkauf;$i++){
            $searchsql[] = "(SELECT kundenartikelnummer FROM verkaufspreise v WHERE v.artikel=b.id LIMIT ".$i.",1)";
          }

        }
        $sumcol = 9;
        //$menu = "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>";
        $numbercols = array(5,6,7,8);
        $alignright = array(6,7,8,9);

        $fauftrag = $this->TableSearchFilter($name, 5, 'auftrag', '0', 0, 'checkbox');
        $frechnung = $this->TableSearchFilter($name, 6, 'rechnung', '0', 0, 'checkbox');
        $fgutschrift = $this->TableSearchFilter($name, 7, 'gutschrift', '0', 0, 'checkbox');
        $fangebot = $this->TableSearchFilter($name, 8, 'angebot', '0', 0, 'checkbox');
        $fbestellung = $this->TableSearchFilter($name, 9, 'bestellung', '0', 0, 'checkbox');
        $flieferschein = $this->TableSearchFilter($name, 10, 'lieferschein', '0', 0, 'checkbox');
        $fproduktion = $this->TableSearchFilter($name, 11, 'produktion', '0', 0, 'checkbox');

        $fnurgeraet = $this->TableSearchFilter($name, 12, 'nurgeraete', '0', 0, 'checkbox');
        $fnurservice = $this->TableSearchFilter($name, 13, 'nurservice', '0', 0, 'checkbox');
        $fnurgebuehr = $this->TableSearchFilter($name, 14, 'nurgebuehr', '0', 0, 'checkbox');
        $fmitfreifelder = $this->TableSearchFilter($name, 15, 'mitfreifelder', '0', 0, 'checkbox');

        if(!$fauftrag && !$frechnung && !$fgutschrift && !$fangebot && !$flieferschein && !$fproduktion && !$fbestellung)
        {
          $fauftrag = 1;
          $frechnung = 1;
          $fgutschrift = 1;
          $fangebot = 1;
          $flieferschein = 1;
          $fproduktion = 1;
          $fbestellung = 1;
        }

        if($fnurgeraet) $tmp_or[]="a.geraet='1'";
        if($fnurservice) $tmp_or[]="a.serviceartikel='1'";
        if($fnurgebuehr) $tmp_or[]="a.gebuehr='1'";

        $startindex=8;   
        if(1)//$fmitfreifelder)$id, $lagerplatz, $mitauswahl, $ziellager, $selartikel
        {
          for($i=1;$i<=20;$i++)
          {
            if($this->app->erp->Firmendaten("freifeld".$i."an") || $this->app->erp->Firmendaten("freifeld".$i."ab") ||
                $this->app->erp->Firmendaten("freifeld".$i."re") || $this->app->erp->Firmendaten("freifeld".$i."gs") ||
                $this->app->erp->Firmendaten("freifeld".$i."ls")
            )
            {
              $startindex++; 
              $heading[$startindex]=$this->app->erp->Firmendaten("freifeld".$i);
              $findcols[$startindex]="b.freifeld".$i;
              $width[$startindex]="5%";
              $tmpsql[] = "ap.freifeld".$i;
              $indexes[] = $startindex+1;
            }
          }
          $hidecolumns = array('mitfreifelder','unchecked',$indexes);
          $startindex++; 
          $heading[$startindex]="Men&uuml;";
          $findcols[$startindex]="b.menu";

          $tmpsqlstring = implode(',',$tmpsql);

          if($tmpsqlstring!="")$tmpsqlstring .=",";
        }

        

        if(count($tmp_or)>0)
        {  
          $tmp = "AND (".implode(' or ',$tmp_or).")";
        }

        

        //auftrag
        if($fauftrag && $this->app->erp->RechteVorhanden('auftrag','list')){
          $sqla[] = "(SELECT a.id, 
          a.nummer as nummer, 
          concat(ap.bezeichnung, '<input type=\"hidden\" id=\"kommentar_', ap.id,'\" value=\"',ap.internerkommentar,'\"/>', if(ap.internerkommentar <> '', concat('<br /><span style=\"color:red\">', ap.internerkommentar, '</span>'),'')) as name_de,
          'Auftrag' as typ, 
          auf.belegnr as belegnr, 
          auf.datum, 
          ap.menge as menge, 
          ap.preis as preis, 
          ap.rabatt as rabatt, 
          ".$this->app->erp->FormatPreis("ap.preis*ap.menge*(IF(ap.rabatt>0, (100-ap.rabatt)/100, 1))",2)." as gesamt, $tmpsqlstring
          CONCAT('<table cellpadding=\"0\" cellspacing=\"0\"><tr><td nowrap><a href=\"javascript:;\" onclick=\"InternerKommentarEdit(1,',ap.id,')\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;', '<a href=\"index.php?module=auftrag&action=edit&id=', ap.auftrag, '\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a></td></tr></table>') as menu FROM auftrag_position ap INNER JOIN auftrag auf ON ap.auftrag = auf.id LEFT JOIN artikel a ON a.id = ap.artikel WHERE auf.adresse = '$id' $tmp)";
        }
        //rechnung
        if($frechnung && $this->app->erp->RechteVorhanden('rechnung','list')){
          $sqla[] = "(SELECT a.id, 
          a.nummer as nummer, 
          concat(rp.bezeichnung, '<input type=\"hidden\" id=\"kommentar_', rp.id,'\" value=\"',rp.internerkommentar,'\"/>', if(rp.internerkommentar <> '', concat('<br /><span style=\"color:red\">', rp.internerkommentar, '</span>'),'')) as name_de, 
          'Rechnung' as typ, 
          rec.belegnr as belegnr, 
          rec.datum,  
          rp.menge as menge, 
          rp.preis as preis, 
          rp.rabatt as rabatt, 
          ".$this->app->erp->FormatPreis("rp.preis*rp.menge*(IF(rp.rabatt>0, (100-rp.rabatt)/100, 1))",2)." as gesamt, ".str_replace('ap.','rp.',$tmpsqlstring)."
          CONCAT('<table cellpadding=\"0\" cellspacing=\"0\"><tr><td nowrap><a href=\"javascript:;\" onclick=\"InternerKommentarEdit(2,',rp.id,')\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;', '<a href=\"index.php?module=rechnung&action=edit&id=', rp.rechnung, '\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a></td></tr></table>') as menu FROM rechnung_position rp INNER JOIN rechnung rec ON rp.rechnung = rec.id LEFT JOIN artikel a ON a.id = rp.artikel WHERE rec.adresse = '$id' $tmp)";
        }
        //gutschrift
        if($fgutschrift && $this->app->erp->RechteVorhanden('gutschrift','list')){
          $sqla[] = "(SELECT a.id, 
          a.nummer as nummer, 
          concat(gp.bezeichnung, '<input type=\"hidden\" id=\"kommentar_', gp.id,'\" value=\"',gp.internerkommentar,'\"/>', if(gp.internerkommentar <> '', concat('<br /><span style=\"color:red\">', gp.internerkommentar, '</span>'),'')) as name_de, 
          'Gutschrift' as typ, 
          gut.belegnr as belegnr, 
          gut.datum,  
          gp.menge as menge, 
          gp.preis as preis, 
          gp.rabatt as rabatt, 
          ".$this->app->erp->FormatPreis("gp.preis*gp.menge*(IF(gp.rabatt>0, (100-gp.rabatt)/100, 1))",2)." as gesamt, ".str_replace('ap.','gp.',$tmpsqlstring)."
          CONCAT('<table cellpadding=\"0\" cellspacing=\"0\"><tr><td nowrap><a href=\"javascript:;\" onclick=\"InternerKommentarEdit(3,',gp.id,')\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;', '<a href=\"index.php?module=gutschrift&action=edit&id=', gp.gutschrift, '\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a></td></tr></table>') as menu FROM gutschrift_position gp INNER JOIN gutschrift gut ON gp.gutschrift = gut.id LEFT JOIN artikel a ON a.id = gp.artikel WHERE gut.adresse = '$id' $tmp)";
        }
        //angebot
        if($fangebot && $this->app->erp->RechteVorhanden('angebot','list')){
          $sqla[] = "(SELECT a.id, 
          a.nummer as nummer, 
          concat(anp.bezeichnung, '<input type=\"hidden\" id=\"kommentar_', anp.id,'\" value=\"',anp.internerkommentar,'\"/>', if(anp.internerkommentar <> '', concat('<br /><span style=\"color:red\">', anp.internerkommentar, '</span>'),'')) as name_de, 
          'Angebot' as typ, 
          ang.belegnr as belegnr, 
          ang.datum,  
          anp.menge as menge, 
          anp.preis as preis, 
          anp.rabatt as rabatt, 
          ".$this->app->erp->FormatPreis("anp.preis*anp.menge*(IF(anp.rabatt>0, (100-anp.rabatt)/100, 1))",2)." as gesamt, ".str_replace('ap.','anp.',$tmpsqlstring)."
          CONCAT('<table cellpadding=\"0\" cellspacing=\"0\"><tr><td nowrap><a href=\"javascript:;\" onclick=\"InternerKommentarEdit(4,',anp.id,')\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;', '<a href=\"index.php?module=angebot&action=edit&id=', anp.angebot, '\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a></td></tr></table>') as menu FROM angebot_position anp INNER JOIN angebot ang ON anp.angebot = ang.id LEFT JOIN artikel a ON a.id = anp.artikel WHERE ang.adresse = '$id' $tmp)";
        }
        //bestellung
        if($fbestellung && $this->app->erp->RechteVorhanden('bestellung','list')){
          $sqla[] = "(SELECT a.id, 
          a.nummer as nummer, 
          bp.bezeichnunglieferant as name_de, 
          'Bestellung' as typ, 
          bes.belegnr as belegnr, 
          bes.datum, 
          bp.menge as menge, 
          bp.preis as preis, 
          '0,00' as rabatt, 
          ".$this->app->erp->FormatPreis("bp.preis*bp.menge",2)." as gesamt, ".str_replace('ap.','bp.',$tmpsqlstring)."
          CONCAT('<table cellpadding=\"0\" cellspacing=\"0\"><tr><td nowrap><a href=\"index.php?module=bestellung&action=edit&id=', bp.bestellung, '\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a></td></tr></table>') as menu FROM bestellung_position bp INNER JOIN bestellung bes ON bp.bestellung = bes.id LEFT JOIN artikel a ON a.id = bp.artikel WHERE bes.adresse = '$id' $tmp)";
        }
        //lieferschein
        if($flieferschein && $this->app->erp->RechteVorhanden('lieferschein','list')){
          $sqla[] = "(SELECT a.id, 
          a.nummer as nummer, 
          concat(lp.bezeichnung, '<input type=\"hidden\" id=\"kommentar_', lp.id,'\" value=\"',lp.internerkommentar,'\"/>', if(lp.internerkommentar <> '', concat('<br /><span style=\"color:red\">', lp.internerkommentar, '</span>'),'')) as name_de, 
          'Lieferschein' as typ, 
          lie.belegnr as belegnr, 
          lie.datum, 
          lp.menge as menge, 
          '0,00' as preis, 
          '0,00' as rabatt, 
          ".$this->app->erp->FormatPreis("0",2)." as gesamt, ".str_replace('ap.','lp.',$tmpsqlstring)."
          CONCAT('<table cellpadding=\"0\" cellspacing=\"0\"><tr><td nowrap><a href=\"javascript:;\" onclick=\"InternerKommentarEdit(5,',lp.id,')\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;', '<a href=\"index.php?module=lieferschein&action=edit&id=', lp.lieferschein, '\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>') as menu FROM lieferschein_position lp INNER JOIN lieferschein lie ON lp.lieferschein = lie.id LEFT JOIN artikel a ON a.id = lp.artikel WHERE lie.adresse = '$id' $tmp)";
        }
        //produktion
        if($fproduktion && $this->app->erp->ModulVorhanden('produktion') && $this->app->erp->RechteVorhanden('produktion','list')){
          $sqla[] = "(SELECT a.id, 
          a.nummer as nummer, 
          concat(pp.bezeichnung, '<input type=\"hidden\" id=\"kommentar_', pp.id,'\" value=\"',pp.internerkommentar,'\"/>', if(pp.internerkommentar <> '', concat('<br /><span style=\"color:red\">', pp.internerkommentar, '</span>'),'')) as name_de, 
          'Produktion' as typ, 
          pro.belegnr as belegnr, 
          pro.datum, 
          pp.menge as menge, 
          pp.preis as preis, 
          '0,00' as rabatt, 
          ".$this->app->erp->FormatPreis("pp.preis*pp.menge",2)." as gesamt, ".str_replace('ap.',"'' as ",$tmpsqlstring)."
          CONCAT('<table cellpadding=\"0\" cellspacing=\"0\"><tr><td nowrap><a href=\"javascript:;\" onclick=\"InternerKommentarEdit(6,',pp.id,')\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;', '<a href=\"index.php?module=produktion&action=edit&id=', pp.produktion, '\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a></td></tr></table>') as menu FROM produktion_position pp INNER JOIN produktion pro ON pp.produktion = pro.id LEFT JOIN artikel a ON a.id = pp.artikel WHERE pro.adresse = '$id' $tmp)";
        }


        $sql = "SELECT SQL_CALC_FOUND_ROWS b.id, b.nummer, b.name_de, b.typ, b.belegnr, DATE_FORMAT(b.datum, '%d.%m.%Y'), ".$this->app->erp->FormatMenge("b.menge").", ".$this->app->erp->FormatPreis("b.preis",2).", ".$this->app->erp->FormatPreis('b.rabatt',2).", b.gesamt, ".str_replace('ap.','b.',$tmpsqlstring)." b.menu
            FROM (            
              ".implode(" UNION ALL ", $sqla)."
            )b
        ";
        break;
      case "adresse_artikel_gekaufte":
        $allowed['adresse'] = array('artikel');

        // headings
        $heading = array('Artikel-Nr.', 'Artikel', 'Kategorie', 'Menge', 'Men&uuml;');
        $width = array('10%', '30%','30','10%', '1%');
        $findcols = array('a.nummer', 'a.name_de', "k.bezeichnung", "SUM(rp.menge)", 'a.id');
        $searchsql = array('a.nummer', 'a.name_de', 'k.bezeichnung', 'a.id');
        $menu = "<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>";

        $numbercols = array(3);

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer, a.name_de, k.bezeichnung,
        ".$this->app->erp->FormatMenge("SUM(rp.menge)").",a.id FROM rechnung_position rp 
        LEFT JOIN rechnung r ON r.id=rp.rechnung 
        LEFT JOIN artikel a ON a.id=rp.artikel 
        LEFT JOIN artikelkategorien k ON a.typ = concat(k.id,'_kat')
        ";

        // fester filter
        $where = " r.adresse='$id' AND r.status!='storniert' ";//.$this->app->erp->ProjektRechte();
        $groupby = " GROUP by rp.artikel";
        $alignright = array(4);

        $count = "SELECT COUNT(DISTINCT rp.artikel) FROM rechnung_position rp LEFT JOIN rechnung r ON r.id=rp.rechnung LEFT JOIN artikel a ON a.id=rp.artikel WHERE $where";

      break;
      case "adressestundensatz":
        $allowed['adresse'] = array('stundensatz');
        $heading = array("Projekt-ID", "Projekt", "Typ", "Stundensatz", "Men&uuml;");
        $width = array("10%", "50%", "10%", "15%", "15%");
        $findcols = array("p.id", "p.name", "typ", "satz", "ssid");
        $searchsql = array("p.name");
        $sql = "SELECT SQL_CALC_FOUND_ROWS  p.id, p.abkuerzung, p.name, IFNULL(ss.typ,'Standard') AS typ, 
              IFNULL(ss.satz, (SELECT satz 
                    FROM stundensatz
                    WHERE typ='Standard' AND adresse='$id'
                    ORDER BY datum DESC LIMIT 1)) AS satz,
              IFNULL(ss.id,CONCAT('&projekt=',p.id)) AS ssid
                FROM adresse_rolle ar
                LEFT JOIN projekt as p
                ON ar.parameter=p.id
                LEFT JOIN (SELECT * FROM stundensatz AS dss ORDER BY dss.datum DESC) AS ss
                ON p.id=ss.projekt AND ss.adresse=ar.adresse ";
        $where = " ar.adresse='$id' AND subjekt='Mitarbeiter' AND objekt='Projekt' GROUP BY p.id ";
        $count = "SELECT COUNT(parameter) FROM adresse_rolle WHERE adresse='$id' AND subjekt='Mitarbeiter' AND objekt='Projekt'";
        $menu = "<a href=\"index.php?module=adresse&action=stundensatzedit&user=$id&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse&action=stundensatzdelete&user=$id&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";
        $moreinfo = false;
        break;
      case "adresselohn":
        $allowed['adresse'] = array('lohn');
        $heading = array('Monat', 'Stunden', 'Men&uuml;');
        $width = array('20%', '20%', '20%', '40%');
        $findcols = array('monat', 'stunden');
        $searchsql = array('monat');
        $sql = "SELECT SQL_CALC_FOUND_ROWS id,DATE_FORMAT(von,'%Y-%m') AS monat,  
              SUM(ROUND((UNIX_TIMESTAMP(bis) - UNIX_TIMESTAMP(von))/3600,2)) as stunden
              FROM zeiterfassung ";
        $where = " adresse='$id' GROUP by monat"; //ORDER BY STR_TO_DATE(CONCAT(MONTH(von),',',YEAR(von)), '%m,%Y') ";

        
        //$where = " adresse='$id' GROUP BY monat,jahr ORDER BY STR_TO_DATE(CONCAT(MONTH(von),',',YEAR(von)), '%m,%Y') ";

        $count = "SELECT FOUND_ROWS() AS treffer,MONTHNAME(von) AS monat, YEAR(von) AS jahr
              FROM zeiterfassung WHERE adresse='$id' GROUP BY monat,jahr "; // ORDER BY STR_TO_DATE(CONCAT(MONTH(von),',',YEAR(von)), '%m,%Y');";

        
        //                                                                      SELECT FOUND_ROWS();";

        $menu = "test";
        $moreinfo = false;
        break;
      case "adresse_suche":
        $allowed['adresse'] = array('list');

        // headings
        $heading = array('Name', 'Kunde', 'Lieferant', 'Land', 'PLZ', 'Ort', 'E-Mail', 'Projekt', 'Men&uuml;');
        $width = array('18%', '10%', '5%', '5%', '5%', '5%', '5%', '15%', '10%');
        $findcols = array('name', 'kundennummer', 'lieferantennummer', 'land', 'plz', 'ort', 'email', 'projekt', 'id');
        $defaultorder = 9; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        
        $searchsql = array('a.sonstiges');

        $menu = "<a href=\"index.php?module=adresse&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";

        // SQL statement
        
        //if(a.typ = 'herr' OR a.typ = 'frau',CONCAT(a.vorname,' ',a.name),a.name) as name,

          if ($this->app->erp->Firmendaten("adresse_freitext1_suche")) {
            $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT(a.name,if(a.freifeld1!='',CONCAT(' (',a.freifeld1,')'),'')) as name,
                  if(a.kundennummer!='',a.kundennummer,'-') as kundennummer,
                    if(a.lieferantennummer!='',a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
                      FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt ";
          } else {
            $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.name as name,
                  if(a.kundennummer!='',a.kundennummer,'-') as kundennummer,
                    if(a.lieferantennummer!='',a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
                      FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt ";
          }

        // fester filter
        $where = "a.geloescht=0 " . $this->app->erp->ProjektRechte();
        $count = "SELECT COUNT(a.id) FROM adresse a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht=0 " . $this->app->erp->ProjektRechte();
        break;
      case "adresseinnendienst":
        $allowed['adresse'] = array('list');
        // headings
        $heading = array('Name', 'Kunde', 'Lieferant', 'Land', 'PLZ', 'Ort', 'E-Mail', 'Projekt', 'Men&uuml;');
        $width = array('18%', '10%', '5%', '5%', '5%', '5%', '5%', '15%', '1%');

        if($this->app->erp->Firmendaten("adresse_freitext1_suche")){
          $findcols = array("CONCAT(a.name,if(a.freifeld1!='',CONCAT(' (',a.freifeld1,')'),''))", "if(a.kundennummer!='',a.kundennummer,'-')", "if(a.lieferantennummer!='',a.lieferantennummer,'-')", 'a.land', 'a.plz', 'a.ort', 'a.email', 'p.abkuerzung', 'id');
        }else{
          $findcols = array('a.name', "if(a.kundennummer!='',a.kundennummer,'-')", "if(a.lieferantennummer!='',a.lieferantennummer,'-')", 'a.land', 'a.plz', 'a.ort', 'a.email', 'p.abkuerzung', 'id');
        }

        $defaultorder = 9; //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc = 1;

        $module = $this->app->Secure->GetGET("smodule"); 
        if ($this->app->erp->Firmendaten("adresse_freitext1_suche")){
          $searchsql = array('a.ort', 'a.name', 'p.abkuerzung', 'a.land', 'a.plz', 'a.email', "if(a.kundennummer!='',a.kundennummer,'-')", "if(a.lieferantennummer!='',a.lieferantennummer,'-')", 'a.ansprechpartner', 'a.freifeld1','a.freifeld2');
        }else{
          $searchsql = array('a.ort', 'a.name', 'p.abkuerzung', 'a.land', 'a.plz', 'a.email', "if(a.kundennummer!='',a.kundennummer,'-')", "if(a.lieferantennummer!='',a.lieferantennummer,'-')", 'a.ansprechpartner');
        }
        $menu = "<a href=\"index.php?module=$module&action=edit&cmd=changeinnendienst&id=$id&sid=%value%\")><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>";

        $gruppeJoin = '';
        $gruppeWhere = '';
        $gruppeBearbeiter = $this->app->erp->Firmendaten('group_employee');
        if($gruppeBearbeiter !== ''){
          $gruppeBearbeiterKennziffer = explode(' ', $gruppeBearbeiter);
          $gruppeBearbeiterKennziffer = $gruppeBearbeiterKennziffer[0];
          $gruppeBearbeiterId = $this->app->DB->Select("SELECT id FROM gruppen WHERE kennziffer = '$gruppeBearbeiterKennziffer' LIMIT 1");
          if($gruppeBearbeiterId > 0){
            $gruppeJoin = " LEFT JOIN adresse_rolle ar ON a.id = ar.adresse";

            $gruppeWhere = " AND ar.subjekt = 'Mitglied' AND ar.objekt = 'Gruppe' AND ar.parameter = '$gruppeBearbeiterId' 
                              AND ar.von <= CURDATE() AND (ar.bis = '0000-00-00' OR ar.bis >= CURDATE())";
          }
        }

        // SQL statement
          
          if ($this->app->erp->Firmendaten("adresse_freitext1_suche")) {
            $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT(a.name,if(a.freifeld1!='',CONCAT(' (',a.freifeld1,')'),'')) as name,
                  if(a.kundennummer!='',a.kundennummer,'-') as kundennummer,
                    if(a.lieferantennummer!='',a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
                      FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt ".$gruppeJoin;
          } else {
            $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.name as name,
                  if(a.kundennummer!='',a.kundennummer,'-') as kundennummer,
                    if(a.lieferantennummer!='',a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
                      FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt ".$gruppeJoin;
          }

        // fester filter
        $where = "a.geloescht=0 ".$gruppeWhere . $this->app->erp->ProjektRechte();

        $count = "SELECT COUNT(a.id) FROM adresse a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht=0 " . $this->app->erp->ProjektRechte();
        break;


      case "adressevertrieb":
        $allowed['adresse'] = array('list');
        // headings
        $heading = array('Name', 'Kunde', 'Lieferant', 'Land', 'PLZ', 'Ort', 'E-Mail', 'Projekt', 'Men&uuml;');
        $width = array('18%', '10%', '5%', '5%', '5%', '5%', '5%', '15%', '1%');
        if ($this->app->erp->Firmendaten("adresse_freitext1_suche")) {
          $findcols = array("CONCAT(a.name,if(a.freifeld1!='',CONCAT(' (',a.freifeld1,')'),''))", "if(a.kundennummer!='',a.kundennummer,'-')", "if(a.lieferantennummer!='',a.lieferantennummer,'-')", 'a.land', 'a.plz', 'a.ort', 'a.email', 'p.abkuerzung', 'id');
        }else{
          $findcols = array('a.name', "if(a.kundennummer!='',a.kundennummer,'-')", "if(a.lieferantennummer!='',a.lieferantennummer,'-')", 'a.land', 'a.plz', 'a.ort', 'a.email', 'p.abkuerzung', 'id');
        }

        $defaultorder = 9; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $module = $this->app->Secure->GetGET("smodule"); 
        if ($this->app->erp->Firmendaten("adresse_freitext1_suche")) $searchsql = array('a.ort', 'a.name', 'p.abkuerzung', 'a.land', 'a.plz', 'a.email', 'a.kundennummer', 'a.lieferantennummer', 'a.ansprechpartner', 'a.freifeld1','a.freifeld2');
        else $searchsql = array('a.ort', 'a.name', 'p.abkuerzung', 'a.land', 'a.plz', 'a.email', 'a.kundennummer', 'a.lieferantennummer', 'a.ansprechpartner');
        $menu = "<a href=\"index.php?module=$module&action=edit&cmd=change&id=$id&sid=%value%\")><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>";

        $gruppeJoin = '';
        $gruppeWhere = '';
        $gruppeVertrieb = $this->app->erp->Firmendaten('group_sales');
        if($gruppeVertrieb !== ''){
          $gruppeVertriebKennziffer = explode(' ', $gruppeVertrieb);
          $gruppeVertriebKennziffer = $gruppeVertriebKennziffer[0];
          $gruppeVertriebId = $this->app->DB->Select("SELECT id FROM gruppen WHERE kennziffer = '$gruppeVertriebKennziffer' LIMIT 1");
          if($gruppeVertriebId > 0){
            $gruppeJoin = " LEFT JOIN adresse_rolle ar ON a.id = ar.adresse";

            $gruppeWhere = " AND ar.subjekt = 'Mitglied' AND ar.objekt = 'Gruppe' AND ar.parameter = '$gruppeVertriebId' 
                              AND ar.von <= CURDATE() AND (ar.bis = '0000-00-00' OR ar.bis >= CURDATE())";
          }
        }


        // SQL statement
          
          if ($this->app->erp->Firmendaten("adresse_freitext1_suche")) {
            $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT(a.name,if(a.freifeld1!='',CONCAT(' (',a.freifeld1,')'),'')) as name,
                  if(a.kundennummer!='',a.kundennummer,'-') as kundennummer,
                    if(a.lieferantennummer!='',a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
                      FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt ".$gruppeJoin;
          } else {
            $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.name as name,
                  if(a.kundennummer!='',a.kundennummer,'-') as kundennummer,
                    if(a.lieferantennummer!='',a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
                      FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt ".$gruppeJoin;
          }

        // fester filter
        $where = "a.geloescht=0 ".$gruppeWhere . $this->app->erp->ProjektRechte();

        $count = "SELECT COUNT(a.id) FROM adresse a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht=0 " . $this->app->erp->ProjektRechte();
        break;

      case "adressetabelle":
        $allowed['adresse'] = array('list');
        // headings

        $projectsearchdisabled = (String)$this->app->erp->GetKonfiguration('adressetabelle_projectsearchdisabled');
        if($projectsearchdisabled==='') {
          $projectsearchdisabled = ($this->app->DB->Select('SELECT COUNT(id) FROM adresse') >= 100000)?1:0;
          $this->app->erp->SetKonfigurationValue('adressetabelle_projectsearchdisabled', $projectsearchdisabled);
        }
        $projectCol = 'p.abkuerzung';

        if($projectsearchdisabled) {
          $projectCol = '(SELECT abkuerzung FROM projekt WHERE id = a.projekt)';
        }
        $projectColSql = $projectCol.' AS projektabkuerung';

        if ($this->app->erp->RechteVorhanden("multilevel", "list") && $this->app->erp->Firmendaten("modul_mlm") == "1") {
          $heading = array('','Name', 'Kunde','Sponsor', 'Lieferant', 'Land', 'PLZ', 'Ort', 'E-Mail', 'Projekt');
          $width = array('1%','18%', '10%','10%', '5%', '5%', '5%', '5%', '5%', '15%');
          $findcols = array('a.id', "CONCAT(a.name,if(a.ansprechpartner!='','<br><i style=color:#999>Ansprechpartner: ',''),a.ansprechpartner,if(a.ansprechpartner!='','</i>',''))", 'a.kundennummer', 'a.sponsor','a.lieferantennummer', 'a.land', 'a.plz', 'a.ort', 'a.email', 'a.projekt');
          $defaultorder = 11; //Optional wenn andere Reihenfolge gewuenscht
          $menucol = 10;
          if ($this->app->erp->Firmendaten("adresse_freitext1_suche")) {
            $searchsql = array('a.ort', 'a.name', 'a.land', 'a.plz', 'a.email', 'a.kundennummer', 'a.lieferantennummer', 'a.ansprechpartner', 'a.freifeld1','a.ansprechpartner','a.adresszusatz','a.freifeld2');
          }
          else {
            $searchsql = array('a.ort', 'a.name', 'a.land', 'a.plz', 'a.email', 'a.kundennummer', 'a.lieferantennummer', 'a.ansprechpartner','a.ansprechpartner','a.adresszusatz');
          }
          if(!$projectsearchdisabled) {
            $searchsql[] = 'p.abkuerzung';
          }
        }
        else {
          $heading = array('','Name', 'Kunde', 'Lieferant', 'Land', 'PLZ', 'Ort', 'E-Mail', 'Projekt');
          $width = array('1%','18%', '10%', '5%', '5%', '5%', '5%', '5%', '15%');
          $findcols = array('a.id',"CONCAT(a.name,if(a.ansprechpartner!='','<br><i style=color:#999>Ansprechpartner: ',''),a.ansprechpartner,if(a.ansprechpartner!='','</i>',''))", 'a.kundennummer', 'a.lieferantennummer', 'a.land', 'a.plz', 'a.ort', 'a.email', '(SELECT abkuerzung FROM projekt WHERE id = a.projekt)');
          $defaultorder = 10; //Optional wenn andere Reihenfolge gewuenscht
          $menucol = 9;

          if ($this->app->erp->Firmendaten("adresse_freitext1_suche")){
            $searchsql = array('a.ort', 'a.name', 'a.land', 'a.plz', 'a.email', 'a.kundennummer', 'a.lieferantennummer', 'a.ansprechpartner', 'a.ansprechpartner', 'a.adresszusatz', 'a.freifeld2', 'a.telefon', 'a.freifeld1');
          }
          else{
            $searchsql = array('a.ort', 'a.name', 'a.land', 'a.plz', 'a.email', 'a.kundennummer', 'a.lieferantennummer', 'a.ansprechpartner', 'a.ansprechpartner', 'a.adresszusatz', 'a.telefon');
          }
          if(!$projectsearchdisabled) {
            $searchsql[] = 'p.abkuerzung';
          }
        }
        $zusatzfelderstr = '';
        $adressezusatzfelder = $this->app->erp->getZusatzfelderAdresse();
        $zusatzcols = null;
        $joincached = "";
        for($i = 1; $i <= 5; $i++)
        {
          $zusatzfeld = $this->app->erp->Firmendaten('adressetabellezusatz'.$i);
          if($zusatzfeld && isset($adressezusatzfelder[$zusatzfeld]))
          {
            $zusatzfeldHead = $adressezusatzfelder[$zusatzfeld];
            $typ = $this->app->erp->Firmendaten('adresse'.$zusatzfeld.'typ');

            if(strstr($zusatzfeldHead,'|')!==false && $typ==='select'){
              $zusatzfeldHead = explode('|', $zusatzfeldHead)[0];
            }

            $heading[] = $zusatzfeldHead;
            $width[] = '10%';
            $zusatzcols[] = 'a.'.$zusatzfeld;
            $findcols[] = 'a.'.$zusatzfeld;
            $zusatzfelderstr .= 'a.'.$zusatzfeld.', ';
            if(!in_array('a.'.$zusatzfeld, $searchsql))$searchsql[] = 'a.'.$zusatzfeld;
            $menucol++;
          }
        }

        $width[] = '1%';
        $heading[] = 'Men&uuml;';
        $findcols[] = 'a.id';
        $moreinfo = true;
        $moreinfoaction = "adr";

        $defaultorderdesc = 1;

        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=adresse&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"2\" data-label-reference-id=\"%value%\" data-label-reference-table=\"adresse\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";

        // SQL statement

        //if(a.typ = 'herr' OR a.typ = 'frau',CONCAT(a.vorname,' ',a.name),a.name) as name,

        $parameter = $this->app->User->GetParameter('table_filter_adresse');
        $parameter = base64_decode($parameter);
        $parameter = json_decode($parameter, true);

          if ($this->app->erp->Firmendaten("adresse_freitext1_suche")) {
            $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, 


              CONCAT(CONCAT(a.name,if(a.freifeld1!='',CONCAT(' (',a.freifeld1,')'),'')),if(a.ansprechpartner!='','<br><i style=color:#999>',''),a.ansprechpartner,if(a.ansprechpartner!='','</i>','')) as name,
                  if(a.kundennummer!='',a.kundennummer,'-') as kundennummer,
                    if(a.lieferantennummer!='',a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, $projectColSql,$zusatzfelderstr a.id as menu
                      FROM  adresse AS a ";
          }
          else {
            $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, CONCAT(a.name,if(a.ansprechpartner!='','<br><i style=color:#999>',''),a.ansprechpartner,if(a.ansprechpartner!='','</i>','')) as name,
                  if(a.kundennummer!='',a.kundennummer,'-') as kundennummer,
                    if(a.lieferantennummer!='',a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, $projectColSql,$zusatzfelderstr a.id as menu
                      FROM  adresse AS a  ";
          }

        if(!$projectsearchdisabled) {
          $sql .= ' LEFT JOIN projekt AS p ON a.projekt = p.id ';
        }

        

        /*
        if(isset($parameter['durchsuchenAnsprechpartner']) && !empty($parameter['durchsuchenAnsprechpartner'])) {
          $subJoins['durchsuchenAnsprechpartner'] = ' LEFT JOIN ansprechpartner ON ansprechpartner.adresse = adresse.id';
        }

        if(isset($parameter['durchsuchenLieferadresse']) && !empty($parameter['durchsuchenLieferadresse'])) {
          $subJoins['durchsuchenLieferadresse'] = ' LEFT JOIN lieferadressen ON lieferadressen.adresse = adresse.id';
        }
        */

        if (isset($parameter['rolle']) && !empty($parameter['rolle'])) {
          if(isset($parameter['gruppe']) &&  !empty($parameter['gruppe'])) {
            /*$sql .= "
               INNER JOIN adresse_rolle adr ON adr.adresse = a.id AND adr.subjekt LIKE '" . $parameter['rolle'] . "' AND adr.objekt LIKE 'gruppe' AND adr.parameter = '".$parameter['gruppe']."' AND (adr.bis = '0000-00-00' OR adr.bis >= curdate()) AND (adr.von = '0000-00-00' OR  adr.von <= curdate())
                  INNER JOIN gruppen gr ON adr.parameter = gr.id  AND (gr.projekt = 0 OR (1 ".$this->app->erp->ProjektRechte('gr.projekt')."))
            ";*/
            $sql .="INNER JOIN adresse_rolle adr ON adr.adresse = a.id AND adr.objekt LIKE 'Gruppe' AND adr.parameter = '".$parameter['gruppe']."' AND (adr.bis = '0000-00-00' OR adr.bis >= curdate()) AND (adr.von = '0000-00-00' OR  adr.von <= curdate())
            INNER JOIN adresse_rolle adr2 ON adr2.adresse = a.id AND adr2.subjekt LIKE '".$parameter['rolle']."' AND (adr.bis = '0000-00-00' OR adr.bis >= curdate()) AND (adr.von = '0000-00-00' OR  adr.von <= curdate())
            INNER JOIN gruppen gr ON adr.parameter = gr.id  AND (gr.projekt = 0 OR (1 ".$this->app->erp->ProjektRechte('gr.projekt')."))
            INNER JOIN gruppen gr2 ON adr2.parameter = gr2.id AND (gr2.projekt = 0 OR (1 ".$this->app->erp->ProjektRechte('gr.projekt')."))";
          }
          else {
            $sql .= "
               INNER JOIN adresse_rolle adr ON adr.adresse = a.id AND adr.subjekt LIKE '" . $parameter['rolle'] . "'  AND (adr.bis = '0000-00-00' OR adr.bis >= curdate()) AND (adr.von = '0000-00-00' OR  adr.von <= curdate())
            ";
          }
        }
        elseif(isset($parameter['gruppe']) &&  !empty($parameter['gruppe'])) {
          $sql .= "
               INNER JOIN adresse_rolle adr ON adr.adresse = a.id AND adr.objekt LIKE 'gruppe' AND adr.parameter = '".$parameter['gruppe']."'   AND (adr.bis = '0000-00-00' OR adr.bis >= curdate()) AND (adr.von = '0000-00-00' OR  adr.von <= curdate())
               INNER JOIN gruppen gr ON adr.parameter = gr.id AND (gr.projekt = 0 OR (1 ".$this->app->erp->ProjektRechte('gr.projekt')."))
            ";
        }

        /*
        if (isset($parameter['ansprechpartner']) && !empty($parameter['ansprechpartner'])) {
          $sql .= "
             LEFT JOIN ansprechpartner anp ON anp.adresse = a.id AND anp.name LIKE '%" . $parameter['ansprechpartner'] . "%'
          ";
        }
        */


        // fester filter
        $where = 'a.geloescht=0 ' . $this->app->erp->ProjektRechte('a.projekt', true, 'a.vertrieb');
        $cached_count = 'SELECT COUNT(id) FROM adresse AS a WHERE geloescht = 0 '.$this->app->erp->ProjektRechte('a.projekt', true, 'a.vertrieb');

        /* STAMMDATEN */
        if(isset($parameter['kundennummer']) && !empty($parameter['kundennummer'])) {
          $paramsArray[] = "a.kundennummer LIKE '%".$parameter['kundennummer']."%' ";
        }

        if(isset($parameter['name']) && !empty($parameter['name'])) {
          $paramsArray[] = "a.name LIKE '%".$parameter['name']."%' ";
        }

        if(isset($parameter['ansprechpartner']) && !empty($parameter['ansprechpartner'])) {
          /*
          $paramsArray[] = "
            a.ansprechpartner LIKE '%".$parameter['ansprechpartner']."%'
          ";
          */

          $paramsArray[] = "
            ( 
              a.ansprechpartner LIKE '%".$parameter['ansprechpartner']."%' 
              OR
              a.id IN
              (
                SELECT 
                  adresse 
                FROM 
                  ansprechpartner ansp 
                WHERE 
                  ansp.name LIKE '%" . $parameter['ansprechpartner'] . "%'
              )
            )
          ";
        }

        if(isset($parameter['abteilung']) && !empty($parameter['abteilung'])) {
          $paramsArray[] = "a.abteilung LIKE '%".$parameter['abteilung']."%' ";
        }
        if(isset($parameter['adresszusatz']) && !empty($parameter['adresszusatz'])) {
          $paramsArray[] = "a.adresszusatz LIKE '%".$parameter['adresszusatz']."%' ";
        }
        if(isset($parameter['abo']) && !empty($parameter['abo'])) {
          $paramsArray[] = " NOT isnull((SELECT abra.id FROM abrechnungsartikel abra WHERE abra.adresse = a.id AND (abra.enddatum = '0000-00-00' or date(now()) <= abra.enddatum )  LIMIT 1)) ";
        }
        if(isset($parameter['strasse']) && !empty($parameter['strasse'])) {
          $paramsArray[] = "a.strasse LIKE '%".$parameter['strasse']."%' ";
        }

        if(isset($parameter['plz']) && !empty($parameter['plz'])) {
          $paramsArray[] = "a.plz LIKE '".$parameter['plz']."%'";
        }

        if(isset($parameter['ort']) && !empty($parameter['ort'])) {
          $paramsArray[] = "a.ort LIKE '%".$parameter['ort']."%' ";
        }

        if(isset($parameter['land']) && !empty($parameter['land'])) {
          $paramsArray[] = "a.land LIKE '".$parameter['land']."' ";
        }

        if(isset($parameter['ustid']) && !empty($parameter['ustid'])) {
          $paramsArray[] = "a.ustid LIKE '%".$parameter['ustid']."%' ";
        }

        if(isset($parameter['telefon']) && !empty($parameter['telefon'])) {
          $paramsArray[] = "a.telefon LIKE '%".$parameter['telefon']."%' ";
        }

        if(isset($parameter['email']) && !empty($parameter['email'])) {
          $paramsArray[] = "a.email LIKE '%".$parameter['email']."%' ";
        }

        /* XXX */
        if(isset($parameter['kdnrVon']) && !empty($parameter['kdnrVon'])) {
          $paramsArray[] = "a.kundennummer >= '" . $parameter['kdnrVon'] . "'";
        }

        if(isset($parameter['kdnrBis']) && !empty($parameter['kdnrBis'])) {
          $paramsArray[] = "a.kundennummer <= '" . $parameter['kdnrBis'] . "'";
        }

        if(isset($parameter['projekt']) && !empty($parameter['projekt'])) {

          $projektData = $this->app->DB->SelectRow('
            SELECT
              *
            FROM
              projekt
            WHERE
              abkuerzung LIKE "' . $parameter['projekt'] . '"
          ');
          $paramsArray[] = "a.projekt = '".$projektData['id']."' ";
        }

        if(isset($parameter['sonstiges']) && !empty($parameter['sonstiges'])) {
          $paramsArray[] = "a.sonstiges LIKE '%".$parameter['sonstiges']."%' ";
        }

        if(isset($parameter['infoAuftragserfassung']) && !empty($parameter['infoAuftragserfassung'])) {
          $paramsArray[] = "a.infoauftragserfassung LIKE '%".$parameter['infoAuftragserfassung']."%' ";
        }

        if(isset($parameter['aktion']) && !empty($parameter['aktion'])) {
          $paramsArray[] = "a.aktion LIKE '%".$parameter['aktion']."%' ";
        }

        if(isset($parameter['freitext']) && !empty($parameter['freitext'])) {
          $paramsArray[] = "a.freitext LIKE '%".$parameter['freitext']."%' ";
        }

        if(isset($parameter['zahlungsweise']) && !empty($parameter['zahlungsweise'])) {
          $paramsArray[] = " (a.zahlungsweise LIKE '".$parameter['zahlungsweise']."' OR a.zahlungsweiselieferant  LIKE '".$parameter['zahlungsweise']."') ";
        }

        if(isset($parameter['lieferantennummer']) && !empty($parameter['lieferantennummer'])) {
          $paramsArray[] = "a.adresse LIKE '%".$parameter['lieferantennummer']."%' ";
        }

        if(isset($parameter['lieferantennummer']) && !empty($parameter['lieferantennummer'])) {
          $paramsArray[] = "a.adresse LIKE '%".$parameter['lieferantennummer']."%' ";
        }

        if(isset($parameter['vertrieb']) && !empty($parameter['vertrieb'])) {
          $paramsArray[] = "a.vertrieb = '".$parameter['vertrieb']."' ";
        }

        if(isset($parameter['innendienst']) && !empty($parameter['innendienst'])) {
          $paramsArray[] = "a.innendienst = '".$parameter['innendienst']."' ";
        }

        if(isset($parameter['mitarbeiternummer']) && !empty($parameter['mitarbeiternummer'])) {
          $paramsArray[] = "a.mitarbeiternummer LIKE '%".$parameter['mitarbeiternummer']."%' ";
        }

        if(isset($parameter['marketingsperre']) && !empty($parameter['marketingsperre'])) {
          $paramsArray[] = "a.marketingsperre = '1' ";
        }

        if(isset($parameter['lead']) && !empty($parameter['lead'])) {
          $paramsArray[] = "a.lead = '1' ";
        }



        /*
        if(isset($parameter['rolle']) && !empty($parameter['rolle'])) {
          $paramsArray[] = "a.rolle LIKE '%".$parameter['rolle']."%' ";
        }
        */

        // projekt, belegnummer, internetnummer, bestellnummer, transaktionsId, freitext, internebemerkung, aktionscodes

        if (!empty($paramsArray)) {
          $where .= ' AND ' . implode(' AND ', $paramsArray);
        }
        $groupby = ' GROUP BY a.id ';

        $count = "SELECT COUNT(a.id) FROM adresse AS a WHERE a.geloescht=0 " . $this->app->erp->ProjektRechte('a.projekt', true, 'a.vertrieb');
        break;

      case "artikeltabellehinweisausverkauft":
        $allowed['artikel'] = array('lagerlampe');

        // headings
        $heading = array('', 'Nummer', 'Artikel', 'Im Lager', 'Projekt', 'Men&uuml;');
        $width = array('5%', '10%', '45%', '8%', '15%', '10%');
        $findcols = array('nummer', 'name_de', 'projekt', 'id');
        $searchsql = array('a.name_de', 'a.nummer', 'p.abkuerzung');
        $menu = "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "<!--&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>-->";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT('<input type=\"checkbox\" name=\"artikelmarkiert[]\" value=\"',a.id,'\">') as wahl, a.nummer as nummer, a.name_de as name_de, (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lager, p.abkuerzung as projekt, a.id as menu                                                                          
              FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt ";

        // fester filter
        $where = "a.geloescht=0 AND (a.ausverkauft='1' OR a.intern_gesperrt=1) AND a.shop > 0 AND (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0";
        $count = "SELECT COUNT(a.id) FROM artikel a WHERE a.geloescht=0 AND a.shop > 0 AND (a.ausverkauft=1 OR a.intern_gesperrt=1) AND (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0";
        break;
      case "artikeltabellelagerndabernichtlagernd":
        $allowed['artikel'] = array('lagerlampe');

        // headings
        $heading = array('', 'Nummer', 'Artikel', 'Im Lager', 'Projekt', 'Men&uuml;');
        $width = array('5%', '10%', '45%', '8%', '15%', '10%');
        $findcols = array('nummer', 'name_de', 'projekt', 'id');
        $searchsql = array('a.name_de', 'a.nummer', 'p.abkuerzung');
        $menu = "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "<!--&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>-->";
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT('<input type=\"checkbox\" name=\"artikelmarkiert[]\" value=\"',a.id,'\">') as wahl, a.nummer as nummer, a.name_de as name_de, (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lager, p.abkuerzung as projekt, a.id as menu                                                                          
              FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt ";
        $where = "a.geloescht=0 AND (a.lieferzeit='lager' || a.lieferzeit='') AND a.lagerartikel=1  AND (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) IS NULL 
              AND a.shop!=0 AND a.intern_gesperrt=0 AND a.ausverkauft!='1' AND a.inaktiv!='1'";
        $count = "SELECT COUNT(a.id) FROM artikel a WHERE a.geloescht=0 AND (a.lieferzeit='lager' || a.lieferzeit='') AND (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) IS NULL 
              AND a.shop!=0 AND a.intern_gesperrt=0 AND a.ausverkauft!='1' AND a.inaktiv!='1'";
        break;


      case "anfrage":
        $tmp = '';
        // START EXTRA checkboxen
        $heading = array('Anfrage','Vom', 'Mitarbeiter', 'Kd-Nr.', 'Kunde', 'Projekt', 'Status', 'Men&uuml;');
        $width = array('5%','10%', '15%', '5%', '30%', '15%', '10%', '5%');
        $findcols = array('belegnr','l.datum', 'adr.name', 'adr2.kundennummer', "CONCAT(l.name,if(l.internebezeichnung!='',CONCAT('<br><i style=color:#999>',l.internebezeichnung,'</i>'),''))", 'p.abkuerzung', 'l.status', 'id');
        $searchsql = array('l.belegnr', "DATE_FORMAT(l.datum, '%d.%m.%Y')", 'adr.name', 'adr2.kundennummer', 'l.name', 'p.abkuerzung', 'l.status', 'l.id','l.internebezeichnung');
        $defaultorder = 8; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=anfrage&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=anfrage&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=CopyDialog(\"index.php?module=anfrage&action=copy&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=anfrage&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"5\" data-label-reference-id=\"%value%\" data-label-reference-table=\"anfrage\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, if(l.status='angelegt','ENTWURF',l.belegnr),DATE_FORMAT(l.datum,'%d.%m.%Y') as vom, 
              adr.name as mitarbeiter,
              adr2.kundennummer,
              CONCAT(l.name,if(l.internebezeichnung!='',CONCAT('<br><i style=color:#999>',l.internebezeichnung,'</i>'),'')) as name,
              p.abkuerzung as projekt,
              UPPER(l.status) as status, l.id
                FROM  anfrage l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.bearbeiterid=adr.id 
                LEFT JOIN adresse adr2 ON l.adresse=adr2.id
                LEFT JOIN adresse ma ON ma.id=l.mitarbeiter ";

        // Fester filter
        
          $where = " l.id!='' $tmp " . $this->app->erp->ProjektRechte();
          $count = "SELECT COUNT(l.id) FROM anfrage l";


        // gesamt anzahl
        
        //$moreinfo = true;

        break;
      case "anfrageinbearbeitung":

        // START EXTRA checkboxen
        $heading = array('Vom', 'Mitarbeiter', 'Kd-Nr.', 'Kunde', 'Projekt', 'Status', 'Men&uuml;');
        $width = array('15%', '15%', '5%', '30%', '15%', '10%', '10%');
        $findcols = array('vom', 'mitarbeiter', 'kundennummer', 'name', 'projekt', 'status', 'id');
        $searchsql = array('l.datum', 'adr.name', 'adr2.kundennummer', 'l.name', 'p.abkuerzung', 'l.status', 'l.id');
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=anfrage&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=anfrage&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"index.php?module=anfrage&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, DATE_FORMAT(l.datum,'%d.%m.%Y') as vom, 
              adr.name as mitarbeiter,
              adr2.kundennummer,
              l.name,
              p.abkuerzung as projekt,
              UPPER(l.status) as status, l.id
                FROM  anfrage l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.bearbeiterid=adr.id 
                LEFT JOIN adresse adr2 ON l.adresse=adr2.id
                LEFT JOIN adresse ma ON ma.id=l.mitarbeiter ";

        // Fester filter
        
        if ($this->app->User->GetType() != "admin") {
          
          if ($this->app->User->GetProjektleiter()) {

            // normaler angestellter
            $where = " l.status='angelegt' AND l.id!='' $tmp " . $this->app->erp->ProjektleiterRechte();
            $count = "SELECT COUNT(l.id) FROM anfrage l WHERE  l.id!='' " . $this->app->erp->ProjektleiterRechte();
          } else {

            // normaler angestellter
            $where = " l.id!='' AND l.status='angelegt' AND l.bearbeiterid='" . $this->app->User->GetAdresse() . "'  
                  $tmp " . $this->app->erp->ProjektRechte();
            $count = "SELECT COUNT(l.id) FROM anfrage l WHERE 
                  l.bearbeiterid='" . $this->app->User->GetAdresse() . "' AND l.status='angelegt'";
          }
        } else {
          $where = "l.status='angelegt' AND l.id!='' $tmp " . $this->app->erp->ProjektRechte();
          $count = "SELECT COUNT(l.id) FROM anfrage l WHERE l.status='angelegt'";
        }

        // gesamt anzahl
        
        //$moreinfo = true;

        break;
    case "proformarechnung":

        // START EXTRA checkboxen
        $heading = array('','Belegnr','Vom', 'Kd-Nr.', 'Rechnungsadresse','Lieferadresse','Lieferbedingung','Betrag (brutto)', 'Projekt', 'Status', 'Men&uuml;');
        $width = array('1%','5%', '10%', '5%', '20%', '20%','10%','5%','10%', '5%', '5%');
        $findcols = array('open','l.belegnr','l.datum', 'adr2.kundennummer', 'l.name','l.liefername','l.lieferbedingung','l.soll', 'p.abkuerzung', 'l.status', 'l.id');
        $searchsql = array('l.belegnr','l.datum', 'adr2.kundennummer', 'l.name','l.liefername','l.lieferbedingung',"l.soll",'p.abkuerzung', 'l.status', 'l.internebezeichnung','l.freitext');
        $defaultorder = 11; //Optional wenn andere Reihenfolge gewuenscht

        $menucol = 10;
        $alignright=array(8);
        $moreinfo=true;
        $sumcol = 8;

        $defaultorderdesc = 1;
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=proformarechnung&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=proformarechnung&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=CopyDialog(\"index.php?module=proformarechnung&action=copy&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=proformarechnung&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, l.belegnr,DATE_FORMAT(l.datum,'%d.%m.%Y') as vom, 
              adr2.kundennummer,
              CONCAT(l.name,if(l.internebezeichnung!='',CONCAT('<br><i style=color:#999>',l.internebezeichnung,'</i>'),'')) as name, if(l.abweichendelieferadresse,CONCAT(l.liefername),'-'), l.lieferbedingung,FORMAT(l.soll,2,'de_DE'),
              p.abkuerzung as projekt,
              UPPER(l.status) as status, l.id
                FROM  proformarechnung l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.bearbeiterid=adr.id 
                LEFT JOIN adresse adr2 ON l.adresse=adr2.id";


        // Fester filter
        
        if ($this->app->User->GetType() != "admin") {
          
          if ($this->app->User->GetProjektleiter()) {

            // normaler angestellter
            $where = " l.id!='' $tmp " . $this->app->erp->ProjektleiterRechte();
            $count = "SELECT COUNT(l.id) FROM proformarechnung l WHERE  l.id!='' " . $this->app->erp->ProjektleiterRechte();
          } else {

            // normaler angestellter
            $where = " l.id!='' AND l.status!='abgerechnet' $tmp " . $this->app->erp->ProjektRechte();
            $count = "SELECT COUNT(l.id) FROM proformarechnung l WHERE 
                  l.bearbeiterid='" . $this->app->User->GetAdresse() . "' AND l.status!='abgerechnet'";
          }
        } else {
          $where = " l.id!='' $tmp " . $this->app->erp->ProjektRechte();
          $count = "SELECT COUNT(l.id) FROM proformarechnung l";
        }

        // gesamt anzahl
        
        //$moreinfo = true;

        break;

  case "preisanfrage":

        // START EXTRA checkboxen
        $heading = array('Belegnr','Vom', 'Mitarbeiter', 'Lf-Nr.', 'Lieferant', 'Projekt', 'Status', 'Men&uuml;');
        $width = array('10%','15%', '15%', '5%', '30%', '15%', '10%', '5%');
        $findcols = array('l.belegnr','l.datum', 'adr.name', 'adr2.lieferantennummer', 'l.name', 'p.abkuerzung', 'l.status', 'l.id');
        $searchsql = array('l.belegnr',"DATE_FORMAT(l.datum,'%d.%m.%Y')", 'adr.name', 'adr2.lieferantennummer', 'l.name', 'p.abkuerzung', 'l.status', 'l.id','l.internebezeichnung');
        $defaultorder = 7; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=preisanfrage&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=preisanfrage&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=CopyDialog(\"index.php?module=preisanfrage&action=copy&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=preisanfrage&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"1\" data-label-reference-id=\"%value%\" data-label-reference-table=\"preisanfrage\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, l.belegnr,DATE_FORMAT(l.datum,'%d.%m.%Y') as vom, 
              adr.name as mitarbeiter,
              adr2.lieferantennummer,
              CONCAT(l.name,if(l.internebezeichnung!='',CONCAT('<br><i style=color:#999>',l.internebezeichnung,'</i>'),'')) as name,
              p.abkuerzung as projekt,
              UPPER(l.status) as status, l.id
                FROM  preisanfrage l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.bearbeiterid=adr.id 
                LEFT JOIN adresse adr2 ON l.adresse=adr2.id
                LEFT JOIN adresse ma ON ma.id=l.mitarbeiter ";

        // Fester filter
        
          $where = " l.id!='' " . $this->app->erp->ProjektRechte();
          $count = "SELECT COUNT(l.id) FROM preisanfrage l LEFT JOIN projekt p ON p.id=l.projekt  WHERE l.id > 0 ".$this->app->erp->ProjektRechte();

        // gesamt anzahl
        
        //$moreinfo = true;

        break;
      case "preisanfrageinbearbeitung":

        // START EXTRA checkboxen
        $heading = array('Vom', 'Mitarbeiter', 'Kd-Nr.', 'Kunde', 'Projekt', 'Status', 'Men&uuml;');
        $width = array('15%', '15%', '5%', '30%', '15%', '10%', '10%');
        $findcols = array('vom', 'mitarbeiter', 'kundennummer', 'name', 'projekt', 'status', 'id');
        $searchsql = array('l.datum', 'adr.name', 'adr2.kundennummer', 'l.name', 'p.abkuerzung', 'l.status', 'l.id');
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=preisanfrage&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=preisanfrage&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"index.php?module=preisanfrage&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, DATE_FORMAT(l.datum,'%d.%m.%Y') as vom, 
              adr.name as mitarbeiter,
              adr2.kundennummer,
              l.name,
              p.abkuerzung as projekt,
              UPPER(l.status) as status, l.id
                FROM  preisanfrage l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.bearbeiterid=adr.id 
                LEFT JOIN adresse adr2 ON l.adresse=adr2.id
                LEFT JOIN adresse ma ON ma.id=l.mitarbeiter ";

        // Fester filter
        
        if ($this->app->User->GetType() != "admin") {
          
          if ($this->app->User->GetProjektleiter()) {

            // normaler angestellter
            $where = " l.status='angelegt' AND l.id!='' $tmp " . $this->app->erp->ProjektleiterRechte();
            $count = "SELECT COUNT(l.id) FROM preisanfrage l WHERE  l.id!='' " . $this->app->erp->ProjektleiterRechte();
          } else {

            // normaler angestellter
            $where = " l.id!='' AND l.status='angelegt' AND l.bearbeiterid='" . $this->app->User->GetAdresse() . "'  
                  $tmp " . $this->app->erp->ProjektRechte();
            $count = "SELECT COUNT(l.id) FROM preisanfrage l WHERE 
                  l.bearbeiterid='" . $this->app->User->GetAdresse() . "' AND l.status='angelegt'";
          }
        } else {
          $where = "l.status='angelegt' AND l.id!='' $tmp " . $this->app->erp->ProjektRechte();
          $count = "SELECT COUNT(l.id) FROM preisanfrage l WHERE l.status='angelegt'";
        }

        // gesamt anzahl
        
        //$moreinfo = true;

        break;
    case "proformarechnunginbearbeitung":

        // START EXTRA checkboxen
        $heading = array('Vom', 'Mitarbeiter', 'Kd-Nr.', 'Kunde', 'Projekt', 'Status', 'Men&uuml;');
        $width = array('15%', '15%', '5%', '30%', '15%', '10%', '10%');
        $findcols = array('vom', 'mitarbeiter', 'kundennummer', 'name', 'projekt', 'status', 'id');
        $searchsql = array('l.datum', 'adr.name', 'adr2.kundennummer', 'l.name', 'p.abkuerzung', 'l.status', 'l.id');
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=proformarechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=proformarechnung&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"index.php?module=proformarechnung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, DATE_FORMAT(l.datum,'%d.%m.%Y') as vom, 
              adr.name as mitarbeiter,
              adr2.kundennummer,
              l.name,
              p.abkuerzung as projekt,
              UPPER(l.status) as status, l.id
                FROM  proformarechnung l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.bearbeiterid=adr.id 
                LEFT JOIN adresse adr2 ON l.adresse=adr2.id
                LEFT JOIN adresse ma ON ma.id=l.mitarbeiter ";

        // Fester filter
        
        if ($this->app->User->GetType() != "admin") {
          
          if ($this->app->User->GetProjektleiter()) {

            // normaler angestellter
            $where = " l.status='angelegt' AND l.id!='' $tmp " . $this->app->erp->ProjektleiterRechte();
            $count = "SELECT COUNT(l.id) FROM proformarechnung l WHERE  l.id!='' " . $this->app->erp->ProjektleiterRechte();
          } else {

            // normaler angestellter
            $where = " l.id!='' AND l.status='angelegt' AND l.bearbeiterid='" . $this->app->User->GetAdresse() . "'  
                  $tmp " . $this->app->erp->ProjektRechte();
            $count = "SELECT COUNT(l.id) FROM proformarechnung l WHERE 
                  l.bearbeiterid='" . $this->app->User->GetAdresse() . "' AND l.status='angelegt'";
          }
        } else {
          $where = "l.status='angelegt' AND l.id!='' $tmp " . $this->app->erp->ProjektRechte();
          $count = "SELECT COUNT(l.id) FROM proformarechnung l WHERE l.status='angelegt'";
        }

        // gesamt anzahl
        
        //$moreinfo = true;

        break;
      case "lieferscheine":

        $useProjectAb = $this->app->erp->ModulVorhanden('batches');
        $projectCol = 'p.abkuerzung';
        $prAbJoin = '';
        if($useProjectAb) {
          $projectCol = 'IFNULL(pab.abkuerzung ,p.abkuerzung)';
          $prJoin = ' LEFT JOIN projekt AS pab ON ab.projekt = pab.id ';
          $prAbJoin = ' LEFT JOIN auftrag AS ab ON l.auftragid = ab.id
          LEFT JOIN projekt AS pab ON ab.projekt = pab.id ';
        }
        $allowed['lieferschein'] = array('list');

        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#lieferscheinoffen').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#lieferscheinheute').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#anlieferanten').click( function() { fnFilterColumn3( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#ohne_rechnung').click( function() { fnFilterColumn4( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#nichtausgelagert').click( function() { fnFilterColumn5( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#manuellabgeschlossen').click( function() { fnFilterColumn6( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#abgeschlossenlogistik').click( function() { fnFilterColumn7( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#nochinlogistik').click( function() { fnFilterColumn8( 0 ); } );");
        $rowcallback_gt = 1;
        $defaultorder = 12; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        for ($r = 1;$r < 9;$r++) {
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

        // ENDE EXTRA checkboxen

        $zusatzcols = array();
        $lieferscheinzusatzfelder = $this->app->erp->getZusatzfelderLieferschein();
        
        // headings

        $heading = array('','', 'Lieferschein', 'Vom', 'Kd-Nr./Lf-Nr.', 'Kunde/Lieferant', 'Land', 'Projekt', 'Versand', 'Art', 'Status');
        $width = array('1%','1%', '10%', '10%', '10%', '35%', '5%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open','l.id', 'l.belegnr', 'l.datum', 'if(l.lieferantenretoure=1,lfr.lieferantennummer,adr.kundennummer)', 'l.name', 'l.land', $projectCol, 'l.versandart', 'l.lieferscheinart', 'l.status');
        $searchsql = array('l.id', 'DATE_FORMAT(l.datum,\'%d.%m.%Y\')', 'l.belegnr', 'if(l.lieferantenretoure=1,lfr.lieferantennummer,adr.kundennummer)', 'l.name', 'l.land', $projectCol, 'l.status', 'l.plz', 'l.id', 'adr.freifeld1', 'l.ihrebestellnummer','l.internebezeichnung','l.versandart');
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=lieferschein&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialogLieferschein(\"%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=lieferschein&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>" .

        //             "&nbsp;<a href=\"index.php?module=paketmarke&action=create&frame=false&sid=lieferschein&id=%value%\" class=\"popup\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/stamp.png\" border=\"0\"></a>".
        "&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>".
        "&nbsp;<a href=\"#\" class=\"label-manager\" data-label-column-number=\"6\" data-label-reference-id=\"%value%\" data-label-reference-table=\"lieferschein\"><span class=\"label-manager-icon\"></span></a>"."</td></tr></table>";
        $menucol = 11;

        for($i = 1; $i <= 5; $i++) {
          $zusatzfeld = $this->app->erp->Firmendaten('lieferscheintabellezusatz' . $i);
          if($zusatzfeld && isset($lieferscheinzusatzfelder[$zusatzfeld])){
            $defaultorder++;
            $menucol++;
            $heading[] = $lieferscheinzusatzfelder[$zusatzfeld];
            $width[] = '10%';
            switch($zusatzfeld)
            {
              case 'internet':
                $searchsql[] = "auf.internet";
                $zusatzcols[] = "auf.internet";
                $findcols[] = 'auf.internet';
                break;

              case 'tatsaechlicheslieferdatum':
              case 'lieferdatum':
                $searchsql[] = 'date_format(a.'.$zusatzfeld.",'%d.%m.%Y')";
                $zusatzcols[] = 'date_format(a.'.$zusatzfeld.",'%d.%m.%Y')";
                $findcols[] = 'a.'.$zusatzfeld;

                break;
              default:
                $searchsql[] = 'l.'.$zusatzfeld;
                $zusatzcols[] = 'l.'.$zusatzfeld;
                $findcols[] = 'l.'.$zusatzfeld;
            }
          }
        }


        $width[] = '1%';
        $findcols[] = 'a.id';
        $heading[] = 'Men&uuml;';

        $parameter = $this->app->User->GetParameter('table_filter_lieferschein');
        $parameter = base64_decode($parameter);
        $parameter = json_decode($parameter, true);
        $groupby = 'group by l.id, adr.id, auf.id, p.id';
        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,
        concat('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',l.id,'\" />'), l.belegnr, DATE_FORMAT(l.datum,'%d.%m.%Y') as vom, if(l.lieferantenretoure=1,lfr.lieferantennummer,adr.kundennummer) as kundennummer,
          CONCAT(" . $this->app->erp->MarkerUseredit("l.name", "l.useredittimestamp") . ", if(l.internebezeichnung!='',CONCAT('<br><i style=color:#999>',l.internebezeichnung,'</i>'),'')) as kunde,
              l.land as land, $projectCol as projekt, l.versandart as versandart,  
              l.lieferscheinart as art, UPPER(l.status) as status, ".(!empty($zusatzcols)?implode(', ',$zusatzcols).',':'')." l.id
                FROM  
                  lieferschein l 
                  LEFT JOIN projekt p ON p.id=l.projekt 
                  LEFT JOIN adresse adr ON l.adresse=adr.id
                  LEFT JOIN adresse lfr ON l.lieferant=lfr.id
                  LEFT JOIN auftrag auf ON l.auftragid = auf.id
         ";
         
        $isapjoin = false;
        if(isset($parameter['artikel']) && !empty($parameter['artikel'])) {
          $artikelid = $this->app->DB->Select("SELECT id FROM artikel where geloescht != 1 AND nummer != 'DEL' AND nummer != '' AND nummer = '".$this->app->DB->real_escape_string(reset(explode(' ',trim($parameter['artikel']))))."' LIMIT 1");
          if($artikelid)
          {
            $paramsArray[] = "ap.artikel = '" . $artikelid . "' ";
            $sql .= " INNER JOIN lieferschein_position ap ON l.id = ap.lieferschein ";
            $isapjoin = true;
          }
        }
        // Fester filter
        $more_data6 = $this->app->Secure->GetGET("more_data6");
        $more_data7 = $this->app->Secure->GetGET("more_data7");
        $more_data8 = $this->app->Secure->GetGET("more_data8");
        
        $more_data4 = $this->app->Secure->GetGET("more_data4");

        $versandjoin = "";
        if(isset($parameter['offenversandzentrum'])  && !empty($parameter['offenversandzentrum']))
        {
          $versandjoin = "  INNER JOIN versand v ON l.id = v.lieferschein 
          AND v.abgeschlossen!='1' AND (v.tracking <> '' OR v.weitererlieferschein <> 1) AND v.cronjob = 0 AND l.status!='storniert' ";          
        }else{  
          if(isset($parameter['ohnetracking']) && !empty($parameter['ohnetracking'])) {
            if($more_data6)
            {
              $paramsArray[] = " isnull(v.id) ";
              $paramsArray[] = " l.status='versendet' ";
              $versandjoin = "  LEFT JOIN versand v ON l.id = v.lieferschein";
            }else{
              $paramsArray[] = " isnull(v.id) ";
              $versandjoin = "  LEFT JOIN versand v ON l.id = v.lieferschein AND v.tracking <> '' ";            
            }
          }elseif($more_data6)
          {
            $paramsArray[] = " isnull(v.id) ";
            $paramsArray[] = " l.status='versendet' ";
            $versandjoin = "  LEFT JOIN versand v ON l.id = v.lieferschein";
          }
        }
        if($more_data8)
        {
          $versandjoin .= "  INNER JOIN versand v2 ON l.id = v2.lieferschein AND v2.abgeschlossen = 0";
        }
        elseif($more_data7)
        {
          $versandjoin .= "  INNER JOIN versand v2 ON l.id = v2.lieferschein";
        }
        
        if($versandjoin)$sql .= $versandjoin;
        
        
        if($more_data4 || (isset($parameter['ohnerechnung']) && !empty($parameter['ohnerechnung']))) {

            $paramsArray[] = " l.status !='storniert' ";
            $paramsArray[] = " isnull(re.id) AND isnull(re2.id) ";
            $paramsArray[] = " l.keinerechnung = 0 ";
            $sql .= "  
            LEFT JOIN auftrag ab ON l.auftragid = ab.id
            LEFT JOIN rechnung re ON l.id = re.lieferschein
            LEFT JOIN rechnung re2 ON ab.id = re2.auftragid ";
            if(!empty($prAbJoin)) {
              $prAbJoin = $prJoin;
            }
            if($this->app->erp->ModulVorhanden('sammelrechnung'))
            {
              $check = $this->app->DB->Select("SELECT id FROM sammelrechnung_position LIMIT 1");
              if(!$this->app->DB->error())
              {
                $sql .= " LEFT JOIN (
                SELECT max(sp.id) as spid, lsp.lieferschein
                FROM lieferschein_position lsp 
                INNER JOIN sammelrechnung_position sp ON lsp.id = sp.lieferschein_position_id AND sp.rechnung != 0 
                GROUP BY lsp.lieferschein
                ) sammp ON l.id = sammp.lieferschein";
                $paramsArray[] = " isnull(sammp.spid) ";
              }
            }
            if($this->app->erp->ModulVorhanden('gruppenrechnung')){
              $check = $this->app->DB->Select("SELECT id FROM gruppenrechnung_position LIMIT 1");
              if(!$this->app->DB->error())
              {
                $sql .= " LEFT JOIN (
                SELECT max(gp.id) as gpid, lsp2.lieferschein
                FROM lieferschein_position lsp2 
                INNER JOIN gruppenrechnung_position gp ON lsp2.id = gp.lieferschein_position_id
                GROUP BY lsp2.lieferschein
                ) grp ON l.id = grp.lieferschein";
                $paramsArray[] = " isnull(grp.gpid) ";
              }
            }
        }
        
        $more_data5 = $this->app->Secure->GetGET("more_data5");
        if($more_data5)
        {
          if(!$isapjoin)
          {
            $sql .= " LEFT JOIN lieferschein_position ap ON l.id = ap.lieferschein ";
          }
          //06.04.18 Lisa: von > 0 zu <= 0 angepasst
          $groupby .= " HAVING sum(ap.geliefert) <= 0 ";
          
          $sql .= " LEFT JOIN versand v ON l.id = v.lieferschein ";
          $paramsArray[] = "v.id IS NULL";

          //#620035
          $sql .= " INNER JOIN artikel a ON a.id = ap.artikel ";
          $groupby .= "AND MAX(a.lagerartikel)>0 ";
          $paramsArray[] = "l.status !='storniert'";
        }
        
        
        // START EXTRA more
        $subwhere = [];
        $more_data1 = $this->app->Secure->GetGET("more_data1");
        
        if ($more_data1 == 1) $subwhere[] = " l.status='freigegeben' ";
        $more_data2 = $this->app->Secure->GetGET("more_data2");
        
        if ($more_data2 == 1) $subwhere[] = " l.datum=CURDATE() ";
        $more_data3 = $this->app->Secure->GetGET("more_data3");
        
        if ($more_data3 == 1) $subwhere[] = " l.lieferantenretoure=1 ";

        // ENDE EXTRA more
        for ($j = 0;$j < count($subwhere);$j++) $tmp.= " AND " . $subwhere[$j];
        $where = " l.id!='' AND l.status!='angelegt' $tmp " . $this->app->erp->ProjektRechte('p.id', true, 'l.vertriebid');

        /* STAMMDATEN */
        if(isset($parameter['kundennummer']) && !empty($parameter['kundennummer'])) {
          $paramsArray[] = "
            ( l.kundennummer LIKE '%".$parameter['kundennummer']."%' OR auf.kundennummer LIKE '%".$parameter['kundennummer']."%' OR adr.kundennummer LIKE '%".$parameter['kundennummer']."%')
          ";
        }

        if(isset($parameter['name']) && !empty($parameter['name'])) {
          $paramsArray[] = "
            ( l.name LIKE '%".$parameter['name']."%' OR auf.name LIKE '%".$parameter['name']."%' )
          ";
        }

        if(isset($parameter['ansprechpartner']) && !empty($parameter['ansprechpartner'])) {
          $paramsArray[] = "
            ( l.ansprechpartner LIKE '%".$parameter['ansprechpartner']."%' OR auf.ansprechpartner LIKE '%".$parameter['ansprechpartner']."%' )
          ";
        }

        if(isset($parameter['abteilung']) && !empty($parameter['abteilung'])) {
          $paramsArray[] = "
            ( l.abteilung LIKE '%".$parameter['abteilung']."%' OR auf.abteilung LIKE '%".$parameter['abteilung']."%' )
          ";
        }

        if(isset($parameter['strasse']) && !empty($parameter['strasse'])) {
          $paramsArray[] = "
            ( l.strasse LIKE '%".$parameter['strasse']."%' OR auf.strasse LIKE '%".$parameter['strasse']."%' )
          ";
        }

        if(isset($parameter['plz']) && !empty($parameter['plz'])) {
          $paramsArray[] = "
            ( l.plz LIKE '".$parameter['plz']."%' OR auf.plz LIKE '".$parameter['plz']."%' )
          ";
        }

        if(isset($parameter['ort']) && !empty($parameter['ort'])) {
          $paramsArray[] = "
            ( l.ort LIKE '%".$parameter['ort']."%' OR auf.ort LIKE '%".$parameter['ort']."%' )
          ";
        }

        if(isset($parameter['land']) && !empty($parameter['land'])) {
          $paramsArray[] = "
            ( l.land LIKE '%".$parameter['land']."%' OR auf.land LIKE '%".$parameter['land']."%' )
          ";
        }

        if(isset($parameter['ustid']) && !empty($parameter['ustid'])) {
          $paramsArray[] = "
            ( l.ustid LIKE '%".$parameter['ustid']."%' OR auf.ustid LIKE '%".$parameter['ustid']."%' )
          ";
        }

        if(isset($parameter['telefon']) && !empty($parameter['telefon'])) {
          $paramsArray[] = "
            ( l.telefon LIKE '%".$parameter['telefon']."%' OR auf.telefon LIKE '%".$parameter['telefon']."%' )
          ";
        }

        /* XXX */
        if(isset($parameter['datumVon']) && !empty($parameter['datumVon'])) {
          $paramsArray[] = "l.datum >= '" . date('Y-m-d',strtotime($parameter['datumVon']))."' ";
        }

        if(isset($parameter['datumBis']) && !empty($parameter['datumBis'])) {
          $paramsArray[] = "l.datum <= '" . date('Y-m-d',strtotime($parameter['datumBis']))."' ";
        }

        if(isset($parameter['projekt']) && !empty($parameter['projekt'])) {

          $projektData = $this->app->DB->SelectArr('
            SELECT
              *
            FROM
              projekt
            WHERE
              abkuerzung LIKE "' . $parameter['projekt'] . '"
          ');
          $projektData = reset($projektData);
          $paramsArray[] = "l.projekt = '".$projektData['id']."' ";
        }

        if(isset($parameter['belegnummer']) && !empty($parameter['belegnummer'])) {
          $paramsArray[] = "l.belegnr LIKE '".$parameter['belegnummer']."' ";
        }

        if(isset($parameter['internebemerkung']) && !empty($parameter['internebemerkung'])) {
          $paramsArray[] = "l.internebemerkung LIKE '%".$parameter['internebemerkung']."%' ";
        }

        if(isset($parameter['aktion']) && !empty($parameter['aktion'])) {
          $paramsArray[] = "l.aktion LIKE '%".$parameter['aktion']."%' ";
        }

        if(isset($parameter['freitext']) && !empty($parameter['freitext'])) {
          $paramsArray[] = "l.freitext LIKE '%".$parameter['freitext']."%' ";
        }

        if(isset($parameter['status']) && !empty($parameter['status'])) {
          $paramsArray[] = "l.status LIKE '%".$parameter['status']."%' ";
        }

        if(isset($parameter['versandart']) && !empty($parameter['versandart'])) {
          $paramsArray[] = "l.versandart LIKE '%".$parameter['versandart']."%' ";
        }

        // projekt, belegnummer, internetnummer, bestellnummer, transaktionsId, freitext, internebemerkung, aktionscodes

        if ($paramsArray) {
          $where .= ' AND ' . implode(' AND ', $paramsArray);
        }

        if(!empty($prAbJoin)) {
          $sql .= $prAbJoin;
        }

        // gesamt anzahl
        $count = "SELECT COUNT(l.id) FROM lieferschein l WHERE  l.status!='angelegt' ";
        $moreinfo = true;
        break;
      case "gutschrifteninbearbeitung":
        $allowed['gutschrift'] = array('list');
        $heading = array('', 'Gutschrift', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Zahlweise', 'Betrag (brutto)', 'bezahlt', 'Status', 'Men&uuml;');
        $width = array('1%', '10%', '10%', '10%', '35%', '5%', '1%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open', 'belegnr', 'r.datum', 'adr.kundennummer', 'r.name', 'r.land', 'p.abkuerzung', 'r.zahlungsweise', 'r.soll', 'r.zahlungsstatus', 'r.status', 'id');
        $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')', 'r.belegnr', 'adr.kundennummer', 'r.name', 'r.land', 'p.abkuerzung', 'r.zahlungsweise', 'r.status', "FORMAT(r.soll,2{$extended_mysql55})", 'r.zahlungsstatus', 'adr.freifeld1', 'r.ihrebestellnummer','r.internebezeichnung');
        $defaultorder = 12; //Optional wenn andere Reihenfolge gewuenscht

        $alignright = array('9');

        if($this->app->erp->RechteVorhanden('gutschrift','summe'))
          $sumcol = 9;

        $defaultorderdesc = 1;
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=gutschrift&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=gutschrift&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=gutschrift&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"5\" data-label-reference-id=\"%value%\" data-label-reference-table=\"gutschrifteninbearbeitung\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";
        $menucol = 11;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, 
              'ENTWURF' as belegnr,
              DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, adr.kundennummer as kundennummer,
          CONCAT(" . $this->app->erp->MarkerUseredit("r.name", "r.useredittimestamp") . ", if(r.internebezeichnung!='',CONCAT('<br><i style=color:#999>',r.internebezeichnung,'</i>'),'')) as kunde,
              r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
              FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                FROM  gutschrift r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";

        // Fester filter
        $where = " ( r.status='angelegt') " . $this->app->erp->ProjektRechte('p.id', true, 'r.vertriebid');

        // gesamt anzahl
        $count = "SELECT COUNT(r.id) FROM gutschrift r WHERE ( r.status='angelegt') ";
        $moreinfo = true;
        break;
      case "gutschriftenoffene":
        $allowed['gutschrift'] = array('list');
        $heading = array('', 'Gutschrift', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Zahlweise', 'Betrag (brutto)', 'bezahlt', 'Status', 'Men&uuml;');
        $width = array('1%', '10%', '10%', '10%', '35%', '5%', '1%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open', 'belegnr', 'r.datum', 'adr.kundennummer', 'r.name', 'r.land', 'p.abkuerzung', 'r.zahlungsweise', 'r.soll', 'r.zahlungsstatus', 'r.status', 'id');
        $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')', 'r.belegnr', 'adr.kundennummer', 'r.name', 'r.land', 'p.abkuerzung', 'r.zahlungsweise', 'r.status', "FORMAT(r.soll,2{$extended_mysql55})", 'r.zahlungsstatus', 'adr.freifeld1', 'r.ihrebestellnummer','r.internebezeichnung');
        $defaultorder = 12; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $alignright = array('9');

        if($this->app->erp->RechteVorhanden('gutschrift','summe'))
          $sumcol = 9;
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=gutschrift&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=gutschrift&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=gutschrift&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"5\" data-label-reference-id=\"%value%\" data-label-reference-table=\"gutschriftenoffene\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";
        $menucol = 11;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, 
              r.belegnr,
              DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, adr.kundennummer as kundennummer,
          CONCAT(" . $this->app->erp->MarkerUseredit("r.name", "r.useredittimestamp") . ", if(r.internebezeichnung!='',CONCAT('<br><i style=color:#999>',r.internebezeichnung,'</i>'),'')) as kunde,
r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
              FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                FROM  gutschrift r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";

        // Fester filter
        $where = " r.id!='' AND r.status='freigegeben' " . $this->app->erp->ProjektRechte('p.id',true,'r.vertriebid');

        // gesamt anzahl
        $count = "SELECT COUNT(r.id) FROM gutschrift r";
        $moreinfo = true;
        break;
      case "gutschriften":
        $allowed['gutschrift'] = array('list');
        $rowcallback_gt = 1;

        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#gutschriftoffen').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#gutschriftheute').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#gutschriftnichterledigt').click( function() { fnFilterColumn3( 0 ); } );");
        for ($r = 1;$r < 4;$r++) {
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



        $heading = array('', '', 'Gutschrift', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt',
          'Zahlweise', 'Betrag (brutto)', 'bezahlt','RE-Nr.', 'Status','Monitor' ,'Men&uuml;'
        );

        $width = array('1%', '1%', '10%', '10%', '10%', '25%', '5%', '1%', '1%', '1%', '1%', '1%','5%', '1%','1%', '1%');
        $findcols = array('open', 'r.belegnr', 'r.belegnr', 'r.datum', 'adr.kundennummer', 'r.name', 'r.land', 'p.abkuerzung', 'r.zahlungsweise', 'r.soll','re.belegnr', 'r.zahlungsstatus', 'r.status', 'pt.payement_status' ,'id');
        $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')', 'r.belegnr', 'adr.kundennummer', 'r.name', 'r.land', 'p.abkuerzung','re.belegnr', 'r.status', "FORMAT(r.soll,2{$extended_mysql55})", 'adr.freifeld1', 'r.ihrebestellnummer','r.internebezeichnung','au.internet');
        $defaultorder = 13; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $alignright = array('10');

        if($this->app->erp->RechteVorhanden('gutschrift','summe')){
          $sumcol = 10;
        }

        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=gutschrift&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=gutschrift&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=CopyDialog(\"index.php?module=gutschrift&action=copy&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=gutschrift&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"6\" data-label-reference-id=\"%value%\" data-label-reference-table=\"gutschrift\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";


        $menucol = 14;

        $parameter = $this->app->User->GetParameter('table_filter_gutschrift');
        $parameter = base64_decode($parameter);
        $parameter = json_decode($parameter, true);

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, concat('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',r.id,'\" />')as auswahl,
              r.belegnr,
              DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, adr.kundennummer as kundennummer,
          CONCAT(" . $this->app->erp->MarkerUseredit("r.name", "r.useredittimestamp") . ", if(r.internebezeichnung!='',CONCAT('<br><i style=color:#999>',r.internebezeichnung,'</i>'),'')) as kunde,
              r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
              FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, re.belegnr as rechnung, UPPER(r.status) as status,
             ".$this->IconsSQLReturnOrder()."  ,r.id
          FROM  gutschrift r 
          LEFT JOIN rechnung re ON re.id=r.rechnungid 
          LEFT JOIN projekt p ON p.id=r.projekt 
          LEFT JOIN adresse adr ON r.adresse=adr.id  
          LEFT JOIN auftrag au ON au.id = re.auftragid 
          LEFT JOIN payment_transaction AS pt ON r.id = pt.returnorder_id ";

        if(isset($parameter['artikel']) && !empty($parameter['artikel'])) {
          $artikelid = $this->app->DB->Select("SELECT id FROM artikel where geloescht != 1 AND nummer != 'DEL' AND nummer != '' AND nummer = '".$this->app->DB->real_escape_string(reset(explode(' ',trim($parameter['artikel']))))."' LIMIT 1");
          if($artikelid)
          {
            $paramsArray[] = "ap.artikel = '" . $artikelid . "' ";
            $sql .= " INNER JOIN gutschrift_position ap ON r.id = ap.gutschrift ";
            $groupby = " GROUP BY r.id, p.id, adr.id ";
          }
        }

        // Fester filter
        
        //if($tmp!="")$tmp .= " AND r.belegnr!='' ";


        $more_data1 = $this->app->Secure->GetGET("more_data1");
        if ($more_data1 == 1) $subwhere[] = " (r.zahlungsstatus!='bezahlt' AND (r.manuell_vorabbezahlt='' OR r.manuell_vorabbezahlt='0000-00-00' OR r.manuell_vorabbezahlt IS NULL)) ";

        $more_data2 = $this->app->Secure->GetGET("more_data2");
        if ($more_data2 == 1) $subwhere[] = " r.datum=DATE_FORMAT(NOW(),'%y-%m-%d') ";

        $more_data3 = $this->app->Secure->GetGET("more_data3");
        if ($more_data3 == 1) $subwhere[] = " (r.manuell_vorabbezahlt IS NULL OR r.manuell_vorabbezahlt='0000-00-00') ";


        for ($j = 0;$j < count($subwhere);$j++) $tmp.= " AND " . $subwhere[$j];

        $where = " r.status!='angelegt' AND  r.id!='' ".$tmp ." ". $this->app->erp->ProjektRechte('p.id', true, 'r.vertriebid');


        /* STAMMDATEN */
        if(isset($parameter['kundennummer']) && !empty($parameter['kundennummer'])) {
          $paramsArray[] = "(r.kundennummer LIKE '%".$parameter['kundennummer']."%' OR adr.kundennummer LIKE '%".$parameter['kundennummer']."%') ";
        }

        if(isset($parameter['name']) && !empty($parameter['name'])) {
          $paramsArray[] = "r.name LIKE '%".$parameter['name']."%' ";
        }

        if(isset($parameter['ansprechpartner']) && !empty($parameter['ansprechpartner'])) {
          $paramsArray[] = "r.ansprechpartner LIKE '%".$parameter['ansprechpartner']."%' ";
        }

        if(isset($parameter['abteilung']) && !empty($parameter['abteilung'])) {
          $paramsArray[] = "r.abteilung LIKE '%".$parameter['abteilung']."%' ";
        }

        if(isset($parameter['strasse']) && !empty($parameter['strasse'])) {
          $paramsArray[] = "r.strasse LIKE '%".$parameter['strasse']."%' ";
        }

        if(isset($parameter['plz']) && !empty($parameter['plz'])) {
          $paramsArray[] = "r.plz LIKE '".$parameter['plz']."%'";
        }

        if(isset($parameter['ort']) && !empty($parameter['ort'])) {
          $paramsArray[] = "r.ort LIKE '%".$parameter['ort']."%' ";
        }

        if(isset($parameter['land']) && !empty($parameter['land'])) {
          $paramsArray[] = "r.land LIKE '%".$parameter['land']."%' ";
        }

        if(isset($parameter['ustid']) && !empty($parameter['ustid'])) {
          $paramsArray[] = "r.ustid LIKE '%".$parameter['ustid']."%' ";
        }

        if(isset($parameter['telefon']) && !empty($parameter['telefon'])) {
          $paramsArray[] = "r.telefon LIKE '%".$parameter['telefon']."%' ";
        }

        if(isset($parameter['ustid']) && !empty($parameter['ustid'])) {
          $paramsArray[] = "r.ustid LIKE '%".$parameter['ustid']."%' ";
        }

        /* XXX */
        if(isset($parameter['datumVon']) && !empty($parameter['datumVon'])) {
          $paramsArray[] = "r.datum >= '" . date('Y-m-d',strtotime($parameter['datumVon']))."' ";
        }

        if(isset($parameter['datumBis']) && !empty($parameter['datumBis'])) {
          $paramsArray[] = "r.datum <= '" . date('Y-m-d',strtotime($parameter['datumBis']))."' ";
        }

        if(isset($parameter['projekt']) && !empty($parameter['projekt'])) {

          $projektData = $this->app->DB->SelectArr('
            SELECT
              *
            FROM
              projekt
            WHERE
              abkuerzung LIKE "' . $parameter['projekt'] . '"
          ');
          $projektData = reset($projektData);
          $paramsArray[] = "r.projekt = '".$projektData['id']."' ";
        }

        if(isset($parameter['belegnummer']) && !empty($parameter['belegnummer'])) {
          $paramsArray[] = "r.belegnr LIKE '".$parameter['belegnummer']."' ";
        }

        if(isset($parameter['internebemerkung']) && !empty($parameter['internebemerkung'])) {
          $paramsArray[] = "r.internebemerkung LIKE '%".$parameter['internebemerkung']."%' ";
        }

        if(isset($parameter['aktion']) && !empty($parameter['aktion'])) {
          $paramsArray[] = "r.aktion LIKE '%".$parameter['aktion']."%' ";
        }

        if(isset($parameter['freitext']) && !empty($parameter['freitext'])) {
          $paramsArray[] = "r.freitext LIKE '%".$parameter['freitext']."%' ";
        }

        if(isset($parameter['zahlungsweise']) && !empty($parameter['zahlungsweise'])) {
          $paramsArray[] = "r.zahlungsweise LIKE '".$parameter['zahlungsweise']."' ";
        }

        if(isset($parameter['status']) && !empty($parameter['status'])) {
          $paramsArray[] = "r.status LIKE '%".$parameter['status']."%' ";
        }

        if(isset($parameter['versandart']) && !empty($parameter['versandart'])) {
          $paramsArray[] = "r.versandart LIKE '%".$parameter['versandart']."%' ";
        }

        if(isset($parameter['betragVon']) && !empty($parameter['betragVon'])) {
          $paramsArray[] = "r.soll >= '" . $parameter['betragVon'] . "' ";
        }

        if(isset($parameter['betragBis']) && !empty($parameter['betragBis'])) {
          $paramsArray[] = "r.soll <= '" . $parameter['betragBis'] . "' ";
        }

        // projekt, belegnummer, internetnummer, bestellnummer, transaktionsId, freitext, internebemerkung, aktionscodes

        if ($paramsArray) {
          $where .= ' AND ' . implode(' AND ', $paramsArray);
        }

        // gesamt anzahl
        $count = "SELECT COUNT(r.id) FROM gutschrift r WHERE r.status='freigegeben'";
        $moreinfo = true;

        /*
            // headings
            $heading =  array('','Vom','Kunde','Gutschrift','Land','Projekt','Zahlung','Betrag','Zahlung','Status','Men&uuml;');
            $width   =  array('1%','1%','40%','1%','1%','1%','1%','1%','1%','1%');
            $findcols = array('open','vom','name','land','projekt','zahlungsweise','betrag','zahlung','status','icons','id');
            $searchsql = array('r.id','r.datum','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.zahlungsweise','r.status','r.soll','r.ist','r.zahlungsstatus','r.plz','r.id');
        
            $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=gutschrift&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>".
            "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=gutschrift&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>".
            "&nbsp;<a href=\"index.php?module=gutschrift&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>";
        
            $menucol=10;
            // SQL statement
            $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=/themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, 
            r.name as name, r.belegnr, r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
            r.soll as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
            FROM  gutschrift r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
            // Fester filter
        
        
            $where = " r.id!='' AND r.status='freigegeben' ";
        
            // gesamt anzahl
            $count = "SELECT COUNT(r.id) FROM gutschrift r WHERE r.status='freigegeben'";
        
            $moreinfo = true;
        */
        break;
      case "rechnungeninbearbeitung":
        $allowed['rechnung'] = array('list', 'create');

        // headings
        $heading = array('', 'Rechnung', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Zahlung', 'Betrag (brutto)', 'Zahlung', 'Status', 'Men&uuml;');
        $width = array('1%', '10%', '10%', '10%', '35%', '1%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open', 'r.belegnr', 'r.datum', 'adr.kundennummer', "CONCAT(" . $this->app->erp->MarkerUseredit("r.name", "r.useredittimestamp") . ", if(r.internebezeichnung!='',CONCAT('<br><i style=color:#999>',r.internebezeichnung,'</i>'),''))", 'r.land', 'p.abkuerzung', 'r.zahlungsweise', 'r.soll', 'r.zahlungsstatus', 'r.status', 'r.id');
        $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')', 'r.belegnr', 'adr.kundennummer', 'r.name', 'r.land', 'p.abkuerzung', 'r.zahlungsweise', 'r.status', "FORMAT(r.soll,2{$extended_mysql55})", 'r.zahlungsstatus', 'adr.freifeld1', 'r.ihrebestellnummer','r.internebezeichnung', 'au.internet');
        $defaultorder = 12; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $alignright = array('9');

        if($this->app->erp->RechteVorhanden('rechnung','summe'))
          $sumcol = 9;
        $menu = "<table class=\"nopadding\" cellpadding=0 cellspacing=0>";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=rechnung&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=rechnung&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=rechnung&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";
        $menucol = 11;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, 'ENTWURF' as belegnr, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, 
              adr.kundennummer,
          CONCAT(" . $this->app->erp->MarkerUseredit("r.name", "r.useredittimestamp") . ", if(r.internebezeichnung!='',CONCAT('<br><i style=color:#999>',r.internebezeichnung,'</i>'),'')) as kunde,
            r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
              FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                FROM  rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id LEFT JOIN auftrag au ON au.id = r.auftragid ";

        // Fester filter
        $where = " ( r.status='angelegt') " . $this->app->erp->ProjektRechte('p.id', true, 'r.vertriebid');

        // gesamt anzahl
        $count = "SELECT COUNT(r.id) FROM rechnung r WHERE ( r.status='angelegt') ";
        $moreinfo = true;
        break;
      case "rechnungenoffene":

        // headings
        $allowed['rechnung'] = array('list');
        $heading = array('', 'Rechnung', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Zahlung', 'Betrag (brutto)', 'Zahlung', 'Status', 'Men&uuml;');

        //$width   =  array('1%','2%','5%','5%','50%','3%','3%','3%','3%','3%','3%','3%');
        $width = array('1%', '10%', '10%', '10%', '35%', '1%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open', 'r.belegnr', 'r.datum', 'adr.kundennummer', 'r.name', 'r.land', 'p.abkuerzung', 'r.zahlungsweise', 'r.soll', 'r.zahlungsstatus', 'r.status', 'r.id');
        $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')', 'r.belegnr', 'adr.kundennummer', 'r.name', 'r.land', 'p.abkuerzung', 'r.zahlungsweise', 'r.status', "FORMAT(r.soll,2{$extended_mysql55})", 'r.zahlungsstatus', 'adr.freifeld1', 'r.ihrebestellnummer','r.internebezeichnung');
        $defaultorder = 12; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $alignright = array('9');

        if($this->app->erp->RechteVorhanden('rechnung','summe')){
          $sumcol = 9;
        }
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=rechnung&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=rechnung&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=rechnung&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu  .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";
        $menucol = 11;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, r.belegnr as belegnr, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, 
              adr.kundennummer, 

          CONCAT(" . $this->app->erp->MarkerUseredit("r.name", "r.useredittimestamp") . ", if(r.internebezeichnung!='',CONCAT('<br><i style=color:#999>',r.internebezeichnung,'</i>'),'')) as kunde,
            r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
              FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                FROM  rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";

        // Fester filter
        $where = " r.id!='' AND r.status='freigegeben' " . $this->app->erp->ProjektRechte('p.id', true, 'r.vertriebid');

        // gesamt anzahl
        $count = "SELECT COUNT(r.id) FROM rechnung r WHERE r.status='freigegeben'";
        $moreinfo = true;
        break;
      case "rechnungen":
        $allowed['rechnung'] = array('list');
        $rowcallback_gt = 1;
        $this->app->Tpl->Add('JQUERYREADY', "$('#zahlungseingang').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#zahlungseingangfehlt').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#rechnungenheute').click( function() { fnFilterColumn3( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#rechnungenstorniert').click( function() { fnFilterColumn4( 0 ); } );");
        $defaultorder = 15; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $alignright = array(10,13);

        if($this->app->erp->RechteVorhanden('rechnung','summe'))
          $sumcol = array(10,13);

        for ($r = 1;$r < 5;$r++) {
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

        $zusatzcols = array();
        $rechnungzusatzfelder = $this->app->erp->getZusatzfelderRechnung();

 // headings
        $heading = array('', '', 'Rechnung', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Zahlung', 'Betrag (brutto)', 'W&auml;hrung', 'Zahlstatus', 'Differenz', 'Status');
        $width = array('1%', '1%', '10%', '10%', '10%', '35%', '1%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open', 'r.belegnr', 'r.belegnr', 'r.datum', 'r.kundennummer', 'r.name', 'r.land', 'p.abkuerzung',
        'r.zahlungsweise', 'r.soll', 'r.waehrung', "if(r.soll-r.ist+r.skonto_gegeben!=0 AND r.ist > 0 AND r.zahlungsstatus!='bezahlt','teilbezahlt',r.zahlungsstatus)", "r.ist-r.soll+r.skonto_gegeben", 'r.status');

        $searchsql = array('r.belegnr', 'r.belegnr','DATE_FORMAT(r.datum,\'%d.%m.%Y\')', array('r.kundennummer','adr.kundennummer'), 'r.name', 'r.land', 'p.abkuerzung', 
        'r.zahlungsweise', array("FORMAT(r.soll,2{$extended_mysql55})", "if(r.soll-r.ist+r.skonto_gegeben!=0 AND r.ist > 0 AND r.zahlungsstatus!='bezahlt','teilbezahlt',r.zahlungsstatus)"), 'r.waehrung', "if(r.soll-r.ist+r.skonto_gegeben!=0 AND r.ist > 0 AND r.zahlungsstatus!='bezahlt','teilbezahlt',r.zahlungsstatus)"
        , "FORMAT(r.ist-r.soll+r.skonto_gegeben,2{$extended_mysql55})", "if(r.status = 'storniert' AND r.teilstorno = 1,'Teilstorno',r.status)", 'r.id', 'adr.freifeld1', 'r.ihrebestellnummer', 'r.internebezeichnung', 'au.internet');

        $menu = "<table class=\"nopadding\" cellpadding=0 cellspacing=0>";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=rechnung&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=rechnung&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=CopyDialog(\"index.php?module=rechnung&action=copy&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=rechnung&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= '<a href="#" class="label-manager" data-label-column-number="6" data-label-reference-id="%value%" data-label-reference-table="rechnung">';
        $menu .= '<span class="label-manager-icon"></span>';
        $menu .= '</a>';
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";
        $menucol = 14;

        $datecols = array(3);
        $numbercols = array(9, 12);


        for($i = 1; $i <= 5; $i++) {
          $zusatzfeld = $this->app->erp->Firmendaten('rechnungtabellezusatz' . $i);
          if($zusatzfeld && isset($rechnungzusatzfelder[$zusatzfeld])){
            $defaultorder++;
            $menucol++;
            $heading[] = $rechnungzusatzfelder[$zusatzfeld];
            $width[] = '10%';

            switch($zusatzfeld)
            {
              case 'internet':
                $searchsql[] = "au.internet";
                $zusatzcols[] = "au.internet";
                $findcols[] = 'au.internet';
                break;

              case 'umsatz_netto':
                $searchsql[] = $this->app->erp->FormatPreis('r.umsatz_netto',2);
                $zusatzcols[] = $this->app->erp->FormatPreis('r.umsatz_netto',2);
                $findcols[] = 'r.umsatz_netto';
                $numbercols = array(9, 12, array_search('r.umsatz_netto', $findcols));
                $sumcol[] = array_search('r.umsatz_netto', $findcols) + 1;
                $alignright[] = array_search('r.umsatz_netto', $findcols) + 1;
                break;

              default:
                $searchsql[] = 'r.'.$zusatzfeld;
                $zusatzcols[] = 'r.'.$zusatzfeld;
                $findcols[] = 'r.'.$zusatzfeld;
            }
          }
        }

        $width[] = '1%';
        $findcols[] = 'r.id';
        $heading[] = 'Men&uuml;';

        $parameter = $this->app->User->GetParameter('table_filter_rechnung');
        $parameter = base64_decode($parameter);
        $parameter = json_decode($parameter, true);
//        $columnfilter = true;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,concat('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',r.id,'\" />')as auswahl, r.belegnr, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom,
              if(r.kundennummer <> '',r.kundennummer,adr.kundennummer),

          CONCAT(" . $this->app->erp->MarkerUseredit("r.name", "r.useredittimestamp") . ", if(r.internebezeichnung!='',CONCAT('<br><i style=color:#999>',r.internebezeichnung,'</i>'),'')) as kunde,
           r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise, FORMAT(r.soll,2{$extended_mysql55} ) as soll, ifnull(r.waehrung,'EUR'),
                        if(r.soll-r.ist+r.skonto_gegeben!=0 AND r.ist > 0 AND r.zahlungsstatus!='bezahlt','teilbezahlt',r.zahlungsstatus) as zahlung, 
              if(r.soll-r.ist+r.skonto_gegeben!=0 AND r.ist > 0,FORMAT(r.ist-r.soll+r.skonto_gegeben,2{$extended_mysql55}),FORMAT((r.soll-r.ist+r.skonto_gegeben)*-1,2{$extended_mysql55})) as fehlt, if(r.status = 'storniert' AND r.teilstorno = 1,'TEILSTORNO',UPPER(r.status))  as status, ".(!empty($zusatzcols)?implode(', ',$zusatzcols).',':'')." r.id
                FROM  rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id LEFT JOIN auftrag au ON au.id = r.auftragid ";
        if(isset($parameter['artikel']) && !empty($parameter['artikel'])) {
          $artikelid = $this->app->DB->Select("SELECT id FROM artikel where geloescht != 1 AND nummer != 'DEL' AND nummer != '' AND nummer = '".$this->app->DB->real_escape_string(reset(explode(' ',trim($parameter['artikel']))))."' LIMIT 1");
          if($artikelid)
          {
            $paramsArray[] = "ap.artikel = '" . $artikelid . "' ";
            $sql .= " INNER JOIN rechnung_position ap ON r.id = ap.rechnung ";
            $groupby = " GROUP BY r.id, p.id, adr.id ";
          }
        }
        // Fester filter
        $more_data1 = $this->app->Secure->GetGET("more_data1");
        if ($more_data1 == 1) {
          $subwhere[] = " r.zahlungsstatus='offen' ";
        }

        $more_data2 = $this->app->Secure->GetGET("more_data2");
        if ($more_data2 == 1) {
          $subwhere[] = " r.zahlungsstatus!='bezahlt' AND r.zahlungsstatus!='forderungsverlust' ";
        }

        $more_data3 = $this->app->Secure->GetGET("more_data3");
        if ($more_data3 == 1) {
          $subwhere[] = " r.datum=CURDATE() ";
          $ignore = true;
        }

        $more_data4 = $this->app->Secure->GetGET("more_data4");
        if ($more_data4 == 1) {
          $subwhere[] = " r.status='storniert' ";
        }
        $csubwhere = !empty($subwhere)?count($subwhere):0;
        for ($j = 0;$j < $csubwhere;$j++) {
          $tmp.= " AND " . $subwhere[$j];
        }
        
        if ($tmp != "" && !$ignore) {
          $tmp.= " AND r.belegnr!='' ";
        }
        $where = " r.id!='' AND r.status!='angelegt' $tmp " . $this->app->erp->ProjektRechte('p.id',true, 'r.vertriebid');

        /* STAMMDATEN */
        if(isset($parameter['kundennummer']) && !empty($parameter['kundennummer'])) {
          $paramsArray[] = " (r.kundennummer LIKE '%".$parameter['kundennummer']."%' OR adr.kundennummer LIKE '%".$parameter['kundennummer']."%' ) ";
        }

        if(isset($parameter['name']) && !empty($parameter['name'])) {
          $paramsArray[] = "r.name LIKE '%".$parameter['name']."%' ";
        }

        if(isset($parameter['ansprechpartner']) && !empty($parameter['ansprechpartner'])) {
          $paramsArray[] = "r.ansprechpartner LIKE '%".$parameter['ansprechpartner']."%' ";
        }

        if(isset($parameter['abteilung']) && !empty($parameter['abteilung'])) {
          $paramsArray[] = "r.abteilung LIKE '%".$parameter['abteilung']."%' ";
        }

        if(isset($parameter['strasse']) && !empty($parameter['strasse'])) {
          $paramsArray[] = "r.strasse LIKE '%".$parameter['strasse']."%' ";
        }

        if(isset($parameter['plz']) && !empty($parameter['plz'])) {
          $paramsArray[] = "r.plz LIKE '".$parameter['plz']."%'";
        }

        if(isset($parameter['ort']) && !empty($parameter['ort'])) {
          $paramsArray[] = "r.ort LIKE '%".$parameter['ort']."%' ";
        }

        if(isset($parameter['land']) && !empty($parameter['land'])) {
          $paramsArray[] = "r.land LIKE '%".$parameter['land']."%' ";
        }

        if(isset($parameter['ustid']) && !empty($parameter['ustid'])) {
          $paramsArray[] = "r.ustid LIKE '%".$parameter['ustid']."%' ";
        }

        if(isset($parameter['telefon']) && !empty($parameter['telefon'])) {
          $paramsArray[] = "r.telefon LIKE '%".$parameter['telefon']."%' ";
        }

        /* XXX */
        if(isset($parameter['datumVon']) && !empty($parameter['datumVon'])) {
          $paramsArray[] = "r.datum >= '" . date('Y-m-d',strtotime($parameter['datumVon']))."' ";
        }

        if(isset($parameter['datumBis']) && !empty($parameter['datumBis'])) {
          $paramsArray[] = "r.datum <= '" . date('Y-m-d',strtotime($parameter['datumBis']))."' ";
        }

        if(isset($parameter['projekt']) && !empty($parameter['projekt'])) {

          $projektData = $this->app->DB->SelectArr('
            SELECT
              *
            FROM
              projekt
            WHERE
              abkuerzung LIKE "' . $parameter['projekt'] . '"
          ');
          $projektData = reset($projektData);
          $paramsArray[] = "r.projekt = '".$projektData['id']."' ";
        }

        if(isset($parameter['belegnummer']) && !empty($parameter['belegnummer'])) {
          $paramsArray[] = "r.belegnr LIKE '".$parameter['belegnummer']."' ";
        }

        if(isset($parameter['internebemerkung']) && !empty($parameter['internebemerkung'])) {
          $paramsArray[] = "r.internebemerkung LIKE '%".$parameter['internebemerkung']."%' ";
        }

        if(isset($parameter['aktion']) && !empty($parameter['aktion'])) {
          $paramsArray[] = "r.aktion LIKE '%".$parameter['aktion']."%' ";
        }

        if(isset($parameter['freitext']) && !empty($parameter['freitext'])) {
          $paramsArray[] = "r.freitext LIKE '%".$parameter['freitext']."%' ";
        }

        if(isset($parameter['zahlungsweise']) && !empty($parameter['zahlungsweise'])) {
          $paramsArray[] = "r.zahlungsweise LIKE '".$parameter['zahlungsweise']."' ";
        }

        if(isset($parameter['status']) && !empty($parameter['status'])) {
          $paramsArray[] = "r.status LIKE '%".$parameter['status']."%' ";
        }

        if(isset($parameter['versandart']) && !empty($parameter['versandart'])) {
          $paramsArray[] = "r.versandart LIKE '%".$parameter['versandart']."%' ";
        }

        if(isset($parameter['betragVon']) && !empty($parameter['betragVon'])) {
          $parameter['betragVon'] = $this->app->erp->FromFormatZahlToDB($parameter['betragVon']);
          $paramsArray[] = "r.soll >= '" . $parameter['betragVon'] . "' ";
        }

        if(isset($parameter['betragBis']) && !empty($parameter['betragBis'])) {
          $parameter['betragBis'] = $this->app->erp->FromFormatZahlToDB($parameter['betragBis']);
          $paramsArray[] = "r.soll <= '" . $parameter['betragBis'] . "' ";
        }

        // projekt, belegnummer, internetnummer, bestellnummer, transaktionsId, freitext, internebemerkung, aktionscodes

        if ($paramsArray) {
          $where .= ' AND ' . implode(' AND ', $paramsArray);
        }

        // gesamt anzahl
        $count = "SELECT COUNT(r.id) FROM rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id WHERE ".$where;
        $moreinfo = true;
        break;
      case "bestellungeninbearbeitung":
        $allowed['bestellung'] = array('create', 'list');

        // headings
        $heading = array('', 'Bestellung', 'Vom', 'Lf-Nr.', 'Lieferant', 'Land', 'Projekt', 'Betrag (brutto)', 'Status', 'Men&uuml;');
        $width = array('1%', '10%', '10%', '10%', '40%', '1%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('b.id', 'belegnr', 'b.datum', 'adr.lieferantennummer', 'b.name', 'b.land', 'p.abkuerzung', 'b.gesamtsumme', 'b.status', 'b.id');
        $searchsql = array('DATE_FORMAT(b.datum,\'%d.%m.%Y\')', 'b.belegnr', 'adr.lieferantennummer', 'b.name', 'b.land', 'p.abkuerzung', 'b.status', 'b.gesamtsumme','b.internebezeichnung');
        $defaultorder = 10; //Optional wenn andere Reihenfolge gewuenscht

        $alignright = array('8');
        $sumcol = 8;
        $defaultorderdesc = 1;
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=bestellung&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=bestellung&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=bestellung&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"2\" data-label-reference-id=\"%value%\" data-label-reference-table=\"bestellung\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";
        $menucol = 9;

        // SQL statement
        $sql =
          "SELECT 
          b.id,
          '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' AS `open`, 
          'ENTWURF' AS `belegnr`, 
          DATE_FORMAT(b.datum,'%d.%m.%Y') AS `vom`, 
          adr.lieferantennummer AS `lieferantennummer`,
          CONCAT(
            " . $this->app->erp->MarkerUseredit("b.name", "b.useredittimestamp") . ", 
            IF(
              b.internebezeichnung!='',
              CONCAT(
                '<br><i style=color:#999>',
                b.internebezeichnung,
                '</i>'
              ),
              ''
            ),
            IF(
              b.abweichendelieferadresse = 1,
              CONCAT(
                '<br><i style=color:#999>Abw. Lieferadr.: ',
                b.liefername,', ',
                b.lieferstrasse,', ',
                b.lieferland,'-',
                b.lieferplz,' ',
                b.lieferort,
                '</i>'
              ),
              ''
            )
          ) AS `lieferant`,
          b.land AS `land`, 
          p.abkuerzung AS `projekt`,  
          FORMAT(b.gesamtsumme,2{$extended_mysql55}) AS `summe`, 
          UPPER(b.status) AS `status`, 
          b.id
          FROM  `bestellung` AS `b` 
          LEFT JOIN `projekt` AS `p` ON p.id=b.projekt 
          LEFT JOIN adresse AS `adr` ON b.adresse=adr.id  ";

        // Fester filter
        $where = " ( b.status='angelegt') " . $this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(b.id) FROM `bestellung` AS `b` WHERE ( b.status='angelegt') ";
        $moreinfo = true;
        break;
 
      case "bestellung_offenepositionen":
        $allowed['bestellung'] = array('offenepositionen');

// START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#bestellung_offenepositionen_lager').click( function() { fnFilterColumn1( 0 ); } );");
        for ($r = 1;$r < 2;$r++) {
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

        // headings
        $heading = array('Vom','Bestellung', 'LF-Datum', 'best. LF-Datum', 'Lf-Nr.', 'Lieferant', 'Artikel-Nr','Artikel','Bestell-Nr.','Bezeichnung','Menge','Geliefert', 'Projekt','Men&uuml;');
        $width = array('5%','5%', '8%', '8%', '10%', '20%', '10%', '20%', '10%', '10%', '10%', '10%','10%','1%');
        $liefertermine = $this->app->erp->ModulVorhanden('liefertermine');
        if($liefertermine)
        {
          $searchsql = array('DATE_FORMAT(b.datum,\'%d.%m.%Y\')','b.belegnr', 'if(bp.lieferdatum != \'0000-00-00\' ,bp.lieferdatum,if(b.lieferdatum != \'0000-00-00\',b.lieferdatum,\'-\'))', 'if(lp.lieferdatum != \'0000-00-00\' ,lp.lieferdatum,if(b.bestaetigteslieferdatum != \'0000-00-00\',b.bestaetigteslieferdatum,\'-\'))','adr.lieferantennummer', 'b.name', 'a.nummer','a.name_de','bp.bestellnummer','bp.bezeichnunglieferant','bp.menge','bp.geliefert', 'p.abkuerzung');
        }else{
          $searchsql = array('DATE_FORMAT(b.datum,\'%d.%m.%Y\')','b.belegnr', 'if(bp.lieferdatum != \'0000-00-00\' ,bp.lieferdatum,if(b.lieferdatum != \'0000-00-00\',b.lieferdatum,\'-\'))', 'if(b.bestaetigteslieferdatum != \'0000-00-00\',b.bestaetigteslieferdatum,\'-\')','adr.lieferantennummer', 'b.name', 'a.nummer','a.name_de','bp.bestellnummer','bp.bezeichnunglieferant','bp.menge','bp.geliefert', 'p.abkuerzung');
        }
        $findcols = array('b.datum','b.belegnr', "if(bp.lieferdatum != '0000-00-00', bp.lieferdatum, if(b.lieferdatum != '0000-00-00', b.lieferdatum, '-'))", "if(lp.lieferdatum != '0000-00-00', lp.lieferdatum, if(b.bestaetigteslieferdatum != '0000-00-00', b.bestaetigteslieferdatum, '-'))", 'adr.lieferantennummer', 'b.name', 'a.nummer','a.name_de','bp.bestellnummer','bp.bezeichnunglieferant','bp.menge','bp.geliefert', 'p.abkuerzung', 'id');
        
        //$defaultorder = 10; //Optional wenn andere Reihenfolge gewuenscht

        //$defaultorderdesc = 1;
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=bestellung&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=bestellung&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";


        $more_data1 = $this->app->Secure->GetGET("more_data1");
        if ($more_data1 == 1) $subwhere[] = " a.lagerartikel='1' ";

        $tmp = '';
        $csubwhere = !empty($subwhere)?count($subwhere):0;
        for ($j = 0;$j < $csubwhere;$j++) $tmp.= " AND " . $subwhere[$j];

        if($liefertermine)
        {
          // SQL statement
          $sql = "SELECT SQL_CALC_FOUND_ROWS b.id, DATE_FORMAT(b.datum,'%d.%m.%Y') as vom, b.belegnr, 

          if(bp.lieferdatum != '0000-00-00' ,DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),if(b.lieferdatum != '0000-00-00',DATE_FORMAT(b.lieferdatum,'%d.%m.%Y'),'-')) as standardlieferdatum,
          if(lp.lieferdatum != '0000-00-00' ,DATE_FORMAT(lp.lieferdatum,'%d.%m.%Y'),if(b.bestaetigteslieferdatum != '0000-00-00',DATE_FORMAT(b.bestaetigteslieferdatum,'%d.%m.%Y'),'-')) as bestaetigteslieferdatum,

                  adr.lieferantennummer as lieferantennummer,
                " . $this->app->erp->MarkerUseredit("b.name", "b.useredittimestamp") . " as lieferant,  a.nummer,a.name_de, bp.bestellnummer,bp.bezeichnunglieferant,trim(bp.menge)+0,trim(bp.geliefert)+0, p.abkuerzung as projekt,  
                  b.id FROM  bestellung b LEFT JOIN projekt p ON p.id=b.projekt LEFT JOIN adresse adr ON b.adresse=adr.id  LEFT JOIN bestellung_position bp ON bp.bestellung=b.id LEFT JOIN artikel a ON a.id=bp.artikel LEFT JOIN liefertermine_positionen lp ON bp.id=lp.bestellung_position";


          // Fester filter
          $where = "  (bp.geliefert < bp.menge AND (b.status!='abgeschlossen' AND b.status!='angelegt' AND b.status!='storniert')) $tmp  " . $this->app->erp->ProjektRechte();
        }else{
          // SQL statement
          $sql = "SELECT SQL_CALC_FOUND_ROWS b.id, DATE_FORMAT(b.datum,'%d.%m.%Y') as vom, b.belegnr, 

          if(bp.lieferdatum != '0000-00-00' ,DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),if(b.lieferdatum != '0000-00-00',DATE_FORMAT(b.lieferdatum,'%d.%m.%Y'),'-')) as standardlieferdatum,
          if(b.bestaetigteslieferdatum != '0000-00-00',DATE_FORMAT(b.bestaetigteslieferdatum,'%d.%m.%Y'),'-') as bestaetigteslieferdatum,
                  adr.lieferantennummer as lieferantennummer,
                " . $this->app->erp->MarkerUseredit("b.name", "b.useredittimestamp") . " as lieferant,  a.nummer,a.name_de, bp.bestellnummer,bp.bezeichnunglieferant,trim(bp.menge)+0,trim(bp.geliefert)+0, p.abkuerzung as projekt,  
                  b.id FROM  bestellung b LEFT JOIN projekt p ON p.id=b.projekt LEFT JOIN adresse adr ON b.adresse=adr.id  LEFT JOIN bestellung_position bp ON bp.bestellung=b.id LEFT JOIN artikel a ON a.id=bp.artikel";
          // Fester filter
          $where = "  (bp.geliefert < bp.menge AND (b.status!='abgeschlossen' AND b.status!='angelegt' AND b.status!='storniert')) $tmp  " . $this->app->erp->ProjektRechte();
        }
        // gesamt anzahl
        $count = "SELECT COUNT(b.id) FROM bestellung b LEFT JOIN projekt p ON p.id=b.projekt LEFT JOIN adresse adr ON b.adresse=adr.id  LEFT JOIN bestellung_position bp ON bp.bestellung=b.id LEFT JOIN artikel a ON a.id=bp.artikel WHERE $where";
        $moreinfo = false;
        break;


      case "bestellungenoffene":

        // headings
        $heading = array('', 'Vom', 'Lf-Nr.', 'Lieferant', 'Land', 'Projekt', 'Betrag (brutto)', 'Status', 'Men&uuml;');
        $width = array('1%', '10%', '10%', '40%', '1%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open', 'vom', 'lieferantennummer', 'lieferant', 'land', 'projekt', 'betrag', 'status', 'id');
        $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')', 'a.belegnr', 'adr.lieferantennummer', 'a.name', 'a.land', 'p.abkuerzung', 'a.zahlungsweise', 'a.status', 'a.gesamtsumme', 'a.status');
        $defaultorder = 10; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=bestellung&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>";
        $menucol = 8;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS b.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, DATE_FORMAT(b.datum,'%d.%m.%Y') as vom, adr.lieferantennummer as lieferantennummer,
              " . $this->app->erp->MarkerUseredit("b.name", "b.useredittimestamp") . " as lieferant,  b.land as land, p.abkuerzung as projekt,  
              b.gesamtsumme as summe, UPPER(b.status) as status, b.id
                FROM  bestellung b LEFT JOIN projekt p ON p.id=b.projekt LEFT JOIN adresse adr ON b.adresse=adr.id  ";

        // Fester filter
        $where = " b.id!='' " . $this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(b.id) FROM bestellung b";
        $moreinfo = true;

        // gesamt anzahl
        $count = "SELECT COUNT(b.id) FROM bestellung b WHERE b.status='freigegeben'";
        $moreinfo = true;
        break;

        //offene
        
      case "bestellungen":
        $allowed['bestellung'] = array('list');

        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#bestellungenoffen').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#bestellungnichtbestaetigt').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#bestellungversendet').click( function() { fnFilterColumn3( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#bestellungstorniert').click( function() { fnFilterColumn4( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#bestellungfehlt').click( function() { fnFilterColumn5( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#bestellunglieferdatumueberschritten').click( function() { fnFilterColumn6( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#bestellungohneverbindlichkeit').click( function() { fnFilterColumn7( 0 ); } );");
        $defaultorder = 10; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $rowcallback_gt = 1;
        $sumcol = 9;
        $alignright = array(9);
        for ($r = 1;$r < 8;$r++) {
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

        // ENDE EXTRA checkboxen
        
        // headings

        $heading = array('', '', 'Bestellung', 'Vom', 'Lf-Nr.', 'Lieferant', 'Land', 'Projekt', 'Betrag (brutto)', 'Status');
        $width = array('1%', '1%', '10%', '10%', '10%', '30%', '1%', '1%', '1%', '1%');
        $findcols = array('open', 'b.belegnr', 'b.belegnr', 'b.datum', 'adr.lieferantennummer', 'b.name', 'b.land', 'p.abkuerzung', 'b.gesamtsumme', 'b.status');
        $searchsql = array('DATE_FORMAT(b.datum,\'%d.%m.%Y\')', 'b.belegnr', 'adr.lieferantennummer', 'b.name', 'b.land', 'p.abkuerzung', 'b.status', 'b.gesamtsumme','b.internebezeichnung');
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=bestellung&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=bestellung&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=CopyDialog(\"index.php?module=bestellung&action=copy&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=bestellung&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"3\" data-label-reference-id=\"%value%\" data-label-reference-table=\"bestellung\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";
        $menucol = 10;


        $bestellungzusatzfelder = $this->app->erp->getZusatzfelderBestellung();

        for($i = 1; $i <= 5; $i++) {
          $zusatzfeld = $this->app->erp->Firmendaten('bestellungtabellezusatz' . $i);
          if($zusatzfeld && isset($bestellungzusatzfelder[$zusatzfeld])){
            $defaultorder++;
            $menucol++;
            $heading[] = $bestellungzusatzfelder[$zusatzfeld];
            $width[] = '10%';
            $findcols[] = 'b.'.$zusatzfeld;
            switch($zusatzfeld)
            {
              case 'bestaetigteslieferdatum':
              case 'gewuenschteslieferdatum':
                $searchsql[] = 'IF(b.'.$zusatzfeld.'!="0000-00-00", DATE_FORMAT(b.'.$zusatzfeld.",'%d.%m.%Y'), '')";
                $zusatzcols[] = 'IF(b.'.$zusatzfeld.'!="0000-00-00", DATE_FORMAT(b.'.$zusatzfeld.",'%d.%m.%Y'), '')";
                $datecols[] = array_search('b.'.$zusatzfeld, $findcols);

                break;
              default:
                $searchsql[] = 'b.'.$zusatzfeld;
                $zusatzcols[] = 'b.'.$zusatzfeld;
            }
          }
        }

        $heading[] = 'Men&uuml;';
        $width[] = '1%';
        $findcols[] = 'b.id';


        $parameter = $this->app->User->GetParameter('table_filter_bestellung');
        $parameter = base64_decode($parameter);
        $parameter = json_decode($parameter, true);
        
        $more_data6 = $this->app->Secure->GetGET("more_data6");
        $more_data7 = $this->app->Secure->GetGET("more_data7");
        
        // SQL statement
        $sql = "SELECT 
              b.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' AS `open`, 
              CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',b.id,'\" />') AS `auswahl`,    
              IF(b.status='storniert',CONCAT(b.belegnr),b.belegnr) AS `belegnr`, 
              IF(b.status='storniert',CONCAT(DATE_FORMAT(b.datum,'%d.%m.%Y')),DATE_FORMAT(b.datum,'%d.%m.%Y')) AS `vom`, 
              IF(b.status='storniert',CONCAT(adr.lieferantennummer),adr.lieferantennummer) AS `lieferantennummer`,  
              IF(
                b.status='storniert',
                CONCAT(
                  " . $this->app->erp->MarkerUseredit("b.name", "b.useredittimestamp") . ",
                  IF(
                    b.internebezeichnung!='',
                    CONCAT(
                      '<br><i style=color:#999>',
                      b.internebezeichnung,
                      '</i>'
                    ),
                    ''
                  ),
                  IF(
                    b.abweichendelieferadresse = 1,
                    CONCAT(
                      '<br><i style=color:#999>Abw. Lieferadr.: ',
                      b.liefername,', ',
                      b.lieferstrasse,', ',
                      b.lieferland,'-',
                      b.lieferplz,' ',
                      b.lieferort,
                      '</i>'
                    ),
                    ''
                  )
                ),
                CONCAT(
                  " . $this->app->erp->MarkerUseredit("b.name", "b.useredittimestamp") . ",
                  IF(
                    b.internebezeichnung!='',
                    CONCAT(
                      '<br><i style=color:#999>',
                      b.internebezeichnung,
                      '</i>'
                    ),
                    ''
                  ),
                  IF(
                    b.abweichendelieferadresse = 1,
                    CONCAT(
                      '<br><i style=color:#999>Abw. Lieferadr.: ',
                      b.liefername,', ',
                      b.lieferstrasse,', ',
                      b.lieferland,'-',
                      b.lieferplz,' ',
                      b.lieferort,
                      '</i>'
                    ),
                    ''
                  )
                )
              ) AS `lieferant`,  
              IF(b.status='storniert',CONCAT(b.land),b.land) AS `land`, 
              IF(b.status='storniert',CONCAT(p.abkuerzung),p.abkuerzung) AS `projekt`,
              IF(b.status='storniert',CONCAT(FORMAT(b.gesamtsumme,2{$extended_mysql55})),FORMAT(b.gesamtsumme,2{$extended_mysql55})) AS `summe`, 
              IF(b.status='storniert',CONCAT('<font color=red>',UPPER(b.status),'</font>'),UPPER(b.status)) AS `status`, 
              ".(!empty($zusatzcols)?implode(', ',$zusatzcols).',':'')." 
              b.id";

        $sql .= " 
        FROM  `bestellung` AS `b` 
        LEFT JOIN `projekt` AS `p` ON p.id=b.projekt 
        LEFT JOIN `adresse` AS `adr` ON b.adresse=adr.id";

        if(isset($parameter['artikel']) && !empty($parameter['artikel'])) {

          $artikelData = $this->app->DB->SelectArr('SELECT id FROM artikel WHERE nummer = "'.$parameter['artikel'].'"');
          if ($artikelData) {
            $artikelData = reset($artikelData);
            $sql .= "
              RIGHT JOIN `bestellung_position` AS `bp` ON bp.bestellung = b.id AND bp.artikel = " . $artikelData['id'] . " 
            ";
          }
        }

        $liefertermine = $this->app->erp->ModulVorhanden('liefertermine');

        if($more_data6){
          if($liefertermine){
            $sql .=' LEFT JOIN (SELECT MAX(`lieferdatum`) AS `datum`, `bestellung` FROM `liefertermine_positionen` GROUP BY `bestellung`) AS `ltp` ON b.id = ltp.bestellung
            LEFT JOIN (SELECT MAX(`lieferdatum`) AS `datum`, `bestellung` FROM `bestellung_position` GROUP BY `bestellung`) AS `bpl` ON b.id = bpl.bestellung 
            LEFT JOIN (SELECT SUM(`menge`) AS `menge`, SUM(geliefert) AS `geliefert`, `bestellung` FROM `bestellung_position` GROUP BY `bestellung`) AS `bm` ON b.id = bm.bestellung';
          }
          else{
            $sql .=' LEFT JOIN (SELECT MAX(`lieferdatum`) AS `datum`, `bestellung` FROM `bestellung_position` GROUP BY `bestellung`) AS `bpl` ON b.id = bpl.bestellung 
            LEFT JOIN (SELECT SUM(`menge`) AS `menge`, SUM(`geliefert`) AS `geliefert`, `bestellung` FROM `bestellung_position` GROUP BY `bestellung`) AS `bm` ON b.id = bm.bestellung';
          }
        }

        if($more_data7)
        {
          $sql .= " LEFT JOIN `verbindlichkeit` AS `verb` ON b.id = verb.bestellung ";
          for($i = 1; $i <= 15; $i++)
          {
            $sql .= " OR b.id = verb.bestellung".$i;
          }

          $sql .= " LEFT JOIN `verbindlichkeit_bestellungen` AS `verb_best` ON b.id = verb_best.bestellung ";
          $subwhere[] = " ISNULL(verb.id)  AND ISNULL(verb_best.id) ";
        }

        // Fester filter
        
        //FORMAT(b.gesamtsumme,2,'de_DE')

        
        // START EXTRA more

        
        // TODO: status abgeschlossen muss noch umgesetzt werden

        $more_data1 = $this->app->Secure->GetGET("more_data1");
        if ($more_data1 == 1) $subwhere[] = " b.status!='abgeschlossen' AND b.status!='storniert' ";

        $more_data2 = $this->app->Secure->GetGET("more_data2");
        if ($more_data2 == 1) $subwhere[] = " b.bestellung_bestaetigt!='1' AND b.status!='abgeschlossen' ";

        $more_data3 = $this->app->Secure->GetGET("more_data3");
        if ($more_data3 == 1) $subwhere[] = " b.status='versendet' ";

        $more_data4 = $this->app->Secure->GetGET("more_data4");
        if ($more_data4 == 1) $subwhere[] = " b.status='storniert' ";

        $more_data5 = $this->app->Secure->GetGET("more_data5");
        if ($more_data5 == 1) $subwhere[] = " b.status!='storniert' AND b.status!='abgeschlossen' AND (SELECT SUM(bp1.menge)>SUM(bp1.geliefert) FROM bestellung_position bp1 WHERE bp1.bestellung=b.id)!=0";

        if($more_data6 == 1){
          if($liefertermine){
            $subwhere[] = " IF(ISNULL(ltp.datum) OR ltp.datum = '0000-00-00', IF(ISNULL(bpl.datum) OR bpl.datum = '0000-00-00', IF(b.bestaetigteslieferdatum != '' && b.bestaetigteslieferdatum != '0000-00-00', b.bestaetigteslieferdatum < NOW(), b.gewuenschteslieferdatum < NOW() AND b.gewuenschteslieferdatum != '0000-00-00'), bpl.datum < NOW() AND bpl.datum != '0000-00-00'), ltp.datum < NOW() AND ltp.datum != '0000-00-00') AND b.status != 'storniert' AND b.status != 'abgeschlossen' AND (bm.menge > bm.geliefert)";
          }
          else{
            $subwhere[] =
              " IF(
                ISNULL(bpl.datum) OR bpl.datum = '0000-00-00', 
                IF(
                b.bestaetigteslieferdatum != '' && b.bestaetigteslieferdatum != '0000-00-00', 
                b.bestaetigteslieferdatum < NOW(), 
                b.gewuenschteslieferdatum < NOW() AND b.gewuenschteslieferdatum != '0000-00-00'
                ), 
                bpl.datum < NOW() AND bpl.datum != '0000-00-00'
              )
              AND b.status != 'storniert' 
              AND b.status != 'abgeschlossen' 
              AND (bm.menge > bm.geliefert)";
          }
        }

        for ($j = 0;$j < count($subwhere);$j++) $tmp.= " AND " . $subwhere[$j];

        // START EXTRA more
        $where = " b.id!='' AND b.status!='angelegt' $tmp " . $this->app->erp->ProjektRechte();

        /* STAMMDATEN */
        if(isset($parameter['name']) && !empty($parameter['name'])) {
          $paramsArray[] = "b.name LIKE '%".$parameter['name']."'% ";
        }

        if(isset($parameter['ansprechpartner']) && !empty($parameter['ansprechpartner'])) {
          $paramsArray[] = "b.ansprechpartner LIKE '%".$parameter['ansprechpartner']."%' ";
        }

        if(isset($parameter['abteilung']) && !empty($parameter['abteilung'])) {
          $paramsArray[] = "b.abteilung LIKE '%".$parameter['abteilung']."%' ";
        }

        if(isset($parameter['strasse']) && !empty($parameter['strasse'])) {
          $paramsArray[] = "b.strasse LIKE '%".$parameter['strasse']."%' ";
        }

        if(isset($parameter['plz']) && !empty($parameter['plz'])) {
          $paramsArray[] = "b.plz LIKE '".$parameter['plz']."%'";
        }

        if(isset($parameter['ort']) && !empty($parameter['ort'])) {
          $paramsArray[] = "b.ort LIKE '%".$parameter['ort']."%' ";
        }

        if(isset($parameter['land']) && !empty($parameter['land'])) {
          $paramsArray[] = "b.land LIKE '%".$parameter['land']."%' ";
        }

        if(isset($parameter['ustid']) && !empty($parameter['ustid'])) {
          $paramsArray[] = "b.ustid LIKE '%".$parameter['ustid']."%' ";
        }

        if(isset($parameter['telefon']) && !empty($parameter['telefon'])) {
          $paramsArray[] = "b.telefon LIKE '%".$parameter['telefon']."%' ";
        }

        if(isset($parameter['email']) && !empty($parameter['email'])) {
          $paramsArray[] = "b.email LIKE '%".$parameter['email']."%' ";
        }


        if(isset($parameter['datumVon']) && !empty($parameter['datumVon'])) {
          $paramsArray[] = "b.datum >= '" . date('Y-m-d',strtotime($parameter['datumVon']))."' ";
        }

        if(isset($parameter['datumBis']) && !empty($parameter['datumBis'])) {
          $paramsArray[] = "b.datum <= '" . date('Y-m-d',strtotime($parameter['datumBis']))."' ";
        }

        if(isset($parameter['betragVon']) && !empty($parameter['betragVon'])) {
          $paramsArray[] = "b.gesamtsumme >= ' ".$parameter['betragVon']."' ";
        }

        if(isset($parameter['betragBis']) && !empty($parameter['betragBis'])) {
          $paramsArray[] = "b.gesamtsumme <= ' ".$parameter['betragBis']."' ";
        }

        if(isset($parameter['projekt']) && !empty($parameter['projekt'])) {

          $projektData = $this->app->DB->SelectArr('
            SELECT
              *
            FROM
              `projekt`
            WHERE
              `abkuerzung` LIKE "' . $parameter['projekt'] . '"
          ');

          $projektData = reset($projektData);
          $paramsArray[] = "b.projekt = '".$projektData['id']."' ";
        }

        if(isset($parameter['belegnummer']) && !empty($parameter['belegnummer'])) {
          $paramsArray[] = "b.belegnr LIKE '%".$parameter['belegnummer']."%' ";
        }

        if(isset($parameter['internebemerkung']) && !empty($parameter['internebemerkung'])) {
          $paramsArray[] = "b.internebemerkung LIKE '%".$parameter['internebemerkung']."%' ";
        }

        if(isset($parameter['aktion']) && !empty($parameter['aktion'])) {
          $paramsArray[] = "b.aktion LIKE '%".$parameter['aktion']."%' ";
        }

        if(isset($parameter['freitext']) && !empty($parameter['freitext'])) {
          $paramsArray[] = "b.freitext LIKE '%".$parameter['freitext']."%' ";
        }

        if(isset($parameter['zahlungsweise']) && !empty($parameter['zahlungsweise'])) {
          $paramsArray[] = "b.zahlungsweise LIKE '%".$parameter['zahlungsweise']."%' ";
        }

        if(isset($parameter['status']) && !empty($parameter['status'])) {
          $paramsArray[] = "b.status LIKE '%".$parameter['status']."%' ";
        }

        if(isset($parameter['versandart']) && !empty($parameter['versandart'])) {
          $paramsArray[] = "b.versandart LIKE '%".$parameter['versandart']."%' ";
        }

        if(isset($parameter['lieferantennummer']) && !empty($parameter['lieferantennummer'])) {
          $paramsArray[] = "(b.lieferantennummer LIKE '%".$parameter['lieferantennummer']."%' OR adr.lieferantennummer LIKE '%".$parameter['lieferantennummer']."%')";
        }

        if ($paramsArray) {
          $where .= ' AND ' . implode(' AND ', $paramsArray);
        }


        // gesamt anzahl
        $count = "SELECT COUNT(b.id) FROM `bestellung` AS `b` WHERE b.status!='angelegt'";
        $moreinfo = true;
        break;
      case "vertreter":

        // headings
        $id = $this->app->Secure->GetGET('id');
        $heading = array('', 'Kennziffer', 'Name', 'Men&uuml;');
        $width = array('1%', '5%', '90%', '5%');
        $findcols = array('open', 'g.kennziffer', 'g.name', 'g.id');
        $searchsql = array('g.kennziffer', 'g.name');

        //$defaultorder = 6;  //Optional wenn andere Reihenfolge gewuenscht
        
        //$defaultorderdesc=1;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=versanderzeugen&action=einzel&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a></td></tr></table>";

        //&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a></td></tr></table>";
        $menucol = 6;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS g.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, 
              g.kennziffer, g.name, g.id 
              FROM  gruppen g ";
        $where = " g.art='verband' " . $this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(g.id) FROM gruppen g WHERE g.art='verband' " . $this->app->erp->ProjektRechte();
        $moreinfo = false;
        break;
      case "adresse_angebot":
        $allowed['adresse'] = array('belege');

        // headings
        $id = $this->app->Secure->GetGET('id');
        $heading = array('Angebot', 'Vom', 'Anfrage', 'Projekt', 'Zahlung', 'Betrag (brutto)', 'Status', 'Men&uuml;');
        $width = array('1%', '10%', '40%', '50%', '5%', '1%', '1%', '1%');
        $findcols = array('belegnr', 'vom', 'name', 'projekt', 'zahlungsweise', 'betrag', 'status', 'id');
        $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')', 'a.belegnr', 'a.anfrage', 'a.status', 'a.name', 'a.land', 'p.abkuerzung', 'a.zahlungsweise', 'a.status', "FORMAT(a.gesamtsumme,2{$extended_mysql55})");
        $defaultorder = 8; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $sumcol = 6;

        $alignright = array(6);

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=angebot&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"index.php?module=angebot&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>";
        $menucol = 9;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,
              if(belegnr='','ENTWURF',belegnr) as belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as vom, 
                a.anfrage as name, 
                  LEFT(UPPER( p.abkuerzung),10) as projekt, a.zahlungsweise as zahlungsweise,  
                  FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, a.status as status,  a.id
                    FROM  angebot a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
        $where = " a.adresse='$id' AND  a.status!='angelegt' " . $this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(a.id) FROM angebot a WHERE a.status!='angelegt' AND a.adresse='$id' ";
        $moreinfo = false;
        break;
      case "adresse_auftrag":
        $allowed['adresse'] = array('belege');

        // headings
        $id = $this->app->Secure->GetGET('id');
        $heading = array('Auftrag', 'Vom', 'Kommission/Bestellnummer', 'Projekt', 'Zahlung', 'Betrag (brutto)', 'Status', 'Men&uuml;');
        $width = array('1%', '10%', '40%', '50%', '5%', '1%', '1%', '1%');
        $findcols = array('belegnr', 'vom', 'name', 'projekt', 'zahlungsweise', 'a.gesamtsumme', 'status', 'id');
        $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')', 'a.belegnr', 'a.ihrebestellnummer', 'internet', 'a.status', 'a.name', 'a.land', 'p.abkuerzung', 'a.zahlungsweise', 'a.status', "FORMAT(a.gesamtsumme,2{$extended_mysql55})");
        $defaultorder = 8; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $sumcol = 6;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=auftrag&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"index.php?module=auftrag&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>";
        $menucol = 8;

        $alignright = array(6);

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,
              if(belegnr='','ENTWURF',belegnr) as belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as vom, 
                a.ihrebestellnummer as name, 
                  LEFT(UPPER( p.abkuerzung),10) as projekt, a.zahlungsweise as zahlungsweise,  
                  ".$this->app->erp->FormatPreis("a.gesamtsumme",2)." as betrag, a.status as status, a.id
                    FROM  auftrag a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
        $where = " a.adresse='$id' AND  a.status!='angelegt' " . $this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(a.id) FROM auftrag a LEFT JOIN projekt p ON p.id=a.projekt WHERE $where";
        $moreinfo = false;
        break;
      case "adresse_rechnung":
        $allowed['adresse'] = array('belege');

        // headings
        $id = $this->app->Secure->GetGET('id');
        $heading = array('Rechnung', 'Vom', 'Kommission/Internetnummer', 'Projekt', 'Zahlung', 'Betrag (brutto)','IST','Skonto gegeben','Zahlungsstatus','bezahlt am', 'Status', 'Men&uuml;');
        $width = array('1%', '10%', '40%', '5%', '5%', '1%','1%','1%', '1%','6%', '1%', '1%');
        $findcols = array('r.belegnr', 'r.datum', 'a.ihrebestellnummer', 'r.projekt', 'r.zahlungsweise', 'r.soll','r.ist','r.skonto_gegeben', 'r.zahlungsstatus','r.bezahlt_am', 'r.status', 'r.id');
        $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')', 'r.belegnr', 'a.ihrebestellnummer', 'r.status', 'r.name', 'r.land', 'p.abkuerzung', 'r.zahlungsweise', 'r.status', "FORMAT(r.ist,2{$extended_mysql55})", "FORMAT(r.soll,2{$extended_mysql55})", 'r.zahlungsstatus', "if(r.zahlungsstatus='offen',
              if(DATEDIFF(NOW(),DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day)) > 0,
                  CONCAT('<font color=red>',upper(substring(r.mahnwesen,1,1)),lower(substring(r.mahnwesen,2)),'</font>'),
                  'offen')

                ,if(r.zahlungsstatus='','offen',r.zahlungsstatus))");
        $defaultorder = 11;
        $defaultorderdesc = 1;
        $sumcol = 6;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>";
        $menucol = 1;

        $alignright = array(6,7,8);
   
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,
              if(r.belegnr='','ENTWURF',r.belegnr) as belegnr, 

                CONCAT(DATE_FORMAT(r.datum,'%d.%m.%Y'),' ',if(r.zahlungsstatus='offen',
                      if(DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day) >= NOW(),CONCAT('<br><font color=blue>f&auml;llig in ',DATEDIFF(DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day),NOW()),' Tagen</font>'),CONCAT('<br><font color=red>f&auml;llig seit ',DATEDIFF(NOW(),DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day)),' Tagen</font>'))
                      ,'')) as vom, 

                  a.ihrebestellnummer, 
                  LEFT(UPPER( p.abkuerzung),10) as projekt, r.zahlungsweise as zahlungsweise,  
                  FORMAT(r.soll,2{$extended_mysql55}) as betrag, 
                  FORMAT(r.ist,2{$extended_mysql55}) as ist, 
                  FORMAT(r.skonto_gegeben,2{$extended_mysql55}) as skonto_gegeben, 
                  if(r.zahlungsstatus='offen',
                      if(DATEDIFF(NOW(),DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day)) > 0,
                        CONCAT('<font color=red>',upper(substring(r.mahnwesen,1,1)),lower(substring(r.mahnwesen,2)),'</font>'),
                        'offen')

                      ,if(r.zahlungsstatus='','offen',r.zahlungsstatus)) as zahlungsstatus,
                      r.bezahlt_am, 
                      r.status, r.id
                    FROM  rechnung r LEFT JOIN auftrag a ON r.auftragid=a.id LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
        $where = " r.adresse='$id' AND  r.status!='angelegt' " . $this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(r.id) FROM rechnung r LEFT JOIN projekt p ON p.id=r.projekt WHERE r.adresse='$id'  AND  r.status!='angelegt' " . $this->app->erp->ProjektRechte();
        $moreinfo = false;
        break;
      case "adresse_gutschrift":
        $allowed['adresse'] = array('belege');

        // headings
        $id = $this->app->Secure->GetGET('id');
        $heading = array('Gutschrift', 'Vom', 'Projekt', 'Zahlung', 'Betrag (brutto)', 'Zahlungsstatus', 'Status', 'Men&uuml;');
        $width = array('1%', '10%', '5%', '5%', '10%', '10%', '10%', '1%');
        $findcols = array('g.belegnr', 'g.datum', 'g.projekt', 'g.zahlungsweise', 'g.soll', 'g.zahlungsstatus', 'g.status', 'g.id');
        $searchsql = array("DATE_FORMAT(g.datum,'%d.%m.%Y')", 'g.belegnr', 'g.status', 'g.name', 'g.land', 'p.abkuerzung', 'g.zahlungsweise', 'g.status', "FORMAT(g.ist,2{$extended_mysql55})", "FORMAT(g.soll,2{$extended_mysql55})");
        $defaultorder = 8;
        $defaultorderdesc = 1;
        $defaultsum = 5;

        $alignright = array(5);
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=gutschrift&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"index.php?module=gutschrift&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>";
        $menucol = 1;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS g.id,
              if(g.belegnr='','ENTWURF',g.belegnr) as belegnr, DATE_FORMAT(g.datum,'%d.%m.%Y') as vom, 
                LEFT(UPPER( p.abkuerzung),10) as projekt, g.zahlungsweise as zahlungsweise,  
                  FORMAT(g.soll,2{$extended_mysql55}) as betrag, g.zahlungsstatus as zahlungsstatus, g.status, g.id
                    FROM  gutschrift g LEFT JOIN projekt p ON p.id=g.projekt LEFT JOIN adresse adr ON g.adresse=adr.id  ";
        $where = " g.adresse='$id' AND  g.status!='angelegt' " . $this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(g.id) FROM gutschrift g WHERE g.adresse='$id' AND g.status!='angelegt' ";
        $moreinfo = false;
        break;
      case "adresse_lieferschein":
        $allowed['adresse'] = array('belege');

        // headings
        $id = $this->app->Secure->GetGET('id');
        $heading = array('Lieferschein', 'Auftrag', 'Kommission/Bestellnummer', 'Vom', 'Projekt', 'Versandart', 'Tracking', 'Status', 'Men&uuml;');
        $width = array('5%', '5%', '30%', '10%', '5%', '10%', '10%','10%', '1%');
        $findcols = array('l.belegnr', 'a.belegnr', 'a.ihrebestellnummer', 'l.datum', 'l.projekt', 'l.versandart', 'v.tracking', 'l.status', 'l.id');
        $searchsql = array("DATE_FORMAT(l.datum,'%d.%m.%Y')", 'a.belegnr', 'a.ihrebestellnummer', 'l.belegnr', 'a.ihrebestellnummer', 'l.status', 'v.tracking', 'l.name', 'l.land', 'p.abkuerzung', 'l.versandart', 'l.status');
        $defaultorder = 9;
        $defaultorderdesc = 1;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=lieferschein&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>";
        $menucol = 1;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id,
              if(l.belegnr='','ENTWURF',l.belegnr) as belegnr, a.belegnr, a.ihrebestellnummer, DATE_FORMAT(l.datum,'%d.%m.%Y') as vom, 
                LEFT(UPPER( p.abkuerzung),10) as projekt, l.versandart, if(v.tracking,v.tracking,'-'), l.status, l.id
                  FROM  lieferschein l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.adresse=adr.id 
                  LEFT JOIN auftrag a ON l.auftragid=a.id LEFT JOIN versand v ON v.lieferschein=l.id ";
        $where = " l.adresse='$id' AND  l.status!='angelegt' " . $this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(l.id) FROM lieferschein l WHERE l.adresse='$id' AND l.status!='angelegt' ";
        $moreinfo = false;
        break;
      case "angeboteinbearbeitung":
        $allowed['angebot'] = array('create', 'list');

        // headings
        $heading = array('', 'Angebot', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Zahlung', 'Betrag (brutto)', 'Status', 'Men&uuml;');
        $width = array('1%', '1%', '10%', '10%', '40%', '5%', '1%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open', 'belegnr', 'a.datum', 'adr.kundennummer', 'a.name', 'a.land', 'p.abkuerzung', 'a.zahlungsweise', 'a.gesamtsumme', 'a.status', 'id');
        $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')', 'a.belegnr', 'adr.kundennummer', 'a.name', 'a.land', 'p.abkuerzung', 'a.zahlungsweise', 'a.status', "FORMAT(a.gesamtsumme,2{$extended_mysql55})", 'a.status', 'adr.freifeld1','a.anfrage','a.internebezeichnung');
        $defaultorder = 11;
        $defaultorderdesc = 1;

        if($this->app->erp->RechteVorhanden('angebot','summe'))
          $sumcol = 9;

        $alignright = array('9');
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=angebot&action=edit&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=angebot&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=angebot&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"5\" data-label-reference-id=\"%value%\" data-label-reference-table=\"angebot\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";
        $menucol = 10;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, 'ENTWURF' as belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as vom, 
              adr.kundennummer as kundennummer, 

          CONCAT(" . $this->app->erp->MarkerUseredit("a.name", "a.useredittimestamp") . ", if(a.internebezeichnung!='',CONCAT('<br><i style=color:#999>',a.internebezeichnung,'</i>'),'')) as name,
a.land as land, p.abkuerzung as projekt, a.zahlungsweise as zahlungsweise,  
              FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, UPPER(a.status) as status, a.id
                FROM  angebot a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
        $where = " ( a.status='angelegt') " . $this->app->erp->ProjektRechte('p.id', true, 'a.vertriebid');

        // gesamt anzahl
        $count = "SELECT COUNT(a.id) FROM angebot a WHERE ( a.status='angelegt') ";
        $moreinfo = true;
        break;
      case "angeboteoffene":
        $allowed['angebot'] = array('list');

        // headings
        $heading = array('', 'Angebot', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Zahlung', 'Betrag (brutto)', 'Status', 'Men&uuml;');
        $width = array('1%', '1%', '10%', '10%', '40%', '5%', '1%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open', 'belegnr', 'a.datum', 'kundennummer', 'name', 'land', 'projekt', 'zahlungsweise', 'a.gesamtsumme', 'status', 'id');
        $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')', 'a.belegnr', 'adr.kundennummer', 'a.name', 'a.land', 'p.abkuerzung', 'a.zahlungsweise', 'a.status', "FORMAT(a.gesamtsumme,2{$extended_mysql55})", 'a.status', 'adr.freifeld1','a.anfrage');
        $defaultorder = 11; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $alignright = array('9');
        $sumcol = 9;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=angebot&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=angebot&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"index.php?module=angebot&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>";
        $menucol = 10;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, a.belegnr as belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as vom, 
              adr.kundennummer as kundennummer, " . $this->app->erp->MarkerUseredit("a.name", "a.useredittimestamp") . " as name, a.land as land, p.abkuerzung as projekt, a.zahlungsweise as zahlungsweise,  
              FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, UPPER(a.status) as status, a.id
                FROM  angebot a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
        $where = " a.id!='' AND a.status='freigegeben' " . $this->app->erp->ProjektRechte('p.id', true, 'a.vertriebid');

        // gesamt anzahl
        $count = "SELECT COUNT(a.id) FROM angebot a WHERE a.status='freigegeben'";
        $moreinfo = true;
        break;
 
      case "projekt_aufgaben":
        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#nuroffene').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#nurabgeschlossene').click( function() { fnFilterColumn2( 0 ); } );");
        for ($r = 1;$r < 3;$r++) {
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

        // ENDE EXTRA checkboxen
        (int)$fteilprojektfilter = $this->app->YUI->TableSearchFilter($name, 19, 'teilprojektfilter', $this->app->User->GetParameter("projektdashboardartikelteilprojektfilter"));
        // headings

        $heading = array('Aufgabe','Teilprojekt', 'Mitarbeiter',  'Abgabe-Termin', 'Status', 'Men&uuml;');
        $width = array('35%', '20%','20%', '10%', '10%','1%');
        $findcols = array('aufgabe','ap.aufgabe', 'mitarbeiter', 'abgabe', 'status', 'id');
        $searchsql = array('a.aufgabe','ap.aufgabe', 'adr.name', 'a.status', 'a.abgabe_bis');
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"#\" onclick=\"AufgabenEdit(%value%);\" ><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=FinalDialog(\"index.php?module=aufgaben&action=abschluss&id=%value%&sid=$id&referrer=projekt\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/haken.png\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=aufgaben&action=delete&id=%value%&sid=$id&referrer=projekt\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

        //            $menucol=9;
        
        // SQL statement

        $sql = "SELECT a.id, 
              if(a.prio,CONCAT('<b><font color=red>',a.aufgabe,'</font></b>'),a.aufgabe) as aufgabe, ap.aufgabe,
                adr.name as mitarbeiter,
                  if(a.abgabe_bis,DATE_FORMAT(abgabe_bis,'%d.%m.%Y'),'') as abgabe,
                        a.status, a.id
                          FROM  aufgabe a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id LEFT JOIN arbeitspaket ap ON ap.id=a.teilprojekt";

        // Fester filter
        
        // START EXTRA more
        $subwhere = [];
        $more_data1 = $this->app->Secure->GetGET("more_data1");
        if ($more_data1 == 1) $subwhere[] = " a.status='offen' ";

        $more_data2 = $this->app->Secure->GetGET("more_data2");
        if ($more_data2 == 1) $subwhere[] = " a.status='abgeschlossen' ";

        if($fteilprojektfilter) {
          $subwhere[] = " a.teilprojekt = '$fteilprojektfilter' ";
        }
        for ($j = 0;$j < count($subwhere);$j++) $tmp.= " AND " . $subwhere[$j];

        $where = " a.projekt='" . $id."'".$tmp;
        $count = "SELECT COUNT(a.id) FROM aufgabe a WHERE $where ";

        break;

      case "abrechnungzeit":
        $allowed['adresse'] = array('abrechnungzeit');
        $id = $this->app->Secure->GetGET('id');

        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#archiviert').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#kunden').click( function() { fnFilterColumn2( 0 ); } );");
        for ($r = 1;$r < 3;$r++) {
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

        // ENDE EXTRA checkboxen
        
        // headings

        $heading = array('', '', 'Aufgabe', 'Mitarbeiter', 'Von', 'Bis', 'Stunden','Projekt', 'Projektname', 'Art','Information','Status', 'Men&uuml;');
        $width = array('1%', '1%', '25%', '10%', '10%', '10%', '1%','5%','10%','5%','5%','1%', '1%');
        $findcols = array('open', 'auswahl', 'aufgabe', 'a.name', 'z.von', 'z.bis','TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600','p.abkuerzung','p.name',"CONCAT(if(z.produktion > 0 ,'Produktion',''),if(z.auftrag > 0 ,'Auftrag',''),if(z.arbeitspaket > 0 ,'Teilprojekt',''))","IF(z.produktion>0, pr.belegnr COLLATE utf8_general_ci, IF(z.auftrag>0, auf.belegnr COLLATE utf8_general_ci, IF(z.arbeitspaket>0, ap.aufgabe COLLATE utf8_general_ci, '')))", "if(z.abrechnen,if(z.abgerechnet!=1 AND z.abrechnen='1','offen','abgerechnet'),'abgeschlossen')", 'id');
        $searchsql = array("z.aufgabe","p.abkuerzung","CONCAT(if(z.produktion > 0 ,'Produktion',''),if(z.auftrag > 0 ,'Auftrag',''),if(z.arbeitspaket > 0 ,'Teilprojekt',''))","IF(z.produktion>0, pr.belegnr COLLATE utf8_general_ci, IF(z.auftrag>0, auf.belegnr COLLATE utf8_general_ci, IF(z.arbeitspaket>0, ap.aufgabe COLLATE utf8_general_ci, '')))",'a.name', "DATE_FORMAT(von, '%d.%m.%Y')", "DATE_FORMAT(bis, '%d.%m.%Y')", "if(z.abrechnen,if(z.abgerechnet!=1 AND z.abrechnen='1','offen','abgerechnet'),'abgeschlossen')",$this->app->erp->FormatPreis('TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600',2),'p.name');
        $defaultorder = 6;
        $defaultorderdesc = 1;
        $id = $this->app->Secure->GetGET("id");
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=zeiterfassung&action=create&id=%value%&back=adresse&back_id=$id#tabs-3\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=FinalDialog(\"index.php?module=adresse&action=abrechnungzeitabgeschlossen&id=$id&sid=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/haken.png\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse&action=abrechnungzeitdelete&id=$id&sid=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

        $numbercols = array(6);
        $datecols = array(4, 5);
        $alignright = array(7);

        //            $menucol=9;
       $sumcol = 7; 
        // SQL statement
              //CONCAT(LPAD(HOUR(TIMEDIFF(bis, von)),2,'0'),':',LPAD(MINUTE(TIMEDIFF(bis, von)),2,'0')) AS dauer,

        $sql = "SELECT SQL_CALC_FOUND_ROWS z.id, 
              '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,
              CONCAT('<input class=\"chcktbl\"  type=\"checkbox\" name=\"zeit[]\" value=\"',z.id,'\" ',if(z.abrechnen,if(z.abgerechnet!=1 AND z.abrechnen='1','checked',''),''),'>',if(z.abrechnen,'(A)','')) as auswahl,
              z.aufgabe as aufgabe, a.name as name, DATE_FORMAT(z.von, '%d.%m.%Y') as von, DATE_FORMAT(z.bis, '%d.%m.%Y') as bis, 
".$this->app->erp->FormatPreis("FORMAT(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600,2)")." as dauer,
                p.abkuerzung as projekt, p.name,
                CONCAT(if(z.produktion > 0 ,'Produktion',''),if(z.auftrag > 0 ,'Auftrag',''),if(z.arbeitspaket > 0 ,'Teilprojekt','')) as art,
                IF(z.produktion>0, pr.belegnr, IF(z.auftrag>0, auf.belegnr, IF(z.arbeitspaket>0, ap.aufgabe, ''))) AS information,
                if(z.abrechnen,if(z.abgerechnet!=1 AND z.abrechnen='1','offen','abgerechnet'),'abgeschlossen') as status,z.id  as id
                FROM zeiterfassung z LEFT JOIN adresse a ON a.id=z.adresse LEFT JOIN projekt p ON p.id=z.projekt
                LEFT JOIN produktion pr ON pr.id = z.produktion
                LEFT JOIN auftrag auf  ON auf.id = z.auftrag
                LEFT JOIN arbeitspaket ap on ap.id = z.arbeitspaket";

        // Fester filter
        
        // START EXTRA more

        
        //        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " z.abrechnen='1' ";

        $more_data1 = $this->app->Secure->GetGET("more_data1");
        
        if ($more_data1 == 1) {
          $subwhere[] = " (z.abgerechnet='1' OR z.abrechnen!='1') ";
        } else $subwhere[] = " z.abgerechnet!=1 ";


        if($this->app->Secure->GetGET("more_data2")=="1")
        {
          $subwhere[] = " z.aufgabe_id <=0 AND z.auftrag <=0 AND z.produktion <=0 AND z.arbeitsanweisung <=0 AND z.projekt <=0  ";
        }


        for ($j = 0;$j < count($subwhere);$j++) $tmp.= " AND " . $subwhere[$j];
        $where = " (z.adresse_abrechnung='" . $id . "' OR p.kunde='$id') $tmp";

        $count = "SELECT COUNT(z.id) FROM zeiterfassung z LEFT JOIN adresse a ON a.id=z.adresse LEFT JOIN projekt p ON p.id=z.projekt WHERE  $where";

        // gesamt anzahl
        $menucol = 12;
        $moreinfo = true;
        break;

    case "abrechnungszeitprojektdashboard":
        $tmp = '';
        $allowed['projekt'] = array('dashboard');
        $id = $this->app->Secure->GetGET('id');
        //$uid = (int)$this->app->User->GetParameter('projektdashboardartikelteilprojektfilter');
        // START EXTRA checkboxen
        //$this->app->Tpl->Add('JQUERYREADY', "$('#archiviert').click( function() { fnFilterColumn6( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#zeiterfassungabgeschlossene').click( function() { fnFilterColumn7( 0 ); } );");
        for ($r = 7;$r < 8;$r++) {
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
        $this->app->Tpl->Add('JQUERYREADY', "
        $(document).ready(function() {fnFilterColumn8( $('#teilprojektfilter').val() );});
        $('#teilprojektfilter').click( function() { fnFilterColumn8( $('#teilprojektfilter').val() ); } );$('#teilprojektfilter').change( function() { fnFilterColumn8(  $('#teilprojektfilter').val()); } );");

        for ($r = 8;$r < 9;$r++) {
          $this->app->Tpl->Add('JAVASCRIPT', '
                  function fnFilterColumn' . $r . ' ( i )
                  {
                  oMoreData' . $r . $name . ' = i;

                  $(\'#' . $name . '\').dataTable().fnFilter( 
                    \'\',
                    i, 
                    0,0
                    );
                  }
                  ');
        }
        
        
        
        $heading = array( 'Auswahl','Teilprojekt', 'Aufgabe', 'Mitarbeiter', 'Von', 'Bis', 'Stunden','Stundensatz','Kosten','Status', 'Men&uuml;');
        $width = array( '1%', '25%', '10%', '10%', '10%', '10%','5%','5%','5%','5%','5%','1%', '1%');
        $findcols = array( 'auswahl', 'arb.aufgabe','z.aufgabe', 'name', 'von', 'bis','dauer','stundensatz','kosten','status', 'z.id');
        $searchsql = array("z.aufgabe",'arb.aufgabe','z.aufgabe','a.name');
        $defaultorder = 6;
        $defaultorderdesc = 1;
        $alignright = array(7,8,9);
        $id = $this->app->Secure->GetGET("id");
        $menu = "";//<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=zeiterfassung&action=create&id=%value%&back=adresse&back_id=$id#tabs-3\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=FinalDialog(\"index.php?module=adresse&action=abrechnungzeitabgeschlossen&id=$id&sid=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/haken.png\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse&action=abrechnungzeitdelete&id=$id&sid=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

        $sumcol = array(7,9);


        $sql = "SELECT SQL_CALC_FOUND_ROWS z.id, 
              
              CONCAT('<input type=\"checkbox\" name=\"auswahl_',z.id,'\" id=\"auswahl_',z.id,'\" value=\"',z.id,'\">') as auswahl,
              arb.aufgabe as teilprojekt,
              z.aufgabe as aufgabe, a.name as name, z.von as von, z.bis as bis, 
                FORMAT(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600,2) as dauer,
                ifnull(z.stundensatz ,0 ) as stundensatz,FORMAT(ifnull(z.stundensatz ,0 ) * FORMAT(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600,2),2) as kosten,
                if(z.abgerechnet = 1,'abgerechnet','offen') as status,z.id  as id
                FROM zeiterfassung z LEFT JOIN arbeitspaket arb ON z.arbeitspaket = arb.id LEFT JOIN adresse a ON a.id=z.adresse ";
        //$more_data6 = $this->app->Secure->GetGET("more_data6");
        $more_data7 = $this->app->Secure->GetGET("more_data7");
        $more_data8 = $this->app->Secure->GetGET("more_data8");
        if($more_data8 > 0)$subwhere[] = " z.arbeitspaket = '$more_data8' ";
        /*if ($more_data6 == 1) {
          $subwhere[] = " (z.abgerechnet='1' OR z.abrechnen!='1') ";
        } else $subwhere[] = " z.abgerechnet!=1 ";*/
        if($more_data7 == 1)
        {
          $subwhere[] = " z.abgerechnet = 1 ";
        }else{
          $subwhere[] = " z.abgerechnet <> 1 ";
        }
        for ($j = 0;$j < count($subwhere);$j++) $tmp.= " AND " . $subwhere[$j];
        $where = "   z.projekt ='" . $id . "' $tmp";
        $count = "SELECT COUNT(z.id) FROM zeiterfassung z LEFT JOIN arbeitspaket arb ON z.arbeitspaket = arb.id WHERE $where";

        // gesamt anzahl
        $menucol = 11;
        $moreinfo = false;
    break;
      case "angebote":
        $allowed['angebot'] = array('list');


        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#angeboteoffen').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#angeboteheute').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#angeboteohneab').click( function() { fnFilterColumn3( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#angeboteabgelehnt').click( function() { fnFilterColumn4( 0 ); } );");
        for ($r = 1;$r < 5;$r++) {
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

        // ENDE EXTRA checkboxen
        
        // headings

        $heading = array('', '', 'Angebot', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Zahlung', 'Betrag (brutto)', 'Status', 'Men&uuml;');
        $width = array('1%', '1%', '1%', '10%', '10%', '40%', '5%', '1%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open', 'a.belegnr', 'a.belegnr', 'a.datum', 'adr.kundennummer', 'a.name', 'a.land', 'p.abkuerzung', 'a.zahlungsweise', 'a.gesamtsumme', 'a.status', 'id');
        $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')', 'a.anfrage','a.belegnr', 'adr.kundennummer', 'a.name', 'a.land', 'p.abkuerzung', 'a.zahlungsweise', 'a.status', "FORMAT(a.gesamtsumme,2{$extended_mysql55})", 'a.status', 'adr.freifeld1','a.internebezeichnung');
        $defaultorder = 12; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;

        if($this->app->erp->RechteVorhanden('angebot','summe'))
          $sumcol = 10;


        $rowcallback_gt = 1;

        $alignright = array('10');
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=angebot&action=edit&id=%value%\">"
          ."<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>"
          ."</td>"
          ."<td>"
          ."<a href=\"#\" onclick=DeleteDialog(\"index.php?module=angebot&action=delete&id=%value%\");>"
          ."<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>"
          ."</td>"
          ."<td>"
          ."<a href=\"#\" onclick=CopyDialog(\"index.php?module=angebot&action=copy&id=%value%\");>"
          ."<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>"
          ."</td>"
          ."<td>"
          ."<a href=\"index.php?module=angebot&action=pdf&id=%value%\">"
          ."<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>"
          ."</td>"
          ."<td>"
          ."<a href=\"#\" class=\"label-manager\" data-label-column-number=\"5\" data-label-reference-id=\"%value%\" data-label-reference-table=\"angebot\"><span class=\"label-manager-icon\"></span></a>"
          ."</td></tr></table>";
        $menucol = 11;

        $parameter = $this->app->User->GetParameter('table_filter_angebot');
        $parameter = base64_decode($parameter);
        $parameter = json_decode($parameter, true);

        $sql = "";

        $sql .= "
          SELECT SQL_CALC_FOUND_ROWS a.id,
          '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, 
          CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',a.id,'\" />') as auswahl,
          a.belegnr as belegnr, 
          DATE_FORMAT(a.datum,'%d.%m.%Y') as vom, 
          adr.kundennummer as kundennummer, 
          CONCAT(" . $this->app->erp->MarkerUseredit("a.name", "a.useredittimestamp") . ", if(a.internebezeichnung!='',CONCAT('<br><i style=color:#999>',a.internebezeichnung,'</i>'),'')) as name, 
          a.land as land, 
          p.abkuerzung as projekt, 
          a.zahlungsweise as zahlungsweise,  
          FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, 
          UPPER(a.status) as status, 
          a.id
        ";

        $sql .= "
          FROM 
            angebot a 
            LEFT JOIN projekt p ON p.id=a.projekt 
            LEFT JOIN adresse adr ON a.adresse=adr.id 
        ";
        if(isset($parameter['artikel']) && !empty($parameter['artikel'])) {
          $artikelid = $this->app->DB->Select("SELECT id FROM artikel where geloescht != 1 AND nummer != 'DEL' AND nummer != '' AND nummer = '".$this->app->DB->real_escape_string(reset(explode(' ',trim($parameter['artikel']))))."' LIMIT 1");
          if($artikelid)
          {
            $paramsArray[] = "ap.artikel = '" . $artikelid . "' ";
            $sql .= " INNER JOIN angebot_position ap ON a.id = ap.angebot ";
            $groupby = " GROUP BY a.id, p.id, adr.id ";
          }
        }
        // SQL statement
        /*
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, a.belegnr as belegnr, DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, 
                            adr.kundennummer as kundennummer, " . $this->app->erp->MarkerUseredit("a.name", "a.useredittimestamp") . " as name, a.land as land, p.abkuerzung as projekt, a.zahlungsweise as zahlungsweise,  
                            FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, UPPER(a.status) as status, a.id
                              FROM  angebot a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id ";
        */
        // Fester filter
        
        // START EXTRA more

        $more_data1 = $this->app->Secure->GetGET("more_data1");
        if ($more_data1 == 1) $subwhere[] = " a.status='freigegeben' ";

        $more_data2 = $this->app->Secure->GetGET("more_data2");
        if ($more_data2 == 1) $subwhere[] = " a.datum=CURDATE() ";

        $more_data3 = $this->app->Secure->GetGET("more_data3");
        if ($more_data3 == 1) $subwhere[] = " a.auftragid <= 0 AND a.status!='storniert'  AND a.status!='abgelehnt' AND a.status != 'beauftragt' ";
 
        $more_data4 = $this->app->Secure->GetGET("more_data4");
        if ($more_data4 == 1) $subwhere[] = " a.status='abgelehnt' ";


        $tmp = '';
        $csubwhere = !empty($subwhere)?count($subwhere):0;
        for ($j = 0;$j < $csubwhere;$j++) $tmp.= " AND " . $subwhere[$j];
        $where = " a.id!='' AND a.status!='angelegt' $tmp " . $this->app->erp->ProjektRechte();


        /* STAMMDATEN */
        if(!empty($parameter['kundennummer'])) {
          $paramsArray[] = " (a.kundennummer LIKE '%".$parameter['kundennummer']."%' OR adr.kundennummer LIKE '%".$parameter['kundennummer']."%') ";
        }

        if(!empty($parameter['name'])) {
          $paramsArray[] = "a.name LIKE '%".$parameter['name']."%' ";
        }

        if(!empty($parameter['ansprechpartner'])) {
          $paramsArray[] = "a.ansprechpartner LIKE '%".$parameter['ansprechpartner']."%' ";
        }

        if(!empty($parameter['abteilung'])) {
          $paramsArray[] = "a.abteilung LIKE '%".$parameter['abteilung']."%' ";
        }

        if(!empty($parameter['strasse'])) {
          $paramsArray[] = "a.strasse LIKE '%".$parameter['strasse']."%' ";
        }

        if(!empty($parameter['plz'])) {
          $paramsArray[] = "a.plz LIKE '".$parameter['plz']."%'";
        }

        if(!empty($parameter['ort'])) {
          $paramsArray[] = "a.ort LIKE '%".$parameter['ort']."%' ";
        }

        if(!empty($parameter['land'])) {
          $paramsArray[] = "a.land LIKE '%".$parameter['land']."' ";
        }

        if(!empty($parameter['ustid'])) {
          $paramsArray[] = "a.ustid LIKE '%".$parameter['ustid']."%' ";
        }

        if(!empty($parameter['telefon'])) {
          $paramsArray[] = "a.telefon LIKE '%".$parameter['telefon']."%' ";
        }

        if(!empty($parameter['email'])) {
          $paramsArray[] = "a.email LIKE '%".$parameter['email']."%' ";
        }

        /* XXX */
        if(!empty($parameter['datumVon'])) {
          $paramsArray[] = "a.datum >= '" . date('Y-m-d',strtotime($parameter['datumVon']))."' ";
        }

        if(!empty($parameter['datumBis'])) {
          $paramsArray[] = "a.datum <= '" . date('Y-m-d',strtotime($parameter['datumBis']))."' ";
        }

        if(!empty($parameter['projekt'])) {

          $projektData = $this->app->DB->SelectArr('
            SELECT
              *
            FROM
              projekt
            WHERE
              abkuerzung LIKE "' . $parameter['projekt'] . '"
          ');
          if(!empty($projektData)){
            $projektData = reset($projektData);
            $paramsArray[] = "a.projekt = '" . $projektData['id'] . "' ";
          }
        }

        if(!empty($parameter['belegnummer'])) {
          $paramsArray[] = "a.belegnr LIKE '".$parameter['belegnummer']."' ";
        }

        if(!empty($parameter['internebemerkung'])) {
          $paramsArray[] = "a.internebemerkung LIKE '%".$parameter['internebemerkung']."%' ";
        }

        if(!empty($parameter['aktion'])) {
          $paramsArray[] = "a.aktion LIKE '%".$parameter['aktion']."%' ";
        }

        if(!empty($parameter['freitext'])) {
          $paramsArray[] = "a.freitext LIKE '%".$parameter['freitext']."%' ";
        }

        if(!empty($parameter['zahlungsweise'])) {
          $paramsArray[] = "a.zahlungsweise LIKE '%".$parameter['zahlungsweise']."%' ";
        }

        if(!empty($parameter['status'])) {
          $paramsArray[] = "a.status LIKE '%".$parameter['status']."%' ";
        }

        if(!empty($parameter['versandart'])) {
          $paramsArray[] = "a.versandart LIKE '%".$parameter['versandart']."%' ";
        }

        if(!empty($parameter['betragVon'])) {
          $paramsArray[] = "a.gesamtsumme >= '" . $parameter['betragVon'] . "' ";
        }

        if(!empty($parameter['betragBis'])) {
          $paramsArray[] = "a.gesamtsumme <= '" . $parameter['betragBis'] . "' ";
        }
        // projekt, belegnummer, internetnummer, bestellnummer, transaktionsId, freitext, internebemerkung, aktionscodes

        if ($paramsArray) {
          $where .= ' AND ' . implode(' AND ', $paramsArray);
        }


        // gesamt anzahl
        $count = "SELECT COUNT(a.id) FROM angebot a WHERE a.status!='angelegt'";
        $moreinfo = true;
        break;
      case "mlmwartekonto":
        $allowed['adresse'] = array('multilevel');

        // headings
        
        // headings

        $heading = array('Bezeichnung', 'Betrag', 'Men&uuml;');
        $width = array('700px', '10%', '3%');
        $findcols = array('bezeichnung', 'betrag', 'id');
        $searchsql = array('bezeichnung', 'betrag', 'id');
        $id = $this->app->Secure->GetGET("id");
        $menu = "<a href=\"index.php?module=adresse&action=multilevel&cmd=edit&id=$id&sid=%value%#tabs-2\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\"></a><a href=\"index.php?module=adresse&action=multilevel&cmd=delete&id=$id&sid=%value%#tabs-2\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\" ></a>";

        //            $menucol=3;
        
        // SQL statement

        $sql = "SELECT SQL_CALC_FOUND_ROWS m.id, m.bezeichnung, m.betrag, m.id FROM mlm_wartekonto m "; //LEFT JOIN artikel a ON a.id=m.artikel ";

        
        // Fester filter

        $where = " m.adresse='$id' AND m.abgerechnet=0 ";

        // gesamt anzahl
        $count = "SELECT COUNT(id) FROM mlm_wartekonto WHERE adresse='$id' AND abgerechnet=0";
        break;
      case "auftraegeinbearbeitung":
        $allowed['auftrag'] = array('create', 'list');
        $auftragmarkierenegsaldo = $this->app->erp->Firmendaten('auftragmarkierenegsaldo');
        // headings
        $heading = array('', 'Auftrag', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Zahlung', 'Betrag (brutto)', 'Monitor', 'Men&uuml;');
        $width = array('1%', '10%', '10%', '10%', '31%', '5%', '1%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('open', 'a.belegnr', 'a.datum', 'if(a.lieferantenauftrag=1,adr.lieferantennummer,adr.kundennummer)', 'a.name', 'a.land', 'p.abkuerzung', 'a.zahlungsweise', 'a.gesamtsumme', 'a.status', 'a.id');
        $searchsql = array('a.datum', 'a.belegnr', 'a.ihrebestellnummer', 'internet', "if(a.lieferantenauftrag=1,adr.lieferantennummer,adr.kundennummer)", 'a.name', 'a.land', 'p.abkuerzung', 'a.zahlungsweise', 'a.status', 'a.gesamtsumme');
        $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')', 'a.belegnr', 'a.ihrebestellnummer', 'internet', 'adr.kundennummer', 'a.name', 'a.land', 'p.abkuerzung', 'a.zahlungsweise', 'a.status', "FORMAT(a.gesamtsumme,2{$extended_mysql55})", 'adr.freifeld1','a.internebezeichnung');
        $alignright = array('9');
        $defaultorder = 11; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=auftrag&action=edit&id=%value%\">";
        $menu .= "<img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=auftrag&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=auftrag&action=pdf&id=%value%\">";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"5\" data-label-reference-id=\"%value%\" data-label-reference-table=\"auftrag\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";
        $menucol = 10;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, 
                                     CONCAT(if(belegnr='','ENTWURF',belegnr),if(projektfiliale > 0,' (F)','')) as belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as vom, if(a.lieferantenauftrag=1,adr.lieferantennummer,adr.kundennummer) as kunde,
                                       CONCAT(" . $this->app->erp->MarkerUseredit("a.name", "a.useredittimestamp") . ",if(a.internebemerkung='','',' <font color=red><strong>*</strong></font>'),if(a.freitext='','',' <font color=blue><strong>*</strong></font>'),if(a.internebezeichnung!='',CONCAT('<br><i style=color:#999>',a.internebezeichnung,'</i>'),'')) as name, 
                                         a.land as land,LEFT(UPPER( p.abkuerzung),10) as projekt, 
                                         ".($auftragmarkierenegsaldo?"CONCAT('<span',if(a.status = 'angelegt' or a.status = 'storniert' OR isnull(a.saldogeprueft) OR ( -(a.saldo) <= (if(isnull(a.skontobetrag),a.gesamtsumme * ( a.zahlungszielskonto) / 100.0,a.skontobetrag) )) OR (a.vorabbezahltmarkieren = 1 and a.zahlungsweise = 'vorkasse'),'',' style=\"color:red;\" '),'>',":'')."a.zahlungsweise".($auftragmarkierenegsaldo?",'<span>')":"")." as zahlungsweise,  
                                         FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag,  (" . $this->IconsSQL() . ")  as icons, a.id
                                           FROM  auftrag a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
        $where = " a.id!='' AND a.status='angelegt' " . $this->app->erp->ProjektRechte('p.id', true, 'a.vertriebid');

        // gesamt anzahl
        $count = "SELECT COUNT(a.id) FROM auftrag a WHERE a.status='angelegt' ";
        $moreinfo = true;
        $this->app->erp->RunHook('auftraege_tablesearch', 2, $sql, $where);
        break;
      case "auftraegeoffene":
        $allowed['auftrag'] = array('positionstabelle', 'list');
        $auftragmarkierenegsaldo = $this->app->erp->Firmendaten('auftragmarkierenegsaldo');
        // headings
        $heading = array('', '', 'Auftrag', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Zahlung', 'Betrag (brutto)', 'Monitor', 'Men&uuml;');
        $width = array('1%', '1%', '10%', '12%', '10%', '35%', '1%', '1%', '1%', '1%', '1%', '1%', '1%', '1%');
        $findcols = array('a.id','a.id', 'a.belegnr', 'r.datum', 'kundennummer', 'kunde', 'land', 'p.abkuerzung', 'zahlungsweise', 'a.gesamtsumme', 'a.status', 'a.id', 'a.id');
        $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')', 'a.belegnr', 'a.ihrebestellnummer', 'internet', 'adr.kundennummer', 'a.name', 'a.land', 'p.abkuerzung', 'a.zahlungsweise', 'adr.freifeld1', 'a.status', "FORMAT(a.gesamtsumme,2{$extended_mysql55})");
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=auftrag&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=auftrag&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"index.php?module=auftrag&action=pdf&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a></td></tr></table>";
        $defaultorder = 11;
        $defaultorderdesc = 1;
        $menucol = 11;

        // SQL statement
        $sql =
            "SELECT SQL_CALC_FOUND_ROWS 
            a.id,
            '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' AS `open`, 
            CONCAT(
              '<!--',if(a.autoversand='1' AND a.vorkasse_ok='1' AND a.liefertermin_ok='1' AND a.porto_ok='1' AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1','checked',''),'-->
              <input type=\"checkbox\" name=\"auftraegemarkiert[]\" value=\"',a.id,'\"',
              IF(
                a.autoversand 
                AND a.vorkasse_ok = '1'
                AND a.liefertermin_ok = '1' 
                AND a.porto_ok = '1' 
                AND a.lager_ok = '1' 
                AND a.check_ok = '1' 
                AND a.ust_ok,
                'checked',
                ''
              ),
              '>'
            ) AS `versand`, 
            CONCAT(
              a.belegnr,
              IF(a.autoversand,'','')
            ), 
            a.datum AS `vom`, 
            IF(
              a.lieferantenauftrag = 1,
              adr.lieferantennummer,
              adr.kundennummer
            ) AS `kundennummer`, 
            CONCAT(
              " . $this->app->erp->MarkerUseredit("a.name", "a.useredittimestamp") . ",
              IF(
                a.internebemerkung='',
                '',
                ' <font color=red><strong>*</strong></font>',
                IF(
                  a.freitext='',
                  '',
                  ' <font color=blue><strong>*</strong></font>'
                )
              ),
              IF(
                a.abweichendelieferadresse = 1,
                CONCAT(
                  '<br><i style=color:#999>Abw. Lieferadr.: ',
                  a.liefername,', ',
                  a.lieferstrasse,', ',
                  a.lieferland,'-',
                  a.lieferplz,' ',
                  a.lieferort,
                  '</i>'
                ),
                ''
              )
            ) AS `kunde`, 
            a.land AS `land`, 
            p.abkuerzung AS `projekt`, 
            ".(
                $auftragmarkierenegsaldo ?
                "CONCAT(
                  '<span',
                  IF(
                    a.status = 'angelegt' 
                    OR a.status = 'storniert' 
                    OR ISNULL(a.saldogeprueft) 
                    OR ( -(a.saldo) <= (IF(ISNULL(a.skontobetrag), a.gesamtsumme * ( a.zahlungszielskonto) / 100.0, a.skontobetrag))) 
                    OR (a.vorabbezahltmarkieren = 1 AND a.zahlungsweise = 'vorkasse'),
                    '',
                    ' style=\"color:red;\" '),
                    '>'," :
                    ''
            )."a.zahlungsweise".($auftragmarkierenegsaldo ? ",'<span>')" : "")." AS `zahlungsweise`,  
            FORMAT(a.gesamtsumme,2{$extended_mysql55}) AS `betrag`, 
            (" . $this->IconsSQL() . ")  AS `icons`,
            a.id 
            FROM  `auftrag` AS `a` 
            LEFT JOIN `projekt` AS `p` ON p.id = a.projekt 
            LEFT JOIN `adresse` AS `adr` ON a.adresse = adr.id";

        // Fester filter
        $where =
          " a.id!='' 
          AND (a.belegnr !=0 OR a.belegnr != '') 
          AND a.status = 'freigegeben' 
          AND a.inbearbeitung = 0 
          AND a.nachlieferung != '1'  
          AND a.vorkasse_ok = '1' 
          AND a.porto_ok = '1' 
          AND a.lager_ok = '1' 
          AND a.check_ok = '1'
          AND a.ust_ok = '1' " .
          $this->app->erp->ProjektRechte('p.id', true, 'a.vertriebid');

        // gesamt anzahl
        $count =
          "SELECT COUNT(a.id) 
          FROM `auftrag` AS `a`
          WHERE  a.id != '' 
            AND (a.belegnr != 0 OR a.belegnr != '') 
            AND a.status = 'freigegeben'
            AND a.inbearbeitung = 0 
            AND a.nachlieferung != '1' 
            AND a.vorkasse_ok = '1' 
            AND a.porto_ok = '1' 
            AND a.lager_ok = '1' 
            AND a.check_ok = '1' 
            AND a.ust_ok = '1'";
        
        $moreinfo = true;
        break;
      case "arbeitsnachweiseprojekt":
        $allowed['projekt'] = array('arbeitsnachweise');

        // headings
        $heading = array('Datum', 'Dauer', 'Teilprojekt/Aufgabe', 'Men&uuml;');
        $width = array('10%', '10%', '75%', '5%');
        $findcols = array('Datum', 'Dauer', 'aufgabe', 'id');
        $searchsql = array('z.id', 'z.bis');
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=projekt&action=arbeitsnachweispdf&date=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";
        $menucol = 11;

        // SQL statement
        
        //'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,

        $sql = "SELECT
                                     'leer',
                                     DATE_FORMAT(z.bis, '%d.%m.%Y') AS Datum, SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600) as Dauer, ap.aufgabe, CONCAT(DATE_FORMAT(z.bis, '%d.%m.%Y'),'-',ap.id) as id

                                       FROM zeiterfassung z LEFT JOIN arbeitspaket ap ON ap.id=z.arbeitspaket
                                       ";

        // Fester filter
        
        // START EXTRA more

        
        //        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " a.status='freigegeben' ";

        
        //        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " a.datum=CURDATE() AND a.status='freigegeben'";

        for ($j = 0;$j < count($subwhere);$j++) $tmp.= " AND " . $subwhere[$j];
        $id = $this->app->Secure->GetGET("id");
        $where = " ap.aufgabe IS NOT NULL $tmp AND ap.projekt='$id' GROUP by Datum,ap.id ";

        // gesamt anzahl
        $count = "SELECT COUNT(z.id) FROM zeiterfassung z";

        //    $moreinfo = true;
        break;
      case "arbeitspakete":
        $this->app->Tpl->Add('JQUERYREADY', "$('#altearbeitspaket').click( function() { fnFilterColumn1( 0 ); } );");
        for ($r = 1;$r < 2;$r++) {
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

        // headings
        $heading = array('Art', 'Aufgabe', 'Verantwortlicher', 'Abgabe', 'geplant', 'gebucht', 'Status', 'Men&uuml;');
        $width = array('5%', '25%', '25%', '3%', '3%', '3%', '1%', '10%');
        $findcols = array('art', 'aufgabe', 'name', 'abgabedatum', 'geplant', 'gebucht', 'status', 'id');
        $searchsql = array('adr.name', 'ap.aufgabe', 'ap.abgabedatum', 'ap.status');
        $id = $this->app->Secure->GetGET("id");
        $menu = "<a href=\"index.php?module=projekt&action=arbeitspaketeditpopup&id=%value%&sid=$id\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DisableDialog(\"index.php?module=projekt&action=arbeitspaketdisable&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/haken.png\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=projekt&action=arbeitspaketdelete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=projekt&action=arbeitspaketcopy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>";

        // SQL statement
        
        //'<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,

        $sql = "SELECT  SQL_CALC_FOUND_ROWS  ap.id, 
                                     if(ap.abgenommen,CONCAT('<i>',UCASE(ap.art),'</i>'),UCASE(ap.art)) as art, 
                                       if(ap.abgenommen,CONCAT('<i>',ap.aufgabe,'</i>'),ap.aufgabe) as aufgabe, 
                                         if(ap.abgenommen,CONCAT('<i>',adr.name,'</i>'),adr.name) as name, 
                                           if(ap.abgenommen,CONCAT('<i>',ap.abgabedatum,'</i>'),ap.abgabedatum) as abgabedatum, 
                                             if(ap.abgenommen,CONCAT('<i>',ap.zeit_geplant,'</i>'),ap.zeit_geplant) as geplant, 
                                               if(ap.abgenommen,CONCAT('<i>',

                                                     (SELECT FORMAT(SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von)))/3600,2) FROM zeiterfassung z WHERE z.arbeitspaket=ap.id)


                                                     ,'</i>'),

                                                   (SELECT FORMAT(SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von)))/3600,2) FROM zeiterfassung z WHERE z.arbeitspaket=ap.id)

                                                 ) as gebucht,
                                                 ap.status as status,
                                                   ap.id 
                                                     FROM arbeitspaket ap LEFT JOIN adresse adr ON ap.adresse=adr.id  ";
        $more_data1 = $this->app->Secure->GetGET("more_data1");
        
        if ($more_data1 == 1) $subwhere[] = " OR ( ap.abgenommen='1')  ";
        for ($j = 0;$j < count($subwhere);$j++) $tmp.= "  " . $subwhere[$j];

        //              if($tmp!="")$tmp .= " AND e.geloescht='1' ";
        
        // Fester filter

        $where = "ap.projekt='$id' AND (ap.geloescht='0' OR ap.geloescht IS NULL) AND ap.abgenommen!='1'$tmp";

        // Fester filter
        
        //            $where = "e.artikel='$id' AND e.geloescht='0' ";

        
        // gesamt anzahl

        $count = "SELECT COUNT(ap.id) FROM arbeitspaket ap WHERE ap.projekt='$id' AND (ap.geloescht='0' OR  ap.geloescht IS NULL)";

        //                      $menucol = 6;
        
        //      $moreinfo = true;

        break;

      case "einkaufspreise":
        $allowed['artikel'] = array('einkauf');
        $this->app->Tpl->Add('JQUERYREADY', "$('#alteeinkaufspreise').click( function() { fnFilterColumn1( 0 ); } );");
        $defaultorder = 4; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 0;
        for ($r = 1;$r < 2;$r++) {
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

        // headings
        $heading = array('Lieferant', 'Bezeichnung', 'Bestellnummer', 'ab', 'VPE', 'Preis', 'W&auml;hrung', 'bis', 'Rahmenvert.', 'Men&uuml;');
        $width = array('35%', '20%', '3%', '3%', '1%', '1%', '1%', '1%', '10%', '10%');
        $findcols = array('adr.name', 'e.bezeichnunglieferant', 'e.bestellnummer', 'e.ab_menge', 'e.vpe', 'e.preis', 'e.waehrung', 'e.gueltig_bis', "if(e.rahmenvertrag='1',CONCAT(e.rahmenvertrag_menge,' / ',IFNULL((SELECT trim(SUM(bp.menge)) FROM bestellung b LEFT JOIN bestellung_position bp ON bp.bestellung=b.id WHERE b.datum >=e.rahmenvertrag_von AND b.datum <= e.rahmenvertrag_bis AND b.status!='storniert' AND e.adresse=b.adresse AND bp.artikel=e.artikel),0)),'-')", 'id');
        $searchsql = array('adr.name', 'e.bezeichnunglieferant', 'e.bestellnummer', 'e.ab_menge', 'e.vpe', $this->FormatPreis('e.preis'), 'e.waehrung', "DATE_FORMAT(e.gueltig_bis,'%d.%m.%Y')", "if(e.rahmenvertrag='1',CONCAT(e.rahmenvertrag_menge,' / ',IFNULL((SELECT trim(SUM(bp.menge)) FROM bestellung b LEFT JOIN bestellung_position bp ON bp.bestellung=b.id WHERE b.datum >=e.rahmenvertrag_von AND b.datum <= e.rahmenvertrag_bis AND b.status!='storniert' AND e.adresse=b.adresse AND bp.artikel=e.artikel),0)),'-')");
        
        $menu = '<a href="javascript:;" onclick="EinkaufspreiseEdit(%value%);">';
        //$menu = "<a href=\"index.php?module=artikel&action=einkaufeditpopup&id=%value%\">";
          $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>" . "&nbsp;";
        $menu .= "<a href=\"#\" onclick=DisableDialog(\"index.php?module=artikel&action=einkaufdisable&id=%value%\");>";
          $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/disable.png\" border=\"0\">";
        $menu .= "</a>" . "&nbsp;";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=einkaufdelete&id=%value%\");>";
          $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>" . "&nbsp;";
        $menu .= "<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=einkaufcopy&id=%value%\");>";
          $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
        $menu .= "</a>";

        // SQL statement

        $datecols = array(7);
        $numbercols = array(5);

        $alignright=array(4,5,6,7,8,9);
        
          $sql = "SELECT SQL_CALC_FOUND_ROWS e.id, CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',adr.id,'\" target=\"_blank\">',adr.name,'</a>') as lieferant, e.bezeichnunglieferant, e.bestellnummer, 
                                       ".$this->app->erp->FormatMenge('e.ab_menge')." as ab_menge ,e.vpe as vpe, ".$this->FormatPreis('e.preis')." as preis,e.waehrung as waehrung, if(e.gueltig_bis='0000-00-00','-',DATE_FORMAT(e.gueltig_bis, '%d.%m.%Y')) as gueltig_bis, 
                                        if(e.rahmenvertrag='1',CONCAT(e.rahmenvertrag_menge,' / ',IFNULL((SELECT trim(SUM(bp.menge)) FROM bestellung b LEFT JOIN bestellung_position bp ON bp.bestellung=b.id WHERE b.datum >=e.rahmenvertrag_von AND b.datum <= e.rahmenvertrag_bis AND b.status!='storniert' AND e.adresse=b.adresse AND bp.artikel=e.artikel),0)),'-') as rahmenvertrag, e.id as menu
                                       FROM  einkaufspreise e LEFT JOIN projekt p ON p.id=e.projekt LEFT JOIN adresse adr ON e.adresse=adr.id  ";
       
        $more_data1 = $this->app->Secure->GetGET("more_data1");
        //              if($tmp!="")$tmp .= " AND e.geloescht='1' ";
        
        // Fester filter

        
        if ($more_data1 == 1) 
          $where = "e.artikel='$id' AND e.geloescht='0' ";
        else
          $where = "e.artikel='$id'  AND e.geloescht='0' AND (e.gueltig_bis>NOW() OR e.gueltig_bis='0000-00-00') ";

        // Fester filter
        //            $where = "e.artikel='$id' AND e.geloescht='0' ";

        
        // gesamt anzahl

        $count = "SELECT COUNT(e.id) FROM einkaufspreise e WHERE $where";
        break;
    
      case "artikel_eigenschaften":
        $allowed['artikel'] = array('eigenschaften');
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 0;
        $alignright = array(3,4,5);
        $heading = array('Eigenschaft', 'Wert', 'Einheit (Optional)', 'Men&uuml;');
        $width = array('32%', '32%', '32', '8%');
        $findcols = array('e.name', 'ew.wert', 'ew.einheit', 'ew.id');
        $searchsql = array('e.name', 'ew.wert', 'ew.einheit');
        $menu = "<a href=\"#\" onclick=\"editeigenschaft(%value%)\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=\"copyeigenschaft(%value%)\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>". "&nbsp;<a href=\"#\" onclick=\"deleteeigenschaft(%value%)\";><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS ew.id, e.name,  CONCAT(ew.wert,'&nbsp;&nbsp;'),
                                     ew.einheit, ew.id FROM artikeleigenschaften e INNER JOIN artikeleigenschaftenwerte ew ON e.id = ew.artikeleigenschaften";

        $where = " ew.artikel='$id' ";

        // gesamt anzahl
        $count = "SELECT COUNT(ew.id) FROM artikeleigenschaften e INNER JOIN artikeleigenschaftenwerte ew ON e.id = ew.artikeleigenschaften WHERE $where ";
        break;
      break;
      case "eigenschaften":
        $allowed['artikel'] = array('eigenschaften');
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 0;
        $alignright = array(3,4,5);
        $heading = array('Hauptkategorie', 'Unterkategorie (Optional)', 'Wert', 'Einheit (Optional)', 'Men&uuml;');
        $width = array('15%', '15%', '12%', '8%', '10%');
        $findcols = array('e.hauptkategorie', 'e.unterkategorie', 'e.wert', 'e.einheit', 'e.id');
        $searchsql = array('e.hauptkategorie', 'e.unterkategorie', 'e.wert', 'e.einheit');
        $menu = "<a href=\"index.php?module=artikel&action=eigenschafteneditpopup&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=eigenschaftencopy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>". "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=eigenschaftendelete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS e.id, e.hauptkategorie, e.unterkategorie, CONCAT(e.wert,'&nbsp;&nbsp;'),
                                     e.einheit, e.id FROM eigenschaften e ";

        $where = "e.artikel='$id' ";

        // gesamt anzahl
        $count = "SELECT COUNT(e.id) FROM eigenschaften e WHERE e.artikel='$id' ";
        break;
   
      case "verkaufspreise":
        $allowed['artikel'] = array('verkauf');
        $this->app->Tpl->Add('JQUERYREADY', "$('#alteverkaufspreise').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#nurkunde').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#nurgruppe').click( function() { fnFilterColumn3( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#nurstandard').click( function() { fnFilterColumn4( 0 ); } );");
        $defaultorder = 3; //Optional wenn andere Reihenfolge gewuenscht

        $alignright=array(3,4,5,6,7);

        $defaultorderdesc = 0;
        for ($r = 1;$r < 5;$r++) {
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
        $heading = array('Kunde/Gruppe', 'Hinweis', 'ab', 'Preis', 'W&auml;hrung', 'G&uuml;ltig ab', 'G&uuml;ltig bis', 'Men&uuml;');
        $width = array('40%', '15%', '10%', '5%', '10%', '10%','15%');
        $findcols = array("if(v.art='Kunde',if(v.adresse='' or v.adresse=0,'Standardpreis',CONCAT(adr.kundennummer,' ',adr.name)),CONCAT(g.name,' ',g.kennziffer))", 'hinweis', 'v.ab_menge', 'v.preis', 'v.waehrung','gueltig_ab','gueltig_bis', 'id');
        $searchsql = array("if(v.art='Kunde',if(v.adresse='' or v.adresse=0,'Standardpreis',CONCAT(adr.kundennummer,' ',adr.name)),CONCAT(g.name,' ',g.kennziffer))",'adr.name', 'g.name', $this->app->erp->FormatMenge('v.ab_menge'), 'v.waehrung',$this->FormatPreis('v.preis'),"DATE_FORMAT(v.gueltig_bis,'%d.%m.%Y')","DATE_FORMAT(v.gueltig_ab,'%d.%m.%Y')");
        $menu = "<table cellpadding=0 cellspacing=0>";
          $menu .= "<tr>";
            $menu .= "<td nowrap>";
              $menu .= '<a href="javascript:;" onclick="VerkaufspreiseEdit(%value%);">';
              //$menu .= "<a href=\"index.php?module=artikel&action=verkaufeditpopup&id=%value%\">";
                $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
              $menu .= "</a>" . "&nbsp;";
              $menu .= "<a href=\"#\" onclick=DisableDialog(\"index.php?module=artikel&action=verkaufdisable&id=%value%\");>";
                $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/disable.png\" border=\"0\">";
              $menu .= "</a>" . "&nbsp;";
              $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=verkaufdelete&id=%value%\");>";
                $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
              $menu .= "</a>" . "&nbsp;";
              $menu .= "<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=verkaufcopy&id=%value%\");>";
                $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
              $menu .= "</a>";
            $menu .= "</td>";
          $menu .= "</tr>";
        $menu .= "</table>";

        $numbercols = array(2,3);

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS v.id, 
                            if(v.art='Kunde',if(v.adresse='' or v.adresse=0,'Standardpreis',CONCAT(adr.kundennummer,' ',adr.name)),CONCAT(g.name,' ',g.kennziffer)) as kunde,  
                             if(v.adresse > 0 OR v.gruppe >0,'','') as hinweis,
                                ".$this->app->erp->FormatMenge('v.ab_menge')." as ab_menge, ".$this->FormatPreis('v.preis')." as preis, v.waehrung, DATE_FORMAT(v.gueltig_ab, '%d.%m.%Y') as gueltig_ab, DATE_FORMAT(v.gueltig_bis, '%d.%m.%Y') as gueltig_bis, v.id as menu
                                 FROM  verkaufspreise v LEFT JOIN adresse adr ON v.adresse=adr.id  LEFT JOIN gruppen g ON g.id=v.gruppe ";
        $more_data1 = $this->app->Secure->GetGET("more_data1");

           // kunde
        $more_data2 = $this->app->Secure->GetGET("more_data2");
        if($more_data2=="1") $subwhere[] = " v.adresse > 0 AND v.gruppe <= 0 ";

        //Gruppe
        $more_data3 = $this->app->Secure->GetGET("more_data3");
        if($more_data3=="1") $subwhere[] = " v.gruppe > 0 AND v.adresse<=0 ";
        
        //listenpreise
        $more_data4 = $this->app->Secure->GetGET("more_data4");
        if($more_data4=="1") $subwhere[] = " v.adresse = 0 AND v.gruppe=0 ";

        for ($j = 0;$j < count($subwhere);$j++) $tmp.= "  AND " . $subwhere[$j];
 
        if ($more_data1 == 1) 
          $where = "v.artikel='$id' AND v.geloescht = 0 ".$tmp; 
        else
          $where = "v.artikel='$id'  AND v.geloescht='0' AND (v.gueltig_bis>NOW() OR v.gueltig_bis='0000-00-00') ".$tmp;




        // gesamt anzahl
        $count = "SELECT COUNT(v.id) FROM verkaufspreise v WHERE $where";
        break;

      case "projektzeiterfassung":
        $allowed['projekt'] = array('zeit', 'arbeitspaket');
        $this->app->Tpl->Add('JQUERYREADY', "$('#altearbeitspaket').click( function() { fnFilterColumn1( 0 ); } );");
        for ($r = 1;$r < 2;$r++) {
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

        // headings
        $heading = array('','', 'Art', 'Bezeichnung', 'Verantwortlicher', 'Abgabe', 'SOLL', 'IST','Status','Monitor', 'Men&uuml;');
        $width = array('1%', '5%', '25%', '25%', '3%', '8%', '3%','1%', '10%','5%',);
        $findcols = array('open', 'art', 'bezeichnung', 'name', 'abgabedatum', 'geplant', 'gebucht','abgeschlossen', 'status', 'id');
        $searchsql = array('adr.name', 'ap.aufgabe', 'ap.abgabedatum', 'ap.status',"if(ap.art='material' OR ap.kosten_geplant!=0,CONCAT(FORMAT(ap.kosten_geplant,2{$extended_mysql55}),' &euro;'),CONCAT(ap.zeit_geplant,' h'))","CONCAT((SELECT FORMAT(SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von)))/3600,2) FROM zeiterfassung z WHERE z.arbeitspaket=ap.id),' h')");
        $id = $this->app->Secure->GetGET("id");

        $alignright = array(6,7);

        $menu = "<a href=\"index.php?module=projekt&action=arbeitspaketeditpopup&id=%value%&sid=$id\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "<!--&nbsp;<a href=\"#\" onclick=DisableDialog(\"index.php?module=projekt&action=arbeitspaketdisable&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/haken.png\" border=\"0\"></a>-->" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=projekt&action=arbeitspaketdelete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=projekt&action=arbeitspaketcopy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>";

        // SQL statement
        $sql = "SELECT  SQL_CALC_FOUND_ROWS  ap.id, 
                CONCAT('<input class=\"chcktbl\"  type=\"checkbox\" name=\"zeit[]\" value=\"',ap.id,'\">') as auswahl,
                                     '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,
                UCASE(ap.art) as art,
                ap.aufgabe as 'Bezeichnung',
                adr.name as name,
                ap.abgabedatum as abgabedatum,
                if(ap.art='material' OR ap.kosten_geplant!=0,CONCAT(FORMAT(ap.kosten_geplant,2{$extended_mysql55}),' &euro;'),CONCAT(ap.zeit_geplant,' h')) as geplant, 
                CONCAT((SELECT FORMAT(SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von)))/3600,2) FROM zeiterfassung z WHERE z.arbeitspaket=ap.id),' h')
                as gebucht,
                ap.status as status, '<img src=./themes/new/images/lagerstop.png><img src=./themes/new/images/vorkassego.png><img src=./themes/new/images/kreditlimitgo.png>' as monitor2,
                ap.id 
                FROM arbeitspaket ap LEFT JOIN adresse adr ON ap.adresse=adr.id  ";

        $more_data1 = $this->app->Secure->GetGET("more_data1");
        if ($more_data1 == 1) $subwhere[] = " AND ap.status='abgerechnet' ";
        else $subwhere[] = " AND ap.status!='abgerechnet' ";

        for ($j = 0;$j < count($subwhere);$j++) $tmp.= "  " . $subwhere[$j];

        
        // Fester filter
        $where = "ap.projekt='$id' AND (ap.geloescht='0' OR ap.geloescht IS NULL) $tmp";

        // gesamt anzahl
        $count = "SELECT COUNT(ap.id) FROM arbeitspaket ap WHERE $where";
        $menucol = 10;
        $moreinfoaction = 'arbeitspaket';
        //      $moreinfo = true;
        $moreinfo = true;
        break;
      case "zeiterfassungmitarbeiter":
        $allowed['adresse'] = array('zeiterfassung');

        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#offen').click( function() { fnFilterColumn1( 0 ); } );");

        //$this->app->Tpl->Add('JQUERYREADY',"$('#abrechnung').click( function() { fnFilterColumn2( 0 ); } );");
        for ($r = 1;$r < 2;$r++) {
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

        // ENDE EXTRA checkboxen
        
        // headings

        
        //$heading =  array('','A','Datum','Von','Bis','Dauer','Mitarbeiter','Tätigkeit','Projekt','Men&uuml;');

        $heading = array('', 'Datum', 'Von', 'Bis', 'Dauer', 'Mitarbeiter', 'Tätigkeit', 'Projekt', 'Men&uuml;');

        //$width   =  array('1%','1%','1%','1%','1%','5%','20%','40%','10%','1%');
        $width = array('1%', '1%', '1%', '1%', '5%', '20%', '40%', '10%', '1%');

        //$findcols = array('open','Auswahl','z.von','von','bis','Dauer','Mitarbeiter','id');
        $findcols = array('open', 'z.von', 'z.von', 'z.bis', "CONCAT(LPAD(HOUR(TIMEDIFF(z.bis, z.von)),2,'0'),':',LPAD(MINUTE(TIMEDIFF(z.bis, z.von)),2,'0'))", 'a.name',"if(z.adresse_abrechnung!=0,CONCAT('<i>Kunde: ',b.name,' (',b.kundennummer,')</i><br>',z.aufgabe),z.aufgabe)",'p.abkuerzung','z.id');
        $searchsql = array('z.id', "DATE_FORMAT(z.bis, '%d.%m.%Y')", "DATE_FORMAT(z.von,'%H:%i')", "DATE_FORMAT(z.bis,'%H:%i')", "CONCAT(LPAD(HOUR(TIMEDIFF(z.bis, z.von)),2,'0'),':',LPAD(MINUTE(TIMEDIFF(z.bis, z.von)),2,'0'))", 'z.aufgabe', 'a.name', 'p.abkuerzung');
        $defaultorder = 9;
        $defaultorderdesc = 1;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=zeiterfassung&action=create&id=%value%&back=zeiterfassungmitarbeiter&sid=$id\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=zeiterfassung&action=listuser&do=stornieren&id=$id&lid=%value%&back=zeiterfassungmitarbeiter\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        //CONCAT('<input type=\"checkbox\">') as auswahl,
        
        //$menucol=9;

        $datecols = array(1);

        $menucol = 8;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS z.id,
                                           '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,

                                           DATE_FORMAT(z.bis, GET_FORMAT(DATE,'EUR')) AS Datum, 
                                           DATE_FORMAT(z.von,'%H:%i') as von, DATE_FORMAT(z.bis,'%H:%i') as bis,
                                           CONCAT(LPAD(HOUR(TIMEDIFF(z.bis, z.von)),2,'0'),':',LPAD(MINUTE(TIMEDIFF(z.bis, z.von)),2,'0')) AS Dauer,

                                           a.name as Mitarbeiter,
                                           if(z.adresse_abrechnung!=0,CONCAT('<i>Kunde: ',b.name,' (',b.kundennummer,')</i><br>',z.aufgabe),z.aufgabe) as Taetigkeit,
                                             p.abkuerzung,
                                               z.id

                                                 FROM zeiterfassung z 
                                                 LEFT JOIN adresse a ON a.id=z.adresse 
                                                 LEFT JOIN adresse b ON b.id=z.adresse_abrechnung
                                                 LEFT JOIN projekt p ON p.id=z.projekt 
                                                 LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id";

        // Fester filter
        
        // START EXTRA more

        $more_data1 = $this->app->Secure->GetGET("more_data1");
        
        if ($more_data1 == 1) $subwhere[] = " z.abrechnen='1' AND z.abgerechnet!='1' ";

        //        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " a.datum=CURDATE() AND a.status='freigegeben'";
        for ($j = 0;$j < count($subwhere);$j++) $tmp.= " AND " . $subwhere[$j];
        $where = " z.id!='' AND z.adresse='" . $id . "' $tmp";
        $count = "SELECT COUNT(z.id) FROM zeiterfassung z WHERE z.adresse='" . $id . "'";
        $moreinfo = true;
        break;


        // Administration-tables:

      case 'permissionhistory':
        $allowed['user'] = array('list');
        $allowed['benutzer'] = array('list');

        $heading = array('Datum','Erhalten von','Erteilt für', 'Modul', 'Action', 'Recht','');
        $width = array('10%','10%' ,'10%', '10%', '10%', '10%','1%');
        $findcols = array("DATE_FORMAT(ph.timeofpermission,'%d.%m.%Y %H:%i:%s')","IF(granter.username<>ph.granting_user_name,CONCAT(granter.username,' (ehemals ',ph.granting_user_name,')'),ph.granting_user_name)" ,
          "IF(receiver.username<>ph.receiving_user_name,CONCAT(receiver.username,' (ehemals ',ph.receiving_user_name,')'),ph.receiving_user_name)", 'ph.module', 'ph.action', "IF(ph.permission=1,'aktiviert','deaktiviert')","ph.id" );

        $searchsql = $findcols;
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 0;
        $menu = '';

        $sql = "SELECT SQL_CALC_FOUND_ROWS 
            ph.id,
            DATE_FORMAT(ph.timeofpermission,'%d.%m.%Y %H:%i:%s') AS timeofpermission,
            IF(granter.username<>ph.granting_user_name,CONCAT(granter.username,' (ehemals ',ph.granting_user_name,')'),ph.granting_user_name) AS granter,
            IF(receiver.username<>ph.receiving_user_name,CONCAT(receiver.username,' (ehemals ',ph.receiving_user_name,')'),ph.receiving_user_name) AS receiver,
            ph.module AS module,
            ph.action AS action,
            IF(ph.permission=1,'aktiviert','deaktiviert') AS permission,
            ph.id
            FROM permissionhistory AS ph
            JOIN user AS granter ON ph.granting_user_id = granter.id
            JOIN user AS receiver ON ph.receiving_user_id = receiver.id";

        $where = "1"; // z.abrechnen=1 AND z.abgerechnet!=1 AND a.id > 0 ";


        $count = "SELECT COUNT(ph.id) FROM permissionhistory AS ph";
        break;
      case "userlist":
        $allowed['user'] = array('list');
        $allowed['benutzer'] = array('list');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        
        // headings

        $heading = array('Login','Typ', 'Beschreibung', 'Aktiv', 'Extern', 'Anzahl Rechte', 'Hardware', 'Men&uuml;');
        $width = array('30%','10%' ,'20%', '20%', '10%', '10%', '10%', '10%');
        $findcols = array('u.username','u.type' ,'a.name', "if(u.activ,'ja','-')", "if(u.externlogin,'erlaubt','-')", "IF(u.type = 'standard', (SELECT COUNT(ur.id) FROM userrights ur WHERE ur.user = u.id), 'alle')", 'u.hwtoken', 'u.id'); //'a.name','a.kundennummer',"SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von)))/3600",'id');

        $searchsql = array('u.username','u.type', 'a.name', 'u.activ', 'u.externlogin', 'u.hwtoken', "IF(u.type = 'standard', (SELECT COUNT(ur.id) FROM userrights ur WHERE ur.user = u.id), 'alle')");
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 0;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=benutzer&action=edit&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"index.php?module=benutzer&action=download&id=%value%\" title=\"Rechte herunterladen\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/download.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=benutzer&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS u.id, u.username as login, u.type,  a.name as beschreibung, if(u.activ,'ja','-') as aktiv,  if(u.externlogin,'erlaubt','-') as extern, IF(u.type = 'standard' OR u.type = 'lightuser', (SELECT COUNT(ur.id) FROM userrights ur WHERE ur.user = u.id), 'alle') as anzahlrechte, 
        if(u.hwtoken=3,'WaWision OTP',if(u.hwtoken=1,'mOTP',if(u.hwtoken=4,'Zeiterfassung',''))) as 'Hardware', u.id FROM user u LEFT JOIN adresse a ON a.id=u.adresse ";
        $where = ""; // z.abrechnen=1 AND z.abgerechnet!=1 AND a.id > 0 ";

        
        //$groupby=" GROUP by z.adresse_abrechnung ";

        
        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM user";
        break;
      case "geschaeftsbrief_vorlagenlist":
        $allowed['geschaeftsbrief_vorlagen'] = array('list');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        
        // headings

        $heading = array('Typ', 'Betreff', 'Projekt', 'Sprache', 'Men&uuml;');
        $width = array('10%', '50%', '20%', '10%', '10%');
        $findcols = array('g.subjekt', 'g.betreff', 'p.abkuerzung', 'g.sprache', 'g.id');
        $searchsql = array('g.subjekt', 'g.betreff', 'p.abkuerzung', 'g.sprache');
        $menucol = 4;
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 0;
        $menu = "<table cellpadding=0 cellspacing=0>";
          $menu .= "<tr>";
            $menu .= "<td nowrap>";
              $menu .= '<a href="javascript:;" onclick="Geschaeftsbrief_vorlagenEdit(%value%);">';
              //$menu .= "<a href=\"index.php?module=geschaeftsbrief_vorlagen&action=edit&id=%value%\">";
                $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
              $menu .= "</a>"."&nbsp;";
              $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=geschaeftsbrief_vorlagen&action=delete&id=%value%\");>";
                $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
              $menu .= "</a>"."&nbsp;";
              $menu .= "<a href=\"javascript:;\" onclick=\"Geschaeftsbrief_vorlagenCopyEdit(%value%);\">";
                $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\">";
              $menu .= "</a>";
            $menu .= "</td>";
          $menu .= "</tr>";
        $menu .= "</table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS g.id, g.subjekt as typ, g.betreff, if(g.projekt<=0,'Standard Vorlage / ohne Projekt',p.abkuerzung) as projekt, g.sprache, g.id FROM geschaeftsbrief_vorlagen g 
                                           LEFT JOIN projekt p ON g.projekt=p.id ";
        $firma = $this->app->User->GetFirma();
        if($firma == 0)$firma = 1;
        $where = " (g.firma='" . $firma . "' OR g.firma = 0) " . $this->app->erp->ProjektRechte();

        //$groupby=" GROUP by z.adresse_abrechnung ";
        
        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM geschaeftsbrief_vorlagen";
        break;
      case "artikeleinheitlist":
        $allowed['artikeleinheit'] = array('list');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        
        // headings

        $heading = array('Einheit', 'Men&uuml;');
        $width = array('40%', '20%');
        $findcols = array('a.einheit_de', 'a.id');
        $searchsql = array('a.einheit_de');
        $defaultorder = 1;
        $defaultorderdesc = 0;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=artikeleinheit&action=edit&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikeleinheit&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.einheit_de as einheit, a.id FROM artikeleinheit a ";
        $where = "";

        //$groupby=" GROUP by z.adresse_abrechnung ";
        
        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM artikeleinheit";
        break;
      case "projekt_mitglieder":
        $allowed['projekt'] = array('mitglieder');
        $id = (int)$this->app->Secure->GetGET('id');
        // headings
        $heading = array('Kd-Nr.','Lf-Nr.','Mitarbeiter-Nr.', 'Name','Rolle','von','Gruppe','Land', 'PLZ', 'Ort', 'E-Mail', 'Projekt', 'Men&uuml;');
        $width = array('5%', '5%', '5%','15%', '5%', '5%', '5%','2%','5%','5%', '10%', '10%', '3%');
        $findcols = array('a.kundennummer','a.lieferantennummer','a.mitarbeiternummer', 'a.name','a2.subjekt','a2.objekt',"(SELECT g.name FROM gruppen g WHERE g.id=a2.parameter)", 'a.land', 'a.plz', 'ort', 'email', 'p.abkuerzung', 'a2.id');
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 0;
        $searchsql = array('a.ort', 'a.name', 'p.abkuerzung', 'a.land', 'a.plz', 'a.email', 'a.kundennummer', 'a.lieferantennummer', 'a.ansprechpartner','a.mitarbeiternummer','a2.subjekt','a2.objekt');
        $menu = "<a href=\"index.php?module=projekt&action=mitglieder&cmd=adresse&id=%value%\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a onclick=DeleteDialog(\"index.php?module=projekt&action=mitgliederdelete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT(a.kundennummer,if(a.verbandsnummer!='',CONCAT(' / ',a.verbandsnummer),'')),a.lieferantennummer,a.mitarbeiternummer, a.name as name, a2.subjekt,a2.objekt,
          if(a2.objekt='Gruppe',(SELECT g.name FROM gruppen g WHERE g.id=a2.parameter LIMIT 1),''),
                                           a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a2.id as menu
                                           FROM adresse a 
                                           INNER JOIN adresse_rolle a2 ON a2.adresse=a.id
                                           INNER JOIN projekt p ON p.id=a2.projekt
                                            ";
        $groupby = "";
        $where = " p.id='$id' AND (a2.bis='0000-00-00' OR a2.bis >= date(NOW())) AND a2.objekt like 'Projekt' AND a.geloescht <> 1 ";
        $count = "SELECT count(a.id) FROM adresse a 
                                           INNER JOIN adresse_rolle a2 ON a2.adresse=a.id 
                                           INNER JOIN projekt p ON p.id=a.projekt OR p.id = a2.projekt WHERE $where";
        break;

      case "adresse_typlist":
        $allowed['adresse_typ'] = array('list');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        
        // headings

        $heading = array('Bezeichnung', 'Typ','Netto', 'Projekt', 'Men&uuml;');
        $width = array('40%', '20%','10%', '20%', '5%');
        $findcols = array('k.bezeichnung', 'k.type','k.netto', 'p.abkuerzung', 'k.id');
        $searchsql = array('k.bezeichnung', 'k.type','p.abkuerzung');
        $defaultorder = 2;
        $defaultorderdesc = 0;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=adresse_typ&action=edit&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse_typ&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS k.id, ".$this->Stroke("!k.aktiv", "k.bezeichnung").",".$this->Stroke("!k.aktiv", "k.type")." as typ, if(k.netto=1,'ja','-') as netto, if(k.projekt > 0,p.abkuerzung,''), k.id FROM adresse_typ k LEFT JOIN projekt p ON p.id=k.projekt";
        $where = " k.geloescht!=1 ".$this->app->erp->ProjektRechte();

        // gesamt anzahl

        $count = "SELECT COUNT(k.id) FROM adresse_typ k WHERE k.id >0 ".$this->app->erp->ProjektRechte();
        break;
      case "zahlungsweisenlist":
        $allowed['zahlungsweisen'] = array('list');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        
        // headings

        $heading = array('Bezeichnung', 'Typ', 'Projekt','Automatisch bezahlt', 'Men&uuml;');
        $width = array('40%', '20%', '20%','10%', '5%');
        $findcols = array('k.bezeichnung', 'k.type', 'p.abkuerzung','k.automatischbezahlt', 'k.id');
        $searchsql = array('k.bezeichnung', 'k.type','p.abkuerzung');
        $defaultorder = 2;
        $defaultorderdesc = 0;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=zahlungsweisen&action=edit&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=zahlungsweisen&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS k.id, ".$this->Stroke("!k.aktiv", "k.bezeichnung").",".$this->Stroke("!k.aktiv", "k.type")." as typ, if(k.projekt > 0,p.abkuerzung,''), 
          if(k.automatischbezahlt,'Ja',''), k.id FROM zahlungsweisen k LEFT JOIN projekt p ON p.id=k.projekt";
        $where = " k.geloescht!=1 ".$this->app->erp->ProjektRechte();

        // gesamt anzahl

        $count = "SELECT COUNT(k.id) FROM zahlungsweisen k WHERE k.id >0 ".$this->app->erp->ProjektRechte();
        break;


      case "versandartenlist":
        $allowed['versandarten'] = array('list');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        
        // headings

        $heading = array('Bezeichnung', 'Typ','Modul', 'Projekt', 'Men&uuml;');
        $width = array('40%', '12%','12%', '12%', '5%');
        $findcols = array('k.bezeichnung', 'k.type','k.modul', 'p.abkuerzung', 'k.id');
        $searchsql = array('k.bezeichnung', 'k.type','k.modul','p.abkuerzung');
        $defaultorder = 2;
        $defaultorderdesc = 0;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=versandarten&action=edit&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=versandarten&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS k.id, ".$this->Stroke("!k.aktiv", "k.bezeichnung").",".$this->Stroke("!k.aktiv", "k.type")." as typ, ".$this->Stroke("!k.aktiv", "k.modul")." as modul, if(k.projekt > 0,p.abkuerzung,''), k.id FROM versandarten k LEFT JOIN projekt p ON p.id=k.projekt";
        $where = " k.geloescht!=1 ".$this->app->erp->ProjektRechte();

        // gesamt anzahl

        $count = "SELECT COUNT(k.id) FROM versandarten k WHERE k.id >0 ".$this->app->erp->ProjektRechte();
        break;
      case "artikeloptionengruppelist":
        // headings
        $allowed['artikeloptionengruppe'] = array('list');
        $heading = array('Name', 'Projekt','Artikel', 'Men&uuml;');
        $width = array('20%', '30%','30%', '10%');
        $findcols = array('k.name', 'p.abkuerzung','a.nummer', 'k.id');
        $searchsql = array('k.name');
        $defaultorder = 1;
        $defaultorderdesc = 0;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=artikeloptionengruppe&action=edit&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikeloptionengruppe&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS k.id, k.name,p.abkuerzung,a.nummer, k.id FROM  artikeloptionengruppe k 
                                           LEFT JOIN projekt p ON p.id=k.projekt left join artikel a on a.id = k.artikel ";

        $where = " k.geloescht!=1 ";


        $count = "SELECT COUNT(id) FROM artikeloptionengruppe WHERE geloescht!=1";
        
      break;
      case "etikettenlist":
        $allowed['etiketten'] = array('list');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        // headings

        $heading = array('Name', 'Verwenden als', 'Format','Men&uuml;');
        $width = array('20%', '40%', '30%','5%');
        $findcols = array('k.name', 'k.verwendenals',"if(k.manuell,CONCAT(k.labelbreite,'x',k.labelhoehe,'x',labelabstand),k.format)", 'k.id');
        $searchsql = array('k.name', 'k.verwendenals','k.format',"if(k.manuell,CONCAT(k.labelbreite,'x',k.labelhoehe,'x',labelabstand),k.format)");
        $defaultorder = 1;
        $defaultorderdesc = 0;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=etiketten&action=edit&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=etiketten&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS k.id, k.name,k.verwendenals, if(k.manuell,CONCAT(k.labelbreite,'x',k.labelhoehe,'x',labelabstand),k.format), k.id FROM etiketten k ";
        $where = "";

        //$groupby=" GROUP by z.adresse_abrechnung ";
        
        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM etiketten";
        break;



      case "reisekostenartlist":
        $allowed['reisekostenart'] = array('list');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        
        // headings

        $heading = array('Numer', 'Beschreibung', 'Men&uuml;');
        $width = array('10%', '30%', '10%');
        $findcols = array('r.nummer', 'r.beschreibung', 'r.id');
        $searchsql = array('r.nummer', 'r.beschreibung');
        $defaultorder = 1;
        $defaultorderdesc = 0;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=reisekostenart&action=edit&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=reisekostenart&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS r.id, r.nummer, r.beschreibung, r.id FROM reisekostenart r ";
        $where = "";

        //$groupby=" GROUP by z.adresse_abrechnung ";
        
        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM reisekostenart";
        break;
      case "onlineshopslist":
        $allowed['onlineshops'] = array('list');
        $allowed['einstellungen'] = array('category');
        $isSettingAction = $this->app->Secure->GetGET('module') === 'einstellungen'
          || $this->app->Secure->GetGET('smodule') === 'einstellungen';
        if($isSettingAction) {
          $maxrows = 10;
        }
        // START EXTRA checkboxen

        // ENDE EXTRA checkboxen

        // headings

        $heading = array('Shop-ID', 'Bezeichnung', 'Modul' , 'Url', 'Aktiv', 'Automatisches Abholen', 'Projekt', 'Men&uuml;');
        $width = array('5%', '10%', '15%', '20%', '10%', '10%', '10%', '1%');
        $findcols = array('s.id', 's.bezeichnung', 'modulename', "if(s.shoptyp = 'intern','Intern',if(s.shoptyp = 'custom','Custom',s.url))", "if(s.aktiv,'ja','nein')", "if(s.cronjobaktiv = 1,if(s.direktimport = 1, 'ja (direktimport)','ja'),'nein')", 'p.abkuerzung', 's.id');
        $searchsql = array('s.id', 's.bezeichnung', 'modulename', "if(s.shoptyp = 'intern','Intern',if(s.shoptyp = 'custom','Custom',s.url))", 's.url', 's.aktiv', 'p.abkuerzung');
        $defaultorder = 1;
        $defaultorderdesc = 0;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>"
          . "<a href=\"index.php?module=onlineshops&action=edit&id=%value%\">"
          . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>";
        if(!$isSettingAction) {
          $menu .= "&nbsp;"
            . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=onlineshops&action=delete&id=%value%\");>"
            . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";
        }
        $menu .= "</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, s.id, s.bezeichnung, IF(modulename  <> '', modulename, 'extern') AS modulename, if(s.shoptyp = 'intern','Intern',if(s.shoptyp = 'custom','Custom',s.url)), if(s.aktiv,'ja','nein') as aktiv,
        if(s.cronjobaktiv = 1,if(s.direktimport = 1, 'ja (direktimport)','ja'),'nein'),
        p.abkuerzung, s.id FROM shopexport s LEFT JOIN projekt p ON s.projekt=p.id";
        $where = "s.id > 0 " . $this->app->erp->ProjektRechte();

        //$groupby=" GROUP by z.adresse_abrechnung ";
        
        // gesamt anzahl
        $count = "SELECT COUNT(s.id) FROM shopexport s LEFT JOIN projekt p ON s.projekt=p.id WHERE ".$where;
        break;
      case "exportvorlage":
        $allowed['exportvorlage'] = array('list');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        
        // headings

        $heading = array('Name', 'Ziel', 'Men&uuml;');
        $width = array('30%', '30%', '10%');
        $findcols = array('i.bezeichnung', 'i.ziel', 'i.id');
        $searchsql = array('i.ziel', 'i.bezeichnung');

        //                              $defaultorder=2;
        
        //            $defaultorderdesc=0;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=exportvorlage&action=edit&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"index.php?module=exportvorlage&action=export&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/download.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"index.php?module=exportvorlage&action=export&format=xls&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/xls.png\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=exportvorlage&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS i.id, i.bezeichnung, i.ziel, i.id FROM exportvorlage i ";
        $where = ""; //d.firma='".$this->app->User->GetFirma()."'";

        
        //$groupby=" GROUP by z.adresse_abrechnung ";

        
        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM exportvorlage";
        break;
      case "importvorlage":
        $allowed['importvorlage'] = array('list');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        
        // headings

        $heading = array('Name', 'Ziel', 'Men&uuml;');
        $width = array('30%', '30%', '10%');
        $findcols = array('i.bezeichnung', 'i.ziel', 'i.id');
        $searchsql = array('i.ziel', 'i.bezeichnung');

        //                              $defaultorder=2;
        
        //            $defaultorderdesc=0;

        $menu =
          "<table cellpadding=0 cellspacing=0><tr><td nowrap>" .
          "<a href=\"index.php?module=importvorlage&action=edit&id=%value%\" title=\"{|Bearbeiten|}\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" .
          "<a href=\"index.php?module=importvorlage&action=import&id=%value%\" title=\"{|Importieren|}\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/download.svg\" border=\"0\"></a>" . "&nbsp;" .
          "<a href=\"index.php?module=importvorlage&action=downloadjson&id=%value%\" title=\"{|Vorlage erstellen|}\">"."<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/streamline-icon-share-3-alternate.svg\" border=\"0\"></a>". "&nbsp;".
          "<a href=\"index.php?module=importvorlage&action=copy&id=%value%\" title=\"{|Kopie erstellen|}\">"."<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>". "&nbsp;".
          "<a href=\"#\" title=\"{|Löschen|}\" onclick=DeleteDialog(\"index.php?module=importvorlage&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;" .
          "</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS i.id, i.bezeichnung, i.ziel, i.id FROM importvorlage i ";
        $where = ""; //d.firma='".$this->app->User->GetFirma()."'";

        
        //$groupby=" GROUP by z.adresse_abrechnung ";

        
        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM importvorlage";
        break;


      case "adresse_abo":
        $allowed['adresse'] = array('abo');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        
        // headings

        $heading = array('Bezeichnung', 'Nummer', 'abgerechnet', 'Preis','Menge','Art','Men&uuml;');
        $width = array('30%', '10%', '10%', '10%', '10%','10%','10%');
        $findcols = array('aa.bezeichnung', 'art.nummer', 'aa.abgerechnetbis', 'aa.preis','aa.menge','art', 'aa.id');
        $searchsql = array('aa.bezeichnung', 'art.nummer', "DATE_FORMAT(aa.abgerechnetbis,'%d.%m.%Y')");
        //$defaultorder = 2; // sortiert nach dem oeffnen nach spalte 2

        //$defaultorderdesc = 0; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=adresse&action=positioneneditpopup&id=%value%&frame=false&pid=$id\" class=\"popup\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=deleteartikel&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS aa.id,  aa.bezeichnung,art.nummer, DATE_FORMAT(aa.abgerechnetbis,'%d.%m.%Y') as abgerechnet,
      aa.preis as preis, aa.menge as menge, if(aa.wiederholend=1 OR aa.preisart='monat' OR aa.preisart='jahr','wdh','einmalig') as art, aa.id as id
        FROM abrechnungsartikel aa LEFT JOIN artikel art ON art.id=aa.artikel";
        $where = " aa.adresse='" . $id . "'";

        //$groupby=" GROUP by z.adresse_abrechnung ";
        
        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM abrechnungsartikel WHERE adresse='$id'";
        break;



      case "pinwand_list":
        $allowed['drucker'] = array('list');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        
        // headings

        $heading = array('Name', 'Erstellt von', 'Men&uuml;');
        $width = array('30%', '30%', '10%');
        $findcols = array('p.name', 'a.name', 'p.id');
        $searchsql = array('p.name', 'a.name');
//        $defaultorder = 2; // sortiert nach dem oeffnen nach spalte 2

 //       $defaultorderdesc = 0; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=pinwand&action=edit&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=pinwand&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS p.id, p.name, a.name, p.id FROM pinwand p LEFT JOIN user u ON p.user=u.id LEFT JOIN adresse a ON a.id=u.adresse";

        if ($this->app->User->GetType() != "admin") 
        {
          $where = "p.user='" . $this->app->User->GetID() . "'";
          $where = "";
          $count = "SELECT COUNT(p.id) FROM pinwand p AND ".$where;
        } else {
          $where = "";
          $count = "SELECT COUNT(p.id) FROM pinwand p ";
        }

        //$groupby=" GROUP by z.adresse_abrechnung ";
        
        // gesamt anzahl

        
        break;



      case "artikel_eigenschaftensuche":
        $allowed['artikel'] = array('eigenschaftensuche');


        $heading = array('Artikel-Nr.', 'Karat', 'Schliff', 'Reinheit','Labor','GA-Nr.','Men&uuml;');
        $width = array('30%', '20%', '20%','10%','10%','10%', '10%');
        $findcols = array('ae.nummer', 'ae.karat', 'ae.schliff', 'ae.reinheit','ae.labor','ae.ganr','ae.id');
        $searchsql = array('ae.nummer', "FORMAT(ae.karat,2{$extended_mysql55})",'ae.schliff','ae.reinheit','ae.labor','ae.ganr');
        $searchsql_dir = array('LR', 'R','LR','LR','LR','LR'); 

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=artikel&action=edit&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "&nbsp;</td></tr></table>";


        $alignright=array('2');
        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS ae.id,  ae.nummer, FORMAT(ae.karat,2{$extended_mysql55}), ae.schliff, ae.reinheit,ae.labor,ae.ganr,ae.id
                                           FROM artikel_eigenschaftensuche ae ";
        //$where = "d.firma='" . $this->app->User->GetFirma() . "'";

        //$groupby=" GROUP by z.adresse_abrechnung ";
        
        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM artikel_eigenschaftensuche";
        break;

    
    case "adresse_brief":
        $allowed['adresse'] = array('brief');

    // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#brief').change( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#email').change( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#telefon').change( function() { fnFilterColumn3( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#notiz').change( function() { fnFilterColumn4( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#ticket').change( function() { fnFilterColumn5( 0 ); } );");
        //$this->app->Tpl->Add('JQUERYREADY', "$('#versendete').click( function() { fnFilterColumn6( 0 ); } );");
        //$this->app->Tpl->Add('JQUERYREADY', "$('#nichtversendete').click( function() { fnFilterColumn7( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "fnFilterColumn8( 0 );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#belege').click( function() { fnFilterColumn9( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#wiedervorlage').change( function() { fnFilterColumn10( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#kalender').change( function() { fnFilterColumn11( 0 ); } );");
        for ($r = 1;$r < 12;$r++) {
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


        $heading = array('','Datum', 'Titel','Ansprechpartner','Projekt','Bearbeiter', 'Art', 'Gesendet','', '');
        $width = array('1%','15%', '30%','10%','15%', '15%', '10%', '10%','1%', '1%');
        $findcols = array('a.datum','a.datum', 'if(ifnull(a.internebezeichnung,\'\') = \'\', a.title, concat(a.title,\'<br /><i style="color:grey">\',a.internebezeichnung,\'</i>\'))','a.ansprechpartner','a.abkuerzung', 'a.bearbeiter', 'a.art','a.gesendet','a.did','a.did');
        $searchsql = array('a.datum', 'a.title','a.ansprechpartner','a.abkuerzung', 'a.bearbeiter','a.art','a.suchtext','a.internebezeichnung', 'a.gesendet');

        $defaultorder = 2; // sortiert nach dem oeffnen nach spalte 2
        $defaultorderdesc = 1; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)
//index.php?module=adresse&action=korreseditpopup&id=%value% popup
        $menu = '';
        $menu .= '<table width="60" cellpadding="0" cellspacing="0">';
          $menu .= '<tr>';
            $menu .= '<td nowrap >';
              $menu .= '<span style="display:none">%value%</span><a href="javascript:;" onclick="edit_Eintrag(this);" class="editEintrag"><img src="themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/edit.svg" border="0"></a> ';            
            $menu .= "&nbsp;";
              $menu .= '<a href="javascript:;" class="deleteEintrag"><img src="themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/delete.svg" border="0"></a> ';
            $menu .= '</td>';
            //$menu .= '<td align="right">';
            //  $menu .= '<a href="javascript:;" class="previewEintrag"><img src="themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/details_open.png" border="0"> <img src="themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/details_close.png" border="0" class="close" style="display:none;"></a> ';
            //$menu .= '</td>';

          $menu .= '</tr>';
        $menu .= '</table>';
        // $menu = '<a href="javascript:;" onclick="" class="previewEintrag">OPEN</a> <a href="javascript:;" onclick="" class="editEintrag">EDIT</a>';
        #$menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=adresse&action=ansprechpartner&edit&id=$id&iframe=$iframe&lid=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse&action=ansprechpartner&delete=1&id=$id&iframe=$iframe&lid=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . $einfuegen . "&nbsp;</td></tr></table>";


        $cmd = $this->app->Secure->GetGET("cmd");
        $id = $this->app->Secure->GetGET("id");

        if($cmd=="adresse_brief" && $id > 0) $adresseId = $id;
        else
          $adresseId = $this->app->User->GetParameter('adresse_brief_adresseId');
        
        $sql = '
          SELECT
            SQL_CALC_FOUND_ROWS a.id,
            a.open,
            DATE_FORMAT(a.datum, "%d.%m.%Y %H:%i"),
            if(ifnull(a.internebezeichnung,\'\') = \'\', a.title, concat(a.title,\'<br /><i style="color:grey">\',a.internebezeichnung,\'</i>\')),
            a.ansprechpartner,
            a.abkuerzung,
            a.bearbeiter,
            a.art,
            a.gesendet,
            a.pdf,
            a.did
          FROM 
          (
            (
              SELECT
                d.id,"<img src=./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/details_open.png class=details>" as open,
                CONCAT(DATE_FORMAT(d.datum, "%Y-%m-%d"), " ", IF(d.uhrzeit IS NULL OR DATE_FORMAT(d.uhrzeit, "%H:%i")="00:00", "", DATE_FORMAT(d.uhrzeit, "%H:%i")) ) as datum,
                d.betreff as title,if(d.typ = \'email\',if(d.ansprechpartner <> \'\',d.ansprechpartner,d.email_an),d.ansprechpartner) as ansprechpartner,
                p.abkuerzung as abkuerzung,
                if(bearbeiter!="",bearbeiter,a2.name) as bearbeiter,
                CONCAT(UCASE(LEFT(d.typ, 1)), SUBSTRING(d.typ, 2)) as art,
                CONCAT(IF(d.sent = 1, "JA", "NEIN"),"<a data-type=dokumente data-id=", d.id, "></a>") as gesendet,
                "" as pdf,
                concat("1","-",d.id) as did,d.content as suchtext,d.internebezeichnung
              FROM
                dokumente d
              LEFT JOIN projekt p ON p.id=d.projekt
              LEFT JOIN adresse a2 ON a2.id=adresse_from
              WHERE
                adresse_to = ' . $adresseId . '
            )

            UNION ALL

            (
              SELECT
                ds.id,"<img src=./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/details_open.png class=details>" as open,
                CONCAT(DATE_FORMAT(ds.zeit, "%Y-%m-%d")," ", IF(DATE_FORMAT(ds.zeit, "%H:%i")="00:00", "", DATE_FORMAT(ds.zeit, "%H:%i"))) as datum,
                ds.betreff  as title,ds.ansprechpartner ,
                p.abkuerzung as abkuerzung,
                ds.bearbeiter  as bearbeiter,
                CONCAT(UCASE(LEFT(ds.dokument, 1)), SUBSTRING(ds.dokument, 2),"<span style=\"display:none;\" class=\"editlink\">index.php?module=",ds.dokument,"&action=edit&id=",ds.parameter,"</span>") as art,
                CONCAT(IF(ds.versendet = 1, "JA", "NEIN"),"<a data-type=dokumente_send data-id=", ds.id, "></a>") as gesendet,
                concat("<a href=\"index.php?module=",ds.dokument,"&action=pdf&id=",ds.parameter,"\"><img src=./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/pdf.svg></a>") as pdf,
                concat("2","-",ds.id) as did,ds.text  as suchtext,
                ifnull(d1.internebezeichnung,
                ifnull(d2.internebezeichnung,
                ifnull(d3.internebezeichnung,
                ifnull(d4.internebezeichnung,
                ifnull(d5.internebezeichnung,
                ifnull(d6.internebezeichnung,
                 \'\'))))))  as internebezeichnung
              FROM
                dokumente_send ds
                LEFT JOIN lieferschein d1 ON ds.parameter = d1.id AND ds.dokument = \'lieferschein\'
                LEFT JOIN auftrag d2 ON ds.parameter = d2.id AND ds.dokument = \'auftrag\'
                LEFT JOIN rechnung d3 ON ds.parameter = d3.id AND ds.dokument = \'rechnung\'
                LEFT JOIN gutschrift d4 ON ds.parameter = d4.id AND ds.dokument = \'gutschrift\'
                LEFT JOIN angebot d5 ON ds.parameter = d5.id AND ds.dokument = \'angebot\'
                LEFT JOIN bestellung d6 ON ds.parameter = d6.id AND ds.dokument = \'bestellung\'
                LEFT JOIN projekt p ON p.id=ds.projekt
              WHERE
                ds.adresse = ' . $adresseId . '
            )
            
            UNION ALL

            (
              SELECT
                k.id,"<img src=./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/details_open.png class=details>" as open,
                CONCAT(DATE_FORMAT(k.von, "%Y-%m-%d")," ", IF(DATE_FORMAT(k.von, "%H:%i")="00:00", "", DATE_FORMAT(k.von, "%H:%i"))) as datum,
                k.bezeichnung COLLATE utf8_general_ci as title,\'\' as ansprechpartner,
                p.abkuerzung as abkuerzung,
                a2.name COLLATE utf8_general_ci as bearbeiter,
                "Kalender" as art,
                CONCAT("<a data-type=kalender data-id=", k.id, "></a>") as gesendet,
                "" as pdf,
                concat("6","-",k.id) as did,k.beschreibung COLLATE utf8_general_ci as suchtext,\'\' as internebezeichnung
              FROM
                kalender_event k 
                LEFT JOIN adresse a2 ON k.adresseintern = a2.id
                LEFT JOIN projekt p ON p.id=k.projekt
              WHERE
                k.adresse = ' . $adresseId . '
            )
            
            ';



            if($this->app->erp->RechteVorhanden('wiedervorlage','list')){
              if($this->app->erp->GetKonfiguration('adresse_crm_collateerror') && method_exists($this,'ConvertLatin1UTF'))
              {
                $sql .= '
            UNION ALL

            (
              SELECT
                w.id,"<img src=./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/details_open.png class=details>" as open,
                CONCAT(DATE_FORMAT(datum_erinnerung, "%Y-%m-%d"), " ", IF(zeit_erinnerung IS NULL OR DATE_FORMAT(zeit_erinnerung, "%H:%i")="00:00", "", DATE_FORMAT(zeit_erinnerung, "%H:%i")) ) as datum,
                '. $this->ConvertLatin1UTF('w.bezeichnung').'   as title,\'\' as ansprechpartner,
                '. $this->ConvertLatin1UTF('p.abkuerzung').'   as abkuerzung,
                adr.name COLLATE utf8_general_ci as bearbeiter,
                CONCAT("Wiedervorlage") as art,
                CONCAT("<a data-type=wiedervorlage data-id=", w.id, "></a>") as gesendet,
                "" as pdf,
                concat("5","-",w.id) as did,'. $this->ConvertLatin1UTF('w.beschreibung').'  as suchtext,\'\' as internebezeichnung
              FROM
                wiedervorlage w left join adresse adr on w.bearbeiter = adr.id
                LEFT JOIN projekt p ON p.id=w.projekt
              WHERE
                w.adresse = ' . $adresseId . '
            )';
              }else{
                $sql .= '
            UNION ALL

            (
              SELECT
                w.id,"<img src=./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/details_open.png class=details>" as open,
                CONCAT(DATE_FORMAT(datum_erinnerung, "%Y-%m-%d"), " ", IF(zeit_erinnerung IS NULL OR DATE_FORMAT(zeit_erinnerung, "%H:%i")="00:00", "", DATE_FORMAT(zeit_erinnerung, "%H:%i")) ) as datum,
                w.bezeichnung  COLLATE utf8_general_ci as title,\'\' as ansprechpartner,
                p.abkuerzung  COLLATE utf8_general_ci as abkuerzung,
                adr.name COLLATE utf8_general_ci as bearbeiter,
                CONCAT("Wiedervorlage") as art,
                CONCAT("<a data-type=wiedervorlage data-id=", w.id, "></a>") as gesendet,
                "" as pdf,
                concat("5","-",w.id) as did,w.beschreibung COLLATE utf8_general_ci as suchtext,\'\' as internebezeichnung
              FROM
                wiedervorlage w left join adresse adr on w.bearbeiter = adr.id
                LEFT JOIN projekt p ON p.id=w.projekt
              WHERE
                w.adresse = ' . $adresseId . '
            )';
              }
            }
            $sql .='
          ) a
        ';


        $moreinfo = true;
        $doppelteids = true;
        $moreinfoaction = 'brief';
        $menucol = 9;
   
        $more_data1 = $this->app->Secure->GetGET("more_data1");
        $more_data2 = $this->app->Secure->GetGET("more_data2");
        $more_data3 = $this->app->Secure->GetGET("more_data3");
        $more_data4 = $this->app->Secure->GetGET("more_data4");
        $more_data5 = $this->app->Secure->GetGET("more_data5");
        $more_data6 = $this->app->Secure->GetGET("more_data6");
        $more_data7 = $this->app->Secure->GetGET("more_data7");
        $more_data9 = $this->app->Secure->GetGET("more_data9");
        $more_data10 = $this->app->Secure->GetGET("more_data10");
        $more_data11 = $this->app->Secure->GetGET("more_data11");

        if ($more_data1 == 1) {
          $subWhereConditions[] = 'art LIKE "brief%"';
        }
        if ($more_data3 == 1) {
          $subWhereConditions[] = 'art LIKE "telefon"';
        }
        if ($more_data4 == 1) {
          $subWhereConditions[] = 'art LIKE "notiz"';
        }
        if ($more_data10 == 1) {
          $subWhereConditions[] = 'art LIKE "wiedervorlage"';
        }
        if ($more_data9 == 1) {
          $subWhereConditions[] = '(art LIKE "lieferschein%" OR art LIKE "angebot%" OR art LIKE "auftrag%" OR art LIKE "rechnung%" OR art LIKE "bestellung%" OR art LIKE "gutschrift%")';
        }
        
        if ($more_data11 == 1) {
          $subWhereConditions[] = 'art LIKE "kalender"';
        }        

        if ($subWhereConditions) {
          $whereConditions[] = '( ' . implode(' OR ', $subWhereConditions) . ' )';
        }

        if ($more_data6 == 1) {
          $whereConditions[] = 'gesendet LIKE "ja%"';
        }

        if ($more_data7 == 1) {
          $whereConditions[] = 'gesendet LIKE "nein%"';
        }


        if ($whereConditions) {
          $where = implode(' AND ', $whereConditions);
        }

        //$orderby = 'UNIX_TIMESTAMP(a.datum)';

        //$groupby=" GROUP BY artikel.id ";
        
        // gesamt anzahl
        $count = '
          SELECT
            SUM(anzahl)
          FROM 
          (

            (
              SELECT
                COUNT(id) as anzahl
              FROM
                dokumente
              WHERE
                adresse_to = ' . $adresseId . '
            )

            UNION ALL

            (
              SELECT
                COUNT(id) as anzahl
              FROM
                dokumente_send
              WHERE
                adresse = ' . $adresseId . '
            )';


            $count .= '
            UNION ALL

            ( 
              SELECT
                COUNT(k.id) as anzahl
              FROM
                kalender_event k 
                LEFT JOIN adresse a2 ON k.adresseintern = a2.id
                LEFT JOIN projekt p ON p.id=k.projekt
              WHERE
                k.adresse = ' . $adresseId . '

            )';


            if($this->app->erp->RechteVorhanden('wiedervorlage','list')){
              $count .= '
              UNION ALL

              (
                SELECT
                  COUNT(id) as anzahl
                FROM
                  wiedervorlage
                WHERE
                 wiedervorlage.adresse = ' . $adresseId . '

              )';
            }
            $count .= '
          ) a
        ';
        break;
      case 'stammdatenbereinigen_list':

        $allowed['stammdatenbereinigen'] = array('list');

        $this->app->Tpl->Add('JQUERYREADY', "$('#stammdatenbereinigenName').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#stammdatenbereinigenPlz').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#stammdatenbereinigenEMail').click( function() { fnFilterColumn3( 0 ); } );");
        for ($r = 1;$r <= 3;$r++) {
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

        $heading = array('Name', 'Straße', 'Ort','PLZ', 'Land', 'Anzahl','Projekt', 'Men&uuml;');
        $width = array('20%','20%','20%', '10%', '10%', '10%','5%', '5%');
        $findcols = array('a.name','a.strasse', 'a.ort', 'a.plz', 'a.land', 'p.abkuerzung','anzahl');
        $searchsql = array('a.name','a.strasse', 'a.ort', 'a.plz', 'a.land', 'p.abkuerzung');

        $defaultorder = 0; // sortiert nach dem oeffnen nach spalte 2
        $defaultorderdesc = 1; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $menu = '';
        $menu = '<table cellpadding="0" cellspacing="0" width="100%">';
          $menu .= '<tr>';
            $menu .= '<td nowrap align="right">';
              $menu .= '<a href="javascript:;" onclick="zusammenfuehren(%value%);">';
                $menu .= '<img src="themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/edit.svg" border="0">';
              $menu .= '</a>';
            $menu .= '</td>';
          $menu .= '</tr>';
        $menu .= '</table>';

        $grouping = 0;
        $more_data1 = $this->app->Secure->GetGET("more_data1");
        if ($more_data1 == 1) {
          $groupABy[] = 'a.name';
          $paramsGroupBy[] = 'name';
          $grouping++;
        }

        $more_data2 = $this->app->Secure->GetGET("more_data2");
        if ($more_data2 == 1) {
          $groupABy[] = 'a.plz';
          $paramsGroupBy[] = 'plz';
          $grouping++;
        }

        $more_data3 = $this->app->Secure->GetGET("more_data3");
        if ($more_data3 == 1) {
          $groupABy[] = 'a.email';
          $paramsGroupBy[] = 'email';
          $grouping++;
        }
/*
        if ($grouping <= 0) {
          $groupABy = array();
          $groupABy[] = 'a.name';
          $paramsGroupBy[] = 'name';
        }
*/
        $this->app->User->SetParameter('stammdatenbereinigen_list_param', implode(';',$paramsGroupBy));

        if(count($groupABy)>0)
        {
          $groupby = '
            GROUP BY ' . implode(',', $groupABy) . '
            HAVING count(*) > 1
          ';

          $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.name, a.strasse, a.ort, a.plz, a.land, count(*) as anzahl, p.abkuerzung, a.id
          FROM adresse a LEFT JOIN projekt p ON p.id=a.projekt ";

        } else {
            $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.name, a.strasse, a.ort, a.plz, a.land, '1' as anzahl, p.abkuerzung, a.id
            FROM adresse a LEFT JOIN projekt p ON p.id=a.projekt ";
        }

        $where = " a.geloescht!=1";
        
        // gesamt anzahl
        $count = 'SELECT COUNT(id) FROM adresse WHERE geloescht!=1';

      break;
      case 'layoutvorlagen_list':

        $allowed['layoutvorlagen'] = array('list');

        $heading = array('Name', 'Typ', 'Format','Kategorie','Projekt','Men&uuml;');
        $width = array('60%','10%','10%','15%','10%','5%');
        $findcols = array('l.name','l.typ', 'l.format', 'l.kategorie', 'p.abkuerzung', 'l.id');
        $searchsql = array('l.name','l.typ', 'l.format', 'l.kategorie', 'p.abkuerzung', 'l.id');

        $defaultorder = 1; // sortiert nach dem oeffnen nach spalte 2
        $defaultorderdesc = 1; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        // $alignright=array(3,4,5);
        $menu = '';
        $menu = '<table cellpadding="0" cellspacing="0" width="100%">';
          $menu .= '<tr>';
            $menu .= '<td nowrap align="right">';
              $menu .= '<a href="index.php?module=layoutvorlagen&action=edit&id=%value%">';
                $menu .= '<img src="themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/edit.svg" border="0">';
              $menu .= '</a> ';
              $menu .= '<a target="_blank" href="index.php?module=layoutvorlagen&action=export&id=%value%">';
                $menu .= '<img src="themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/download.svg" border="0">';
              $menu .= '</a> ';
              $menu .= "<a href=\"#\" onclick=CopyDialog(\"index.php?module=layoutvorlagen&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a> ";
              $menu .= '<a href="javascript:;" onclick="deleteLayoutvorlage(%value%);">';
                $menu .= '<img src="themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/delete.svg" border="0">';
              $menu .= '</a>';
            $menu .= '</td>';
          $menu .= '</tr>';
        $menu .= '</table>';

        $sql = '
          SELECT
            SQL_CALC_FOUND_ROWS l.id,
            l.name,
            l.typ,
            l.format,
            l.kategorie,
            p.abkuerzung,                                     
            l.id
          FROM
            layoutvorlagen l
          LEFT JOIN projekt p 
          ON l.projekt = p.id
        ';

        //$groupby = '';

        $where = "";
        
        // gesamt anzahl
        $count = '
          SELECT 
            COUNT(id) 
          FROM 
            layoutvorlagen
          ';

      break;

      case 'layoutvorlagen_edit':

        $allowed['layoutvorlagen'] = array('edit');

        $heading = array('Sort','Beschreibung','Name',  'Position X','Position Y','Men&uuml;');
        $width = array('5%','25%','25%','10%','10%', '1%');
        $findcols = array('lp.sort','lp.beschreibung','lp.name', 'lp.position_x', 'lp.position_y', 'lp.id');
        $searchsql = array('lp.sort','lp.beschreibung','lp.name', 'lp.position_x', 'lp.position_y');

        $defaultorder = 1; // sortiert nach dem oeffnen nach spalte 2
        $defaultorderdesc = 0; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $layoutvorlageId = $this->app->User->GetParameter('layoutvorlagen_id');

        $menu = '';
        $menu = '<table cellpadding="0" cellspacing="0" width="100%">';
          $menu .= '<tr>';
            $menu .= '<td nowrap align="right">';
              $menu .= '<a href="javascript:;" onclick="editLayoutvorlagePosition(%value%);">';
                $menu .= '<img src="themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/edit.svg" border="0">';
              $menu .= '</a> ';
              $menu .= '<a href="javascript:;" onclick="deleteLayoutvorlagePosition(%value%);">';
                $menu .= '<img src="themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/delete.svg" border="0">';
              $menu .= '</a>';
            $menu .= '</td>';
          $menu .= '</tr>';
        $menu .= '</table>';

        $sql = '
          SELECT
            SQL_CALC_FOUND_ROWS lp.id,
            lp.sort, 
            lp.beschreibung,
            lp.name,
           
            lp.position_x,
            lp.position_y,
            
            lp.id
          FROM
            layoutvorlagen_positionen lp
        ';

        //$groupby = '';

        $where = ' lp.layoutvorlage = ' . $layoutvorlageId;
        
        // gesamt anzahl
        $count = '
          SELECT 
            COUNT(id) 
          FROM 
            layoutvorlagen_positionen
          WHERE
            layoutvorlage = ' . $layoutvorlageId . '
          ';

      break;

      case "shopexport_artikeluebertragung":
        $allowed['shopexport'] = array('artikeluebertragung');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        // headings

        $heading = array('Artikel-Nr', 'Artikel','Typ', 'Sortierung','Men&uuml;');
        $width = array('15%', '60%', '10%','5%','1%');
        $findcols = array('a.nummer', 'a.name_de',"IF(s.typ = 1, 'zum &uuml;bertragen','wird gepr&uuml;ft')", 's.id','s.id');
        $searchsql = array('a.nummer', 'a.name_de',"IF(s.typ = 1, 'zum &uuml;bertragen','wird gepr&uuml;ft')");

        $defaultorderdesc = 0; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)
        $defaultorder = 3; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $menu = "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=shopexport&action=artikeluebertragungdel&id=%value%&shop=".$id."\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS s.artikel, a.nummer,a.name_de,
                           IF(s.typ = 1, 'zum &uuml;bertragen','wird gepr&uuml;ft'),
                           s.id,s.artikel
                
            FROM (
                  (
                      SELECT artikel, shop, 1 as typ , id
                        FROM shopexport_artikeluebertragen
                      )
                UNION ALL (
                    SELECT artikel, shop, 2 as typ ,id
                        FROM shopexport_artikeluebertragen_check
                )
                
                ) AS s  
            LEFT JOIN artikel AS a ON a.id=s.artikel ";
        $where = sprintf(' s.shop=%d ', $id);
        $alignright = [4];

        $count = '';//sprintf('SELECT COUNT(id) FROM shopexport_artikeluebertragen WHERE shop=%d', $id);
        break;

      case "shopexport_adressuebertragung":
        $allowed['shopexport'] = array('shopexport_adressuebertragung');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        // headings

        $heading = array('Name', 'Kunden-Nr', 'Lieferanten-Nr','Men&uuml;');
        $width = array('33%', '33%', '33%','1%');
        $findcols = array('a.name', "IF(a.kundennummer='','-',a.kundennummer)", "IF(a.lieferantennummer='','-',a.lieferantennummer)",'s.id');
        $searchsql = array('a.name', "IF(a.kundennummer='','-',a.kundennummer)", "IF(a.lieferantennummer='','-',a.lieferantennummer)");

        $defaultorderdesc = 0; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)
        $defaultorder = 3; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $menu = "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=shopexport&action=adressuebertragungdel&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, a.name, IF(a.kundennummer='','-',a.kundennummer) ,IF(a.lieferantennummer='','-',a.lieferantennummer) ,s.id,s.id
                                           FROM shopexport_adressenuebertragen s  LEFT JOIN adresse a ON a.id=s.adresse ";
        $where = " s.shop='$id' ";

        $count = "SELECT COUNT(id) FROM shopexport_adressenuebertragen WHERE shop='$id'";
        break;

      case "shopexport_zahlweisen":
        $allowed['onlineshops'] = array('edit');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        
        // headings

        $heading = array('Zahlweise Shop', 'Zahlweise Xentral', 'Vorab als bezahlt','Autoversand','keine Rechnung anlegen','Fast-Lane','Aktiv','Men&uuml;');
        $width = array('30%', '30%', '10%','10%', '10%','10%','10%', '5%');
        $findcols = array('s.zahlweise_shop', 's.zahlweise_wawision', 's.vorabbezahltmarkieren','s.autoversand','s.keinerechnung','s.fastlane','s.aktiv','s.id');
        $searchsql = array('s.zahlweise_shop', 's.zahlweise_wawision', 's.vorabbezahltmarkieren','s.autoversand','s.keinerechnung','s.fastlane','s.aktiv');

        $defaultorderdesc = 0; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"#\" onclick=zahlweisenEdit(%value%)>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=onlineshops&action=zahlweisedelete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, s.zahlweise_shop, s.zahlweise_wawision,if(s.vorabbezahltmarkieren=1,'ja','-'),if(s.autoversand=1,'ja','-'),if(s.keinerechnung=1,'ja','-'),if(s.fastlane=1,'ja','-'),if(s.aktiv=1,'ja','-'), s.id
                                           FROM shopexport_zahlweisen s  ";
        $where = " s.shop='$id' ";

        //$groupby=" GROUP by z.adresse_abrechnung ";
        
        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM shopexport_zahlweisen WHERE shop='$id'";
        break;

      case "shopexport_versandarten":
        $allowed['onlineshops'] = array('edit');

        $heading = array('Versandart Shop', 'Versandart Xentral', 'Versandart Ausgehend', 'Produkt Ausgehend', 'Land','Autoversand','Fast-Lane','Aktiv','Men&uuml;');
        $width = array('25%', '25%','25%', '10%','10%','10%','10%','5%', '5%');
        $findcols = array('s.versandart_shop', 's.versandart_wawision','s.versandart_ausgehend','s.produkt_ausgehend','s.land','s.autoversand','s.fastlane','s.aktiv','s.id');
        $searchsql = array('s.versandart_shop', 's.versandart_wawision','s.versandart_ausgehend','s.land','s.autoversand','s.fastlane', 's.aktiv');

        $defaultorderdesc = 0; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"#\" onclick=versandartenEdit(%value%)>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=onlineshops&action=versandartdelete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, s.versandart_shop, s.versandart_wawision, s.versandart_ausgehend,s.produkt_ausgehend, s.land,if(s.autoversand=1,'ja','-'),if(s.fastlane=1,'ja','-'),if(s.aktiv=1,'ja','-'), s.id
                                           FROM shopexport_versandarten s  ";
        $where = " s.shop='$id' ";

        //$groupby=" GROUP by z.adresse_abrechnung ";
        
        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM shopexport_versandarten WHERE shop='$id'";
        break;
      case "shopexport_freifelder":
        $allowed['onlineshops'] = array('edit');

        $heading = array('Freifeld Xentral', 'Bezeichnung in Shop','Aktiv','Men&uuml;');
        $width = array('30%', '30%','10%', '5%');
        $findcols = array('s.freifeld_wawi', 's.freifeld_shop','s.aktiv','s.id');
        $searchsql = array('s.freifeld_wawi', 's.freifeld_shop');

        $defaultorderdesc = 0; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $subsql = '';
        for($i = 1; $i <= 40; $i++)
        {
          $freifeldname = $this->app->erp->Firmendaten('freifeld'.$i);
          if(!$freifeldname){
            $freifeldname = 'Freifeld '.$i;
          }
          $freifeldname = explode('|',$freifeldname);
          $subsql .= " if(s.freifeld_wawi = 'freifeld".$i."', '$freifeldname[0]', ";
          
        }
        $subsql .= 's.freifeld_wawi';
        for($i = 1; $i <= 40; $i++)$subsql .= ')';
        
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"#\" onclick=freifelderEdit(%value%)>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=onlineshops&action=freifelddelete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, $subsql, s.freifeld_shop,if(s.aktiv=1,'ja','-'), s.id
                                           FROM shopexport_freifelder s  ";
        $where = " s.shop='$id' ";

        //$groupby=" GROUP by z.adresse_abrechnung ";
        
        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM shopexport_freifelder WHERE shop='$id'";
        break;

      case "shopexport_subshop":
        $allowed['onlineshops'] = array('edit');

        $heading = array('Subshop Kennung', 'Projekt Xentral','Sprache','Aktiv','Men&uuml;');
        $width = array('30%', '30%','10%','5%', '5%');
        $findcols = array('s.subshopkennung', 'p.abkuerzung','s.sprache','s.aktiv','s.id');
        $searchsql = array('s.subshopkennung', 'p.abkuerzung','s.sprache');

        $defaultorderdesc = 0; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"#\" onclick=subshopEdit(%value%)>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=onlineshops&action=subshopdelete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, s.subshopkennung, p.abkuerzung,s.sprache,if(s.aktiv=1,'ja','-'), s.id
                                           FROM shopexport_subshop s LEFT JOIN projekt p ON p.id=s.projekt ";
        $where = " s.shop='$id' ";

        $count = "SELECT COUNT(id) FROM shopexport_subshop WHERE shop='$id'";
        break;
      case "berichte":
        $allowed['berichte'] = array('list');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        
        // headings

        $heading = array('Name','Projekt', 'Men&uuml;');
        $width = array('75%','20%','5%');
        $findcols = array('b.name','p.abkuerzung', 'b.id');
        $searchsql = array('d.name', 'd.bezeichnung', 'd.anbindung','p.abkuerzung', 'd.aktiv');


        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=berichte&action=live&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"index.php?module=berichte&action=pdf&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>" . "&nbsp;". "<a href=\"index.php?module=berichte&action=csv&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/download.svg\" border=\"0\"></a>" . "&nbsp;". "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=berichte&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"index.php?module=berichte&action=edit&id=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a></td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS b.id, b.name, p.abkuerzung, b.id
                                           FROM berichte b LEFT JOIN projekt p ON p.id=b.project ";
        $where = " b.id > 0 ".$this->app->erp->ProjektRechte("b.project");//d.firma='" . $this->app->User->GetFirma() . "'";

        //$groupby=" GROUP by z.adresse_abrechnung ";
        
        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM berichte";
        break;
      case "adresse_stammdatenlieferadresselist":
      case "adresse_stammdatenverzollungadresselist":
        $allowed['adresse'] = array('ansprechpartner');
        $allowed['proformarechnung'] = array('edit','create');
        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        $id = $this->app->Secure->GetGET('id');
        $lid = $this->app->Secure->GetGET("lid");
        $iframe = $this->app->Secure->GetGET("iframe");
        $postfix = $this->app->Secure->GetGET("postfix");
        $smodule = $this->app->Secure->GetGET("smodule");
        // headings
        $heading = array('Name', 'Abteilung', 'Strasse', 'PLZ', 'Ort', 'Men&uuml;');
        $width = array('20%', '20%', '20%', '15%', '10%', '20%', '1%');
        $findcols = array('a.name', 'a.abteilung', 'a.strasse', 'a.plz', 'a.ort', 'a.id');
        $searchsql = array('a.name', 'a.abteilung', 'a.strasse', 'a.plz', 'a.ort','a.kundennummer','a.lieferantennummer');
        $defaultorder = 1;
        $defaultorderdesc = 0;

        switch($smodule)
        {
          case "alsverzollungadresse": $auswahl = "Verzolladresse"; break;
          case "alsansprechpartner": $auswahl = "AdresseStammdatenIframe"; break;
          default: $auswahl = "AdresseStammdatenLieferscheinIframe"; break;
        }

        
        if ($iframe == "true") $einfuegen = "<a onclick=".$auswahl."(\"%value%\"".((String)$postfix!==""?",\"$postfix\"":'').");><img title=\"Adresse übernehmen\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/down.png\" border=\"0\"></a>";

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . $einfuegen . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql =
          "SELECT DISTINCT SQL_CALC_FOUND_ROWS 
          a.id, 
          a.name, 
          a.abteilung, 
          a.strasse, 
          a.plz, 
          a.ort, 
          a.id 
          FROM `adresse` AS `a`     
          LEFT JOIN `adresse_rolle` AS `ar` ON a.id = ar.adresse AND ar.objekt = 'Projekt' 
          LEFT JOIN `projekt` AS `p` ON p.id = ar.parameter";
        $where = " a.geloescht!=1 ".$this->app->erp->ProjektRechte();

        //$orderby = "a.name,a.strasse";
        
        //$orderby = "l.name, l.strasse";
        
        //$groupby=" GROUP by z.adresse_abrechnung ";

        
        // gesamt anzahl

        $count = "SELECT COUNT(a.id) FROM adresse a WHERE a.geloescht!=1 ";
        break;
 
      case "adresse_ansprechpartnerlieferadresselist":
        $allowed['adresse'] = array('ansprechpartner');

        $this->app->Tpl->Add('JQUERYREADY', "$('#alle').click( function() { fnFilterColumn1( 0 ); } );");

        for ($r = 1;$r < 2;$r++) {
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

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        $id = $this->app->Secure->GetGET('id');
        $lid = $this->app->Secure->GetGET("lid");
        $iframe = $this->app->Secure->GetGET("iframe");
        $postfix = $this->app->Secure->GetGET("postfix");
        if ($lid > 0) $id = $this->app->DB->Select("SELECT adresse FROM ansprechpartner WHERE id='$lid' LIMIT 1");

        // headings
        $heading = array('Name', 'Bereich', 'Email', 'Telefon', 'Telefax', 'Mobil', 'Men&uuml;');
        $width = array('20%', '15%', '15%', '10%', '10%', '10%', '10%', '5%');
        $findcols = array('a.name', 'a.bereich', 'a.email', 'a.telefon', 'a.telefax', 'a.mobil', 'a.id');
        $searchsql = array('a.name', 'a.bereich', 'a.email', 'a.telefon', 'a.telefax', 'a.mobil');
        $defaultorder = 1;
        $defaultorderdesc = 0;
        
        if ($iframe == "true") $einfuegen = "<a onclick=AnsprechpartnerLieferscheinIframe(\"%value%\"".((String)$postfix!==""?",\"$postfix\"":'').");><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/down.png\" border=\"0\"></a>";
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=adresse&action=ansprechpartner&edit&id=$id&iframe=$iframe&lid=%value%\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse&action=ansprechpartner&delete=1&id=$id&iframe=$iframe&lid=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . $einfuegen . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.name, a.bereich, a.email, a.telefon, a.telefax, a.mobil, a.id FROM ansprechpartner a ";

        if($this->app->Secure->GetGET("more_data1")=="1")
          $where = " a.name!='Neuer Datensatz' ";
        else
          $where = " a.adresse='" . $id . "' AND a.name!='Neuer Datensatz' ";

        //$orderby = "a.name,a.strasse";
        
        //$orderby = "l.name, l.strasse";

        
        //$groupby=" GROUP by z.adresse_abrechnung ";

        
        // gesamt anzahl

        $count = "SELECT COUNT(a.id) FROM ansprechpartner a WHERE a.adresse='" . $id . "' AND a.name!='Neuer Datensatz'";
        break;
  
      case "adresse_accounts":
        $allowed['adresse'] = array('accounts');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        $id = $this->app->Secure->GetGET('id');
        $lid = $this->app->Secure->GetGET("lid");
        $iframe = $this->app->Secure->GetGET("iframe");
        
        if ($lid > 0) $id = $this->app->DB->Select("SELECT adresse FROM adresse_accounts WHERE id='$lid' LIMIT 1");

        // headings
        $heading = array('Bezeichnung', 'Art', 'Benutzer','URL', 'Aktiv','Zwischenspeicher', 'Men&uuml;');
        $width = array('10%', '10%', '10%','35%', '5%', '15%', '5%');
        $findcols = array('a.bezeichnung', 'a.art', 'a.benutzername','a.url', "if(a.aktiv,'Ja','Nein')", 'a.id', 'a.id');
        $searchsql = array('a.bezeichnung', 'a.art', 'a.benutzername','a.url', "if(a.aktiv,'Ja','Nein')");
        $defaultorder = 1;
        $defaultorderdesc = 0;
        
        if ($iframe == "true") $einfuegen = "<a onclick=AnsprechpartnerIframe(\"%value%\");><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/down.png\" border=\"0\"></a>";
        $menu = "<table cellpadding=0 cellspacing=0>";
          $menu .= "<tr>";
            $menu .= "<td nowrap>";
              $menu .= '<a href="javascript:;" onclick="AccountsEdit(%value%);">';
              //$menu .= "<a href=\"index.php?module=adresse&action=accounts&edit&id=$id&iframe=$iframe&lid=%value%\">";
                $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
              $menu .= "</a>"."&nbsp;";
              $menu .= '<a href="javascript:;" onclick="AccountsDelete(%value%);">';
              //$menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse&action=accounts&delete=1&id=$id&iframe=$iframe&lid=%value%\");>";
                $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
              $menu .= "</a>" . $einfuegen . "&nbsp;";
            $menu .= "</td>";
          $menu .= "</tr>";
        $menu .= "</table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.bezeichnung, a.art, a.benutzername, 
        if(LOCATE('https://',url) > 0 OR LOCATE('http://',url) > 0,concat('<a href=\"',url,'\" target=\"_blank\" onclick=\"copyTextToClipboard(&apos;',replace(a.benutzername,'&apos;','\\\\&apos;'),'&apos;);\">',IF(LENGTH(a.url) > 60, CONCAT(LEFT(a.url,60),'...'), a.url),'</a>'), IF(LENGTH(a.url) > 60, CONCAT(LEFT(a.url,60),'...'), a.url))
        , if(a.aktiv,'Ja','Nein'),CONCAT('<input type=\"button\" value=\"URL\" onclick=\"copyTextToClipboard(&apos;',replace(replace(a.url,'\'','&apos;'),'&apos;','\\\\&apos;'),'&apos;);\">&nbsp;<input type=\"button\" value=\"Benutzer\" onclick=\"copyTextToClipboard(&apos;',replace(replace(a.benutzername,'\'','&apos;'),'&apos;','\\\\&apos;'),'&apos;);\">&nbsp;<input type=\"button\" value=\"Passwort\" onclick=\"copyTextToClipboard(&apos;',replace(replace(a.passwort,'\'','&apos;'),'&apos;','\\\\&apos;'),'&apos;);\">'), a.id FROM adresse_accounts a ";
        $where = "a.adresse='" . $id . "' AND a.bezeichnung!='Neuer Datensatz' ";

        // gesamt anzahl
        $count = "SELECT COUNT(a.id) FROM adresse_accounts a WHERE a.adresse='" . $id . "' AND a.bezeichnung!='Neuer Datensatz'";
        break;

      case "adresse_ansprechpartnerlist":
        $allowed['adresse'] = array('ansprechpartner');

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        $id = $this->app->Secure->GetGET('id');
        $lid = $this->app->Secure->GetGET("lid");
        $iframe = $this->app->Secure->GetGET("iframe");
        $alslieferadresse = $this->app->Secure->GetGET("smodule") === 'alslieferadresse';

        $tempid = $this->app->Secure->GetGET("more_data1");
        if($tempid > 0){
          $id = $tempid;
        }
        if ($lid > 0) $id = $this->app->DB->Select("SELECT adresse FROM ansprechpartner WHERE id='$lid' LIMIT 1");

        // headings
        $heading = array('', 'Name','Abteilung', 'Bereich', 'Email', 'Telefon', 'Telefax', 'Mobil', 'Men&uuml;');
        $width = array('1%', '20%', '15%', '15%', '10%', '10%', '10%', '10%', '5%');
        $findcols = array('open', 'a.name','a.abteilung', 'a.bereich', 'a.email', 'a.telefon', 'a.telefax', 'a.mobil', 'a.id');
        $searchsql = array('a.name','a.abteilung', 'a.bereich', 'a.email', 'a.telefon', 'a.telefax', 'a.mobil');
        $defaultorder = 1;
        $defaultorderdesc = 0;

        $menucol = 8;
        $moreinfo = true;
        $moreinfoaction='ansprechpartner';
        
        if ($iframe == "true"){
          $onClickFunction = $alslieferadresse ? 'AnsprechpartnerLieferscheinIframe' : 'AnsprechpartnerIframe';
          $einfuegen = "<a onclick={$onClickFunction}(\"%value%\");><img title=\"Ansprechpartner übernehmen\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/down.png\" border=\"0\"></a>";
        }
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><img title=\"Datensatz bearbeiten\" src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\" onclick=\"neuedit(%value%);\" style=\"cursor: pointer;\">&nbsp;<img title=\"Datensatz löschen\" src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\" onclick=\"deleteeintrag(%value%);\" style=\"cursor: pointer;\">" . $einfuegen . "&nbsp;</td></tr></table>";


//      $menu = "<a href=\"#\" onclick=call(\"%value%\",\"index.php?module=placetel&action=call&id=%value%\")><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/phone.png\" border=\"0\"></a>";


        $sipuid = $this->app->erp->GetPlacetelSipuid();
        if( $sipuid)
        { 
          $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, if(a.adresszusatz!='',CONCAT(a.name,if(a.interne_bemerkung='','',' <font color=red><strong>*</strong></font>'),'<br><i style=color:#999>',a.adresszusatz,'</i>'),CONCAT(a.name, if(a.interne_bemerkung='','',' <font color=red><strong>*</strong></font>'))) as name2, if(a.unterabteilung!='',CONCAT(a.abteilung,'<br><i style=color:#999>',a.unterabteilung,'</i>'),a.abteilung) as unterabteilung2, a.bereich, a.email, CONCAT('<div style=\"float: left; line-height: 20px;\">',a.telefon,'</div><a href=\"#\" onclick=call(\"3-',a.id,'\",0)><img style=\"cursor:pointer; float: right;\" height=\"20\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/phone.png\" border=\"0\"></a>'), a.telefax, CONCAT('<div style=\"float: left; line-height: 20px;\">',a.mobil,'</div><a href=\"#\" onclick=call(\"4-',a.id,'\",0)><img style=\"cursor:pointer; float: right;\" height=\"20\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/phone.png\" border=\"0\"></a>'), a.id FROM ansprechpartner a ";
        } else {
          // SQL statement
          $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, if(a.adresszusatz!='',CONCAT(a.name,if(a.interne_bemerkung='','',' <font color=red><strong>*</strong></font>'),'<br><i style=color:#999>',a.adresszusatz,'</i>'),CONCAT(a.name, if(a.interne_bemerkung='','',' <font color=red><strong>*</strong></font>'))) as name2, if(a.unterabteilung!='',CONCAT(a.abteilung,'<br><i style=color:#999>',a.unterabteilung,'</i>'),a.abteilung) as unterabteilung2, a.bereich, a.email, CONCAT('<div style=\"float: left; line-height: 20px;\">',a.telefon,'</div><a href=\"tel://',a.telefon,'\"><img style=\"cursor:pointer; float: right;\" height=\"20\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/phone.png\" border=\"0\"></a>'), a.telefax, CONCAT('<div style=\"float: left; line-height: 20px;\">',a.mobil,'</div><a href=\"tel://',a.mobil,'\"><img style=\"cursor:pointer; float: right;\" height=\"20\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/phone.png\" border=\"0\"></a>'), a.id FROM ansprechpartner a ";
        }
        $where = "a.adresse='" . $id . "' AND a.name!='Neuer Datensatz' ";
        // gesamt anzahl

        $count = "SELECT COUNT(a.id) FROM ansprechpartner a WHERE a.adresse='" . $id . "' AND a.name!='Neuer Datensatz'";
        break;
      case "adresse_lieferadressenlist":
        $allowed['adresse'] = array('lieferadresse');
        $this->app->Tpl->Add('JQUERYREADY', "$('#alle').click( function() { fnFilterColumn1( 0 ); } );");

        for ($r = 1;$r < 2;$r++) {
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

        // START EXTRA checkboxen
        
        // ENDE EXTRA checkboxen

        $id = $this->app->Secure->GetGET("id");
        $iframe = $this->app->Secure->GetGET("iframe");
        $lid = $this->app->Secure->GetGET("lid");
        
        if ($lid > 0) $id = $this->app->DB->Select("SELECT adresse FROM lieferadressen WHERE id='$lid' LIMIT 1");
        
        // headings
        $heading = array('', 'Name', 'Abteilung', 'Strasse', 'Land', 'PLZ', 'Ort', 'Telefon', 'Email', 'Men&uuml;');
        $width = array('1%', '15%', '15%', '15%', '1%', '1%', '5%','10%', '15%', '1%');
        $findcols = array('open', "CONCAT(l.name, l.adresszusatz, l.gln)", "CONCAT(l.abteilung, l.unterabteilung)", 'l.strasse', 'l.land', 'l.plz', 'l.ort', 'l.telefon', 'l.email', 'l.id');
        $searchsql = array('l.name', 'l.abteilung', 'l.strasse', 'l.land', 'l.plz', 'l.ort', 'l.telefon', 'l.email', 'l.adresszusatz', 'l.gln', 'l.unterabteilung', 'l.ustid');
        
        $defaultorder = 1;
        $defaultorderdesc = 0;

        $moreinfo = true;
        $moreinfoaction='lieferadressen';
        $menucol = 9;
        

        //                              $id = $this->app->Secure->GetGET("sid");
        $postfix = $this->app->Secure->GetGET('postfix');
        if ($iframe == "true") $einfuegen = "<a onclick=LieferadresseIframe(\"%value%\"".((String)$postfix!==""?",\"$postfix\"":'').");><img title=\"Lieferadresse übernehmen\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/down.png\" border=\"0\"></a>";
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><img title=\"Datensatz bearbeiten\" src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\" onclick=\"neuedit(%value%);\">&nbsp;<img title=\"Datensatz löschen\" src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\" onclick=\"deleteeintrag(%value%);\">" . $einfuegen . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,
        CONCAT(
          CONCAT(IF(l.standardlieferadresse,CONCAT('<strong>',l.name,' (Standardlieferadresse)</strong>'),l.name),if(l.interne_bemerkung='' OR ISNULL(l.interne_bemerkung),'',' <font color=red><strong>*</strong></font>')),
          IF(l.adresszusatz!='',CONCAT('<br><i style=color:#999>',l.adresszusatz,'</i>'),''),
          IF(l.gln!='',CONCAT('<br><i style=color:#999>GLN: ',l.gln,'</i>'),'')) AS name2,
          if(l.unterabteilung!='',CONCAT(l.abteilung,'<br><i style=color:#999>',l.unterabteilung,'</i>'),l.abteilung) as unterabteilung2, l.strasse, l.land, l.plz, l.ort, l.telefon,l.email, l.id FROM lieferadressen l ";
        
        if($this->app->Secure->GetGET("more_data1")=="1")
          $where = " l.name!='Neuer Datensatz' ";
        else
          $where = " l.adresse='" . $id . "' AND l.name!='Neuer Datensatz' ";

        //$orderby = "l.name, l.strasse";
        
        //$groupby=" GROUP by z.adresse_abrechnung ";

        
        // gesamt anzahl

        $count = "SELECT COUNT(l.id) FROM lieferadressen l WHERE ".$where;
        break;
      case "zeiterfassungprojektoffen":
        $allowed['zeiterfassung'] = array('list');

        // headings

        
        $heading = array('Projekt','Bezeichnung','Kunden-Nr','Kunde', 'Offen','Men&uuml;');
        $alignright = array(5);

        $width = array('10%','20%','10%','20%','15%','1%');

        $findcols = array('p.abkuerzung', 'p.name','adr.kundennummer','adr.name', "(SELECT SUM(TIME_TO_SEC(TIMEDIFF(z2.bis, z2.von))) FROM zeiterfassung z2 WHERE z2.adresse_abrechnung=a.id AND z2.abrechnen=1 AND z2.abgerechnet!=1)","(SELECT SUM(TIME_TO_SEC(TIMEDIFF(z2.bis, z2.von))) FROM zeiterfassung z2 WHERE z2.adresse_abrechnung=a.id AND z2.abrechnen!=1 AND z2.abgerechnet!=1)", 'z.id');
        $searchsql = array('adr.name','adr.kundennummer','p.abkuerzung', 'p.name');
        $defaultorder = 4;
        $defaultorderdesc = 1;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=projekt&action=arbeitspaket&id=%value%\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        $sql = "SELECT 'leer',
                                        p.abkuerzung, p.name,adr.kundennummer,adr.name,
         (SELECT FORMAT(SUM(TIME_TO_SEC(TIMEDIFF(z2.bis, z2.von)))/3600,2) FROM zeiterfassung z2 WHERE z2.arbeitspaket=a.id AND z2.abrechnen!=1 AND z2.abgerechnet!=1) as offen2,                                
                                           p.id
                                             FROM zeiterfassung z LEFT JOIN arbeitspaket a ON a.id=z.arbeitspaket LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON adr.id=p.kunde";

        $where = " z.abgerechnet!=1 AND a.id > 0 ";
        $groupby = " GROUP by a.projekt ";

        // gesamt anzahl
        $count = "SELECT COUNT(distinct a.projekt) FROM zeiterfassung z LEFT JOIN arbeitspaket a ON a.id=z.arbeitspaket WHERE z.abgerechnet!=1 AND z.adresse_abrechnung > 0 ";

        //                    $moreinfo = true;
        break;


      case 'gruppen_kategorienlist':
        $heading = array('Name','Projekt' , 'Men&uuml;');
        $width = array('55%','40%', '5%');
        $findcols = array('gk.bezeichnung', 'p.abkuerzung' , 'gk.id');
        $searchsql = array('gk.bezeichnung','p.abkuerzung');

        $defaultorder = 1;  //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc=0;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=gruppen_kategorien&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a></td></tr></table>";

        $menucol = 2;

        $sql = "SELECT SQL_CALC_FOUND_ROWS gk.id, gk.bezeichnung,p.abkuerzung,gk.id
              FROM gruppen_kategorien gk
              LEFT JOIN projekt p ON gk.projekt = p.id";

        $where = "1 ". $this->app->erp->ProjektRechte();
        $count = "SELECT COUNT(gk.id) FROM gruppen_kategorien gk LEFT JOIN projekt p ON gk.projekt = p.id WHERE ($where)";
        $moreinfo = false;
        break;
      default:
        if($frommodule && $fromclass) {
          $paramFrommodule = $frommodule;
          $paramFromClass = $fromclass;
          $isFromCustom = false;
          if(strtolower(substr($fromclass, -6)) === 'custom') {
            $isFromCustom = true;
            $fromclass = substr($fromclass,0, -6);
          }
          //$fromclass = str_replace('Custom','',$fromclass);
          if(strtolower(substr($frommodule,-11)) === '_custom.php') {
            $isFromCustom = true;
            $frommodule = substr($frommodule, 0, -11).'.php';
          }
          //$frommodule = str_replace('_custom.php','.php',$frommodule);
          $fromclass_custom = $fromclass;
          $frommodule_custom = $frommodule;
          //if(strpos($fromclass,'Custom')=== false)
          $fromclass_custom = $fromclass.'Custom';
          if(!strpos($frommodule, '_custom.php')) {
            $frommodule_custom = str_replace('.php','',$frommodule).'_custom.php';
          }

          if(!class_exists($fromclass) && file_exists(dirname(dirname(__DIR__)).'/www/pages/'.$frommodule)) {
            include_once dirname(dirname(__DIR__)).'/www/pages/'.$frommodule;
          }
          if($isFromCustom && !class_exists($fromclass_custom)
            && file_exists(dirname(dirname(__DIR__)).'/www/pages/'.$frommodule_custom)) {
            include_once dirname(dirname(__DIR__)).'/www/pages/'.$frommodule_custom;
          }

          $erlaubtevars = array('moreDataMaxNr','forcerowclick','rowclick','rowclickaction','matchesql','aligncenter','onequeryperuser','postfix','datesearchcols','datecols','numbercols','hidecolumns','extracallback','maxrows','nowrap','orderby','fastcount','disableautosavefilter','columnfilter','disablebuttons','doppelteids','countcol','heading','width','sql','count','findcols','searchsql','defaultorder','defaultorderdesc','menu','menucol','where','groupby','groupcol','allowed','moreinfo','moreinfoaction','sumcol','alignright','hide767','hide480','hide320','trcol','colcolor','cellcolor','rowcallback_gt','cached_count','tageditor','moreinfomodule','defferloading');
          if($isFromCustom && class_exists($fromclass_custom) && (method_exists($fromclass_custom, 'TableSearch')
            || method_exists($fromclass_custom, 'Tablesearch'))) {
            $tablesearchmethod = 'TableSearch';
            if(!method_exists($fromclass_custom, $tablesearchmethod)) {
              $tablesearchmethod = 'Tablesearch';
            }
            $MethodChecker = new ReflectionMethod($fromclass_custom,$tablesearchmethod);
            if($MethodChecker->isStatic()) {
              $erg = $fromclass_custom::$tablesearchmethod($this->app, $name, $erlaubtevars);
              $paramFrommodule = $frommodule_custom;
              $paramFromClass = $fromclass_custom;
            }
            else{
              $obj = new $fromclass_custom($this->app, true);
              $erg = $obj->$tablesearchmethod($this->app, $name, $erlaubtevars);
              $paramFrommodule = $frommodule_custom;
              $paramFromClass = $fromclass_custom;
            }
            if($erg && is_array($erg)) {
              foreach($erlaubtevars as $k => $v) {
                if(isset($erg[$v])){
                  $$v = $erg[$v];
                }
              }
            }
            if(!$sql) {
              if(class_exists($fromclass) && (method_exists($fromclass, 'TableSearch')
                || (method_exists($fromclass, 'Tablesearch')))) {
                $tablesearchmethod = 'TableSearch';
                if(!method_exists($fromclass,$tablesearchmethod)) {
                  $tablesearchmethod = 'Tablesearch';
                }
                $MethodChecker = new ReflectionMethod($fromclass,$tablesearchmethod);
                if($MethodChecker->isStatic()) {
                  $erg = $fromclass::$tablesearchmethod($this->app, $name, $erlaubtevars);
                  $paramFrommodule = $frommodule;
                  $paramFromClass = $fromclass;
                }
                else{
                  $obj = new $fromclass($this->app, true);
                  $erg = $obj->$tablesearchmethod($this->app, $name, $erlaubtevars);
                  $paramFrommodule = $frommodule;
                  $paramFromClass = $fromclass;
                }
                if($erg && is_array($erg)) {
                  foreach($erlaubtevars as $k => $v) {
                    if(isset($erg[$v])) {
                      $$v = $erg[$v];
                    }
                  }
                }
              }
            }
          }
          elseif(class_exists($fromclass) && (method_exists($fromclass, 'TableSearch') ||
            method_exists($fromclass, 'Tablesearch'))) {
            $tablesearchmethod = 'TableSearch';
            if(!method_exists($fromclass,$tablesearchmethod)) {
              $tablesearchmethod = 'Tablesearch';
            }
            $MethodChecker = new ReflectionMethod($fromclass,$tablesearchmethod);
            if($MethodChecker->isStatic()) {
              $erg = $fromclass::$tablesearchmethod($this->app, $name, $erlaubtevars);
              $paramFrommodule = $frommodule;
              $paramFromClass = $fromclass;
            }
            else{
              $obj = new $fromclass($this->app, true);
              $erg = $obj->$tablesearchmethod($this->app, $name, $erlaubtevars);
              $paramFrommodule = $frommodule;
              $paramFromClass = $fromclass;
            }
            if($erg && is_array($erg)) {
              foreach($erlaubtevars as $k => $v) {
                if(isset($erg[$v])) {
                  $$v = $erg[$v];
                }
              }
            }
          }
          $frommodule = $paramFrommodule;
          $fromclass = $paramFromClass;
        }

        break;
      }

      $erlaubtevars = array('moreDataMaxNr','forcerowclick','rowclick','aligncenter','onequeryperuser','postfix','datesearchcols','datecols','numbercols','hidecolumns','extracallback','maxrows','nowrap','orderby','fastcount','disableautosavefilter','columnfilter','disablebuttons','doppelteids','countcol','heading','width','sql','count','findcols','searchsql','defaultorder','defaultorderdesc','menu','menucol','where','groupby','groupcol','allowed','moreinfo','moreinfoaction','sumcol','alignright','hide767','hide480','hide320','trcol','colcolor','cellcolor','rowcallback_gt','cached_count');
      $hookfelder = array();
      $hookname = $name;
      foreach($erlaubtevars as $k => $v)
      {
        if(isset($$v)){
          $hookfelder[$v] =$$v;
        }
      }
      $this->app->erp->RunHook('tablesearch_after', 2, $hookname, $hookfelder);
      if(!empty($hookfelder)){
        foreach ($hookfelder as $k => $v) {
          if(!empty($k)){
            $$k = $v;
          }
        }
      }

      switch($callback) {
        case 'show':
          break;
        case 'sql':
          return $sql;
          break;
        case 'searchsql':
          return $searchsql;
          break;
        case 'searchsql_dir':
          return $searchsql_dir;
          break;
        case 'searchfulltext':
          return $searchfulltext;
          break;
        case 'defaultorder':
          return $defaultorder;
          break;
        case 'defaultorderdesc':
          return $defaultorderdesc;
          break;
        case 'heading':
          return $heading;
          break;
        case 'menu':
          return $menu;
          break;
        case 'findcols':
          return $findcols;
          break;
        case 'moreinfo':
          return $moreinfo;
          break;
        case 'allowed':
          return $allowed;
          break;
        case 'where':
          return $where;
          break;
        case 'groupby':
          return $groupby;
          break;
        case 'groupcol':
          return $groupcol;
          break;
        case 'count':
          return $count;
          break;
        case 'orderby':
          return isset($orderby)?$orderby:'';
          break;
        case 'ALL':
          return [
            'findcols' => (isset($findcols) ? $findcols : ''),
            'moreinfo' => (isset($moreinfo) ? $moreinfo : ''),
            'defaultorder' => (isset($defaultorder) ? $defaultorder : ''),
            'defaultorderdesc' => (isset($defaultorderdesc) ? $defaultorderdesc : ''),
            'where' => (isset($where) ? $where : ''),
            'searchsql' => (isset($searchsql) ? $searchsql : ''),
            'searchfulltext' => (isset($searchfulltext) ? $searchfulltext : ''),
            'columnfilter' => (isset($columnfilter) ? $columnfilter : ''),
            'sql' => (isset($sql) ? $sql : ''),
            'matchesql' => (isset($matchesql) ? $matchesql : ''),
            'groupcol' => (isset($groupcol) ? $groupcol : ''),
            'groupby' => (isset($groupby) ? $groupby : ''),
            'orderby' => (isset($orderby) ? $orderby : ''),
            'count' => (isset($count) ? $count : ''),
            'heading' => (isset($heading) ? $heading : ''),
            'fastcount' => (isset($fastcount) ? $fastcount : ''),
            'menu' => (isset($menu) ? $menu : ''),
            'datecols' => (isset($datecols) ? $datecols : ''),
            'datesearchcols' => (isset($datesearchcols) ? $datesearchcols : ''),
            'numbercols' => (isset($numbercols) ? $numbercols : ''),
            'maxrows' => (isset($maxrows) ? $maxrows : '0'),
            'onequeryperuser' => (isset($onequeryperuser) ? $onequeryperuser : '0'),
            'cached_count' => (isset($cached_count) ? $cached_count : ''),
          ];
          break;
      }

      if ($callback === 'show') {
        $this->app->Tpl->Add('ADDITIONALCSS', "

        .ex_highlight #$name tbody tr.even:hover, #example tbody tr.even td.highlighted {
        background-color: [TPLFIRMENFARBEHELL]; 
        }

        .ex_highlight_row #$name tr.even:hover {
        background-color: [TPLFIRMENFARBEHELL];
        }

        .ex_highlight_row #$name tr.even:hover td.sorting_1 {
        background-color: [TPLFIRMENFARBEHELL];
        }

        .ex_highlight_row #$name tr.odd:hover {
        background-color: [TPLFIRMENFARBEHELL];
        }

        .ex_highlight_row #$name tr.odd:hover td.sorting_1 {
        background-color: [TPLFIRMENFARBEHELL];
        }
        ");
        $moreDataMaxNrC = 31;
        if(empty($moreDataMaxNr) || (int)$moreDataMaxNr < 32) {
          $moreDataMaxNr = '';
        }
        else{
          $moreDataMaxNrC = (int)$moreDataMaxNr;
          if($moreDataMaxNrC > 50) {
            $moreDataMaxNrC = 50;
          }
          $moreDataMaxNr = '';
          for($i = 32; $i <= $moreDataMaxNrC; $i++) {
            $moreDataMaxNr .=  '
            aoData.push( { "name": "more_data'.$i.'", "value": oMoreData'.$i . $name . ' } );';
          }
        }
        //"sPaginationType": "full_numbers",
        
        //"aLengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],

        $this->app->Tpl->Add('JAVASCRIPT', " var oTable" . $name . "; ");
        for($i = 1; $i <= ($moreDataMaxNrC>31?$moreDataMaxNrC:31); $i++) {
          $_name = 'oMoreData'.$i;
          $this->app->Tpl->Add('JAVASCRIPT',"var oMoreData".$i . $name . "=".(isset($this->$_name) && $this->$_name !== '0'?"'".$this->$_name."'":0).";");// var oMoreData2" . $name . "=0; var oMoreData3" . $name . "=0; var oMoreData4" . $name . "=0; var oMoreData5" . $name . "=0;  var oMoreData6" . $name . "=0; var oMoreData7" . $name . "=0; var oMoreData8" . $name . "=0; var oMoreData9" . $name . "=0; var oMoreData10" . $name . "=0; var oMoreData11" . $name . "=0; var oMoreData12" . $name . "=0; var oMoreData13" . $name . "=0;var oMoreData14" . $name . "=0;var oMoreData15" . $name . "=0;var oMoreData16" . $name . "=0;var oMoreData17" . $name . "=0;"
        }
        
        if(isset($countcol) && $countcol && (empty($sumcol) || $sumcol != $countcol))
        {
          $this->app->Tpl->Add('JAVASCRIPT', ' 
          setInterval(function(){countcol' . $name . '();},1000);
          function countcol' . $name . '() 
          {
            /*
            $(\'#'.$name.'\').find(\'input[type="checkbox"]\').each(function(){
              $(this).on(\'change\',function(){
              countcol' . $name . '();
              });
            });*/
            var anz = 0;
            var anzges = 0;
            var isfloat = false;
            $(\'#'.$name.'\').find(\'input[type="checkbox"]\').each(function(){
              var anz2 = 0;
              $(this).parent().parent().children(\':nth-child('.$countcol.')\').each(function(){
                anz2 = $(this).html();
                if(anz2.indexOf(\'.\') > 0)anz2 = anz2.replace(/\./g, \'\'); // Tausendertrenner entfernen
                if(anz2.indexOf(\',\') > 0)anz2 = anz2.replace(\',\',\'.\'); // Dezimaltrenner in Punkt wandeln
                if(anz2.indexOf(\'.\') > 0)isfloat = true;
                
                anz2 = parseFloat(anz2);
              });
              if(!isNaN(anz2))
              {
                anzges = anzges + anz2;
                if($(this).prop(\'checked\'))anz = anz + anz2;
              }
            });
            if(isfloat)
            {
              // Mit zwei Nachkommastellen und Tausendertrenner anzeigen
              anz = anz.toLocaleString(\'de-DE\', {minimumFractionDigits: 2, maximumFractionDigits: 2});
              anzges = anzges.toLocaleString(\'de-DE\', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
            $(\'#'.$name.'\').children(\'tfoot\').find(\'tr > :nth-child('.$countcol.')\').each(function(){
              $(this).html(\'<span style="white-space:nowrap;font-weight:bold;">\'+anz+ \' / \'+anzges+\'</span>\');
            });
          }
          ');
        }

        
        $this->app->Tpl->Add('JAVASCRIPT'," var aData;");
        $smoduleRule = $this->app->Secure->GetGET('module') === 'wiki' ? 'nohtml' : null;
        $smodule = $this->app->Secure->GetGET("cmd", $smoduleRule);
        $sid = $this->app->Secure->GetGET("sid");
        
        if ($this->app->Secure->GetGET("module") == "artikel") {
          $sort = '"aaSorting": [[ 0, "desc" ]],';
        } else {
          $sort = '"aaSorting": [[ 1, "desc" ]],';
        }
        if(isset($alignright))
        {
          for ($aligni = 0;$aligni < count($alignright);$aligni++) {
            $this->app->Tpl->Add('YUICSS', '
  #' . $name . ' > tbody > tr > td:nth-child(' . $alignright[$aligni] . ') {
                    text-align: right;
                    }
                    ');
          }
        }
        if(isset($aligncenter))
        {
          for ($aligni = 0;$aligni < count($aligncenter);$aligni++) {
            $this->app->Tpl->Add('YUICSS', '
  #' . $name . ' > tbody > tr > td:nth-child(' . $aligncenter[$aligni] . ') {
                    text-align: center;
                    }
                    ');
          }
        }
        if(isset($hide320))
        {
          for ($h = 0;$h < count($hide320);$h++) {
            $this->app->Tpl->Add('YUICSS', '
            @media screen and (max-width: 320px){
              #' . $name . ' > thead > tr >  th:nth-child(' . $hide320[$h] . ') {
                display: none;
              }
              #' . $name . ' > tfoot > tr >  th:nth-child(' . $hide320[$h] . ') {
                display: none;
              }
              #' . $name . ' > tbody > tr > td:nth-child(' . $hide320[$h] . '){
                      display: none;
              }
            }
            ');
          }
        }
        
        if(isset($nowrap) && is_array($nowrap))
        {
          for ($h = 0;$h < count($nowrap);$h++) {
            $this->app->Tpl->Add('YUICSS', '
            @media screen and (max-width: 767px){
              #' . $name . ' > tbody > tr > td:nth-child(' . $hide767[$h] . '){
                      white-space: nowrap;
              }
            }
            
            ');
          }
        }
        
        if(isset($hide767))
        {
          for ($h = 0;$h < count($hide767);$h++) {
            $this->app->Tpl->Add('YUICSS', '
            @media screen and (max-width: 767px){
              #' . $name . ' > thead > tr > th:nth-child(' . $hide767[$h] . ') {
                display: none;
              }
              #' . $name . ' > tfoot > tr > th:nth-child(' . $hide767[$h] . ') {
                display: none;
              }
              #' . $name . ' > tbody > tr > td:nth-child(' . $hide767[$h] . '){
                      display: none;
              }
            }
            
            ');
          }
        }
        if(isset($hide480))
        {
          for ($h = 0;$h < count($hide480);$h++) {
            $this->app->Tpl->Add('YUICSS', '
            @media screen and (max-width: 479px){
              #' . $name . ' > thead > tr > th:nth-child(' . $hide480[$h] . ') {
                display: none;
              }
              #' . $name . ' > tfoot > tr > th:nth-child(' . $hide480[$h] . ') {
                display: none;
              }
              #' . $name . ' > tbody > tr > td:nth-child(' . $hide480[$h] . '){
                      display: none;
              }
            }
            
            ');
          }
        }
        
        if(isset($trcol))
        {
          $this->app->Tpl->Add('YUICSS', '
              #' . $name . ' > thead > tr > th:nth-child(' . ($trcol+1) . ') {
                display: none;
              }
              #' . $name . ' > tfoot > tr > th:nth-child(' . ($trcol+1) . ') {
                display: none;
              }
              #' . $name . ' > tbody > tr > td:nth-child(' . ($trcol+1) . '){
                      display: none;
              }');
        }
        
        if(isset($colcolor) && is_array($colcolor))
        {
          foreach($colcolor as $k => $color)
          {
            $this->app->Tpl->Add('YUICSS', '
            
              #' . $name . ' > tbody > tr > td:nth-child(' . $k . '){
                      background-color: '.$color.';
              }
            ');
          }
        }
        
        if(isset($hidecolumns) && $hidecolumns && is_array($hidecolumns) && count($hidecolumns) > 2)
        {
          $hidecolumnsitem = $hidecolumns[0];
          if(!is_array($hidecolumns[2]))$hidecolumns[2][0] = $hidecolumns[2];
          $this->app->Tpl->Add('JQUERYREADY','
            $(\'#'.$hidecolumnsitem.'\').on(\'change\',function(){
              var prop = '.($hidecolumns[0] == 'unchecked'?'1-':'').'$(this).prop(\'checked\')?1:0;
              var tab = $(\'#'.$name.'\');
              if(prop)
              {
          ');
          foreach($hidecolumns[2] as $vh)
          {
            //$this->app->Tpl->Add('JQUERYREADY','  
            
            $this->app->Tpl->Add('YUICSS',
            '
            .'.$name.'_hidecolums > thead > tr > th:nth-child('.$vh.') {display:none;}
            .'.$name.'_hidecolums > tbody > tr > td:nth-child('.$vh.'){display:none;}
            .'.$name.'_hidecolums > tfoot > tr > th:nth-child('.$vh.'){display:none;}
            ');
            $this->app->Tpl->Add('JQUERYREADY','  
              if(tab != null && typeof tab.toggleClass != \'undefined\')$(tab).toggleClass(\''.$name.'_hidecolums\',false);
            ');
          }
              
          $this->app->Tpl->Add('JQUERYREADY','
              
            }else{
          ');
          foreach($hidecolumns[2] as $vh)
          {
            $this->app->Tpl->Add('JQUERYREADY','  
              if(tab != null && typeof tab.toggleClass != \'undefined\')$(tab).toggleClass(\''.$name.'_hidecolums\',true);
            ');
          }              
          $this->app->Tpl->Add('JQUERYREADY','              
            }
          });
          $(\'#'.$hidecolumnsitem.'\').trigger(\'change\');
          ');
        }
        
        $this->app->Tpl->Add('YUICSS', '
                /*
                 * Row highlighting example
                 */
                .ex_highlight #' . $name . ' tbody tr.even:hover, #' . $name . ' tbody tr.even td.highlighted {
                background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight #' . $name . ' tbody tr.odd:hover, #' . $name . ' tbody tr.odd td.highlighted {
                background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #' . $name . ' tr.even:hover {
                background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #' . $name . ' tr.even:hover td.sorting_1 {
                background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #' . $name . ' tr.even:hover td.sorting_2 {
                background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #' . $name . ' tr.even:hover td.sorting_3 {
                background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #' . $name . ' tr.odd:hover {
                  background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #' . $name . ' tr.odd:hover td.sorting_1 {
                  background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #' . $name . ' tr.odd:hover td.sorting_2 {
                  background-color: #E0FF84;
                }
                .ex_highlight_row #' . $name . ' tr.odd:hover td.sorting_3 {
                  background-color: #DBFF70;
                }
                ');
        
        if ($name == "artikeltabelle") {

          //$js="alert($(nTds[0]).text()); //window.location.href='index.php?module=artikel&action=edit&nummer='+$(nTds[0]).text();";
          
        } else $js = "";
        $anzahl_datensaetze = $this->app->erp->Firmendaten("standard_datensaetze_datatables");
        
        if ($anzahl_datensaetze != 0 && $anzahl_datensaetze != "10" && $anzahl_datensaetze != "25" && $anzahl_datensaetze != "50" && $anzahl_datensaetze != "200" && $anzahl_datensaetze != "1000" && $anzahl_datensaetze != "")
        {
          $extra_anzahl_datensaetze = $anzahl_datensaetze . ",";
        }
        else {
          
          if ($anzahl_datensaetze > 0) {

            //$extra_anzahl_datensaetze=$anzahl_datensaetze.",";
            
          } else {
            $extra_anzahl_datensaetze = '';
            $anzahl_datensaetze = '10';
          }
        }
        if(isset($maxrows) && ($maxrows > 0) && ($maxrows < $anzahl_datensaetze))
        {
          $extra_anzahl_datensaetze = $maxrows;
          if($anzahl_datensaetze > $maxrows)$anzahl_datensaetze = $maxrows;
        }
        
        
         if (!empty($sumcolnumber) && $sumcolnumber >= 1) {
          $sumcolnumber = $sumcolnumber - 1;
          $footercallback = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    
                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                      return typeof i === \'string\' ?
                        i.replace(/[\$,.]/g, \'\')*1 :
                        typeof i === \'number\' ?
                        i : 0;
                    };

                    // Total over all pages
                    data = api.column( ' . $sumcolnumber . ' ).data();
                    total = data.length ?
                      data.reduce( function (a, b) {
                          if(String(a) == \'\')a = 0;
                          if(String(b) == \'\')b = 0;
                          return intVal(a) + intVal(b);
                          } ) :
                        0;

                        // Total over this page
                        data = api.column( ' . $sumcolnumber . ', { page: \'current\'} ).data();
                        pageTotal = data.length ?
                          data.reduce( function (a, b) {
                              if(String(a) == \'\')a = 0;
                              if(String(b) == \'\')b = 0;
                              return intVal(a) + intVal(b);
                              } ) :
                            0;

                            $( api.column( ' . $sumcolnumber . ' ).footer() ).html(
                                \'<font color=blue>&nbsp;\' + pageTotal + \'</font>\' 
                                );
                  },
                    ';
        }
 
        if(!empty($sumcol) && is_array($sumcol))
        {
          $footercallback = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                      return typeof i === \'string\' ?
                        i.replace(/[\$,.]/g, \'\')*1 :
                        typeof i === \'number\' ?
                        i : 0;
                    };
              ';
          foreach($sumcol as $_sumcol)
          {
            
          $_sumcol = $_sumcol - 1;
          $footercallback .= '
                    // Total over all pages
                    data = api.column( ' . $_sumcol . ' ).data();
                    total = data.length ?
                      data.reduce( function (a, b) {
                        if(String(a) == \'\')a = 0;
                        if(String(b) == \'\')b = 0;
                        var stra = (a+\'\').replace(\'</font>\',\'\').replace(\'<font color="red">\',\'\').replace(\'<font color=red>\',\'\');
                        
                        if(stra.indexOf(\'.\') >= 0 && stra.indexOf(\',\') >= 0 )
                        {
                          a = parseFloat(stra.replace(\'.\',\'\').replace(\'.\',\'\').replace(\'.\',\'\').replace(\'.\',\'\').replace(\',\',\'.\'));
                        }else{
                          if(stra.indexOf(\'.\') >= 0)
                          {
                            a = parseFloat(stra);
                          }else{
                            if(stra.indexOf(\',\') >= 0)
                            {
                              a = parseFloat(stra.replace(\',\',\'.\'));
                            }else{
                              a = parseFloat(stra);
                            }
                          }
                        }
                        var strb = (b+\'\').replace(\'</font>\',\'\').replace(\'<font color="red">\',\'\').replace(\'<font color=red>\',\'\');
                        if(strb.indexOf(\'.\') >= 0 && strb.indexOf(\',\') >= 0 )
                        {
                          b = parseFloat(strb.replace(\'.\',\'\').replace(\'.\',\'\').replace(\'.\',\'\').replace(\'.\',\'\').replace(\',\',\'.\'));
                        }else{
                          if(strb.indexOf(\'.\') >= 0)
                          {
                            b = parseFloat(strb);
                          }else{
                            if(strb.indexOf(\',\') >= 0)
                            {
                              b = parseFloat(strb.replace(\',\',\'.\'));
                            }else{
                              b = parseFloat(strb);
                            }
                          }
                        }
                        return (a+b).toFixed(2);
                        
                          return intVal(a) + intVal(b);
                          } ) :
                        0;

                        // Total over this page
                        data = api.column( ' . $_sumcol . ', { page: \'current\'} ).data();
                        pageTotal = data.length ?
                          data.reduce( function (a, b) { 
                          if(String(a) == \'\')a = 0;
                          if(String(b) == \'\')b = 0;
                        var stra = (a+\'\').replace(\'</font>\',\'\').replace(\'<font color="red">\',\'\').replace(\'<font color=red>\',\'\');
                        if(stra.indexOf(\'.\') >= 0 && stra.indexOf(\',\') >= 0 )
                        {
                          a = parseFloat(stra.replace(\'.\',\'\').replace(\'.\',\'\').replace(\'.\',\'\').replace(\'.\',\'\').replace(\',\',\'.\'));
                        }else{
                          if(stra.indexOf(\'.\') >= 0)
                          {
                            a = parseFloat(stra);
                          }else{
                            if(stra.indexOf(\',\') >= 0)
                            {
                              a = parseFloat(stra.replace(\',\',\'.\'));
                            }else{
                              a = parseFloat(stra);
                            }
                          }
                        }
                        var strb = (b+\'\').replace(\'</font>\',\'\').replace(\'<font color="red">\',\'\').replace(\'<font color=red>\',\'\');
                        if(strb.indexOf(\'.\') >= 0 && strb.indexOf(\',\') >= 0 )
                        {
                          b = parseFloat(strb.replace(\'.\',\'\').replace(\'.\',\'\').replace(\'.\',\'\').replace(\'.\',\'\').replace(\',\',\'.\'));
                        }else{
                          if(strb.indexOf(\'.\') >= 0)
                          {
                            b = parseFloat(strb);
                          }else{
                            if(strb.indexOf(\',\') >= 0)
                            {
                              b = parseFloat(strb.replace(\',\',\'.\'));
                            }else{
                              b = parseFloat(strb);
                            }
                          }
                        }

                        return (a+b).toFixed(2);
                              return intVal(a) + intVal(b);
                              } ) :
                            0;

                            //                                      if(typeof pageTotal === \'int\')
                            if(data.length > 1)
                            {
                              //pageTotal = pageTotal / 100.0;  
                              text = pageTotal.toString();

                              var parts = text.toString().split(".");
                              parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                              showTotal =  parts.join(",");
                            }
                            else if(data.length > 0) { 
                              pageTotal = pageTotal.replace(/,/, "A");
                              pageTotal = pageTotal.replace(/A/, "\,");
                              showTotal = pageTotal;
                            }
                            else showTotal = 0;

                            $( api.column( ' . $_sumcol . ' ).footer() ).html(
                                \'<font color=red>&Sigma;&nbsp;\' + showTotal + \'</font>\' 
                                );
                  
                    ';
            
          }
          $footercallback .= '},';
        }elseif (!empty($sumcol) && $sumcol >= 1) {
          $sumcol = $sumcol - 1;
          $footercallback = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                      return typeof i === \'string\' ?
                        i.replace(/[\$,.]/g, \'\')*1 :
                        typeof i === \'number\' ?
                        i : 0;
                    };

                    // Total over all pages
                    data = api.column( ' . $sumcol . ' ).data();
                    total = data.length ?
                      data.reduce( function (a, b) {
                          if(String(a) == \'\')a = 0;
                          if(String(b) == \'\')b = 0;
                          return intVal(a.toString().replace(\'</font>\',\'\').replace(\'<font color="red">\',\'\').replace(\'<font color=red>\',\'\').replace(/[^\.\,\d\-]/g, \'\').replace(\',\',\'.\')) + intVal(b.toString().replace(\'</font>\',\'\').replace(\'<font color="red">\',\'\').replace(\'<font color=red>\',\'\').replace(/[^\.\,\d\-]/g, \'\').replace(\',\',\'.\'));
                          } ) :
                        0;

                        // Total over this page
                        data = api.column( ' . $sumcol . ', { page: \'current\'} ).data();
                        pageTotal = data.length ?
                          data.reduce( function (a, b) {
                              return intVal(a.toString().replace(\'</font>\',\'\').replace(\'<font color="red">\',\'\').replace(\'<font color=red>\',\'\').replace(/[^\.\,\d\-]/g, \'\').replace(\',\',\'.\')) + intVal(b.toString().replace(\'</font>\',\'\').replace(\'<font color="red">\',\'\').replace(\'<font color=red>\',\'\').replace(/[^\.\,\d\-]/g, \'\').replace(\',\',\'.\'));
                              } ) :
                            0;

                            //                                      if(typeof pageTotal === \'int\')
                            if(data.length > 1)
                            {
                              pageTotal = pageTotal / 100.0;  
                              text = pageTotal.toString();

                              var parts = text.toString().split(".");
                              parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                              showTotal =  parts.join(",");
                            }
                            else if(data.length > 0) { 
                              pageTotal = pageTotal.replace(/,/, "A");
                              pageTotal = pageTotal.replace(/A/, "\,");
                              showTotal = pageTotal;
                            }
                            else showTotal = 0;

                            $( api.column( ' . $sumcol . ' ).footer() ).html(
                                \'<font color=red>&Sigma;&nbsp;\' + showTotal + \'</font>\' 
                                );
                  },
                    ';
        }
        
        if ($name == "versandoffene" || $name == "auftragscockpit_list") {
          $bStateSave = "false";
          $cookietime = 0;

        } else {
          $bStateSave = "true";
          $cookietime = 365 * 24 * 60 * 60; // 1 Jahr
        }
        $iframe = $this->app->Secure->GetGET("iframe");
if($this->app->erp->Firmendaten("datatables_export_button_flash")=="1")
{
   $tabletools = '
"dom": \'lfrtipB\',
"buttons": [
            \'copy\', \'csv\', \'excel\', 
            , \'pdf\'
            , \'print\'
        ],
                      ';
  if(!empty($disablebuttons))$tabletools = '';
}

$_module = $this->app->Secure->GetGET("module");
$_action = $this->app->Secure->GetGET("action");
$directlink=0;
if( (($this->app->erp->RechteVorhanden($_module,"edit") && $_action=="list") ||
    (!empty($forcerowclick))) && !empty($rowclick))
{
  if($menucol > 0 || count($heading) > 0) {
    if(!empty($doppelteids)){
      $doppelteids = '\-[1-9]{1}[0-9]*';
      }else{
      $doppelteids = '';
    }
    if(!empty($menucol) && $menucol > 0)
    {
      $tmpmenucol=$menucol;
    } else {
      $tmpmenucol=!empty($heading)?count($heading)-1:0;
    }


if(!empty($menucol) && $menucol > 0 || count($heading) > 0) {
  if(isset($doppelteids)&& $doppelteids){
    $doppelteids = '\-[1-9]{1}[0-9]*';
  }
  }else{
    $doppelteids = '';
  }
  if(!empty($menucol) && $menucol > 0)
  {
    $tmpmenucol=$menucol;
  } else {
    $tmpmenucol=count($heading)-1;
  }

  if(empty($rowclickaction) || $rowclickaction=="")
  {
    $rowclickaction="edit";
  }

      $jsscriptcallback ='
          var str = aData[' . $tmpmenucol . '];
                              if (str.match(\'string\')) {
                                str = str.replace("string", "");
                                var menuid = str;
                              } else {                            var match = str.match(/[1-9]{1}[0-9]*'.$doppelteids.'/);
                                '.($doppelteids==''?'var menuid = parseInt(match[0], 10);':'var menuid = match[0];').'
                              }
      window.location.href=\'index.php?module='.$_module.'&action='.$rowclickaction.'&id=\'+menuid;';

      if(!isset($rowcallback_gt))
      {
        $rowcallback_gt = 0;
      } else{
        $directlink = 1;
      }

  } else {
    if(!isset($rowcallback_gt))
    {
      $rowcallback_gt=-1;
    }
    $checkmenucol=0;
  }

}
if($directlink || (!empty($rowclickaction) && !empty($jsscriptcallback)))
{
  $this->app->Tpl->Add('JAVASCRIPT','function setCursorTmp'.$name.'() {$("#'.$name.' tbody td").css( \'cursor\', \'pointer\' );}');
  $this->app->Tpl->Add('JQUERYREADY',' setInterval("setCursorTmp'.$name.'()",1000);');
}
if(empty($smodule))
{
  $smodule = !empty($_module)?$_module:'';
}


$aLengthMenuArr = array(10, 25, 50,200,1000);
if((isset($extra_anzahl_datensaetze) && (int)$extra_anzahl_datensaetze > 0) || (isset($maxrows) && $maxrows > 0))
{
  $_aLengthMenuArr = $aLengthMenuArr;
  $aLengthMenuArr = null;
  foreach($_aLengthMenuArr as $k => $v)
  {
    if(isset($maxrows) && (int)$maxrows > 0)
    {
      if($v > $maxrows)
      {
        if(!empty($aLengthMenuArr))
        {
          if($aLengthMenuArr[count($aLengthMenuArr)-1] < $maxrows)
          {
            $aLengthMenuArr[] = (int)$maxrows;
            break;
          }
        }else{
          $aLengthMenuArr[] = (int)$maxrows;
          break;
        }
      }
    }
    if((isset($extra_anzahl_datensaetze) && (int)$extra_anzahl_datensaetze > 0))
    {
      if(empty($aLengthMenuArr))
      {
        if($extra_anzahl_datensaetze < $v)
        {
          $aLengthMenuArr[] = (int)$extra_anzahl_datensaetze;
        }
      }elseif(!in_array($extra_anzahl_datensaetze, $aLengthMenuArr))
      {
        if($v > $extra_anzahl_datensaetze)
        {
          $aLengthMenuArr[] = (int)$extra_anzahl_datensaetze;
        }elseif($v < $extra_anzahl_datensaetze)
        {
          if($k == count($_aLengthMenuArr)-1)
          {
            $aLengthMenuArr[] = (int)$extra_anzahl_datensaetze;
          }
        }
      }
    }
    if(empty($maxrows) || (int)$v < $maxrows)
    {
      $aLengthMenuArr[] = (int)$v;
    }
  }
}
$aLengthMenuArrStr = implode(',', $aLengthMenuArr);


   if(!isset($columnfilter))
   {
     if(empty($extracallback))
     {
       $extracallback = '';
     }
     $extracallback .= '
        if(typeof json != \'undefinded\' && typeof json.iTotalRecords != \'undefined\' && json.iTotalRecords )
        {
          $(\'#' . $name . '\').toggleClass(\'trsnotempty\',true);
        }else{
          $(\'#' . $name . '\').toggleClass(\'trsnotempty\',false);
        }
        setTimeout(function(){
          var throws = $(\'#'.$name.' > thead > tr\'); 
          if(typeof throws[1] != \'undefined\')
          {
            var emptytd = $(\'#'.$name.' > tbody > tr > td.dataTables_empty\');
            $(throws[1]).toggleClass(\'allowhide\', true);
            if(emptytd.length)
            {
              var trausblenden = !$(\'#' . $name . '\').hasClass(\'trsnotempty\');
              $(throws[1]).find(\'input\').each(function(){
                if(!$(this).hasClass(\'search_init\'))
                {
                  trausblenden = false;
                }
              });
              $(\'#'.$name.'_filter input\').each(function()
              {
                var tmpval = $(this).val()+\'\';
                if(tmpval != \'\')
                {
                  trausblenden = false;
                  $(throws[1]).toggleClass(\'allowhide\', false);
                }
              });
              if(true)
              {
                $(\'.table_filter\').each(function(){
                  if($(this).css(\'display\') != \'none\')
                  {
                    trausblenden = false;
                    $(throws[1]).toggleClass(\'allowhide\', true);
                  }
                });
                if(trausblenden)
                {
                  $(throws[1]).hide();
                  $(throws[1]).toggleClass(\'forcehide\', true);
                }
              }
              
            }else {
              $(throws[1]).toggleClass(\'forcehide\', false);
              $(throws[1]).toggleClass(\'allowhide\', true);
              var trausblenden = true;
              $(throws[1]).find(\'input\').each(function(){
                if(!$(this).hasClass(\'search_init\'))
                {
                  trausblenden = false;
                }
              });
              $(\'#'.$name.'_filter input\').each(function()
              {
                var tmpval = $(this).val()+\'\';
                if(tmpval != \'\')
                {
                  trausblenden = false;
                  $(throws[1]).toggleClass(\'allowhide\', true);
                }
              });
              if(true)
              {
                $(\'.table_filter\').each(function(){
                  if($(this).css(\'display\') != \'none\'){
                    trausblenden = false;
                    $(throws[1]).toggleClass(\'allowhide\', true);
                  }
                });
              }            
              if(!trausblenden)
              {
                $(throws[1]).show(300);
              }else{
                $(throws[1]).show(300);
                //$(throws[1]).hide(300);
              }
            }
          }
        },100);
     ';
   }

        if(!empty($tageditor))
        {
          if(empty($extracallback)) {
            $extracallback = '';
          }
            $extracallback .= '
              window.setTimeout(function() {
                $(\'.tageditor\').tagEditor({
                  sortable:false,
                  onChange: function(field, editor, tags) {
                    return false;
                  },
                  beforeTagSave: function(field, editor, tags, tag, val) {
                    return false;
                  },
                  beforeTagDelete: function(field, editor, tags, val) {
                    return false;
                  }
                });
                
                // Input-Feld mit Tag-Liste nach dem ersten Ausführen löschen.
                // Ansonsten werden Tags mehrfach gerendert. Jeder aktivierter Filter löst ein zusätzliches Rendering aus.
                // Grund für dieses Verhalten sind die zusätzlichen AJAX-Requests pro aktiviertem Filter. 
                $(\'input.tageditor\').remove();
                
                $(\'#' . $name . '\').find(\'div.tag-editor-delete\').hide();
                $(\'#' . $name . '\').find(\'ul.tag-editor\').prop(\'disabled\',true);
                $(\'#' . $name . '\').find(\'ul.tag-editor\').off(\'click\');
                $(\'#' . $name . '\').find(\'ul.tag-editor\').off(\'paste\');
                $(\'#' . $name . '\').find(\'ul.tag-editor\').off(\'keydown\');
                $(\'#' . $name . '\').find(\'ul.tag-editor\').off(\'keypress\');
              }, 100);
            ';
        }
        if ($name == "versandoffene" || $name == "auftragscockpit_list") {
          $bStateSave = "false";
          $cookietime = 0;

        } else {
          $bStateSave = "true";
          $cookietime = 365 * 24 * 60 * 60; // 1 Jahr
        }

        if (!empty($groupcol)) {
            $rowgroupDataSrc = null;
            if (is_int($groupcol)) {
                $rowgroupDataSrc = (string)$groupcol;
            }
            if (is_array($groupcol)) {
                $rowgroupDataSrc = '[' . implode(', ', $groupcol) . ']';
            }
        }
        $rowgroupOption = !empty($rowgroupDataSrc) ? 'rowGroup: { dataSrc: ' . $rowgroupDataSrc . ' }, ' : 'rowGroup: false, ';

        $this->app->Tpl->Add('DATATABLES', '
                    var currentdate = new Date();
                    var datetime = "'.$this->app->Tpl->pruefeuebersetzung('Stand').': " + currentdate.getDay() + "."+currentdate.getMonth() 
                    + "." + currentdate.getFullYear() + " um " 
                    + currentdate.getHours() + ":" 
                    + currentdate.getMinutes() + ":" + currentdate.getSeconds() + " '.$this->app->Tpl->pruefeuebersetzung('von').' ' . $this->app->User->GetName() . '";

                    // Add custom button type
                    $.fn.dataTable.ext.buttons.excelFormatMoney = {
                      extend: \'excelHtml5\',
                      _exportTextarea: $(\'<textarea/>\')[0],
                      action: function (event, dt, node, config) {
                        if (typeof config.exportOptions === \'undefined\') {
                          config.exportOptions = {};
                        }
                        if (typeof config.exportOptions.format === \'undefined\') {
                          config.exportOptions.format = {};
                        }
                        if (typeof config.exportOptions.decodeEntities === \'undefined\') {
                          config.exportOptions.decodeEntities = true;
                        }
                        
                        // Overwrite export format function
                        config.exportOptions.format.body = function (data, row, column, node) {
                          if (typeof data !== \'string\') {
                            return data;
                          }
                          
                          data = $.fn.dataTable.ext.buttons.excelFormatMoney.formatMoneyValue(data);
                          data = $.fn.dataTable.ext.buttons.excelFormatMoney.stripHtml(data);
                          
                          if (config.exportOptions.decodeEntities) {
                            data = $.fn.dataTable.ext.buttons.excelFormatMoney.decodeEntities(data);
                          }
                          
                          return data;
                        };
                        
                        // Call the default excelHtml5 action method to create the excel file
                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, event, dt, node, config);
                      },
                      
                      /** @source www/js/datatables/datatables.js Line 76767 */
                      stripHtml: function (str) {
                        if (typeof str !== \'string\') {
                          return str;
                        }
                    
                        // Always remove script tags
                        str = str.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, \'\');
                    
                        // Always remove comments
                        str = str.replace(/<!\-\-.*?\-\->/g, \'\');
                        
                        // Strip HTML tags                  
                        str = str.replace(/<[^>]*>/g, \'\');
                        str = str.replace(/&nbsp;/ig, \' \');
                        
                        // Trim value
                        str = str.replace(/^\s+|\s+$/g, \'\');
                    
                        return str;
                      },
                      
                      formatMoneyValue: function (str) {
                        // Datum unverändert ausgeben
                        // (notwendig da nachfolgendes isGermanMoneyValue-Pattern auch auf deutsches Datumsformat matched)
                        var isDateValue = /^\d{2}\.\d{2}\.\d{2,4}$/.test(str);
                        if (isDateValue) {
                          return str;
                        }
                        
                        // Deutsches Währungsformat "123.456.789,12" erkennen und umwandeln
                        // Ansonsten werden Geldbeträge in Excel falsch umgewandelt
                        // Beträge im US-Format "123,456,789.12" werden nicht umgewandelt
                        var isGermanMoneyValue = /^([\+\-])?(\d+,{1}|(\d+\.{1}\d+)+,{1})*\d+$/.test(str);
                        if (isGermanMoneyValue) {
                         str = str.replace(/[\.]/g,\'\'); // Mehrere Punkte ersetzen
                         str = str.replace(\',\', \'.\'); // Komma durch Punkt ersetzen
                        }
                        
                        return str;
                      },
                      
                      decodeEntities: function (str) {
                        if (typeof this._exportTextarea === \'undefined\') {
                          return str;
                        }
                        
                        this._exportTextarea.innerHTML = str;
                        str = this._exportTextarea.value;
			                  
                        return str;
                      }
                    };

                     oTable' . $name . ' = $(\'#' . $name . '\').dataTable( {
                      "bAutoWidth": false,
                      "bProcessing": true,
                      
                      "responsive":false,
fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {'.(($directlink || (!empty($rowclickaction) && !empty($jsscriptcallback)))?'
// Cell click
$(\'td:gt('.$rowcallback_gt.'):lt(' . ($tmpmenucol - ($rowcallback_gt>1?1+$rowcallback_gt:2)) . ')\', nRow).on(\'click\', function() {
'.$jsscriptcallback.'
});
':'').'
},
                      "aLengthMenu": [[' .$aLengthMenuArrStr  .'], [' .$aLengthMenuArrStr  .']],
                      "iDisplayLength": ' . $anzahl_datensaetze . ',
                      "bStateSave": ' . $bStateSave . ',
                      "iCookieDuration": ' . (int)$cookietime . ',
                      ' . $sort . '
                      "bServerSide": true,
                      '.(empty($tabletools)?'':$tabletools).'
                      "language": {
                        "decimal": ",",
                        "thousands": ".",
                        "paginate": {first: "Erste", last: "Letzte", next: ">>", previous: "<<"},
                        "emptyTable": "Keine Daten vorhanden",
                        "info": "Zeige _START_ bis _END_ von _TOTAL_ Einträgen",
                        "infoEmpty": "Zeige 0 bis 0 von 0 Einträge",
                        "infoFiltered": "(gefiltert von _MAX_ Einträgen)",
                        "infoPostFix": "",
                        "lengthMenu": "_MENU_ Eintr&auml;ge pro Seite",
                        "loadingRecords": "Loading...",
                        "processing": "",
                        "search": "Suche:",
                        "searchPlaceholder": "",
                        "zeroRecords": "Keine Einträge gefunden",
                        "aria": {
                          "sortAscending": ": activate to sort column ascending",
                          "sortDescending": ": activate to sort column descending"
                        }
                      },
                      ' . $rowgroupOption . '
                      "buttons": {
                        "dom": {"button": {"tag": "a"}},
                        "buttons": [
                          { "extend": "copy", "text": "Zwischenablage" },
                          { "extend": "csv", "text": "CSV" },
                          { "extend": "excelFormatMoney", "text": "Excel" },
                          { "extend": "pdf", "text": "PDF", "orientation": "landscape", "pageSize": "A4" },
                          { "extend": "print", "text": "Drucken" }
                        ]
                      },
                      stateLoadParams: function (settings, data) {
                        if (typeof data !== \'object\') {
                          return;
                        }
                        
                        // Alter des gespeicherten States/Zustands bestimmen 
                        var currentTimestamp = Math.floor(Date.now() / 1000);
                        var savedStateTimestamp = Math.floor(data.time / 1000);
                        var savedStateAge = currentTimestamp - savedStateTimestamp;
                      
                        // Suche nach 10 Minuten (600 Sekunden) Inaktivität leeren
                        if (savedStateAge > 600) {
                          data.search.search = \'\';
                          data.columns.forEach(function (column) {
                            column.search.search = \'\';                          
                          });
                        }
                        
                        // Beim ersten Aufruf immer Seite 1 aufrufen
                        if (data.start > 0) {
                          data.start = 0;
                        }
                      },
                      "fnInitComplete": function (){
                        '.((isset($countcol) && $countcol && (empty($sumcol) || $sumcol != $countcol))?'countcol' . $name . '();':'').'
                        $(oTable' . $name . '.fnGetNodes()).click(function (){
                            var nTds = $(\'td\', this);
                            ' . $js . ' //alert($(nTds[1]).text());// my js window....
                            });},
                      ' . (isset($footercallback)?$footercallback:'') . '
                          
                        "fnServerData": function ( sSource, aoData, fnCallback ,oSettings) {
                       
                       oSettings.jqXHR = $.ajax( {
                                    "dataType": \'json\',
                                    "type": "GET",
                                    "url": sSource,
                                    "data": aoData,
                                    "success": fnCallback
                                  } );
                                   if (typeof $ !== "undefined" && $.fn.dataTable) {
            var all_settings = $($.fn.dataTable.tables()).DataTable().settings();
            for (var i = 0, settings; (settings = all_settings[i]); ++i) {
             
                if (settings.jqXHR) {                    
                settings.jqXHR.abort();
               
                }
            }
        }
                        
                        
                          /* Add some extra data to the sender */
                          '.((isset($countcol) && $countcol && (empty($sumcol) || $sumcol != $countcol))?'countcol' . $name . '();':'').'
                          aoData.push( { "name": "more_data1", "value": oMoreData1' . $name . ' } );
                          aoData.push( { "name": "more_data2", "value": oMoreData2' . $name . ' } );
                          aoData.push( { "name": "more_data3", "value": oMoreData3' . $name . ' } );
                          aoData.push( { "name": "more_data4", "value": oMoreData4' . $name . ' } );
                          aoData.push( { "name": "more_data5", "value": oMoreData5' . $name . ' } );
                          aoData.push( { "name": "more_data6", "value": oMoreData6' . $name . ' } );
                          aoData.push( { "name": "more_data7", "value": oMoreData7' . $name . ' } );
                          aoData.push( { "name": "more_data8", "value": oMoreData8' . $name . ' } );
                          aoData.push( { "name": "more_data9", "value": oMoreData9' . $name . ' } );
                          aoData.push( { "name": "more_data10", "value": oMoreData10' . $name . ' } );
                          aoData.push( { "name": "more_data11", "value": oMoreData11' . $name . ' } );
                          aoData.push( { "name": "more_data12", "value": oMoreData12' . $name . ' } );
                          aoData.push( { "name": "more_data13", "value": oMoreData13' . $name . ' } );
                          aoData.push( { "name": "more_data14", "value": oMoreData14' . $name . ' } );
                          aoData.push( { "name": "more_data15", "value": oMoreData15' . $name . ' } );
                          aoData.push( { "name": "more_data16", "value": oMoreData16' . $name . ' } );
                          aoData.push( { "name": "more_data17", "value": oMoreData17' . $name . ' } );
                          aoData.push( { "name": "more_data18", "value": oMoreData18' . $name . ' } );
                          aoData.push( { "name": "more_data19", "value": oMoreData19' . $name . ' } );
                          aoData.push( { "name": "more_data20", "value": oMoreData20' . $name . ' } );
                          aoData.push( { "name": "more_data21", "value": oMoreData21' . $name . ' } );
                          aoData.push( { "name": "more_data22", "value": oMoreData22' . $name . ' } );
                          aoData.push( { "name": "more_data23", "value": oMoreData23' . $name . ' } );
                          aoData.push( { "name": "more_data24", "value": oMoreData24' . $name . ' } );
                          aoData.push( { "name": "more_data25", "value": oMoreData25' . $name . ' } );
                          aoData.push( { "name": "more_data26", "value": oMoreData26' . $name . ' } );
                          aoData.push( { "name": "more_data27", "value": oMoreData27' . $name . ' } );
                          aoData.push( { "name": "more_data28", "value": oMoreData28' . $name . ' } );
                          aoData.push( { "name": "more_data29", "value": oMoreData29' . $name . ' } );
                          aoData.push( { "name": "more_data30", "value": oMoreData30' . $name . ' } );
                          aoData.push( { "name": "more_data31", "value": oMoreData31' . $name . ' } );
                          '.(!empty($moreDataMaxNr)?$moreDataMaxNr:'').'
                          $.getJSON( sSource, aoData, function (json) {
                              /* Do whatever additional processing you want on the callback, then tell DataTables */
                              fnCallback(json);
                              '.(isset($extracallback)?$extracallback:'').'
                              
                              $(\'#'.$name.'\').trigger(\'afterreload\');
                              
                                      ');  
                                      
        if(!empty($trcol))
                        $this->app->Tpl->Add('DATATABLES', '
                              $(\'#' . $name . ' td:nth-child(' . ($trcol+1) . ')\').each(function(){
                              if($(this).html() != "")
                              {
                                $(this).parent("tr").find("td").css("background-color",$(this).html());
                                $(this).parent("tr").find("td").css("border-bottom","1px solid #ddd");
                              }else{
                                $(this).parent("tr").find("td").css("border-bottom","");
                              }
                              });
                            ;
                            
                            ');


                            
        if(!empty($cellcolor))
        {
          foreach($cellcolor as $keyc => $vc)
          {
            
              $this->app->Tpl->Add('YUICSS', '
                  #' . $name . ' > thead > tr > th:nth-child(' . ($keyc) . ') {
                    display: none;
                  }
                  #' . $name . ' > tfoot > tr > th:nth-child(' . ($keyc) . ') {
                    display: none;
                  }
                  #' . $name . ' > tbody > tr > td:nth-child(' . ($keyc) . '){
                          display: none;
                  }');
            
            $this->app->Tpl->Add('DATATABLES', '
                  $(\'#' . $name . ' td:nth-child(' . ($keyc) . ')\').each(function(){
                  if($(this).html() != "")
                  {
                    $(this).parent("tr").find("td:nth-child(' . ($vc) . ')").css("background-color",$(this).html());
                    $(this).parent("tr").find("td:nth-child(' . ($vc) . ')").css("border-bottom","1px solid #ddd");
                  }else{
                    $(this).parent("tr").find("td:nth-child(' . ($vc) . ')").css("border-bottom","");
                  }
                  });
                ;
                
                ');
            
            
          }
        }
        $postfix = $this->app->Secure->GetGET('postfix');
        $this->app->Tpl->Add('DATATABLES',     '          
                              } );
                        },

                      "sAjaxSource": "./index.php?module=ajax&action=table&smodule=' . $smodule . '&cmd=' . $name . '&id=' . $id . '&iframe=' . $iframe . '&sid=' . $sid.($frommodule&&$fromclass?'&frommodule='.$frommodule.'&fromclass='.$fromclass:'').($postfix?'&postfix='.$postfix:'').'&uid='.uniqid($name,false).(!empty($defferloading)?'&deferLoading=1':'') .'"
                    } );
                    
                    
                     $(\'#'.$name.'\').on(\'preXhr.dt\', function ( e, settings, data ) {
          if (settings.jqXHR) {
          settings.jqXHR.abort();
          }
        });
                    ');

        if(!empty($columnfilter)) {
          $this->app->Tpl->Add('DATATABLES', '$(\'#'.$name.'\').dataTable().columnFilter();');
        }
        elseif(
          !(isset($columnfilter) && $columnfilter === false)
        ){
          $this->app->Tpl->Add('DATATABLES', 'if(typeof $(\'#'.$name.'\').dataTable().columnFilter != \'undefined\') $(\'#'.$name.'\').dataTable().columnFilter({"sPlaceHolder":"head:after"});
          /*var '.$name.'filtertimeout = null;
          $(\'#'.$name.' > thead > tr\').first().on(\'mouseover\', function(){
            if(!$(this).hasClass(\'forcehide\'))
            {
              $(this).next().show(300);
              if('.$name.'filtertimeout != null)clearInterval('.$name.'filtertimeout);
            }            
          });
          $(\'#'.$name.' > thead > tr\').first().next().on(\'mouseover\', function(){
            if(!$(this).hasClass(\'forcehide\'))
            {
              $(this).show(300);
              if('.$name.'filtertimeout != null)clearInterval('.$name.'filtertimeout);
            }            
          });
          $(\'#'.$name.' > thead > tr\').first().on(\'mouseleave\', function(){
            if(!$(this).next().hasClass(\'allowhide\'))return;
            if('.$name.'filtertimeout != null)clearInterval('.$name.'filtertimeout);
            '.$name.'filtertimeout = setInterval(function(){            
              var infocus = $(\'#'.$name.' > thead > tr\').first().next().find(\':focus\');
              if(infocus.length)
              {
              }else{
                clearInterval('.$name.'filtertimeout);
                //$(\'#'.$name.' > thead > tr\').first().next().hide(300);
              }
            },1000);
          });
          $(\'#'.$name.' > thead > tr\').first().next().on(\'mouseleave\', function(){
            if(!$(this).next().hasClass(\'allowhide\'))return;
            if('.$name.'filtertimeout != null)clearInterval('.$name.'filtertimeout);
            '.$name.'filtertimeout = setInterval(function(){            
              var infocus = $(\'#'.$name.' > thead > tr\').first().next().find(\':focus\');
              if(infocus.length)
              {
              }else{
                clearInterval('.$name.'filtertimeout);
                //$(\'#'.$name.' > thead > tr\').first().next().hide(300);
              }
            },1000);
          });*/     
          ');
          if(!empty($heading)){
            foreach ($heading as $kh => $vh) {
              if($vh == '' || $vh == 'Men&uuml;'){
                $this->app->Tpl->Add('YUICSS', '
                  #' . $name . ' > thead > tr > th:nth-child(' . ($kh + 1) . ') > span > input {
                    display: none;
                  }
              ');
              }else{
                $this->app->Tpl->Add('YUICSS', '
                #' . $name . ' > thead > tr > th:nth-child(' . ($kh + 1) . ') > span > input {
                  width:100%;
                }
            ');
              }
            }
          }
        }
        
        if (!empty($moreinfo)) {

          #auftraege > tbody:nth-child(2) > tr:nth-child(1) > td:nth-child(1) > img:nth-child(1)
          $this->app->Tpl->Add('DATATABLES', '
                          $(document).on( \'click\',\'#' . $name . ' tbody td img.details\', function () {
                            var nTr = this.parentNode.parentNode;
                            aData =  oTable' . $name . '.fnGetData( nTr );

                            if ( this.src.match(\'details_close\') )
                            {
                            /* This row is already open - close it */
                            this.src = "./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/details_open.png";
                            oTable' . $name . '.fnClose( nTr );
                            }
                            else
                            {
                            /* Open this row */
                            this.src = "./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/details_close.png";
                            oTable' . $name . '.fnOpen( nTr, ' . $name . 'fnFormatDetails(nTr), \'details\' );
                            $(\'#'.$name.'\').trigger(\'afteropening\');
                            }
                            });
                          ');

          /*  $.get("index.php?module=auftrag&action=minidetail&id=2", function(text){
                          spin=0; 
                          miniauftrag = text;
                          });
          */
          $module = $this->app->Secure->GetGET("module");
          if(!empty($doppelteids)){
            $doppelteids = '\-[1-9]{1}[0-9]*';
          }else{
            $doppelteids = '';
          }


  

          $this->app->Tpl->Add('JAVASCRIPT', 'function ' . $name . 'fnFormatDetails ( nTr ) {
                          //var aData =  oTable' . $name . '.fnGetData( nTr );
                          var str = aData[' . $menucol . '];
                          if (str.match(\'string\')) {
                            str = str.replace("string", "");
                            var auftrag = str;
                          } else {
                            var match = str.match(/[1-9]{1}[0-9]*'.$doppelteids.'/);
                            '.($doppelteids==''?'var auftrag = parseInt(match[0], 10);':'var auftrag = match[0];').'
                          }

                          
                          //console.log(str);
                          //var match = str.match(/[1-9]{1}[0-9]*/);
                          //var auftrag = parseInt(match[0], 10);
                          

                          var miniauftrag;
                          var strUrl = "index.php?module=' . ((isset($moreinfomodule) && $moreinfomodule)?$moreinfomodule:$module) . '&action=minidetail'.(isset($moreinfoaction)&& $moreinfoaction?$moreinfoaction:'').'&id="+auftrag; //whatever URL you need to call
                          var strReturn = "";

                          jQuery.ajax({
url:strUrl, success:function(html){strReturn = html;}, async:false
});

                          miniauftrag = strReturn;

                          var sOut = \'<table cellpadding="0" cellspacing="0" border="0" align="center" style="padding-left: 15px; padding-right:15px; width:calc(100% - 30px);">\';
                          sOut += \'<tr><td>\'+miniauftrag+\'</td></tr>\';
                          sOut += \'</table>\';
                          
                          return sOut;
                      }
');
        }
        $colspan = !empty($heading)?count($heading):1;
    //<tr><th colspan="' . $colspan . '"><br></th></tr>
        $this->app->Tpl->Add($parsetarget, '
        <div class="table-responsive">
    <table cellpadding="0" cellspacing="0" border="0" class="display'.(!empty($defferloading)?' defferloading':'').'" id="' . $name . '">
    <thead>
    <tr>');
        if(!empty($heading)){
          $cHeading = count($heading);
          for ($i = 0; $i < $cHeading; $i++) {
            $this->app->Tpl->Add(
              $parsetarget,
              '<th width="' . (isset($width[$i]) ? $width[$i] : '') . '">'
              . $this->app->Tpl->pruefeuebersetzung($heading[$i], 'table', $name)
              . '</th>'
            );
          }
        }

        if(empty($columnfilter) && !(isset($columnfilter) && $columnfilter === false)) {
          $this->app->Tpl->Add($parsetarget, '</tr><tr>');
          if(!empty($heading)){
            $cHeading = count($heading);
            for ($i = 0; $i < $cHeading; $i++) {
              $this->app->Tpl->Add(
                $parsetarget,
                '<th>' . $this->app->Tpl->pruefeuebersetzung($heading[$i], 'table', $name) . '</th>'
              );
            }
          }
        }
        
        
        $this->app->Tpl->Add($parsetarget, '</tr>
    </thead>
    <tbody>
    <tr>
    <td colspan="' . $colspan . '" class="dataTables_empty">Lade Daten</td>
    </tr>
    </tbody>

    <tfoot>
    <tr>
    ');
        if(!empty($heading)){
          $cheader = count($heading);
          for ($i = 0; $i < $cheader; $i++) {
            $this->app->Tpl->Add(
              $parsetarget,
              '<th>' . $this->app->Tpl->pruefeuebersetzung($heading[$i], 'table', $name) . '</th>'
            );
          }
        }
        $this->app->Tpl->Add($parsetarget, '
    </tr>
    </tfoot>
    </table></div>
    <br>
    <br>
    <br>
    ');
    
        if((empty($disableautosavefilter)) && $this->anzusersaves < 2) {
          if($name !== 'textvorlagenohnefilter') {
            $this->anzusersaves++;
          }
          $this->anzusersaves++;
          $this->anzusersaves = 2;
          //TODO
          //zusammenfassen in ein ajaxrequest
            $this->app->Tpl->Add(
              'JAVASCRIPT','
            var anzfilter_'.$name.' = 0;
            
            function getTableFromUserparameter(element)
            {
              if($(element).data(\'datatable\')) {
                return $(element).data(\'datatable\');
              }
              var parents = $(element).parents();
              if(parents)
              {
                var i = 0;
                for(i = 0; i < parents.length; i++)
                {
                  var tables = $(parents[i]).find(\'table.dataTable, table.display\');
                  if(tables.length)
                  {
                    return tables[ 0 ].id;
                  }
                }
              }
              return \''.$name.'\';
            }
            
           
            $(document).ready(function() {
              var elemarr = \'\';
              var namearr = \'\';

              $(\'fieldset.usersave input, div.filter-usersave input\').each(function(){
                if(typeof this.id != \'undefined\' && this.id != \'\')
                {
                  var tablename = getTableFromUserparameter(this);
                  var $usersaveElement = $(this).parents(\'fieldset.usersave, div.filter-usersave\').first();
                  var name = "table_"+tablename+"_"+this.id;
                  if($usersaveElement.length && typeof $($usersaveElement).data(\'prefix\') != \'undefined\') {
                     name =  $($usersaveElement).data(\'prefix\')+"_"+this.id;
                  } 
                  
                  var elem = this.id;
                  
                  if(elemarr !== \'\') {
                    elemarr += \',\';
                  }
                  if(namearr !== \'\') {
                    namearr += \',\';
                  }
                  elemarr += elem;
                  namearr += name;
                }
              });
             
              $.ajax({
                type: "POST",
                dataType: \'json\',
                url: "index.php?module=ajax&action=getuserparameter",
                data:  { names: namearr, elems:elemarr }
              }) .done(function( data ) {
                if(data)
                {
                $(data).each(function(kk, vv){
                  if(typeof vv.elem != \'undefined\' && vv.elem !== \'\')
                  {
                    if($(\'#\'+vv.elem).is("[type=\'checkbox\']"))
                    {
                      var change_cb = false;
                      if($(\'#\'+vv.elem).prop(\'checked\') !== (vv.value?(vv.value === null || vv.value === \'null\'?false:true):false))
                      {
                        change_cb = true;
                      }
                      $(\'#\'+vv.elem).prop(\'checked\', vv.value?(vv.value === null || vv.value === \'null\'?false:true):false);
                      if(change_cb)
                      {
                        $(\'#\'+vv.elem).trigger(\'click\');
                        $(\'#\'+vv.elem).prop(\'checked\', vv.value?(vv.value === null || vv.value === \'null\'?false:true):false);
                      }
                    }else{
                      $(\'#\'+vv.elem).val(vv.value);
                    }
                    
                    $(\'#\'+vv.elem).change(function() {
                      var wert = $(this).val();
                      if(typeof wert == \'undefined\')wert = \'\';
                      var wert = $.base64Encode(wert);
                      if($(this).is("[type=\'checkbox\']") && $(this).prop(\'checked\'))
                      {
                        wert = $.base64Encode(\'1\');
                      }
                      if($(this).is("[type=\'checkbox\']") && !$(this).prop(\'checked\'))wert = $.base64Encode(\'\');
                      
                      var prefix = "table_"+getTableFromUserparameter(this);
                      var $usersaveElement = $(this).parents(\'fieldset.usersave, div.filter-usersave\').first();
                 
                      if($usersaveElement.length && typeof $($usersaveElement).data(\'prefix\') != \'undefined\') {
                          prefix =  $($usersaveElement).data(\'prefix\');
                      } 
                      $.ajax({
                        type: "POST",
                        url: "index.php?module=ajax&action=autosaveuserparameter",
                        data:  { 
                          name: prefix+"_"+this.id, value: wert 
                        }
                      }) .done(function( vv ) {
                        
                      });
                    });
                    $(\'#\'+vv.elem).focusout(function() {
                      var wert = $(this).val();
                      if(typeof wert == \'undefined\')wert = \'\';
                      var wert = $.base64Encode(wert);
                      if($(this).is("[type=\'checkbox\']") && $(this).prop(\'checked\'))wert = $.base64Encode(\'1\');
                      if($(this).is("[type=\'checkbox\']") && !$(this).prop(\'checked\'))wert = $.base64Encode(\'\');
                      var $usersaveElement = $(this).parents(\'fieldset.usersave, div.filter-usersave\').first();
                      var prefix = "table_"+getTableFromUserparameter(this);
                      if($usersaveElement.length && typeof $($usersaveElement).data(\'prefix\') != \'undefined\') {
                          prefix =  $($usersaveElement).data(\'prefix\');
                      } 
                      $.ajax({
                        type: "POST",
                        url: "index.php?module=ajax&action=autosaveuserparameter",
                        data:  { 
                          name: prefix+"_"+this.id, value: wert 
                        }
                      }) .done(function( vv ) {
                        
                      });
                    });
                  }
                });
                }
              });              
            });
            ');
        }
      }
    }

    function AutoCompleteAuftrag($fieldname, $filter, $onlyfirst = 0, $extendurl = "") {

      $module = $this->app->Secure->GetGET("module");
      $id = $this->app->Secure->GetGET("id");
      
      if ($onlyfirst) {
        $tpl = '
      $( "#' . $fieldname . '" ).autocomplete({
source: "index.php?module=ajax&action=filter&filtername=' . $filter . $extendurl . '&smodule=' . $module . '&sid=' . $id . '",
select: function( event, ui ) {
var i = ui.item.value;
var zahl = i.indexOf(" ");
var text = i.slice(0, zahl);
$( "#' . $fieldname . '" ).val( ui.item.value );
$( "#' . $fieldname . '" ).trigger(\'blur\');
return false;
},
create: function () {
$(this).data(\'ui-autocomplete\')._renderItem = function (ul, item) {
var suchstring = /(Aktuell kein Lagerbestand)/g;
var suchergebnis = suchstring.test( item.label );
if (suchergebnis != false)
{
return $(\'<li>\')
.append(\'<a style="color:red">\' + item.label + \'</a>\')
.appendTo(ul);
}
else
{
  return $(\'<li>\')
    .append(\'<a>\' + item.label + \'</a>\')
    .appendTo(ul);
}
};
}
});';
      } else {

        //TODO
        $tpl = '
    $( "#' . $fieldname . '" ).autocomplete({
source: "index.php?module=ajax&action=filter&filtername=' . $filter . $extendurl . '"
});';
      }
      $this->app->Tpl->Add('AUTOCOMPLETE', $tpl);
      $this->app->Tpl->Set(strtoupper($fieldname) . 'START', '<div style="font-size: 8pt;"><div class="ui-widget" style="font-size: 8pt;">');
      $this->app->Tpl->Set(strtoupper($fieldname) . 'ENDE', '</div></div>');
    }
  
    function CodiereSQLForOneQuery($sql, $name)
    {
      $md5 = md5($this->app->User->GetID().$name);
      $pos = stripos($sql,'SELECT SQL_CALC_FOUND_ROWS ');
      if($pos === false)
      {
        return $sql;
      }
      $replace = substr($sql, $pos, 27);
      $new = '';
      for($i = 0; $i < 27; $i++)
      {
        $j = $i + $pos;
        if (ord($sql[$j]) % 2 == 0)
        {
          $new .= strtolower($sql[$j]);
        }else{
          $new .= strtoupper($sql[$j]);
        }
      }
      return str_replace($replace, $new, $sql);
    }
    
    function AutoCompleteBestellung($fieldname, $filter, $onlyfirst = 0, $extendurl = "") {

      $module = $this->app->Secure->GetGET("module");
      $id = $this->app->Secure->GetGET("id");
      
      if ($onlyfirst) {
        $tpl = '
      $( "#' . $fieldname . '" ).autocomplete({
source: "index.php?module=ajax&action=filter&filtername=' . $filter . $extendurl . '&smodule=' . $module . '&sid=' . $id . '",
select: function( event, ui ) {
var i = ui.item.value;
var zahl = i.indexOf(" ");
var text = i.slice(0, zahl);
$( "#' . $fieldname . '" ).val( ui.item.value );
$( "#' . $fieldname . '" ).trigger(\'blur\');
return false;
}
});';
      } else {
        $tpl = '

    $( "#' . $fieldname . '" ).autocomplete({
source: "index.php?module=ajax&action=filter&filtername=' . $filter . '"
});';
      }
      $this->app->Tpl->Add('AUTOCOMPLETE', $tpl);
      $this->app->Tpl->Set(strtoupper($fieldname) . 'START', '<div style="font-size: 8pt;"><div class="ui-widget" style="font-size: 8pt;">');
      $this->app->Tpl->Set(strtoupper($fieldname) . 'ENDE', '</div></div>');
    }
    
    function AutoCompleteAddCut($fieldname, $filter, $onlyfirst = 0, $extendurl = "") {

      
      if ($onlyfirst) {
        $tpl = '
      $( "#' . $fieldname . '" ).autocomplete({
source: "index.php?module=ajax&action=filter&filtername=' . $filter . $extendurl . '",
select: function( event, ui ) {
var j = ui.item.value;
var i = $( "#' . $fieldname . '" ).val()+ui.item.value;
var zahl = i.indexOf(",");
var zahl2 = j.indexOf(" ");
var text = i.slice(0, zahl);
var text2 = j.slice(0, zahl2);
if(zahl <=0)
$( "#' . $fieldname . '" ).val( text2 );
else {
var j = $( "#' . $fieldname . '" ).val();
var zahlletzte = j.lastIndexOf(",");
var text3 = j.substring(0,zahlletzte); 

$( "#' . $fieldname . '" ).val( text3 +","+ text2 );
}
return false;
}
});';
      } else {
        $tpl = '
    $( "#' . $fieldname . '" ).autocomplete({
source: "index.php?module=ajax&action=filter&filtername=' . $filter . $extendurl . '",
select: function( event, ui ) {
var i = $( "#' . $fieldname . '" ).val()+ui.item.value;
var zahl = i.indexOf(",");

var text = i.slice(0, zahl);
if(zahl <=0)
$( "#' . $fieldname . '" ).val( ui.item.value );
else {
var j = $( "#' . $fieldname . '" ).val();
var zahlletzte = j.lastIndexOf(",");
var text2 = j.substring(0,zahlletzte); 

$( "#' . $fieldname . '" ).val( text2 + "," + ui.item.value );
}
return false;
}
});';
      }
      $this->app->Tpl->Add('AUTOCOMPLETE', $tpl);
      $this->app->Tpl->Set(strtoupper($fieldname) . 'START', '<div style="font-size: 8pt;"><div class="ui-widget" style="font-size: 8pt;">');
      $this->app->Tpl->Set(strtoupper($fieldname) . 'ENDE', '</div></div>');
    }
    
    function AutoCompleteAdd($fieldname, $filter, $onlyfirst = 0, $extendurl = "") {

      
      if ($onlyfirst) {
        $tpl = '
      $( "#' . $fieldname . '" ).autocomplete({
source: "index.php?module=ajax&action=filter&filtername=' . $filter . $extendurl . '",
select: function( event, ui ) {
var j = ui.item.value;
j = j.replace(",","");
var i = $( "#' . $fieldname . '" ).val()+j;
var zahl = i.indexOf(",");
var zahl2 = j.indexOf(" ");
var text = i.slice(0, zahl);
var text2 = j.slice(0, zahl2);
if(zahl <=0)
$( "#' . $fieldname . '" ).val( text2 );
else {
var j = $( "#' . $fieldname . '" ).val();
var zahlletzte = j.lastIndexOf(",");
var text3 = j.substring(0,zahlletzte); 

$( "#' . $fieldname . '" ).val( text3 +","+ text2 );
}
return false;
}
});';
      } else {
        $tpl = '
    $( "#' . $fieldname . '" ).autocomplete({
source: "index.php?module=ajax&action=filter&filtername=' . $filter . $extendurl . '",
select: function( event, ui ) {
var i = $( "#' . $fieldname . '" ).val()+ui.item.value;
var zahl = i.indexOf(",");

var text = i.slice(0, zahl);
if(zahl <=0)
$( "#' . $fieldname . '" ).val( ui.item.value );
else {
var j = $( "#' . $fieldname . '" ).val();
var zahlletzte = j.lastIndexOf(",");
var text2 = j.substring(0,zahlletzte); 

$( "#' . $fieldname . '" ).val( text2 + "," + ui.item.value );
}
return false;
}
});';
      }
      $this->app->Tpl->Add('AUTOCOMPLETE', $tpl);
      $this->app->Tpl->Set(strtoupper($fieldname) . 'START', '<div style="font-size: 8pt;"><div class="ui-widget" style="font-size: 8pt;">');
      $this->app->Tpl->Set(strtoupper($fieldname) . 'ENDE', '</div></div>');
    }
    
    function AutoCompleteAddEvent($fieldname, $filter, $onlyfirst = 0, $extendurl = "") {
      if ($onlyfirst) {
        $tpl = '
      $( "#' . $fieldname . '" ).autocomplete({
source: "index.php?module=ajax&action=filter&filtername=' . $filter . $extendurl . '"
});';
      } else {
        $tpl = '
    $( "#' . $fieldname . '" ).autocomplete({
source: "index.php?module=ajax&action=filter&filtername=' . $filter . $extendurl . '"
});';
      }
      $this->app->Tpl->Add('AUTOCOMPLETE', $tpl);
      $this->app->Tpl->Set(strtoupper($fieldname) . 'START', '<div style="font-size: 8pt;"><div class="ui-widget" style="font-size: 8pt;">');
      $this->app->Tpl->Set(strtoupper($fieldname) . 'ENDE', '</div></div>');
    }
    
    function AutoCompleteJSON($fieldname, $filter, $onlyfirst = 0, $extendurl = "", $extendedelement = "", $extendurl2 ="", $extendedelement2 = "", $appendto = "", $return = false)
    {
      if($extendedelement)
      {
        $tpl = '{
            source: function( request, response ) {
                $.ajax( {
                  url: "index.php?module=ajax&action=filter&rmodule='.$this->app->Secure->GetGET('module').'&raction='.$this->app->Secure->GetGET('action').'&rid='.$this->app->Secure->GetGET('id').'&filtername=' . $filter.$extendurl.'"+encodeURI($("#'.$extendedelement.'").val())'.($extendedelement2?'+"'.$extendurl2.'"+encodeURI($("#'.$extendedelement2.'").val())':'').'
                  ,
                  dataType: "json",
                  data: {
                    term: request.term
                  },
                  success: function( data ) {
                    if(data == null)
                    {
                      response ([]);
                    }else
                     response( data.length === 1 && data[ 0 ].length === 0 ? [] : data );
                  }
                } );
              }'.($onlyfirst?'
              ,
              "select": function( event, ui ) {
              var i = ui.item.value;
              var zahl = i.indexOf(" ");
              var text = i.slice(0, zahl);
              $( "input#' . $fieldname . '" ).val( text );
              return false;
              }
              ':'').'
              
              '.($appendto == ''?'':'
              ,"appendTo": "#'.$appendto.'"
              ').'
              
              }';

      }else{
        if ($onlyfirst) {
          $tpl = '
            {
              "source": "index.php?module=ajax&action=filter&rmodule='.$this->app->Secure->GetGET('module').'&raction='.$this->app->Secure->GetGET('action').'&rid='.$this->app->Secure->GetGET('id').'&filtername=' . $filter . $extendurl . '",
              "select": function( event, ui ) {
              var i = ui.item.value;
              var zahl = i.indexOf(" ");
              var text = i.slice(0, zahl);
              $( "input#' . $fieldname . '" ).val( text );
              return false;
              }
                            '.($appendto == ''?'':'
              ,"appendTo": "#'.$appendto.'"
              ').'
              
            }';
        } else {
          $tpl = '
          {
            "source": "index.php?module=ajax&action=filter&filtername=' . $filter . $extendurl . '"
                          '.($appendto == ''?'':'
              ,appendTo: "#'.$appendto.'"
              ').'
              
          }';
        }
      }
      return '<script type="application/json" class="json_autocomplete">
          {"element":"'.$fieldname.'","data":'.$tpl.'}
        </script>';

    }
    
    
    function AutoComplete($fieldname, $filter, $onlyfirst = 0, $extendurl = "", $extendedelement = "", $extendurl2 ="", $extendedelement2 = "", $appendto = "", $return = false) {

      if($extendedelement)
      {
        $tpl = '$( "input#' . $fieldname . '" ).autocomplete({
            source: function( request, response ) {
                $.ajax( {
                  url: "index.php?module=ajax&action=filter&rmodule='.$this->app->Secure->GetGET('module').'&raction='.$this->app->Secure->GetGET('action').'&rid='.$this->app->Secure->GetGET('id').'&filtername=' . $filter.$extendurl.'"+encodeURI($("#'.$extendedelement.'").val())'.($extendedelement2?'+"'.$extendurl2.'"+encodeURI($("#'.$extendedelement2.'").val())':'').'
                  ,
                  dataType: "json",
                  data: {
                    term: request.term
                  },
                  success: function( data ) {
                    if(data == null)
                    {
                      response ([]);
                    }else
                     response( data.length === 1 && data[ 0 ].length === 0 ? [] : data );
                  }
                } );
              }'.($onlyfirst?'
              ,
              select: function( event, ui ) {
              var i = ui.item.value;
              var zahl = i.indexOf(" ");
              var text = i.slice(0, zahl);
              $( "input#' . $fieldname . '" ).val( text );
              return false;
              }
              ':'').'
              
              '.($appendto == ''?'':'
              ,appendTo: "#'.$appendto.'"
              ').'
              
              });';
        
      }else{
        if ($onlyfirst) {
          $tpl = '
            $( "input#' . $fieldname . '" ).autocomplete({
              source: "index.php?module=ajax&action=filter&rmodule='.$this->app->Secure->GetGET('module').'&raction='.$this->app->Secure->GetGET('action').'&rid='.$this->app->Secure->GetGET('id').'&filtername=' . $filter . $extendurl . '",
              select: function( event, ui ) {
              var i = ui.item.value;
              var zahl = i.indexOf(" ");
              var text = i.slice(0, zahl);
              $( "input#' . $fieldname . '" ).val( text );
              return false;
              }
                            '.($appendto == ''?'':'
              ,appendTo: "#'.$appendto.'"
              ').'
              
            });';
        } else {
          $tpl = '
          $( "input#' . $fieldname . '" ).autocomplete({
            source: "index.php?module=ajax&action=filter&filtername=' . $filter . $extendurl . '"
                          '.($appendto == ''?'':'
              ,appendTo: "#'.$appendto.'"
              ').'
              
          });';
        }
      }
      if($return)return $tpl;
      $this->app->Tpl->Add('AUTOCOMPLETE', $tpl);
      $this->app->Tpl->Set(strtoupper($fieldname) . 'START', '<div style="font-size: 8pt;"><div class="ui-widget" style="font-size: 8pt;">');
      $this->app->Tpl->Set(strtoupper($fieldname) . 'ENDE', '</div></div>');
    }

  /**
   * @param string $target
   * @param string $element
   * @param array  $fields
   * @param string $prefix
   * @param string $legend
   * @param null   $options
   */
    public function DrawSimpleFormDiv($target, $element, $fields, $prefix, $legend = 'Einstellungen', $options = null)
    {
      if(!empty($options['form'])) {
        $submit = false;
        if($fields) {
          foreach($fields as $k => $v) {
            if(!empty($v['type']) && $v['type'] === 'submit') {
              $submit = $this->app->Secure->GetPOST($prefix.'_'.$k);
              if($submit){
                break;
              }
            }
          }
        }
        if(!$submit && !(is_numeric($options['form']) || is_bool($options['form']))) {
          $submit = $this->app->Secure->GetPOST($options['form']);
        }
        if($submit) {
          foreach($fields as $k => $v) {
            $type = 'text';
            if(!empty($v['type'])) {
              $type = $v['type'];
            }
            if($type === 'legend' || $type === 'submit'){
              continue;
            }
            $value = $this->app->Secure->GetPOST($prefix.'_'.$k);
            if($type === 'checkbox'){
              $value = (int)$value;
            }
            if(!empty($v['replace'])) {
              switch (strtolower($v['replace'])) {
                case 'artikel':
                  $value = $this->app->erp->ReplaceArtikel(1, $value, 1);
                  break;
                case 'adresse':
                  $value = $this->app->erp->ReplaceAdresse(1, $value, 1);
                  break;
                case 'lagerplatz':
                  $value = $this->app->erp->ReplaceLagerPlatz(1, $value, 1);
                  break;
                case 'lager':
                  $value = $this->app->erp->ReplaceLager(1, $value, 1);
                  break;
              }
            }
            $this->app->erp->SetKonfigurationValue($prefix.'_'.$k, $value);
          }
        }

        $this->app->Tpl->Add($target,'<form method="post"'.
          ((is_numeric($options['form']) || is_bool($options['form']))?'':' name="'.$options['form'].'" id="'.$options['form'].'"').'>');
      }
      if(empty($options['nodiv'])) {
        $this->app->Tpl->Add($target,'<div id="'.$element.'">');
      }
      if($options['white_background']){
        $this->app->Tpl->Add($target,'<input type="hidden" id="'.$prefix.'_id" value="" />
                                      <div class="row">
                                      <div class="row-height">
                                      <div class="col-md-12 col-md-height">
                                      <div class="inside inside-full-height">
                                      <fieldset><legend>'.$legend.'</legend>');
      }else{
        $this->app->Tpl->Add($target,'<input type="hidden" id="'.$prefix.'_id" value="" /><fieldset><legend>'.$legend.'</legend>');
      }

      if($fields) {
        foreach($fields as $k => $v) {
          if(empty($v['type']) || strtolower($v['type']) !== 'hidden'){
            continue;
          }
          $this->app->Tpl->Add(
            $target,
            '<input type="hidden" name="'.$prefix.'_'.$k.'" id="'.$prefix.'_'.$k.'" value="'.$this->app->erp->GetKonfiguration($prefix.'_'.$k).'" />'
          );
        }
      }
      $this->app->Tpl->Add($target,'<table width="100%">');
      if($fields) {
        $defaultsize = '';
        if(isset($options['size'])) {
          $defaultsize = $options['size'];
        }
        foreach($fields as $k => $v) {
          $type = 'text';
          if(isset($v['type'])) {
            $type = strtolower($v['type']);
          }
          $size = '';
          if(isset($v['size'])) {
            $size = $v['size'];
          }
          elseif($type === 'text') {
            $size = $defaultsize;
          }
          if($type === 'hidden') {
            continue;
          }
          $name = $k;
          $bezeichnung = ucfirst($name);
          if(isset($v['bezeichnung'])) {
            $bezeichnung = $v['bezeichnung'];
          }
          if($type === 'legend') {
            if($options['white_background']){
              $this->app->Tpl->Add($target,'</table></fieldset></div></div></div></div>
                                            <div class="row">
                                            <div class="row-height">
                                            <div class="col-xs-12 col-md-height">
                                            <div class="inside inside-full-height">
                                            <fieldset><legend>'.$bezeichnung.'</legend><table>');
            }else{
              $this->app->Tpl->Add($target,'</table></fieldset><fieldset><legend>'.$bezeichnung.'</legend><table>');
            }
          }
          else{
            if(substr(trim($bezeichnung), -1) != ':' && $type !== 'submit' && empty($v['nodoublepoint'])) {
              $bezeichnung .= ':';
            }
            if(trim($bezeichnung) === '&nbps;:') {
              $bezeichnung = '';
            }
            $value = $this->app->erp->GetKonfiguration($prefix.'_'.$k);
            if($type === 'submit') {
              $this->app->Tpl->Add($target, '<tr><td></td><td>');
            }
            elseif($type === 'radiobutton'){
              $this->app->Tpl->Add($target, '<tr><td colspan="2" class="radiobutton">');
            }
            else{
              $this->app->Tpl->Add($target, '<tr><td><label for="' . $prefix . '_' . $name . '">' . $bezeichnung . '</label></td><td>');
            }
            if($type === 'select') {
              $this->app->Tpl->Add($target,'<select '.($size?' size="'.$size.'" ':'').' name="'.$prefix.'_'.$name.'" id="'.$prefix.'_'.$name.'">');
              if(empty($v['optionen'][''])){
                $this->app->Tpl->Add($target, '<option value=""></option>');
              }
              if(isset($v['optionen']))
              {
                foreach($v['optionen'] as $k2 => $v2)
                {
                  $this->app->Tpl->Add($target,'<option value="'.$k2.'"'.($value == $k2?' selected="selected"':'').'>'.$v2.'</option>');
                }
              }
              $this->app->Tpl->Add($target,'</select>');
              
            }
            elseif($type === 'textarea' || $type === 'ckeditor'){
              $this->app->Tpl->Add($target,'<textarea '.($size?' cols="'.$size.'" ':'').' name="'.$prefix.'_'.$name.'" id="'.$prefix.'_'.$name.'">'.$value.'</textarea>');
            }
            elseif($type === 'submit'){
              $this->app->Tpl->Add($target, '<input type="' . $type . '" ' . ($size ? ' size="' . $size . '" ' : '') . ' name="' . $prefix . '_' . $name . '" id="' . $prefix . '_' . $name . '"  value="' . $bezeichnung . '" />');
            }
            elseif($type==='link'){
              $this->app->Tpl->Add($target, !empty($v['link']) ? $v['link'] : '');
            }
            elseif($type === 'radiobutton'){
              if(isset($v['optionen'])) {
                $first = true;
                foreach($v['optionen'] as $k2 => $v2) {
                  if(!$first) {
                    $this->app->Tpl->Add($target,'</td></tr><tr><td colspan="2" class="radiobutton">');
                  }
                  $this->app->Tpl->Add($target,'<input type="radio" class="hidden" name="'.$prefix.'_'.$name.'" id="'.$prefix.'_'.$name.'_'.$k2.'"  '.($value==$v2?' checked="checked" ':'').' value="'.$v2.'" /><label for="'.$prefix.'_'.$name.'_'.$k2.'" class="radiobutton">'.$v2.'</label>');

                  $first = false;
                }
              }
            }
            else{
              if(!empty($v['replace'])) {
                switch (strtolower($v['replace'])) {
                  case 'artikel':
                    $value = $this->app->erp->ReplaceArtikel(0, $value, 0);
                    if(!isset($v['autocomplete'])) {
                      $v['autocomplete'] = 'artikelnummer';
                    }
                    break;
                  case 'adresse':
                    $value = $this->app->erp->ReplaceAdresse(0, $value, 0);
                    if(!isset($v['autocomplete'])) {
                      $v['autocomplete'] = 'adresse';
                    }
                    break;
                  case 'lagerplatz':
                    $value = $this->app->erp->ReplaceLagerPlatz(0, $value, 0);
                    if(!isset($v['autocomplete'])) {
                      $v['autocomplete'] = 'lagerplatz';
                    }
                    break;
                  case 'lager':
                    $value = $this->app->erp->ReplaceLager(0, $value, 0);
                    if(!isset($v['autocomplete'])) {
                      $v['autocomplete'] = 'lager';
                    }
                    break;
                }
              }
              $this->app->Tpl->Add($target,'<input type="'.$type.'" '.($size?' size="'.$size.'" ':'').' name="'.$prefix.'_'.$name.'" id="'.$prefix.'_'.$name.'" '.($type === 'checkbox'?' value="1" '.($value?' checked="checked" ':''):' value="'.$value.'').'" />');
            }
            if(!empty($v['info'])) {
              $this->app->Tpl->Add($target,'&nbsp;<i>'.$v['info'].'</i>');
            }
            $this->app->Tpl->Add($target,'</td></tr>');
            if(isset($v['autocomplete'])) {
              $this->AutoComplete($prefix.'_'.$k, $v['autocomplete']);
            }
            if($type === 'datum') {
              $this->DatePicker($prefix.'_'.$k);
            }
            elseif($type === 'zeit') {
              $this->TimePicker($prefix.'_'.$k);
            }
            elseif($type === 'ckeditor') {
              $this->CkEditor($prefix.'_'.$name,"belege");
            }
          }
        }
      }
      if($options['white_background']){
        $this->app->Tpl->Add($target, '</table></fieldset></div></div></div></div>');
      }else{
        $this->app->Tpl->Add($target, '</table></fieldset>');
      }
      
      if(empty($options['nodiv'])) {
        $this->app->Tpl->Add($target, '</div>');
      }
      if(!empty($options['form'])) {
        $this->app->Tpl->Add($target, '</form>');
      }
      if(!empty($options['template'])){
        if(is_numeric($options['template']) || is_bool($options['template'])) {
          $this->app->Tpl->Parse('PAGE', 'tabview.tpl');
        }
        else{
          if(substr($options['template'],-4) !== '.tpl'){
            $options['template'] .= '.tpl';
          }
          $this->app->Tpl->Parse('PAGE', $options['template']);
        }
      }
    }
  
    function AddSimpleForm($table, $fields, $options = null, $obj = null) 
    { 
      $target = 'TAB1';
      $funktionsave = 'save'.$table;
      $funktiondelete = 'delete'.$table;
      $funktiondget = 'get'.$table;
      $element = $table.'popup';
      $minwidth = 1200;
      $afterclose = '';
      $livetabelle = '';
      $save = 'SPEICHERN';
      $onsave = '';
      $weiteretabelle = '';
      $weiteretabellekey = '';
      $prefix = $table;
      $functionafterdelete = '';
      $functionbeforedelete = '';
      $functionafterget = '';
      $functionaftersave = '';
      $legend = 'Einstellungen';
      $title = '';
      $nocreate = false;
      $nocreatebutton = false;
      $editbutton = '';
      $edittext = '&auml;ndern';
      $addbuttonafter = '';
      $beforebutton = '';
      $afterbutton = '';
      $btnclass = 'btnGreen';
      $module = $this->app->Secure->GetGET('module');
      $action = $this->app->Secure->GetGET('action');
      $id = $this->app->Secure->GetGET('id');
      $optionfields = array('target','legend','element','minwidth','afterclose','livetabelle','save',
        'onsave','funktionget','funktiondelete','funktionsave','weiteretabelle','weiteretabellekey',
        'prefix','id','module','action','functionbeforedelete','functionafterdelete','functionafterget','functionaftersave',
        'title','nocreate','edittext','editbutton','addbuttonafter','beforebutton','afterbutton','btnclass','nocreatebutton');
      if($options)
      {
        foreach($optionfields as $option)
        {
          if(isset($options[$option])) {
            $$option = $options[$option];
          }
        }
      }
      $btntarget = $target;
      if(!empty($options['btntarget'])) {
        $btntarget = $options['btntarget'];
      }


      if(!empty($options['width']))
      {
        $width = $options['width'];
      }
      if(!empty($options['left']))
      {
        $left = $options['left'];
      }

      if(empty($title)){
        $title = $legend;
      }
      $cmd = $this->app->Secure->GetGET('cmd');
      
      if($cmd == $funktiondget)
      {
        $data = null;
        $id = (int)$this->app->Secure->GetPOST('id');
        if($id)
        {
          $data = $this->app->DB->SelectArr("SELECT * FROM $table WHERE id = '$id' LIMIT 1");
          if($data)
          {
            $data = reset($data);
            foreach($fields as $k => $v)
            {
              $type = 'text';
              if(isset($v['type'])) {
                $type = $v['type'];
              }
              if($type === 'legend') {
                continue;
              }
              if(isset($v['autocomplete']))
              {
                switch($v['autocomplete'])
                {
                  case 'projektname':
                    $v['replace'] = 'ProjektNameDyn';
                  break;
                  case 'artikelnummer':
                    $v['replace'] = 'Artikel';
                  break;
                  case 'kunde':
                    $v['replace'] = 'Kunde';
                  break;
                  case 'lieferant':
                    $v['replace'] = 'Lieferant';
                  break;
                  case 'retoure':
                  case 'rechnung':
                    $v['replace'] = ucfirst($v['autocomplete']);
                    break;
                }
              }
              if($type === 'menge')
              {
                $v['replace'] = 'Menge';
              }
              if(isset($v['replace']) && $v['replace'] != '')
              {
                $methodname = 'Replace'.$v['replace'];
                if(method_exists($this->app->erp, $methodname))
                {
                  $data[$k] = $this->app->erp->$methodname(0, $data[$k], 0);
                }
              }
              if($type === 'datum')
              {
                if(strpos($data[$k],'-')) {
                  $data[$k] = $this->app->String->Convert($data[$k],'%1-%2-%3','%3.%2.%1');
                }
              }
              if($type === 'datetime') {
                if(strpos($data[$k],'-')) {
                  $data[$k] = $this->app->String->Convert($data[$k],'%1-%2-%3 %4','%3.%2.%1 %4');
                }
              }
            } 
          }
        }
        if(!$data)
        {
          foreach($fields as $k => $v)
          {
            if(isset($v['default']))
            {
              $data[$k] = $v['default'];
            }else{
              $data[$k] = '';
            }
          }
        }else{
          foreach($fields as $k=> $v)
          {
            if(isset($v['defaultonempty']) && empty($data[$k]))
            {
              $data[$k] = $v['defaultonempty'];
            }
          }
        }
        if($functionafterget && $obj && method_exists($obj, $functionafterget)) {
          $data = $obj->$functionafterget($id, $data);
        }
        echo json_encode($data);
        $this->app->ExitXentral();
      }
      
      if($cmd == $funktiondelete)
      {
        $id = (int)$this->app->Secure->GetPOST('id');
        $ret = array('status'=>0);
        if($id) {
          $id = $this->app->DB->Select("SELECT id FROM $table WHERE id = '$id' LIMIT 1");
        }
        
        if($id)
        {
          if($functionbeforedelete && $obj && method_exists($obj, $functionbeforedelete)) {
            $obj->$functionbeforedelete($id);
          }
          $this->app->DB->Delete("DELETE FROM $table WHERE id = '$id' LIMIT 1");
          if($weiteretabelle != '' && $weiteretabellekey != '')
          {
            $this->app->DB->Delete("DELETE FROM $weiteretabelle WHERE $weiteretabellekey = '$id' LIMIT 1");
          }
          if($functionafterdelete && $obj && method_exists($obj, $functionafterdelete)) {
            $obj->$functionafterdelete($id);
          }
          $ret = array('status'=>1);
        }
        echo json_encode($ret);
        $this->app->ExitXentral();
      }
      
      if(!empty($cmd) && $cmd == $funktionsave)
      {
        $id = (int)$this->app->Secure->GetPOST('id');
        $ret = array('status'=>0);
        if($id) {
          $id = $this->app->DB->Select("SELECT id FROM $table WHERE id = '$id' LIMIT 1");
        }
        if(!$id && !$nocreate)
        {
          foreach($fields as $k => $v)
          {
            if(!empty($v['unique']))
            {
              $data[$k] = $this->app->Secure->GetPOST($k);
              if($type === 'menge')
              {
                $v['replace'] = 'Menge';
              }
              if(isset($v['autocomplete']))
              {
                switch($v['autocomplete'])
                {
                  case 'projektname':
                    $v['replace'] = 'ProjektNameDyn';
                    break;
                  case 'artikelnummer':
                    $v['replace'] = 'Artikel';
                    break;
                  case 'kunde':
                    $v['replace'] = 'Kunde';
                    break;
                  case 'lieferant':
                    $v['replace'] = 'Lieferant';
                    break;
                  case 'lieferschein':
                  case 'rechnung':
                  case 'retoure':
                    $v['replace'] = ucfirst($v['autocomplete']);
                    break;
                }
              }
              if(isset($v['replace']) && $v['replace'] != '')
              {
                $methodname = 'Replace'.$v['replace'];
                if(method_exists($this->app->erp, $methodname))
                {
                  $data[$k] = $this->app->DB->real_escape_string($this->app->erp->$methodname(1, $data[$k], 1));
                }
              }
              if(!empty($data[$k]))
              {
                if($this->app->DB->Select("SELECT id FROM $table WHERE `$k` = '".$data[$k]."' ".(!empty($v['uniquewhere'])?' AND '.$v['uniquewhere']:'').' LIMIT 1'))
                {
                  echo json_encode(
                    ['status'=>0,
                      'error'=>'Es existiert bereits ein Eintrag mit dem Feld: '.
                          (!empty($v['bezeichnung'])?$v['bezeichnung']:$k)
                    ]
                  );
                  $this->app->ExitXentral();
                }
              }
            }
            if(!empty($v['notempty'])) {
              $data[$k] = $this->app->Secure->GetPOST($k);
              if($type === 'menge') {
                $v['replace'] = 'Menge';
              }
              if(isset($v['autocomplete'])) {
                switch($v['autocomplete']) {
                  case 'projektname':
                    $v['replace'] = 'ProjektNameDyn';
                    break;
                  case 'artikelnummer':
                    $v['replace'] = 'Artikel';
                    break;
                  case 'kunde':
                    $v['replace'] = 'Kunde';
                    break;
                  case 'lieferant':
                    $v['replace'] = 'Lieferant';
                    break;
                  case 'lieferschein':
                  case 'rechnung':
                  case 'retoure':
                    $v['replace'] = ucfirst($v['autocomplete']);
                    break;
                }
              }
              if(isset($v['replace']) && $v['replace'] != '') {
                $methodname = 'Replace'.$v['replace'];
                if(method_exists($this->app->erp, $methodname)) {
                  $data[$k] = $this->app->DB->real_escape_string($this->app->erp->$methodname(1, $data[$k], 1));
                }
              }
              if(empty($data[$k])) {
                echo json_encode(
                  [
                    'status'=>0,
                    'error'=>'Das Feld '.(!empty($v['bezeichnung'])?$v['bezeichnung']:$k).' darf nicht leer sein: '
                  ]
                );
                $this->app->ExitXentral();
              }
            }
          }

          $this->app->DB->Insert("INSERT INTO $table (id) VALUES (NULL)");
          $id = $this->app->DB->GetInsertID();
        }
        if($id)
        {
          $ret = array('status'=>1);
          $data = null;
          $bearbeiterex = false;
          foreach($fields as $k => $v)
          {
            if($k === 'bearbeiter') {
              $bearbeiterex = true;
            }
            $type = 'text';
            if(isset($v['type'])) {
              $type = $v['type'];
            }
            if($type === 'legend' || $type === 'link')
            {
              continue;
            }
            $data[$k] = $this->app->Secure->GetPOST($k);
            if($type === 'menge')
            {
              $v['replace'] = 'Menge';
            }
            if(isset($v['autocomplete']))
            {
              switch($v['autocomplete'])
              {
                case 'projektname':
                  $v['replace'] = 'ProjektNameDyn';
                break;
                case 'artikelnummer':
                  $v['replace'] = 'Artikel';
                break;
                case 'kunde':
                  $v['replace'] = 'Kunde';
                break;
                case 'lieferant':
                  $v['replace'] = 'Lieferant';
                break;
                case 'lieferschein':
                case 'rechnung':
                case 'retoure':
                  $v['replace'] = ucfirst($v['autocomplete']);
                  break;
              }
            }
            if(isset($v['replace']) && $v['replace'] != '')
            {
              $methodname = 'Replace'.$v['replace'];
              if(method_exists($this->app->erp, $methodname))
              {
                $data[$k] = $this->app->DB->real_escape_string($this->app->erp->$methodname(1, $data[$k], 1));
              }
            }
            if($type === 'datum') {
              if(strpos($data[$k],'.')) {
                $data[$k] = $this->app->String->Convert($data[$k],'%3.%2.%1','%1-%2-%3');
              }
            }
            elseif($type === 'datetime') {
              if(strpos($data[$k],'.')) {
                $data[$k] = $this->app->String->Convert($data[$k],'%3.%2.%1 %4','%1-%2-%3 %4');
              }
            }
          }
          $this->app->DB->UpdateArr($table,$id,'id',$data);
          if(!$bearbeiterex && empty($options['nobearbeiter']))
          {
            $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
            $this->app->DB->Update("UPDATE $table SET bearbeiter = '$bearbeiter' WHERE id = '$id' LIMIT 1");
          }
          if($functionaftersave && $obj && method_exists($obj, $functionaftersave)) {
            $obj->$functionaftersave($id);
          }
        }
        echo json_encode($ret);
        $this->app->ExitXentral();
      }
      
      $updatetable = '';
      
      if($livetabelle != '')
      {
        $updatetable .= 'var oTable = $(\'#'.ltrim($livetabelle,'#').'\').DataTable();
        oTable.ajax.reload();
        ';
      }
      if(!is_numeric($minwidth)){
        $minwidth = "'".$minwidth."'";
      }

      if(!empty($beforebutton))
      {
        $this->app->Tpl->Add($target, $beforebutton);
      }

      if(!$nocreate && !$nocreatebutton){
        $btn = '<input type="button" id="btnnew_'.$table.'" class="'.$btnclass.'" value="{|&#10010; Neuer Eintrag|}" onclick="' . $funktiondget . '(0);" />';
        if(!empty($addbuttonafter))
        {
          $this->app->Tpl->Add('JQUERYREADY','
 
          '.$addbuttonafter.'.after(\''.$btn.'\');
          ');
        }else{
          $this->app->Tpl->Add($btntarget, $btn);
        }
      }
      if(!empty($editbutton)){
        $btn = '<input type="button" id="btnedit_'.$table.'" class="'.$btnclass.'" value="{|' . $edittext . '|}" onclick="' . $funktiondget . '(' . $editbutton . ');" />';
        if(!empty($addbuttonafter))
        {
          $this->app->Tpl->Add('JQUERYREADY','

          '.$addbuttonafter.'.after(\''.$btn.'\');
          ');
        }else{
          $this->app->Tpl->Add($btntarget, $btn);
        }
      }

      if(!empty($afterbutton))
      {
        $this->app->Tpl->Add($target, $afterbutton);
      }

      $this->DrawSimpleFormDiv($target, $element, $fields, $prefix, $legend, $options);
      
      $this->app->Tpl->Add($target, '<script style="text/javascript">
        function '.$funktiondelete.'(id)
        {
          if(confirm(\'Wirklich löschen?\'))
          {
          $.ajax({
            url: \'index.php?module='.$module.'&action='.$action.''.($id?'&id='.$id:'').'&cmd='.$funktiondelete.'\',
            type: \'POST\',
            dataType: \'json\',
            data: { id:id}
            ,success: function(data) {
            '.$updatetable.'
            }});
          }
        }
      
      
        function '.$funktiondget.'(id)
        {        
          $.ajax({
            url: \'index.php?module='.$module.'&action='.$action.''.($id?'&id='.$id:'').'&cmd='.$funktiondget.'\',
            type: \'POST\',
            dataType: \'json\',
            data: { id:id}
            ,success: function(data) {
              $(\'#'.$prefix.'_id\').val(id);
              ');
            foreach($fields as $k => $v)
            {
              $type = 'text';
              if(isset($v['type']))$type = $v['type'];
              if($type === 'legend' || $type === 'link')
              {
                continue;
              }
              if($type === 'checkbox'){
                $this->app->Tpl->Add($target, ' $(\'#' . $prefix . '_' . $k . '\').prop(\'checked\', (data.' . $k . ' == 1?true:false) );  ');
              }elseif($type === 'radiobutton'){
                $this->app->Tpl->Add($target, ' $(\'input[name='.$prefix.'_'.$k.'][value="\'+data.'.$k.'+\'"]\').prop(\'checked\',true);  ');
              }else{
                $this->app->Tpl->Add($target, ' $(\'#'.$prefix.'_'.$k.'\').val(data.'.$k.' );  ');
              }
            }
      
         $this->app->Tpl->Add($target, '     $(\'#'.$element.'\').dialog(\'open\');
            }
          });
        }
      
      </script>
      ');
      
    $this->app->Tpl->Add('JQUERYREADY','    
     $(\'#'.$element.'\').dialog(
        {
          modal: true,
          autoOpen: false,
          minWidth: '.$minwidth.',
          title:\''.$title.'\',
          buttons: {
            ');
      $this->app->Tpl->Add(
        'JQUERYREADY',
        ' \'{|ABBRECHEN|}\': function() {
              $(this).dialog(\'close\');
          }'
      );
            if($save) {
              $this->app->Tpl->Add(
                'JQUERYREADY',
                '     ,'.$save.': function() { 
              var fehler = \'\';
              
              ');
              
              foreach($fields as $k => $v)
              {
                if(isset($v['notempty']) && $v['notempty'])
                {
                  $this->app->Tpl->Add('JQUERYREADY',' if($(\'#'.$prefix.'_'.$k.'\').val()+\'\' === \'\')fehler = \'Bitte '.(isset($v['bezeichnung'])?$v['bezeichnung']:ucfirst($k)).' ausfüllen\'; ');
                }
              }
              
              $this->app->Tpl->Add('JQUERYREADY',' 
              
              if(fehler == \'\')
              {
              
                  $.ajax({
                    url: \'index.php?module='.$module.'&action='.$action.''.($id?'&id='.$id:'').'&cmd='.$funktionsave.'\',
                    type: \'POST\',
                    dataType: \'json\',
                    data: { 
                      id:$(\'#'.$prefix.'_id\').val() 
                      ');
              foreach($fields as $k => $v)
              {
                $type = 'text';
                if(isset($v['type'])) {
                  $type = $v['type'];
                }
                if($type === 'legend') {
                  continue;
                }
                if($type === 'checkbox'){
                  $this->app->Tpl->Add('JQUERYREADY', ' 
                ,' . $k . ':($(\'#' . $prefix . '_' . $k . '\').prop(\'checked\')?1:0) 
                  ');
                }elseif($type === 'radiobutton'){
                  $this->app->Tpl->Add('JQUERYREADY', ' 
                ,' . $k . ':($(\'input[name=' . $prefix . '_' . $k . ']:checked\').val()) 
                  ');
                }else{
                  $this->app->Tpl->Add('JQUERYREADY',' 
                ,'.$k.':($(\'#'.$prefix.'_'.$k.'\').val()) 
                  ');
                }

              }
              
              $this->app->Tpl->Add('JQUERYREADY',' 
                    },
                    success: function(data) {
                    if(typeof data.status != \'undefined\' && data.status == 0 && typeof data.error != \'undefined\')
                    {
                       alert(data.error);
                       return;
                    }
                    
                    $(\'#'.$element.'\').dialog(\'close\');
              '.$updatetable.'
              '.$onsave.'
                }              
              });
              
              
              }else alert(fehler);
              
              
                  
                }'
              );
            }

        $this->app->Tpl->Add('JQUERYREADY', '  },
          close: function(event, ui){
            '.$afterclose.'
          }
        });
        $(\'#'.$element.'\').on("dialogopen",function(){
         ');
        if(!empty($width))
        {
          $this->app->Tpl->Add('JQUERYREADY', '$(\'#'.$element.'\').parent().css(\'width\',\''.$width.'\');');
        }
        if(!empty($left))
        {
          $this->app->Tpl->Add('JQUERYREADY', '$(\'#'.$element.'\').parent().css(\'left\',\''.$left.'\');');
        }
          
        $this->app->Tpl->Add('JQUERYREADY',   '
        });
    ');
      
    }
  
  
    function AutoSaveFormular($prefix, $fields, $options = null)
    {
      /** @var SystemConfigModule $systemConfig */
      $systemConfig = $this->app->Container->get('SystemConfigModule');

      $target = 'TAB1';
      $finaltarget = 'PAGE';
      $template = 'tabview.tpl';
      $legend = '{|Einstellungen|}';
      if($options && isset($options['target']))$target = $options['target'];
      if($options && isset($options['finaltarget']))$finaltarget = $options['finaltarget'];
      if($options && isset($options['template']))$template = $options['template'];
      if($options && isset($options['legend']))$legend = $options['legend'];
      $this->app->Tpl->Add($target, '<fieldset><legend>'.$legend.'</legend><table>');
      if($fields)
      {
        foreach($fields as $k => $v)
        {
          $type = 'text';
          if(isset($v['type'])) {
            $type = $v['type'];
          }
          $size = '';
          if(!empty($v['size'])) {
            $size = $v['size'];
          }
          $placeholder = '';
          if(!empty($v['placeholder'])) {
            $placeholder = $v['placeholder'];
          }
          $bezeichnung = ucfirst($k);
          if(isset($v['bezeichnung'])) {
            $bezeichnung = $v['bezeichnung'];
          }
          if($type === 'legend') {
            $this->app->Tpl->Add($target,'</table></fieldset><fieldset><legend>'.$bezeichnung.'</legend><table>');
          }else{
            $name = $k;
            if(substr(trim($bezeichnung), -1) !== ':') {
              $bezeichnung .= ':';
            }
            $value = $this->app->erp->GetKonfiguration($prefix.'_'.$k);

            $isNewConfigValue = false;
            if(empty($value)){
              $value = $systemConfig->tryGetValue($prefix, $k);
              if(!empty($value)){
                $isNewConfigValue = true;
              }
            }
            if($type !== 'select') {
              $this->app->Tpl->Add($target,
                '<tr>
                  <td><label for="' . $name . '">' . $bezeichnung . '</label></td>
                  <td><input type="' . $type . '" ' . ($size ? ' size="' . $size . '" ' : '') .
                ' name="' . $name . '" id="' . $name . '" ' .
                ($type === 'checkbox' ? ' value="1" ' . ($value ? ' checked="checked" ' : '') : ' value="' . $value . ''). '"'.
                (!empty($placeholder)?' placeholder="'.htmlspecialchars($placeholder).'" ' :'').' /></td>
                </tr>'
              );
            }else{
              $this->app->Tpl->Add($target,
                '<tr>
                  <td><label for="' . $name . '">' . $bezeichnung . '</label></td>
                  <td><select  name="' . $name . '" id="' . $name . '">'
              );
              if(!empty($v['optionen']))
              {
                foreach($v['optionen'] as $k2 => $v2)
                {
                  $this->app->Tpl->Add($target,
                    '<option value="'.$k2.'"'.($k2 == $value?' selected="selected"':'').'>'.$v2.'</option>'
                  );
                }
              }
              $this->app->Tpl->Add($target,
                '</select></td></tr>'
              );
            }
            $delimiter = '_';
            if($isNewConfigValue){
              $delimiter = '__';
            }
            $this->app->YUI->AutoSaveKonfiguration($name, $prefix.$delimiter.$k);
          }
        }
      }
      $this->app->Tpl->Add($target, '</table></fieldset>');
      $this->app->Tpl->Parse($finaltarget, $template);
    }
    
    function DisableFormular($fieldname,$filter)
    {
      if(!is_array($filter))return;
      
      $tpl = '$(document).ready(function() { disablefunction'.$fieldname.'(); 
        $(\'input[name="'.$fieldname.'"]\').on(\'change\',function(){ disablefunction'.$fieldname.'(); });
        $(\'select[name="'.$fieldname.'"]\').on(\'change\',function(){ disablefunction'.$fieldname.'(); });
      });
        
        function disablefunction'.$fieldname.'() {
              var usestandard = true;
        var disableformulareltyp'.$fieldname.' = \'\';
        if($(\'input[name="'.$fieldname.'"]\').is(\'input[type="checkbox"]\'))disableformulareltyp'.$fieldname.' = \'checkbox\';
        if($(\'input[name="'.$fieldname.'"]\').is(\'input[type="radio"]\'))disableformulareltyp'.$fieldname.' = \'radio\';
        if($(\'select[name="'.$fieldname.'"]\').is(\'select\'))disableformulareltyp'.$fieldname.' = \'select\';
        if(disableformulareltyp'.$fieldname.' != \'\')
        {
        
        ';
      
      foreach($filter as $k => $f)
      {
        if($f)$tpl .= '$(\'.'.$f.', .'.$f.' input,.'.$f.' select ,.'.$f.' textarea \').prop(\'disabled\',\'\');';
      }

      
      foreach($filter as $k => $f)
      {
        if($f)
        {
          $tpl .= 'if(disableformulareltyp'.$fieldname.' == \'checkbox\')
          {
            ';
            
          if($k == 'checked' || $k == 1)
          {
            $tpl .= 'if($(\'input[name="'.$fieldname.'"]\').prop(\'checked\'))
            {
              $(\'.'.$f.', .'.$f.' input,.'.$f.' select ,.'.$f.' select \').prop(\'disabled\',\'disabled\');
              usestandard = false;
            }
              ';
          }elseif($k == 'unchecked' || !$k){
            $tpl .= 'if(!$(\'input[name="'.$fieldname.'"]\').prop(\'checked\'))
            {
              $(\'.'.$f.', .'.$f.' input,.'.$f.' select ,.'.$f.' select \').prop(\'disabled\',\'disabled\');
              usestandard = false;
            }
              ';
          }
          $tpl .= '
            
          }else if(disableformulareltyp'.$fieldname.' == \'radio\')
          {
            $(\'input[name="'.$fieldname.'"]:checked\').first().each(function(){
              if($(this).val() == \''.$k.'\')
              {
                $(\'.'.$f.', .'.$f.' input,.'.$f.' select,.'.$f.' select  \').prop(\'disabled\',\'disabled\');
                usestandard = false;
              }
            });
            
          }else if(disableformulareltyp'.$fieldname.' == \'select\')
          {
            if($(\'select[name="'.$fieldname.'"]\').val() == \''.$k.'\')
            {
              $(\'.'.$f.', .'.$f.' input,.'.$f.' select,.'.$f.' select  \').prop(\'disabled\',\'disabled\');
              usestandard = false;
            }
          }          
          ';
        }
      }
      
      foreach($filter as $k => $f)
      {
        if($f)$tpl .= 'if(usestandard){ $(\'.'.$f.', .'.$f.' input,.'.$f.' select,.'.$f.' select  \').prop(\'disabled\',\'disabled\');}';
        break;
      }
      
      $tpl .= '
        }
      }';
      $this->app->Tpl->Add('JAVASCRIPT',$tpl);
    }
    
    function HideFormular($fieldname, $filter)
    {
      if(!is_array($filter))return;
      
      $tpl = '
        
        function hidefunction'.$fieldname.'() { 
        var hideformulareltyp'.$fieldname.' = \'\';
        var usestandard = true;
        if($(\'input[name="'.$fieldname.'"]\').is(\'input[type="checkbox"]\'))hideformulareltyp'.$fieldname.' = \'checkbox\';
        if($(\'input[name="'.$fieldname.'"]\').is(\'input[type="radio"]\'))hideformulareltyp'.$fieldname.' = \'radio\';
        if($(\'select[name="'.$fieldname.'"]\').is(\'select\'))hideformulareltyp'.$fieldname.' = \'select\';
        if(hideformulareltyp'.$fieldname.' != \'\')
        {
        
        ';
      
      foreach($filter as $k => $_f)
      {
        if(preg_match('/^([a-zA-Z0-9\_\s]+)$/', $_f, $_matches))
        {
          $fa = explode(' ',$_f);
        }else $fa = array($_f);
        foreach($fa as $f)
        {
          if($f !== '')$tpl .= '$(\'.'.$f.'\').css(\'display\',\'\');';
        }
      }

      
      foreach($filter as $k => $_f)
      {
        if(preg_match('/^([a-zA-Z0-9\_\s]+)$/', $_f, $_matches))
        {
          $fa = explode(' ',$_f);
        }else $fa = array($_f);
        foreach($fa as $f)
        {
          if($f)
          {
            $fa = explode(' ', $f);
            $tpl .= 'if(hideformulareltyp'.$fieldname.' == \'checkbox\')
            {
              ';
              
            if($k == 'checked' || $k == 1)
            {
              $tpl .= 'if($(\'input[name="'.$fieldname.'"]\').prop(\'checked\'))
              {
                $(\'.'.$f.'\').css(\'display\',\'none\');
                usestandard = false;
              }
                ';
            }elseif($k == 'unchecked' || !$k){
              $tpl .= 'if(!$(\'input[name="'.$fieldname.'"]\').prop(\'checked\'))
              {
                $(\'.'.$f.'\').css(\'display\',\'none\');
                usestandard = false;
              }
                ';
            }
            $tpl .= '
              
            }else if(hideformulareltyp'.$fieldname.' == \'radio\')
            {
              $(\'input[name="'.$fieldname.'"]:checked\').first().each(function(){
                if($(this).val() == \''.$k.'\')
                {
                  $(\'.'.$f.'\').css(\'display\',\'none\');
                  usestandard = false;
                }
              });
              
            }else if(hideformulareltyp'.$fieldname.' == \'select\')
            {
              if($(\'select[name="'.$fieldname.'"]\').val() == \''.$k.'\')
              {
                $(\'.'.$f.'\').css(\'display\',\'none\');
                usestandard = false;
              }
            }          
            ';
          }
        }
      }
      
      foreach($filter as $k => $_f)
      {
        if(preg_match('/^([a-zA-Z0-9\_\s]+)$/', $_f, $_matches))
        {
          $fa = explode(' ',$_f);
        }else $fa = array($_f);
        foreach($fa as $f)
        {
          if($f !== '')$tpl .= 'if(usestandard) { $(\'.'.$f.'\').css(\'display\',\'none\');}';
        }
        break;
      }
      
      $tpl .= '
        }
      }
      
      
      $(document).ready(function() { hidefunction'.$fieldname.'(); 
        $(\'input[name="'.$fieldname.'"]\').on(\'change\',function(){ hidefunction'.$fieldname.'(); });
        $(\'select[name="'.$fieldname.'"]\').on(\'change\',function(){ hidefunction'.$fieldname.'(); });
      });
      
      ';
      
      $this->app->Tpl->Add('JAVASCRIPT',$tpl);
    }

    function AutoSaveKonfiguration($feldname,$konfigurationsname,$success="")
    {

      $tpl = "$(\"[name='$feldname']\").change(function() {
        var wert = $.base64Encode( $(\"[name='$feldname']\").val());
        if($(this).is(\"[type='checkbox']\") && !$(this).prop('checked'))wert = $.base64Encode('');
          $.ajax({
            type: \"POST\",
            url: \"index.php?module=ajax&action=autosavekonfiguration\",
            data:  { name: \"$konfigurationsname\", value: wert }
          }).done(function( data ) {
            $success
          });
        });
        $(\"[name='$feldname']\").focusout(function() {
        var wert = $.base64Encode( $(\"[name='$feldname']\").val() );
        if($(this).is(\"[type='checkbox']\") && !$(this).prop('checked'))wert = $.base64Encode('');
          $.ajax({
            type: \"POST\",
            url: \"index.php?module=ajax&action=autosavekonfiguration\",
            data:  { name: \"$konfigurationsname\", value: wert }
          }) .done(function( data ) {
            $success
          });


        });


        ";

      $this->app->Tpl->Add('AUTOCOMPLETE',$tpl);
    }
    

    function AutoSaveUserParameter($feldname,$parametername,$success="")
    {
      $tpl = "$(\"[name='$feldname']\").change(function() {
        var wert = $.base64Encode( $(\"[name='$feldname']\").val() );
        if($(this).is(\"[type='checkbox']\") && !$(this).prop('checked'))wert = $.base64Encode('');
          $.ajax({
            type: \"POST\",
            url: \"index.php?module=ajax&action=autosaveuserparameter\",
            data:  { name: \"$parametername\", value: wert }
          }) .done(function( data ) {
            $success
          });
        });
        $(\"[name='$feldname']\").focusout(function() {
        var wert = $.base64Encode( $(\"[name='$feldname']\").val() );
        if($(this).is(\"[type='checkbox']\") && !$(this).prop('checked'))wert = $.base64Encode('');
          $.ajax({
            type: \"POST\",
            url: \"index.php?module=ajax&action=autosaveuserparameter\",
            data:  { name: \"$parametername\", value: wert }
          }) .done(function( data ) {
            $success
          });


        });
        ";

      $this->app->Tpl->Add('AUTOCOMPLETE',$tpl);
    }

    
    function ChartDB($sql, $parsetarget, $width, $height, $limitmin = 0, $limitmax = 100, $gridy = 5) {

      $result = $this->app->DB->SelectArr($sql);
      for ($i = 0;$i < count($result);$i++) {
        $lables[] = $result[$i]['legende'];
        $values[] = $result[$i]['wert'];
      }
      $values = array_reverse($values, false);
      $lables = array_reverse($lables, false);
      $this->app->YUI->ChartAdd("#4040FF", $values);
      $this->app->YUI->Chart('TAB3', $lables, $width, $height, $limitmin, $limitmax, $gridy);
    }
    
    function Chart($parsetarget, $labels, $width = 400, $height = 200, $limitmin = 0, $limitmax = 100, $gridy = 5) {
      $werte = '';
      $values = $labels;
      for ($i = 0;$i < count($values) - 1;$i++) {
        $werte = $werte . "'" . $values[$i] . "',";
      }
      $werte = $werte . "'" . $values[$i + 1] . "'";
      $this->app->Tpl->Set('LABELS', "[" . $werte . "]");
      $this->app->Tpl->Set('CHART_WIDTH', $width);
      $this->app->Tpl->Set('CHART_HEIGHT', $height);
      $this->app->Tpl->Set('LIMITMIN', $limitmin);
      $this->app->Tpl->Set('LIMITMAX', $limitmax);
      $this->app->Tpl->Set('GRIDX', count($values));
      $this->app->Tpl->Set('GRIDY', $gridy);
      $this->app->Tpl->Parse($parsetarget, "chart.tpl");
    }

  /**
   * @param string $popupId
   * @param string $title
   * @param string $url
   * @param string $formular
   * @param string $uniqueid
   * @param string $elementId
   *
   * @return string
   */
    public function addConfirmPopup($popupId, $title, $url, $formular, $uniqueid = '', $elementId = '')
    {
      $this->app->Tpl->Set('POPUPID', $popupId);
      $this->app->Tpl->Set('UNIQUEID', $uniqueid);
      $this->app->Tpl->Set('ELEMENTID', $elementId);
      $this->app->Tpl->Set('POPUPTITLE', $title);
      $this->app->Tpl->Set('POPUPURL', $url);
      $this->app->Tpl->Set('POPUPFORMULAR', $formular);

      return $this->app->Tpl->OutputAsString('confirm_popup.tpl');
    }
    
    function ChartAdd($color, $values) {
      $werte = '';
      for ($i = 0;$i < count($values) - 1;$i++) {
        $werte = $werte . $values[$i] . ",";
      }
      $werte = $werte . $values[$i + 1];
      $this->app->Tpl->Add('CHARTS', "c.add('', '$color', [ $werte]);");
    }
    
    function DateiUploadNeuVersion($parsetarget, $datei) {
      $speichern = $this->app->Secure->GetPOST("speichern");
      $module = $this->app->Secure->GetGET("module");
      $action = $this->app->Secure->GetGET("action");
      $id = $this->app->Secure->GetGET("id");
      if($id)$this->app->Tpl->Set('ID',$id);
      if ($speichern != "") {
        $titel = $this->app->Secure->GetPOST("titel");
        $beschreibung = $this->app->Secure->GetPOST("beschreibung");
        $stichwort = $this->app->Secure->GetPOST("stichwort");
        $this->app->Tpl->Set('TITLE', $titel);
        $this->app->Tpl->Set('BESCHREIBUNG', $beschreibung);
        
        if ($_FILES['upload']['tmp_name'] == "") {
          $this->app->Tpl->Set('ERROR', "<div class=\"info\">Bitte w&auml;hlen Sie eine Datei aus und laden Sie diese herauf!</div>");
          $this->app->erp->EnableTab("tabs-2");
        } else {

          //$fileid = $this->app->erp->CreateDatei($_FILES['upload']['name'],$titel,$beschreibung,"",$_FILES['upload']['tmp_name'],$this->app->User->GetName());
          $this->app->erp->AddDateiVersion($datei, $this->app->User->GetName(), $_FILES['upload']['name'], "Neue Version", $_FILES['upload']['tmp_name']);
          header("Location: index.php?module=$module&action=$action&id=$id");
          exit;
        }
      }
      $this->app->Tpl->Set('STARTDISABLE', "<!--");
      $this->app->Tpl->Set('ENDEDISABLE', "-->");
      $this->app->Tpl->Parse($parsetarget, "datei_neudirekt.tpl");
    }
    
    function DateiUpload($parsetarget, $objekt, $parameter, $optionen = null) {
      $speichern = $this->app->Secure->GetPOST("speichern");
      $module = $this->app->Secure->GetGET("module");
      $action = $this->app->Secure->GetGET("action");
      $typ = $this->app->Secure->GetGET("typ");
      $cmd = $this->app->Secure->GetGET("cmd");
      if($cmd == 'up')
      {
        $sid = (int)$this->app->Secure->GetPOST("sid");
        $sort = $this->app->DB->Select("SELECT sort FROM datei_stichwoerter WHERE id = '$sid' LIMIT 1");
        $id = (int)$this->app->Secure->GetGET("id");
        switch($module)
        {
          case "adresse": $objekt="adressen"; break;
          default: $objekt=$module;
        }

        if(!preg_match('/[A-Za-z_]/', $objekt))$objekt="";
        $parameter=$id;
        // fester filter
        $where = "s.objekt LIKE '$objekt' AND s.parameter='$parameter' AND d.geloescht=0 AND s.sort <= '$sort'";

        $sql = "SELECT s.id,s.sort 
            FROM datei d 
            LEFT JOIN datei_stichwoerter s ON d.id=s.datei
            LEFT JOIN (SELECT datei, max(version) as version FROM datei_version GROUP BY datei ) v2  ON v2.datei=d.id
            LEFT JOIN datei_version v ON v.datei=v2.datei AND v.version = v2.version WHERE $where ORDER BY s.sort DESC LIMIT 2 ";
        $query = $this->app->DB->SelectArr($sql);
        $status = 0;
        if($query && count($query) == 2)
        {
          $status = 1;
          $this->app->DB->Update("UPDATE datei_stichwoerter SET sort = '".$query[1]['sort']."' WHERE id = '".$query[0]['id']."' LIMIT 1");
          $this->app->DB->Update("UPDATE datei_stichwoerter SET sort = '".$query[0]['sort']."' WHERE id = '".$query[1]['id']."' LIMIT 1");
        }
        $arr = array('status'=>$status);
        echo json_encode($arr);
        exit;
      }
      if($cmd == 'down')
      {
        $sid = (int)$this->app->Secure->GetPOST("sid");
        $sort = $this->app->DB->Select("SELECT sort FROM datei_stichwoerter WHERE id = '$sid' LIMIT 1");
        $id = (int)$this->app->Secure->GetGET("id");
        switch($module)
        {
          case "adresse": $objekt="adressen"; break;
          default: $objekt=$module;
        }

        if(!preg_match('/[A-Za-z_]/', $objekt))$objekt="";
        $parameter=$id;
        // fester filter
        $where = "s.objekt LIKE '$objekt' AND s.parameter='$parameter' AND d.geloescht=0 AND s.sort >= '$sort'";

        $sql = "SELECT s.id,s.sort 
            FROM datei d 
            LEFT JOIN datei_stichwoerter s ON d.id=s.datei
            LEFT JOIN (SELECT datei, max(version) as version FROM datei_version GROUP BY datei ) v2  ON v2.datei=d.id
            LEFT JOIN datei_version v ON v.datei=v2.datei AND v.version = v2.version WHERE $where ORDER BY s.sort LIMIT 2 ";
        $query = $this->app->DB->SelectArr($sql);
        $status = 0;
        if($query && count($query) == 2)
        {
          $status = 1;
          $this->app->DB->Update("UPDATE datei_stichwoerter SET sort = '".$query[1]['sort']."' WHERE id = '".$query[0]['id']."' LIMIT 1");
          $this->app->DB->Update("UPDATE datei_stichwoerter SET sort = '".$query[0]['sort']."' WHERE id = '".$query[1]['id']."' LIMIT 1");
        }
        $arr = array('status'=>$status);
        echo json_encode($arr);
        exit;
      }
      $_module = $module;
      $_action = $action;
      if($optionen && isset($optionen['module']))$module = $optionen['module'];
      if($optionen && isset($optionen['action']))$action = $optionen['action'];
      $id = $this->app->Secure->GetGET("id");
      $sid = $this->app->Secure->GetGET("sid");
      if($id)$this->app->Tpl->Set('ID', $id);
      if ($speichern != "") {
        if($parameter == '')$parameter = $id;
        if(isset($_POST['dateiv']))
        {
          foreach($_POST['dateiv'] as $k => $v)
          {
            $name = $this->app->DB->real_escape_string($_POST['dateiname'][$k]);
            $titel = $this->app->DB->real_escape_string($_POST['dateititel'][$k]);
            $beschreibung = $this->app->DB->real_escape_string($_POST['beschreibung'][$k]);
            $stichwort = $this->app->DB->real_escape_string($_POST['dateistichwort'][$k]);
            
            //$getMime = explode('.', $name);
            //$mime = end($getMime);

            $data = explode(',', $v);

            $encodedData = str_replace(' ','+',$data[1]);
            $decodedData = base64_decode($encodedData);
            
            $this->app->Tpl->Set('TITLE', $titel);
            $this->app->Tpl->Set('BESCHREIBUNG', $beschreibung);
            
            if ($v == "" ) {
              $this->app->Tpl->Set('ERROR', "<div class=\"error\">Keine Datei ausgew&auml;hlt!</div>");
              $this->app->erp->EnableTab("tabs-2");
            } else {
              $fileid = $this->app->erp->CreateDatei($name, $titel, $beschreibung, "", $decodedData, $this->app->User->GetName());

              // stichwoerter hinzufuegen
              $this->app->erp->AddDateiStichwort($fileid, $stichwort, $objekt, $parameter);
            }            
          }
          if($_FILES['upload']['tmp_name'] == "")
          {
            header("Location: index.php?module=$_module&action=$_action&id=$id&sid=$sid".($typ!=''?"&typ=".$typ:''));
          }
        }

        
        
        $titel = $this->app->Secure->GetPOST("titel");
        $beschreibung = $this->app->Secure->GetPOST("beschreibung");
        $stichwort = $this->app->Secure->GetPOST("stichwort");
        $this->app->Tpl->Set('TITLE', $titel);
        $this->app->Tpl->Set('BESCHREIBUNG', $beschreibung);

        if ($_FILES['upload']['tmp_name'] == "" && empty($_POST['dateiv'])) {
          $this->app->Tpl->Set('ERROR', "<div class=\"error\">Keine Datei ausgew&auml;hlt!</div>");
          $this->app->erp->EnableTab("tabs-2");
        } elseif($_FILES['upload']['tmp_name'] != '') {
          $fileid = $this->app->erp->CreateDatei($_FILES['upload']['name'], $titel, $beschreibung, "", $_FILES['upload']['tmp_name'], $this->app->User->GetName());

          // stichwoerter hinzufuegen
          $this->app->erp->AddDateiStichwort($fileid, $stichwort, $objekt, $parameter);
          header("Location: index.php?module=$_module&action=$_action&id=$id&sid=$sid".($typ!=''?"&typ=".$typ:''));
        }
      }

      if($this->app->Secure->GetPOST('sammelpdf'))
      {
        $auswahl = $this->app->Secure->GetPOST('auswahl');
        if(!($auswahl))
        {
          echo 'Keine PDF-Dateien ausgew&auml;hlt!';
          exit;
          $this->app->Tpl->Add('MESSAGE','<div class="error">Keine Dateien ausgew&auml;hlt!</div>');
        }else{
          $objekt = $this->app->Secure->GetGET('module');
          if($objekt == 'adresse')$objekt = 'adressen';          
          $parameter = (int)$this->app->Secure->GetGET('id');
          $alledateien = $this->app->DB->SelectArr("SELECT  v.datei, v.id FROM 
              datei d INNER JOIN datei_stichwoerter s ON d.id=s.datei INNER JOIN datei_version v ON v.datei=d.id WHERE s.objekt LIKE '$objekt' AND s.parameter='$parameter' AND d.geloescht=0 ");         
          
          $auswahl = explode(',',$auswahl);
          foreach($auswahl as $k => $ausw)
          {
            $found = false;
            if($alledateien)
            {
              foreach($alledateien as $datei)
              {
                if($datei['datei'] == $ausw)
                {
                  $dateiname = $this->app->erp->GetDateiPfad($datei['datei']);
                  if($dateiname && file_exists($dateiname) && mime_content_type($dateiname) == 'application/pdf')
                  {
                    $found = true;
                    break;
                  }
                }
              }
            }
            if(!$found)unset($auswahl[$k]);
          }
          if(!$auswahl || count($auswahl) == 0)
          {
            echo 'Keine PDF-Dateien ausgew&auml;hlt!';
            exit; 
            $this->app->Tpl->Add('MESSAGE','<div class="error">Keine PDF-Dateien ausgew&auml;hlt!</div>');
          }else{
            try {
              /** @var \Xentral\Components\Pdf\PdfMerger $pdfMerger */
              $pdfMerger = $this->app->Container->get('PdfMerger');

              $mergeInputPaths = [];
              foreach ($auswahl as $dateiid){
                $mergeInputPaths[] = $this->app->erp->GetDateiPfad($dateiid);
              }

              // Dateien zusammenführen
              $mergeOutputPath = realpath($this->app->erp->GetTMP()) . '/' . uniqid('sammelpdf_') . '.pdf';
              $pdfMerger->merge($mergeInputPaths, $mergeOutputPath);
              header('Content-type: application/pdf');
              readfile($mergeOutputPath);
              $this->app->ExitXentral();

            } catch (\Xentral\Components\Pdf\Exception\PdfComponentExceptionInterface $exception) {
              echo 'Fehler beim Generieren der Sammelpdf: ' . htmlspecialchars($exception->getMessage());
              $this->app->ExitXentral();
            }
          }
        }
      }


      if($this->app->Secure->GetPOST('dateizip'))
      {
        $auswahl = $this->app->Secure->GetPOST('auswahl');
        if(!($auswahl))
        {
          echo 'Keine Dateien ausgew&auml;hlt!';
          exit;
          $this->app->Tpl->Add('MESSAGE','<div class="error">Keine Dateien ausgew&auml;hlt!</div>');
        }else{
          $objekt = $this->app->Secure->GetGET('module');
          if($objekt == 'adresse')$objekt = 'adressen';
          $typmodul = $this->app->Secure->GetPOST('typ');
          if($objekt == 'dateien' && $typmodul == 'geschaeftsbrief_vorlagen'){
            $objekt = $typmodul;
          }
          $parameter = (int)$this->app->Secure->GetGET('id');
          $alledateien = $this->app->DB->SelectArr("SELECT  v.datei, v.id FROM 
              datei d INNER JOIN datei_stichwoerter s ON d.id=s.datei INNER JOIN datei_version v ON v.datei=d.id WHERE s.objekt LIKE '$objekt' AND s.parameter='$parameter' AND d.geloescht=0 ");

          $auswahl = explode(',',$auswahl);
          foreach($auswahl as $k => $ausw)
          {
            $found = false;
            if($alledateien)
            {
              foreach($alledateien as $datei)
              {
                if($datei['datei'] == $ausw)
                {
                  $dateiname = $this->app->erp->GetDateiPfad($datei['datei']);
                  if($dateiname && file_exists($dateiname))
                  {
                    $found = true;
                    break;
                  }
                }
              }
            }
            if(!$found)unset($auswahl[$k]);
          }
          if(!$auswahl || count($auswahl) == 0)
          {
            echo 'Keine Dateien ausgew&auml;hlt!';
            exit; 
            $this->app->Tpl->Add('MESSAGE','<div class="error">Keine Dateien ausgew&auml;hlt!</div>');
          }else{

            $dateinamezip = "Dateien_".date('Y-m-d').".zip";

            $zip = new ZipArchive;
            $zip->open($dateinamezip, ZipArchive::CREATE);
            
            foreach($auswahl as $dateiid)
            {
              $filename = $this->app->erp->GetDateiPfad($dateiid);
              $zip->addFile($filename, $this->app->erp->GetDateiName($dateiid));
            }
                      
            $zip->close();

            // download
            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=$dateinamezip");
            header("Content-Length: " . filesize($dateinamezip));

            readfile($dateinamezip);
            unlink($dateinamezip);
            exit; 

          }
        }
      }

      // Spezielle Upload-Fehler abfangen
      $helpdeskUploadSettingsLink = '<a target="_blank" href="https://xentral.biz/akademie-faq/grundinstallation-server-hd-manchmal-ist-der-upload-von-dateien-nicht-moglich-gibt-es-beim-upload-bestimmte-begrenzungen-was-dateiformat-grosse-usw-angeht">Link</a>';
      // post_max_size zu klein
      if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST' && empty($_POST)) {
        $errorMsg = '<div class="error">';
        $errorMsg .= 'Die Datei konnte nicht hochgeladen werden. ';
        $errorMsg .= 'Die post_max_size-Einstellung in der php.ini ist kleiner als die hochgeladene Datei! ';
        $errorMsg .= 'Wollen Sie größere Dateien hochladen, bitte befolgen Sie die Anleitung im Heldesk: ';
        $errorMsg .= $helpdeskUploadSettingsLink;
        $errorMsg .= '</div>';
        $this->app->Tpl->Set('ERROR',$errorMsg);
      }
      // upload_max_filesize zu klein
      if (!empty($_FILES) && $_FILES['upload']['error'] === UPLOAD_ERR_INI_SIZE) {
        $errorMsg = '<div class="error">';
        $errorMsg .= 'Die Datei konnte nicht hochgeladen werden. ';
        $errorMsg .= 'Die upload_max_filesize-Einstellung in der php.ini ist kleiner als die hochgeladene Datei! ';
        $errorMsg .= 'Wollen Sie größere Dateien hochladen, bitte befolgen Sie die Anleitung im Heldesk: ';
        $errorMsg .= $helpdeskUploadSettingsLink;
        $errorMsg .= '</div>';
        $this->app->Tpl->Set('ERROR',$errorMsg);
      }

      if($parameter == ''){
        $parameter = $this->app->Secure->GetGET('id');
      }

      if ($objekt != "" && $parameter != "") {
        /*
        $table = new EasyTable($this->app);
        $table->Query("SELECT d.titel, s.subjekt, v.version, v.ersteller, v.bemerkung, d.id FROM datei d LEFT JOIN datei_stichwoerter s ON d.id=s.datei  
        LEFT JOIN datei_version v ON v.datei=d.id
        WHERE s.objekt='$objekt' AND s.parameter='$parameter' AND d.geloescht=0");
        $table->DisplayNew('INHALT', "<!--<a href=\"index.php?module=dateien&action=send&fid=%value%&ext=.jpg\"  rel=\"group\" class=\"zoom2\">
        <img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/vorschau.png\" border=\"0\"></a>-->
        &nbsp;<a href=\"index.php?module=dateien&action=send&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/download.svg\" border=\"0\"></a>&nbsp;
        <!--<a href=\"index.php?module=dateien&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;-->
        <a href=\"#\"onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=dateien&action=delete&id=%value%';\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\" ></a>
        ");
         */
        if($_module == 'dateien' && $_action == 'popup') {

        }else{
          $this->app->Tpl->Set('ID',$this->app->Secure->GetGET('id'));
          $this->app->Tpl->Set('MODULE',$this->app->Secure->GetGET('module'));
          $this->app->Tpl->Parse('INHALT','datei_list_referer_filter.tpl');
          $this->app->YUI->TableSearch('INHALT', "datei_list_referer");
        }

        $fieldset = '';
        $fieldset = '<form method="POST" target="_blank">
                      <fieldset>
                        <legend>{|Stapelverarbeitung|}</legend>
                        <input type="checkbox" id="auswahlalle" onchange="alleauswaehlen();" />&nbsp;alle markieren&nbsp;
                        <input type="submit" class="btnBlue" value="{|ZIP erstellen|}" name="dateizip">
                        <input type="hidden" name="auswahl" id="auswahl">
                        <input type="submit" class="btnBlue" value="{|Sammel-PDF &ouml;ffnen|}" name="sammelpdf">
                        <input type="hidden" name="typ" value="[TYP]">

                      </fieldset>
                    </form>';

        $this->app->Tpl->Add('INHALT', $fieldset);
        $typ = $this->app->Secure->GetGET('typ');
        $this->app->Tpl->Set('TYP', $typ);
        $alleauswaehlen ="<script>
              function alleauswaehlen()
              {
                var wert = $('#auswahlalle').prop('checked');
                $('input[id^=auswahl_]').prop('checked', wert);
                chauswahl();
              }
            </script>";
        $this->app->Tpl->Add('SCRIPT', $alleauswaehlen);


        //$this->app->Tpl->Add('INHALT','<form method="POST" target="_blank"><input type="hidden" name="auswahl" id="auswahl" /><input type="submit" class="btnBlue" value="Sammel-PDF &ouml;ffnen" name="sammelpdf" /></form>');
      }

      $this->app->Tpl->Parse('TAB1', "rahmen70_ohne_form.tpl");

      $stichwoerter = $this->app->erp->getDateiTypen($module);
      foreach ($stichwoerter as $stichwort){
        $this->app->Tpl->Add('EXTRASTICHWOERTER','<option value="'.$stichwort['wert'].'">'.$stichwort['beschriftung'].'</option>');
      }

/* 
      $tmp = $this->app->DB->SelectArr("SELECT * FROM datei_stichwortvorlagen WHERE modul='' ORDER by beschriftung");
      for($i=0;$i<count($tmp);$i++)
        $this->app->Tpl->Add('EXTRASTICHWOERTER','<option value="'.$tmp[$i]['beschriftung'].'">'.$tmp[$i]['beschriftung'].'</option>');
*/
      $maxsize = 0;
      $uploadmaxsize = ini_get('upload_max_filesize');
      if(strpos($uploadmaxsize, 'M') !== false) {
        $maxsize = (int)str_replace(['M',' '],'', $uploadmaxsize)*1024*1024;
      }
      $maxpostsize = ini_get('post_max_size');
      if(strpos($maxpostsize, 'M') !== false) {
        $maxpostsize = (int)str_replace(['M',' '], '', $maxpostsize)*1024*1024;
      }
      if(!empty($maxpostsize) && ($maxpostsize < $maxsize || $maxsize === 0)) {
        $maxsize = $maxpostsize;
      }

      if($maxsize > 1024*1024 && !empty($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'],'nginx')!= false) {
        $maxsize = 1024*1024;
      }

      $this->app->Tpl->Set('MAXSIZE', (int)($maxsize * 0.70));

      if($_module === 'dateien' && $_action === 'popup') {
        $this->app->Tpl->Parse('TAB2', 'datei_neudirekt2.tpl');
      }else{
        $this->app->Tpl->Parse('TAB2', 'datei_neudirekt.tpl');
      }
      $this->app->Tpl->Set('AKTIV_TAB1', 'selected');
      if($_module === 'dateien' && $_action === 'popup')
      {
        $this->app->Tpl->Parse($parsetarget, 'dateienuebersicht2.tpl');
      }else{
        $this->app->Tpl->Parse($parsetarget, 'dateienuebersicht.tpl');
      }
    }

    function SortListAdd($parsetarget, &$ref, $menu, $sql, $sort = true) {

      $module = $this->app->Secure->GetGET("module");
      $id = $this->app->Secure->GetGET("id");
      $projekt = $this->app->DB->Select("SELECT projekt FROM `$module` WHERE id='$id' LIMIT 1");
      $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM $module WHERE id='$id'");
      $table = new EasyTable($this->app);
      $summencol = 6;
      $mengencol = 0;
      $zwischensumme = 0;
      $rabattcol = 0;
      $ecol = 0;
      $dcol = 0;
      $einkaufspreissumme = 0;
      $deckungsbeitragsumme = 0;
      if ($sort) $table->Query($sql . (strpos($sql,'b.sort')? " ORDER BY b.sort":" ORDER BY sort"));
      else $table->Query($sql);

      $this->app->erp->RunHook('sortlistadd', 3, $module, $id, $table);

      // letzte zeile anzeigen
      if(isset($table->headings) && isset($table->headings[-1]))unset($table->headings[-1]);

      if ($module == "lieferschein") {
        
        if ($schreibschutz != 1) {
          $table->AddRow(array('<form action="" method="post">', '[ARTIKELSTART]<input type="text" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);">[ARTIKELENDE]', '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >', '<input type="text" name="nummer" id="nummer" size="7">', '<input type="text" size="10" name="lieferdatum" id="lieferdatum">', '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblurmenge()\',200);">', '<input type="hidden" name="preis" id="preis" size="5" onclick="checkhere();">', '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen">

            <script type="text/javascript">
            document.onkeydown = function(evt) {
            evt = evt || window.event;
            if (evt.keyCode == 27) {
            document.getElementById("artikel").focus();
            document.getElementById("artikel").value="";
            document.getElementById("projekt").value="";
            document.getElementById("nummer").value="";
            document.getElementById("lieferdatum").value="";
            document.getElementById("menge").value="";
            if(typeof aktuallisierePreise != \'undefined\')setTimeout(function(){aktuallisierePreise()},200);
            }
            };
      </script>

        </form>'));
          $this->app->YUI->AutoCompleteAuftrag("artikel", "verkaufartikelnummerprojekt", 1, "&projekt=$projekt");
        }
      } else 
      if ($module === 'retoure') {
        if ($schreibschutz != 1) {
          $projekte = $this->app->User->getUserProjects();
          $projekte[] = 0;
          $projekte = implode(', ', $projekte);

          $rmavorlagen = $this->app->DB->SelectPairs(
            sprintf(
              'SELECT rvg.bezeichnung, lp.kurzbezeichnung 
              FROM rma_vorlagen_grund AS rvg
              LEFT JOIN lager_platz AS lp ON rvg.default_storagelocation = lp.id
              WHERE rvg.ausblenden = 0 AND (rvg.projekt IN (%s))
              ORDER BY rvg.bezeichnung',
              $projekte
            )
          );
          $selgrund = '';
          foreach($rmavorlagen as $rmabez => $rmalager) {
            $selgrund .=  '<option data-lager="'.$rmalager.'">'.$rmabez.'</option>';
          }

          $table->AddRow(
            array(
              '<form action="" method="post">', '[ARTIKELSTART]<input type="text" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);">[ARTIKELENDE]',
              '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >',
              '<input type="text" name="nummer" id="nummer" size="7">',
              '<input type="text" size="10" name="lieferdatum" id="lieferdatum">',
              '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblurmenge()\',200);">',
              '<input type="hidden" name="preis" id="preis" size="5" onclick="checkhere();"><input type="text" name="geliefert" id="geliefert" size="5" />',
              '<input type="text" name="menge_eingang" id="menge_eingang" size="5" />',
              '<input type="text" name="menge_gutschrift" id="menge_gutschrift" size="5" />',
              '<select class="selgrund" name="grund" id="grund">'.$selgrund.'</select>',
              '<input type="text" size="7" name="lager_platz" id="lager_platz" />',
              '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen">

            <script type="text/javascript">
            document.onkeydown = function(evt) {
            evt = evt || window.event;
            if (evt.keyCode == 27) {
            document.getElementById("artikel").focus();
            document.getElementById("artikel").value="";
            document.getElementById("projekt").value="";
            document.getElementById("nummer").value="";
            document.getElementById("lieferdatum").value="";
            document.getElementById("menge").value="";
            if(typeof aktuallisierePreise != \'undefined\')setTimeout(function(){aktuallisierePreise()},200);
            }
            };
      </script>

        </form>'));
          $this->app->YUI->AutoComplete('lager_platz', 'lagerplatz');
          $this->app->YUI->AutoCompleteAuftrag("artikel", "artikelnummer", 1, "&projekt=$projekt");
        }
      } else 
      if ($module == "inventur") {
        
        if ($schreibschutz != 1) {
          $table->AddRow(array('<form action="" method="post">', '[ARTIKELSTART]<input type="text" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);" [COMMONREADONLYINPUT]>[ARTIKELENDE]', '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >', '<input type="text" name="nummer" id="nummer" size="7">', '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblurmenge()\',200);">', '<input type="text" name="preis" id="preis" size="5" onclick="checkhere();">', '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen">

            <script type="text/javascript">
            document.onkeydown = function(evt) {
            evt = evt || window.event;
            if (evt.keyCode == 27) {
            document.getElementById("artikel").focus();
            document.getElementById("projekt").value="";
            document.getElementById("artikel").value="";
            document.getElementById("nummer").value="";
            document.getElementById("menge").value="";
            document.getElementById("preis").value="";
            if(typeof aktuallisierePreise != \'undefined\')setTimeout(function(){aktuallisierePreise()},200);
            }
            };
      </script>

        </form>'));
          //$this->app->YUI->AutoCompleteAddEvent("artikel", "artikelnummer", 1);
          $this->app->YUI->AutoCompleteAuftrag("artikel", "artikelnummer", 1, "&projekt=$projekt");
        }
      } else 
      if ($module == "anfrage" || $module == "preisanfrage") {
        
        if ($schreibschutz != 1) {
          $table->AddRow(array('<form action="" method="post">', '[ARTIKELSTART]<input type="text" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);" [COMMONREADONLYINPUT]>[ARTIKELENDE]', '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >', '<input type="text" name="nummer" id="nummer" size="7">', '<input type="text" size="10" name="lieferdatum" id="lieferdatum">', '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblurmenge()\',200);">', '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen">
            <script type="text/javascript">
            document.onkeydown = function(evt) {
            evt = evt || window.event;
            if (evt.keyCode == 27) {
            document.getElementById("artikel").focus();
            document.getElementById("artikel").value="";
            document.getElementById("nummer").value="";
            document.getElementById("lieferdatum").value="";
            document.getElementById("menge").value="";
            if(typeof aktuallisierePreise != \'undefined\')setTimeout(function(){aktuallisierePreise()},200);
            }
            };
      </script>
        </form>'));
          //$this->app->YUI->AutoCompleteAddEvent("artikel", "artikelnummer", 1, "&projekt=$projekt");
          $this->app->YUI->AutoCompleteAuftrag("artikel", "artikelnummer", 1, "&projekt=$projekt");
        }
      } else 
      if ($module == "arbeitsnachweis") {
        
        if ($schreibschutz != 1) {
          $table->AddRow(array('<form action="" method="post">', '[ADRESSESTART]<input type="text" size="20" name="adresse" id="adresse">[ADRESSEENDE]', '<input type="text" name="ort" id="ort" size="10">', '<input type="text" name="datum" id="datum" size="10">', '<input type="text" name="von" id="von" size="5">', '<input type="text" name="bis" id="bis" size="5">', '<input type="text" name="bezeichnung" id="bezeichnung" size="30">', '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen"> <input type="hidden" name="bezeichnunglieferant" id="bezeichnunglieferant"><input type="hidden" name="bestellnummer" id="bestellnummer">
            <script type="text/javascript">
            document.onkeydown = function(evt) {
            evt = evt || window.event;
            if (evt.keyCode == 27) {
            document.getElementById("adresse").focus();
            document.getElementById("adresse").value="";
            document.getElementById("ort").value="";
            document.getElementById("datum").value="";
            document.getElementById("von").value="";
            document.getElementById("bis").value="";
            document.getElementById("bezeichnung").value="";
            if(typeof aktuallisierePreise != \'undefined\')setTimeout(function(){aktuallisierePreise()},200);
            }
            };
      </script>
        </form>'));
          $this->app->YUI->AutoCompleteAddEvent("adresse", "mitarbeiter");
        }
      } else 
      if ($module == "kalkulation") {
        
        if ($schreibschutz != 1) {
          $table->AddRow(array('<form action="" method="post">', 
              '<select name="kalkulationart" onchange="
                document.getElementById(\'divbetrag\').style.display = \'block\';
                document.getElementById(\'divmenge\').style.display = \'block\';
                document.getElementById(\'divartikel\').style.display = \'none\';
                document.getElementById(\'divstueckliste\').style.display = \'none\';
                if(this.value==\'artikel\' || this.value==\'stueckliste\')
                document.getElementById(\'div\'+this.value).style.display = \'block\';
                if(this.value==\'zwischensumme\' || this.value==\'hauptpunkt\' || this.value==\'unterpunkt\') {
                  document.getElementById(\'divbetrag\').style.display = \'none\'; 
                  document.getElementById(\'divmenge\').style.display = \'none\'; 
                }
                if(this.value==\'zwischensummemengex\')
                  document.getElementById(\'divbetrag\').style.display = \'none\'; 
                if(this.value==\'einmalkosten\')
                  document.getElementById(\'divmenge\').style.display = \'none\'; 

                ">' . $this->app->erp->GetSelectKalkulationart() . '</select>', 
              '<div style="padding:3px"><input type="text" name="bezeichnung" id="bezeichnung" size="40"></div>
               <div id="divartikel" style="display:block;padding:5px;">Artikel-Nr.:&nbsp;<input type="text" name="artikel" id="artikel"></div>
               <div id="divstueckliste" style="display:none;padding:5px;">Stueckliste:&nbsp;<input type="text" name="stueckliste" id="stueckliste"></div>
              ', 
              '<div id="divmenge"><input type="text" name="menge" id="menge" size="8"></div>', 
              '<div id="divbetrag"><input type="text" name="betrag" id="betrag" size="8"></div>', 
              '',
              '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen"> <input type="hidden" name="bezeichnunglieferant" id="bezeichnunglieferant"><input type="hidden" name="bestellnummer" id="bestellnummer">
            <script type="text/javascript">
            document.onkeydown = function(evt) {
            evt = evt || window.event;
            if (evt.keyCode == 27) {
            document.getElementById("datum").focus();
            document.getElementById("datum").value="";
            document.getElementById("betrag").value="";
            document.getElementById("umsatzsteuer").value="";
            document.getElementById("bezeichnung").value="";
            if(typeof aktuallisierePreise != \'undefined\')setTimeout(function(){aktuallisierePreise()},200);
            }
            };
      </script></form>'));
          $this->app->YUI->AutoCompleteProduktion("artikel", "artikelnummer", 1);
          $this->app->YUI->AutoCompleteProduktion("stueckliste", "artikelnummer", 1);
        }
      } else 

      if ($module == "reisekosten") {
        
        if ($schreibschutz != 1) {
          $table->AddRow(array('<form action="" method="post">', '<input type="text" name="datum" id="datum" size="10">', '<select name="reisekostenart">' . $this->app->erp->GetSelectReisekostenart() . '</select>', '<input type="text" name="betrag" id="betrag" size="8">', '<input type="checkbox" name="abrechnen" id="abrechnen" value="1">', '<input type="checkbox" name="keineust" id="keineust" value="1">', '<select name="uststeuersatz">' . $this->app->erp->GetSelectSteuersatz("", $id, "reisekosten") . '</select>', '<input type="text" name="bezeichnung" id="bezeichnung" size="30">', '<select name="bezahlt_wie">' . $this->app->erp->GetSelectBezahltWie() . '
            </select>', '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen"> <input type="hidden" name="bezeichnunglieferant" id="bezeichnunglieferant"><input type="hidden" name="bestellnummer" id="bestellnummer">
            <script type="text/javascript">
            document.onkeydown = function(evt) {
            evt = evt || window.event;
            if (evt.keyCode == 27) {
            document.getElementById("datum").focus();
            document.getElementById("datum").value="";
            document.getElementById("betrag").value="";
            document.getElementById("umsatzsteuer").value="";
            document.getElementById("bezeichnung").value="";
            if(typeof aktuallisierePreise != \'undefined\')setTimeout(function(){aktuallisierePreise()},200);
            }
            };
      </script></form>'));
          $this->app->YUI->AutoCompleteAddEvent("adresse", "mitarbeiter");
        }
      } else 
      if ($module == "produktion") {
      } else 
      if ($module == "bestellung") {
        if ($schreibschutz != 1) {
          $table->AddRow(array('<form action="" method="post">', '[ARTIKELSTART]<input type="text" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',300);"  >[ARTIKELENDE]', '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >', '<input type="text" name="nummer" id="nummer" size="7">', '<input type="text" size="8" name="lieferdatum" id="lieferdatum">', '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblurmenge()\',200);"><input type="hidden" name="vpe" id="vpe">', '<input type="text" name="preis" id="preis" size="10" onclick="checkhere();">', '<input type="text" name="waehrung" id="waehrung" size="10" onclick="checkhere();">','<input type="submit" value="einf&uuml;gen" name="ajaxbuchen"> <input type="hidden" name="bezeichnunglieferant" id="bezeichnunglieferant"><input type="hidden" name="bestellnummer" id="bestellnummer">
            <script type="text/javascript">
            document.onkeydown = function(evt) {
            evt = evt || window.event;
            if (evt.keyCode == 27) {
            document.getElementById("artikel").focus();
            document.getElementById("artikel").value="";
            document.getElementById("projekt").value="";
            document.getElementById("nummer").value="";
            document.getElementById("lieferdatum").value="";
            document.getElementById("menge").value="";
            document.getElementById("preis").value="";
            document.getElementById("vpe").value="";
            document.getElementById("waehrung").value="";
            }
            };
      </script>
        </form>'));
        }
        $adresse = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id='$id' LIMIT 1");
        $this->app->YUI->AutoCompleteBestellung("artikel", "einkaufartikelnummerprojekt", 1, "&adresse=$adresse");
      } else 
      if ($module == "angebot" || $module == "auftrag" || $module == "rechnung" || $module == "gutschrift" || $module == "proformarechnung") {
        
        if ($schreibschutz != 1) {
          $addrow = array('<form action="" method="post" id="myform">', '[ARTIKELSTART]<input type="text" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);">[ARTIKELENDE]', '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >', '<input type="text" name="nummer" id="nummer" size="7">', '<input type="text" size="8" name="lieferdatum" id="lieferdatum">', '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblurmenge()\',200); document.getElementById(\'preis\').style.background =\'none\';">', '<input type="text" name="preis" id="preis" size="10" onclick="checkhere();">', '<input type="text" name="waehrung" id="waehrung" size="10" onclick="checkhere();">' ,'<input type="text" name="rabatt" id="rabatt" size="7">');
          $addrow[] = '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen">
            <script type="text/javascript">
            document.onkeydown = function(evt) {
            evt = evt || window.event;
            if (evt.keyCode == 27) {
            document.getElementById("artikel").focus();
            document.getElementById("artikel").value="";
            document.getElementById("projekt").value="";
            document.getElementById("nummer").value="";
            document.getElementById("lieferdatum").value="";
            document.getElementById("menge").value="";
            document.getElementById("preis").value="";
            document.getElementById("waehrung").value="";
            document.getElementById("rabatt").value="";
            }
            if (evt.keyCode == 160) { // pfeil rechts
              document.getElementById("menge").focus();
              document.getElementById("menge").select();
            }
            if (evt.keyCode == 39) { // pfeil rechts
              //checkhere();
              //document.getElementById("menge").focus();
              //checkhere();
            }
            if(typeof aktuallisierePreise != \'undefined\')setTimeout(function(){aktuallisierePreise()},200);
            };
      </script></form>';
          
          $table->AddRow($addrow);
          $adresse = $this->app->DB->Select("SELECT adresse FROM $module WHERE id='$id' LIMIT 1");
          $this->app->YUI->AutoCompleteAuftrag("artikel", "verkaufartikelnummerprojekt", 1, "&projekt=$projekt&adresse=$adresse");
        }
      } else {

        $shown = false;
        $this->app->erp->RunHook('yui_sortlistadd', 4, $module, $id, $table, $shown);
        if($shown === false){
          if($schreibschutz != 1){
            $addrow = array('<form action="" method="post">', '[ARTIKELSTART]<input type="text" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);">[ARTIKELENDE]', '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >', '<input type="text" name="nummer" id="nummer" size="7">', '<input type="text" size="8" name="lieferdatum" id="lieferdatum">', '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblurmenge()\',200);">', '<input type="text" name="preis" id="preis" size="10" onclick="checkhere();">');

            $addrow[] = '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen">
            <script type="text/javascript">
            document.onkeydown = function(evt) {
            evt = evt || window.event;
            if (evt.keyCode == 27) {
            document.getElementById("artikel").focus();
            document.getElementById("artikel").value="";
            document.getElementById("projekt").value="";
            document.getElementById("nummer").value="";
            document.getElementById("lieferdatum").value="";
            document.getElementById("menge").value="";
            document.getElementById("preis").value="";
            }
            };
      </script></form>';
            $table->AddRow($addrow);
            $adresse = $this->app->DB->Select("SELECT adresse FROM $module WHERE id='$id' LIMIT 1");
            $this->app->YUI->AutoCompleteAuftrag("artikel", "verkaufartikelnummerprojekt", 1, "&projekt=$projekt&adresse=$adresse");
          }
        }
      }
      
      $table->headings[0] = 'Pos';
      $table->headings[1] = 'Artikel';
      $table->headings[2] = 'Projekt';
      $table->headings[3] = 'Nummer';
      $table->headings[4] = 'Lieferung';
      $table->headings[5] = 'Menge';
  
      $table->width_headings[0] = '5%';
      $table->width_headings[1] = '25%';
      $table->width_headings[2] = '10%';
      $table->width_headings[3] = '10%';
      $table->width_headings[4] = '10%';
      $table->width_headings[5] = '10%';
      $table->width_headings[6] = '10%';

     
      
      if ($module == "lieferschein" || $module == "retoure") {
        $table->headings[6] = 'ausgeliefert';
        $zwischensumme = $this->app->DB->Select("SELECT sum(menge*preis) FROM $module"."_position WHERE $module = '$id'");
      } else
      if($module == "anfrage" || $module == "preisanfrage" ) {
        $table->headings[6] = 'Aktion';
        //$zwischensumme = $this->app->DB->Select("SELECT sum(menge*preis) FROM $module"."_position WHERE $module = '$id'");
      } else 
      if ($module == "inventur") {
        $table->headings[4] = 'Menge';
        $table->headings[5] = 'Preis';
        $summencol = 5;
        $mengencol = 4;
        $zwischensumme = $this->app->DB->Select("SELECT sum(menge*preis) FROM $module"."_position WHERE $module = '$id'");
      } else 
      if ($module == "produktion") {
        $table->headings[2] = 'Projekt';
        $table->headings[3] = 'Nummer';
        $table->headings[4] = 'Menge';
        $table->headings[5] = 'Lager';
        $table->headings[6] = 'Reserviert';
        $mengencol = 4;
      } else 
      if ($module == "bestellung") {
        $table->headings[6] = 'Preis';
        $table->headings[7] = 'W&auml;hrung';
        $zwischensumme = $this->app->DB->Select("SELECT sum(menge*preis) FROM $module"."_position WHERE $module = '$id'");
        $summencol = 6;
        $mengencol = 5;
      } else 
      if ($module == "arbeitsnachweis") {
        $table->headings[0] = 'Pos';
        $table->headings[1] = 'Mitarbeiter';
        $table->headings[2] = 'Ort';
        $table->headings[3] = 'Datum';
        $table->headings[4] = 'Von';
        $table->headings[5] = 'Bis';
        $table->headings[6] = 'Tätigkeit';
      } else 
      if ($module == "kalkulation") {
        $table->headings[0] = 'Pos';
        $table->headings[1] = 'Kostenart';
        $table->headings[2] = 'Beschreibung';
        $table->headings[3] = 'Menge';
        $table->headings[4] = 'Einzelbetrag';
        $table->headings[5] = 'Gesamt';
        $summencol = 5;
        $mengencol = 3;
  
      } else 
      if ($module == "reisekosten") {
        $table->headings[0] = 'Pos';
        $table->headings[1] = 'Datum';
        $table->headings[2] = 'Kostenart';
        $table->headings[3] = 'Betrag';
        $table->headings[4] = 'Abr. bei Kd';
        $table->headings[5] = 'sonst. MwSt'; // kann man auch umbenennen in Keine



        $table->headings[6] = 'MwSt';
        $table->headings[7] = 'Kommentar';
        $table->headings[8] = 'Bezahlt';

        $table->width_headings[1] = '10%';
        $table->width_headings[2] = '25%';
        $table->width_headings[3] = '10%';
        $table->width_headings[4] = '5%';
        $table->width_headings[5] = '5%';
        $table->width_headings[6] = '5%';
        $table->width_headings[7] = '20%';


      } else {
        if ($module == "angebot" || $module == "auftrag" || $module == "rechnung" || $module == "gutschrift")
        {
          $zwischensumme = $this->app->DB->Select("SELECT sum(menge*(preis * (100-rabatt)/100)) FROM $module"."_position WHERE $module = '$id'");
        }else{
          $zwischensumme = $this->app->DB->Select("SELECT sum(menge*preis) FROM $module"."_position WHERE $module = '$id'");
        }
        $table->headings[6] = 'Preis';
        $mengencol = 5;
        if ($module == "angebot" || $module == "auftrag" || $module == "rechnung" || $module == "gutschrift") $table->headings[7] = 'W&auml;hrung';
        if ($module == "angebot" || $module == "auftrag" || $module == "rechnung" || $module == "gutschrift") $table->headings[8] = 'Rabatt';
        if ($module == "angebot" || $module == "auftrag" || $module == "rechnung" || $module == "gutschrift") $rabattcol = 8;
      }
      $__arr = array($summencol, $mengencol, $rabattcol, $ecol, $dcol,$zwischensumme);
      $this->app->erp->RunHook('yui_sortlistadd_draw', 2,$table,$__arr);
      $summencol = (is_int($__arr[0]) ? $__arr[0] : 0);
      $mengencol = (is_int($__arr[1]) ? $__arr[1] : 0);
      $rabattcol = $__arr[2];
      $ecol = $__arr[3];
      $dcol = $__arr[4];
      $zwischensumme = $__arr[5];
      if ($module == "produktion" || $module == "angebot" || $module == "auftrag" || $module == "rechnung" || $module == "gutschrift") $table->width_headings[7] = '10%';
      $this->app->YUI->DatePicker("lieferdatum");
      $this->app->YUI->DatePicker("datum");
      $this->app->YUI->TimePicker("von");
      $this->app->YUI->TimePicker("bis");
      if(isset($summenarray))unset($summenarray);
      for($i = 0; $i < $summencol; $i++)$summenarray[] = '';
      $js = '';

      $this->app->Tpl->Add($parsetarget,'<style>
      tr.zwischensumme td:nth-child(3)
      {
        text-align:right;
      }
      tr.gruppensumme td:nth-child(3)
      {
        text-align:right;
      }
      ');
      if($rabattcol)
      {
        $this->app->Tpl->Add($parsetarget,'
        table.mkTable td:nth-child('.($rabattcol+1).')
        {
          text-align:right;
        }
        table#tableone td:nth-child('.($rabattcol+1).')
        {
          text-align:right;
        }');
      }
      if($summencol)
      {
        $this->app->Tpl->Add($parsetarget,'
        table.mkTable td:nth-child('.($summencol+1).')
        {
          text-align:right;
        }
        table#tableone td:nth-child('.($summencol+1).')
        {
          text-align:right;
        }');
      }
      if($mengencol)
      {
        $this->app->Tpl->Add($parsetarget,'
        table.mkTable td:nth-child('.($mengencol+1).')
        {
          text-align:right;
        }
        table#tableone td:nth-child('.($mengencol+1).')
        {
          text-align:right;
        }');
      }
      if($dcol)
      {
        $this->app->Tpl->Add($parsetarget,'
        table.mkTable td:nth-child('.($dcol+1).')
        {
          text-align:right;
        }
        table#tableone td:nth-child('.($dcol+1).')
        {
          text-align:right;
        }');
      }
      if($ecol)
      {
        $this->app->Tpl->Add($parsetarget,'
        table.mkTable td:nth-child('.($ecol+1).')
        {
          text-align:right;
        }
        table#tableone td:nth-child('.($ecol+1).')
        {
          text-align:right;
        }');
      }
      if ($module == "angebot" || $module == "auftrag" || $module == "rechnung" || $module == "gutschrift" || $module == "bestellung")
      {
        for($i = $module != 'bestellung'?7:5; $i <= 7; $i++)
        {
          $this->app->Tpl->Add($parsetarget,'
          table.mkTable td:nth-child('.($i+1).')
          {
            text-align:right;
          }
          table#tableone td:nth-child('.($i+1).')
          {
            text-align:right;
          }');
        }
      }
      $this->app->Tpl->Add($parsetarget,'</style>');
      
      if($summencol && $mengencol && $module != "produktion" && $module!="bestellung!" && $module!="preisanfrage")
      {
        $js = "<script type=\"text/javascript\">
        
        
        aktuallisierePreise();
        
        var holepreise = false;
        function aktuallisierePreise (){
          var summe = 0;
          var scol = $summencol;
          var mcol = $mengencol;
          if(holepreise)
          {
            holepreise = false;
            var preisestr = '';
            var mengestr = '';
            ".'$'."('#zwischensumme').parents('table').first().find('tr').each(function(){
              var sel = $(this).find('td').first();
              var mel = $(this).find('td').first();
              for(var i = 0; i < scol ; i++) {
                sel = $(sel).next();
              }
              for(var i = 0; i < mcol ; i++) {
                mel = $(mel).next();
              }
              var preis = $(sel).text();
              var menge = $(mel).text();
              if(preis.indexOf('.') < 0)
              {
                preis = parseFloat(preis.replace(',','.'));
              }else{
                if(preis.indexOf(',') < 0)
                {
                  preis = parseFloat(preis);
                  
                }else{
                  preis = preis.replace('.','');
                  preis = parseFloat(preis.replace(',','.'));
                }
              }
              if(menge.indexOf('.') < 0)
              {
                menge = parseFloat(menge.replace(',','.'));
              }else{
                if(menge.indexOf(',') < 0)
                {
                  menge = parseFloat(menge);
                  
                }else{
                  menge = menge.replace('.','');
                  menge = parseFloat(menge.replace(',','.'));
                }
              }
              if(typeof sel[0].id != 'undefined' && !isNaN(preis))
              {
                if(preisestr != '') {
                  preisestr += ';';
                }
                preisestr += sel[0].id+':'+preis;
                if(mengestr != '') {
                  mengestr += ';';
                }
                mengestr += mel[0].id+':'+menge;
              }
            });
            
            ".'$'.".ajax({
                url: 'index.php?module=".$module."&action=positionen&cmd=getpreise&id=".$this->app->Secure->GetGET('id')."',
                type: 'POST',
                dataType: 'json',
                data: { 
                  scol:$summencol,
                  mcol:$mengencol,
                  preise:preisestr,
                  quantities:mengestr
                },
                success: function(data) {
                  if(data != null)
                  {
                    var datalen = data.length;
                    for(var ind = 0; ind < datalen; ind++)
                    {
                      var eltmp = data[ind];
                      if(typeof eltmp.elid != 'undefined' && typeof eltmp.value != 'undefined') {
                        $('#'+eltmp.elid).text(eltmp.value);
                      }
                    }
                    aktuallisierePreise();
                  }
                }
            });
          }
          var mcol = $mengencol;
          var rcol = ".(int)$rabattcol.";
          var sgruppensumme = 0;
          var szwischensumme = 0;
          ";
        $js .=  "
          var first = true;
          ".'$'."('#zwischensumme').parents('table').first().find('tr').each(function(){
            if(!first)
            {
              var sel = $(this).find('td').first();
              for(var i = 0; i < scol ; i++)sel = $(sel).next();
              var quantityElement = $(this).find('td').first();
              for(var i = 0; i < mcol ; i++) {
                quantityElement = $(quantityElement).next();
              }
              var rabatt = 0;
              if(rcol > 0)
              {
                var rel = $(this).find('td').first();
                for(var i = 0; i < rcol ; i++)rel = $(rel).next();
                rabatt = $(rel).text();
                if(rabatt.indexOf('.') < 0)
                {
                  rabatt = parseFloat(rabatt.replace(',','.'));
                }else{
                  if(rabatt.indexOf(',' < 0))
                  {
                    rabatt = parseFloat(rabatt);
                    
                  }else{
                    rabatt = rabatt.replace('.','');
                    rabatt = parseFloat(rabatt.replace(',','.'));
                  }
                }
              }
              var menge = $(quantityElement).text();
              if(menge.indexOf('.') < 0)
              {
                menge = parseFloat(menge.replace(',','.'));
              }else{
                if(menge.indexOf(',') < 0)
                {
                  menge = parseFloat(menge);
                  
                }else{
                  menge = menge.replace('.','');
                  menge = parseFloat(menge.replace(',','.'));
                }
              }
              
              var preis = $(sel).text();
              if(preis.indexOf('.') < 0)
              {
                preis = parseFloat(preis.replace(',','.'));
              }else{
                if(preis.indexOf(',') < 0)
                {
                  preis = parseFloat(preis);
                  
                }else{
                  preis = preis.replace('.','');
                  preis = parseFloat(preis.replace(',','.'));
                }
              }
              
              if(menge > 0 && preis != 0)
              {
                if(rabatt > 0)
                {
                  summe = summe + ((menge * preis)*((100-rabatt) / 100.0));
                  szwischensumme = szwischensumme + ((menge * preis)*((100-rabatt) / 100.0));
                  sgruppensumme = sgruppensumme + ((menge * preis)*((100-rabatt) / 100.0));
                }else{
                  summe = summe + menge * preis;
                  szwischensumme = szwischensumme + menge * preis;
                  sgruppensumme = sgruppensumme + menge * preis;
                }
              }
              if(".'$'."(this).hasClass('gruppensumme'))
              {
                ".'$'."(this).find('span.cgruppensumme').each(function(){".'$'."(this).html(ZahlFormatieren(sgruppensumme))});
                sgruppensumme = 0;
              }
              if(".'$'."(this).hasClass('gruppensummemitoptionalenpreisen'))
              {
                ".'$'."(this).find('span.cgruppensummemitoptionalenpreisen').each(function(){".'$'."(this).html(ZahlFormatieren(sgruppensumme))});
                sgruppensumme = 0;
              }
              if(".'$'."(this).hasClass('zwischensumme'))
              {
                ".'$'."(this).find('span.czwischensumme').each(function(){".'$'."(this).html(ZahlFormatieren(szwischensumme))});
                //szwischensumme = 0;
              }
              if(".'$'."(this).hasClass('gruppe'))sgruppensumme = 0;
              
          ";
          
          $js .= "
              
            }
            first = false;
          });
          ";
          $js .= "
          
          
          $('#zwischensumme').html(ZahlFormatieren(summe));

          
        };
        function ZahlFormatieren(x) {
          x = parseFloat(x);
          var k = (x.toFixed(2)).toString().replace('.',',');
          var anzstellen = k.length;
          var vorzeichen = 0;           
          if(k.substring(0,1) == '-')
          {
            vorzeichen = 1;
          }
          if(anzstellen - 1 <= 6)return k;
          var vorzeichenstring = '';
          if(vorzeichen)vorzeichenstring = k.substring(0, 1);
          var vorkomma = k.substring(vorzeichen, anzstellen - 3);
          var ret = vorzeichenstring;
          var modstellen = vorkomma.length % 3;
          if(modstellen > 0)ret = ret + vorkomma.substring(0, modstellen)+'.';
          var nachkomma = k.substring(anzstellen - 3, anzstellen);
          
          var i = 0;
          for(i = 0; i < Math.floor(vorkomma.length / 3); i++)
          {
            if(i > 0)ret = ret + '.';
            ret = ret + vorkomma.substring(i*3+modstellen, (i+1)*3+modstellen);
          }
          ret = ret+nachkomma;
          return ret;
        }
        </script>";
        
      }
      $summenarray[] = '<input type="hidden" id="mcol" value="'.$mengencol.'" /><input type="hidden" id="rcol" value="'.$rabattcol.'" /><input type="hidden" id="scol" value="'.$summencol.'" /><span id="zwischensumme">'.number_format($zwischensumme,4,'.','').'</span>';
      if($module != 'verbindlichkeit')$summenarray[] = '';
      $summenarray[] = '';
      $summenarray[count($summenarray)-1] .= $js;
      if($mengencol && $summencol && $module!='produktion')$table->AddRow($summenarray);

      //$this->app->YUI->AutoComplete(ARTIKELAUTO,"artikel",array('name_de','warengruppe'),"nummer");
      
      if ($module == "bestellung") $fillArtikel = "fillArtikelBestellung";
      elseif ($module == "inventur") $fillArtikel = "fillArtikelInventur";
      elseif ($module == "lieferschein" || $module == "anfrage" || $module=="preisanfrage" || $module == "retoure") $fillArtikel = "fillArtikelLieferschein";
      elseif ($module == "produktion") $fillArtikel = "fillArtikelProduktion";
      else $fillArtikel = "fillArtikel";
      
      if ($fillArtikel == "fillArtikelBestellung") {
        $this->app->Tpl->Add($parsetarget, '<script type="text/javascript">
        var Tastencode;

        var status=1;

        var nureinmal=0;

        function selectafterblurmenge()
        {
        ' . $fillArtikel . '(document.getElementById("nummer").value,document.getElementById("menge").value);
        }


        function selectafterblur()
        {
        //  if(document.getElementById("artikel").value))
          {
            //      nureinmal=1;
            ' . $fillArtikel . '(document.getElementById("artikel").value,document.getElementById("menge").value);
          }
        }

        function TasteGedrueckt (Ereignis) {
          if (!Ereignis)
            Ereignis = window.event;
          if (Ereignis.which) {
            Tastencode = Ereignis.which;
          } else if (Ereignis.keyCode) {
            Tastencode = Ereignis.keyCode;
          }
          //if((Tastencode=="9" || Tastencode=="13") && !isNaN(document.getElementById("artikel").value) )
          if((Tastencode=="9" || Tastencode=="13"))
          {
            ' . $fillArtikel . '(document.getElementById("artikel").value,document.getElementById("menge").value);
            //document.myform.konto.focus();
            status=1;
          }
        }
        document.onkeydown = TasteGedrueckt;


        function updatehere()
        {
          //    ' . $fillArtikel . '(document.getElementById("nummer").value);

        }



        function checkhere()
        {
          //var test = document.getElementById("artikel").value;
          //if(!isNaN(test.substr(0,6)))
          // ' . $fillArtikel . '(document.getElementById("nummer").value,document.getElementById("menge").value);

          //if(!isNaN(test.substr(0,6))
          //      fillArtikel(document.getElementById("artikel").value);
          // wenn ersten 6 stellen nummer dann update
          //if(!isNaN(document.getElementById("artikel").value))
          //if(document.getElementById("artikel").value)
          //     fillArtikel(document.getElementById("artikel").value);

        }

        </script>

          ');
      } else {
        $this->app->Tpl->Add($parsetarget, '<script type="text/javascript">
        var Tastencode;

        var status=1;

        var nureinmal=0;

        function selectafterblurmenge()
        {
        ' . $fillArtikel . '(document.getElementById("nummer").value,document.getElementById("menge").value);
        }

        var oldvalue;
        function selectafterblur()
        {
          if(document.getElementById("nummer").value!="" && (nureinmal==1 || oldvalue==document.getElementById("artikel").value))
            ' . $fillArtikel . '(document.getElementById("nummer").value+ " " +document.getElementById("artikel").value,document.getElementById("menge").value);
          else
            ' . $fillArtikel . '(document.getElementById("artikel").value,document.getElementById("menge").value);

          nureinmal=1;
          if(oldvalue!=document.getElementById("artikel").value) nureinmal=0;
          oldvalue=document.getElementById("artikel").value;
        }

    function TasteGedrueckt (Ereignis) {
      if (!Ereignis)
        Ereignis = window.event;
      if (Ereignis.which) {
        Tastencode = Ereignis.which;
      } else if (Ereignis.keyCode) {
        Tastencode = Ereignis.keyCode;
      }
      if((Tastencode=="9" || Tastencode=="13") && !isNaN(document.getElementById("artikel").value) )
      {
        if(document.getElementById("nummer").value!="")
          ' . $fillArtikel . '(document.getElementById("artikel").value,document.getElementById("menge").value);
        else
          ' . $fillArtikel . '(document.getElementById("nummer").value+ " " + document.getElementById("artikel").value,document.getElementById("menge").value);
        //document.myform.konto.focus();
        status=1;
      }
    }
    document.onkeydown = TasteGedrueckt;


    function updatehere()
    {
      ' . $fillArtikel . '(document.getElementById("artikel").value);

    }

    function checkhere()
    {
      //var test = document.getElementById("artikel").value;
      //if(!isNaN(test.substr(0,6)))
      //      ' . $fillArtikel . '(document.getElementById("artikel").value,document.getElementById("menge").value);

      //if(!isNaN(test.substr(0,6))
      //      fillArtikel(document.getElementById("artikel").value);
      // wenn ersten 6 stellen nummer dann update
      //if(!isNaN(document.getElementById("artikel").value))
      //if(document.getElementById("artikel").value)
      //     fillArtikel(document.getElementById("artikel").value);

    }

    </script>

      ');
      }

      //$this->app->YUI->AutoComplete(NUMMERAUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");
      
      if ($schreibschutz != 1) {
        $tmp = '';
        $fmodul = $this->app->Secure->GetGET('fmodul');
        foreach ($menu as $key => $value) {

          // im popup öffnen
          if($key == "add"){
            $tmp .=
              "<a href=\"index.php?module=$module&action=$value&id=%value%&frame=false&pid=$id" . ($fmodul ? "&fmodul=" . $fmodul : "") . "\" 
              onclick=\"makeRequest(this);return false\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/new.png\"></a>&nbsp;";
          }
          else{
            if($key == "del"){
              $tmp .= "<a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=$module&action=$value&sid=%value%&id=$id" . ($fmodul ? "&fmodul=" . $fmodul : "") . "';\" href=\"#\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\"></a>&nbsp;";
            }
            else{
              if($key == "edit" ){
                if($module != 'goodspostingdocument'){
                  $tmp .= "<a href=\"index.php?module=$module&action=$value&id=%value%&frame=false&pid=$id" . ($fmodul ? "&fmodul=" . $fmodul : "") . "\" 
                  class=\"popup\" data-position-id=\"%value%\" title=\"Artikel &auml;ndern\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\"></a>&nbsp;";
                }
              }
              else if($key == 'copy'){
                $tmp .= "<a href=\"index.php?module=$module&action=$value&sid=%value%&id=$id" . ($fmodul ? "&fmodul=" . $fmodul : "") . "\" title=\"Artikel kopieren\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/$key.png\"></a>&nbsp;";
              }
              // nur aktion ausloesen und liste neu anzeigen
              else{
                $tmp .= "<a href=\"index.php?module=$module&action=$value&sid=%value%&id=$id" . ($fmodul ? "&fmodul=" . $fmodul : "") . "\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/$key.png\"></a>&nbsp;";
              }
            }
          }
        }
        $table->DisplayEditable($parsetarget, $tmp,"","","","",false);
      } else {
        $table->DisplayNew($parsetarget, $tmp,"","","","",false);
      }
      $this->app->erp->RunHook('yui_sortlistadd_ende', 1,$parsetarget);
    }
    
    function SortList($parsetarget, &$ref, $menu, $sql, $sort = true) {

      $module = $this->app->Secure->GetGET("module");
      $fmodul = $this->app->Secure->GetGET("fmodul");
      $id = $this->app->Secure->GetGET("id");
      $table = new EasyTable($this->app);
      
      if ($sort) $table->Query($sql . " ORDER by sort");
      else $table->Query($sql);

      foreach ($menu as $key => $value) {

        // im popup öffnen
        
        if ($key == "add") $tmp.= "<a href=\"index.php?module=$module&action=$value&id=%value%&frame=false&pid=$id".($fmodul?"&fmodul=".$fmodul:"")."\" 
        onclick=\"makeRequest(this);return false\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/new.png\"></a>&nbsp;";
        else 
        if ($key == "del") $tmp.= "<a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=$module&action=$value&sid=%value%&id=$id".($fmodul?"&fmodul=".$fmodul:"")."';\" href=\"#\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\"></a>&nbsp;";
        else 
        if ($key == "edit") $tmp.= "<a href=\"index.php?module=$module&action=$value&id=%value%&frame=false&pid=$id".($fmodul?"&fmodul=".$fmodul:"")."\" class=\"popup\" title=\"Artikel &auml;ndern\">
        <img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/$key.png\" /></a>&nbsp;";
        else
        if ($key == "copy") $tmp.= "<a href=\"index.php?module=$module&action=$value&id=%value%&frame=false&pid=$id".($fmodul?"&fmodul=".$fmodul:"")."\"  title=\"Artikel kopieren\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/$key.png\" /></a>"; 
        // nur aktion ausloesen und liste neu anzeigen
        else $tmp.= "<a href=\"index.php?module=$module&action=$value&sid=%value%&id=$id".($fmodul?"&fmodul=".$fmodul:"")."\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/$key.png\"></a>&nbsp;";
      }
      $table->DisplayNew($parsetarget, $tmp);
    }
    
    function SortListEvent($event, $table, $fremdschluesselindex, $id = null, $sid = null) {

      if(is_null($sid))$sid = $this->app->Secure->GetGET("sid","sid");
      if(is_null($id))$id = $this->app->Secure->GetGET("id");
      $sort = $this->app->DB->Select("SELECT sort FROM $table WHERE id='$sid' LIMIT 1");
      $this->app->erp->RunHook('yui_sortlistevent',4, $event, $table, $id, $sid);
      if ($event == "up") {
        $zwischenposition = $this->app->DB->SelectArr("SELECT id, pos, sort FROM `beleg_zwischenpositionen` WHERE doctype = '$fremdschluesselindex' AND doctypeid = '$id' AND pos = '$sort' ORDER BY sort LIMIT 1");
        if($zwischenposition)
        {
          $zwischenvor = $this->app->DB->SelectArr("SELECT id, pos, sort FROM `beleg_zwischenpositionen` WHERE doctype = '$fremdschluesselindex' AND doctypeid = '$id' AND pos = '".($sort - 1)."' ORDER BY sort DESC LIMIT 1");
          if($zwischenvor)
          {
            $this->app->DB->Update("UPDATE `beleg_zwischenpositionen` SET pos = pos - 1, sort = '".($zwischenvor[0]['sort'] + 1)."' WHERE id = '".$zwischenposition[0]['id']."' LIMIT 1");
          }else{
            $this->app->DB->Update("UPDATE `beleg_zwischenpositionen` SET pos = pos - 1, sort = '0' WHERE id = '".$zwischenposition[0]['id']."' LIMIT 1");
          }
        }else{
          //gibt es ein element an hoeherer stelle?
          $nextsort = $this->app->DB->Select("SELECT sort FROM $table WHERE $fremdschluesselindex='$id' AND sort ='" . ($sort + 1) . "' LIMIT 1");

          if ($nextsort > $sort) {
            $nextid = $this->app->DB->Select("SELECT id FROM $table WHERE $fremdschluesselindex='$id' AND sort = '" . ($sort + 1) . "' LIMIT 1");
            $this->app->DB->Update("UPDATE $table SET sort='$nextsort' WHERE id='$sid' LIMIT 1");
            $this->app->DB->Update("UPDATE $table SET sort='$sort' WHERE id='$nextid' LIMIT 1");
          } else {

            // element ist bereits an oberster stelle

          }
        }
        $this->app->Location->execute('index.php?module='.$fremdschluesselindex.'&action=positionen&id='.$id);
      } else 
      if ($event == "down") {
        $zwischenposition = $this->app->DB->SelectArr("SELECT id, pos, sort FROM `beleg_zwischenpositionen` WHERE doctype = '$fremdschluesselindex' AND doctypeid = '$id' AND pos = '".($sort-1)."' ORDER BY sort DESC LIMIT 1");
        if($zwischenposition)
        {
          $zwischennach = $this->app->DB->SelectArr("SELECT id, pos, sort FROM `beleg_zwischenpositionen` WHERE doctype = '$fremdschluesselindex' AND doctypeid = '$id' AND pos = '$sort' ORDER BY sort LIMIT 1");
          if($zwischennach)
          {
            $this->app->DB->Update("UPDATE `beleg_zwischenpositionen` SET sort = sort + 1 WHERE doctype = '$fremdschluesselindex' AND doctypeid = '$id' AND pos = '$sort'");
            $this->app->DB->Update("UPDATE `beleg_zwischenpositionen` SET sort = 0, pos = '$sort' WHERE id = '".$zwischenposition[0]['id']."' LIMIT 1");
          }else{
            $this->app->DB->Update("UPDATE `beleg_zwischenpositionen` SET sort = 0, pos = '$sort' WHERE id = '".$zwischenposition[0]['id']."' LIMIT 1");
          }
        }else{
          //gibt es ein element an hoeherer stelle?
          $prevsort = $this->app->DB->Select("SELECT sort FROM $table WHERE $fremdschluesselindex='$id' AND sort = '" . ($sort - 1) . "' LIMIT 1");

          if ($prevsort < $sort && $prevsort != 0) {
            $previd = $this->app->DB->Select("SELECT id FROM $table WHERE $fremdschluesselindex='$id' AND sort = '" . ($sort - 1) . "' LIMIT 1");
            $this->app->DB->Update("UPDATE $table SET sort='$prevsort' WHERE id='$sid' LIMIT 1");
            $this->app->DB->Update("UPDATE $table SET sort='$sort' WHERE id='$previd' LIMIT 1");
          } else {

            // element ist bereits an oberster stelle

          }
        }
        $this->app->Location->execute('index.php?module='.$fremdschluesselindex.'&action=positionen&id='.$id);
      } else 
      if ($event == "del") {
        if(strpos($sid, 'z') !== false || strpos($sid, 'b') !== false)
        {
          $sida = explode(',',$sid);
          foreach($sida as $v)
          {
            if ($v[0] == 'b')
            {
              $v = substr($v ,1);
              $this->SortListEvent($event, $table, $fremdschluesselindex, $id, $v);
            }else{
              $v = substr($v ,1);
              $this->DeleteDrawItem($fremdschluesselindex, $id, $v);
            }
            
          }
        }elseif (is_numeric($sid) && $sid > 0) {
          if ($table == "auftrag_position" || $table == "produktion_position" || $table == "lieferschein_position" || $table == "angebot_position" || $table == "rechnung_position"
              || $table == "gutschein_position" || $table == "bestellung_position" || $table == "retoure") {
            
            switch ($table) {
              case "auftrag_position";
                $tmptable = "auftrag";
              break;
              case "angebot_position";
                $tmptable = "angebot";
              break;
              case "rechnung_position";
                $tmptable = "rechnung";
              break;
               case "gutschein_position";
                $tmptable = "gutschein";
              break;
              case "bestellung_position";
                $tmptable = "bestellung";
              break;
              case "lieferschein_position";
                $tmptable = "lieferschein";
              break;
              case "retoure_position";
                $tmptable = "retoure";
              break;
              case "produktion_position";
                $tmptable = "produktion";
              break;
            }
          
            // alle reservierungen fuer die eine position loeschen
            $tmpartikel = $this->app->DB->Select("SELECT artikel FROM $table WHERE id='$sid' LIMIT 1");
            $tmptable_value = $this->app->DB->Select("SELECT $tmptable FROM $table WHERE id='$sid' LIMIT 1");
            $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE artikel='$tmpartikel' AND objekt='$tmptable' AND parameter='$tmptable_value'");
            if($tmptable === 'auftrag') {
              $this->app->DB->Update(
                sprintf(
                  'UPDATE `artikel` SET `laststorage_changed` = NOW() WHERE `id` = %d',
                  $tmpartikel
                )
              );
            }
          }
          $this->app->DB->Delete("DELETE FROM $table WHERE id='$sid' LIMIT 1");
          if($tmptable==='angebot') {
            $this->app->DB->Delete("DELETE FROM $table WHERE explodiert_parent='$sid'");
          }

          $this->app->DB->Update(
            sprintf(
              "UPDATE `%s` SET sort=sort-1 WHERE id=%d AND sort > %d LIMIT 1",
              $table, (int)$sid, (int)$sort
            )
          );
          if(in_array($tmptable, array('auftrag','rechnung','gutschrift')))
          {
            $this->app->DB->Update("UPDATE $tmptable SET extsoll = 0 WHERE id = '$tmptable_value' LIMIT 1");
          }
          $beleg_zwischenpositionensort = $this->app->DB->SelectArr("SELECT id, sort FROM beleg_zwischenpositionen where doctype = '$tmptable' AND doctypeid = '$tmptable_value' AND pos = '$sort' ORDER BY sort DESC LIMIT 1");
          $offset = 0;
          if($beleg_zwischenpositionensort) {
            $offset = 1 + (int)$beleg_zwischenpositionensort[0]['sort'];
          }

          $this->app->DB->Update(
            sprintf(
              "UPDATE beleg_zwischenpositionen 
                SET pos = pos - 1, sort = sort + %d 
                WHERE doctype = '%s' AND doctypeid = %d AND pos = %d",
              $offset, $tmptable, (int)$tmptable_value, (int)$sort
            )
          );
          $this->app->DB->Update(
            sprintf(
              "UPDATE beleg_zwischenpositionen 
              SET pos = pos - 1 
              WHERE doctype = '%s' AND doctypeid = %d AND pos > %d",
              $tmptable, (int)$tmptable_value, (int)$sort
            )
          );

          if ($tmptable === 'auftrag') {
            $this->app->erp->AuftragEinzelnBerechnen($tmptable_value);
          }
        }
        
      } elseif($event === "copy") {
        $arr = $this->app->DB->SelectArr("SELECT * FROM $table WHERE id = '$sid' LIMIT 1");
        if($arr)
        {
          $nextsort = 1 + (int)$this->app->DB->Select("SELECT max(sort) FROM $table WHERE $fremdschluesselindex='$id' LIMIT 1");
          $arr[0]['sort'] = $nextsort;
          unset($arr[0]['id']);
          $this->app->DB->Insert("INSERT INTO $table ($fremdschluesselindex) VALUES ('$id')");
          $newsid = $this->app->DB->GetInsertID();
          $this->app->DB->UpdateArr($table,$newsid,'id',$arr[0], true);
          if(is_null($arr[0]['steuersatz']))
          {
            $this->app->DB->Update("UPDATE $table SET steuersatz = NULL WHERE id = '$newsid' LIMIT 1");
          }
          if($table === 'auftrag_position') {
            $this->app->DB->Update(
              sprintf(
                'UPDATE `artikel` SET `laststorage_changed` = NOW() WHERE `id` = %d',
                $arr[0]['artikel']
              )
            );
          }
          if($fremdschluesselindex)$this->app->erp->AuftragEinzelnBerechnen($id);
        }
        $this->app->Location->execute('index.php?module='.$fremdschluesselindex.'&action=positionen&id='.$id);
      }
    }

function IframeDialog($width, $height, $src = "") {

  $id = $this->app->Secure->GetGET("id");
  $sid = $this->app->Secure->GetGET("sid");
  $module = $this->app->Secure->GetGET("module");
  $action = $this->app->Secure->GetGET("action");
  
  if ($src != "") $this->app->Tpl->Set('PAGE', "<iframe name=\"framepositionen\" id=\"framepositionen\" width=\"$width\"  frameborder=\"0\" src=\"$src&iframe=true\" height=\"$height\"></iframe>");
  else $this->app->Tpl->Set('PAGE', "<iframe name=\"framepositionen\" onload=\"this.style.height=this.contentDocument.body.scrollHeight +'px';\" id=\"framepositionen\" width=\"$width\" height=\"$height\"  frameborder=\"0\" src=\"index.php?module=$module&action=$action&id=$id&sid=$sid&iframe=true\"></iframe>");
  $this->app->BuildNavigation = false;
}


  function WaehrungsumrechnungTabelle($targetbutton, $targettabelle)
  {
    $this->app->Tpl->Set($targetbutton,'<input type="button" value="W&auml;hrungumrechnungstabelle" onclick="loadintotable();" />');
    $_waehrungen = $this->app->erp->GetWaehrungUmrechnungskurseTabelle('EUR');
    $waehrungen['EUR'] = 1;
    foreach($_waehrungen as $waehrung => $kurs)$waehrungen[$waehrung] = $kurs;
    if($_waehrungen)
    {

      foreach($waehrungen as $k => $v)$waehrung_felder[$k] = $k;      
    }
    
    $htmltabelle = "
    <script>
    var kurs = new Array();
    var waehrungen = new Array();
    ";
    
    $i = -1;
    foreach($waehrungen as $waehrung => $kurs)
    {
      $i++;
      $htmltabelle .= "kurs[".$i."] = ".$kurs.";\r\n";
      $htmltabelle .= "waehrungen[".$i."] = '".$waehrung."';\r\n";
    }
    
    $htmltabelle .= "
    
    function loadintotable() {
      var waehrung = $('#waehrung').val();
      if(waehrung == '')waehrung = 'EUR';
      var preis = parseFloat($('#preis').val().replace(',','.'));
      if (isNaN(preis))preis = 0;
      var titel = 'Umrechnung von '+preis+' '+waehrung;
      $('#preistabellediv').dialog({width: 500, title:titel});
      changerunden();
    }
    
    function uebernehmen(id)
    {
      var waehrung = $('#waehrung_'+id).html();
      var preis = $('#preis_'+id).val();
      $('#preis').val(preis);
      $('#waehrung').val(waehrung);
      $('#preistabellediv').dialog('close');
    }
    
    function changerunden()
    {
      var waehrung = $('#waehrung').val();
      var preis = parseFloat($('#preis').val().replace(',','.'));
      if (isNaN(preis))preis = 0;
      var aktwaehrung = 'EUR';
      var aktind = -1;
      var aktkurs = 1;
      var stellen = parseInt($('#stellen').val());
      if (isNaN(stellen))
      {
        stellen = 0;
      }
      if(stellen < 0)stellen = 0;
      var isstellen = $('#runden').prop('checked');
      $.each(waehrungen, function(k,v){
        if(waehrung == v)
        {
          aktwaehrung = waehrung;
        }
      });
      $.each(waehrungen, function(k,v){
        if(aktwaehrung == v)
        {
          aktind = k;
          aktkurs = kurs[aktind];
          $('#tr_'+k).css('display','none');
        } else {
          $('#tr_'+k).css('display','');
        }
      });
      
      $.each(waehrungen, function(k,v){
        if(aktwaehrung == v)
        {

        } else {
          $('#kurs_'+k).html(kurs[k]/aktkurs);
          var neuerpreis = kurs[k]/aktkurs*preis;
          if(isstellen)neuerpreis = Math.round(neuerpreis*Math.pow(10,stellen), stellen)/Math.pow(10,stellen);
          $('#preis_'+k).val( neuerpreis);
        }
      });
      
      
    }
    
    </script>
    <div id=\"preistabellediv\" style=\"display:none;\"><div id=\"preiserror\">".(count($waehrungen) > 1?"":"Bitte legen Sie erst W&auml;hrungen an!")."</div>
    
    ";
    $i = -1;
    if(count($waehrungen) > 1)
    {
      $htmltabelle .= "<table><tr><th>W&auml;hrung</th><th>Kurs</th><th>umgerechnter Preis</th><th>Aktion</th></tr>";
      foreach($waehrungen as $waehrung => $kurs)
      {
        $i++;
        $htmltabelle .= "<tr id=\"tr_".$i."\"><td><span id=\"waehrung_".$i."\">".$waehrung."</span></td><td><span id=\"kurs_".$i."\">".$kurs."</span></td><td><input type=\"text\" id=\"preis_".$i."\" value=\"\" /></td><td><input type=\"button\" value=\"&uuml;bernehmen\" onclick=\"uebernehmen(".$i.")\" /></td></tr>";
      }
      $htmltabelle .= "<tr><td></td><td><input type=\"checkbox\" id=\"runden\" onchange=\"changerunden();\" />Runden auf </td><td><input onchange=\"changerunden();\" type=\"text\" id=\"stellen\" value=\"2\" /></td><td> Stellen</td></tr></table>";
    }
    
    $htmltabelle .= "</div>";
    
    
    $this->app->Tpl->Set($targettabelle, $htmltabelle);
  }


  public function ConvertLatin1UTF($field)
  {
    return 'convert(cast(convert('.$field.' using  latin1) as binary) using utf8)';
    //return $field.' COLLATE utf8_general_ci'; ersetzt Original
  }
}
