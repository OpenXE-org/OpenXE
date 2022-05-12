<?php
include '_gen/widget.gen.shopexport.php';

class WidgetShopexport extends WidgetGenShopexport 
{
  /**
   * @var Application $app
   */
  private $app;

  /**
   * WidgetShopexport constructor.
   *
   * @param Application $app
   * @param string      $parsetarget
   */
  function __construct($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::__construct($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
    //firma
    $id = (int)$this->app->Secure->GetGET('id');
    $this->app->Tpl->Set('NURPREISESTYLE',' style="display:none;" ');
    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
    $this->form->NewField($field);

    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->app->YUI->AutoComplete("artikelrabattsteuer","steuersatz",1);
    $this->form->ReplaceFunction("artikelrabattsteuer",$this,"ReplaceSteuersatz");
    $this->app->YUI->AutoComplete("artikelporto","artikelnummer",1);
    $this->form->ReplaceFunction("artikelporto",$this,"ReplaceArtikel");
    $this->app->YUI->AutoComplete("preisgruppe","preisgruppekennziffer");
    $this->form->ReplaceFunction("preisgruppe",$this,"ReplacePreisgruppe");
    $this->app->YUI->AutoComplete("artikelportoermaessigt","artikelnummer",1);
    $this->form->ReplaceFunction("artikelportoermaessigt",$this,"ReplaceArtikel");
    $this->app->YUI->AutoComplete("artikelrabatt","artikelnummer",1);
    $this->form->ReplaceFunction("artikelrabatt",$this,"ReplaceArtikel");
    $this->app->YUI->AutoComplete('vertrieb','adressemitvertrieb');
    $this->form->ReplaceFunction('vertrieb',$this,'ReplaceVertrieb');

    $this->app->YUI->AutoComplete("artikelnachnahme","artikelnummer",1);
    $this->form->ReplaceFunction("artikelnachnahme",$this,"ReplaceArtikel");
    
    $this->app->YUI->DatePicker('vondatum');
    $this->app->YUI->TimePicker('vonzeit');

    $this->app->YUI->DatePicker('startdate');
    $this->form->ReplaceFunction('startdate',$this,'ReplaceDatum');

    if($id > 0 && $this->app->Secure->GetPOST('resetarchiv')) {
      $this->app->DB->Update("UPDATE shopexport_archiv SET status = 'abgebrochen' WHERE shop = '$id' LIMIT 1");
      $this->app->Tpl->Add('MESSAGE','<div class="info">{|Alte Auftr&auml;ge abholen abgebrochen.|}</div>');
    }
    elseif($id > 0 && $this->app->Secure->GetPOST('resetgetarticlelist')) {
      $this->app->DB->Delete("DELETE FROM shopexport_getarticles WHERE shop = '$id'");
      $this->app->Tpl->Add('MESSAGE','<div class="info">{|Artikel abholen abgebrochen.|}</div>');
    }
    elseif($id > 0 && $this->app->Secure->GetPOST('getarticlelist')) {
      if($this->app->DB->Select(sprintf("SELECT id FROM prozessstarter WHERE parameter = 'onlineshops_tasks' AND aktiv = 1 LIMIT 1"))){


        $command = 'getarticlelist';
        $task = $this->app->DB->SelectRow('SELECT * FROM onlineshops_tasks WHERE shop_id=\'' . $id . '\' AND command=\'' . $command . '\' LIMIT 1');
        if(!empty($task['id'])){
          if($task['status'] !== 'running'){
            $this->app->DB->Update('UPDATE onlineshop_tasks SET status=\'inactive\',counter=0 WHERE id=' . $task['id']);
          }
        }else{
          $this->app->DB->Insert('INSERT INTO onlineshops_tasks (shop_id, command) VALUES (' . $id . ',\'' . $command . '\')');
        }

        $status = $this->app->DB->Select("SELECT status FROM onlineshops_tasks WHERE shop_id=$id AND command='$command'");

        if($status === 'running'){
          $this->app->Tpl->Add('MESSAGE', '<div class="info">{|Anzahl abzuholender Artikel wird ermittelt|}.</div>');
        }else{
          $this->app->Tpl->Add('MESSAGE', '<div class="info">{|Aufgabe wurde zu Shoptasks hinzugefügt und wird im Hintergrund ausgeführt|}.</div>');
        }
      }
      else {
        $ret = $this->app->remote->RemoteCommand($id,'getarticlelist');
        if($ret) {
          $anz = 0;
          if(isset($ret['errors']) && $ret['errors']) {
            if(is_array($ret['errors'])) {
              $this->app->Tpl->Add('MESSAGE', '<div class="error">'.implode('<br />',$ret['errors']).'</div>');
            }
            else {
              $this->app->Tpl->Add('MESSAGE', '<div class="error">'.$ret['errors'].'</div>');
            }
          }
          else{
            foreach($ret as $v) {
              $anz++;
              $this->app->DB->Insert("INSERT INTO shopexport_getarticles (shop, nummer) VALUES ('$id', '".$this->app->DB->real_escape_string($v)."')");
            }
            if(!$this->app->DB->Select("SELECT id FROM `prozessstarter` WHERE aktiv = 1 AND parameter = 'getarticles' LIMIT 1")) {
              $this->app->DB->Update("UPDATE `prozessstarter` SET aktiv = 1 WHERE parameter = 'getarticles' LIMIT 1");
            }
            $this->app->DB->Update("UPDATE `prozessstarter` SET letzteausfuerhung = DATE_SUB(now(), INTERVAL periode MINUTE) WHERE aktiv = 1 AND parameter = 'getarticles' AND mutex = 0 LIMIT 1");
            $this->app->Tpl->Add('MESSAGE', '<div class="info">'.$anz.' {|Artikel gefunden. Diese werden im Hintergrund importiert.|}</div>');
          }
          $this->app->erp->SetKonfigurationValue('artikelimportanzahl_'.$id, $anz);
          unset($ret);
        }
      }

      //  $this->app->erp->SetKonfigurationValue('artikelimportanzahl_'.$id, $anz);


      /*
      $ret = $this->app->remote->RemoteCommand($id,"getarticlelist");
      if($ret)
      {
        $anz = 0;
        if(isset($ret['errors']) && $ret['errors'])
        {
          if(is_array($ret['errors']))
          {
            $this->app->Tpl->Add('MESSAGE', '<div class="error">'.implode('<br />',$ret['errors']).'</div>');
          }else
          {
            $this->app->Tpl->Add('MESSAGE', '<div class="error">'.$ret['errors'].'</div>');
          }
        }else{
          foreach($ret as $v)
          {
            $anz++;
            $this->app->DB->Insert("INSERT INTO shopexport_getarticles (shop, nummer) VALUES ('$id', '".$this->app->DB->real_escape_string($v)."')");
          }
          if(!$this->app->DB->Select("SELECT id FROM `prozessstarter` WHERE aktiv = 1 AND parameter = 'getarticles' LIMIT 1"))$this->app->DB->Update("UPDATE `prozessstarter` SET aktiv = 1 WHERE parameter = 'getarticles' LIMIT 1");
          $this->app->DB->Update("UPDATE `prozessstarter` SET letzteausfuerhung = DATE_SUB(now(), INTERVAL periode MINUTE) WHERE aktiv = 1 AND parameter = 'getarticles' AND mutex = 0 LIMIT 1");
          $this->app->Tpl->Add('MESSAGE', '<div class="info">'.$anz.' {|Artikel gefunden. Diese werden im Hintergrund importiert.|}</div>');
        }
        $this->app->erp->SetKonfigurationValue('artikelimportanzahl_'.$id, $anz);
        unset($ret);
      }
      */

    }

    if($this->app->Secure->GetGET('action') === 'create') {
      $field = new HTMLInput("versandartenmapping","checkbox",1,'','','','','checked');
      $this->form->NewField($field);
      $field = new HTMLInput("zahlungsweisenmapping","checkbox",1,'','','','','checked');
      $this->form->NewField($field);
      $field = new HTMLInput("artikeltexteuebernehmen","checkbox",1,'','','','','checked');
      $this->form->NewField($field);
      $field = new HTMLInput("artikelimport","checkbox",1,'','','','','checked');
      $this->form->NewField($field);
    }else{      
      if($this->app->Secure->GetPOST('speichern'))
      {
        $this->app->erp->StartChangeLog('shopexport');
        $altedaten = $this->app->DB->SelectArr("SELECT * FROM shopexport WHERE aktiv = 0 AND (shoptyp = 'intern' OR shoptyp = 'custom') AND modulename <> '' AND id = '$id' LIMIT 1");
        $altedaten2 = $this->app->DB->SelectArr("SELECT * FROM shopexport WHERE id = '$id' LIMIT 1");
        if(true || $this->form->CallbackAndMandatorycheck(true))
        {
          $data = $this->app->DB->SelectArr("SELECT * FROM shopexport WHERE id = '$id' LIMIT 1");
          if($data)$data = reset($data);
          /*$bezeichnung = $this->app->Secure->GetPOST('frm_bezeichnung');
          //$this->app->DB->Update("UPDATE shopexport SET bezeichnung = '$bezeichnung' WHERE id = '$id' LIMIT 1");
          $projekt = (String)reset(explode(' ',$this->app->Secure->GetPOST('frm_projekt')));
          if($projekt !== '')
          {
            $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung <> '' AND abkuerzung = '".$projekt."' ORDER BY geloescht LIMIT 1");
          }
          $this->app->DB->Update("UPDATE shopexport SET projekt = '$projekt' WHERE id = '$id' LIMIT 1");*/
          if($this->app->Secure->GetPOST('modulename') != $altedaten2[0]['modulename'])
          {
            $modulename = $this->app->Secure->GetPOST('modulename');
            if($modulename == '')
            {
              $this->app->DB->Update("UPDATE shopexport SET shoptyp = 'extern' WHERE id = '$id' LIMIT 1");
              $data['shoptyp'] = 'extern';
            }else
            {
              $found = false;
              $obj = $this->app->erp->LoadModul('onlineshops');
              $modulesel = array(''=>'Extern');
              if($obj)
              {
                $module = $obj->getApps();
                if(isset($module['installiert']))
                {
                  foreach($module['installiert'] as $k => $v)
                  {
                    if(strpos($v['key'],'shopimporter_') !== 0)continue;
                    if($v['key'] == $modulename)
                    {
                      $this->app->DB->Update("UPDATE shopexport SET shoptyp = 'intern' WHERE id = '$id' LIMIT 1");
                      $data['shoptyp'] = 'intern';
                      $found = true;
                      break;
                    }
                  }
                }
              }
            }
          }
          $einzelnabholen = $this->app->Secure->GetPOST('einzelnabholen');
          $inwarteschlange = $this->app->Secure->GetPOST('inwarteschlange');
          $modus = $this->app->Secure->GetPOST('modus');
          $abholmodus = $this->app->Secure->GetPOST('abholmodus');
          $vondatum = (String)$this->app->Secure->GetPOST('vondatum');
          $vonzeit = (String)$this->app->Secure->GetPOST('vonzeit');
          $this->form->HTMLList['datumvon']->htmlvalue = '0000-00-00 00:00:00';
          $this->form->HTMLList['datumvon']->dbvalue = '0000-00-00 00:00:00';
          if($vondatum !== '')
          {
            $this->form->HTMLList['datumvon']->htmlvalue = $this->app->String->Convert($vondatum, '%1.%2.%3', '%3-%2-%1');
            if($vonzeit !== '')$this->form->HTMLList['datumvon']->htmlvalue = $this->form->HTMLList['datumvon']->htmlvalue.' '.$vonzeit.':00';
            $this->form->HTMLList['datumvon']->dbvalue = $this->form->HTMLList['datumvon']->htmlvalue;
          }
          
          if($abholmodus !== 'zeitbereich' && $this->app->Secure->GetPOST('maxmanuell') == 1)
          {
            $this->form->HTMLList['einzelsync']->dbvalue = 1;
            $this->form->HTMLList['einzelsync']->htmlvalue = 1;
          }

          /*switch($abholmodus)
          {
            case 'ab_nummer':
              $this->app->DB->Update("UPDATE shopexport SET holealle = 1, anzgleichzeitig = 0 WHERE id = '$id' LIMIT 1");
            break;
            case 'zeitbereich':
              $this->app->DB->Update("UPDATE shopexport SET anzgleichzeitig = 50 WHERE id = '$id' AND anzgleichzeitig < 2 LIMIT 1");
            break;
            default:
              $this->app->DB->Update("UPDATE shopexport SET holealle = 0, anzgleichzeitig = 0 WHERE id = '$id' LIMIT 1");
            break;
          }*/
          
          $aktivhaken = $this->app->Secure->GetPOST('aktivhaken');
          $frmarr = array('ab_nummer','holeallestati','datumvon','datumbis','nummersyncstatusaendern');
          //foreach($frmarr as $v)$this->app->DB->Update("UPDATE shopexport SET $v = '".$this->app->Secure->GetPOST($v)."' WHERE id = '$id' LIMIT 1");
          switch($modus)
          {
            case 'demomodus':
              //$this->app->DB->Update("UPDATE shopexport SET demomodus = 1 WHERE id = '$id' LIMIT 1");
              //$this->app->DB->Update("UPDATE shopexport SET cronjobaktiv = 0 WHERE id = '$id' LIMIT 1");
            break;
            case 'manuell':
              //$this->app->DB->Update("UPDATE shopexport SET demomodus = 0 WHERE id = '$id' LIMIT 1");
              //$this->app->DB->Update("UPDATE shopexport SET cronjobaktiv = 0 WHERE id = '$id' LIMIT 1");
            break;
            case 'automatisch':
              //$this->app->DB->Update("UPDATE shopexport SET demomodus = 0 WHERE id = '$id' LIMIT 1");
              //$this->app->DB->Update("UPDATE shopexport SET cronjobaktiv = 1 WHERE id = '$id' LIMIT 1");
              if(!$this->app->DB->Select("SELECT id FROM  `prozessstarter` WHERE parameter = 'shopimport' AND aktiv = 1"))
              {
                if($this->app->DB->Select("SELECT id FROM  `prozessstarter` WHERE parameter = 'shopimport' AND aktiv = 0"))
                {
                  $this->app->DB->Update("UPDATE `prozessstarter` SET aktiv = 1 WHERE parameter = 'shopimport' LIMIT 1");
                }else{
                  $this->app->erp->CheckProzessstarter("Shopimporter","periodisch","15","2017-01-01 00:00:00","cronjob","shopimport",1);  
                }
              }
            break;          
          }
          /*
          if($einzelnabholen)
          {
            $this->app->DB->Update("UPDATE shopexport SET einzelsync = 1 WHERE id = '$id' LIMIT 1");
          }else{
            $this->app->DB->Update("UPDATE shopexport SET einzelsync = 0 WHERE id = '$id' LIMIT 1");
          }
          if($inwarteschlange)
          {
            $this->app->DB->Update("UPDATE shopexport SET direktimport = 0 WHERE id = '$id' LIMIT 1");
          }else{
            $this->app->DB->Update("UPDATE shopexport SET direktimport = 1 WHERE id = '$id' LIMIT 1");
          }*/
          if($data['shoptyp'] !== 'custom')
          {
            $this->app->DB->Update("UPDATE shopexport SET aktiv = '$aktivhaken' WHERE id = '$id' LIMIT 1");
          }else{
            $this->app->DB->Update("UPDATE shopexport SET aktiv = '0' WHERE id = '$id' LIMIT 1");
            $data['modulename'] = trim($data['modulename'],'.');
            if($data['modulename'] !== '')
            {
              $file = __DIR__ .'/../plugins/external/shopimporter/'.$data['modulename'];
              if(is_file($file))
              {
                include_once($file);
                $this->app->DB->Update("UPDATE shopexport SET aktiv = '$aktivhaken' WHERE id = '$id' LIMIT 1");
              }
            }
          }
          $err = null;
          if($altedaten && $this->app->Secure->GetPOST('aktiv') && method_exists($this->app->erp, 'OnlineshopsLizenzFehler'))
          {
            if($err = $this->app->erp->OnlineshopsLizenzFehler($data['modulename']))
            {
              $this->app->DB->Update("UPDATE shopexport SET aktiv = '0' WHERE id = '$id' LIMIT 1");
              $this->form->HTMLList['aktiv']->dbvalue = 0;
              $this->form->HTMLList['aktiv']->htmlvalue = 0;
              $this->app->User->SetParameter('shopexport_meldung', '<div class="error">'.$err['Error'].'</div>');
            }
          }
          $this->app->erp->RunHook('shopexport_speichern',1, $id);
        }
      }else
      {
        $meldung = $this->app->User->GetParameter('shopexport_meldung');
        if($meldung <> '')
        {
          $this->app->User->SetParameter('shopexport_meldung','');
          $this->app->Tpl->Set('MESSAGE', $meldung);
        }
      }

      $this->app->erp->RunHook('shopexport_widget', 1, $id);

      $this->app->Tpl->Set('ID', $id);
      $data = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM shopexport WHERE id = %d LIMIT 1', $id
        )
      );
      if($data['aktiv'])
      {
        //$this->app->Tpl->Set('AKTIVHAKEN', ' checked="checked" ');
      }else{
        
      }
      if($data['projekt'])
      {
        //$this->app->Tpl->Set('FRM_PROJEKT', $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '".$data['projekt']."' LIMIT 1"));
      }
      //$this->app->YUI->AutoComplete('projekt',"projektname", 1);
      //$this->app->Tpl->Set('FRM_BEZEICHNUNG', $data['bezeichnung']);
      if($data['demomodus'])
      {
        $status = 'demomodus';
      }elseif($data['cronjobaktiv'])
      {
        $status = 'automatisch';
      }else{
        $status = 'manuell';
      }
      
      if($data['anzgleichzeitig'] > 1)
      {
        $abbholstatus = 'zeitbereich';
      }elseif($data['holealle']){
        $abbholstatus = 'ab_nummer';
      }else{
        $abbholstatus = 'status';
      }
      if($data['datumvon'] !== '0000-00-00 00:00:00')
      {
        $vondatum = $this->app->String->Convert($data['datumvon'],'%1-%2-%3 %4:%5:%6', '%3.%2.%1');
        $vonzeit = $this->app->String->Convert($data['datumvon'],'%1-%2-%3 %4:%5:%6', '%4:%5');
      }else{
        $vonzeit = '';
        $vondatum = '';
      }
      $field = new HTMLInput('vonzeit','text',$vonzeit,'10','','','','');
      $this->form->NewField($field);
      $field = new HTMLInput('vondatum','text',$vondatum,'12','','','','');
      $this->form->NewField($field);
      $this->app->YUI->HideFormular('abholmodus',array('status'=>'ab_nummer zeitraum','ab_nummer'=>'zeitraum','zeitbereich'=>'ab_nummer manuellebegrenzung'));
      //$this->app->YUI->HideFormular('abholmodus',array('status'=>'dummy','zeitbereich'=>'manuellebegrenzung'));
      
      //$this->app->Tpl->Set('CBINWARTESCHLANGE', '<input type="checkbox" name="inwarteschlange" value="1" [INWARTESCHLANGE] />');
      
      //if($data['holeallestati'])$this->app->Tpl->Set('HOLEALLESTATI',' checked="checked" ');
      //if($data['nummersyncstatusaendern'])$this->app->Tpl->Set('NUMMERSYNCSTATUSAENDERN',' checked="checked" ');
      $this->app->Tpl->Set('AB_NUMMER', $data['ab_nummer']);
      $this->app->Tpl->Set('DATUMVON', $data['datumvon']);
      $this->app->Tpl->Set('DATUMBIS', $data['datumbis']);
      $this->app->Tpl->Add('AKTIONBUTTONS','<tr><td><a href="index.php?module=shopimport&action=import"><input type="button" class="aktionbutton btnzwischentabelle" value="{|Shopimport Zwischentabelle|}"></a></td></tr>');
      $this->app->YUI->HideFormular('modulename', [''=>'trstartderschnittstelle','shopimporter_amazon' => 'dummy','shopimporter_shopify'=>'dummy']);
      $this->app->YUI->HideFormular('ueberschreibe_lagerkorrekturwert',['checked'=>'dummy','unchecked'=>'lagerkorrektur']);
      $tab = 10;
      switch($data['shoptyp'])
      {
        case 'intern':
          $this->app->Tpl->Set('SHOPTYP', 'Intern');
        break;
        case 'custom':
          $this->app->Tpl->Set('SHOPTYP', 'Custom');
          $data['modulename'] = trim($data['modulename'],'.');
          $this->app->Tpl->Set('DATEINAME',$data['modulename']);
          $file = __DIR__ .'/../plugins/external/shopimporter/'.$data['modulename'];
          if(is_file($file))$this->app->Tpl->Set('CUSTOMDATEI', htmlspecialchars(file_get_contents($file)));
          $this->app->Tpl->Add('HOOKLITABS','<li><a href="#tabs-9">Custom</a></li>');
          
          $this->app->Tpl->Parse('HOOKTABS','shopexport_datei.tpl');
          $tab++;
          
        break;
        default:
          $this->app->Tpl->Set('SHOPTYP', 'Extern');
        break;
      }
      
      $struktur = null;
      if($data['shoptyp'] === 'intern' || ($data['shoptyp'] === 'custom' && $data['aktiv']))
      {
        if($data['modulename'] !== '')
        {
          $obj = null;
          if($data['shoptyp'] === 'intern')
          {
            $obj = $this->app->erp->LoadModul($data['modulename']);
          }else{
            $file = __DIR__ .'/../plugins/external/shopimporter/'.$data['modulename'];
            if(is_file($file))
            {
              include_once($file);
              $classa = explode('_',str_replace('_'.$id.'.php','', $data['modulename']));
              foreach($classa as $k => $v)$classa[$k] = ucfirst($v);
              $class = implode('_', $classa);
              $obj = new $class($this->app, true);
            }
          }
          if($obj)
          {
            $this->ShowExtraeinstellungen($id, $obj, 'EXTRAEINSTELLUNGEN', $struktur);
            $this->app->erp->RunHook('shopexport_show', 3, $id, $obj, $tab);
            $shopCapabilities = ShopimporterBase::shopCapabilities();
            if(
              !empty($shopCapabilities[$data['modulename']])
              && !empty($shopCapabilities[$data['modulename']]['kategoriebaum'])
              && $shopCapabilities[$data['modulename']]['kategoriebaum']['updatearticle'] === ShopimporterBase::CAN
            ) {
              $this->showCategoryTree($id, $tab);
            }
          }
          
        }else{
          $this->app->Tpl->Set('VOREXTRA', '<!--');
          $this->app->Tpl->Set('NACHEXTRA', '-->');
        }
      }
      elseif($data['shoptyp'] === 'custom') {
        $this->app->Tpl->Set('EXTRAEINSTELLUNGEN','<tr><td></td><td style="color:red;">F&uuml;r weitere Einstellungen den Importer aktivieren</td></tr>');
      }else{
        $this->app->Tpl->Set('VOREXTRA', '<!--');
        $this->app->Tpl->Set('NACHEXTRA', '-->');
      }
      
      $statusarr = array('demomodus'=>'{|Demo (zum Testen)|}','manuell'=>'{|Manuell (mit Importzentrale)|}','automatisch'=>'{|Automatisch (per Prozessstarter)|}');
      $abholarr = array('status'=>'{|nach Status|}','ab_nummer'=>'{|ab Nummer|}','zeitbereich'=>'{|ab Datum|}');
      
      if($struktur && isset($struktur['ausblenden']))
      {
        if(isset($struktur['ausblenden']['abholmodus']) && is_array($struktur['ausblenden']['abholmodus']))
        {
          foreach($struktur['ausblenden']['abholmodus'] as $k)unset($abholarr[$k]);
        }
      }
      $this->app->Tpl->Add('SELABHOLMODUS','<select id="abholmodus" name="abholmodus" onchange="changeabholmodus(this);">');
      foreach($abholarr as $k => $v)
      {
        $this->app->Tpl->Add('SELABHOLMODUS','<option value="'.$k.'"'.($k == $abbholstatus?' selected="selected" ':'').'>'.$v.'</option>');
      }
      $this->app->Tpl->Add('SELABHOLMODUS','</select>');
      $this->app->Tpl->Add('SELMODUS','<select id="modus" name="modus" onchange="changemodus(this);">');
      foreach($statusarr as $k => $v)
      {
        $this->app->Tpl->Add('SELMODUS','<option value="'.$k.'"'.($k == $status?' selected="selected" ':'').'>'.$v.'</option>');
      }
      $this->app->Tpl->Add('SELMODUS','</select>');
      
      $this->app->Tpl->Set('WARTESCHLANGE','<input type="checkbox" value="1" id="warteschlange" '.($data['direktimport']?'':' checked="checked" ').' onchange="changedirektimport(this);" />');
      /*if(!$data['direktimport'])
      {
        $this->app->Tpl->Set('INWARTESCHLANGE', ' checked="checked" ');
      }*/
      /*
      if($data['einzelsync'])
      {
        $this->app->Tpl->Set('EINZELNABHOLEN', ' checked="checked" ');
      }*/
      
      
      //$this->app->Tpl->Parse('TAB1', 'shopexport_detail.tpl');
      
      $json = $data['json'];
      if($json)
      {
        $json = @json_decode($json, true);
        if($json && is_array($json))
        {
          
          if(isset($json['erlaubtefunktionen']))
          {
            if(isset($json['erlaubtefunktionen']['rabattartikel']) && !$json['erlaubtefunktionen']['rabattartikel'])
            {
              $this->app->Tpl->Add('MSGARTIKELRABATT','&nbsp;<b style="color:red;">wird von diesem Importer nicht unterst&uuml;tzt</b>&nbsp;');
            }
            if(isset($json['erlaubtefunktionen']['zeitbereich']) && !$json['erlaubtefunktionen']['zeitbereich'])
            {
              $this->app->Tpl->Add('MSGANZGLEICHZEITIG','&nbsp;<b style="color:red;">wird von diesem Importer nicht unterst&uuml;tzt</b>&nbsp;');
            }
            if(isset($json['erlaubtefunktionen']['auftragabnummer']) && !$json['erlaubtefunktionen']['auftragabnummer'])
            {
              $this->app->Tpl->Add('MSGHOLEALLE','&nbsp;<b style="color:red;">wird von diesem Importer nicht unterst&uuml;tzt</b>&nbsp;');
            }
            if(isset($json['erlaubtefunktionen']['eigenschaften']) && !$json['erlaubtefunktionen']['eigenschaften'])
            {
              $this->app->Tpl->Add('MSGEIGENSCHAFTENUEBERTRAGEN','&nbsp;<b style="color:red;">wird von diesem Importer nicht unterst&uuml;tzt</b>&nbsp;');
            }
            if(isset($json['erlaubtefunktionen']['artikelbilder']) && !$json['erlaubtefunktionen']['artikelbilder'])
            {
              $this->app->Tpl->Add('MSGSHOPBILDERUEBERTRAGEN','&nbsp;<b style="color:red;">wird von diesem Importer nicht unterst&uuml;tzt</b>&nbsp;');
            }
            if(isset($json['erlaubtefunktionen']['kategorien']) && !$json['erlaubtefunktionen']['kategorien'])
            {
              $this->app->Tpl->Add('MSGKATEGORIENUEBERTRAGEN','&nbsp;<b style="color:red;">wird von diesem Importer nicht unterst&uuml;tzt</b>&nbsp;');
            }
          }
        }
      }
      if($this->app->erp->RechteVorhanden('shopexport','artikeluebertragen')){
        $this->app->Tpl->Add('AKTIONBUTTONS', '<tr><td><a href="index.php?module=shopexport&action=artikeluebertragung&id=' . $id . '"><input type="button" class="aktionbutton" value="{|Artikel &uuml;bertragen|}"></a></td></tr>');
      }
      if($struktur && isset($struktur['functions']) && is_array($struktur['functions']) && in_array('exportartikelbaum',$struktur['functions']))
      {
        $this->app->Tpl->Add('AKTIONBUTTONS','<tr><td><a href="#" onclick="artikelbaumexport();"><input type="button" class="aktionbutton" value="Artikelbaum &uuml;bertragen"></a></td></tr>');
      }
      if($struktur && isset($struktur['functions']) && is_array($struktur['functions']) && in_array('getarticlelist',$struktur['functions']))
      {
        $anzahl = $this->app->DB->Select("SELECT COUNT(id) FROM shopexport_getarticles WHERE shop = '$id' AND nummer <> ''");
        if($anzahl > 0)
        {
          $this->app->Tpl->Add('VORFORMULAR','
            <form action="" method="post" id="frmresetgetarticlelist">
              <input type="hidden" name="resetgetarticlelist" value="1" />
            </form>
          ');
          $gesamtAnzahlAbgeholteArtikel = $this->app->erp->GetKonfiguration('artikelimportanzahl_'.$id);
          $anzahlstring = '('.$anzahl.' von '.$gesamtAnzahlAbgeholteArtikel.')';
          $this->app->Tpl->Add('AKTIONBUTTONS','<tr><td><a href="#" onclick="$(\'#frmresetgetarticlelist\').submit();"><input type="button" class="aktionbutton" value="Artikel abholen abbrechen '.$anzahlstring.'"></a></td></tr>');
        }else {
          $buttoninfo = '';
          if(!empty($struktur['buttoninfo']) && !empty($struktur['buttoninfo']['getarticlelist']))
          {
            $buttoninfo = $struktur['buttoninfo']['getarticlelist'];
          }
          $this->app->Tpl->Add('VORFORMULAR','
            <form action="" method="post" id="frmgetarticlelist">
              <input type="hidden" name="getarticlelist" value="1" />
            </form>
          ');
          $this->app->Tpl->Add('AKTIONBUTTONS','<tr><td><a href="#" onclick="$(\'#frmgetarticlelist\').submit();"><input type="button" class="aktionbutton" value="{|Artikelliste abholen|}'.$buttoninfo.'"></a></td></tr>');
        }
      }else{
        $this->app->Tpl->Set('NURARTIKELLISTESTYLE',' style="display:none;" ');
      }
      if($struktur && isset($struktur['archiv']) && is_array($struktur['archiv']))
      {
        if($this->app->DB->Select("SELECT id FROM shopexport_archiv WHERE shop='$id' AND status = 'aktiv' LIMIT 1"))
        {
          $this->app->Tpl->Add('VORFORMULAR','
            <form action="" method="post" id="frmresetarchiv">
              <input type="hidden" name="resetarchiv" value="1" />
            </form>
          ');
          $this->app->Tpl->Add('AKTIONBUTTONS','<tr><td><a href="#" onclick="$(\'#frmresetarchiv\').submit();"><input type="button" class="aktionbutton" value="{|Alte Auftr&auml;ge abholen abbrechen|}"></a></td></tr>');
        }else{
          if(in_array('zeitraum', $struktur['archiv']))
          {
            $this->app->Tpl->Set('ARCHIVTYP', 'Zeitraum');
            $this->app->Tpl->Set('ARCHIVTYPVAL', 'zeitraum');
            $this->app->YUI->DatePicker('archiv_von');
            $this->app->YUI->DatePicker('archiv_bis');
            $this->app->YUI->TimePicker('archiv_zeitvon');
            $this->app->YUI->TimePicker('archiv_zeitbis');
          }elseif(in_array('ab_nummer', $struktur['archiv'])){
            $this->app->Tpl->Set('ARCHIVTYP', 'Nummer');
            $this->app->Tpl->Set('ARCHIVTYPVAL', 'ab_nummer');
            $this->app->Tpl->Set('ARCHIVZEITSTYPE','display:none;');
          }
          $this->app->Tpl->Parse('VORFORMULAR', 'shopexport_archiv.tpl');
          $this->app->Tpl->Add('AKTIONBUTTONS','<tr><td><a href="#" onclick="$(\'#popuparchiv\').dialog(\'open\');"><input type="button" class="aktionbutton" value="{|Alte Auftr&auml;ge importieren|}"></a></td></tr>');
        }
      }
    }
    $this->app->YUI->HideFormular('artikelimport',array('unchecked'=>'dummy','checked'=>'hinweisartikelimport'));
    $this->app->YUI->HideFormular('cronjobaktiv',array('unchecked'=>'direktimport','checked'=>'dummy'));
    $aktmodule = (String)$this->app->DB->Select("SELECT modulename FROM shopexport WHERE id = '$id' LIMIT 1");
    $found = false;
    if($aktmodule === '') {
      $found = true;
    }
    $obj = $this->app->erp->LoadModul('onlineshops');
    $modulesel = array(''=>'Extern');
    if($obj){
      $module = $obj->getApps();
      if(isset($module['installiert'])){
        foreach($module['installiert'] as $k => $v) {
          if(strpos($v['key'],'shopimporter_') !== 0) {
            continue;
          }
          if($v['key'] == $aktmodule){
            $found = true;
          }
          $modulesel[$v['key']] = $v['Bezeichnung'].(!empty($v['beta'])?' (Beta)':'');
        }
      }
    }
    if(!$found)$modulesel[$aktmodule] = 'Custom';
    $field = new HTMLSelect("modulename",0);
    $field->onchange = 'selectmodule(this);';
    //$field->onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($modulesel);
    $this->form->NewField($field);
    
    //$this->app->Tpl->Set(DATUM_BUCHUNG,
    //    "<img src=\"./themes/[THEME]/images/kalender.png\" onclick=\"displayCalendar(document.forms[0].datum,'dd.mm.yyyy',this)\">");

  }
  
  function ShowExtraeinstellungen($id, $obj, $target, &$struktur)
  {
    $target2 = $target.'2';
    if(method_exists($obj, 'EinstellungenStruktur'))
    {
      $r = new ReflectionMethod(get_class($obj), 'EinstellungenStruktur');
      $params = $r->getParameters();
      $anzargs = count($params);
      if($anzargs > 0)
      {
        $struktur = $obj->EinstellungenStruktur($id);
      }else{
        $struktur = $obj->EinstellungenStruktur();
      }

      if(empty($struktur['felder']['category_root_id'])) {
        $struktur['felder']['category_root_id'] = [
          'typ' => 'hidden',
        ];
      }
      if(empty($struktur['felder']['transform_cart_active'])) {
        $struktur['felder']['transform_cart_active'] = [
          'typ' => 'hidden',
          ];
        $struktur['felder']['transform_cart_data'] = [
          'typ' => 'hidden', 'bezeichnung' => '',
          ];
        $struktur['felder']['transform_cart'] = [
          'typ' => 'hidden',
          ];
        $struktur['felder']['transform_cart_replace'] = [
          'typ' => 'hidden',
          ];
        $struktur['felder']['transform_cart_format'] = [
          'typ' => 'hidden',
          ];
        $this->app->YUI->AutoComplete('cart', 'shopimport_auftraege', 1, '&id='.$id);
        $this->app->Tpl->Set('SHOPID', $id);
        //$this->app->Tpl->Parse('EXTRAEINSTELLUNGEN', 'shopexport_carttransformation.tpl');
      }

      if($struktur && isset($struktur['erlauben']) && is_array($struktur['erlauben']) && in_array('nurpreise',$struktur['erlauben']))
      {
        $this->app->Tpl->Set('NURPREISESTYLE','');
      }
      
      if($this->app->Secure->GetPOST('speichern'))
      {
        if(isset($struktur['felder']))
        {
          $json = array();
          $ok = false;
          foreach($struktur['felder'] as $name => $val)
          {
            if(isset($_POST[$name])){
              $ok = true;
            }
            $json['felder'][$name] = $this->app->Secure->GetPOST($name, '','', 1);
            if(isset($val['replace']))
            {
              switch($val['replace'])
              {
                case 'lieferantennummer':
                  $json['felder'][$name] = $this->app->erp->ReplaceLieferantennummer(1,$json['felder'][$name],1);
                break;
                case 'projekt':
                  $json['felder'][$name] = $this->app->erp->ReplaceProjekt(1,$json['felder'][$name],1);
                break;
                case 'artikel':
                  $json['felder'][$name] = $this->app->erp->ReplaceArtikel(1,$json['felder'][$name],1);
                break;
                case 'preisgruppekennziffer':
                  $json['felder'][$name] = $this->app->erp->ReplacePreisgruppe(1,$json['felder'][$name],1);
                break;
              }
            }
            if(isset($val['autocomplete']))
            {
              switch($val['autocomplete'])
              {
                case 'kunde':
                  $json['felder'][$name] = $this->app->erp->ReplaceKundennummer(1,$json['felder'][$name],1);
                  break;
              }
            }
            $typ = 'text';
            if(!empty($val['typ'])){
              $typ = $val['typ'];
            }
            switch($typ) {
              case 'datum':
              case 'date':
                $this->app->YUI->DatePicker($name);
                if(isset($json['felder'][$name]) && strpos($json['felder'][$name], '.') !== false) {
                  $json['felder'][$name] = $this->app->String->Convert($json['felder'][$name],'%3.%2.%1','%1-%2-%3');
                }
                break;
              case 'password':
                if($json['felder'][$name] === '***************'){
                  $oldData = json_decode($this->app->DB->Select('SELECT einstellungen_json FROM shopexport WHERE id='.$id),true);
                  $json['felder'][$name] = $oldData['felder'][$name];
                }
            }
          }
          $json_str = $this->app->DB->real_escape_string(json_encode($json));
          if(json_decode(json_encode($json),true) && $ok)
          {
            $this->app->DB->Update("UPDATE shopexport SET einstellungen_json = '$json_str' WHERE id = '".$id."' LIMIT 1");
          }
        }
      }
      $html = '';
      $html2 = '';
      $cols1 = null;
      $cols2 = null;
      $aktcol = null;
      $hidden = '';
      $json = $this->app->DB->Select("SELECT einstellungen_json FROM shopexport WHERE id = '$id' LIMIT 1");
      if($json == '' && isset($struktur['felder']))
      {
        $json = null;
        foreach($struktur['felder'] as $k => $v)
        {
          if(!empty($v['defaultcreate']))
          {
            $json['felder'][$k] = $v['defaultcreate'];
          }elseif(!empty($v['default']))
          {
            $json['felder'][$k] = $v['default'];
          }
        }
        $json = json_encode($json);
      }
      if($json){
        $json = json_decode($json, true);
      }
      $this->app->Tpl->Set(
        'TEXTAREASMARTYINCOMMING',
        !isset($json['felder']['transform_cart'])?'': htmlspecialchars(trim($json['felder']['transform_cart']))
      );
      if(!empty($json['felder']['transform_cart_active'])) {
        $this->app->Tpl->Set('TRANSFERACTIVE', ' checked="checked" ');
      }
      if(!empty($json['felder']['transform_cart_replace'])) {
        $this->app->Tpl->Set('REPLACECART', ' checked="checked" ');
      }
      if(!empty($json['felder']['transform_cart_format'])) {
        if($json['felder']['transform_cart_format'] === 'xml') {
          $this->app->Tpl->Set('OPTIONXML', ' selected="selected" ');
        }
        elseif($json['felder']['transform_cart_format'] === 'json') {
          $this->app->Tpl->Set('OPTIONJSON', ' selected="selected" ');
        }
      }
      if(!is_array($json)) {
        $json = ['felder' => []];
      }
      if(!is_array($json['felder'])) {
        $json['felder'] = [];
      }
      foreach($struktur['felder'] as $name => $val)
      {
        if(!isset($json['felder'][$name]))
        {
          if(isset($val['default']))
          {
            $json['felder'][$name] = $val['default'];
          }else{
            $json['felder'][$name] = '';
          }
        }
        $typ = 'text';
        if(!empty($val['typ'])){
          $typ = $val['typ'];
        }
        if($typ === 'hidden')
        {
          $hidden .= '<input type="hidden" name="'.$name.'" value="'.(!isset($json['felder'][$name])?'':htmlspecialchars($json['felder'][$name])).'" id="'.$name.'" />';
          continue;
        }
        $aktcol = '';
        //$aktcol .= '<tr><td>'.(empty($val['bezeichnung'])?$name:$val['bezeichnung']).'</td><td>';
        $aktcol .= '<td>'.(empty($val['bezeichnung'])?$name:$val['bezeichnung']).'</td><td>';

        if(isset($val['replace']))
        {
          switch($val['replace'])
          {
            case 'lieferantennummer':
              $json['felder'][$name] = $this->app->erp->ReplaceLieferantennummer(0,$json['felder'][$name],0);
              if($target !== 'return'){
                $this->app->YUI->AutoComplete($name, 'lieferant', 1);
              }
            break;
            case 'projekt':
              $json['felder'][$name] = $this->app->erp->ReplaceProjekt(0,$json['felder'][$name],0);
              if($target !== 'return'){
                $this->app->YUI->AutoComplete($name, 'projektname', 1);
              }
            break;
            case 'artikel':
              $json['felder'][$name] = $this->app->erp->ReplaceArtikel(0,$json['felder'][$name],0);
              if($target !== 'return'){
                $this->app->YUI->AutoComplete($name, 'artikelnummer', 1);
              }
            break;
            case 'preisgruppekennziffer':
              $json['felder'][$name] = $this->app->erp->ReplacePreisgruppe(0,$json['felder'][$name],0);
              if($target !== 'return'){
                $this->app->YUI->AutoComplete($name, 'preisgruppekennziffer', 1);
              }
              break;
          }
        }
        if(isset($val['autocomplete']))
        {
          switch($val['autocomplete'])
          {
            case 'kunde':
              $json['felder'][$name] = $this->app->erp->ReplaceKundennummer(0,$json['felder'][$name],0);
              $this->app->YUI->AutoComplete($name, 'kunde');
              break;
            case 'adresse':
              $this->app->YUI->AutoComplete($name, 'adresse');
              break;
            case 'artikel':
              $this->app->YUI->AutoComplete($name, 'artikelnummer');
            break;
          }
        }
        switch($typ)
        {
          case 'textarea':
            $aktcol .= '<textarea name="'.$name.'" id="'.$name.'">'.(!isset($json['felder'][$name])?'':htmlspecialchars($json['felder'][$name])).'</textarea>';
          break;
          case 'checkbox':
            $aktcol .= '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="1" '.((isset($json['felder'][$name]) && $json['felder'][$name])?' checked="checked" ':'').' />';
          break;
          case 'password':
            $aktcol .= '<input type="text" name="'.$name.'" id="'.$name.'" value="'.(empty($json['felder'][$name])?'':'***************').'" />';
            break;
          case 'select':
            $aktcol .= '<select name="'.$name.'">';
            if(isset($val['optionen']) && is_array($val['optionen']))
            {
              foreach($val['optionen'] as $k => $v)
              {
                $aktcol .= '<option value="'.$k.'"'.($k == (isset($json['felder'][$name])?$json['felder'][$name]:'')?' selected="selected" ':'').'>'.$v.'</option>';
              }
            }
            $aktcol .= '</select>';
          break;
          case 'submit':
            if(isset($val['text'])){
              $aktcol .= '<form method="POST"><input type="submit" name="'.$name.'" value="'.$val['text'].'"></form>';
            }
          break;
          case 'custom':
            if(isset($val['function']))
            {
              $tmpfunction = $val['function'];
              if(method_exists($this, $tmpfunction)){
                $aktcol .= $this->$tmpfunction();
              }
            }
          break;
          default:
            switch($typ) {
              case 'datum':
              case 'date':
                $this->app->YUI->DatePicker($name);
                if(isset($json['felder'][$name]) && strpos($json['felder'][$name], '-') !== false) {
                  $json['felder'][$name] = $this->app->String->Convert($json['felder'][$name],'%1-%2-%3','%3.%2.%1');
                }
                break;
            }
            $aktcol .= '<input type="text" '.(empty($val['size'])?'':' size="'.$val['size'].'" ').' name="'.$name.'" id="'.$name.'" value="'.(!isset($json['felder'][$name])?'':htmlspecialchars($json['felder'][$name])).'" />';
          break;
        }
        if(isset($val['vorschlag']) && $val['vorschlag'] != '')
        {
          $aktcol .= '&nbsp;<input type="button" value="'.($val['vorschlag_label']!=''?$val['vorschlag_label']:$val['vorschlag']).'" onclick="$(\'#'.$name.'\').val(\''.$val['vorschlag'].'\');" '.(isset($val['minvorschlagsize']) && $val['minvorschlagsize']?' style="min-width:'.$val['minvorschlagsize'].'px;" ':'').' />';
        }
        if(isset($val['info']) && $val['info']){
          $aktcol .= ' <i>'.$val['info'].'</i>';
        }
        if(isset($val['col']) && $val['col'] == 2)
        {
          if(isset($val['heading']))
          {
            $cols2[] = '<td colspan="2"><b>'.$val['heading'].'</b></td>';
          }
          $cols2[] = $aktcol;
        }else{
          if(isset($val['heading']))
          {
            $cols1[] = '<td colspan="2"><b>'.$val['heading'].'</b></td>';
          }
          $cols1[] = $aktcol;
        }
        $aktcol .= '</td>';
        //$aktcol .= '</td></tr>';
                //$htmlname = 'html';
        //if(isset($val['col']) && $val['col'] == 2)$htmlname = 'html2';
        
      }
      /*if($cols1)
      {
        foreach($cols1 as $k => $v)
        {
          $html .= '<tr>'.$cols1[$k].'</tr>';
        }
      }
      if($cols2)
      {
        $html2 .= '<table>';
        foreach($cols2 as $k => $v)
        {
          $html2 .= '<tr>'.$cols2[$k].'</tr>';
        }
        $html2 .= '</table>';
      }*/
      
      
      
      $rows = 0;
      if($cols1){
        $rows = count($cols1);
      }
      if($cols2 && count($cols2) > $rows){
        $rows = count($cols2);
      }
      if($rows == 0)
      {
        $this->app->Tpl->Set('VOREXTRA', '<!--');
        $this->app->Tpl->Set('NACHEXTRA', '-->');
      }
      
      for($i = 0; $i < $rows; $i++)
      {
        if($i == 0)
        {
          $html .= '<tr id="erstezeile">';
        }else
        {
          $html .= '<tr>';
        }
        if($cols1 && isset($cols1[$i]))
        {
          $html .= $cols1[$i];
        }else{
          $html .= '<td>&nbsp;</td><td>&nbsp;</td>';
        }
        if($cols2 && isset($cols2[$i]))
        {
          $html .= $cols2[$i];
        }else{
          $html .= '<td>&nbsp;</td><td>&nbsp;</td>';
        }
        $html .= '</tr>';
      }
      $html .= $hidden;
      
      $this->app->Tpl->Parse('TAB9', 'shopexport_smarty.tpl');
      if($target === 'return'){
        return $html;
      }
      $this->app->Tpl->Add($target, $html);
      $this->app->Tpl->Add($target2, $html2);
    }
    
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplaceArtikel($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceArtikel($db,$value,$fromform);
  }




  function ReplaceDecimal($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden

    return str_replace(",",".",$value);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(strpos($value,'-') > 0) $dbformat = 1;

    // wenn ziel datenbank
    if($db)
    { 
      if($dbformat) return $value;
      else return $this->app->String->Convert($value,"%1.%2.%3","%3-%2-%1");
    }
    // wenn ziel formular
    else
    { 
      if($dbformat) return $this->app->String->Convert($value,"%1-%2-%3","%3.%2.%1");
      else return $value;
    }
  }

  /**
   * @param int|bool   $db
   * @param int|string $value
   * @param int|bool   $fromform
   *
   * @return array|mixed|string|null
   */
  public function ReplaceVertrieb($db,$value,$fromform)
  {
    if(!$fromform) {
      $id = $value;
      $abkuerzung = $this->app->DB->Select(sprintf("SELECT CONCAT(id, ' ', name) FROM adresse WHERE id=%d AND geloescht=0 LIMIT 1", $id));
    }
    else {
      $abkuerzung = explode(' ', $value);
      $id = reset($abkuerzung);
      $id =  $this->app->DB->Select(sprintf('SELECT id FROM adresse WHERE id = %d AND geloescht=0 LIMIT 1', $id));
    }

    // wenn ziel datenbank
    if($db) {
      return $id;
    }
    // wenn ziel formular

    return $abkuerzung;
  }

  function ReplaceAdresse($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    if(!$fromform) {
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    }
    else {
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE name='$value' AND name <> '' AND geloescht=0 LIMIT 1");
    }

    // wenn ziel datenbank
    if($db) {
      return $id;
    }
    // wenn ziel formular

    return $abkuerzung;
  }

  public function Table()
  {
		$this->app->YUI->TableSearch('INHALT',"onlineshopslist");
    $this->app->Tpl->Parse($this->parsetarget,"rahmen70.tpl");

  }



  public function Search()
  {
    //$this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }
  function ReplaceSteuersatz($db,$value,$fromform)
  {
    if($db)
    {
      if($value === "" || $value === null)return -1;
      return str_replace(',','.', $value);
    }else{
      if($value < 0)return "";
      return $value;
    }
  }
  
  function ReplacePreisgruppe($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT CONCAT(kennziffer,' ',name) as name FROM gruppen WHERE id='$id' LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM gruppen WHERE CONCAT(kennziffer,' ',name)='$value' OR (kennziffer='$value' AND kennziffer!='') AND art LIKE 'preisgruppe' LIMIT 1");
      if($id <=0) $id=0;
    }

    // wenn ziel datenbank
    if($db)
    {
      return $id;
    }
    // wenn ziel formular
    else
    {
      return $abkuerzung;
    }
  }

  /**
   * @param int    $shopId
   * @param string $tab
   */
  protected function showCategoryTree($shopId, $tab)
  {
    $this->app->Tpl->Set('SHOPID', $shopId);
    $this->app->Tpl->Add('TAB'.$tab,$this->app->Tpl->OutputAsString('shopexport_tree.tpl'));
    $this->app->Tpl->Add('HOOKLITABS','<li><a href="#tabs-'.$tab.'">{|Artikelbaum|}</a></li>');
    $this->app->Tpl->Add('HOOKTABS', '<div id="tabs-'.$tab.'">[TAB'.$tab.']</div>');
  }
}
