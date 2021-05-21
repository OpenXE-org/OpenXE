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

/**
 * Class ModuleScriptCache
 *
 * Cache-Datei mit zufälligem Namen generieren
 * @example IncludeJavascriptFiles('chat', $files) => cache/chat-1234abcd.js
 *
 * Cache-Datei mit festen Dateinamen generieren (erster Parameter muss mit .js oder .css enden)
 * @example IncludeJavascriptFiles('chat.js', $files) => cache/chat.js?hash=1234abcd
 */
class ModuleScriptCache
{
  /** @var string $baseDir Absoluter Pfad zur Xentral-Installation */
  protected $baseDir;

  /** @var string $absoluteCacheDir Absoluter Pfad zum Cache-Ordner (muss in www sein) */
  protected $absoluteCacheDir;

  /** @var string $relativeCacheDir Relativer Pfad zum Cache-Ordner (ausgehend von www) */
  protected $relativeCacheDir;

  /** @var array $javascriptFiles Absolute Pfade zu Javascript-Dateien die gecached werden sollen */
  protected $javascriptFiles = [
    'head' => [],
    'body' => [],
  ];

  /** @var array $stylesheetFiles Absolute Pfade zu Stylesheet-Dateien die gecached werden sollen */
  protected $stylesheetFiles = [];

  public function __construct()
  {
    $this->baseDir = dirname(dirname(__DIR__));
    $this->absoluteCacheDir = $this->baseDir . '/www/cache';
    $this->relativeCacheDir = './cache';

    // Cache-Ordner anzulegen, falls nicht existent
    if (!is_dir($this->absoluteCacheDir)) {
      if(!mkdir($concurrentDirectory = $this->absoluteCacheDir, 0777) && !is_dir($concurrentDirectory)){
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
      }
    }
  }

  /**
   * @param string $legacyModuleClassName Kompletter Klassenname das alten Moduls
   *
   * @return void
   */
  public function IncludeModule($legacyModuleClassName)
  {
    $newModuleName = $this->DetermineNewModuleName($legacyModuleClassName);

    // Neuer Modulname konnte nicht ermittelt werden; MODULE_NAME Konstante fehlt im alten Modul
    if ($newModuleName === null) {
      return;
    }

    // Javascript- und Stylesheet-Dateien sind als Eigenschaft im Modul definiert
    $javascript = $this->GetClassProperty($legacyModuleClassName, 'javascript');
    $stylesheet = $this->GetClassProperty($legacyModuleClassName, 'stylesheet');

    // Falls nicht im Modul definiert > Defaults verwenden
    if (empty($javascript)) {
      $javascript = [$this->GetDefaultModuleJavascriptFile($newModuleName)];
    }
    if (empty($stylesheet)) {
      $stylesheet = [$this->GetDefaultModuleStylesheetFile($newModuleName)];
    }

    $this->IncludeJavascriptFiles($newModuleName, $javascript);
    $this->IncludeStylesheetFiles($newModuleName, $stylesheet);
  }

  /**
   * @param string $widgetName
   *
   * @throws RuntimeException
   *
   * @return void
   */
  public function IncludeWidgetNew($widgetName)
  {
    $widgetNameCleaned = preg_replace('/[^a-z]+/im', '', $widgetName);
    if ($widgetName !== $widgetNameCleaned) {
      throw new RuntimeException(sprintf(
        'Widget name "%s" contains illegal characters. Valid characters: A-Z, a-z', $widgetName
      ));
    }
    if (empty($widgetName)){
      throw new RuntimeException('Widget name can not be empty.');
    }

    $javascript = $stylesheet = [];

    // Javascript- und CSS-Dateien aus Bootstrap holen
    $widgetBootstrapClass = sprintf('Xentral\\Widgets\\%s\\Bootstrap', $widgetName);
    if (class_exists($widgetBootstrapClass, true)) {
      $javascript = (array)@forward_static_call([$widgetBootstrapClass, 'registerJavascript']);
      foreach ($javascript as $cacheName => $jsFiles) {
        $this->IncludeJavascriptFiles($cacheName, $jsFiles);
      }
      $stylesheets = (array)@forward_static_call([$widgetBootstrapClass, 'registerStylesheets']);
      foreach ($stylesheets as $cacheName => $cssFiles) {
        $this->IncludeStylesheetFiles($cacheName, $cssFiles);
      }
    }

    // Falls nicht in Bootstrap definiert > Fallback auf Defaults
    if (empty($javascript)) {
      $javascript = [$this->GetDefaultWidgetJavascriptFile($widgetName)];
      $this->IncludeJavascriptFiles($widgetName, $javascript);
    }
    if (empty($stylesheet)) {
      $stylesheet = [$this->GetDefaultWidgetStylesheetFile($widgetName)];
      $this->IncludeStylesheetFiles($widgetName, $stylesheet);
    }
  }

