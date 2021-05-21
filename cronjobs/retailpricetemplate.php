<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include_once __DIR__.'/../www/lib/class.remote.php';
include_once __DIR__.'/../phpwf/class.application_core.php';
include_once __DIR__.'/../phpwf/class.application.php';
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");

class app_t {
  var $DB;
  var $erp;
  var $user;
  var $remote;
}
*/
//ENDE
if(empty($app->Conf)){
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)){
  $app->DB = new DB($conf->WFdbhost, $conf->WFdbname, $conf->WFdbuser, $conf->WFdbpass, $app, $conf->WFdbport);
}
if(!isset($app->erp) || !$app->erp) {
  if (class_exists('erpAPICustom')) {
    $erp = new erpAPICustom($app);
  } else {
    $erp = new erpAPI($app);
  }
  $app->erp = $erp;
}

if (is_file(dirname(__DIR__) . '/www/lib/class.remote_custom.php')) {
  if(!class_exists('RemoteCustom')){
    require_once dirname(__DIR__) . '/www/lib/class.remote_custom.php';
  }
  $app->remote = new RemoteCustom($app);
} else {
  $app->remote = new Remote($app);
}

$app->DB->Update("UPDATE prozessstarter SET mutexcounter = mutexcounter + 1 WHERE mutex = 1 AND parameter = 'retailpricetemplate' AND aktiv = 1");

if($app->DB->Select("SELECT mutex FROM prozessstarter WHERE parameter = 'retailpricetemplate' LIMIT 1") == 1){
  return;
}


$templates = $app->DB->SelectArr('SELECT rpt.id, rpt.article_id FROM retail_price_template rpt WHERE rpt.active=1');
foreach ($templates as $template){
  $templatePrices = $app->DB->SelectArr('SELECT * FROM verkaufspreise WHERE geloescht=0 AND artikel='.$template['article_id']);
  $assignedArticles = $app->DB->SelectArr('SELECT * FROM retail_price_template_assignment WHERE retail_price_template_id='.$template['id']);

  foreach ($assignedArticles as $assignedArticle){
    $articlePrices = $app->DB->SelectArr('SELECT * FROM verkaufspreise WHERE geloescht=0 AND artikel='.$assignedArticle['article_id']);

    foreach ($templatePrices as $templatePrice){
      $matching = false;
      foreach ($articlePrices as $articlePriceKey => $articlePrice){

        if($articlePrice['projekt'] == $templatePrice['projekt'] &&
          $articlePrice['adresse'] == $templatePrice['adresse'] &&
          $articlePrice['objekt'] == $templatePrice['objekt'] &&
          $articlePrice['preis'] == $templatePrice['preis'] &&
          $articlePrice['waehrung'] == $templatePrice['waehrung'] &&
          $articlePrice['ab_menge'] == $templatePrice['ab_menge'] &&
          $articlePrice['vpe'] == $templatePrice['vpe'] &&
          $articlePrice['vpe_menge'] == $templatePrice['vpe_menge'] &&
          $articlePrice['gueltig_bis'] == $templatePrice['gueltig_bis'] &&
          $articlePrice['bemerkung'] == $templatePrice['bemerkung'] &&
          $articlePrice['bearbeiter'] == $templatePrice['bearbeiter'] &&
          $articlePrice['firma'] == $templatePrice['firma'] &&
          $articlePrice['kundenartikelnummer'] == $templatePrice['kundenartikelnummer'] &&
          $articlePrice['art'] == $templatePrice['art'] &&
          $articlePrice['gruppe'] == $templatePrice['gruppe'] &&
          $articlePrice['apichange'] == $templatePrice['apichange'] &&
          $articlePrice['gueltig_ab'] == $templatePrice['gueltig_ab'] &&
          $articlePrice['kurs'] == $templatePrice['kurs']){
          $matching = true;
          $articlePrices[$articlePriceKey]['keep'] = true;
          break;
        }
      }

      if(!$matching){
        $app->DB->Insert("INSERT INTO verkaufspreise (artikel, objekt, projekt, 
        adresse, preis, waehrung, 
        ab_menge, vpe, vpe_menge, 
        angelegt_am, gueltig_bis, bemerkung, 
        bearbeiter, firma, 
        geloescht,kundenartikelnummer, art, 
        gruppe, apichange, nichtberechnet, 
        gueltig_ab, kurs, kursdatum) 
        VALUES 
        ('$assignedArticle[article_id]', '$templatePrice[objekt]','$templatePrice[projekt]',
         '$templatePrice[adresse]', '$templatePrice[preis]', '$templatePrice[waehrung]',
         '$templatePrice[ab_menge]', '$templatePrice[vpe]', '$templatePrice[vpe_menge]',
         '$templatePrice[angelegt_am]', '$templatePrice[gueltig_bis]', '$templatePrice[bemerkung]',
         '$templatePrice[bearbeiter]', '$templatePrice[firma]',
         '0', '$templatePrice[kundenartikelnummer]', '$templatePrice[art]',
         '$templatePrice[gruppe]', '$templatePrice[apichange]', '$templatePrice[nichtberechnet]',
         '$templatePrice[gueltig_ab]', '$templatePrice[kurs]', '$templatePrice[kursdatum]')");
      }
      $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 1,mutexcounter=0 WHERE parameter = 'retailpricetemplate'");
    }

    foreach ($articlePrices as $articlePrice){
      if(empty($articlePrice['keep'])){
        $app->DB->Delete('UPDATE verkaufspreise SET geloescht=1, gueltig_bis=NOW() WHERE id='.$articlePrice['id']);
      }
    }

  }
}

$app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW(), mutex = 0,mutexcounter=0 WHERE parameter = 'retailpricetemplate'");

