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

class Layoutvorlagen {
  /** @var Application $app */
  var $app;

  /**
   * Layoutvorlagen constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    $this->app = $app;
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler("list", "LayoutvorlagenList");
    $this->app->ActionHandler("edit", "LayoutvorlagenEdit");
    $this->app->ActionHandler("create", "LayoutvorlagenCreate");
    $this->app->ActionHandler("copy", "LayoutvorlagenCopy");
    $this->app->ActionHandler("getposition", "LayoutvorlagenGetPosition");
    $this->app->ActionHandler("saveposition", "LayoutvorlagenSavePosition");
    $this->app->ActionHandler("createposition", "LayoutvorlagenCreatePosition");
    $this->app->ActionHandler("deleteposition", "LayoutvorlagenDeletePosition");
    $this->app->ActionHandler("delete", "LayoutvorlagenDelete");
    $this->app->ActionHandler("download", "LayoutvorlagenDownload");
    $this->app->ActionHandler("export", "LayoutvorlagenExport");
    //$this->app->ActionHandler("import", "LayoutvorlagenImport");

    $this->app->ActionHandler("imgvorschau", "LayoutvorlagenImgVorschau");

    $this->app->erp->Headlines('Layoutvorlagen');

    $this->app->ActionHandlerListen($app);
  }
  
  public function LayoutvorlagenCopy()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    if($id)
    {
      $layoutvorlage = $this->app->DB->SelectArr("SELECT * FROM layoutvorlagen WHERE id = '$id'");
      if($layoutvorlage)
      {
        $this->app->DB->Insert("INSERT INTO layoutvorlagen (id) VALUES('')");
        $newvorlage = $this->app->DB->GetInsertID();
        $layoutvorlage[0]['name'] .= ' (Kopie)';
        $this->app->FormHandler->ArrayUpdateDatabase("layoutvorlagen",$newvorlage,$layoutvorlage[0],true);
        $positionen = $this->app->DB->SelectArr("SELECT * FROM layoutvorlagen_positionen WHERE layoutvorlage = '$id'");
        if($positionen)
        {
          foreach($positionen as $position)
          {
            $this->app->DB->Insert("INSERT INTO layoutvorlagen_positionen (id) VALUES('')");
            $newvorlagepos = $this->app->DB->GetInsertID();
            $position['layoutvorlage'] = $newvorlage;
            $this->app->FormHandler->ArrayUpdateDatabase("layoutvorlagen_positionen",$newvorlagepos,$position, true);
          }
        }
      }
    }
    header('Location: index.php?module=layoutvorlagen&action=list');
    exit;
  }

  public function LayoutvorlagenDownload($id = 0)
  {  
    if(!$id)$id = $this->app->Secure->GetGET('id');
    // mit infos aus zertifikat und konkreten inhalten
    $projekt = "";
    $Brief = new LayoutvorlagenPDF($this->app, $projekt);
    $Brief->GetLayoutvorlage($id);
    $Brief->inlineDocument();
  }


  public function LayoutvorlagenMenu() {
    $this->app->erp->MenuEintrag("index.php?module=layoutvorlagen&action=list","&Uuml;bersicht");
  }

  public function LayoutvorlagenList() {

    //$this->LayoutvorlagenMenu();
    $this->app->erp->MenuEintrag("index.php?module=layoutvorlagen&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=layoutvorlagen&action=create","Neu");

    $layoutanlegen = $this->app->Secure->GetPOST('layoutanlegen');


    if ($layoutanlegen) {

      $row['name'] = $this->app->Secure->GetPOST('name');
      //$row['name'] = preg_replace('#[^-A-Za-z0-9]#', '-', $row['name']);
      $row['typ'] = $this->app->Secure->GetPOST('typ');
      $row['format'] = $this->app->Secure->GetPOST('format');
      $row['kategorie'] = $this->app->Secure->GetPOST('kategorie');

      if ($row['name']) {

        $this->app->DB->Insert('
          INSERT INTO 
              layoutvorlagen
          SET 
              name = "' . $row['name'] . '", 
              typ = "' . $row['typ'] . '", 
              format = "' . $row['format'] . '", 
              kategorie = "' . $row['kategorie'] . '"
        ');

        $id = $this->app->DB->GetInsertID();
        if ($id) {
          header('location: index.php?module=layoutvorlagen&action=edit&id=' . $id);
          exit;
        }
      }
    }
    if($this->app->Secure->GetPOST('cmd') === 'import') {
      if (!empty($_FILES['importfile']['tmp_name'])) {
        $string = file_get_contents($_FILES['importfile']['tmp_name']);
        $ret = $this->importJson($string, $this->app->Secure->GetPOST('ueberschreiben'));
        if(!empty($ret['message'])) {
          $this->app->Tpl->Add('ERRORMSG', $ret['message']);
        }
      }
      else {
        $this->app->Tpl->Add('ERRORMSG', "Keine Datei ausgew&auml;lt");
      }
    }
    
    
    $this->app->YUI->TableSearch('TABELLE', 'layoutvorlagen_list');
    $this->app->Tpl->Parse('PAGE',"layoutvorlagen_list.tpl");

  }

  /**
   * @param string $string
   * @param bool   $overwrite
   *
   * @return array
   */
  public function importJson($string, $overwrite = false)
  {
    $ret = [];
    if(!(NULL !== $json = json_decode($string))) {
      return ['status' => 0, 'message' => 'Keine g&uuml;ltige Datei'];
    }
    if(empty($json->Layout) && empty($json->Layout->name)) {
      return ['status' => 0, 'message' => 'Keine g&uuml;ltige Datei Fehlendes Element: Layout'];
    }

    $altesLayout = $this->app->DB->SelectArr(
      sprintf(
        "select * from layoutvorlagen where name like '%s'",
        $this->app->DB->real_escape_string($json->Layout->name)
      )
    );

    if(!empty($altesLayout) && !$overwrite) {
      return ['status' => 0, 'message' => 'Es existiert bereis ein Layout mit dem Namen','id' => $altesLayout['id']];
    }

    if(isset($json->Layout->id)) {
      unset($json->Layout->id);
    }
    $columns = $this->app->DB->SelectArr('SHOW COLUMNS FROM layoutvorlagen');
    $error = false;
    foreach($json->Layout as $k => $v) {
      $found = false;
      foreach($columns as $k2 => $v2) {
        if($v2['Field'] == $k) {
          $found = true;
        }
      }
      if(!$found) {
        $error = true;
      }
    }
    $columnspos = $this->app->DB->SelectArr('SHOW COLUMNS FROM layoutvorlagen_positionen');
    if(!empty($json->Layoutpositionen)) {
      foreach($json->Layoutpositionen as $k => $pos) {
        if(isset($pos->id)) {
          unset($json->Layoutpositionen[$k]->id);
        }
        if(isset($pos->layoutvorlage)) {
          unset($json->Layoutpositionen[$k]->id);
        }

        foreach($pos as $kp => $vp) {
          $found = false;
          foreach($columnspos as $k2 => $v2) {
            if($v2['Field'] == $kp) {
              $found = true;
            }
          }
          if(!$found) {
            $error = true;
          }
        }
      }
    }
    if(!empty($error)) {
      return ['status' => 0, 'message' => 'Keine g&uuml;ltige Datei: falsche Elemente'];
    }

    $query = "insert into layoutvorlagen (";
    $i = 0;
    foreach($columns as $k => $v) {
      if($v['Field'] !== 'id') {
        $i++;
        if($i > 1) {
          $query .= ', ';
        }
        $query .= $v['Field'];
      }
    }
    $query .= ') values (';
    $i = 0;
    foreach($columns as $k => $v) {
      if($v['Field'] !== 'id') {
        $i++;
        if($i > 1) {
          $query .= ', ';
        }
        $query .= "'";
        $fieldName = $v['Field'];
        if(isset($json->Layout->$fieldName)) {
          $query .= $this->app->DB->real_escape_string($json->Layout->$fieldName);
        }
        $query .= "'";
      }
    }
    $query .= ')';

    //alte Löschen falls existiert
    if($altesLayout) {
      foreach($altesLayout as $l) {
        if($l['id']) {
          $this->app->DB->Delete("delete from layoutvorlagen_positionen where layoutvorlage = ".$l['id']);
          $this->app->DB->Delete("delete from layoutvorlagen where id = ".$l['id']);
        }
      }
    }
      //
    $this->app->DB->Insert($query);
    $newid = $this->app->DB->GetInsertID();

    if(empty($newid)) {
      return ['status' => 0, 'message' => 'Fehler beim Erstellen des Layouts'];
    }

    $j = 0;
    foreach ($json->Layoutpositionen as $kpos => $pos) {

      $querypos[$j] = "insert into layoutvorlagen_positionen (layoutvorlage";
      $i = 0;
      foreach($columnspos as $k => $v) {
        if($v['Field'] !== 'id' && $v['Field'] !== 'layoutvorlage') {
          $i++;
          $querypos[$j] .= ', ';
          $querypos[$j] .= $v['Field'];
        }
      }

      $querypos[$j] .= ") values ('".$newid."'";
      $i = 0;
      foreach($columnspos as $k => $v) {
        if($v['Field'] !== 'id' && $v['Field'] !== 'layoutvorlage') {
          $i++;
          $querypos[$j] .= ', ';
          $querypos[$j] .= "'";
          $fieldName = $v['Field'];
          if(isset($pos->$fieldName)) {
            $querypos[$j] .= $this->app->DB->real_escape_string($pos->$fieldName);
          }
          $querypos[$j] .= "'";
        }
      }

      $querypos[$j] .= ")";
      $j++;
    }
    if(isset($querypos)) {
      $fehler = false;
      foreach($querypos as $qp) {
        $this->app->DB->Insert($qp);
        if($this->app->DB->error()){
          $ret['error'] = $this->app->DB->error();
          $fehler = true;
        }
      }
    }
    if($fehler) {
      return [
        'status' => 0,
        'message' => (empty($ret['error'])?'':$ret['error'].' ')
        . 'Fehler beim Erstellen von einer oder mehreren Layoutposition(en)'
      ];
    }

    return [
      'message' => 'Layout ' .$json->Layout->name. ' erfolgreich erstellt',
      'status' => true, 'id' => $newid
    ];
  }