  /**
   * @param string $cacheName Name unter dem die Cache-Datei zusammengefasst werden
   * @param array  $files     Array mit relativen Pfaden zur Xentral-Installation
   *
   * @return void
   */
  public function IncludeJavascriptFiles($cacheName, array $files)
  {
    foreach ($files as $section => $file) {
      // Neues Verhalten => Trennung nach Head und Body
      if ($section === 'head' && is_array($file)) {
        $this->IncludeJavascriptHeadFiles($cacheName, $file);
        continue;
      }
      if ($section === 'body' && is_array($file)) {
        $this->IncludeJavascriptBodyFiles($cacheName, $file);
        continue;
      }

      // Altes Verhalten (vor Trennung in Head un Body) => Alles in Body
      $realPath = realpath($this->baseDir . '/' . $file);
      if(is_file($realPath)){
        $this->javascriptFiles['body'][$cacheName][] = $realPath;
      }
    }
  }

  /**
   * @param string $cacheName
   * @param array  $files
   *
   * @return void
   */
  protected function IncludeJavascriptHeadFiles($cacheName, array $files)
  {
    // Prüfen ob Dateien existieren
    foreach ($files as $file) {
      $realPath = realpath($this->baseDir . '/' . $file);
      if(is_file($realPath)){
        $this->javascriptFiles['head'][$cacheName . '-head'][] = $realPath;
      }
    }
  }

  /**
   * @param string $cacheName
   * @param array  $files
   *
   * @return void
   */
  protected function IncludeJavascriptBodyFiles($cacheName, array $files)
  {
    // Prüfen ob Dateien existieren
    foreach ($files as $file) {
      $realPath = realpath($this->baseDir . '/' . $file);
      if(is_file($realPath)){
        $this->javascriptFiles['body'][$cacheName . '-body'][] = $realPath;
      }
    }
  }

  /**
   * @param string $cacheName Name unter dem die Cache-Datei zusammengefasst werden
   * @param array  $files     Array mit relativen Pfaden zur Xentral-Installation
   *
   * @return void
   */
  public function IncludeStylesheetFiles($cacheName, array $files)
  {
    // Prüfen ob Dateien existieren
    foreach ($files as $file) {
      $realPath = realpath($this->baseDir . '/' . $file);
      if(is_file($realPath)){
        $this->stylesheetFiles[$cacheName][] = $realPath;
      }
    }
  }

  /**
   * @return string
   */
  public function GetStylesheetHtmlTags()
  {
    if (empty($this->stylesheetFiles)) {
      return '';
    }

    $html = '';
    foreach ($this->stylesheetFiles as $moduleName => $files) {
      $cacheFilesUri = $this->GetCacheFileUri($moduleName, 'css', $files);
      if (!empty($cacheFilesUri)){
        $html .= sprintf('<link href="%s" rel="stylesheet" type="text/css" />', $cacheFilesUri);
        $html .= "\r\n";
      }
    }

    return $html;
  }

  /**
   * @param string $section [head|body]
   *
   * @return string
   */
  public function GetJavascriptHtmlTags($section = 'body')
  {
    if ($section !== 'body' && $section !== 'head') {
      throw new RuntimeException(sprintf('Invalid section parameter "%s"', $section));
    }

    if (empty($this->javascriptFiles[$section])) {
      return '';
    }

    $html = '';
    foreach ($this->javascriptFiles[$section] as $moduleName => $files) {
      $cacheFilesUri = $this->GetCacheFileUri($moduleName, 'js', $files);
      if (!empty($cacheFilesUri)){
        $html .= sprintf('<script type="text/javascript" src="%s" charset="UTF-8"></script>', $cacheFilesUri);
        $html .= "\r\n";
      }
    }

    return $html;
  }

  /**
   * @return string
   */
  public function GetAbsoluteCacheDir()
  {
    return $this->absoluteCacheDir;
  }

  /**
   * @return string
   */
  public function GetRelativeCacheDir()
  {
    return $this->relativeCacheDir;
  }

  /**
   * @return bool
   */
  public function IsCacheDirWritable()
  {
    $randomData = md5(microtime(true));
    $tempFile = $this->absoluteCacheDir . '/' . $randomData . '.tmp';
    if (!file_put_contents($tempFile, $randomData)) {
      return false;
    }
    unlink($tempFile);

    return true;
  }

  /**
   * @param string $moduleName Neuer Modulename
   * @param string $fileType [js|css]
   * @param array $files Array mit absoluten Pfaden zu Resourcen
   *
   * @return string
   */
  protected function GetCacheFileUri($moduleName, $fileType, array $files = [])
  {
    if(!in_array($fileType, ['css', 'js'])){
      return '';
    }

    $files = array_unique($files);

    // Hash über alle Dateien bilden
    $hash = $this->CalculateFilesHash($files);

    // Pfad zur Cache-Datei bestimmen
    if(substr($moduleName, -3) === '.js' || substr($moduleName, -4) === '.css'){
      $hashFilename = $moduleName;
      $cacheFileUri = $this->relativeCacheDir . '/' . $hashFilename . '?hash=' . $hash;
    }else{
      $hashFilename = strtolower($moduleName) . '-' . $hash . '.' . $fileType;
      $cacheFileUri = $this->relativeCacheDir . '/' . $hashFilename;
    }

    // Cache-Datei anlegen, falls nicht existent
    $cacheFilePath = $this->absoluteCacheDir . '/' . $hashFilename;
    if(!is_file($cacheFilePath)){
      $this->CreateCacheFile($files, $cacheFilePath);
    }

    return $cacheFileUri;
  }

