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
use Xentral\Components\Http\RedirectResponse;
use Xentral\Modules\Report\ReportGateway;
use Xentral\Modules\Report\ReportLegacyConverterService;

include '_gen/berichte.php';

class Berichte extends GenBerichte {
  /** @var Application  */
  var $app;

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public function TableSearch($app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case 'berichte_live':
        $allowed['berichte'] = array('live');

        $id = $app->Secure->GetGET('id');

        $arr = $this->app->DB->SelectRow('SELECT * FROM berichte WHERE id = '.$id);

        $spaltenausrichtung = explode(';',rtrim($this->app->erp->ReadyForPDF($arr['spaltenausrichtung']),';'));
        foreach($spaltenausrichtung as $k => $v) {
          if($v === 'R') {
            $alignright[] = ($k+1);
          }
          elseif($v === 'C'){
            $aligncenter[] = ($k+1);
          }
        }
        $spaltennamen = rtrim($arr['spaltennamen'],';');
        $spaltennamen = explode(';', $spaltennamen);
        foreach($spaltennamen as $key=>$value){
          $heading[] = $value;
          $findcols[] = 'berichtelive.tab'.$key;
          $findcols2[] = 'tab'.$key;
          $dummy[] = "'' as tab".$key;
          $weitereswhere[] = 'tab'.$key."!=''";
        }
        $heading[] = '';

        $summenspalten = rtrim($arr['sumcols'],';');
        $summenspalten = explode(';', $summenspalten);
        foreach($summenspalten as $k => $v)
        {
          $v = (int)$v;
          if($v <= 0)
          {
            unset($summenspalten[$k]);
          }else{
            $summenspalten[$k] = $v;
          }
        }
        if(!empty($summenspalten))
        {
          $sumcol = $summenspalten;
        }


        $spaltenbreite = rtrim($arr['spaltenbreite'],';');
        $spaltenbreite = explode(';', $spaltenbreite);
        foreach($spaltenbreite as $key=>$value){
          $width[] = $value.'%';
        }
        $width[] = '1%';

        $struktur = $arr['struktur'];

        $variablen = $arr['variablen'];
        if($variablen != ''){
          $variablen = explode(';', trim($variablen));
          foreach($variablen as $key=>$value){
            if($value != ''){
              $wert = explode('=', trim($value));
              if(trim($wert[0]) != ''){
                $struktur = str_replace(trim($wert[0]), trim($wert[1]), $struktur);
              }
            }        
          }
        }

        $struktur = rtrim($struktur, ';');

        $struktur = str_replace('&apos;',"'",$struktur);

        $sql = 'SELECT SQL_CALC_FOUND_ROWS '.$findcols[0].','.implode(',',$findcols2).",".$findcols[0]." FROM ((SELECT ".implode(',', $dummy)." LIMIT 0) UNION ALL ($struktur)) as berichtelive";
        
        $findcols[] = $findcols[0];
        $searchsql = $findcols;

        $defaultorder = 1;
        $defaultorderdesc = 0;
        
        $where = '('.implode(' OR ',$weitereswhere).')';

        $app->DB->Query($sql .' WHERE '.$where);
        if($app->DB->error())
        {
          $sql = 'SELECT SQL_CALC_FOUND_ROWS '.$findcols[0].','.implode(',',$findcols2).",".$findcols[0]." FROM ((SELECT ".implode(',', $dummy)." LIMIT 0) UNION ALL $struktur) as berichtelive";
        }

        if(!$this->sqlok($sql.' WHERE '.$where)) {
          $sql = '';
          $struktur = '';
          $where = '';
        }

        $app->DB->Query($sql .' WHERE '.$where);
        if($app->DB->error())
        {
          $query = $app->DB->Query($struktur);
          $fields = $app->DB->Fetch_Fields($query);
          if(!empty($fields))
          {
            $findcols = array();
            $searchsql = array();
            $pos2 = 0;
            foreach($fields as $v)
            {
              $findcols[]  = '`'.trim($v->name,'`').'`';
              if($pos2 !== false){
                $pos2 = strpos($struktur, $v->name, $pos2) + strlen($v->name);
              }
              if(!empty($v->table) && !empty($v->orgname))
              {
                $searchsql[] = $v->table.'.'.$v->orgname;
              }
            }
            //$searchsql = $findcols;
            $findcols[] = '`' . trim($fields[0]->name, '`') . '`';
            if($pos2 !== false){
              $pos2 = stripos($struktur,'FROM',$pos2);
              if($pos2 !== false){
                $struktur = substr($struktur,0,$pos2).', 0 '.substr($struktur,$pos2);
                if(stripos($struktur, 'SQL_CALC_FOUND_ROWS') === false){
                  $pos1 = stripos($struktur, 'SELECT');
                  if($pos1 !== false){
                    $struktur = substr($struktur, 0, $pos1) . ' SELECT SQL_CALC_FOUND_ROWS 0, ' . substr($struktur, $pos1 + 6);
                  }
                }
              }
              $sql = $struktur;
              $where = '';
              $posorder = strripos($sql, 'ORDER');
              if($posorder !== false){
                $posby = stripos($sql, 'BY', $posorder-1);
                if($posby !== false)
                {
                  $orderby = substr($sql, $posorder);
                  $sql = substr($sql,0, $posorder);
                }
              }
              $app->DB->Query($sql.' WHERE 1');
              if($app->DB->error())
              {
                $posgroup = strripos($sql, 'GROUP');
                if($posgroup !== false){
                  $posby = stripos($sql, 'BY', $posgroup-1);
                  if($posby === false)
                  {
                    $posgroup = false;
                  }
                }
                $poswhere = strripos($sql, 'WHERE');
                if($posgroup !== false && $posgroup > (int)$poswhere)
                {
                  $groupby = substr($sql, $posgroup);
                  $sql = substr($sql,0, $posgroup);
                  if($poswhere)
                  {
                    $app->DB->Query($sql.' WHERE 1');
                  }
                }
                if($poswhere && $app->DB->error())
                {
                  $where = substr($sql, $poswhere+6);
                  $sql = substr($sql,0, $poswhere);
                }
              }
            }
          }
        }


      break;
    }