  public function LayoutvorlagenEdit() {

    $id = $this->app->Secure->GetGET('id');
    $cmd = $this->app->Secure->GetGET('cmd');
    $speichern = $this->app->Secure->GetPOST('layoutspeichern');

    if ($speichern) {
      $name = $this->app->Secure->GetPOST('name');
      $typ = $this->app->Secure->GetPOST('typ');
      $format = $this->app->Secure->GetPOST('format');
      $kategorie = $this->app->Secure->GetPOST('kategorie');
      $projekt = $this->app->Secure->GetPOST('layoutvorlagen_projekt');
      $delete_hintergrund = $this->app->Secure->GetPOST('delete_hintergrund')==''?false:true;
      $pdf_hintergrund = $_FILES['pdf_hintergrund'];

      if (isset($pdf_hintergrund['tmp_name']) && ($pdf_hintergrund['type'] == 'application/pdf' || $pdf_hintergrund['type'] == 'application/force-download' || $pdf_hintergrund['type'] =='binary/octet-stream' || $pdf_hintergrund['type'] == 'application/octetstream')) {
        $fp = fopen($pdf_hintergrund['tmp_name'], 'r');
        $imgContent = fread($fp, filesize($pdf_hintergrund['tmp_name']));
        fclose($fp);
        $sets[] = 'pdf_hintergrund = "' . base64_encode($imgContent) . '"';
      } elseif($delete_hintergrund) {
        $sets[] = 'pdf_hintergrund = ""';
      }

      $sets[] = 'name = "' . $name . '" ';
      $sets[] = 'typ = "' . $typ . '" ';
      $sets[] = 'format = "' . $format . '" ';
      $sets[] = 'kategorie = "' . $kategorie . '" ';

      if ($sets) {
        $this->app->DB->Insert('UPDATE layoutvorlagen SET ' . implode(', ', $sets) . ' WHERE id = ' . $id);
      }

      if($projekt != ''){
        $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$projekt' LIMIT 1");
      }else{
        $projektid = 0;
      }

      $this->app->DB->Update("UPDATE layoutvorlagen SET projekt = '$projektid' WHERE id = '$id'");

    }

    $this->app->YUI->AutoComplete("kategorie","layoutvorlagenkategorie");
    $this->app->YUI->AutoComplete("layoutvorlagen_projekt", "projektname", 1);
    //$this->app->erp->MenuEintrag("index.php?module=layoutvorlagen&action=create","Neu");
    $this->app->erp->MenuEintrag("index.php?module=layoutvorlagen&action=edit&id=" . $id . "","Details");

    $vorlage = $this->app->DB->SelectArr('SELECT * FROM layoutvorlagen WHERE id = ' . $id);
    $vorlage = reset($vorlage);

    if ($cmd) {
      switch ($cmd) {
        case 'pdfvorschau':
          $pdf_hintergrund = $this->app->DB->Select('SELECT pdf_hintergrund FROM layoutvorlagen WHERE id = ' . $id);
          $pdf_hintergrund = base64_decode($pdf_hintergrund);

          header("Content-type: application/pdf");
          header('Content-disposition: attachment; filename="pdf_hintergrund.pdf"');
          print $pdf_hintergrund;


          break;
        default:
          break;
      }
      exit;
    }

    $this->app->User->SetParameter('layoutvorlagen_id', $id);

    $this->app->Tpl->Add('NAME', $vorlage['name']);
    $this->app->Tpl->Add('KATEGORIE', $vorlage['kategorie']);

    if($vorlage['projekt'] > 0){
      $projektname = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '".$vorlage['projekt']."' LIMIT 1");
      if($projektname != ""){
        $this->app->Tpl->Add('PROJEKT', $projektname);
      }
    }

    if ($vorlage['pdf_hintergrund']) {
      $this->app->Tpl->Add('PDFVORSCHAU', '<input type="button" name="" onclick="window.open(\'index.php?module=layoutvorlagen&action=edit&id=' . $id . '&cmd=pdfvorschau\', \'_blank\')" value="Vorschau">');
    }
    $this->app->Tpl->Add('TAB3', '<iframe src="index.php?module=layoutvorlagen&action=download&id='.$id.'" width="100%" height="600"></iframe>');
    /*
    $schriftarten = $this->app->erp->GetSchriftarten();
    //Test
    $schriftarten['times'] = "Times";
    $schriftarten['juliusc'] = 'juliusc';
    $schriftarten['bernard'] = 'Bernard';
    $schriftarten['HLBC____'] = 'HLBC____';
    */
    $schriftartena = $this->app->erp->GetFonts();
    foreach($schriftartena as $kk => $vv)
    {
      $schriftarten[$kk] = $vv['name'];
      
    }
    //Test End
    $schriftartenTpl = '';
    if ($schriftarten) {
      foreach ($schriftarten as $schriftartKey => $schriftart) {
        $schriftartenTpl .= '<option value="' . $schriftartKey . '">' . $schriftart . '</option>';
      }  
    }
    $this->app->Tpl->Add(SCHRIFTARTEN, $schriftartenTpl);

    $rahmenbreiten = array(
        '0' => 'Kein Rahmen',
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
        '8' => '8',
        '9' => '9',
        '10' => '10'
    );
    $rahmenTpl = '';
    if ($rahmenbreiten) {
      foreach ($rahmenbreiten as $rahmenbreiteKey => $rahmenbreite) {
        $rahmenTpl .= '<option value="' . $rahmenbreiteKey . '">' . $rahmenbreite . '</option>';
      }  
    }

    $positionen = $this->app->DB->SelectArr('
        SELECT
            id,
            name,
            typ
        FROM
            layoutvorlagen_positionen
        WHERE
            layoutvorlage = "' . $id . '"
    ');

    $positionenTpl = '';
    $positionenTpl .= '<option value="0">Keine</option>';
    if ($positionen) {
      foreach ($positionen as $position) {
        $positionenTpl .= '<option value="' . $position['id'] . '">' . $position['name'] . ' (' . $position['typ'] . ')</option>';
      }
    }

    $schriftausrichtungen = array('left' => 'Links', 'center' => 'Zentriert', 'right' => 'Rechts');
    $schriftausrichtungenTpl = '';
    if ($schriftausrichtungen) {
      foreach($schriftausrichtungen as $schriftausrichtungKey => $schriftausrichtung) {
        $schriftausrichtungenTpl .= '<option value="' . $schriftausrichtungKey . '">' . $schriftausrichtung . '</option>';
      }
    }

    $formate = array('A4' => 'DIN A4 Hoch', 'A4L' => 'DIN A4 Quer','A5' => 'DIN A5 Hoch', 'A5L' => 'DIN A5 Quer','A6' => 'DIN A6 Hoch', 'A6L' => 'DIN A6 Quer');
    $formatTpl = '';
    if ($formate) {
      foreach($formate as $formatKey => $formatBeschriftung) {
        $formatTpl .= '<option value="' . $formatKey . '" '.($vorlage['format']==$formatKey?'selected':'').'>' . $formatBeschriftung . '</option>';
      }
    }



    $this->app->YUI->ColorPicker("schrift_farbe");
    $this->app->YUI->ColorPicker("hintergrund_farbe");
    $this->app->YUI->ColorPicker("rahmen_farbe");

    $this->app->Tpl->Add('SCHRIFTAUSRICHTUNGEN', $schriftausrichtungenTpl);
    $this->app->Tpl->Add('POSITIONPARENT', $positionenTpl);

    $this->app->Tpl->Add('FORMAT', $formatTpl);
    $this->app->Tpl->Add('RAHMEN', $rahmenTpl);
    $this->app->YUI->TableSearch('TABELLE', 'layoutvorlagen_edit');
    $this->app->Tpl->Parse('PAGE',"layoutvorlagen_edit.tpl");

  }

  public function LayoutvorlagenCreate() {

    $speichern = $this->app->Secure->GetPOST('layouterstellen');

    if ($speichern) {

      
      $felder = array('name', 'typ', 'format', 'kategorie');
      $sets = array();
      if ($felder) {
        foreach ($felder as $feld) {
          $sets[] = $feld . ' = "' . $this->app->Secure->GetPOST($feld) . '"';
        }
      }

      $projekt = $this->app->Secure->GetPOST('layoutvorlagen_projekt');
      if($projekt != ''){
        $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$projekt' LIMIT 1");
      }else{
        $projektid = 0;
      }

      $query = ('INSERT INTO layoutvorlagen SET ' . implode(', ', $sets) . ' ');
      $this->app->DB->Insert($query);


      $layoutvorlagenId = $this->app->DB->GetInsertID();

      $this->app->DB->Update("UPDATE layoutvorlagen SET projekt = '$projektid' WHERE id = '$layoutvorlagenId'");
    

      $delete_hintergrund = $this->app->Secure->GetPOST('delete_hintergrund')==''?false:true;
      $pdf_hintergrund = $_FILES['pdf_hintergrund'];
      if (isset($pdf_hintergrund['tmp_name']) && ($pdf_hintergrund['type'] == 'application/pdf' || $pdf_hintergrund['type'] == 'application/force-download' || $pdf_hintergrund['type'] =='binary/octet-stream' || $pdf_hintergrund['type'] == 'application/octetstream')) {
        $fp = fopen($pdf_hintergrund['tmp_name'], 'r');
        $imgContent = fread($fp, filesize($pdf_hintergrund['tmp_name']));
        fclose($fp);
        $sets[] = 'pdf_hintergrund = "' . base64_encode($imgContent) . '"';
      } elseif($delete_hintergrund) {
        $sets[] = 'pdf_hintergrund = ""';
      }

      if ($sets) {
        $this->app->DB->Insert('UPDATE layoutvorlagen SET ' . implode(', ', $sets) . ' WHERE id = ' . $layoutvorlagenId);
      }

    
      
      
      
      if ($layoutvorlagenId) {
        header('location: index.php?module=layoutvorlagen&action=edit&id=' . $layoutvorlagenId);
        exit;
      }

    }

    $this->app->YUI->AutoComplete("kategorie","layoutvorlagenkategorie");
    $this->app->YUI->AutoComplete("layoutvorlagen_projekt", "projektname", 1);

    $this->app->erp->MenuEintrag("index.php?module=layoutvorlagen&action=create","Erstellen");
    $this->app->Tpl->Parse('PAGE','layoutvorlagen_create.tpl');

  }

  public function LayoutvorlagenGetPosition() {

    $id = $this->app->Secure->GetPOST('id');
    $row = $this->app->DB->SelectRow('SELECT * FROM layoutvorlagen_positionen WHERE id = ' . $id);

    if ($row['bild_deutsch']) {
      $bilddata['bild_deutsch'] = '<a href="index.php?module=layoutvorlagen&action=imgvorschau&id=' . $row['id'] . '&cmd=de" class="ilink" target="_blank">VORSCHAU</a>';
      unset($row['bild_deutsch']);
    }

    if ($row['bild_englisch']) {
      $bilddata['bild_englisch'] = '<a href="index.php?module=layoutvorlagen&action=imgvorschau&id=' . $row['id'] . '&cmd=en" class="ilink" target="_blank">VORSCHAU</a>';
      unset($row['bild_englisch']);
    }

    echo json_encode(array(
      'status' => 1,
      'statusText' => '',
      'row' => $row,
      'bilddata' => $bilddata
    ));
    $this->app->ExitXentral();
  }


  public function LayoutvorlagenSavePosition() {

    $id = $this->app->Secure->GetPOST('id');
    $typ = $this->app->Secure->GetPOST('typ');
    
    $name = $this->app->Secure->GetPOST('name');
    $name = strtolower($name);
    $name = preg_replace('#[^-A-Za-z0-9]#', '-', $name);

    $beschreibung = $this->app->Secure->GetPOST('beschreibung');
    $position_typ = $this->app->Secure->GetPOST('position_typ');
    $position_x = $this->app->Secure->GetPOST('position_x');
    $position_y = $this->app->Secure->GetPOST('position_y');
    $position_parent = $this->app->Secure->GetPOST('position_parent');
    $breite = $this->app->Secure->GetPOST('breite');
    $hoehe = $this->app->Secure->GetPOST('hoehe');
    $schrift_art = $this->app->Secure->GetPOST('schrift_art');
    $schrift_groesse = $this->app->Secure->GetPOST('schrift_groesse');
    $zeilen_hoehe = $this->app->Secure->GetPOST('zeilen_hoehe');
    $schrift_align = $this->app->Secure->GetPOST('schrift_align');
    $schrift_farbe = $this->app->Secure->GetPOST('schrift_farbe');
    $hintergrund_farbe = $this->app->Secure->GetPOST('hintergrund_farbe');
    $rahmen = $this->app->Secure->GetPOST('rahmen');
    $rahmen_farbe = $this->app->Secure->GetPOST('rahmen_farbe');
    $sichtbar = ($this->app->Secure->GetPOST('sichtbar')=='')?'0':'1';
    $schrift_fett = ($this->app->Secure->GetPOST('schrift_fett')=='')?'0':'1';
    $schrift_kursiv = ($this->app->Secure->GetPOST('schrift_kursiv')=='')?'0':'1';
    $schrift_underline = ($this->app->Secure->GetPOST('schrift_underline')=='')?'0':'1';
    //$this->app->erp->LogFile("sichtbar: ".$sichtbar.".");
    $inhalt_deutsch = $this->app->Secure->GetPOST('inhalt_deutsch');
    $inhalt_englisch = $this->app->Secure->GetPOST('inhalt_englisch');
    $layoutvorlage = (int)$this->app->Secure->GetPOST('layoutvorlage');
    $sort = (int)$this->app->Secure->GetPOST('sort');
    $zeichenbegrenzung = (int)$this->app->Secure->GetPOST('zeichenbegrenzung');
    $zeichenbegrenzung_anzahl = (int)$this->app->Secure->GetPOST('zeichenbegrenzung_anzahl');
    
    $layoutvorlagenpos = $this->app->DB->SelectArr("select id, sort from layoutvorlagen_positionen where layoutvorlage = ".$layoutvorlage." and id <> ".$id." order by sort");
    $i = 0;

    if(isset($layoutvorlagenpos[0]))
    {
      foreach($layoutvorlagenpos as $key => $pos)
      {
        $i++;
        if($i < $sort && $i != $pos['sort'] )
        {
          $this->app->DB->Update("update layoutvorlagen_positionen set sort = ".$i." where id = ".$pos['id']);
        }
        if($i >= $sort && $i + 1 != $pos['sort'])
        {
          $this->app->DB->Update("update layoutvorlagen_positionen set sort = ".($i + 1)." where id = ".$pos['id']);
        }
      }
    }
    if($sort < 1)
    {
      $sort = 1;
    }
    if($sort > $i + 1)
    {
      $sort = $i + 1;
    }
    
    $sets = array();
    $sets[] = 'typ = "' . $typ . '"';
    $sets[] = 'name = "' . $name . '"';
    $sets[] = 'beschreibung = "' . $beschreibung . '"';
    $sets[] = 'position_typ = "' . $position_typ . '"';
    $sets[] = 'position_x = "' . $position_x . '"';
    $sets[] = 'position_y = "' . $position_y . '"';
    $sets[] = 'position_parent = "' . $position_parent . '"';
    $sets[] = 'breite = "' . $breite . '"';
    $sets[] = 'hoehe = "' . $hoehe . '"';
    $sets[] = 'schrift_art = "' . $schrift_art . '"';
    $sets[] = 'schrift_groesse = "' . $schrift_groesse . '"';
    $sets[] = 'zeilen_hoehe = "' . $zeilen_hoehe . '"';
    $sets[] = 'schrift_fett = "' . $schrift_fett . '"';
    $sets[] = 'schrift_kursiv = "' . $schrift_kursiv . '"';
    $sets[] = 'schrift_underline = "' . $schrift_underline . '"';
    $sets[] = 'schrift_align = "' . $schrift_align . '"';
    $sets[] = 'schrift_farbe = "' . $schrift_farbe . '"';
    $sets[] = 'hintergrund_farbe = "' . $hintergrund_farbe . '"';
    $sets[] = 'rahmen = "' . $rahmen . '"';
    $sets[] = 'rahmen_farbe = "' . $rahmen_farbe . '"';
    $sets[] = 'sichtbar = "' . $sichtbar . '"';
    $sets[] = 'inhalt_deutsch = "' . $inhalt_deutsch . '"';
    $sets[] = 'inhalt_englisch = "' . $inhalt_englisch . '"';
    $sets[] = 'layoutvorlage = "' . $layoutvorlage . '"';
    $sets[] = 'sort = "' . $sort . '"';
    $sets[] = 'zeichenbegrenzung = "' . $zeichenbegrenzung . '"';
    $sets[] = 'zeichenbegrenzung_anzahl = "' . $zeichenbegrenzung_anzahl . '"';
    
    if (isset($_FILES['bild_deutsch']['tmp_name'])) {
      if ($_FILES['bild_deutsch']['type'] == 'image/jpeg' || $_FILES['bild_deutsch']['type'] == 'image/png') {
          
        $imgtype = exif_imagetype($_FILES['bild_deutsch']['tmp_name']);
        $img_type = '';
        switch($imgtype)
        {
          case IMAGETYPE_GIF:
            $img_type = 'GIF';
          break;
          case IMAGETYPE_JPEG:
            $img_type = 'JPEG';
          break;
          case IMAGETYPE_PNG:
            $img_type = 'PNG';
          break;
          case IMAGETYPE_ICO:
            $img_type = 'ICO';
          break;
          case IMAGETYPE_BMP:
            $img_type = 'BMP';
          break;
            
        }
        $fp = fopen($_FILES['bild_deutsch']['tmp_name'], 'r');
        $sets[] = 'bild_deutsch_typ = "' . $img_type . '"';
        $imgContent = fread($fp, filesize($_FILES['bild_deutsch']['tmp_name']));
        fclose($fp);
        $sets[] = 'bild_deutsch = "' . base64_encode($imgContent) . '"';
      }
    }

    if (isset($_FILES['bild_englisch']['tmp_name'])) {
      if ($_FILES['bild_englisch']['type'] == 'image/jpeg' || $_FILES['bild_englisch']['type'] == 'image/png') {
          
        $imgtype = exif_imagetype($_FILES['bild_deutsch']['tmp_name']);
        $img_type = '';
        switch($imgtype)
        {
          case IMAGETYPE_GIF:
            $img_type = 'GIF';
          break;
          case IMAGETYPE_JPEG:
            $img_type = 'JPEG';
          break;
          case IMAGETYPE_PNG:
            $img_type = 'PNG';
          break;
          case IMAGETYPE_ICO:
            $img_type = 'ICO';
          break;
          case IMAGETYPE_BMP:
            $img_type = 'BMP';
          break;
            
        }
        $sets[] = 'bild_englisch_typ = "' . $img_type . '"';
        $fp = fopen($_FILES['bild_englisch']['tmp_name'], 'r');
        $imgContent = fread($fp, filesize($_FILES['bild_englisch']['tmp_name']));
        fclose($fp);
        $sets[] = 'bild_englisch = "' . base64_encode($imgContent) . '"';
      }
    }
    


    if($id) {
      $query = ('UPDATE layoutvorlagen_positionen SET ' . implode(',', $sets) . ' WHERE id = ' . $id);
      $saveType = 'UPDATE';
    } else {

//        $layoutvorlage = $this->app->DB->Select("SELECT layoutvorlage FROM  layoutvorlagen_positionen WHERE id='$id'");
      $checkname = $this->app->DB->Select('
        SELECT id FROM layoutvorlagen_positionen WHERE name = "' . $name . '" AND layoutvorlage="'.$layoutvorlage.'"
      ');

      if ($checkname) {
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Name bereits vergeben.</div> ");
        header('location: index.php?module=layoutvorlagen&action=edit&id=' . $layoutvorlage . '&msg=' . $msg);
        exit;
      }
      
      $query = ('INSERT INTO layoutvorlagen_positionen SET ' . implode(' , ', $sets));
      $saveType = 'INSERT';
    }

    $this->app->DB->Insert($query);

    header('location: index.php?module=layoutvorlagen&action=edit&id=' . $layoutvorlage."#tabs-2");
    exit;

  }

  public function LayoutvorlagenDelete() {

    $id = (int)$this->app->Secure->GetGET('id');

    if($id > 0){
      $this->app->DB->Delete('DELETE FROM layoutvorlagen WHERE id = ' . $id);
      $this->app->DB->Delete('DELETE FROM layoutvorlagen_positionen WHERE layoutvorlage = ' . $id);
    }

    echo json_encode(array(
        'status' => 1,
        'statusText' => 'Gelöscht.'
    ));

    $this->app->ExitXentral();
  }

  public function LayoutvorlagenDeletePosition() {

    $id = (int)$this->app->Secure->GetGET('id');
    if($id <= 0){
      echo json_encode(array(
        'status' => 0,
        'statusText' => 'ID ungültig: nicht Gelöscht.'
        ));
      $this->app->ExitXentral();
    }
    $parent = $this->app->DB->SelectArr("SELECT sort, position_parent, layoutvorlage from layoutvorlagen_positionen where id = ".$id);
    if($parent[0]['position_parent'] !== false)
    {
      $this->app->DB->Update("UPDATE layoutvorlagen_positionen SET parent = ".$parent[0]['position_parent']." where parent = ".$id);
    }
    $this->app->DB->Delete('DELETE FROM layoutvorlagen_positionen WHERE id = ' . $id);
    $this->app->DB->Update('UPDATE layoutvorlagen_positionen set sort = sort - 1 where sort > '.$parent[0]['sort'].' and layoutvorlage = '.$parent[0]['layoutvorlage']);

    echo json_encode(array(
        'status' => 1,
        'statusText' => 'Gelöscht.'
    ));

    $this->app->ExitXentral();
  }

  public function LayoutvorlagenImgVorschau() {

    $id = $this->app->Secure->GetGET('id');
    $cmd = $this->app->Secure->GetGET('cmd');

    if ($cmd == 'de') {
      $bildA = $this->app->DB->SelectArr('SELECT bild_deutsch, bild_deutsch_typ FROM layoutvorlagen_positionen WHERE id = ' . $id);
      if(!isset($bildA[0]))
      {
        $this->app->ExitXentral();
      }
      $bild = $bildA[0]['bild_deutsch'];
      $type = $bildA[0]['bild_deutsch_typ'];
    } else if ($cmd == 'en') {
      $bildA = $this->app->DB->SelectArr('SELECT bild_englisch, bild_englisch_typ FROM layoutvorlagen_positionen WHERE id = ' . $id);
      if(!isset($bildA[0]))
      {
        $this->app->ExitXentral();
      }
      $bild = $bildA[0]['bild_englisch'];
      $type = $bildA[0]['bild_englisch_typ'];

    }

    $bild = base64_decode($bild);

    if ($bild) {

      $im = imagecreatefromstring($bild);
      if ($im !== false) {
        //$type = strtolower($type);
        $type = '';
        if($type == '')$type = $this->get_img_type($bild);
        if($type == 'jpg')
        {
          $type = 'jpeg';
        }
        header('Content-Type: image/'.$type);
        switch(strtolower($type)){
          case "png": 
            imagepng($im); break;
          case "jpeg": case "jpg": 
            imagejpeg($im); break;
          case "gif": 
            imagegif($im); break;
          default:
          
          break;
        }
        imagedestroy($im);
      }

    }

    $this->app->ExitXentral();
  }

  function get_img_type($data) {
    $magics = array(
      'ffd8ff' => 'jpg',
      '89504e470d0a1a0a' => 'png',
    );
       
    foreach ($magics as $str => $ext) {
      if (strtolower(bin2hex(substr($data, 0, strlen($str)/2))) == $str)
      {
        return $ext;
      }
    }
       
    return NULL;
  }
  
  public function LayoutvorlagenExport()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    if($id > 0)
    {
      if($Layout = $this->app->DB->SelectArr("select * from layoutvorlagen where id = ".$id." limit 1"))
      {
        $Layout = reset($Layout);
        $Layoutpositionen = $this->app->DB->SelectArr("select * from layoutvorlagen_positionen where layoutvorlage = ".$id);
        header('Conent-Type: application/json');
        header("Content-Disposition: attachment; filename=\"Layout".$this->app->erp->Dateinamen((trim($Layout['name'])!= ''?'_'.$Layout['name']:(trim($Layout['beschreibung']) != ''?'_'.$Layout['beschreibung']:''))).".json\"");
        $Datei['Layout'] = $Layout;
        if(!empty($Layoutpositionen))
        {
          $Datei['Layoutpositionen'] = $Layoutpositionen;
        }
        echo json_encode($Datei);
        $this->app->ExitXentral();
      }
    }
  }

}
