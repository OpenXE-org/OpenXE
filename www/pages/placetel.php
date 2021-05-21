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

class Placetel {
  var $app;

  static function TableSearch(&$app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case "placetel_list":
      $allowed['laender'] = array('list');

      $heading = array('Firma', 'Ansprechpartner', 'Telefon','Men&uuml;');
      $width = array('40%', '40%', '15%','5%');

      $findcols = array('t.name', 't.ansprechpartner', 't.telefon','t.did');
      $searchsql = array('t.name', 't.ansprechpartner', 't.telefon');
      $doppelteids = true;
      $defaultorder = 1;
      $defaultorderdesc = 0;

      $menu = "<a href=\"#\" onclick=call(\"%value%\",\"index.php?module=placetel&action=call&id=%value%\")><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/phone.png\" border=\"0\"></a>";

      $where = "";

      $sql = "SELECT SQL_CALC_FOUND_ROWS t.id, t.name, t.ansprechpartner, t.telefon, t.did
      
      FROM   (              
          (
            SELECT 
             a.id, a.name, a.ansprechpartner, a.telefon,concat('1-',a.id) as did FROM adresse a WHERE a.geloescht <> 1 AND a.telefon <> ''
          ) UNION ALL (
            SELECT 
             a.id, a.name, a.ansprechpartner, a.mobil as telefon, concat('2-',a.id) as did FROM adresse a WHERE a.geloescht <> 1 AND a.mobil <> '' 
          )UNION ALL (
          
            SELECT 
             ansp.id, a.name, ansp.name as ansprechpartner, ansp.telefon as telefon, concat('3-',ansp.id) as did FROM adresse a INNER JOIN ansprechpartner ansp ON a.id = ansp.adresse WHERE a.geloescht <> 1 AND ansp.telefon <> '' AND  ansp.geloescht <> 1         
          )UNION ALL (
          
            SELECT 
             ansp.id, a.name, ansp.name as ansprechpartner, ansp.mobil as telefon, concat('4-',ansp.id) as did FROM adresse a INNER JOIN ansprechpartner ansp ON a.id = ansp.adresse WHERE a.geloescht <> 1 AND ansp.mobil <> ''  AND ansp.geloescht <> 1         
          )
          
      )t
      ";

      $count = "SELECT
            SUM(anzahl)
          FROM 
          (
            (
              SELECT 0 as anzahl
            )
            UNION ALL
            (
            SELECT count(a.id) as anzahl FROM adresse a WHERE  a.geloescht <> 1 AND a.telefon <> ''
            )
            UNION ALL
            (
            SELECT count(a.id) as anzahl FROM adresse a   WHERE a.geloescht <> 1 AND a.mobil <> '' 
            )
            UNION ALL
            (
            SELECT  count(ansp.id) as anzahl FROM adresse a INNER JOIN ansprechpartner ansp ON a.id = ansp.adresse WHERE a.geloescht <> 1 AND ansp.telefon <> '' AND ansp.geloescht <> 1         
            )
            UNION ALL
            (
            SELECT  count(ansp.id) as anzahl FROM adresse a INNER JOIN ansprechpartner ansp ON a.id = ansp.adresse WHERE a.geloescht <> 1 AND ansp.mobil <> ''  AND ansp.geloescht <> 1         
            )
            
          ) a  
            ";
      break;

    }

