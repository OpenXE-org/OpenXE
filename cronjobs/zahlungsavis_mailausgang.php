<?php
$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND parameter = 'zahlungsavis_mailausgang' AND aktiv = 1");

if($app->DB->Select("SELECT mutex FROM prozessstarter WHERE parameter = 'zahlungsavis_mailausgang' LIMIT 1") == 1){
  return;
}
$avisids = $app->DB->SelectArr('SELECT avis_id FROM zahlungsavis_mailausgang WHERE versendet=0 AND versucht<5 ORDER BY id ASC');
if(!empty($avisids)){
  foreach ($avisids as $avisid){

    $avisid = $avisid['avis_id'];

    $query = sprintf('UPDATE zahlungsavis_mailausgang SET versucht=versucht+1 WHERE avis_id=%d', $avisid);
    $app->DB->Update($query);

    $query = sprintf(
      "SELECT 
      a.name, 
      IF(a.rechnungs_email='',a.email,a.rechnungs_email) AS email, 
      z.projekt, 
      CONCAT(DATE_FORMAT(z.datum,'%%Y%%m%%d'),'/',z.id) AS avis 
      FROM zahlungsavis z 
      JOIN adresse a ON z.adresse = a.id 
      WHERE z.id = '%d'",
      $avisid
    );

    $adressDaten = $app->DB->SelectRow($query);
    if(!empty($adressDaten)){
      $to = $adressDaten['email'];
      $to_name = $adressDaten['name'];
      $projekt = $adressDaten['projekt'];

      if(!empty($to)){

        $Brief = new ZahlungsavisPDF($app,$projekt);
        $Brief->GetZahlungsavis($avisid);

        $text = $app->erp->GetGeschaeftsBriefText('LastschriftenZahlungsavis','deutsch',$projekt);
        $betreff = $app->erp->GetGeschaeftsBriefBetreff('LastschriftenZahlungsavis','deutsch',$projekt);
        if(empty($text) || empty($betreff)){
          break;
        }
        $datei = $Brief->displayTMP();
        $success = $app->erp->MailSend($app->erp->GetFirmaMail(), $app->erp->GetFirmaAbsender(), $to, $to_name, $betreff, $text, [$datei], $projekt);

        if($success){
          $query = sprintf("UPDATE zahlungsavis SET versendet=1, versendet_am=NOW(),zahlungsavis.versendet_per='mail' WHERE id=%d",
            $avisid);
          $app->DB->Update($query);

          $query = sprintf("UPDATE zahlungsavis_mailausgang SET versendet = 1 WHERE avis_id=%d",
            $avisid);
          $app->DB->Update($query);
        }
        unlink($datei);
      }
    }
  }
}

$app->DB->Update("UPDATE zahlungsavis_mailausgang zm
    JOIN zahlungsavis z ON zm.avis_id = z.id
    SET z.versendet_per='Mailversand fehlgeschlagen'
    WHERE zm.versendet = 0 AND zm.versucht = 5 AND z.versendet_per='vorgemerkt'");

$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 0,mutexcounter=0 WHERE parameter = 'zahlungsavis_mailausgang'");