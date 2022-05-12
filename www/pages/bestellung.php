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
use Xentral\Components\Pdf\Exception\PdfComponentExceptionInterface;
use Xentral\Components\Pdf\PdfMerger;
use Xentral\Components\Http\Session\SessionHandler;
use Xentral\Modules\Article\Gateway\ArticleGateway;
use Xentral\Modules\ScanArticle\Service\ScanArticleService;
use Xentral\Modules\ScanArticle\Exception\ArticleNotFoundException;

include '_gen/bestellung.php';

class Bestellung extends GenBestellung
{
  /** @var Application $app */

  /**
   * Bestellung constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","BestellungList");
    $this->app->ActionHandler("create","BestellungCreate");
    $this->app->ActionHandler("positionen","BestellungPositionen");
    $this->app->ActionHandler("upbestellungposition","UpBestellungPosition");
    $this->app->ActionHandler("delbestellungposition","DelBestellungPosition");
    $this->app->ActionHandler("copybestellungposition","CopyBestellungPosition");
    $this->app->ActionHandler("downbestellungposition","DownBestellungPosition");
    $this->app->ActionHandler("positioneneditpopup","BestellungPositionenEditPopup");
    $this->app->ActionHandler("edit","BestellungEdit");
    $this->app->ActionHandler("copy","BestellungCopy");
    $this->app->ActionHandler("auftrag","BestellungAuftrag");
    $this->app->ActionHandler("delete","BestellungDelete");
    $this->app->ActionHandler("undelete","BestellungUndelete");
    $this->app->ActionHandler("freigabe","BestellungFreigabe");
    $this->app->ActionHandler("freigegeben","BestellungFreigegeben");
    $this->app->ActionHandler("abschicken","BestellungAbschicken");
    $this->app->ActionHandler("dateien","BestellungDateien");
    $this->app->ActionHandler("pdf","BestellungPDF");
    $this->app->ActionHandler("inlinepdf","BestellungInlinePDF");
    $this->app->ActionHandler("protokoll","BestellungProtokoll");
    $this->app->ActionHandler("minidetail","BestellungMiniDetail");
    $this->app->ActionHandler("editable","BestellungEditable");
    $this->app->ActionHandler("livetabelle","BestellungLiveTabelle");
    $this->app->ActionHandler("schreibschutz","BestellungSchreibschutz");
    $this->app->ActionHandler("abschliessen","BestellungAbschliessen");
    $this->app->ActionHandler("alsversendet","BestellungAlsversendet");
    $this->app->ActionHandler("pdffromarchive","BestellungPDFfromArchiv");
    $this->app->ActionHandler("archivierepdf","BestellungArchivierePDF");
    $this->app->ActionHandler("einlagern","BestellungEinlagern");
    $this->app->ActionHandler("offenepositionen","BestellungOffenePositionen");
    $this->app->ActionHandler("steuer","BestellungSteuer");
    $this->app->ActionHandler("adressebestellungcopy", "AdresseBestellungCopy");
    $this->app->ActionHandler("ean","BestellungEAN");
    $this->app->DefaultActionHandler("list");

    $id = (int)$this->app->Secure->GetGET('id');
    $nummer = $this->app->Secure->GetPOST('adresse');

    if($id > 0){
      $bestRow = $this->app->DB->Select(
        sprintf(
          'SELECT a.name, b.belegnr 
        FROM bestellung b 
        LEFT JOIN adresse a ON b.adresse = a.id 
        WHERE b.id=%d 
        LIMIT 1',
          $id
        )
      );
    }

    if($nummer=='' && !empty($bestRow)){
      $adresse = $bestRow['name'];
    }
    else{
      $adresse = $nummer;
    }

    $nummer = !empty($bestRow)?$bestRow['belegnr']:'';
    if($nummer=='' || $nummer=='0') {
      $nummer='ohne Nummer';
    }

    $this->app->Tpl->Set('UEBERSCHRIFT','Bestellung:&nbsp;'.$adresse.' ('.$nummer.')');
    $this->app->Tpl->Set('FARBE','[FARBE2]');

    $this->app->erp->Headlines('Bestellung');

    $this->app->ActionHandlerListen($app);
  }

  function BestellungSteuer()
  {
    
  }
  
  function BestellungUndelete()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $bestellungRow = $this->app->DB->SelectRow(
      sprintf("SELECT status, belegnr, name FROM bestellung WHERE id=%d LIMIT 1", $id)
    );
    $status = $bestellungRow['status'];
    $belegnr = $bestellungRow['belegnr'];
    $name = $bestellungRow['name'];

    if($status==='storniert')
    { 
      $this->app->erp->BestellungProtokoll($id,"Bestellung Storno rückgängig");
      $this->app->DB->Update("UPDATE bestellung SET status='freigegeben' WHERE id='$id' LIMIT 1");
      $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Bestellung \"$name\" ($belegnr) wurde wieder freigegeben!</div>  ");
    } else {
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Bestellung \"$name\" ($belegnr) kann nicht wieder freigegeben werden, da sie nicht storniert ist.</div>  ");
    }
    //header("Location: ".$_SERVER['HTTP_REFERER']."&msg=$msg");
    $this->app->Location->execute('index.php?module=bestellung&action=list&msg='.$msg);
  }

  function BestellungEinlagern($id = null, $lagerplatz = null)
  {
    if($id === null)
    {
      $intern = false;
      $id = (int)$this->app->Secure->GetGET('id');
    }else{
      $intern = true;
    }
    if($id)
    {
      $arr = $this->app->DB->SelectRow("SELECT projekt,belegnr,status FROM bestellung WHERE id = '$id' LIMIT 1");
      if(!empty($arr)){
        $projekt = $arr['projekt'];
        $belegnr = $arr['belegnr'];
        $status = $arr['status'];
        if(($status === 'versendet' || $status === 'freigegeben')){
          $standardlager = $this->app->DB->Select("SELECT id FROM lager_platz WHERE geloescht <> 1 AND sperrlager <> 1 AND poslager <> 1 ORDER BY id LIMIT 1");
          $positionen = $this->app->DB->SelectArr("SELECT id,artikel,menge,geliefert FROM bestellung_position WHERE geliefert < menge AND bestellung='$id'");
          if($positionen){
            foreach ($positionen as $position) {
              if($lagerplatz){
                $lager = $lagerplatz;
              }else{
                $lager = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id = '" . $position['artikel'] . "' LIMIT 1");
                if(!$this->app->DB->Select("SELECT id FROM lager_platz WHERE id = '$lager' AND geloescht <> 1 LIMIT 1")) {
                  $lager = $standardlager;
                }
                if(!$lager) {
                  $lager = $standardlager;
                }
              }
              if($lager){
                $this->app->erp->LagerEinlagern(
                  $position['artikel'], $position['menge'] - $position['geliefert'], $lager, $projekt,
                  'Wareneingang von Bestellung ' . $belegnr,'','','bestellung',$id
                );
                $this->app->DB->Update("UPDATE bestellung_position SET geliefert = menge WHERE id = '" . $position['id'] . "' LIMIT 1");
              } else {
                $dataartikel = $this->app->DB->SelectRow("SELECT nummer,name_de FROM artikel WHERE id='".$position['artikel']."'");
                $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Abbruch beim Einlagern da für den Artikel \"".$dataartikel['nummer']." ".$dataartikel['name_de']."\" kein Standard Lagerplatz definiert wurde oder alle Lager Sperr- oder POS-Lager sind!</div>");
                $this->app->Location->execute('index.php?module=bestellung&action=edit&id=' . $id . "&msg=" . $msg);
              }
            }
            $this->app->erp->BestellungProtokoll($id,"Bestellung manuell eingelagert");
            $this->checkAbschliessen($id);
            if($intern) {
              return true;
            }
            $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Bestellung wurde eingelagert!</div>");
            $this->app->Location->execute('index.php?module=bestellung&action=edit&id=' . $id . "&msg=" . $msg);
          }
        }
      }
      if($intern) {
        return false;
      }
      $this->app->Location->execute('index.php?module=bestellung&action=edit&id='.$id);
    }
    if($intern) {
      return false;
    }
    $this->app->Location->execute('index.php?module=bestellung&action=list');
  }

  function checkAbschliessen($id = 0)
  {
    if(!$id) {
      $id = $this->app->Secure->GetGET('id');
    }
    $id = (int)$id;
    if($id <= 0) {
      return;
    }
    $status = $this->app->DB->Select("SELECT status FROM bestellung WHERE id = '$id' LIMIT 1");
    if(($status === 'versendet' || $status === 'freigegeben'))
    {
      $sql = "SELECT bp.id FROM bestellung_position bp LEFT JOIN artikel a ON a.id=bp.artikel WHERE bp.bestellung = '$id' AND a.lagerartikel=1";
      $alleLagerArtikelVonBestellung = $this->app->DB->SelectArr($sql);

      //nur wenn auch Lagerartikel enthalten sind, soll automatisch abgeschlossen werden können.
      if(count($alleLagerArtikelVonBestellung)>0){
        $lagerArtikelIds = [];
        foreach ($alleLagerArtikelVonBestellung as $l){
          $lagerArtikelIds[] = $l['id'];
        }
        $sql = "SELECT bp.id FROM bestellung_position bp LEFT JOIN artikel a ON a.id=bp.artikel WHERE bp.geliefert < bp.menge AND bp.id IN (".implode(',',$lagerArtikelIds).")";
        $offenePositionen = $this->app->DB->SelectArr($sql);

        if(empty($offenePositionen)){
          $this->BestellungAbschliessen($id,true);
        }
      }
    }
  }

  function BestellungOffenePositionen()
  {
    $this->BestellungListMenu();

    $this->app->YUI->TableSearch('TAB1','bestellung_offenepositionen');
    $this->app->Tpl->Parse('PAGE','bestellung_offenepositionen.tpl');
  }
  
  function BestellungArchivierePDF()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $projektbriefpapier = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id = '$id' LIMIT 1");
    if(class_exists('BestellungPDFCustom'))
    {
      $Brief = new BestellungPDFCustom($this->app,$projektbriefpapier);
    }else{
      $Brief = new BestellungPDF($this->app,$projektbriefpapier);
    }
    $Brief->GetBestellung($id);
    $tmpfile = $Brief->displayTMP();
    $Brief->ArchiviereDocument(1);
    unlink($tmpfile);
    $this->app->DB->Update("UPDATE bestellung SET schreibschutz='1' WHERE id='$id'");
    $this->app->Location->execute('index.php?module=bestellung&action=edit&id='.$id);
  }
  
  function BestellungEditable()
  {
    $this->app->YUI->AARLGEditable();
  }

  function BestellungSchreibschutz()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $this->app->DB->Update(sprintf('UPDATE bestellung SET zuarchivieren=1, schreibschutz = 0 WHERE id=%d', $id));
    $this->app->erp->BestellungProtokoll($id,'Schreibschutz entfernt');
    $this->app->Location->execute('index.php?module=bestellung&action=edit&id='.$id);
  }

  function BestellungPDFfromArchiv()
  {
    $id = $this->app->Secure->GetGET('id');
    $archiv = $this->app->DB->Select("SELECT table_id from pdfarchiv where id = '$id' LIMIT 1");
    if($archiv)
    {
      $projekt = $this->app->DB->Select("SELECT projekt from bestellung where id = '".(int)$archiv."'");
    }
    if(class_exists('BestellungPDFCustom'))
    {
      if($archiv) {
        $Brief = new BestellungPDFCustom($this->app,$projekt);
      }
    }else{
      if($archiv) {
        $Brief = new BestellungPDF($this->app,$projekt);
      }
    }
    if($archiv && $content = $Brief->getArchivByID($id))
    {
      header('Content-type: application/pdf');
      header('Content-Disposition: attachment; filename="'.$content['belegnr'].'.pdf"');
      echo $content['file'];
      $this->app->ExitXentral();
    }
    header('Content-type: application/pdf');
    header('Content-Disposition: attachment; filename="Fehler.pdf"');
    $this->app->ExitXentral();
  }  
  
  function BestellungMiniDetail($parsetarget='',$menu=true)
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'lager')
    {
      if($this->app->DB->Select("SELECT id FROM artikel WHERE id = '$id' AND lagerartikel = 1 LIMIT 1"))
      {
        $table = new EasyTable($this->app);
        $table->Query("SELECT ".$this->app->erp->FormatMenge("sum(lpi.menge)")." as menge, l.bezeichnung as Lager 
        FROM lager l 
        INNER JOIN lager_platz lp ON l.id = lp.lager AND l.geloescht = 0 
        INNER JOIN lager_platz_inhalt lpi ON lp.id = lpi.lager_platz AND lpi.artikel = $id 
        GROUP BY l.id
        ORDER BY l.bezeichnung");
        $table->align[0] = 'right';
        $table->align[1] = 'left';
        echo json_encode(array('inhalt'=>'<div class="inlinetooltiptable"><style> div.inlinetooltiptable > table.mkTable > tbody > tr:nth-child(2n+1) td {background-color:#e0e0e0;} </style>'.$table->DisplayNew('return','Lager','noAction').'</div>'));
      }else{
        echo json_encode(array('inhalt'=>'<div class="inlinetooltiptable">kein Lagerartikel</div>'));
      }
      $this->app->ExitXentral();
    }

    if($cmd === 'zuordnungAuftragZuBestellung')
    {
      $bid = $this->app->Secure->GetGet("id");
      $belegnummer = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$bid'");
      $auftragPost = explode(" ",$this->app->Secure->GetPOST("auftrag"));
      $auftragsnummer = $auftragPost[0];

      $auftrag_positionen = $this->app->DB->SelectArr("SELECT ap.id,ap.artikel,ap.menge FROM auftrag_position ap LEFT JOIN auftrag a on a.id=ap.auftrag WHERE a.belegnr='$auftragsnummer' AND a.belegnr!=''");
       foreach($auftrag_positionen as $value){
        $a_positionen[$value['artikel']]['id'] = $value['id'];
        $a_positionen[$value['artikel']]['menge'] = $value['menge'];
      }
      //jetzt holen wir alle Bestellpositonen für diese Bestellung
      $bestellung_positionen = $this->app->DB->SelectArr("SELECT bp.id,bp.artikel,bp.menge FROM bestellung_position bp WHERE bp.bestellung='$bid'");
        foreach($bestellung_positionen as $position){
          // prüfen ob beide artikel gleich sind und ob die menge passt
          if( (is_array($a_positionen[$position['artikel']]))  &&  ($position['menge'] == $a_positionen[$position['artikel']]['menge']) )  {
            // wenn wir den Artikel haben und die Anzahl passt
            $wert = $a_positionen[$position['artikel']]['id'];
            $this->app->DB->Update("UPDATE bestellung_position SET auftrag_position_id='$wert' WHERE id='{$position['id']}' AND bestellung='$bid' LIMIT 1");
          } else{
            // wenn wir den Artikel haben aber die Anzahl nicht passt nehmen wir die Position vom ersten Auffinden des Artikel
            if($position['artikel'] == $a_positionen[$position['artikel']]){
              $wert = $a_positionen[$position['artikel']]['id'];

              $this->app->DB->Update("UPDATE bestellung_position SET auftrag_position_id='$wert' WHERE id='{$position['id']}' AND bestellung='$bid' LIMIT 1");
            }else{
              // wenn wir keinen Artikel haben nehmen wir den ersten Artikel zum verknüpfen
              $wert = $a_positionen[0]['id'];
              $this->app->DB->Update("UPDATE bestellung_position SET auftrag_position_id='$wert' WHERE id='{$position['id']}' AND bestellung='$bid' LIMIT 1");
            }
          }
        }

      $zugeordnet = $this->app->DB->Select("SELECT count(id) FROM bestellung_position WHERE bestellung='$bid' AND auftrag_position_id != '0'");;
      $nichtzugeordnet = $this->app->DB->Select("SELECT count(id) FROM bestellung_position WHERE bestellung='$bid' AND auftrag_position_id = '0'");
      $gesamtpositionen = $this->app->DB->Select("SELECT count(id) FROM bestellung_position WHERE bestellung='$bid'");

      $data['gesamtpositionen'] = $gesamtpositionen;
      $data['belegnummer'] = $belegnummer;
      $data['zugeordnet'] = $zugeordnet;
      $data['nichtzugeordnet'] = $nichtzugeordnet;
      $data['error'] = false;
      echo json_encode($data);
      $this->app->ExitXentral();
    }


    if($cmd === 'checkmenge')
    {
      $bpid = $this->app->Secure->GetPOST("bp");
      $ab_menge = round(str_replace(',','.',$this->app->Secure->GetPOST("ab_menge")),8);
      $data = $this->app->DB->SelectArr("SELECT *, TRIM(menge)+0 as menge FROM bestellung_position WHERE id = '$bpid' LIMIT 1");
      if($data)
      {
        $data = reset($data);
        $bestellung = $data['bestellung'];
        $adresse = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id = '$bestellung' LIMIT 1");
        $ek = $this->app->erp->GetEinkaufspreisArr($data['artikel'], $ab_menge, $adresse, $data['waehrung']);
        if($ek)
        {
          $data = array('menge'=>$ab_menge, 'ab_menge'=>round($ek['ab_menge'],8));
        }else{
          $data = array('menge'=>$ab_menge, 'ab_menge'=>$ab_menge);
        }
      }
      echo json_encode($data);
      $this->app->ExitXentral();
    }
    if($cmd === 'getpreis')
    {
      $bpid = $this->app->Secure->GetPOST("bp");
      $data = $this->app->DB->SelectArr("SELECT *, TRIM(menge)+0 as menge FROM bestellung_position WHERE id = '$bpid' LIMIT 1");
      if($data)
      {
        $data = reset($data);
        $data['menge'] = round((float)$data['menge'], 8);
        $bestellung = $data['bestellung'];
        $adresse = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id = '$bestellung' LIMIT 1");
        $ek = $this->app->erp->GetEinkaufspreisArr($data['artikel'], $data['menge'], $adresse, $data['waehrung']);
        if($ek)$data['ab_menge'] = round($ek['ab_menge'], 8);
        $data['preis'] = number_format(round($data['preis'],8),2,',','.');
        $data['auchinstammdaten'] = $this->app->User->GetParameter('bestellung_auchinstammdaten')?1:0;
      }
      echo json_encode($data);
      $this->app->ExitXentral();
    }
    if($cmd === 'savepreis')
    {
      $bpid = (int)$this->app->Secure->GetPOST('bp');
      $bpRow = $this->app->DB->SelectRow(
        sprintf(
          'SELECT artikel, bestellung FROM bestellung_position WHERE id = %d LIMIT 1'
          ,$bpid
        )
      );
      $artikel = $bpRow['artikel'];
      $preis = str_replace(',','.',$this->app->Secure->GetPOST('preis'));
      $auchinstammdaten = $this->app->Secure->GetPOST('auchinstammdaten');
      $this->app->User->SetParameter('bestellung_auchinstammdaten', $auchinstammdaten);
      $waehrung = $this->app->Secure->GetPOST('waehrung');
      $bestellnummer = $this->app->Secure->GetPOST('bestellnummer');
      $bezeichnung = $this->app->Secure->GetPOST('bezeichnung');
      $ab_menge = str_replace(',','.',$this->app->Secure->GetPOST('ab_menge'));
      $menge = str_replace(',','.',$this->app->Secure->GetPOST('menge'));
      $bestellung = $bpRow['bestellung'];
      
      // Schreibschutz entfernen
      $this->app->DB->Update("UPDATE bestellung SET schreibschutz=0 WHERE id='$bestellung' LIMIT 1");
      $bRow = $this->app->DB->SelectRow(
        sprintf(
          'SELECT schreibschutz, adresse FROM bestellung WHERE id=%d LIMIT 1',
          (int)$bestellung
        )
      );
      $schreibschutz = $bRow['schreibschutz'];
      $adresse = $bRow['adresse'];
      if(!$schreibschutz)
      {
        $this->app->DB->Update(
          sprintf(
            'UPDATE bestellung_position SET preis = %f WHERE id = %d LIMIT 1',
            (float)$preis, $bpid
          )
        );
        if((String)$bestellnummer !== '') {
          $this->app->DB->Update(
            sprintf(
              "UPDATE bestellung_position SET bestellnummer = '%s' WHERE id = %d LIMIT 1",
              $bestellnummer, $bpid
            )
          );
        }
        if((String)$bezeichnung !== '') {
          $this->app->DB->Update(
            sprintf(
              "UPDATE bestellung_position SET bezeichnunglieferant = '%s' WHERE id = %d LIMIT 1",
              $bezeichnung, $bpid
            )
          );
        }
        if($menge) {
          $this->app->DB->Update(
            sprintf(
              "UPDATE bestellung_position SET menge = %f WHERE id = %d LIMIT 1",
              (float)$menge, $bpid
            )
          );
        }
        $this->app->erp->ANABREGSNeuberechnen($bestellung,'bestellung');
      }
      if($auchinstammdaten && $artikel && $adresse)
      {
        $this->app->erp->AddEinkaufspreis($artikel,$ab_menge,$adresse,$bestellnummer,$bezeichnung, $preis, $waehrung);
      }
      $data = $this->app->DB->SelectRow(
        sprintf(
          'SELECT id, %s as preis, bestellnummer, trim(menge)+0 as menge 
            FROM bestellung_position 
            WHERE id = %d 
            LIMIT 1',
          $this->app->erp->FormatPreis('preis'),$bpid
        )
      );
      if(!empty($data))
      {
        $data['menge'] = round($data['menge'], 8);
        $data['preis'] .= '&nbsp;<a href="#" onclick="changepreis'.$this->app->Secure->getPOST('md5').'('.$bpid.');"><img src="themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/edit.svg" border="0"></a>';
      }
      echo json_encode($data);
      $this->app->ExitXentral();
    }
    $bestRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT belegnr, name, status, zahlungsweise, adresse, verbindlichkeiteninfo,preisanfrageid, 
          DATE_FORMAT(gewuenschteslieferdatum,\'%%d.%%m.%%Y\') as gewuenschteslieferdatum, bestellungbestaetigtabnummer,
          DATE_FORMAT(bestaetigteslieferdatum,\'%%d.%%m.%%Y\') as bestaetigteslieferdatum, datum, projekt
          FROM bestellung 
          WHERE id=%d 
          LIMIT 1',
        $id
      )
    );
    $belegnr = $bestRow['belegnr'];
    $name = $bestRow['name'];
    $status = $bestRow['status'];
    //$schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM bestellung WHERE id='$id' LIMIT 1");
    $zahlweise = $bestRow['zahlungsweise'];
    $bestaetigteslieferdatum = $bestRow['bestaetigteslieferdatum'];
    $wunschlieferdatum = $bestRow['gewuenschteslieferdatum'];
    $ablieferant = $bestRow['bestellungbestaetigtabnummer'];
    $verbindlichkeiteninfo = $bestRow['verbindlichkeiteninfo'];
    $preisanfrageid = $bestRow['preisanfrageid'];
    $preisanfrage = $this->app->DB->Select("SELECT belegnr FROM preisanfrage WHERE id = '$preisanfrageid' LIMIT 1");
    $adresse = (int)$bestRow['adresse'];
    $lieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id = '$adresse' LIMIT 1");
    $datum = (int)$bestRow['datum'];
    $projekt = (int)$bestRow['projekt'];
    $this->app->Tpl->Set('VERBINDLICHKEITENINFO',$verbindlichkeiteninfo);

    if($belegnr=='0' || $belegnr=='') {
      $belegnr = 'ENTWURF';
    }

    $this->app->Tpl->Set('BELEGNR',$belegnr);
    $this->app->Tpl->Set('LIEFERANT',"<a href=\"index.php?module=adresse&action=edit&id=".$adresse."\" target=\"_blank\">".$lieferantennummer."</a> ".$name);
    $this->app->Tpl->Set('STATUS',$status);
    $this->app->Tpl->Set('ZAHLWEISE',$zahlweise);
    $this->app->Tpl->Set('BESTELLUNGID',$id);
    if($preisanfrageid > 0)
    {
      $this->app->Tpl->Set('PREISANFRAGE','<a href="index.php?module=preisanfrage&action=edit&id='.$preisanfrageid.'" target="_blank">'.$preisanfrage.'</a>&nbsp;<a href="index.php?module=preisanfrage&action=pdf&id='.$preisanfrageid.'" target="_blank"><img src="./themes/new/images/pdf.svg" title="Preisanfrage PDF" border="0" target="_blank"></a>&nbsp;<a href="index.php?module=preisanfrage&action=edit&id='.$preisanfrageid.'" target="_blank"><img src="./themes/new/images/edit.svg" title="Preisanfrage bearbeiten" border="0" target="_blank"></a>');
    }

    if($bestaetigteslieferdatum=="00.00.0000") $bestaetigteslieferdatum="warte auf Datum";
    $this->app->Tpl->Set('BESTAETIGTESLIEFERDATUM',$bestaetigteslieferdatum);

    if($wunschlieferdatum=="00.00.0000") $wunschlieferdatum="warte auf Datum";
    $this->app->Tpl->Set('WUNSCHLIEFERDATUM',$wunschlieferdatum);
    $this->app->Tpl->Set('ABLIEFERANT',$ablieferant);

    $nettogewicht = $this->app->erp->BestellungNettoGewicht($id);
    if($nettogewicht!='') {
      $nettogewicht = number_format($nettogewicht, 2, ',','.');
      $gewichtbezeichnung = $this->app->erp->Firmendaten('gewichtbezeichnung');
      if($gewichtbezeichnung == '') {
        $gewichtbezeichnung = 'Kg';
      }

      $this->app->Tpl->Set("GEWICHT", $nettogewicht . " ".$gewichtbezeichnung);
    }

    $projektabkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projekt' LIMIT 1");
    if($this->app->erp->RechteVorhanden("projekt","dashboard"))
      $this->app->Tpl->Set('PROJEKT',"<a href=\"index.php?module=projekt&action=dashboard&id=".$projekt."\" target=\"_blank\">$projektabkuerzung</a>");
    else
      $this->app->Tpl->Set('PROJEKT',$projekt);

    $md5 = md5(microtime(true));
    
    $table = new EasyTable($this->app);

    $anzahlzeichen = 200;
    $artikelIdList = [];
    $artikelArr = $this->app->DB->SelectArr(sprintf('SELECT DISTINCT artikel FROM bestellung_position WHERE bestellung = %d', $id));
    if(!empty($artikelArr))
    {
      foreach($artikelArr as $row)
      {
        $artikelIdList[] = $row['artikel'];
      }
    }
    $table->Query("SELECT CONCAT(SUBSTRING(ap.bezeichnunglieferant,1,$anzahlzeichen),'<br>Best-Nr.:<span id=\"spanbestellnummer".$md5."',ap.id,'\">',ap.bestellnummer,'</span>') as artikel, 
        CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',a.id,'\"  target=\"_blank\">',a.nummer,'</a>') as nummer, 
        CONCAT('<span id=\"spanmenge".$md5."',ap.id,'\">',(TRIM(ap.menge)+0),'</span>') as Menge,
        TRIM(ap.geliefert)+0 as geliefert,
        IF(a.lagerartikel = 1,CONCAT( ".$this->app->erp->FormatMenge('IFNULL(lag.menge,0)').",'lagermehr(',ap.artikel,')'),'-') as Lager,
        if(ap.lieferdatum!='0000-00-00',DATE_FORMAT(ap.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum, 
        concat('<span id=\"spanpreis".$md5."',ap.id,'\">',FORMAT(ap.preis,2,'de_DE'),'".
        "&nbsp;<a href=\"#\" onclick=\"changepreis".$md5."(',ap.id,');\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>"
        ."','</span>') as preis
        FROM bestellung_position AS ap
        INNER JOIN artikel AS a ON ap.artikel = a.id
        LEFT JOIN (
          SELECT SUM(lpi.menge) as menge, lpi.artikel 
          FROM lager_platz_inhalt AS lpi
          INNER JOIN lager_platz AS lp ON lpi.lager_platz = lp.id AND IFNULL(lp.sperrlager,0) = 0 AND IFNULL(lp.autolagersperre,0) = 0
          ".(!empty($artikelIdList)?' AND lpi.artikel IN ('.implode(',', $artikelIdList).')':'')."
          GROUP BY lpi.artikel
        ) as lag ON a.id = lag.artikel 
        WHERE ap.bestellung='$id' 
        ORDER by ap.sort");
    foreach($table->datasets as $tablerowKey => $tableRow)
    {
      foreach($tableRow as $columName =>  $columnValue)
      {
        if(preg_match_all('/^(.*)lagermehr\((.*)\)(.*)$/', $columnValue, $matches,PREG_OFFSET_CAPTURE))
        {
          $table->datasets[$tablerowKey][$columName] = $matches[1][0][0].'&nbsp;'.$this->app->YUI->ContentTooltip('return','index.php?module=auftrag&action=minidetail&cmd=lager&id='.$matches[2][0][0],'url').$matches[3][0][0];
        }
      }
    }

    foreach($table->datasets as $k => $row)
    {
      if(strip_tags($table->datasets[$k]['geliefert']) == strip_tags($table->datasets[$k]['Menge']))
      {
        $table->datasets[$k]['geliefert'] = '<b>'.$table->datasets[$k]['geliefert'].'</b>';
        $table->datasets[$k]['Menge'] = '<b>'.$table->datasets[$k]['Menge'].'</b>';
        
      } else {
        $table->datasets[$k]['geliefert'] = '<b style="color:red;">'.$table->datasets[$k]['geliefert'].'</b>';
        $table->datasets[$k]['Menge'] = '<b style="color:red;">'.$table->datasets[$k]['Menge'].'</b>';
      }
      
      
      $table->datasets[$k]['preis'] = '<div style="float:right;text-align:right;"><b>'.$table->datasets[$k]['preis'];
      if(preg_match("/&id=([0-9]*)/",$row['nummer'],$treffer)){
        $artid = (int)$treffer[1];
        if($epreise = $this->app->DB->SelectArr("SELECT ab_menge, preis FROM einkaufspreise WHERE artikel='".$artid."' AND adresse='$adresse' AND (gueltig_bis>='".$datum."' OR gueltig_bis='0000-00-00') and (preis_anfrage_vom = '0000-00-00' or preis_anfrage_vom <= '".$datum."') and ab_menge > ".(int)strip_tags($row['Menge'])." order by ab_menge"))
        {
          $table->datasets[$k]['preis'] .= '</b>&nbsp;&nbsp;<br /><table style="float:right;">';
          foreach($epreise as $key => $pr)
          {
            $table->datasets[$k]['preis'] .= "<tr style=\"background-color: transparent;text-align:right;\"><td><small>(".floatval($pr['ab_menge']).":</small></td><td><small>".number_format($pr['preis'],2,',','.').")</small></td></tr>";
            
          }
          $table->datasets[$k]['preis'] .= '</table>';
        }
      } else {
        $table->datasets[$k]['preis'] .= '</b>';
      }
      $table->datasets[$k]['preis'] .= "</div>";
      
    }

    $check = $this->app->DB->SelectArr("SELECT a.belegnr, a.id  
    FROM auftrag_position ap
    INNER JOIN auftrag a ON ap.auftrag = a.id
    INNER JOIN bestellung_position bp ON ap.id = bp.auftrag_position_id
    WHERE bp.bestellung='$id' GROUP BY a.belegnr, a.id ORDER BY a.belegnr, a.id");
    if($check)
    {
      $auftraege = null;
      $lieferscheine = [];
      $rechnungen = [];
      foreach($check as $row)
      {
        $auftraege[] = '<a href="index.php?module=auftrag&action=edit&id='.$row['id'].'" target="_blank">'.($row['belegnr']?$row['belegnr']:'Entwurf').'</a>
                            <a href="index.php?module=auftrag&action=pdf&id='.$row['id'].'" target="_blank"><img src="./themes/new/images/pdf.svg" title="Auftrag PDF" border="0"></a>
                            <a href="index.php?module=auftrag&action=edit&id='.$row['id'].'" target="_blank"><img src="./themes/new/images/edit.svg" title="Auftrag bearbeiten" border="0"></a>';

        $lieferscheineTemp = $this->app->DB->SelectPairs(
          "SELECT 
            l.id, 
            CONCAT(
            '<a href=\"index.php?module=lieferschein&action=edit&id=',
            l.id,'\" target=\"_blank\"',
            if(l.status='storniert',' title=\"Lieferschein storniert\"><s>','>'),
            if(l.belegnr='0' OR l.belegnr='','ENTWURF',l.belegnr),
            if(l.status='storniert','</s>',''),
            '</a>&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=',l.id,
            '\" target=\"_blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Lieferschein PDF\" border=\"0\"></a>&nbsp;
            <a href=\"index.php?module=lieferschein&action=edit&id=',
            l.id,
            '\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Lieferschein bearbeiten\" border=\"0\"></a>'
            ) as lieferschein
            FROM lieferschein l 
            WHERE l.auftragid='".$row['id']."'"
        );

        if(!empty($lieferscheineTemp)){
          foreach ($lieferscheineTemp as $k => $v){
            $lieferscheine[$k] = $v;
          }
        }

        $rechnungenTemp = $this->app->DB->SelectPairs(
          "SELECT 
          r.id, 
          CONCAT(
          '<a href=\"index.php?module=rechnung&action=edit&id=',
          r.id,
          '\" target=\"_blank\"',
          if(r.status='storniert',' title=\"Rechnung storniert\"><s>','>'),
          if(r.belegnr='0' OR r.belegnr='','ENTWURF',r.belegnr),
          if(r.status='storniert','</s>',''),
          '</a>&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=',
          r.id,
          '\" target=\"_blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Rechnung PDF\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=rechnung&action=edit&id=',
          r.id,
          '\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Rechnung bearbeiten\" border=\"0\"></a>'
          ) as rechnung
          FROM rechnung r 
          WHERE r.auftragid='".$row['id']."'"
        );

        if(!empty($rechnungenTemp)){
          foreach ($rechnungenTemp as $k => $v){
            $rechnungen[$k] = $v;
          }
        }
      }

      if(!empty($auftraege)){
        $this->app->Tpl->Set('AUFTRAG', implode('<br />', $auftraege));
      }

      if(!empty($lieferscheine)){
        $this->app->Tpl->Set('LIEFERSCHEIN', implode('<br />', $lieferscheine));
      }

      if(!empty($rechnungen)){
        $this->app->Tpl->Set('RECHNUNG', implode('<br />', $rechnungen));
      }
    }

    // $nowarp-Parameter (letzter Parameter) auf false setzen, damit lange Artikeltitel umbrechen können
    $artikel = $table->DisplayNew("return", "Preis", "noAction", false, 0, 0, false);

    if($menu)
    {
      $menu = $this->BestellungIconMenu($id);
      $this->app->Tpl->Set('MENU',$menu);
    }

    $this->app->Tpl->Set('ARTIKEL',$artikel);


    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM bestellung_protokoll WHERE bestellung='$id' ORDER by id DESC");
    $tmp->DisplayNew('PROTOKOLL',"Protokoll","noAction");

    $this->app->Tpl->Set('RECHNUNGLIEFERADRESSE',$this->BestellungRechnungsLieferadresse($id));
    
    if(class_exists('BestellungPDFCustom'))
    {
      $Brief = new BestellungPDFCustom($this->app,$projekt);
    }else{
      $Brief = new BestellungPDF($this->app,$projekt);
    }
    
    $Dokumentenliste = $Brief->getArchivedFiles($id, 'bestellung');
    if(!empty($Dokumentenliste))
    {
      $tmp3 = new EasyTable($this->app);
      $tmp3->headings = array('Datum','Belegnr','Bearbeiter','Men&uuml;');
      foreach($Dokumentenliste as $k => $v)
      {
        if(!$v['erstesoriginal'])
        {
          $tmpr['datum'] = date('d.m.Y H:i:s',strtotime($v['zeitstempel']));
          $tmpr['belegnr'] = str_replace('.pdf','',$v['file']);
          $tmpr['belegnr'] = substr($tmpr['belegnr'],strrpos($tmpr['belegnr'],'_')+1);
          if(isset($v['belegnummer']) && $v['belegnummer']) {
            $tmpr['belegnr'] = $v['belegnummer'];
          }
          $tmpr['bearbeiter'] = $v['bearbeiter'];
          $tmpr['menu'] = '<a href="index.php?module=bestellung&action=pdffromarchive&id='.$v['id'].'"><img src="themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/pdf.svg" /></a>';
          $tmp3->datasets[] = $tmpr;
        }
      }
      
      $tmp3->DisplayNew('PDFARCHIV','Men&uuml;',"noAction");
    }

    $wareneingangsbelege = $this->app->DB->SelectFirstCols("SELECT 
        CONCAT('<a href=\"index.php?module=receiptdocument&action=edit&id=',r.id,'\" target=\"_blank\"',if(r.status='storniert',' title=\"Wareneingangsbeleg storniert\"><s>','>'),if(r.document_number='0' OR document_number='','ENTWURF',r.document_number),if(r.status='storniert','</s>',''),'</a>&nbsp;<a href=\"index.php?module=receiptdocument&action=pdf&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/pdf.svg\" title=\"Wareneingangsbeleg PDF\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=receiptdocument&action=edit&id=',r.id,'\" target=\"_blank\"><img src=\"./themes/new/images/edit.svg\" title=\"Wareneingangsbeleg bearbeiten\" border=\"0\"></a>') as wareneingangsbeleg
        FROM receiptdocument r WHERE r.supplier_order_id='$id'");

    if(!empty($wareneingangsbelege)){
      $this->app->Tpl->Add('WARENEINGANGSBELEG', implode('<br />', $wareneingangsbelege));
    }
    else{
      $this->app->Tpl->Set('WARENEINGANGSBELEG', '-');
    }
    

    $this->app->Tpl->Set('ID', $id);
    $this->app->Tpl->Set('MD5', $md5);
    $this->app->Tpl->Parse('ARTIKEL', 'bestellung_minidetail_popup.tpl');
   
    
    if($parsetarget=='')
    {
      $this->app->Tpl->Output("bestellung_minidetail.tpl");
      $this->app->ExitXentral();
    }
    $this->app->Tpl->Parse($parsetarget,"bestellung_minidetail.tpl");
  }


  function BestellungAlsversendet($id = 0)
  {
    if($id)
    {
      $intern = true;
    }else{
      $intern = false;
      $id = $this->app->Secure->GetGET('id');
    }

    if($id > 0)
    {
      $this->app->DB->Update("UPDATE bestellung SET status='versendet' WHERE id='$id' LIMIT 1");
      $this->app->erp->BestellungProtokoll($id,"Bestellung als versendet markiert");
    }
    if($intern) {
      return;
    }
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Bestellung wurde als versendet markiert!</div>");
    $this->app->Location->execute('index.php?module=bestellung&action=list&msg='.$msg);
  }

  function BestellungAbschliessen($id = 0,$auto=false)
  {
    if($id)
    {
      $intern = true;
    }else{
      $intern = false;
      $id = (int)$this->app->Secure->GetGET('id');
    }

    if($id > 0)
    {
      $this->app->DB->Update("UPDATE bestellung SET status='abgeschlossen',schreibschutz=1 WHERE id='$id' LIMIT 1");
      $this->app->DB->Update("UPDATE verbindlichkeit SET freigabe='1' WHERE bestellung='$id'");
      $this->app->erp->BestellungProtokoll($id,'Bestellung '.($auto?'automatisch ':'').'abgeschlossen');
      $this->app->erp->RunHook("bestellung_abschliessen",1,$id);
    }
    if($intern) {
      return;
    }
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Bestellung wurde als abgeschlossen markiert!</div>");
    $this->app->Location->execute('index.php?module=bestellung&action=list&msg='.$msg);
  }

  function BestellungFreigegeben($id = '')
  {
    if($id<=0){
      $intern = false;
      $id = (int)$this->app->Secure->GetGET('id');
    }else{
      $intern = true;
    }
    if($id)
    {
      if($this->app->DB->Select("SELECT id FROM bestellung WHERE status = 'abgeschlossen' AND id = '$id' LIMIT 1"))
      {
        $this->app->DB->Update("UPDATE bestellung SET status = 'freigegeben', schreibschutz = 0 WHERE id = '$id' LIMIT 1");
        $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Die Bestellung wurde auf freigegeben gesetzt!</div>");
      }else{
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Die Bestellung ist nicht abgeschlossen!</div>");
      }
    }else{
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Es wurde keine Bestellung angeben!</div>");
      $this->app->Location->execute('index.php?module=bestellung&action=list&msg='.$msg);
    }
    if($intern)
    {
      return $id;
    }
    $this->app->Location->execute('index.php?module=bestellung&action=edit&id='.$id.'&msg='.$msg);
  }

  function BestellungFreigabe($id='')
  {
    if($id<=0)
    {
      $intern = false;
      $id = (int)$this->app->Secure->GetGET('id');
      $freigabe= $this->app->Secure->GetGET('freigabe');
      $weiter= $this->app->Secure->GetPOST('weiter');
      $this->app->Tpl->Set('TABTEXT','Freigabe');
    } else {
      $intern = true;
      $freigabe=$intern;
    }
    $allowedFrm = true;
    $showDefault = true;
    $doctype = 'bestellung';
    if(empty($intern)){
      $this->app->erp->RunHook('beleg_freigabe', 4, $doctype, $id, $allowedFrm, $showDefault);
    }
    if($weiter!='')
    {
      $this->app->Location->execute('index.php?module=bestellung&action=abschicken&id='.$id);
    }
    if($allowedFrm && $freigabe==$id)
    {
      //$projekt = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id='$id' LIMIT 1");
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
      if($belegnr=='')
      {
        $this->app->erp->BelegFreigabe('bestellung',$id);
        if($intern) {
          return 1;
        }
        $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Die Bestellung wurde freigegeben und kann jetzt versendet werden!</div>");
        $this->app->Location->execute("index.php?module=bestellung&action=edit&id=$id&msg=$msg");
      }
      if($intern) {
        return 0;
      }
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Die Bestellung wurde bereits freigegeben!</div>");
      $this->app->Location->execute("index.php?module=bestellung&action=edit&id=$id&msg=$msg");
    }
    if($showDefault){
      $name = $this->app->DB->Select("SELECT a.name FROM bestellung b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
      $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM bestellung_position
        WHERE bestellung='$id'");
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM bestellung_position
        WHERE bestellung='$id' LIMIT 1");

      $summe = $this->app->erp->EUR($summe);

      $extra = $this->app->erp->CheckboxEntwurfsmodus("bestellung", $id);


      if($this->app->erp->Firmendaten("oneclickrelease")=="1" && $extra=="")
      {
        $this->app->Location->execute("index.php?module=bestellung&action=freigabe&id=$id&freigabe=$id");
      } else {
        $this->app->Tpl->Set('TAB1', "<div class=\"info\">Soll die Bestellung an <b>$name</b> im Wert von <b>$summe $waehrung</b> 
        jetzt freigegeben werden? <input type=\"button\" class=\"btnImportantLarge\" value=\"Jetzt freigeben\" onclick=\"window.location.href='index.php?module=bestellung&action=freigabe&id=$id&freigabe=$id'\">&nbsp;$extra
        </div>");
      }
    }
    $this->BestellungMenu();
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }


  function BestellungCopy()
  {
    $id = $this->app->Secure->GetGET('id');
    $newid = $this->app->erp->CopyBestellung($id);

    $this->app->Location->execute('Location: index.php?module=bestellung&action=edit&id='.$newid);
  }

  function AdresseBestellungCopy()
  {
    $id = $this->app->Secure->GetGET('id');
    $newid = $this->app->erp->CopyBestellung($id);

    echo json_encode(array('status'=>1, 'newid'=>$newid));
    $this->app->ExitXentral();
  }


  function BestellungLiveTabelle()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $status = $this->app->DB->Select("SELECT status FROM bestellung WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    if($status==='freigegeben')
    {
      $anzahlzeichen = 200;
      $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,$anzahlzeichen) as artikel, ap.nummer as Nummer, ap.menge as M,
          if(a.porto,'-',if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel) > ap.menge,(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),
              if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel)>0,CONCAT('<font color=red><b>',(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),'</b></font>'),
                '<font color=red><b>aus</b></font>'))) as L
          FROM bestellung_position ap, artikel a WHERE ap.bestellung='$id' AND a.id=ap.artikel");
      $artikel = $table->DisplayNew("return","A","noAction");
    } else {
      $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,$anzahlzeichen) as artikel, ap.nummer as Nummer, ap.menge as M
          FROM bestellung_position ap, artikel a WHERE ap.bestellung='$id' AND a.id=ap.artikel");
      $artikel = $table->DisplayNew("return","Menge","noAction");
    }
    echo $artikel;
    $this->app->ExitXentral();
  }


  function BestellungAuftrag()
  {
    $id = $this->app->Secure->GetGET('id');

    $newid = $this->app->erp->WeiterfuehrenBestellungZuAuftrag($id);

    $this->app->Location->execute('index.php?module=auftrag&action=edit&id='.$newid);
  }


  function BestellungAbschicken()
  {
    $this->BestellungMenu();
    $this->app->erp->DokumentAbschicken();
  }

  function BestellungDelete()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM bestellung WHERE id='$id' LIMIT 1");
    $status = $this->app->DB->Select("SELECT status FROM bestellung WHERE id='$id' LIMIT 1");

    if($belegnr=='0' || $belegnr=='')
    {
      $this->app->erp->DeleteBestellung($id);
      $belegnr='ENTWURF';
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Die Bestellung \"$name\" ($belegnr) wurde gel&ouml;scht!</div>");
      //header("Location: ".$_SERVER['HTTP_REFERER']."&msg=$msg");
      $this->app->Location->execute('index.php?module=bestellung&action=list&msg='.$msg);
    }

    if($status==='storniert')
    {
      $maxbelegnr = $this->app->DB->Select("SELECT MAX(belegnr) FROM bestellung");
      if(0)//$maxbelegnr == $belegnr)
      {
        $this->app->DB->Delete("DELETE FROM bestellung_position WHERE bestellung='$id'");
        $this->app->DB->Delete("DELETE FROM bestellung_protokoll WHERE bestellung='$id'");
        $this->app->DB->Delete("DELETE FROM bestellung WHERE id='$id'");
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Rechnung \"$name\" ($belegnr) wurde ge&ouml;scht !</div>");
      } else
      {
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Bestellung \"$name\" ($belegnr) kann nicht storniert werden da sie bereits storniert ist!</div>");
      }
      $this->app->Location->execute('index.php?module=bestellung&action=list&msg='.$msg);
    }

    $this->app->DB->Update("UPDATE bestellung SET status='storniert' WHERE id='$id' LIMIT 1");
    $this->app->erp->BestellungProtokoll($id,"Bestellung storniert");
    $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Die Bestellung \"$name\" ($belegnr) wurde storniert!</div>");
    $this->app->Location->execute("Location: index.php?module=bestellung&action=list&msg=$msg#tabs-1");
  }

  function BestellungProtokoll()
  {
    $this->BestellungMenu();
    $id = (int)$this->app->Secure->GetGET('id');

    $this->app->Tpl->Set('TABTEXT','Protokoll');
    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM bestellung_protokoll WHERE bestellung=$id ORDER by zeit DESC");
    $tmp->DisplayNew('TAB1','Protokoll','noAction');

    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  function BestellungInlinePDF()
  {
    $id = (int)$this->app->Secure->GetGET('id');

    $frame = $this->app->Secure->GetGET('frame');
    if($frame != '') {
      $file = urlencode('../../../../index.php?module=bestellung&action=inlinepdf&id='.$id);
      echo "<iframe width=\"100%\" height=\"100%\" style=\"height:calc(100vh - 110px)\" src=\"./js/production/generic/web/viewer.html?file=$file\"></iframe>";
      $this->app->ExitXentral();
    }

    $projekt = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id=$id LIMIT 1");

    if(class_exists('BestellungPDFCustom'))
    {
      $Brief = new BestellungPDFCustom($this->app,$projekt);
    }else{
      $Brief = new BestellungPDF($this->app,$projekt);
    }
    $Brief->GetBestellung($id);
    $Brief->inlineDocument();
  }

  function BestellungPDF()
  {
    $id = (int)$this->app->Secure->GetGET('id');

    //$belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id='$id' LIMIT 1");

    //    if(is_numeric($belegnr) && $belegnr!=0)
    {
      if(class_exists('BestellungPDFCustom'))
      {
        $Brief = new BestellungPDFCustom($this->app,$projekt);
      }else{
        $Brief = new BestellungPDF($this->app,$projekt);
      }
      $Brief->GetBestellung($id);
      $Brief->displayDocument(); 
    } //else
    //     $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Noch nicht freigegebene Bestellungen k&ouml;nnen nicht als PDF betrachtet werden.!</div>");


    $this->BestellungList();
  }


  function BestellungMenu()
  {
    $id = (int)$this->app->Secure->GetGET('id');

    $bestellungRow = $this->app->DB->SelectRow(sprintf('SELECT belegnr, name,status FROM bestellung WHERE id=%d LIMIT 1', $id));
    $belegnr = !empty($bestellungRow)?$bestellungRow['belegnr']:'';
    $name = !empty($bestellungRow)?$bestellungRow['name']:'';

    $this->app->erp->BestellungNeuberechnen($id);


    if($belegnr=='0' || $belegnr=='') {
      $belegnr ='(Entwurf)';
    }
    $this->app->Tpl->Set('KURZUEBERSCHRIFT2',"$name Bestellung $belegnr");

    $status = !empty($bestellungRow)?$bestellungRow['status']:'';
    if($status==="angelegt")
    {
      $this->app->erp->MenuEintrag("index.php?module=bestellung&action=freigabe&id=$id","Freigabe");
    }

    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=edit&id=$id","Details");

    if($status==='bestellt')
    { 
      $this->app->erp->MenuEintrag("index.php?module=bestellung&action=wareneingang&id=$id","Wareneingang<br>R&uuml;ckst&auml;nde");
      $this->app->erp->MenuEintrag("index.php?module=bestellung&action=wareneingang&id=$id","Mahnstufen");
    } 
    //    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=abschicken&id=$id","Abschicken / Protokoll");
    //    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=protokoll&id=$id","Protokoll");

    $anzahldateien = $this->app->erp->AnzahlDateien("Bestellung",$id);
    if($anzahldateien > 0) $anzahldateien = " (".$anzahldateien.")"; else $anzahldateien="";

    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=dateien&id=$id","Dateien".$anzahldateien);

    if($this->app->erp->Firmendaten('auftrag_eantab')=='1'){
      $this->app->erp->MenuEintrag('index.php?module=bestellung&action=ean&id='.$id, 'Barcodescanner');
    }

    if($this->app->Secure->GetGET("action")==="abschicken")
      $this->app->erp->MenuEintrag("index.php?module=bestellung&action=edit&id=$id","Zur&uuml;ck zur Bestellung");
    else
    {
      $backlink = $this->app->Secure->GetGET('backlink');
      // Prüfen ob Backlink mit index.php? beginnt; ansonsten ist Open Redirect möglich
      if (!empty($backlink) && strpos($backlink, 'index.php?') !== 0){
        unset($backlink);
      }
      if($backlink)
      {
        $this->app->erp->MenuEintrag($backlink,"Zur&uuml;ck zur &Uuml;bersicht");
      }else{
        $this->app->erp->MenuEintrag("index.php?module=bestellung&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      }
    }
      

    $this->app->erp->RunMenuHook('bestellung');
    $this->app->Tpl->Parse('MENU',"bestellung_menu.tpl");
  }


  function BestellungDateien()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->BestellungMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT'," (Dateien)");
    $this->app->YUI->DateiUpload('PAGE',"Bestellung",$id);
  }



  function BestellungPositionen()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->AuftragNeuberechnen($id);
    $this->app->YUI->AARLGPositionen(false);
  }
  
  function CopyBestellungPosition()
  {
    $this->app->YUI->SortListEvent("copy","bestellung_position","bestellung");
    $this->BestellungPositionen();
  }

  function DelBestellungPosition()
  {
    $this->app->YUI->SortListEvent("del","bestellung_position","bestellung");
    $this->BestellungPositionen();
  }

  function UpBestellungPosition()
  {
    $this->app->YUI->SortListEvent("up","bestellung_position","bestellung");
    $this->BestellungPositionen();
  }

  function DownBestellungPosition()
  {
    $this->app->YUI->SortListEvent("down","bestellung_position","bestellung");
    $this->BestellungPositionen();
  }


  function BestellungPositionenEditPopup()
  {
    $id = $this->app->Secure->GetGET("id");

    // nach page inhalt des dialogs ausgeben
    $filename = "widgets/widget.bestellung_position_custom.php";
    if(is_file($filename))
    {
      include_once($filename);
      $widget = new WidgetBestellung_positionCustom($this->app,'PAGE');
    } else {
      $widget = new WidgetBestellung_position($this->app,'PAGE');
    }

    $sid= $this->app->DB->Select("SELECT bestellung FROM bestellung_position WHERE id='$id' LIMIT 1");
    $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=bestellung&action=positionen&id=$sid");
    $widget->Edit();
    $this->app->BuildNavigation=false;
  }



  function BestellungIconMenu($id,$prefix="")
  {
    $supplierOrder = $this->app->DB->SelectRow("SELECT status, belegnr FROM bestellung WHERE id='$id' LIMIT 1");
    $status = $supplierOrder['status'];
    $belegnr = $supplierOrder['belegnr'];
    $freigegeben = '';
    if($status=="angelegt" || $status=="")
      $freigabe = "<option value=\"freigabe\">Bestellung freigeben</option>";

    if($status!="angelegt" && $status!="abgeschlossen")
      $abschliessen = "<option value=\"abschliessen\">Bestellung abschliessen</option>";

    $einlagern = '';

    if(($status === 'versendet' || $status === 'freigegeben')
      && $this->app->erp->RechteVorhanden("bestellung", "einlagern")
      && $this->app->DB->Select("SELECT id FROM bestellung_position WHERE bestellung = '$id' AND geliefert < menge LIMIT 1")) {
      $standardlager = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE geloescht <> 1 AND sperrlager <> 1 AND poslager <> 1 ORDER BY id LIMIT 1");
      $nichtlager = $this->app->DB->Select("SELECT art.id FROM bestellung_position bp INNER JOIN artikel art ON bp.artikel = art.id WHERE bp.bestellung = '$id' AND art.porto <> 1 AND lagerartikel <> 1 AND stueckliste <> 1 LIMIT 1");
      if($nichtlager) {
        $standardlager .= ' (Achtung Es gibt Positionen die keine Lagerartikel sind, diese werden nicht eingelagert)';
      }
      $einlagern = '<option value="einlagern">Bestellung einlagern</option>';
    }

    if($status === 'abgeschlossen') {
      $freigegeben = "<option value=\"freigegeben\">als freigegeben markieren</option>";
    }
    if($status === 'abgeschlossen' || $status=="freigegeben")
      $alsversendet = "<option value=\"alsversendet\">als versendet markieren</option>";



    if($status=="storniert")
      $storno = "<option value=\"unstorno\">Bestellung Storno r&uuml;ckg&auml;ngig</option>";
    else if($status!="storniert")
      $storno = "<option value=\"storno\">Bestellung stornieren</option>";

    if($this->app->erp->RechteVorhanden('belegeimport', 'belegcsvexport'))
    {  
      $casebelegeimport = "case 'belegeimport':  window.location.href='index.php?module=belegeimport&action=belegcsvexport&cmd=bestellung&id=%value%'; break;";
      $optionbelegeimport = "<option value=\"belegeimport\">Export als CSV</option>";
    }
    

    $hookoption = '';
    $hookcase = '';
    $this->app->erp->RunHook("Bestellung_Aktion_option",3, $id, $status, $hookoption);
    $this->app->erp->RunHook("Bestellung_Aktion_case",3, $id, $status, $hookcase);
    $abschliessentext = '{|Wirklich abschliessen?|}';
    if($this->app->DB->Select("SELECT id FROM bestellung_position WHERE bestellung = '$id' AND mengemanuellgeliefertaktiviert = 0 AND geliefert < menge LIMIT 1"))
      $abschliessentext = "{|Zu dieser Bestellung gibt es noch offene Postitionen, möchten Sie diese wirklich als abgeschlossen markieren? Eine Warenannahme ist dann nicht mehr möglich für diese Bestellung.|}";
    $menu ="

      <script type=\"text/javascript\">
      function onchangebestellung(cmd)
      {
        switch(cmd)
        {
          case 'storno': if(!confirm('Wirklich stornieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=bestellung&action=delete&id=%value%'; break;
          case 'unstorno':    if(!confirm('Wirklich stornierte Bestellung wieder freigeben?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=bestellung&action=undelete&id=%value%'; break; 
          case 'copy': if(!confirm('Wirklich kopieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=bestellung&action=copy&id=%value%'; break;
          case 'pdf': window.location.href='index.php?module=bestellung&action=pdf&id=%value%'; document.getElementById('aktion$prefix').selectedIndex = 0; break;
          case 'abschicken':  ".$this->app->erp->DokumentAbschickenPopup()." break;
          case 'freigabe': window.location.href='index.php?module=bestellung&action=freigabe&id=%value%'; break;
          case 'alsversendet': window.location.href='index.php?module=bestellung&action=alsversendet&id=%value%'; break;
          case 'freigegeben': if(!confirm('Wirklich auf freigegeben setzen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=bestellung&action=freigegeben&id=%value%'; break;
          case 'abschliessen': if(!confirm('$abschliessentext')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=bestellung&action=abschliessen&id=%value%'; break;
          case 'einlagern': if(!confirm('Wirklich einlagern? Die Artikel werden in den voreingestellten Lagerplätzen eingelagert andersfalls in das Lager $standardlager')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=bestellung&action=einlagern&id=%value%'; break;
          $hookcase
          $casebelegeimport
        }

      }
    </script>

      &nbsp;Aktion:&nbsp;<select id=\"aktion$prefix\" onchange=\"onchangebestellung(this.value);\">
      <option>bitte w&auml;hlen ...</option>
      $storno
      <option value=\"copy\">Bestellung kopieren</option>
      <option value=\"abschicken\">Bestellung abschicken</option>
      $freigabe
      $alsversendet
      $freigegeben
      $abschliessen
      $einlagern
      $hookoption
      $optionbelegeimport
      <option value=\"pdf\">PDF &ouml;ffnen</option>
      </select>&nbsp;

    <a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.svg\"></a>
      <!--        <a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
      <a onclick=\"if(!confirm('Wirklich stornieren?')) return false; else window.location.href='index.php?module=bestellung&action=delete&id=%value%';\">
      <img src=\"./themes/new/images/delete.svg\" border=\"0\"></a>
      <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=bestellung&action=copy&id=%value%';\">
      <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
      <a onclick=\"if(!confirm('Wirklich Auftrag abschicken?')) return false; else window.location.href='index.php?module=bestellung&action=abschicken&id=%value%';\">
      <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"Auftrag abeschicken\"></a>-->";

    //$tracking = $this->AuftragTrackingTabelle($id);

    $menu = str_replace('%value%',$id,$menu);
    return $menu;
  }

  function BestellungEdit()
  {
    //$action = $this->app->Secure->GetGET("action");
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->BestellungNeuberechnen($id);
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'dadown')
    {
      $erg['status'] = 0;
      $daid = $this->app->Secure->GetPOST("da_id");
      $check = $this->app->DB->SelectArr("SELECT ds.* FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.id = '$daid' and d.geloescht <> 1 LIMIT 1");
      if($check)
      {
        $sort = $check[0]['sort']+1;
        if($sort > 1)
        {
          $check2 = $this->app->DB->SelectArr("SELECT ds.* FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.objekt like 'angebot' AND ds.sort = '$sort' AND d.geloescht <> 1 AND ds.parameter = '$id' LIMIT 1");
          if($check2)
          {
            $erg['status'] = 1;
            $erg['from'] = $check2[0]['id'];
            $this->app->DB->Update("UPDATE datei_stichwoerter SET sort = sort + 1 WHERE id = '$daid' LIMIT 1");
            $this->app->DB->Update("UPDATE datei_stichwoerter SET sort = sort - 1 WHERE id = '".$check2[0]['id']."' LIMIT 1");
          }
        }
      }
      echo json_encode($erg);
      $this->app->ExitXentral();
    }
    
    if($cmd === 'daup')
    {
      $erg['status'] = 0;
      $daid = $this->app->Secure->GetPOST("da_id");
      $check = $this->app->DB->SelectArr("SELECT ds.* FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.id = '$daid' and d.geloescht <> 1 LIMIT 1");
      if($check)
      {
        $sort = $check[0]['sort']-1;
        if($sort > 0)
        {
          $check2 = $this->app->DB->SelectArr("SELECT ds.* FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.objekt like 'angebot' AND ds.sort = '$sort' AND d.geloescht <> 1 AND ds.parameter = '$id' LIMIT 1");
          if($check2)
          {
            $erg['status'] = 1;
            $erg['from'] = $check2[0]['id'];
            $this->app->DB->Update("UPDATE datei_stichwoerter SET sort = sort - 1 WHERE id = '$daid' LIMIT 1");
            $this->app->DB->Update("UPDATE datei_stichwoerter SET sort = sort + 1 WHERE id = '".$check2[0]['id']."' LIMIT 1");
          }
        }
      }
      echo json_encode($erg);
      $this->app->ExitXentral();
    }

    if($this->app->erp->DisableModul("bestellung",$id))
    {
      //$this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->BestellungMenu();
      return;
    }
    $this->app->YUI->AARLGPositionen();

    //$storno = $this->app->Secure->GetGET("storno");

    $speichern = $this->app->Secure->GetPOST("speichern");
    $verbindlichkeiteninfo = $this->app->Secure->GetPOST("verbindlichkeiteninfo");

    if($verbindlichkeiteninfo!="" && $speichern!="" && $id > 0)
      $this->app->DB->Update("UPDATE bestellung SET verbindlichkeiteninfo='$verbindlichkeiteninfo' WHERE id='$id' LIMIT 1");

    //$this->BestellungMiniDetail(MINIDETAIL,false);
    $arr = $this->app->DB->SelectArr(
      "SELECT b.belegnr, b.adresse, b.status, b.schreibschutz, b.langeartikelnummern, b.zuarchivieren 
        FROM `bestellung` AS `b`
        WHERE b.id = '$id' LIMIT 1"
    );
    if($arr){
      $arr = reset($arr);
      $belegnr = $arr['belegnr'];
      $nummer = $arr['belegnr'];
      $adresse = $arr['adresse'];
      $status = $arr['status'];
      $schreibschutz = $arr['schreibschutz'];
      $zuArchivieren = $arr['zuarchivieren'];
      $lieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    }

    if($id > 0 && $zuArchivieren == 1 && $schreibschutz == 1) {
      $this->app->erp->PDFArchivieren('bestellung', $id, true);
    }

    if($status !== "angelegt" && $status !== "angelegta" && $status !== "a")
    {
/*
      $laenge = $this->app->DB->SelectArr("SELECT LENGTH(bestellnummer) as length,bestellnummer FROM bestellung_position WHERE bestellung='$id' ORDER by length DESC");
      $pdf=new FPDFWAWISION();
      $pdf->SetFontClassic($this->app->erp->Firmendaten("schriftart"),'',$this->app->erp->Firmendaten("tabellenhinhalt"));
      $laengepdf = $pdf->GetStringWidth($laenge[0]['bestellnummer']);
      if(floor($laengepdf) > $this->app->erp->Firmendaten("breite_nummer")+11 && $arr['langeartikelnummern']!="1")
      {
        $this->app->Tpl->Add('MESSAGE',"<div class=\"error\">Eventuell ist die Artikelnummer zu lang. Sie können die Funktion \"Lange Artikelnummer\" weiter unten aktivieren.</div>");
      }
*/
      $Brief = new Briefpapier($this->app);
      if($Brief->zuArchivieren($id, "bestellung"))
      {
        $this->app->Tpl->Add('MESSAGE',"<div class=\"error\">Die Bestellung ist noch nicht archiviert! Bitte versenden oder manuell archivieren. <input type=\"button\" onclick=\"if(!confirm('Soll das Dokument archiviert werden?')) return false;else window.location.href='index.php?module=bestellung&action=archivierepdf&id=$id';\" value=\"Manuell archivieren\" /> <input type=\"button\" value=\"Dokument versenden\" onclick=\"DokumentAbschicken('bestellung',$id)\"></div>");
      }elseif(!$this->app->DB->Select("SELECT versendet FROM bestellung WHERE id = '$id' LIMIT 1"))
      {

        $this->app->Tpl->Add('MESSAGE',"<div class=\"error\">Die Bestellung wurde noch nicht versendet! <input type=\"button\" value=\"Dokument versenden\" onclick=\"DokumentAbschicken('bestellung',$id)\"></div>");
      }
    }
    
    if($schreibschutz!="1")// && $this->app->erp->RechteVorhanden("bestellung","schreibschutz"))    {      
    {
      $this->app->erp->AnsprechpartnerButton($adresse);      
      $this->app->erp->LieferadresseButton($adresse);
      $this->app->erp->AnsprechpartnerAlsLieferadresseButton($adresse);
      $this->app->erp->AdresseAlsLieferadresseButton($adresse);
    }


    $this->app->Tpl->Set('ICONMENU',$this->BestellungIconMenu($id));
    $this->app->Tpl->Set('ICONMENU2',$this->BestellungIconMenu($id,2));

    if($nummer!="")
    {
      $this->app->Tpl->Set('NUMMER',$nummer);
      if($this->app->erp->RechteVorhanden("adresse","edit"))
        $this->app->Tpl->Set('LIEFERANT',"&nbsp;&nbsp;&nbsp;Lf-Nr. <a href=\"index.php?module=adresse&action=edit&id=$adresse\" target=\"_blank\">".$lieferantennummer."</a>");
      else
        $this->app->Tpl->Set('LIEFERANT',"&nbsp;&nbsp;&nbsp;Lf-Nr. ".$lieferantennummer);
    }

    $check = $this->app->DB->SelectArr("SELECT a.belegnr, a.id, a.name
    FROM bestellung_position bp 
    INNER JOIN auftrag_position ap ON ap.id = bp.auftrag_position_id
    INNER JOIN auftrag a ON ap.auftrag = a.id
    WHERE bp.bestellung='$id' GROUP BY a.belegnr, a.id ORDER BY a.belegnr, a.id");
    if($check)
    {
      $this->app->Tpl->Add('MESSAGE',"<div class=\"info\">Zu dieser Bestellung geh&ouml;r".(count($check) == 1?'t der Auftrag':'en die Auftr&auml;ge:'));
      foreach($check as $row)
      {
        $this->app->Tpl->Add('MESSAGE','&nbsp;<a href="index.php?module=auftrag&action=edit&id='.$row['id'].'" target="_blank"><input type="button" value="'.($row['belegnr']?$row['belegnr']:'Entwurf')." (".$row['name'].')" /></a>');
      }
      $this->app->Tpl->Add('MESSAGE',"</div>");
    }

    if($this->app->Secure->GetPOST("speichern")!="")
    {
      $abweichenderechnungsadresse = $this->app->Secure->GetPOST("abweichenderechnungsadresse");
      $abweichendelieferadresse = $this->app->Secure->GetPOST("abweichendelieferadresse");
    } else {
      $abweichenderechnungsadresse = $this->app->DB->Select("SELECT abweichende_rechnungsadresse FROM adresse WHERE id='$adresse' LIMIT 1");
      $abweichendelieferadresse = $this->app->DB->Select("SELECT abweichendelieferadresse FROM bestellung WHERE id='$id' LIMIT 1");
    }
    if($abweichenderechnungsadresse) $this->app->Tpl->Set('RECHNUNGSADRESSE',"visible"); else $this->app->Tpl->Set('RECHNUNGSADRESSE',"none");
    if($abweichendelieferadresse) $this->app->Tpl->Set('LIEFERADRESSE',"visible"); else $this->app->Tpl->Set('LIEFERADRESSE',"none");

    if($belegnr=="" || $belegnr=="0")
    {
      $this->app->Tpl->Set('LOESCHEN',"<input type=\"button\" value=\"Abbrechen\" onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=bestellung&action=delete&id=$id';\">");
    }
    $status= $this->app->DB->Select("SELECT status FROM bestellung WHERE id='$id' LIMIT 1");
    if($status=="")
      $this->app->DB->Update("UPDATE bestellung SET status='angelegt' WHERE id='$id' LIMIT 1");

    if($schreibschutz=="1" && $this->app->erp->RechteVorhanden("bestellung","schreibschutz"))
    {
      $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Diese Bestellung ist schreibgesch&uuml;tzt und darf daher nicht mehr bearbeitet werden!&nbsp;<input type=\"button\" value=\"Schreibschutz entfernen\" onclick=\"if(!confirm('Soll der Schreibschutz f&uuml;r diese Bestellung wirklich entfernt werden?')) return false;else window.location.href='index.php?module=bestellung&action=schreibschutz&id=$id';\"></div>");
      //      $this->app->erp->CommonReadonly();
    }
    if($schreibschutz=="1")
    {

      $this->app->erp->RemoveReadonly("bestellung_bestaetigt");
      $this->app->erp->RemoveReadonly("bestaetigteslieferdatum");
      $this->app->erp->RemoveReadonly("bestellungbestaetigtabnummer");
      $this->app->erp->RemoveReadonly("bestellungbestaetigtper");

      $speichern = $this->app->Secure->GetPOST("speichern");
      if($speichern!="")
      { 
        $bestellung_bestaetigt = $this->app->Secure->GetPOST("bestellung_bestaetigt");
        $bestaetigteslieferdatum = $this->app->Secure->GetPOST("bestaetigteslieferdatum");
        $bestellungbestaetigtabnummer = $this->app->Secure->GetPOST("bestellungbestaetigtabnummer");
        $bestellungbestaetigtper = $this->app->Secure->GetPOST("bestellungbestaetigtper");
        
        if($bestellung_bestaetigt!="1") $bestellung_bestaetigt="0";

        $bestaetigteslieferdatum = $this->app->String->Convert($bestaetigteslieferdatum,"%1.%2.%3","%3-%2-%1");

        $this->app->DB->Update("UPDATE bestellung SET bestellung_bestaetigt='$bestellung_bestaetigt',bestaetigteslieferdatum='$bestaetigteslieferdatum',
          bestellungbestaetigtabnummer='$bestellungbestaetigtabnummer',bestellungbestaetigtper='$bestellungbestaetigtper' WHERE id='$id' LIMIT 1");

        // alle positonen ebenso anpassen
        $this->app->DB->Update("UPDATE bestellung_position SET lieferdatum='$bestaetigteslieferdatum' WHERE bestellung='$id' AND lieferdatum='0000-00-00'");
      }
      $this->app->erp->CommonReadonly();
    } else {
      $portofreilieferant_aktiv = $this->app->DB->Select("SELECT portofreilieferant_aktiv FROM adresse WHERE id='$adresse' LIMIT 1");
      $portofreiablieferant = $this->app->DB->Select("SELECT portofreiablieferant FROM adresse WHERE id='$adresse' LIMIT 1");
      $gesamtsumme = $this->app->erp->BEGesamtsummeOhnePorto($id,"bestellung");
      if($portofreilieferant_aktiv == 1 && $portofreiablieferant > 0 && $gesamtsumme <= $portofreiablieferant)
      {
        $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">Die Lieferung wird ab ".number_format($portofreiablieferant,2,',','.')." EUR (netto) Portofrei. 
            Aktuell sind nur ".number_format($gesamtsumme,2,',','.')." EUR (netto) in der Bestellung.</div>");
      }
    }

    if($schreibschutz != '1'){
      if($this->app->erp->Firmendaten("schnellanlegen") == "1"){
        $this->app->Tpl->Set('BUTTON_UEBERNEHMEN', '      <input type="button" value="&uuml;bernehmen" onclick="document.getElementById(\'uebernehmen\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen" name="uebernehmen" value="0">
          ');
      }else{
        $this->app->Tpl->Set('BUTTON_UEBERNEHMEN', '
          <input type="button" value="&uuml;bernehmen" onclick="if(!confirm(\'Soll der neue Lieferant wirklich &uuml;bernommen werden? Es werden alle Felder &uuml;berladen.\')) return false;else document.getElementById(\'uebernehmen\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen" name="uebernehmen" value="0">
          ');
      }
    }

    // immer wenn sich der lieferant genändert hat standartwerte setzen
    if($this->app->Secure->GetPOST("adresse")!="")
    {
      $tmp = $this->app->Secure->GetPOST("adresse");
      $lieferantennummer = $this->app->erp->FirstTillSpace($tmp);

      //$name = substr($tmp,6);
      
      $filter_projekt = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id = '$id' LIMIT 1");
      //if($filter_projekt)$filter_projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE id= '$filter_projekt' and eigenernummernkreis = 1 LIMIT 1");
      $adresse =  $this->app->DB->Select("SELECT a.id FROM adresse a 
      LEFT JOIN adresse_rolle ar ON a.id = ar.adresse AND ar.projekt > 0 ".$this->app->erp->ProjektRechte("ar.projekt")."
      WHERE a.lieferantennummer='$lieferantennummer' AND a.geloescht=0 AND
      (1 ".$this->app->erp->ProjektRechte("a.projekt")." OR not isnull(ar.id))
      ORDER by ".($filter_projekt?" a.projekt = '$filter_projekt' DESC, ":"")." a.projekt LIMIT 1");

      $uebernehmen =$this->app->Secure->GetPOST("uebernehmen");
      if($schreibschutz != '1' && $uebernehmen=="1" && $adresse > 0) // nur neuladen bei tastendruck auf uebernehmen // FRAGEN!!!!
      {
        $this->app->erp->LoadBestellungStandardwerte($id,$adresse);
        $this->app->Location->execute('index.php?module=bestellung&action=edit&id='.$id);
      }
    }



    /* 
       $table = new EasyTable($this->app);
       $table->Query("SELECT a.bezeichnung as artikel, a.nummer as Nummer, b.menge, b.vpe as VPE, FORMAT(b.preis,4) as preis
       FROM bestellung_position b LEFT JOIN artikel a ON a.id=b.artikel
       WHERE b.bestellung='$id'");
       $table->DisplayNew(POSITIONEN,"Preis","noAction");
     */
    $arr = $this->app->DB->SelectArr("SELECT bearbeiter, belegnr, status, zahlungsweise,abweichendelieferadresse FROM bestellung WHERE id = '$id' LIMIT 1");
    if($arr){
      $arr = reset($arr);
      $bearbeiter = $arr['bearbeiter'];
      $status = $arr['status'];
      $bestellung = $arr['belegnr'];
      $zahlungsweise = $arr['zahlungsweise'];
      $abweichendelieferadresse = $arr['abweichendelieferadresse'];
    }

    $this->app->Tpl->Set('BEARBEITER',"<input type=\"text\" value=\"".$this->app->erp->GetAdressName($bearbeiter)."\" readonly>");

    $this->app->Tpl->Set('STATUS',"<input type=\"text\" size=\"30\" value=\"".$status."\" readonly [COMMONREADONLYINPUT]>");

    if($bestellung!='') $bestellung="keine Nummer";
    $this->app->Tpl->Set('ANGEBOT',"<input type=\"text\" value=\"".$bestellung."\" readonly>");

    if($this->app->Secure->GetPOST("zahlungsweise")!="") $zahlungsweise = $this->app->Secure->GetPOST("zahlungsweise");
    $zahlungsweise = strtolower($zahlungsweise);
    $this->app->Tpl->Set('RECHNUNG',"none");
    $this->app->Tpl->Set('KREDITKARTE',"none");
    $this->app->Tpl->Set('VORKASSE',"none");
    $this->app->Tpl->Set('PAYPAL',"none");
    $this->app->Tpl->Set('EINZUGSERMAECHTIGUNG',"none");
    if($zahlungsweise=="rechnung") $this->app->Tpl->Set('RECHNUNG',"");
    if($zahlungsweise=="paypal") $this->app->Tpl->Set('PAYPAL',"");
    if($zahlungsweise=="kreditkarte") $this->app->Tpl->Set('KREDITKARTE',"");
    if($zahlungsweise=="einzugsermaechtigung" || $zahlungsweise=="lastschrift") $this->app->Tpl->Set('EINZUGSERMAECHTIGUNG',"");
    if($zahlungsweise=="vorkasse" || $zahlungsweise=="kreditkarte" || $zahlungsweise=="paypal" || $zahlungsweise=="bar") $this->app->Tpl->Set('VORKASSE',"");

    if($this->app->Secure->GetPOST("abweichendelieferadresse")!="") $versandart = $this->app->Secure->GetPOST("abweichendelieferadresse");
    $this->app->Tpl->Set('ABWEICHENDELIEFERADRESSESTYLE',"none");
    if($abweichendelieferadresse=="1") $this->app->Tpl->Set('ABWEICHENDELIEFERADRESSESTYLE',"");

    $this->app->Tpl->Set('AKTIV_TAB1',"selected");
    parent::BestellungEdit();

    $this->app->erp->MessageHandlerStandardForm();	
    /*
       if($this->app->Secure->GetPOST("speichern")!="" && $storno=="")
       {
       if($this->app->Secure->GetGET("msg")=="")
       {
       $msg = $this->app->Tpl->Get(MESSAGE);
       $msg = $this->app->erp->base64_url_encode($msg);
       } else {
       $msg = $this->app->erp->base64_url_encode($msg);
       }

       header("Location: index.php?module=bestellung&action=edit&id=$id&msg=$msg");
       exit;

       } 
     */

    /*
       $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM bestellung_position
       WHERE bestellung='$id'");

       $waehrung = $this->app->DB->Select("SELECT waehrung FROM bestellung_position
       WHERE bestellung='$id' LIMIT 1");

       $ust_befreit_check = $this->app->DB->Select("SELECT ust_befreit FROM bestellung WHERE id='$id' LIMIT 1");
       $summebrutto  = $summe *1.19;

       if($ust_befreit_check==0)
       $tmp = "Kunde zahlt mit UST";
       else if($ust_befreit_check==1)
       $tmp = "Kunde ist UST befreit";
       else
       $tmp = "Kunde zahlt keine UST";


       if($summe > 0)
       $this->app->Tpl->Add(POSITIONEN, "<br><center>Zu zahlen: <b>$summe (netto) $summebrutto (brutto) $waehrung</b> ($tmp)&nbsp;&nbsp;");

     */
    if($this->app->Secure->GetPOST('weiter')!='')
    {
      $this->app->Location->execute('index.php?module=bestellung&action=positionen&id='.$id);
    }
    $this->BestellungMenu();

  }

  function BestellungCreate()
  {
    //$this->app->Tpl->Add(TABS,"<li><h2>Bestellung</h2></li>");
    $this->app->erp->Headlines('Bestellung anlegen');
    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=list","Zur&uuml;ck zur &Uuml;bersicht");


    $anlegen = $this->app->Secure->GetGET("anlegen");

    if($this->app->erp->Firmendaten("schnellanlegen")=="1" && $anlegen!="1")
    {
      $this->app->Location->execute('index.php?module=bestellung&action=create&anlegen=1');
    }

    if($anlegen != "")
    {
      $id = $this->app->erp->CreateBestellung();
      $this->app->erp->BestellungProtokoll($id,"Bestellung angelegt");


      $this->app->Location->execute('index.php?module=bestellung&action=edit&id='.$id);
    }
    $this->app->Tpl->Add('MESSAGE',"<div class=\"warning\">M&ouml;chten Sie eine Bestellung jetzt anlegen? &nbsp;
        <input type=\"button\" onclick=\"window.location.href='index.php?module=bestellung&action=create&anlegen=1'\" value=\"Ja - Bestellung jetzt anlegen\"></div><br>");
    $this->app->Tpl->Set('TAB1',"
        <table width=\"100%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
        <tr>
        <td align=\"center\">
        <br><b style=\"font-size: 14pt\">Bestellungen in Bearbeitung</b>
        <br>
        <br>
        Offene Bestellunge, die durch andere Mitarbeiter in Bearbeitung sind.
        <br>
        </td>
        </tr>
        </table>
        <br>
        [ANGEBOTE]");


    $this->app->Tpl->Set('AKTIV_TAB1',"selected");

    $this->app->YUI->TableSearch('ANGEBOTE',"bestellungeninbearbeitung");
    /*
       $table = new EasyTable($this->app);
       $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%y') as vom, if(belegnr,belegnr,'ohne Nummer') as beleg, name, status, id
       FROM bestellung WHERE status='angelegt' order by datum DESC, id DESC");
       $table->DisplayNew(ANGEBOTE, "<a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
       <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=bestellung&action=delete&id=%value%';\">
       <img src=\"./themes/new/images/delete.svg\" border=\"0\"></a>
       <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=bestellung&action=copy&id=%value%';\">
       <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
       ");
     */

    $this->app->Tpl->Set('TABTEXT',"Bestellung anlegen");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
    //parent::BestellungCreate();
  }

  function BestellungListMenu()
  {
    $backurl = $this->app->Secure->GetGET("backurl");
    $backurl = $this->app->erp->base64_url_decode($backurl);

    //$this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\" style=\"background-color: [FARBE2]\">Allgemein</h2></li>");
    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=create","Neue Bestellung anlegen");
    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=offenepositionen","Offene Positionen");
    //$this->app->erp->MenuEintrag("index.php?module=bestellvorschlag&action=ausgehend","Bestellvorschlag");

    if(strlen($backurl)>5){
      $this->app->erp->MenuEintrag((string)$backurl, 'Zur&uuml;ck zur &Uuml;bersicht');
    }
    else{
      $this->app->erp->MenuEintrag('index.php', 'Zur&uuml;ck zur &Uuml;bersicht');
    }
  }


  function BestellungList()
  {
    //    $this->app->Tpl->Set(UEBERSCHRIFT,"Bestellungssuche");
    //   $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Bestellungssuche");

    if($this->app->Secure->GetPOST('ausfuehren') && $this->app->erp->RechteVorhanden('bestellung', 'edit'))
    {
      $drucker = $this->app->Secure->GetPOST('seldrucker');
      $aktion = $this->app->Secure->GetPOST('sel_aktion');
      $auswahl = $this->app->Secure->GetPOST('auswahl');

      if($drucker > 0) $this->app->erp->BriefpapierHintergrundDisable($drucker);
      if(is_array($auswahl))
      {
        switch($aktion)
        {
          case 'mail':
            foreach($auswahl as $v)
            {
              $v = (int)$v;
              if($v){
                $bestellungarr = $this->app->DB->SelectRow("SELECT email,adresse,projekt,name,sprache FROM bestellung WHERE id = '$v' LIMIT 1");
                if(!empty($bestellungarr))
                {
                  $email = (String)$bestellungarr['email'];//$this->app->DB->Select("SELECT email FROM auftrag WHERE id = '$v' LIMIT 1");
                  $adresse = $bestellungarr['adresse'];//$this->app->DB->Select("SELECT adresse FROM auftrag WHERE id = '$v' LIMIT 1");
                  $projekt = $bestellungarr['projekt'];//$this->app->DB->Select("SELECT projekt FROM auftrag WHERE id = '$v' LIMIT 1");
                  $name = $bestellungarr['name'];// $this->app->DB->Select("SELECT name FROM auftrag WHERE id = '$v' LIMIT 1");
                  $sprache = $bestellungarr['sprache'];// $this->app->DB->Select("SELECT sprache FROM auftrag WHERE id='$v' LIMIT 1");
                }else{
                  $email = '';
                  $adresse = 0;
                  $projekt = 0;
                  $name = '';
                  $sprache = '';
                }

                if($sprache==''){
                  $sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
                }

                if($sprache=='') {
                  $sprache='de';
                }

                $emailtext = $this->app->erp->Geschaeftsbriefvorlage($sprache,'bestellung',$projekt,$name,$v);

                if($email === '')
                {
                  $email = (String)$this->app->DB->Select("SELECT email FROM adresse WHERE id = '$adresse' LIMIT 1");
                }
                if($email !== '')
                {
                  $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                  if(class_exists('BestellungPDFCustom'))
                  {
                    $Brief = new BestellungPDFCustom($this->app,$projekt);
                  }else{
                    $Brief = new BestellungPDF($this->app,$projekt);
                  }
                  $Brief->GetBestellung($v);
                  $_tmpfile = $Brief->displayTMP();
                  $Brief->ArchiviereDocument();
                  unlink($_tmpfile);
                  $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                  if(class_exists('BestellungPDFCustom'))
                  {
                    $Brief = new BestellungPDFCustom($this->app,$projekt);
                  }else{
                    $Brief = new BestellungPDF($this->app,$projekt);
                  }
                  $Brief->GetBestellung($v);
                  $tmpfile = $Brief->displayTMP();
                  $Brief->ArchiviereDocument();

                  $fileid = $this->app->erp->CreateDatei($Brief->filename,'bestellung','','',$tmpfile,$this->app->User->GetName());
                  $this->app->erp->AddDateiStichwort($fileid,'bestellung','bestellung',$v);
                  $this->app->erp->DokumentSend($adresse,'bestellung', $v, 'email',$emailtext['betreff'],$emailtext['text'],array($tmpfile),"","",$projekt,$email, $name);
                  $ansprechpartner = $name." <".$email.">";
                  $this->app->DB->Insert("INSERT INTO dokumente_send
                      (id,dokument,zeit,bearbeiter,adresse,parameter,art,betreff,text,projekt,ansprechpartner,versendet,dateiid) VALUES ('','bestellung',NOW(),'".$this->app->DB->real_escape_string($this->app->User->GetName())."',
                        '$adresse','$v','email','".$this->app->DB->real_escape_string($emailtext['betreff'])."','".$this->app->DB->real_escape_string($emailtext['text'])."','$projekt','$ansprechpartner',1,'$fileid')");
                  $tmpid = $this->app->DB->GetInsertID();
                  unlink($tmpfile);
                  $this->app->DB->Update("UPDATE bestellung SET versendet=1, versendet_am=NOW(),
                    versendet_per='email',versendet_durch='".$this->app->DB->real_escape_string($this->app->User->GetName())."',schreibschutz='1' WHERE id='$v' LIMIT 1");
                  $this->app->erp->BestellungProtokoll($v,'Bestellung versendet');
                }
              }
            }
            break;
          case 'freigeben':
            foreach($auswahl as $v)
            {
              $v = (int)$v;
              if($v){
                if($this->app->DB->Select("SELECT id FROM bestellung WHERE id = '$v' AND status <> 'storniert' LIMIT 1")){
                  $this->BestellungFreigegeben($v);
                }
              }
            }
            break;
          case 'versendet':
            foreach($auswahl as $v)
            {
              $v = (int)$v;
              if($v) {
                $projekt = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id='$v' LIMIT 1");
                if(class_exists('BestellungPDFCustom'))
                {
                  $Brief = new BestellungPDFCustom($this->app,$projekt);
                }else{
                  $Brief = new BestellungPDF($this->app,$projekt);
                }
                $Brief->GetBestellung($v);
                $tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                $this->app->erp->BestellungProtokoll($v,"Bestellung versendet");
                $this->app->erp->closeInvoice($v);
                $this->app->DB->Update("UPDATE bestellung SET schreibschutz=1, versendet = 1, status='versendet' WHERE id = '$v' LIMIT 1");
              }
            }
            break;
          case 'abgeschlossen':
            foreach($auswahl as $v)
            {
              $v = (int)$v;
              if($v){
                if($this->app->DB->Select("SELECT id FROM bestellung WHERE id = '$v' AND status != 'angelegt' AND status != 'abgeschlossen' LIMIT 1")){
                  $this->BestellungAbschliessen($v);
                }
              }
            }
            break;
          case 'drucken':
            if($drucker)
            {
              foreach($auswahl as $v)
              {
                $v = (int)$v;
                if($v){
                  $bestellungsdaten = $this->app->DB->Select("SELECT projekt, adresse FROM bestellung WHERE id='$v' LIMIT 1");
                  $projekt = $bestellungsdaten['projekt'];
                  $adressId = $bestellungsdaten['adresse'];
                  $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                  if(class_exists('BestellungPDFCustom'))
                  {
                    $Brief = new BestellungPDFCustom($this->app,$projekt);
                  }else{
                    $Brief = new BestellungPDF($this->app,$projekt);
                  }
                  $Brief->GetBestellung($v);
                  $_tmpfile = $Brief->displayTMP();
                  $Brief->ArchiviereDocument();
                  unlink($_tmpfile);
                  $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                  if(class_exists('BestellungPDFCustom'))
                  {
                    $Brief = new BestellungPDFCustom($this->app,$projekt);
                  }else{
                    $Brief = new BestellungPDF($this->app,$projekt);
                  }
                  $Brief->GetBestellung($v);
                  $tmpfile = $Brief->displayTMP();
                  $Brief->ArchiviereDocument();
                  $this->app->printer->Drucken($drucker,$tmpfile);
                  $doctype = 'bestellung';
                  $this->app->erp->RunHook('dokumentsend_ende', 5, $doctype, $v, $projekt, $adressId, $aktion);
                  $this->app->erp->BestellungProtokoll($v,"Bestellung versendet");
                  unlink($tmpfile);
                }
              }
            }
            break;
          case 'pdf':
            foreach($auswahl as $v)
            {
              $v = (int)$v;
              if($v){
                $projekt = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id='$v' LIMIT 1");
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('BestellungPDFCustom'))
                {
                  $Brief = new BestellungPDFCustom($this->app,$projekt);
                }else{
                  $Brief = new BestellungPDF($this->app,$projekt);
                }
                $Brief->GetBestellung($v);
                $_tmpfile = $Brief->displayTMP();
                $Brief->ArchiviereDocument();
                unlink($_tmpfile);
                $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
                if(class_exists('BestellungPDFCustom'))
                {
                  $Brief = new BestellungPDFCustom($this->app,$projekt);
                }else{
                  $Brief = new BestellungPDF($this->app,$projekt);
                }
                $Brief->GetBestellung($v);
                $tmpfile[] = $Brief->displayTMP();
                //$Brief->ArchiviereDocument();
              }
            }

            if(count($tmpfile) > 0){
              try {
                /** @var PdfMerger $pdfMerger */
                $pdfMerger = $this->app->Container->get('PdfMerger');
                $mergeOutputPath = realpath($this->app->erp->GetTMP()) . '/' . uniqid('sammelpdf_', true) . '.pdf';
                $pdfMerger->merge($tmpfile, $mergeOutputPath);

                foreach($tmpfile as $key=>$value) {
                  unlink($value);
                }

                header('Content-type:application/pdf');
                header('Content-Disposition: attachment;filename='.md5(microtime(true)).'.pdf');
                readfile($mergeOutputPath);
                $this->app->ExitXentral();
              } catch (PdfComponentExceptionInterface $exception) {
                echo 'Fehler beim Generieren der Sammelpdf: ' . htmlspecialchars($exception->getMessage());
                $this->app->ExitXentral();
              }
            }
            break;
        }
      }
    }
    
    $speichern = $this->app->Secure->GetPOST("speichern");
    $verbindlichkeiteninfo = $this->app->Secure->GetPOST("verbindlichkeiteninfo");
    $bestellungid = $this->app->Secure->GetPOST("bestellungid");

    if($this->app->erp->Firmendaten("bestellungabschliessen"))
    {
      $offene = $this->app->DB->SelectArr("SELECT id FROM bestellung WHERE status='freigegeben' OR status='versendet'");
      foreach($offene as $order)
      {
        $this->checkAbschliessen($order['id']);
      }
    }

    if($verbindlichkeiteninfo!="" && $speichern!="" && $bestellungid > 0)
      $this->app->DB->Update("UPDATE bestellung SET verbindlichkeiteninfo='$verbindlichkeiteninfo' WHERE id='$bestellungid' LIMIT 1");

    $this->BestellungListMenu();

      $zahlungsweisen = $this->app->DB->SelectArr('
      SELECT
        zahlungsweise
      FROM
        bestellung
      GROUP BY
        zahlungsweise
    ');

    $zahlungsweiseStr = '';
    if ($zahlungsweisen) {
      foreach ($zahlungsweisen as $zahlungsweise) {
        if (empty($zahlungsweise['zahlungsweise'])) {
          continue;
        }
        $zahlungsweiseStr .= '<option name="' . $zahlungsweise['zahlungsweise'] . '">' . ucfirst($zahlungsweise['zahlungsweise']) . '</option>';
      }
    }

    $status = $this->app->DB->SelectArr('
      SELECT
        status
      FROM
        bestellung
      GROUP BY
        status
    ');

    $statusStr = '';
    if ($status) {
      foreach ($status as $statusE) {
        if (empty($statusE['status'])) {
          continue;
        }
        $statusStr .= '<option name="' . $statusE['status'] . '">' . ucfirst($statusE['status']) . '</option>';
      }
    }

    $versandarten = $this->app->DB->SelectArr('
      SELECT
        versandart
      FROM
        bestellung
      GROUP BY
        versandart
    ');

    $versandartenStr = '';
    if ($versandarten) {
      foreach ($versandarten as $versandart) {
        if (empty($versandart['versandart'])) {
          continue;
        }
        $versandartenStr .= '<option name="' . $versandart['versandart'] . '">' . ucfirst($versandart['versandart']) . '</option>';
      }
    }

    $laender = $this->app->erp->GetSelectLaenderliste();
    $laenderStr = '';
    foreach ($laender as $landKey => $land) {
      $laenderStr .= '<option value="' . $landKey . '">' . $land . '</option>';
    }

    $this->app->Tpl->Set('AKTIV_TAB1',"selected");
    $this->app->Tpl->Set('INHALT',"");

    // $this->app->Tpl->Add(STATUS,$statusStr);
    // $this->app->Tpl->Add(VERSANDARTEN,$versandartenStr);

    $this->app->YUI->DatePicker("datumVon");
    $this->app->YUI->DatePicker("datumBis");
    $this->app->YUI->AutoComplete("projekt", "projektname", 1);
    $this->app->YUI->AutoComplete("lieferantennummer", "lieferant", 1);
    $this->app->YUI->AutoComplete("artikel", "artikelnummer", 1);
    $this->app->YUI->AutoComplete("bestellungnummer", "bestellung", 1);

    $this->app->Tpl->Add('ZAHLUNGSWEISEN',$zahlungsweiseStr);
    $this->app->Tpl->Add('STATUS',$statusStr);
    $this->app->Tpl->Add('VERSANDARTEN',$versandartenStr);
    $this->app->Tpl->Add('LAENDER',$laenderStr);
    $this->app->Tpl->Parse('TAB1',"bestellung_table_filter.tpl");

    $this->app->YUI->TableSearch('TAB1',"bestellungen");
    $this->app->YUI->TableSearch('TAB2',"bestellungeninbearbeitung");

    $this->app->Tpl->Set('SELDRUCKER', $this->app->erp->GetSelectDrucker($this->app->User->GetParameter('rechnung_list_drucker')));

    $this->app->Tpl->Parse('PAGE',"bestellunguebersicht.tpl");

    return;

    /*
    // suche
    $sql = $this->app->erp->BestellungSuche();

    // offene Bestellungen
    $this->app->Tpl->Set(SUBSUBHEADING,"Offene Bestellunge");

    $table = new EasyTable($this->app);
    $table->Query($sql,$_SESSION[bestellungtreffer]);

    //$table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as vom, if(a.belegnr,a.belegnr,'ohne Nummer') as Bestellung, a.name, p.abkuerzung as projekt, a.id
    //  FROM bestellung a, projekt p WHERE (a.status='freigegeben' OR a.status='versendet') AND p.id=a.projekt order by a.datum DESC, a.id DESC",10);


    $table->DisplayOwn(INHALT, "<a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
    <a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.svg\"></a>
    <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=bestellung&action=copy&id=%value%';\">
    <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
    <a onclick=\"if(!confirm('Weiterf&uuml;fhren als Auftrag?')) return false; else window.location.href='index.php?module=bestellung&action=auftrag&id=%value%';\">
    <img src=\"./themes/new/images/right.png\" border=\"0\"></a>

    ");
    $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");

    $this->app->Tpl->Set(INHALT,"");
    // wartende Bestellungen

    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%y') as vom, if(a.belegnr,a.belegnr,'ohne Nummer') as Bestellung, ad.lieferantennummer as kunde, a.name, p.abkuerzung as projekt, a.id
    FROM bestellung a, projekt p, adresse ad WHERE (a.status='freigegeben' OR a.status='versendet') AND p.id=a.projekt AND a.adresse=ad.id order by a.datum DESC, a.id DESC");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
    <a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.svg\"></a>
    <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=bestellung&action=copy&id=%value%';\">
    <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
    ");
    $this->app->Tpl->Parse(TAB2,"rahmen70.tpl");


    $this->app->Tpl->Set(INHALT,"");
    // In Bearbeitung
    $this->app->Tpl->Set(SUBSUBHEADING,"In Bearbeitung");
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%y') as vom, if(belegnr,belegnr,'ohne Nummer') as auftrag, name, vertrieb, status, id
    FROM bestellung WHERE status='angelegt' order by datum DESC, id DESC");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.svg\"></a>
    <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=bestellung&action=delete&id=%value%';\">
    <img src=\"./themes/new/images/delete.svg\" border=\"0\"></a>
    <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=bestellung&action=copy&id=%value%';\">
    <img src=\"./themes/new/images/copy.svg\" border=\"0\"></a>
    ");

    $this->app->Tpl->Parse(TAB3,"rahmen70.tpl");
     */


    /*
       $this->app->Tpl->Set(TAB2,"lieferant, bestellung, waehrung, sprache, liefertermin, steuersatz, einkäufer, freigabe<br>
       <br>Bestellung (NR),Bestellart (NB), Bestelldatum
       <br>Projekt
       <br>Kostenstelle pro Position
       <br>Terminbestellung (am xx.xx.xxxx raus damit)
       <br>vorschlagsdaten für positionen
       <br>proposition reinklicken zum ändern und reihenfolge tabelle 
       <br>Bestellung muss werden wie bestellung (bestellung beschreibung = allgemein)
       <br>Positionen (wie stueckliste)
       <br>Wareneingang / Rückstand
       <br>Etiketten
       <br>Freigabe
       <br>Dokument direkt faxen
       ");
     */
  }
  function BestellungRechnungsLieferadresse($auftragid)
  {
    $data = $this->app->DB->SelectArr("SELECT * FROM bestellung WHERE id='$auftragid' LIMIT 1");

    foreach($data[0] as $key=>$value)
    {
      if($data[0][$key]!='' && $key!=='abweichendelieferadresse' && $key!=='land' && $key!=='plz' && $key!=='lieferland' && $key!=='lieferplz') {
        $data[0][$key] = $data[0][$key].'<br>';
      }
    }


    $rechnungsadresse = $data[0]['name']."".$data[0]['ansprechpartner']."".$data[0]['abteilung']."".$data[0]['unterabteilung'].
      "".$data[0]['strasse']."".$data[0]['adresszusatz']."".$data[0]['land']."-".$data[0]['plz']." ".$data[0]['ort'];

    if($data[0]['abweichendelieferadresse']!=0){

      $lieferadresse = $data[0]['liefername']."".$data[0]['lieferansprechpartner']."".$data[0]['lieferabteilung']."".$data[0]['lieferunterabteilung'].
        "".$data[0]['lieferstrasse']."".$data[0]['lieferadresszusatz']."".$data[0]['lieferland']."-".$data[0]['lieferplz']." ".$data[0]['lieferort'];


    } else {
      $lieferadresse = "keine abweichende Lieferadresse";
    }

    return "<table width=\"100%\">
      <tr valign=\"top\"><td width=\"50%\"><b>Bestellt bei:</b><br><br>$rechnungsadresse<br></td></tr>
      <tr><td><b>Lieferadresse:</b><br><br>$lieferadresse</td></tr></table>";
  }

  public function BestellungNeuberechnen($id)
  {
    $summeV = $this->app->DB->Select(
      "SELECT IFNULL(SUM(menge*preis), 0) 
       FROM bestellung_position 
       WHERE umsatzsteuer!='ermaessigt' AND umsatzsteuer!='befreit' AND bestellung='$id' AND (isnull(steuersatz) OR steuersatz < 0)"
    );
    $summeR = $this->app->DB->Select("SELECT IFNULL(SUM(menge*preis), 0) FROM bestellung_position WHERE umsatzsteuer='ermaessigt' AND bestellung='$id' AND (isnull(steuersatz) OR steuersatz < 0)");
    $summeS = (float)$this->app->DB->Select("SELECT 
    IFNULL(
      SUM(menge*preis * 
        if(umsatzsteuer = 'befreit',
          0,
          IF(steuersatz < 0 OR isnull(steuersatz),0,steuersatz / 100)
        )
      )
    ,0) 
    FROM bestellung_position WHERE bestellung='$id' AND umsatzsteuer!='ermaessigt' AND umsatzsteuer!='normal'
          AND ((steuersatz IS NOT NULL AND steuersatz >= 0) OR umsatzsteuer='befreit')");

    $summeNetto = $this->app->DB->Select("SELECT IFNULL(SUM(menge*preis),0) FROM bestellung_position WHERE bestellung='$id'");

    $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM bestellung WHERE id='$id' LIMIT 1");

    if($ust_befreit>0)
    {
      $rechnungsbetrag = $summeNetto;
    } else {
      $rechnungsbetrag = $summeNetto + ($summeV*$this->app->erp->GetSteuersatzNormal(true,$id,'bestellung')-$summeV)+ ($summeR*$this->app->erp->GetSteuersatzErmaessigt(true,$id,'bestellung')-$summeR)+$summeS;
    }
    $this->app->DB->Update("UPDATE bestellung SET gesamtsumme='$rechnungsbetrag' WHERE id='$id' LIMIT 1");
  }

  public function DeleteBestellung($id)
  {
    if($id <= 0)
    {
      return;
    }
    $belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
    if($belegnr=='' || $belegnr=='0')
    {
      $this->app->DB->Delete("DELETE FROM bestellung_position WHERE bestellung='$id'");
      $this->app->DB->Delete("DELETE FROM bestellung_protokoll WHERE bestellung='$id'");
      $this->app->DB->Delete("DELETE FROM bestellung WHERE id='$id' LIMIT 1");
    }
  }

  public function CreateBestellung($adresse='')
  {
    $projekt = $this->app->erp->GetCreateProjekt($adresse);
    $belegmax = '';
    $ohnebriefpapier = $this->app->erp->Firmendaten('bestellung_ohnebriefpapier');
    $bestellungohnepreis = $this->app->erp->Firmendaten('bestellungohnepreis');

    $eigenartikelnummer = $this->app->erp->Firmendaten('bestellungeigeneartikelnummer');
    $bestellunglangeartikelnummern = $this->app->erp->Firmendaten('bestellunglangeartikelnummern');

    $this->app->DB->Insert("INSERT INTO bestellung (datum,bearbeiter,firma,belegnr,adresse,status,artikelnummerninfotext,ohne_briefpapier,bestellungohnepreis,projekt,langeartikelnummern)
            VALUES (NOW(),'".$this->app->User->GetName()."','".$this->app->User->GetFirma()."','$belegmax','$adresse','angelegt',".($eigenartikelnummer?'1':'0').",'".$ohnebriefpapier."','".$bestellungohnepreis."','".$projekt."',".($bestellunglangeartikelnummern?'1':'0').')');
    $id = $this->app->DB->GetInsertID();

    $this->app->erp->ObjektProtokoll('bestellung',$id,'bestellung_create','Bestellung angelegt');
    $this->app->erp->SchnellFreigabe('bestellung',$id);

    $this->app->erp->LoadSteuersaetzeWaehrung($id,'bestellung');
    $this->app->erp->EventAPIAdd('EventBestellungCreate',$id,'bestellung','create');
    return $id;
  }

  public function AddBestellungPosition($bestellung, $einkauf,$menge,$datum, $beschreibung = '',$artikel='',$einheit='', $waehrung = '')
  {
    $beschreibung = $this->app->DB->real_escape_string($beschreibung);

    if($artikel<=0)
    {
      if($einkauf > 0){
        $einkaufarr = $this->app->DB->SelectRow("SELECT * FROM einkaufspreise WHERE id='$einkauf' LIMIT 1");
      }
      if(!empty($einkaufarr))
      {
        $artikel = $einkaufarr['artikel'];
        $article = $this->app->DB->SelectRow(
          "SELECT `nummer`, `name_de`, `name_en`, `umsatzsteuer` 
          FROM `artikel` WHERE `id` = {$artikel}"
        );
        $preis = $einkaufarr['preis'];
        $projekt = $einkaufarr['projekt'];
        $waehrung = $einkaufarr['waehrung'];
        $vpe = $einkaufarr['vpe'];
        $bezeichnunglieferant = $this->app->DB->real_escape_string($einkaufarr['bezeichnunglieferant']);
        $bestellnummer = $this->app->DB->real_escape_string($einkaufarr['bestellnummer']);
      }else{
        $artikel = 0;
        $preis = 0;
        $projekt = 0;
        $waehrung = '';
        $vpe = '';
        $bezeichnunglieferant = '';
        $bestellnummer = '';
      }
    }else{
      $article = $this->app->DB->SelectRow(
        "SELECT `nummer`, `name_de`, `name_en`, `umsatzsteuer` 
        FROM `artikel` WHERE `id` = {$artikel}"
      );
      $bestellnummer = $article['nummer'];
      $bezeichnunglieferant = $this->app->DB->real_escape_string($article['name_de']);
      $projekt = $projekt = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id='$bestellung' LIMIT 1");
      $preis = 0;
    }

    if($projekt <= 0) {
      $projekt = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id='$bestellung' LIMIT 1");
    }

    if($bezeichnunglieferant==''){
      $languageIso = $this->app->erp->GetSpracheBelegISO('bestellung', $bestellung);

      if($languageIso === 'EN'){
        $bezeichnunglieferant = $this->app->DB->real_escape_string($article['name_en']);
      }

      if(empty($bezeichnunglieferant)){
        $bezeichnunglieferant = $this->app->DB->real_escape_string($article['name_de']);
      }
    }

    $umsatzsteuer = $article['umsatzsteuer'];
    if($umsatzsteuer=='') {
      $umsatzsteuer='normal';
    }
    $sort = $this->app->DB->Select("SELECT MAX(sort) FROM bestellung_position WHERE bestellung='$bestellung' LIMIT 1");
    $sort++;
    $this->app->DB->Insert("INSERT INTO bestellung_position (bestellung,artikel,bezeichnunglieferant,bestellnummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe, beschreibung,einheit)
            VALUES ('$bestellung','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$preis','$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe','$beschreibung','$einheit')");
    return $this->app->DB->GetInsertID();
  }

  public function CopyBestellung($id)
  {
    $this->app->DB->Insert('INSERT INTO bestellung (id) VALUES (NULL)');
    $newid = $this->app->DB->GetInsertID();
    $arr = $this->app->DB->SelectRow("SELECT NOW() as datum,projekt,bodyzusatz,freitext,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,versandart,einkaeufer,zahlungsweise,zahlungszieltage,'angelegt' as status,typ,
            zahlungszieltageskonto,zahlungszielskonto,firma,'angelegt' as status,abweichendelieferadresse,liefername,lieferabteilung,lieferunterabteilung,ust_befreit,
            lieferland,lieferstrasse,lieferort,lieferplz,lieferadresszusatz,lieferansprechpartner,sprache,anzeigesteuer,waehrung,kostenstelle FROM bestellung WHERE id='$id' LIMIT 1");
    $arr['bundesstaat'] = $this->app->DB->Select("SELECT bundesstaat FROM bestellung WHERE id='$id' LIMIT 1");
    $this->app->DB->UpdateArr('bestellung',$newid,'id',$arr, true);
    $pos = $this->app->DB->SelectArr("SELECT * FROM bestellung_position WHERE bestellung='$id'");
    $cpos = !empty($pos)?count($pos):0;
    for($i=0;$i<$cpos;$i++){
      $this->app->DB->Insert("INSERT INTO bestellung_position (bestellung) VALUES ($newid)");
      $newposid = $this->app->DB->GetInsertID();
      $pos[$i]['bestellung']=$newid;
      $pos[$i]['auftrag_position_id']=0;
      $this->app->DB->UpdateArr('bestellung_position',$newposid,'id',$pos[$i], true);
      if(is_null($pos[$i]['steuersatz'])){
        $this->app->DB->Update("UPDATE bestellung_position SET steuersatz = null WHERE id = '$newposid' LIMIT 1");
      }
    }

    $this->app->erp->CheckFreifelder('bestellung',$newid);
    $this->app->erp->CopyBelegZwischenpositionen('bestellung',$id,'bestellung',$newid);

    $this->app->DB->Update("UPDATE bestellung_position SET geliefert=0, mengemanuellgeliefertaktiviert=0,abgeschlossen='0',abgerechnet='0' WHERE bestellung='$newid'");
    $this->app->erp->LoadSteuersaetzeWaehrung($newid,'bestellung');

    $this->app->erp->SchnellFreigabe('bestellung',$newid);

    return $newid;
  }

  public function LoadBestellungStandardwerte($id,$adresse)
  {
    // standard adresse von lieferant
    $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
    $rolle_projekt = $this->app->DB->Select("SELECT parameter FROM adresse_rolle WHERE adresse='$adresse' AND subjekt='Lieferant' AND objekt='Projekt' AND (bis ='0000-00-00' OR bis <= NOW()) LIMIT 1");

    if($rolle_projekt > 0)
    {
      $arr[0]['projekt'] = $rolle_projekt;
    }
    $field = array('anschreiben','name','abteilung','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','email','telefon','telefax','lieferantennummer','projekt','ust_befreit','titel','lieferbedingung','ansprechpartner');
    foreach($field as $key=>$value)
    {
      if($value==='projekt' && $this->app->Secure->POST[$value]!=''&&0)
      {
        $uparr[$value] = $this->app->Secure->POST[$value];
      } else {
        $this->app->Secure->POST[$value] = str_replace("'", '&apos;',$arr[0][$value]);
        $uparr[$value] = str_replace("'", '&apos;',$arr[0][$value]);
      }
      //$this->app->Secure->POST[$value] = $arr[0][$value];
      //$uparr[$value] = $arr[0][$value];
    }
    $uparr['adresse'] = $adresse;
    $this->app->DB->UpdateArr('bestellung',$id,'id',$uparr);
    $uparr=null;

    //liefernantenvorlage
    $arr = $this->app->DB->SelectArr("SELECT
            kundennummerlieferant as kundennummer,
            zahlungsweiselieferant as zahlungsweise,
            zahlungszieltagelieferant as zahlungszieltage,
            zahlungszieltageskontolieferant as zahlungszieltageskonto,
            zahlungszielskontolieferant as zahlungszielskonto,
            versandartlieferant as versandart,
            waehrung
            FROM adresse WHERE id='$adresse' LIMIT 1");

    // falls von Benutzer projekt ueberladen werden soll
    $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
    if($projekt_bevorzugt=='1')
    {
      $uparr['projekt'] = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      $arr[0]['projekt'] = $uparr['projekt'];
      $this->app->Secure->POST['projekt']=$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr[0]['projekt']."' AND id > 0 LIMIT 1");
    }

    $field = array('kundennummer','zahlungsweise','zahlungszieltage','zahlungszieltageskonto','zahlungszielskonto','versandart','waehrung');
    foreach($field as $key=>$value)
    {
      $uparr[$value] = $arr[0][$value];
      $this->app->Secure->POST[$value] = $arr[0][$value];
    }


    $this->app->DB->UpdateArr('bestellung',$id,'id',$uparr);

    //standardprojekt
    //$projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
    //$this->app->Secure->POST[projekt] = $projekt;
    $this->app->erp->LoadAdresseStandard('bestellung',$id,$adresse);
  }

  public function BestellungEAN()
  {
    $id=$this->app->Secure->GetGET("id");
    $scanner=$this->app->Secure->GetPOST("scanner");
    $menge=$this->app->Secure->GetPOST("menge");
    $posAdd = $this->app->Secure->GetPOST('posadd');
    $posDel = $this->app->Secure->GetPost('posdel');

    /** @var ScanArticleService  $scanArticleService */
    $scanArticleService = $this->app->Container->get('ScanArticleService');

    $this->BestellungMenu();
    $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM bestellung WHERE id='$id' LIMIT 1");

    if($scanner!="" && $schreibschutz!="1")
    {
      if(!is_numeric($menge)){
        $menge=1;
      }
      $adresse = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id='$id' LIMIT 1");
      try{
        $scanArticleService->writeArticleToSession('bestellung',$scanner,$menge,$id);
      } catch(ArticleNotFoundException $e){
        $this->app->Tpl->Set('TAB1',"<div class=\"error\">{|Der Artikel wurde nicht gefunden|}: ".$scanner."</div>");
      } catch(InvalidArgumentException $e){
        $this->app->Tpl->Set('TAB1',"<div class=\"error\">{|Fehler beim Schreiben in die Session|}</div>");
      }
    }

    if($schreibschutz=="1"){
      $this->app->Tpl->Set('TAB1',"<div class=\"info\">Der Schreibschutz ist aktiviert. Bitte entfernen Sie diesen.</div>");
    }
    else{

      if(!empty($posAdd)){
        try{
          $scanArticleService->savePositions('bestellung',$id);
          $this->app->Tpl->Set('MESSAGE',"<div class=\"info\">{|Positionen hinzugefügt|}</div>");
        } catch(Exception $e){
          $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">{|Positionen nicht gespeichert|}!</div>");
        }
      }

      if(!empty($posDel)){
        $scanArticleService->clearAllArticleDataInSession('bestellung');
      }

      $gescannteArtikel = $scanArticleService->getAllArticleDataFromSession('bestellung');
      if(!empty($gescannteArtikel)){

        $table = new EasyTable($this->app);
        $table->headings = ['Artikelnummer','Artikel','Menge','Preis'];

        foreach($gescannteArtikel as $a){

          $row['nummer'] = $a['number'];
          $row['name'] = $a['name'];
          $row['menge'] = $a['amount'];
          $row['preis'] = $a['price'];

          if(!empty($row)){
            $table->AddRow($row);
          }
        }
        $table->DisplayNew('GESCANNTEARTIKEL','Preis','noAction');
        $this->app->Tpl->Set('POSITIONENSPEICHERN','<input type="submit" id="posadd" name="posadd" value="Positionen hinzufügen"><input type="submit" id="posdel" name="posdel" value="Löschen">');
      }

      $this->app->Tpl->Parse('TAB1',"bestellung_ean.tpl");
    }
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }
}