    $erg = false;

    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v))$erg[$v] = $$v;
    }
    return $erg;
  }


  
  function __construct(&$app, $intern = false)
  {
    $this->app=&$app;
    if($intern)return;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","PlacetelList");
    $this->app->ActionHandler("call","PlacetelCall");
    $this->app->ActionHandler("einstellungen","PlacetelEinstellungen");

    $this->app->DefaultActionHandler("list");

    $this->app->ActionHandlerListen($app);
  }

  function PlacetelList()
  {
    $telefon = $this->app->Secure->GetPOST("telefon");
    if($telefon!="")
    {
      $this->PlacetelCall($telefon);
      $this->app->Tpl->Set("TELEFON",$telefon);
    }


    $this->app->YUI->TableSearch('TELEFONBUCH','placetel_list', "show","","",basename(__FILE__), __CLASS__);

    $this->PlacetelMenu();
    $this->app->Tpl->Parse('PAGE',"placetel_list.tpl");
  }

  function PlacetelEinstellungen()
  {
    $this->app->YUI->AutoSaveKonfiguration("apikey","placetel_list_apikey");
    $this->app->YUI->AutoSaveKonfiguration("accounts","placetel_list_accounts");
    $this->app->YUI->AutoSaveKonfiguration("sharedsecret","placetel_shared_secret");

    $apikey = $this->app->erp->GetKonfiguration("placetel_list_apikey");
    $accounts = $this->app->erp->GetKonfiguration("placetel_list_accounts");
    $sharedSecret = $this->app->erp->GetKonfiguration("placetel_shared_secret");

    $this->app->Tpl->Set('APIKEY',$apikey);
    $this->app->Tpl->Set('ACCOUNTS',$accounts);
    $this->app->Tpl->Set('SHAREDSECRET',$sharedSecret);

    if($apikey!="")
    {
      $result = json_decode($this->PlacetelAPI("getVoIPUsers"),true);
      for($i=0;$i<count($result);$i++)
      {
        $this->app->Tpl->Add(ACCOUNTROW,"<tr><td>".$result[$i]['name']."</td><td>".$result[$i]['stype']."</td><td>".$result[$i]['uid']."</td></tr>");
      }
      //print_r($this->PlacetelAPI("test"));
    }

    $this->PlacetelMenu();
    $this->app->Tpl->Parse('PAGE',"placetel_einstellungen.tpl");
  }

  function PlacetelMenu()
  {
    $this->app->erp->MenuEintrag("index.php?module=placetel&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=placetel&action=einstellungen","Einstellungen");
  }


  function PlacetelAPI($request,$fields=array())
  {
    $apikey = $this->app->erp->GetKonfiguration("placetel_list_apikey");
    $fields['api_key']=$apikey;

    //url-ify the data for the POST
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');

    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_POST => count($fields),
      CURLOPT_POSTFIELDS => $fields_string,
      CURLOPT_URL => 'https://api.placetel.de/api/'.$request.'.json',
      CURLOPT_USERAGENT => 'Codular Sample cURL Request'
    ));
      //CURLOPT_URL => 'https://api.placetel.de/api/".$request.".json?sipuid='.$fields['sipuid'],
    // Send the request & save response to $resp
    $resp = curl_exec($curl);   // Close request to clear up some resources
    curl_close($curl);
    return $resp;
  }

  function PlacetelCall($target="")
  {
    
    $internal=false;
    if($target=="")
      $target=$this->app->Secure->GetGET("target");
    else $internal=true;

    if(!$target)
    {
      $ida = explode('-',$this->app->Secure->GetGET('id'));
      if(count($ida) == 2)
      {
        $id = (int)$ida[1];
        switch($ida[0])
        {
          case '1':
            $target = $this->app->DB->Select("SELECT telefon FROM adresse WHERE id = '$id' LIMIT 1");
          break;
          case '2':
            $target = $this->app->DB->Select("SELECT mobil FROM adresse WHERE id = '$id' LIMIT 1");
          break;
          case '3':
            $target = $this->app->DB->Select("SELECT telefon FROM ansprechpartner WHERE id = '$id' LIMIT 1");
          break;
          case '4':
            $target = $this->app->DB->Select("SELECT mobil FROM ansprechpartner WHERE id = '$id' LIMIT 1");
          break;
        }
      }
    }

    // ersetzt ein führendes Plust durch 00
    $target = preg_replace('/\A\+/', '00', $target);
    // entfernt Nicht-Ziffern
    $target = preg_replace('/[^0-9]+/', '', $target); 

    // telefon im format 004908125 dann 0 entfernen
    if(substr($target,0,2)=="00" && substr($target,4,1)=="0")
    {
      $target = substr($target,0,4).substr($target,4+1);
    }

    $username = $this->app->User->GetUsername();
    $accounts = $this->app->erp->GetKonfiguration("placetel_list_accounts");
  
    $tmp = explode(PHP_EOL,trim($accounts));

    for($i=0;$i<count($tmp);$i++)
    {
      $subtmp = explode(':',trim($tmp[$i]));

      if(count($subtmp)>0)
      {
        $subtmp[0] = trim($subtmp[0]);
        $subtmp[1] = trim($subtmp[1]);

        if($subtmp[0]==$username)
        {
          $sipuid = $subtmp[1];
          break;
        } 
      }
    }

    $fields['target']= $target;
    $fields['sipuid']= $sipuid;

    
    if($target)$this->PlacetelAPI("initiateCall",$fields);

    if($internal) return;
    else 
    {
      echo json_encode(true);
      exit;
    }
  }

}

?>
