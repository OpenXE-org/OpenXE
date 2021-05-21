<?php


class sfirm 
{
  /** @var Application $app */
  var $app;

  function __construct()
  {
  }

  /**
   * @param string      $csv
   * @param int         $konto
   * @param Application $app
   *
   * @return array
   */
  function ImportKontoauszug($csv='',$konto=0,$app=null)
  {
    $this->app = $app;
    $resultparse = $this->parse_csv($csv,';');
    $dateCol = 1;
    array_shift($resultparse); // remove header 1
    if(!empty($resultparse[0][0]) && $resultparse[0][0]=== 'Buchung' && !empty($resultparse[1])) {
      $bookingdate = $this->app->String->Convert($resultparse[1][0], '%1.%2.%3', '%3-%2-%1');
      if($this->app->erp->CheckDateValidate($bookingdate)) {
        $dateCol = 0;
      }
    }
    array_shift($resultparse); // remove header 2
    $duplicate = 0;
    $newcsv="date;description;amount;currency\r\n";
    $inserted = 0;


    // fix values
    $gebuehr = 0;
    $gegenkonto = "";
    $stamp = time();

    $userName = $app->User->GetName();
    $userName = $app->DB->real_escape_string($userName);

    $cresultparse = count($resultparse);
    //Buchung;Wertstellung;;Buchungstext;Beg�nstigter/Zahlungspflichtiger;;;Verwendungszweck;Betrag;W�hrung;Auszugsnr.;Laufender Saldo;
    for($row=0;$row<$cresultparse;$row++)
    {
      $bookingdate = $this->app->String->Convert($resultparse[$row][$dateCol], '%1.%2.%3', '%3-%2-%1'); // Wertstellung
      $description = trim(preg_replace('/\s\s+/', ' ',$resultparse[$row][$dateCol]." ".$resultparse[$row][4]));
      $has_h = stripos($resultparse[$row][8], 'H') !== false;
      $has_s = stripos($resultparse[$row][8], 'S') !== false;
    
      $resultparse[$row][8] = str_replace('H','',$resultparse[$row][8]);
      $resultparse[$row][8] = str_replace('S','',$resultparse[$row][8]);

     
      $amount = trim(str_replace(array('.', ','), array('', '.'), $resultparse[$row][8])); 

      $haben = 0;
      $soll = 0;

      if($has_s) {
        $soll = $amount;
        $amount = $amount * -1;
      } else {
        $haben = $amount;
      }


      $currency = $resultparse[$row][9];
      if($amount <> 0 && $this->app->erp->CheckDateValidate($bookingdate) && $description!="")
      {
        $pruefsumme = md5(serialize(array($bookingdate, $description, $soll, $haben, $currency)));

        $check = $app->DB->Select("SELECT id FROM kontoauszuege WHERE buchung='$bookingdate' AND konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1");
        if($check > 0) {
          $duplicate++;
          continue;
        }

        $sql = "INSERT INTO kontoauszuege (
          konto,
          buchung,
          vorgang,
          soll,
          haben,
          gebuehr,
          waehrung,
          fertig,
          bearbeiter,
          pruefsumme,
          importgroup,
          originalbuchung,
          originalvorgang,
          originalsoll,
          originalhaben,
          originalgebuehr,
          originalwaehrung,
          gegenkonto
          ) VALUE (
          '$konto',
          '$bookingdate',
          '$description',
          '$soll',
          '$haben',
          '$gebuehr',
          '$currency',
          0,
          '".$userName."',
          '$pruefsumme',
          '$stamp',
          '$bookingdate',
          '$description',
          '$soll',
          '$haben',
          '$gebuehr',
          '$currency',
          '$gegenkonto')";

        $app->DB->Insert($sql);
        $newid = $app->DB->GetInsertID();
        $app->DB->Update("UPDATE kontoauszuege SET sort='$newid' WHERE id='$newid' LIMIT 1");
        $inserted++;
      }
    }

    return array($inserted, $duplicate);
  }

function parse_csv ($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true)
{
    $enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string);
    $enc = preg_replace_callback(
        '/"(.*?)"/s',
        function ($field) {
            return urlencode(utf8_encode($field[1]));
        },
        $enc
    );
    $lines = preg_split($skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s', $enc);
    return array_map(
        function ($line) use ($delimiter, $trim_fields) {
            $fields = $trim_fields ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line);
            return array_map(
                function ($field) {
                    return str_replace('!!Q!!', '"', utf8_decode(urldecode($field)));
                },
                $fields
            );
        },
        $lines
    );
}

}

