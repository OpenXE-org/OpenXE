<?php

/**
 * @param ApplicationCore $app
 *
 * @return bool
 */
function update($app)
{
  // url with latest update links
  $versandarr = $app->DB->SelectArr("SELECT id FROM versandarten WHERE modul = 'internetmarke'");
  if(!empty($versandarr)){
    foreach ($versandarr as $versandrow) {
      if(file_exists(dirname(__DIR__) . '/www/lib/versandarten/ppl_prices_' . $versandrow['id'] . '.xml')){
        @unlink(dirname(__DIR__) . '/www/lib/versandarten/ppl_prices_' . $versandrow['id'] . '.xml');
      }
    }
  }

  $url = 'https://www.deutschepost.de/content/dam/dpag/images/i_i/Internetmarke/technische_downloads/update_internetmarke/ppl_update.xml';

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
  $result = curl_exec($curl);

  // error handling
  checkCurlError($curl, $result);

  // parsing XML to get the final XML link
  $xml = new SimpleXMLElement($result);
  $link = (string)$xml->xpath('/addins/app/updates/li/updateLink')[0];

  if(empty($link)){
    throw new RuntimeException('Update-link not found');
  }

  // downloading final content
  curl_setopt($curl, CURLOPT_URL, $link);
  $csv = curl_exec($curl);

  checkCurlError($curl, $csv);


  // writing final csv file
  $csvFile = dirname(__DIR__) . '/www/lib/versandarten/ppl_latest.csv';

  if(md5($csv) === md5_file($csvFile)){
    //file already the same
    return false;
  }
  $app->erp->LogFile("Update link: {$link}", '', 'Internetmarke');

  $written = file_put_contents($csvFile, $csv);

  // error handling once again...oh those stupid errors
  if(!$written){
    throw new RuntimeException("Error writing to csv file: $csvFile");
  }
  return true;
}

function isInternetmarkeInUse($app)
{
  $count = (int)$app->DB->Select('SELECT COUNT(*) FROM versandarten WHERE modul="internetmarke"');

  return $count > 0;
}

/**
 * @param mixed  $curl
 * @param string $result
 */
function checkCurlError($curl, $result)
{
  $error = curl_error($curl);
  if($error !== ''){
    throw new RuntimeException("cURL error: $error");
  }
  if(empty($result)){
    throw new RuntimeException('Empty response from server');
  }

  $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  $url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

  switch ($code) {
    case 404:
      throw new RuntimeException("Seite {$url} nicht erreichbar");
  }
  
  if(strpos((string)$code,'2') !== 0) {
    throw new RuntimeException("Fehlerhafte Antwort: {$code}");
  }
}

// include dirname(__DIR__) . '/xentral_autoloader.php';
$app = new ApplicationCore();

if(!isInternetmarkeInUse($app)){
  $app->erp->LogFile('Internetmarke not in use', '', 'Internetmarke');
  return;
}

try {
  if(update($app)){
    $app->erp->LogFile('Successfully updated Internetmarke PPL', '', 'Internetmarke');
  }else{
    $app->erp->LogFile('no new PPL', '', 'Internetmarke');
  }
} catch (RuntimeException $e) {
  $app->erp->LogFile('Error updating PPL', $e->getMessage(), 'Internetmarke');
  /** @var Systemhealth $systemHealth */
  $systemHealth = $app->loadModule('systemhealth');
  $systemHealth->createEntryWithCategoryIfError(
    'internetmarke',
    'Internetmarke',
      'updater',
    'Produktliste Updater Status',
    'warning',
    'Fehler: ' . $e->getMessage(),
    true
    );
}