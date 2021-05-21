<?php

use Xentral\Modules\SystemNotification\Service\NotificationMessageData;
use Xentral\Modules\SystemNotification\Service\NotificationService;

class Printer
{
  /** @var erpooSystem $app */
  public $app;

  function __construct($app)
  {
    $this->app=$app;
  }


  function Drucken($drucker,$dokument,$parameter="",$anzahl="1", $spoolerId = 0)
  {
    if($spoolerId > 0 && empty($dokument)) {
      $spooler = $this->app->DB->SelectRow(
        "SELECT `content`, `filename` FROM `drucker_spooler` WHERE `id` = {$spoolerId}"
      );
      if(empty($spooler)) {
        return 0;
      }
      $dokument = @base64_decode(
        (string)$spooler['content']
      );
      $fileName = $this->app->erp->GetTMP() . empty($spooler['filename']) ? 'print.pdf' : $spooler['filename'];
      if(file_put_contents($fileName, $dokument)) {
        $dokument = $fileName;
      }
    }
    if($dokument=='') {
      return 0;
    }

    $printerRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM drucker WHERE id = %d',
        $drucker
      )
    );
    $befehl = $printerRow['befehl'];

    $anbindung = $printerRow['anbindung'];

    $format = $printerRow['format'];


    if($anbindung=='') {
      //$anbindung='cups'; hat nie funktioniert
    }

    //sicherung in spooler
    if(is_file($dokument)) {
      $spooler_content = base64_encode(file_get_contents($dokument));
      $spooler_filename = basename($dokument);
      $spooler_description = $description;
    }
    else {
      $spooler_content = base64_encode($dokument);
    }
    $spooler_anzahl = $anzahl;
    $spooler_befehl = $befehl;
    $spooler_anbindung = $anbindung;
    if($spoolerId <= 0){
      $this->app->DB->Insert("INSERT INTO drucker_spooler (id,drucker,filename,content,description,anzahl,befehl,anbindung,zeitstempel,user,gedruckt) VALUES 
      ('','$drucker','$spooler_filename','$spooler_content','$spooler_description','$spooler_anzahl','$spooler_befehl','$spooler_anbindung',NOW(),'" . $this->app->User->GetID() . "',0)");
      $druckauftrag = $this->app->DB->GetInsertID();
    }
    else {
      $druckauftrag = $spoolerId;
      $spooler=  $this->app->DB->SelectRow(sprintf(
        'SELECT content FROM drucker_spooler WHERE id = %d',
        $spoolerId
      ));
      if(!empty($spooler) && empty($spooler['gedruckt'])) {
        $this->app->DB->Update(sprintf('UPDATE `drucker_spooler` SET `gedruckt` = 0 WHERE `id` = %d', $spoolerId));
      }
      if(empty($dokument)) {
        if(empty($spooler)) {
          return 0;
        }

        $spooler_filename = $spooler['filename'];
        $dokument = base64_decode(
          (string)$spooler['content']
        );
        if(empty($dokument)) {
          return 0;
        }

      }
    }

    //$this->app->erp->LogFile("HUHUH");

    if(!empty($anbindung)) {
      /** @var Drucker $obj */
      $obj = $this->app->erp->LoadModul('drucker');
      $printerObj = $obj->loadPrinterModul($anbindung, $drucker);
      if(!empty($printerObj) && method_exists($printerObj, 'printDocument')) {
        if($printerObj->printDocument($dokument, $anzahl)) {
          $this->app->DB->Update("UPDATE drucker_spooler SET gedruckt=1 WHERE id='$druckauftrag' LIMIT 1");
        }
        return $druckauftrag;
      }
    }

    switch($anbindung)
    {
      case "cups":
        $befehl = str_replace('{FAX}',$parameter,$befehl);
        exec("$befehl $dokument");
        $this->app->DB->Update("UPDATE drucker_spooler SET gedruckt=1 WHERE id='$druckauftrag' LIMIT 1");
        break;
      case "pdf":
        if(is_file($dokument)) {
          if((String)$befehl !== '') {
            $this->app->erp->CreatePath($befehl);

            $path_parts = pathinfo($dokument);
            if($path_parts['extension'] === 'pdf') {
              copy($dokument, $befehl . '/' . basename($dokument));
            }
            else {
              copy($dokument, $befehl . '/' . basename($dokument) . '.pdf');
            }
          }
          $this->app->DB->Update("UPDATE drucker_spooler SET gedruckt=1 WHERE id='$druckauftrag' LIMIT 1");
        }
      break;

      case "download":

        $printerId = (int)$drucker;
        try {
          $this->CreateSpoolerNotification($printerId, $spooler_filename);
        } catch (Exception $e) {

        }

      break;

      case "spooler":
        // damit wawisionsspooler bescheid weiss das er als drucker arbeiten muss um pdf dateien auszugeben
        $art ="printer";
      case "adapterbox":
        // wenn intern
        $deviceiddest = $this->app->DB->Select("SELECT adapterboxseriennummer FROM drucker WHERE id='".$drucker."' LIMIT 1");
        $ip = $this->app->DB->Select("SELECT adapterboxip FROM drucker WHERE id='".$drucker."' LIMIT 1");
        if($art=="")
          $art = $this->app->DB->Select("SELECT art FROM drucker WHERE id='".$drucker."' LIMIT 1");

        $description = $this->app->DB->Select("SELECT bezeichnung FROM drucker WHERE id='".$drucker."' LIMIT 1");

        switch($art)
        {
          case 0: $art = "printer"; break;
          case 1: $art = "fax"; break;
          case 2: $art = "labelprinter"; break;
          default: $art = "unknown"; break;
        }

        if(is_file($dokument))
        {
          $job = base64_encode(json_encode(
              array('label'=>base64_encode(file_get_contents($dokument)),
                  'filename'=>basename($dokument),
                  'amount'=>$anzahl,'filetype'=>filetype($dokument),'description'=>$description,'format'=>$format)));//."<amount>".$anzahl."</amount>");
        }
        else {
          $job = base64_encode(json_encode(array('label'=>base64_encode($dokument),'amount'=>$anzahl,'format'=>$format)));//."<amount>".$anzahl."</amount>");
        }


        if($this->app->erp->Firmendaten("deviceenable")=="1")
        {
          if($deviceiddest!="")
          {
            $this->app->erp->AdapterboxAPI($deviceiddest,$art,$job,false);
            //$this->app->DB->Insert("INSERT INTO device_jobs (id,zeitstempel,deviceidsource,deviceiddest,job,art) VALUES ('',NOW(),'000000000','$deviceiddest','$job','$art')");
          }
        } else {
          $xml = $dokument;
          if($ip!="")
            HttpClient::quickPost("http://".$this->app->erp->GetIPAdapterbox($drucker)."/labelprinter.php",array('label'=>$xml,'amount'=>$anzahl));
        }
          $this->app->DB->Update("UPDATE drucker_spooler SET gedruckt=1 WHERE id='$druckauftrag' LIMIT 1");
        break;

      case "email":
        $tomail = $this->app->DB->Select("SELECT tomail FROM drucker WHERE id='$drucker' 
            AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

        $tomailsubject = $this->app->DB->Select("SELECT tomailsubject FROM drucker WHERE id='$drucker' 
            AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

        $tomailsubject = str_replace('{FAX}',$parameter,$tomailsubject);

        $tomailtext = $this->app->DB->Select("SELECT tomailtext FROM drucker WHERE id='$drucker' 
            AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

        if($dokument!="")
        {
          $this->app->erp->MailSend($this->app->erp->GetFirmaMail(),$this->app->erp->GetFirmaName(),
              $tomail,"",$tomailsubject,$tomailtext,array($dokument),"",true);
        }
        $this->app->DB->Update("UPDATE drucker_spooler SET gedruckt=1 WHERE id='$druckauftrag' LIMIT 1");
      break;
    }
    if($spoolerId > 0 && !empty($fileName) && is_file($fileName)) {
      @unlink($fileName);
    }

    return $druckauftrag;
  }

  /**
   * @param int    $printerId
   * @param string $filename
   *
   * @return void
   */
  protected function CreateSpoolerNotification($printerId, $filename)
  {
    /** @var \Xentral\Modules\DownloadSpooler\DownloadSpoolerGateway $gateway */
    $gateway = $this->app->Container->get('DownloadSpoolerGateway');
    $unprintedFilesCount = $gateway->getUnprintedFilesCountByUser($this->app->User->GetID(), $printerId);
    $printerName = $gateway->getPrinterNameById($printerId);
    $notificationTitle = !empty($printerName) ? 'Drucker: ' . $printerName : 'Download-Drucker';

    if ($unprintedFilesCount > 1) {
      $notificationText = 'Es stehen ' . $unprintedFilesCount . ' Druckaufträge zum Download bereit.';
    } else {
      $notificationText = 'Ein Druckauftrag steht zum Download bereit: ' . $filename;
    }

    // Notification erstellen
    $message = new NotificationMessageData('default', $notificationTitle);
    $message->setMessage($notificationText);
    $message->addTags(['spooler', 'printer', 'download']);
    $message->setOption('id', 'notification_spooler_' . $printerId);
    $message->setOption('printerId', $printerId);
    $message->setPriority(true);

    $messageButtons = [[
      'text' => 'Download',
      'link' => sprintf('index.php?module=welcome&action=spooler&cmd=download-unprinted&id=%s', $printerId),
    ], [
      'text' => 'Zum Spooler',
      'link' => '#',
      'data-printer' => $printerId,
      'data-action' => 'open-dialog'
    ]];
    $message->setOption('buttons', $messageButtons);

    /** @var NotificationService $notification */
    $notification = $this->app->Container->get('NotificationService');
    $notification->createFromData($this->app->User->GetID(), $message);
  }
}
