<?php

function xentral_autoloader($class) {
  $classes = array(
    'Config'=>__DIR__.'/conf/main.conf.php',
    'EasyTable'=>__DIR__.'/phpwf/widgets/easytable.php',
    'FileTable'=>__DIR__.'/phpwf/widgets/filetable.php',
    'DownloadSpoolerTable'=>__DIR__.'/phpwf/widgets/downloadspoolertable.php',
    'HTMLTable'=>__DIR__.'/phpwf/htmltags/class.table.php',
    'image'=>__DIR__.'/www/lib/class.image.php',
    'Location'=>__DIR__.'/www/lib/class.location.php',
    'TemplateParser'=>__DIR__.'/phpwf/plugins/class.templateparser.php',
    'ModuleScriptCache'=>__DIR__.'/phpwf/plugins/class.modulescriptcache.php',
    'Table'=>__DIR__.'/phpwf/widgets/table.php',
    'Remote'=>__DIR__.'/www/lib/class.remote.php',
    'erpAPI'=>__DIR__.'/www/lib/class.erpapi.php',
    'erpooSystem'=>__DIR__.'/www/eproosystem.php',
    'erpAPICustom'=>__DIR__.'/www/lib/class.erpapi_custom.php',
    'RemoteCustom'=>__DIR__.'/www/lib/class.remote_custom.php',
    'YUI'=>__DIR__.'/phpwf/plugins/class.yui.php',
    'User'=>__DIR__.'/phpwf/plugins/class.user.php',
    'Secure'=>__DIR__.'/phpwf/plugins/class.secure.php',
    'Secure2'=>__DIR__.'/phpwf/plugins/class.secure2.php',
    'Acl'=>__DIR__.'/phpwf/plugins/class.acl.php',
    'WawiString'=>__DIR__.'/phpwf/plugins/class.string.php',
    'phpWFAPI'=>__DIR__.'/phpwf/plugins/class.phpwfapi.php',
    'ApplicationCore'=>__DIR__.'/phpwf/class.application_core.php',
    'Application'=>__DIR__.'/phpwf/class.application.php',
    'HttpClient'=>__DIR__.'/www/lib/class.httpclient.php',
    'SMTP'=>__DIR__.'/www/plugins/phpmailer/class.smtp.php',
    'IMAP'=>__DIR__.'/www/lib/imap.inc.php',
    'PHPMailer'=>__DIR__.'/www/plugins/phpmailer/class.phpmailer.php',
    'Help'=>__DIR__.'/www/lib/class.help.php',
    'StringCleaner'=>__DIR__.'/phpwf/plugins/class.stringcleaner.php',
    'Page'=>__DIR__.'/phpwf/plugins/class.page.php',
    'ObjectAPI'=>__DIR__.'/phpwf/plugins/class.objectapi.php',
    'WFMonitor'=>__DIR__.'/phpwf/plugins/class.wfmonitor.php',
    'FormHandler'=>__DIR__.'/phpwf/plugins/class.formhandler.php',
    'DatabaseUpgrade'=>__DIR__.'/phpwf/plugins/class.databaseupgrade.php',
    'WidgetAPI'=>__DIR__.'/phpwf/plugins/class.widgetapi.php',
    'PageBuilder'=>__DIR__.'/phpwf/plugins/class.pagebuilder.php',
    'DB'=>__DIR__.'/phpwf/plugins/class.mysql.php',
    'Printer'=>__DIR__.'/www/lib/class.printer.php',
    'PrinterCustom'=>__DIR__.'/www/lib/class.printer_custom.php',
    'HTMLForm'=>__DIR__.'/phpwf/htmltags/class.form.php',
    'HTMLTextarea'=>__DIR__.'/phpwf/htmltags/class.form.php',
    'BlindField'=>__DIR__.'/phpwf/htmltags/class.form.php',
    'HTMLInput'=>__DIR__.'/phpwf/htmltags/class.form.php',
    'HTMLCheckbox'=>__DIR__.'/phpwf/htmltags/class.form.php',
    'HTMLSelect'=>__DIR__.'/phpwf/htmltags/class.form.php',
    'SimpleList'=>__DIR__.'/phpwf/types/class.simplelist.php',
    'PicosafeLogin'=>__DIR__.'/phpwf/plugins/class.picosafelogin.php',
    'WaWisionOTP'=>__DIR__.'/phpwf/plugins/class.wawision_otp.php',
    'PDF_EPS'=>__DIR__.'/www/lib/pdf/fpdf_final.php',
    'SuperFPDF'=>__DIR__.'/www/lib/dokumente/class.superfpdf.php',
    'Briefpapier'=>__DIR__.'/www/lib/dokumente/class.briefpapier.php',
    'PDF'=>__DIR__.'/www/lib/pdf/fpdf.php',
    'SpeditionPDF'=>__DIR__.'/www/lib/dokumente/class.spedition.php',
    'EtikettenPDF'=>__DIR__.'/www/lib/dokumente/class.etiketten.php',
    'Dokumentenvorlage'=>__DIR__.'/www/lib/dokumente/class.dokumentenvorlage.php',
    'SepaMandat'=>__DIR__.'/www/lib/dokumente/class.sepamandat.php',
    'TransferBase'=>__DIR__.'/www/lib/TransferBase.php',
    'PrinterBase'=>__DIR__.'/www/lib/PrinterBase.php',
    'WikiParser'=>__DIR__.'/www/plugins/class.wikiparser.php',
    'IndexPoint'=>__DIR__.'/www/plugins/class.wikiparser.php',
    'ICS'=>__DIR__.'/www/plugins/class.ics.php',
    'USTID'=>__DIR__.'/www/lib/class.ustid.php',
    'phpprint'=>__DIR__.'/www/plugins/php-print.php',
    'DHLBusinessShipment'=>__DIR__.'/www/lib/class.intraship.php',
    'Navigation'=>__DIR__.'/www/lib/class.navigation_edit.php',
    'GoShipment'=>__DIR__.'/www/lib/class.go.php',
    'UPSShipment'=>__DIR__.'/www/lib/class.ups.php',
    'XTEA'=>__DIR__.'/www/lib/class.xtea.php',
    'ShopimporterBase'=>__DIR__.'/www/lib/ShopimporterBase.php',
    'LiveimportBase'=>__DIR__.'/www/plugins/liveimport/LiveimportBase.php',
    'paypal'=>__DIR__.'/www/plugins/liveimport/paypal/paypal.php',
    'DocscanRoot' => __DIR__ . '/www/docscan/classes/DocscanRoot.php',
    'DocscanFile' => __DIR__ . '/www/docscan/classes/DocscanFile.php',
    'DocscanDir' => __DIR__ . '/www/docscan/classes/DocscanDir.php',
    'DocscanAuth' => __DIR__ . '/www/docscan/classes/DocscanAuth.php',
    'ArtikelTabelle' => __DIR__ . '/www/widgets/artikeltable.php',
    'Xentral\Core\LegacyConfig\ConfigLoader'=>__DIR__.'/classes/Core/LegacyConfig/ConfigLoader.php',
    'Xentral\Core\LegacyConfig\Exception\LegacyConfigExceptionInterface'=>__DIR__.'/classes/Core/LegacyConfig/Exception/LegacyConfigExceptionInterface.php',
    'Xentral\Core\LegacyConfig\Exception\InvalidArgumentException'=>__DIR__.'/classes/Core/LegacyConfig/Exception/InvalidArgumentException.php',
    'Xentral\Core\LegacyConfig\Exception\MultiDbConfigNotFoundException'=>__DIR__.'/classes/Core/LegacyConfig/Exception/MultiDbConfigNotFoundException.php',
    'Xentral\Core\LegacyConfig\MultiDbArrayHydrator'=>__DIR__.'/classes/Core/LegacyConfig/MultiDbArrayHydrator.php',
    'Xentral\Core\ErrorHandler\ErrorHandler'=>__DIR__.'/classes/Core/ErrorHandler/ErrorHandler.php',
    'Xentral\Core\ErrorHandler\ErrorPageData'=>__DIR__.'/classes/Core/ErrorHandler/ErrorPageData.php',
    'Xentral\Core\ErrorHandler\ErrorPageRenderer'=>__DIR__.'/classes/Core/ErrorHandler/ErrorPageRenderer.php',
    'Xentral\Core\ErrorHandler\PhpErrorException'=>__DIR__.'/classes/Core/ErrorHandler/PhpErrorException.php',
    'Xentral\Core\Exception\ComponentExceptionInterface'=>__DIR__. '/classes/Core/Exception/ComponentExceptionInterface.php',
    'Xentral\Core\Exception\CoreExceptionInterface'=>__DIR__. '/classes/Core/Exception/CoreExceptionInterface.php',
    'Xentral\Core\Exception\ModuleExceptionInterface'=>__DIR__. '/classes/Core/Exception/ModuleExceptionInterface.php',
    'Xentral\Core\Exception\WidgetExceptionInterface'=>__DIR__. '/classes/Core/Exception/WidgetExceptionInterface.php',
    'Xentral\Core\Exception\XentralExceptionInterface'=>__DIR__. '/classes/Core/Exception/XentralExceptionInterface.php',
    'Xentral\Core\Installer\ClassMapGenerator'=>__DIR__.'/classes/Core/Installer/ClassMapGenerator.php',
    'Xentral\Core\Installer\Installer'=>__DIR__.'/classes/Core/Installer/Installer.php',
    'Xentral\Core\Installer\InstallerCacheConfig'=>__DIR__.'/classes/Core/Installer/InstallerCacheConfig.php',
    'Xentral\Core\Installer\InstallerCacheWriter'=>__DIR__.'/classes/Core/Installer/InstallerCacheWriter.php',
    'Xentral\Core\Installer\Psr4ClassNameResolver'=>__DIR__.'/classes/Core/Installer/Psr4ClassNameResolver.php',
  );
  if(isset($classes[$class]) && is_file($classes[$class]))
  {
    include_once $classes[$class];
    return;
  }
  if(strpos($class,'Widget') === 0)
  {
    if(strpos($class,'WidgetGen') === 0)
    {
      $file = strtolower(substr($class,9));
      if(is_file(__DIR__.'/www/widgets/_gen/widget.gen.'.$file.'.php'))
      {
        include __DIR__.'/www/widgets/_gen/widget.gen.'.$file.'.php';
        return;
      }
    }
    $file = strtolower(substr($class,6));
    if(is_file(__DIR__.'/www/widgets/widget.'.$file.'.php'))
    {
      include __DIR__.'/www/widgets/widget.'.$file.'.php';
      return;
    }
  }
  if($class === 'AES')
  {
    if(version_compare(phpversion(),'7.1', '>=') && is_file(__DIR__.'/www/lib/class.aes2.php')){
        include __DIR__.'/www/lib/class.aes2.php';
    } else{
      include __DIR__ . '/www/lib/class.aes.php';
    }
    return;
  }
  if($class === 'FPDFWAWISION')
  {
    if(is_file(__DIR__.'/conf/user_defined.php'))
    {
      include_once __DIR__.'/conf/user_defined.php';
    }
    if(!defined('USEFPDF3')){
      define('USEFPDF3', true);
    }
    if(defined('USEFPDF3') && USEFPDF3)
    {
      if(is_file(__DIR__ .'/www/lib/pdf/fpdf_3.php'))
      {
        require_once __DIR__ .'/www/lib/pdf/fpdf_3.php';
      }else {
        require_once __DIR__ .'/www/lib/pdf/fpdf.php';
      }
    }
    else if(defined('USEFPDF2') && USEFPDF2)
    {
      if(is_file(__DIR__ .'/www/lib/pdf/fpdf_2.php'))
      {
        require_once __DIR__ .'/www/lib/pdf/fpdf_2.php';
      }else {
        require_once __DIR__ .'/www/lib/pdf/fpdf.php';
      }
    } else {
      require_once __DIR__ .'/www/lib/pdf/fpdf.php';
    }
    return;
  }
  if($class === 'BriefpapierCustom')
  {
    if(is_file(__DIR__.'/www/lib/dokumente/class.briefpapier_custom.php'))
    {
      include __DIR__.'/www/lib/dokumente/class.briefpapier_custom.php';
    }else{
      class BriefpapierCustom extends Briefpapier
      {

      }
    }
  }

  if(substr($class, -3) === 'PDF') {
    $file = __DIR__.'/www/lib/dokumente/class.'.strtolower(substr($class,0,-3)).'.php';
    if(file_exists($file)) {
      include $file;
    }
    elseif(file_exists(__DIR__.'/www/lib/dokumente/class.'.strtolower($class).'.php')) {
      include __DIR__.'/www/lib/dokumente/class.'.strtolower($class).'.php';
    }
  }
  elseif(substr($class, -9) === 'PDFCustom') {
    $file = __DIR__.'/www/lib/dokumente/class.'.strtolower(substr($class,0,-9)).'_custom.php';
    if(file_exists($file)) {
      include $file;
    }
  }
}

spl_autoload_register('xentral_autoloader');