  /**
   * Führt mehrere Dateieninhalte in eine Datei zusammen
   *
   * @param array  $sourceFiles
   * @param string $destFile
   */
  protected function CreateCacheFile($sourceFiles, $destFile)
  {
    $destHandle = fopen($destFile, 'wb');
    if ($destHandle === false) {
      throw new RuntimeException(sprintf(
        'Could not create cache file #1. Please make "%s" directory writable. Failed file: %s',
        $this->GetRelativeCacheDir(),
        $destFile
      ));
    }
    foreach ($sourceFiles as $sourceFile) {
      $sourceContents = '/********* ' . basename($sourceFile) . ' *********/ ' . "\r\n";
      $sourceContents .= file_get_contents($sourceFile);
      $sourceContents .= "\r\n\r\n";
      $writeResult = fwrite($destHandle, $sourceContents);
      if ($writeResult === false) {
        throw new RuntimeException(sprintf(
          'Could not create cache file #2. Please make "%s" directory writable. Failed file: %s',
          $this->GetRelativeCacheDir(),
          $destFile
        ));
      }
    }
    fclose($destHandle);
  }

  /**
   * Berechnet einen Hash über mehrere Dateien
   *
   * Der Hash wird über das Änderungsdatum und die Dateigröße generiert.
   *
   * Die Hash-Berechnung über die Dateiinhalte (md5_file) wäre akkurater; ist aber mindestens 10 mal langsamer.
   *
   * @param array $files
   *
   * @return string
   */
  protected function CalculateFilesHash($files)
  {
    $md5s = [];
    foreach ($files as $file) {
      $md5s[] = md5(filemtime($file) . filesize($file));
    }

    // Hash über alle Dateien ermitteln
    return count($md5s) === 1 ? $md5s[0] : md5(implode('', $md5s), false);
  }

  /**
   * @param string $moduleName
   *
   * @return string Relativer Pfad zur Javascript-Datei im neuen Modul-Verzeichnis
   */
  protected function GetDefaultModuleJavascriptFile($moduleName)
  {
    return sprintf('./classes/Modules/%s/www/js/%s.js', $moduleName, strtolower($moduleName));
  }

  /**
   * @param string $moduleName
   *
   * @return string Relativer Pfad zur Stylesheet-Datei im neuen Modul-Verzeichnis
   */
  protected function GetDefaultModuleStylesheetFile($moduleName)
  {
    return sprintf('./classes/Modules/%s/www/css/%s.css', $moduleName, strtolower($moduleName));
  }

  /**
   * @param string $widgetName
   *
   * @return string Relativer Pfad zur Javascript-Datei im neuen Widgets-Verzeichnis
   */
  protected function GetDefaultWidgetJavascriptFile($widgetName)
  {
    return sprintf('./classes/Widgets/%s/www/js/%s.js', $widgetName, strtolower($widgetName));
  }

  /**
   * @param string $widgetName
   *
   * @return string Relativer Pfad zur Stylesheet-Datei im neuen Widgets-Verzeichnis
   */
  protected function GetDefaultWidgetStylesheetFile($widgetName)
  {
    return sprintf('./classes/Widgets/%s/www/css/%s.css', $widgetName, strtolower($widgetName));
  }

  /**
   * @param string $legacyModuleClassName
   * @param string $property
   *
   * @return array|null
   */
  protected function GetClassProperty($legacyModuleClassName, $property)
  {
    if(!class_exists($legacyModuleClassName, true)){
      include_once sprintf('%s/www/pages/%s.php', $this->baseDir, strtolower($legacyModuleClassName));
    }
    if (!class_exists($legacyModuleClassName, false)) {
      return null;
    }
    if (!property_exists($legacyModuleClassName, $property)) {
      return null;
    }

    $properties = get_class_vars($legacyModuleClassName);

    return $properties[$property];
  }

  /**
   * Ermittelt, anhand des alten Moduls, den Name des neuen Moduls
   *
   * Ist notwendig da die alten Module in Deutsch betitelt sind, und die neuen Module in Englisch.
   *
   * Beispiel @see Chat::MODULE_NAME
   *
   * @param string $legacyModuleClassName
   *
   * @return string|null
   */
  protected function DetermineNewModuleName($legacyModuleClassName)
  {
    if(!class_exists($legacyModuleClassName, true)){
      $legacyModuleClassFile = sprintf('%s/www/pages/%s.php', $this->baseDir, strtolower($legacyModuleClassName));
      if (is_file($legacyModuleClassFile)) {
          include_once $legacyModuleClassFile;
      }
    }
    if(!defined($legacyModuleClassName . '::MODULE_NAME')){
      return null;
    }

    return constant($legacyModuleClassName . '::MODULE_NAME');
  }
}
