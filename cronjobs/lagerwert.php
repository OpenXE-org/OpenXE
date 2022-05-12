<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/lib/class.remote.php");
include(dirname(__FILE__)."/../www/lib/class.httpclient.php");
include(dirname(__FILE__)."/../www/lib/class.aes.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");



class app_t {
  var $DB;
  var $erp;
  var $user;
  var $remote;
}
*/
//ENDE

if(empty($app)){
  $app = new app_t();
}

if(empty($app->Conf)) {
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)) {
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, $app, $app->Conf->WFdbport);
}
if(empty($app->erp)) {
  if(class_exists('erpAPICustom')) {
    $erp = new erpAPICustom($app);
  }
  else {
    $erp = new erpAPI($app);
  }
  $app->erp = $erp;
}

/*
    $this->app->erp->CheckTable('lagerwert');
    $this->app->erp->CheckColumn("id", "int(11)", "lagerwert", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("datum", "DATE", "lagerwert", "NOT NULL");
    $this->app->erp->CheckColumn("artikel", "int(11)", "lagerwert", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("lager_platz", "int(11)", "lagerwert", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("lager", "int(11)", "lagerwert", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("menge", "DECIMAL(18,8)", "lagerwert", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("gewicht", "DECIMAL(18,8)", "lagerwert", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("preis_letzterek", "DECIMAL(18,8)", "lagerwert", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("preis_kalkulierterek", "DECIMAL(18,8)", "lagerwert", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("menge", "DECIMAL(18,8)", "lagerwert", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("letzte_bewegung", "DATETIME", "lagerwert");*/
    $app->DB->Select("SELECT waehrungkalk,waehrungletzt,kurskalk,kursletzt FROM lagerwert LIMIT 1");
    if($app->DB->error())
    {
      $app->erp->CheckColumn("waehrungkalk", "VARCHAR(16)", "lagerwert", "NOT NULL DEFAULT ''");
      $app->erp->CheckColumn("waehrungletzt", "VARCHAR(16)", "lagerwert", "NOT NULL DEFAULT ''");
      $app->erp->CheckColumn("kurskalk","DECIMAL(19,8)", "lagerwert", "NOT NULL DEFAULT '0'");
      $app->erp->CheckColumn("kursletzt","DECIMAL(19,8)", "lagerwert", "NOT NULL DEFAULT '0'");
    }
    $app->DB->Delete("DELETE FROM lagerwert WHERE datum = curdate()");
    $app->DB->Insert("INSERT INTO lagerwert (datum, artikel, lager_platz, lager, menge,preis_letzterek, gewicht,volumen,  letzte_bewegung,preis_kalkulierterek, inventurwert,waehrungletzt,waehrungkalk)
    SELECT curdate(), art.id, lpi.lager_platz, 0, lpi.menge, ifnull(ek.preis,0),
    
    lpi.menge * cast(replace(',','.',art.gewicht) as decimal(18,8))
    ,lpi.menge*cast(replace(',','.',art.laenge) as decimal(18,8))*cast(replace(',','.',art.breite) as decimal(18,8))*cast(replace(',','.',art.hoehe) as decimal(18,8))
    , lb.letzte_bewegung,art.berechneterek,art.inventurek,if(ifnull(ek.waehrung,'') <> '',ek.waehrung,'EUR'),if(ifnull(art.berechneterekwaehrung,'') <> '',art.berechneterekwaehrung,'EUR')
    FROM artikel art
    INNER JOIN (SELECT artikel,lager_platz, sum(menge) as menge FROM lager_platz_inhalt GROUP BY artikel,lager_platz) lpi 
    ON art.id = lpi.artikel AND (art.geloescht = 0 OR isnull(art.geloescht))
    LEFT JOIN (SELECT artikel, lager_platz, max(zeit) as letzte_bewegung FROM lager_bewegung GROUP BY artikel, lager_platz) lb 
    ON lb.artikel = lpi.artikel AND lb.lager_platz = lpi.lager_platz
    LEFT JOIN (SELECT artikel, max(id) as id FROM einkaufspreise WHERE gueltig_bis = '0000-00-00' OR gueltig_bis >= curdate() AND geloescht <> 1 GROUP BY artikel) maek ON art.id = maek.artikel
    LEFT JOIN einkaufspreise ek ON maek.id = ek.id
    ");
    $app->DB->Insert("INSERT INTO lagerwert (datum, artikel, lager_platz, lager, menge,preis_letzterek, gewicht,volumen,  letzte_bewegung,preis_kalkulierterek, inventurwert,waehrungletzt,waehrungkalk)
    SELECT curdate(), art.id, 0, lpi.lager, lpi.menge, ifnull(ek.preis,0),lpi.menge * cast(replace(',','.',art.gewicht) as decimal(18,8))
    
    ,lpi.menge*cast(replace(',','.',art.laenge) as decimal(18,8))*cast(replace(',','.',art.breite) as decimal(18,8))*cast(replace(',','.',art.hoehe) as decimal(18,8))
    ,lb.letzte_bewegung,art.berechneterek,art.inventurek,if(ifnull(ek.waehrung,'') <> '',ek.waehrung,'EUR'),if(ifnull(art.berechneterekwaehrung,'') <> '',art.berechneterekwaehrung,'EUR')
    FROM artikel art
    INNER JOIN (
    SELECT lager_platz_inhalt.artikel,lager_platz.lager, sum(lager_platz_inhalt.menge) as menge 
    FROM lager_platz_inhalt INNER JOIN lager_platz ON lager_platz_inhalt.lager_platz = lager_platz.id
    GROUP BY lager_platz_inhalt.artikel,lager_platz.lager
    
    ) lpi ON art.id = lpi.artikel AND (art.geloescht = 0 OR isnull(art.geloescht))
    
    LEFT JOIN (SELECT lager_bewegung.artikel, lager_platz.lager, max(lager_bewegung.zeit) as letzte_bewegung 
    FROM lager_bewegung INNER JOIN lager_platz ON lager_bewegung.lager_platz = lager_platz.id
    GROUP BY lager_bewegung.artikel, lager_platz.lager) lb 
    ON lb.artikel = art.id AND lb.lager = lpi.lager
    LEFT JOIN (SELECT artikel, max(id) as id FROM einkaufspreise WHERE gueltig_bis = '0000-00-00' OR gueltig_bis >= curdate() AND geloescht <> 1 GROUP BY artikel) maek ON art.id = maek.artikel
    LEFT JOIN einkaufspreise ek ON maek.id = ek.id
    ");
    $waehrungen = $app->DB->SelectArr("SELECT DISTINCT waehrungkalk FROM lagerwert WHERE datum = curdate()");
    if($waehrungen)
    {
      foreach($waehrungen as $waehrung)
      {
        if($waehrung['waehrungkalk'] != '')
        {
          $kurs = $app->erp->GetWaehrungUmrechnungskurs('EUR',$waehrung['waehrungkalk']);
          if($kurs > 0)
          {
            $app->DB->Update("UPDATE lagerwert SET kurskalk = '$kurs' WHERE datum = curdate() AND waehrungkalk = '".$app->DB->real_escape_string($waehrung['waehrungkalk'])."'");
          }
        }
      }
    }
    $waehrungen = $app->DB->SelectArr("SELECT DISTINCT waehrungletzt FROM lagerwert WHERE datum = curdate()");
    if($waehrungen)
    {
      foreach($waehrungen as $waehrung)
      {
        if($waehrung['waehrungletzt'] != '')
        {
          $kurs = $app->erp->GetWaehrungUmrechnungskurs('EUR',$waehrung['waehrungletzt']);
          if($kurs > 0)
          {
            $app->DB->Update("UPDATE lagerwert SET kursletzt = '$kurs' WHERE datum = curdate() AND waehrungletzt = '".$app->DB->real_escape_string($waehrung['waehrungletzt'])."'");
          }
        }
      }
    }



