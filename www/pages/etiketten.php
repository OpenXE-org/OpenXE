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
include '_gen/etiketten.php';

class Etiketten extends GenEtiketten {
  /**
   * @var Application $app
   */
  var $app;

  function __construct($app, $intern = false) {
    //parent::GenEtiketten($app);
    $this->app=$app;
    if($intern) {
      return;
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","EtikettenCreate");
    $this->app->ActionHandler("edit","EtikettenEdit");
    $this->app->ActionHandler("list","EtikettenList");
    $this->app->ActionHandler("bild","EtikettenBild");
    $this->app->ActionHandler("delete","EtikettenDelete");

    $this->app->ActionHandlerListen($app);
  }


  function EtikettenCreate()
  {
    $this->EtikettenMenu();
    parent::EtikettenCreate();
  }

  function EtikettenDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
    {
      $this->app->DB->Delete("DELETE FROM etiketten WHERE id='$id'");
    }

    $this->EtikettenList();
  }


  function EtikettenList()
  {
    $this->EtikettenMenu();
    parent::EtikettenList();
  }

  function EtikettenMenu()
  {
    $id = $this->app->Secure->GetGET('id');
    $action = $this->app->Secure->GetGET('action');
    $this->app->erp->MenuEintrag("index.php?module=etiketten&action=create","Neues Etikett anlegen");

    if($action ==='edit' || $action === 'bild')
    {
      $this->app->erp->MenuEintrag("index.php?module=etiketten&action=edit&id=$id","Details");
      $this->app->erp->MenuEintrag("index.php?module=etiketten&action=bild&id=$id","Bild Generator");
    }

    if($action==='list')
    {
      $this->app->erp->MenuEintrag("index.php?module=etiketten&action=list","&Uuml;bersicht");
      $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    }
    else{
      $this->app->erp->MenuEintrag("index.php?module=etiketten&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
    }
  }

  function EtikettenBild()
  {
    $this->EtikettenMenu();
    $submit = $this->app->Secure->GetPOST("submit");

    $pfad = $_FILES["image"]["tmp_name"];
    if($submit!="")
    { 
      if(file_exists($pfad))
      {
        $result = $this->app->erp->PNG2Etikett($pfad);
        if($result['result']=="1")
        {
          $this->app->Tpl->Set('BILD',"<textarea rows=\"10\" cols=\"80\"><image x=\"1\" y=\"1\" width=\"".$result['width']."\" height=\"".$result['height']."\">".$result['stream']."</image></textarea>");
          $this->app->Tpl->Set('BILD2',"<textarea rows=\"10\" cols=\"80\"><label><image x=\"1\" y=\"1\" width=\"".$result['width']."\" height=\"".$result['height']."\">".$result['stream']."</image></label></textarea>");
        } 
        else
          $this->app->Tpl->Set('BILD',"<div class=\"error\">".$result['message']."</div>");
      }
    } 

    $this->app->Tpl->Parse('PAGE',"etiketten_bild.tpl");
  } 

  function EtikettenEdit()
  {
    $this->EtikettenMenu();
    parent::EtikettenEdit();
  }

  /**
   * @param int    $lieferschein
   * @param int    $etiketten_drucker
   * @param string $etiketten_art
   * @param int    $etiketten_sort
   */
  public function LieferscheinPositionenDrucken($lieferschein,$etiketten_drucker,$etiketten_art,$etiketten_sort=0)
  {
    /*
       Barcode, Artikelname,
       Lagerplatznummer,
        Artikelstückzahl
    */
    $lsRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT adresse, belegnr, sprache, kundennummer FROM lieferschein WHERE id=%d',
        (int)$lieferschein
      )
    );
    $adresse = $lsRow['adresse'];

    $pos = $this->app->DB->SelectArr(
      sprintf(
        'SELECT art.*,lp.id, lp.bezeichnung, lp.nummer as lpnummer, lp.menge
          FROM lieferschein_position AS lp
          LEFT JOIN artikel AS art ON lp.artikel = art.id
          WHERE lp.lieferschein= %d ORDER BY %s',
        (int)$lieferschein, $etiketten_sort == 1?'lp.lagertext':'lp.sort'
      )
    );

    $lieferscheinnummer = $lsRow['belegnr'];
    if(!empty($pos)){
      foreach($pos as $row) {
        $lagerbezeichnung = $this->app->erp->GetArtikelStandardlager($row['id']);

        $tmp = $row;
        $row['nummer'] = $row['lpnummer'];
        unset($tmp['lpnummer']);
        /*$tmp = $this->app->DB->SelectRow(
          sprintf('SELECT * FROM artikel WHERE id=%d LIMIT 1', $row['artikel'])
        );*/
        $tmp['belegnr'] = $lieferscheinnummer;
        //$tmp['']

        // pro seriennummer ein artike

        $checkserriennummer = $this->app->DB->SelectArr(
          sprintf(
          "SELECT wert 
            FROM beleg_chargesnmhd 
            WHERE doctype='lieferschein' AND doctypeid=%d AND pos=%d",
            (int)$lieferschein, (int)$row['id']
          )
        );
        if(!empty($checkserriennummer)){
          foreach($checkserriennummer as $checkSeriennummerRow) {
          //for ($ics = 0; $ics < count($checkserriennummer); $ics++) {
            $tmp['name_de'] = $row['bezeichnung'];
            $tmp['nummer'] = $row['nummer'];
            unset($tmp['bezeichnung']);
            $tmp['menge'] = 1;//$pos[$i]['menge'];
            $tmp['lager_platz_name'] = $lagerbezeichnung;
            $tmp['seriennummer'] = '';
            if($checkSeriennummerRow['wert'] != ''){
              $tmp['seriennummer'] = $checkSeriennummerRow['wert'];
            }

            $this->app->erp->EtikettenDrucker($etiketten_art, 1, 'lieferschein', $lieferschein, $tmp, '', $etiketten_drucker, '', false, $adresse, 'lieferschein_position');
          }
        }else{

          $tmp['name_de'] = $row['bezeichnung'];
          $tmp['nummer'] = $row['nummer'];
          unset($tmp['bezeichnung']);
          $tmp['menge'] = str_replace('.', ',', $this->app->erp->FormatMengeBetrag($row['menge'])); //$pos[$i]['menge'];
          $tmp['lager_platz_name'] = $lagerbezeichnung;

          $this->app->erp->EtikettenDrucker($etiketten_art, 1, 'lieferschein', $lieferschein, $tmp, '', $etiketten_drucker, '', false, $adresse, 'lieferschein_position');
        }
      }
    }
    foreach($tmp as $key=>$value) {
      $tmp[$key] = "CUT/Trennetikett";
    }

    $tmp['name_de']="CUT/Trennetikett";
    $tmp['nummer']="CUT/Trennetikett";
    $tmp['menge']="CUT/Trennetikett";
    $tmp['belegnr']="CUT/Trennetikett";
    $tmp['lager_platz_name']="CUT/Trennetikett";

    $this->app->erp->EtikettenDrucker($etiketten_art,1,'lieferschein', $lieferschein,$tmp,'',$etiketten_drucker,'',false,$adresse,'lieferschein_position');
  }
}