    $erg = array();

    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v)){
        $erg[$v] = $$v;
      }
    }
    return $erg;
  }

  /**
   * Berichte constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern){
      return;
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","BerichteCreate");
    $this->app->ActionHandler("edit","BerichteEdit");
    $this->app->ActionHandler("list","BerichteList");
    $this->app->ActionHandler("delete","BerichteDelete");
    $this->app->ActionHandler("pdf","BerichtePDF");
    $this->app->ActionHandler("csv","BerichteCSV");
    $this->app->ActionHandler("live","BerichteLive");
    $this->app->ActionHandler("convert","BerichteToReport");

    $this->app->ActionHandlerListen($app);
  }

  public function Install(){
    $this->app->erp->RegisterHook('Angebot_Aktion_option', 'berichte', 'BerichtZuBelegAktionOption');
    $this->app->erp->RegisterHook('Angebot_Aktion_case', 'berichte', 'BerichtZuBelegAktionCase');

    $this->app->erp->RegisterHook('Auftrag_Aktion_option', 'berichte', 'BerichtZuBelegAktionOption');
    $this->app->erp->RegisterHook('Auftrag_Aktion_case', 'berichte', 'BerichtZuBelegAktionCase');
  
    $this->app->erp->RegisterHook('Gutschrift_Aktion_option', 'berichte', 'BerichtZuBelegAktionOption');
    $this->app->erp->RegisterHook('Gutschrift_Aktion_case', 'berichte', 'BerichtZuBelegAktionCase');

    $this->app->erp->RegisterHook('Rechnung_Aktion_option', 'berichte', 'BerichtZuBelegAktionOption');
    $this->app->erp->RegisterHook('Rechnung_Aktion_case', 'berichte', 'BerichtZuBelegAktionCase');

    $this->app->erp->RegisterHook('Lieferschein_Aktion_option', 'berichte', 'BerichtZuBelegAktionOption');
    $this->app->erp->RegisterHook('Lieferschein_Aktion_case', 'berichte', 'BerichtZuBelegAktionCase');

    $this->app->erp->RegisterHook('Bestellung_Aktion_option', 'berichte', 'BerichtZuBelegAktionOption');
    $this->app->erp->RegisterHook('Bestellung_Aktion_case', 'berichte', 'BerichtZuBelegAktionCase');

    $this->app->erp->RegisterHook('Produktion_Aktion_option', 'berichte', 'BerichtZuBelegAktionOption');
    $this->app->erp->RegisterHook('Produktion_Aktion_case', 'berichte', 'BerichtZuBelegAktionCase');
  }


  /**
   * @param $id
   * @param $projectStatus
   * @param $option
   */
  public function BerichtZuBelegAktionOption($id, $projectStatus, &$option)
  {
    if(!$this->app->erp->RechteVorhanden('berichte','csv') && !$this->app->erp->RechteVorhanden('berichte','pdf')){
      return;
    }
    $module = $this->app->Secure->GetGET("module");
    $data = $this->app->DB->SelectArr(sprintf("SELECT b.name,b.doctype_actionmenuname,b.doctype_actionmenufiletype,b.id
      FROM berichte AS b 
      WHERE b.doctype_actionmenu=1 AND b.doctype='%s' ".$this->app->erp->ProjektRechte('b.project'),$module));
    if(empty($data)) {
      return;
    }

    foreach($data as $rows) {
      $option .= '<option value="berichte_'.$rows['id'].'">'.($rows['doctype_actionmenuname']!=""?$rows['doctype_actionmenuname']:$rows['name']).'</option>';
    }
  }

  /**
   * @param $id
   * @param $projectStatus
   * @param $case
   */
  public function BerichtZuBelegAktionCase($id, $projectStatus, &$case)
  {
    if(!$this->app->erp->RechteVorhanden('berichte','csv') && !$this->app->erp->RechteVorhanden('berichte','pdf')){
      return;
    }
    $module = $this->app->Secure->GetGET('module');
    $data = $this->app->DB->SelectArr(
      sprintf(
        "SELECT b.name,b.doctype_actionmenuname,b.doctype_actionmenufiletype,b.id
        FROM berichte AS b
        WHERE b.doctype_actionmenu=1 AND b.doctype='%s' ". $this->app->erp->ProjektRechte('b.project'),
        $module));
    if(empty($data)) {
      return;
    }

    foreach($data as $rows) {
      $case .= "case 'berichte_".$rows['id']."': window.location.href='index.php?module=berichte&action=".$rows['doctype_actionmenufiletype']."&id=".$rows['id']."&var1=%value%'; break;";
    }
  }

  public function BerichteCreate()
  {
    $this->BerichteMenu();
    parent::BerichteCreate();
  }

  public function BerichteDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    if(is_numeric($id) && $id > 0)
    {
      $this->app->DB->Delete('DELETE FROM berichte WHERE id='.$id);
    }

    $this->BerichteList();
  }
  
  public function sqlok($sql)
  {
    if(preg_match_all('/(.*)SELECT(.*)INTO(.*)/i',$sql, $result)){
      return false;
    }
    if(preg_match_all("/(.*)INSERT(\W+)(.*)INTO(.*)/i",$sql, $result)){
      return false;
    }
    if(preg_match_all('/(.*)DELETE(.*)FROM(.*)/i',$sql, $result)){
      return false;
    }
    if(preg_match_all("/(.*)UPDATE(\W+)(.*)/i",$sql, $result)){
      return false;
    }
    if(preg_match_all("/(.*)LOAD(\W+)DATA(.*)/i",$sql, $result)){
      return false;
    }
    if(preg_match_all("/(.*)LOAD(\W+)XML(.*)/i",$sql, $result)){
      return false;
    }
    if(preg_match_all("/(.*)DROP(\W+)(.*)/i",$sql, $result)){
      return false;
    }
    if(preg_match_all("/(.*)CREATE(\W+)(.*)/i",$sql, $result)){
      return false;
    }
    if(preg_match_all("/(.*)RENAME(\W+)(.*)/i",$sql, $result)){
      return false;
    }
    if(preg_match_all("/(.*)TRUNCATE(\W+)(.*)/i",$sql, $result)){
      return false;
    }
    if(preg_match_all("/(.*)ALTER(\W+)(.*)/i",$sql, $result)){
      return false;
    }
    if(preg_match_all("/(\W*)SHOW(\W+)(.*)/i",$sql, $result)){
      return false;
    }
    if(preg_match_all("/(.*)SHOW(\W+)CREATE(.*)/i",$sql, $result)){
      return false;
    }
    if(preg_match_all("/(.*)SHOW(\W+)DATABASES(.*)/i",$sql, $result)){
      return false;
    }
    if(preg_match_all("/(.*)SHOW(\W+)TABLES(.*)/i",$sql, $result)){
      return false;
    }
    if(preg_match_all("/(.*)USE(\W+)DATABASES(.*)/i",$sql, $result)){
      return false;
    }
    //if(preg_match_all("/(.*)REPLACE(\W+)(.*)/i",$sql, $result))return false;
    return true;
  }

  public function BerichteCSV($id='',$cronjob=false)
  {
    if(!$cronjob) {
      $id = $this->app->Secure->GetGET('id');
    }
    if($id > 0){
      $berichteArr = $this->app->DB->SelectRow("SELECT * FROM berichte WHERE id = $id LIMIT 1");
    }
    if(empty($berichteArr))
    {
      if($cronjob)
      {
        return;
      }
      $this->app->Location->execute('index.php?module=berichte&action=edit&id='.$id);

    }


    $struktur = $berichteArr['struktur'];
    if(!$this->sqlok($struktur))
    {
      if($cronjob)
      {
        return;
      }
      $this->app->Location->execute('index.php?module=berichte&action=edit&id='.$id);
    }
    $variablen = $berichteArr['variablen'];
    if($variablen != ''){
      $variablen = explode(';', trim($variablen));
      $varindex = 1;
      foreach($variablen as $key=>$value){
        if($value != ''){
          $wert = explode('=', trim($value));
          $varfromget = $this->app->Secure->GetGET("var".$varindex);
          if($varfromget!="") $wert[1] = $varfromget;
          if(trim($wert[0]) != ''){
            $struktur = str_replace(trim($wert[0]), trim($wert[1]), $struktur);
          }
        }        
        $varindex++;
      }
    }
        
    $name = $this->app->erp->ReadyForPDF(str_replace(' ','',($this->app->DB->Select("SELECT name FROM berichte WHERE id='$id' LIMIT 1"))));
    if($name == ''){
      $name = '_Id_' . $id;
    }
    $spaltenbreite = $this->app->erp->ReadyForPDF($berichteArr['spaltenbreite']);
    $spaltennamen = $this->app->erp->ReadyForPDF($berichteArr['spaltennamen']);
    $spaltenausrichtung = $this->app->erp->ReadyForPDF($berichteArr['spaltenausrichtung']);
    $sumcols = $this->app->erp->ReadyForPDF($berichteArr['sumcols']);
    $sumcolsa = explode(';',$sumcols);
    foreach($sumcolsa as $k => $v)
    {
      $v = (int)$v;
      if($v <= 0)
      {
        unset($sumcolsa[$k]);
      }else{
        $sumcolsa[$k] = $v;
      }
    }
    $struktur = str_replace('&apos;',"'",$struktur);
    if(!$this->sqlok($struktur)) {
      $struktur = '';
    }
    $arr =empty($struktur)?null: $this->app->DB->Query($struktur);

    $header = explode(';',$spaltennamen);
    $w = explode(';',$spaltenbreite);
    $ausrichtung = explode(';',$spaltenausrichtung);

    $filenameftp = $berichteArr['ftpnamealternativ'];
    if($filenameftp != ''){
      $filenameftp = str_replace('{BERICHTNAME}', $name, $filenameftp);
      $filenameftp = str_replace('{TIMESTAMP}', date('YmdHis'), $filenameftp);
      $filenameftp = str_replace('{DATUM}', date('Ymd'), $filenameftp);
    }
    $filenameemail = $berichteArr['emailnamealternativ'];
    if($filenameemail != ''){
      $filenameemail = str_replace('{BERICHTNAME}', $name, $filenameemail);
      $filenameemail = str_replace('{TIMESTAMP}', date('YmdHis'), $filenameemail);
      $filenameemail = str_replace('{DATUM}', date('Ymd'), $filenameemail);
    }
    //header('Content-Encoding: UTF-8');
    //header('Content-type: text/csv; charset=UTF-8');


    $filename = date('Ymd').'_Bericht_'.$name.'.csv';

    if(!$cronjob) {
      header('Content-Disposition: attachment; filename=' . $filename);
      header('Pragma: no-cache');
      header('Expires: 0');
    }
    $csv = '';
    //spaltennamen
    $countcolumns = 0;
    if(count($header) > count($w)) {
      $countcolumns = count($header);
    } else {
      $countcolumns = count($w);
    }
    $cheader = count($header);
    for($i=0;$i<$cheader;$i++)
    {
      if(!isset($header[$i])) {
        $header[$i]='';
      }
      $csv .= '"'.html_entity_decode($header[$i]).'";';
    }

    $csv .= "\r\n";

    while($row = $this->app->DB->Fetch_Row($arr))
    {
      $colcounter = 0;
      foreach($row as $key=>$value){
        $csv .= '"'.html_entity_decode($value).'";';
        if(!empty($sumcolsa) && in_array($key+1,$sumcolsa))
        {
          if(empty($sums[$key])){
            $sums[$key] = 0;
          }
          $sums[$key] += $this->app->erp->ReplaceBetrag(1, $value);
        }
      }
      $csv .= "\r\n";
    }

    if(!empty($sums))
    {
      for($colcounter = 0;$colcounter<$cheader;$colcounter++)
      {
        $csv .= '"'.html_entity_decode(isset($sums[$colcounter])?$this->app->erp->ReadyForPDF(number_format($sums[$colcounter],2,',','.')):'').'";';
      }
      $csv .= "\r\n";
    }

    if(!$cronjob) {
      echo $csv;
    }
    else {
      $data['filename'] = $filename;
      $data['filenameftp'] = $filenameftp;
      $data['filenameemail'] = $filenameemail;
      $data['csv'] = $csv;
      return $data;
    }
    $this->app->ExitXentral();
  }
  
  public function BerichtePDF()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    if($id > 0){
      $berichteArr = $this->app->DB->SelectRow("SELECT * FROM berichte WHERE id = $id LIMIT 1");
    }
    if(empty($berichteArr))
    {
      $this->app->Location->execute('index.php?module=berichte&action=edit&id='.$id);
    }

    $struktur = $berichteArr['struktur'];
    if(!$this->sqlok($struktur))
    {
      $this->app->Location->execute('index.php?module=berichte&action=edit&id='.$id);
    }

    $variablen = $berichteArr['variablen'];
    if($variablen != ''){
      $variablen = explode(';', trim($variablen));
      foreach($variablen as $key=>$value){
        if($value != ''){
          $wert = explode('=', trim($value));
          if(trim($wert[0]) != ''){
            $struktur = str_replace(trim($wert[0]), trim($wert[1]), $struktur);
          }
        }        
      }
    }


    $name = $this->app->erp->ReadyForPDF(str_replace(' ','',$berichteArr['name']));
    $nameaufpdf = $this->app->erp->ReadyForPDF($berichteArr['name']);
    $spaltenbreite = $this->app->erp->ReadyForPDF($berichteArr['spaltenbreite']);
    $spaltennamen = $this->app->erp->ReadyForPDF($berichteArr['spaltennamen']);
    $spaltenausrichtung = $this->app->erp->ReadyForPDF($berichteArr['spaltenausrichtung']);
    $sumcols = $this->app->erp->ReadyForPDF($berichteArr['sumcols']);
    $sumcolsa = explode(';',$sumcols);
    foreach($sumcolsa as $k => $v)
    {
      $v = (int)$v;
      if($v <= 0)
      {
        unset($sumcolsa[$k]);
      }else{
        $sumcolsa[$k] = $v;
      }
    }
    $struktur = str_replace('&apos;',"'",$struktur);
    if(!$this->sqlok($struktur)) {
      $struktur = '';
    }
    $arr = empty($struktur)?null: $this->app->DB->Query($struktur);

    define('FPDF_FONTPATH2','lib/pdf/font2');
    require dirname(__DIR__).'/lib/pdf/fpdf_org.php';
    $w = explode(';',$spaltenbreite);
    $pdf=new SuperFPDF();
    $pdf->AddPage();

    $pdf->SetFillColor(255,255,255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetLineWidth(.3);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(array_sum($w),7,"Bericht: $nameaufpdf (Ausdruck vom ".date("d.m.Y").")",1,0,'L',true);
    $pdf->Ln();
    $pdf->SetFont('Arial','',8);

    $header = explode(';',$spaltennamen);

    $ausrichtung = explode(';',$spaltenausrichtung);
    $cw = count($w);
    for($i=0;$i<$cw;$i++) {
      $pdf->Cell($w[$i], 7, $header[$i], 1, 0, $ausrichtung[$i], true);
    }
    $pdf->Ln();
    $sums = [];
    while($row = $this->app->DB->Fetch_Row($arr))
    {
      $colcounter = 0;
      foreach($row as $key=>$value){
        $pdf->Cell($w[$colcounter],6,$this->app->erp->ReadyForPDF($value),'LRTB',0,$ausrichtung[$colcounter],true);
        if(!empty($sumcolsa) && in_array($key+1,$sumcolsa))
        {
          if(empty($sums[$key])){
            $sums[$key] = 0;
          }
          $sums[$key] += $this->app->erp->ReplaceBetrag(1, $value);
        }

        $colcounter++;
      }

      for($colcounter;$colcounter<$cw;$colcounter++) {
        $pdf->Cell($w[$colcounter], 6, '', 'LRTB', 0, $ausrichtung[$colcounter], true);
      }

      $pdf->Ln();
    }
    if(!empty($sums)) {
      $pdf->Ln();
      for($colcounter = 0;$colcounter<$cw;$colcounter++) {
        $pdf->Cell($w[$colcounter], 6, isset($sums[$colcounter])?$this->app->erp->ReadyForPDF(number_format($sums[$colcounter],2,',','.')):'', 'LRTB', 0, $ausrichtung[$colcounter], true);
      }
    }

    //$pdf->Cell($w[1],6,$this->app->erp->LimitChar($name_de,30),'LRTB',0,'L',$fill);

    $pdf->SetFont('Arial','',8);
    $pdf->Output(date('Ymd').'_BR_'.$name.'.pdf','D');
    $this->app->ExitXentral();
  }

  public function BerichteList()
  {
    $this->app->erp->Headlines('Berichte');
    //$id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag('index.php?module=berichte&action=create','Neuen Bericht anlegen');
    $this->app->erp->MenuEintrag('index.php?module=berichte&action=list','&Uuml;bersicht');
    $this->showNewAppHint();
    $this->app->erp->checkActiveCronjob('berichte_ftp_uebertragen');

    $this->app->YUI->TableSearch('TAB1','berichte');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function BerichteMenu()
  {
    $this->app->erp->Headlines('Berichte');
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->MenuEintrag('index.php?module=berichte&action=create','Berichte Neu');
    $this->app->erp->MenuEintrag('index.php?module=berichte&action=live&id='.$id, '&Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=berichte&action=pdf&id='.$id,'als PDF anzeigen');
    $this->app->erp->MenuEintrag('index.php?module=berichte&action=csv&id='.$id,'als CSV anzeigen');
    $this->app->erp->MenuEintrag('index.php?module=berichte&action=edit&id='.$id,'Einstellungen');

  }

  public function BerichteEdit()
  {
    $this->BerichteMenu();
    $this->showHintTransferToNewApp();
    parent::BerichteEdit();
  }

  public function BerichteLive(){
    $id = $this->app->Secure->GetGET('id');
    $this->BerichteMenu();
    $struktur = $this->app->DB->Select("SELECT struktur FROM berichte WHERE id='$id' LIMIT 1");
    if(!$this->sqlok($struktur)) {
      $this->app->Location->execute('Location: index.php?module=berichte&action=edit&id='.$id);
    }

    $this->app->YUI->TableSearch('TAB4', 'berichte_live', 'show', '', '', basename(__FILE__), __CLASS__);

    $this->app->Tpl->Parse('PAGE', 'berichte_live.tpl');
  }

  public function BerichteToReport()
  {
      $berichtId = (int)$this->app->Secure->GetPOST('bericht_id');
      if (!$this->app->Container->has('ReportLegacyConverterService')) {
          $redirect = RedirectResponse::createFromUrl(
              sprintf('index.php?module=berichte&action=edit&id=%s', $berichtId)
          );
          $redirect->send();
          $this->app->ExitXentral();
      }

      try {
          /** @var ReportLegacyConverterService $converter */
          $converter = $this->app->Container->get('ReportLegacyConverterService');
          $newId = $converter->convertLegacyReport($berichtId);

          $redirect = RedirectResponse::createFromUrl(sprintf('index.php?module=report&action=edit&id=%s', $newId));
          $redirect->send();
          $this->app->ExitXentral();
      } catch (Exception $e) {
          $redirect = RedirectResponse::createFromUrl(
              sprintf('index.php?module=berichte&action=edit&id=%s', $berichtId)
          );
          $redirect->send();
          $this->app->ExitXentral();
      }
  }

  protected function showNewAppHint()
  {
      /** @var Report $reportModule */
      $reportModule = $this->app->erp->LoadModul('Report');
      if ($reportModule === null || !$reportModule->isUpdateMessageNeedet()) {
          return;
      }
      $this->app->Tpl->Add(
          'MESSAGE',
          '<div class="warning">
                    Es gibt eine <a href="?module=appstore&action=list&cmd=detail&app=report">neue Berichte App</a>.
                    Sie können jetzt Ihre Berichte zur neuen App übertragen um die neuen Features für Ihre Berichte zu nutzen.
                    Sie können im Bericht den Button "Jetzt übertragen" klicken um den Berich zu übernehmen.                 
                  </div>'
      );
  }

  protected function showHintTransferToNewApp()
  {
      if (
          !$this->app->Container->has('ReportGateway')
          || !$this->app->Container->has('ReportLegacyConverterService')
      ) {
          return;
      }
      $id = (int)$this->app->Secure->GetGET('id');
      if ($id === 0) {
          return;
      }

      /** @var ReportGateway $gateway */
      $gateway = $this->app->Container->get('ReportGateway');

      //check if a report with this name already exists
      $sql = sprintf('SELECT name FROM `berichte` WHERE id = %s LIMIT 1', $id);
      $reportName = $this->app->DB->Select($sql);
      if (empty($reportName)) {
          return;
      }

      $existingNewReport = $gateway->findReportByName($reportName);
      if ($existingNewReport === null) {
          $message = sprintf(
              '<form action="index.php?module=berichte&action=convert" method="post">
                    <div class="info">Sie können diesen Bericht jetzt in die neue Berichte App übertragen.
                        <input type="hidden" name="bericht_id" value="%s">
                        <input type="submit" name="convert" value="Jetzt übertragen">           
                    </div>
                  </form>  ',
              $id
          );
      } else {
          $message = sprintf(
              '<div class="info">Dieser Bericht wurde bereits in die neue App übertragen.
                    <a href="?module=report&action=edit&id=%s">Bericht in der neuen App ansehen.</a>
                  </div>',
              $existingNewReport->getId()
          );
      }

      $this->app->Tpl->Add('MESSAGE', $message);
  }
}

