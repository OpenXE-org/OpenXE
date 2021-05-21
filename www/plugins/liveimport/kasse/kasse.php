<?php

class kasse
{
  function __construct()
  {
  }

  function Import($kasse,$app)
  {
    $checkpos = $app->DB->Select("SELECT COUNT(id) FROM projekt WHERE kasse_konto='$kasse'");
    $csv = "";
    if($checkpos <=0 )
    {
      $letzterabschluss = $app->DB->Select("SELECT MAX(datum) FROM kasse WHERE konto='$kasse' AND tagesabschluss='1'");
      $von = $app->DB->Select("SELECT MIN(datum) FROM kasse WHERE konto='$kasse'");
      if($letzterabschluss!="") $csv = $app->erp->KasseExport($kasse,$von,$letzterabschluss,true);
    } else {
      $von = $app->DB->Select("SELECT MAX(buchung) FROM kontoauszuege WHERE konto='$kasse'");
      if($von=="") $von = $app->DB->Select("SELECT MIN(datum) FROM kasse WHERE konto='$kasse'");
      $csv = $app->erp->KasseExport($kasse,$von,date('Y-m-d'),true);
    }
    return $csv;
/*
    $result = $app->DB->SelectArr("SELECT k.auswahl,k.datum,k.betrag,a.name,k.grund,k.nummer FROM kasse k LEFT JOIN adresse a ON k.adresse=a.id WHERE k.konto='$kasse'");
    //user login information

    $csv = "";
    for ($i=0;$i<count($result);$i++)
    {
      if($result[$i]['name']=="") $result[$i]['name'] = $result[$i]['grund'];

      $csv .= $result[$i]['datum'].";";
      if($result[$i]['auswahl']=="einnahme")
        $csv .= $result[$i]['betrag'].";";
      else
        $csv .="-".$result[$i]['betrag'].";";
      $csv .= $result[$i]['name'].";";
      $csv .= $result[$i]['grund'].";";
      $csv .= "Kasse $kasse Buchung ".$result[$i]['nummer'].";";
      $csv .= "EUR;\r\n";
    }
*/
    return $csv;
  }

}
?>
