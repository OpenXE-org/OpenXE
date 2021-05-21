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
/* Author: Benedikt Sauter <sauter@embedded-projetcs.net> 2013
 *
 * Hier werden alle Plugins, Widgets usw instanziert die
 * fuer die Anwendung benoetigt werden.
 * Diese Klasse ist von class.application.php abgleitet.
 * Das hat den Vorteil, dass man dort bereits einiges starten kann,
 * was man eh in jeder Anwendung braucht.
 * - DB Verbindung
 * - Template Parser
 * - Sicherheitsmodul
 * - String Plugin
 * - usw....
 */

date_default_timezone_set('Europe/Berlin');
ini_set('default_charset', 'UTF-8');

ini_set('display_errors', 'on');
ini_set('magic_quotes_runtime', 0);

require_once dirname(__DIR__).'/phpwf/class.application.php';

if( WithGUI(true))
{
  define('FPDF_FONTPATH',__DIR__.'/lib/pdf/font/');
  if(file_exists(__DIR__."/lib/dokumente/class.briefpapier_custom.php"))
  {
    require_once __DIR__.'/lib/dokumente/class.briefpapier_custom.php';
  }else{
    require_once __DIR__.'/lib/dokumente/class.briefpapier.php';
  }
}

include __DIR__.'/function_exists.php';

class erpooSystem extends Application
{
  public $obj;
  public $starttime;
  public $endtime;
  protected $laendercache;
  protected $uselaendercache;

  /** @var erpAPI $erp
   * @var Config $Conf
   */

  public function __construct($config,$group='')
  {
    $this->uselaendercache = false;
    parent::__construct($config, $group);

    if(WithGUI()){
      $module = $this->Secure->GetGET('module');
      $action = $this->Secure->GetGET('action');
      $this->Tpl->Set('DASHBOARDLINK', 'index.php?module=welcome&action=start');

      $this->help = new Help($this);

      $companyletter = strtoupper(substr($this->erp->Firmendaten('name'), 0, 1));
      $this->Tpl->Set('COMPANYLETTER', ($companyletter != '' ? $companyletter : 'W'));


      if($this->erp->Firmendaten('modul_mlm') != '1'){
        $this->Tpl->Set('STARTDISABLEMLM', '<!--');
        $this->Tpl->Set('ENDEDISABLEMLM', '-->');
      }

      if($this->erp->Firmendaten('modul_verband') != '1'){
        $this->Tpl->Set('STARTDISABLEVERBAND', '<!--');
        $this->Tpl->Set('ENDEDISABLEVERBAND', '-->');
      }

      if($this->erp->Version() === 'stock'){
        $this->Tpl->Set('DISABLEOPENSTOCK', '<!--');
        $this->Tpl->Set('DISABLECLOSESTOCK', '-->');
      }

      $icons = array('adresse', 'artikel', 'angebot', 'auftrag', 'lieferschein', 'rechnung');
      foreach ($icons as $icon) {
        if(!$this->erp->RechteVorhanden($icon, 'list')){
          $this->Tpl->Set('ICON' . strtoupper($icon) . 'START', '<!--');
          $this->Tpl->Set('ICON' . strtoupper($icon) . 'ENDE', '-->');
        }
      }

      $this->Tpl->Set(strtoupper($module) . 'ACTIVE', 'active');

      if(is_file('js/' . $module . '.js')){
        $md5 = md5_file('js/' . $module . '.js');
        if(!is_file('js/' . $module . $md5 . '.js')) {
          @copy('js/' . $module . '.js', 'js/' . $module . $md5 . '.js');
        }
        if(is_file('js/' . $module . $md5 . '.js')){
          $this->Tpl->Set('JSSCRIPTS', '<script type="text/javascript" src="./js/' . $module . $md5 . '.js?v=3"></script>');
        }else{
          $this->Tpl->Set('JSSCRIPTS', '<script type="text/javascript" src="./js/' . $module . '.js?v=3"></script>');
        }
      }
      $this->erp->PrinterIcon();
      $this->Tpl->ReadTemplatesFromPath(__DIR__ . '/widgets/templates/_gen/');
      $this->Tpl->ReadTemplatesFromPath(__DIR__ . '/widgets/templates/');
      $this->Tpl->ReadTemplatesFromPath(__DIR__ . '/themes/' . $this->Conf->WFconf['defaulttheme'] . '/templates/');
      $this->Tpl->ReadTemplatesFromPath(__DIR__ . '/pages/content/_gen/');
      $this->Tpl->ReadTemplatesFromPath(__DIR__ . '/pages/content/');
      if(is_dir(__DIR__ . '/lib/versandarten/content')) {
        $this->Tpl->ReadTemplatesFromPath(__DIR__ . '/lib/versandarten/content/');
      }

      if(method_exists($this->erp, 'VersionsInfos')){
        $ver = $this->erp->VersionsInfos();
        if(stripos($ver['Info'], 'Beta') !== false
          || stripos($ver['Info'], 'Alpha') !== false
          || stripos($ver['Info'], 'DEV') !== false
        ) $this->Tpl->Set('VERSIONINFO', strtoupper($ver['Info']));
      }

      $this->Tpl->Set('ID', $this->Secure->GetGET('id'));
      $this->Tpl->Set('POPUPWIDTH', '1200');
      $this->Tpl->Set('POPUPHEIGHT', '800');

      $this->Tpl->Set('YEAR', date('Y'));
      $this->Tpl->Set('COMMONREADONLYINPUT', '');
      $this->Tpl->Set('COMMONREADONLYSELECT', '');

      // templates laden

      //statisch überladen
      $this->Conf->WFconf['defaulttheme'] = 'new';

      if(!empty($this->Conf->WFtestmode) && $this->Conf->WFtestmode == true)
        $this->Tpl->Set('BODYSTYLE', 'style=background-color:red');
    }

    if(WithGUI(true)){

      $benutzername = $this->erp->Firmendaten('benutzername');
      $passwort = $this->erp->Firmendaten('passwort');
      $host = $this->erp->Firmendaten('host');
      $port = $this->erp->Firmendaten('port');
      $mailssl = $this->erp->Firmendaten('mailssl');
      $mailanstellesmtp = $this->erp->Firmendaten('mailanstellesmtp');
      $noauth = $this->erp->Firmendaten('noauth');
      $overviewpage = $this->Secure->GetGET('overviewpage');
      $overviewpageAction = $this->Secure->GetGET('overviewpageaction');
      $backlinkmodule = $this->Secure->GetGET('backlinkmodule');
      $backlinkParameter = $this->Secure->GetGET('backlinkparameter');

      // mail
      $this->mail = new PHPMailer($this);
      $this->mail->CharSet = 'UTF-8';
      $this->mail->PluginDir = 'plugins/phpmailer/';

      if($mailanstellesmtp == '1'){
        $this->mail->IsMail();
      }else{
        $this->mail->IsSMTP();

        if($noauth == '1') {
          $this->mail->SMTPAuth = false;
        }
        else {
          $this->mail->SMTPAuth = true;
        }

        if($mailssl == 1){
          $this->mail->SMTPSecure = 'tls';                 // sets the prefix to the servier
        }
        else if($mailssl == 2){
          $this->mail->SMTPSecure = 'ssl';                 // sets the prefix to the servier
        }

        $this->mail->Host = $host;

        $this->mail->Port = $port;                   // set the SMTP port for the GMAIL server

        $this->mail->Username = $benutzername;  // GMAIL username
        $this->mail->Password = $passwort;            // GMAIL password
      }

      // templates
    }

    if(WithGUI()){
      $this->createSidebarNavigation();


      $layout_iconbar = $this->erp->Firmendaten('layout_iconbar');

      if($this->erp->Version() === 'stock'){
        $this->Tpl->Set('STOCKOPEN', '<!--');
        $this->Tpl->Set('STOCKCLOSE', '-->');
      }

      //nur wenn leiste nicht deaktiviert ist
      if($layout_iconbar != 1){
        if($this->erp->Firmendaten('iconset_dunkel') == '1'){
          $this->Tpl->Parse('ICONBAR', 'iconbar_dunkel.tpl');
        }
        else{
          $this->Tpl->Parse('ICONBAR', 'iconbar.tpl');
        }
      }else{
        $this->Tpl->Parse('ICONBAR', 'iconbar_empty.tpl');
      }

      if($module !== 'kalender' && ($module !== 'welcome' && $action !== 'start')){
        $this->Tpl->Add('YUICSS', '.ui-widget-content {}');
      }

      $overviewLink = null;
      if(!empty($overviewpage)) {
        $obj = $this->loadModule($overviewpage, false);
        if($obj !== null && method_exists($obj, 'getOverViewLink')) {
          $overviewLink = $obj->getOverViewLink($overviewpageAction);
        }
      }

      $backlink = null;
      if(!empty($backlinkmodule)) {
        $obj = $this->loadModule($backlinkmodule, false);
        if($obj !== null && method_exists($obj, 'getBackLink')) {
          $backlink = $obj->getBackLink($backlinkParameter);
        }
      }

      // back to overview for case apps/einstellungen
      if($overviewLink !== null){
          $this->Tpl->Set('BACKTOOVERVIEW', '<a href="'. $overviewLink .'" title="Zur Einstellungsübersicht" id="back-to-overview"></a>');
      }

      $this->Tpl->Set('MODULE', $module);
      $this->Tpl->Set('ACTION', $action);

      $this->Tpl->Set('THEME', $this->Conf->WFconf['defaulttheme']);
      $doc_root = preg_replace("!{$_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']); # ex: /var/www
      $path = preg_replace("!^{$doc_root}!", '', __DIR__);
      $this->Tpl->Set('WEBPATH', $path);

        if(isset($backlink) && strpos($backlink,"index.php?module=") !== false && strpos($backlink, "&action=") !== false){
            $this->Tpl->Set('TABSBACK', $backlink);
        } else {
            if($action === 'list' || $action == ''){
                $this->Tpl->Set('TABSBACK', 'index.php');
            }
            else{
                $this->Tpl->Set('TABSBACK', "index.php?module=$module&action=list");
            }
        }
      $this->Tpl->Set('SAVEBUTTON', '<input type="submit" name="speichern" value="Speichern" class="button-sticky" />');

      $this->help->Run();

      $this->Tpl->Set('TMPSCRIPT', '');

      $msg2 = $this->Secure->GetGET('msg');
      $msgid = (int)$this->Secure->GetGET('msgid');
      if($msgid && method_exists($this->erp, 'GetTmpMessageOut')){
        $msg3 = $this->erp->GetTmpMessageOut($msgid);
        $this->Tpl->Set('MESSAGE', $msg3);
      }elseif($msg2 != ''){
        $msg2 = $this->erp->base64_url_decode($msg2);
        $this->Tpl->Set('MESSAGE', $msg2);
      }
      unset($msg3);



      $module = $this->Secure->GetGET('module');
      $this->Tpl->Set('MODULE', $module);
      if($module == ''){
        $module = 'welcome';
      }
      $this->Tpl->Set('ICON', $module);


      $id = $this->Secure->GetGET('id');
      $this->Tpl->Set('KID', $id);

      // pruefe welche version vorliegt
      include dirname(__DIR__).'/version.php';

      $this->Tpl->Set('REVISION', $this->erp->Revision() . ' (' . $this->erp->Branch() . ')');
      $this->Tpl->Set('REVISIONID', $this->erp->RevisionPlain());
      $this->Tpl->Set('BRANCH', $this->erp->Branch());

      $this->Tpl->Set('LIZENZHINWEIS', '| <a href="https://www.xentral.biz/lizenzhinweis" target="_blank">Lizenzhinweis</a>');

      if($this->erp->Version() === 'OSS'){
        $this->Tpl->Set('WAWIVERSION', 'Open-Source Lizenz AGPLv3.0');
      }
      else if($this->erp->Version() === 'ENT'){
        $this->Tpl->Set('WAWIVERSION', 'Enterprise Version');
      }
      else if($this->erp->Version() === 'PRO'){
        $this->Tpl->Set('WAWIVERSION', 'Professional Version');
      }
      else if($this->erp->Version() === 'PRE'){
        $this->Tpl->Set('WAWIVERSION', 'Premium Version');
      }
      else{
        $this->Tpl->Set('WAWIVERSION', 'Nutzungsbedingungen');
      }


      $this->Tpl->Set('TIMESTAMP', time());

      $this->Tpl->Set('THEME', $this->Conf->WFconf['defaulttheme']);
      $this->Tpl->Set('AKTIV_GEN_TAB1', 'selected');

      if(file_exists(__DIR__ . '/pages/textvorlagen.php') && $this->Secure->GetGET('cmd') !== 'open'){
        $showing = true;

        if($action === 'edit' && in_array($module, array('auftrag', 'angebot', 'rechnung', 'bestellung', 'lieferschein'))){

          $id = (int)$this->Secure->GetGET('id');
          if($id && $this->DB->Select("SELECT count(id) FROM " . $module . "_position WHERE $module = '$id'") > 100) {
            $showing = false;
          }
        }

        if($showing && $this->erp->RechteVorhanden('textvorlagen', 'show')){

          /** @var \Xentral\Widgets\DataTable\Service\DataTableService $service */
          $service = $this->Container->get('DataTableService');
          $buildConfig = new \Xentral\Widgets\DataTable\DataTableBuildConfig(
            'texttemplates',
            \Xentral\Modules\TextTemplate\DataTable\TextTemplateDataTable::class,
            'index.php?module=textvorlagen&action=show&cmd=table',
            false
          );

          $htmlData = $service->renderHtml($buildConfig);
          $this->Tpl->Add('TABTEXTVORLAGEN', $htmlData);
          $this->Tpl->Add('TVFILTERHEADER', '<fieldset><legend>Textvorlage suchen und einfügen</legend></fieldset>');

          $this->YUI->AutoComplete('textvorlageprojekt', 'projektname', 1);
          $this->YUI->CkEditor('textvorlagetext', 'belege');
          $this->Tpl->Add('JSSCRIPTS', $this->Tpl->OutputAsString('textvorlagen.tpl'));
        }
      }

      $isTestlizenz = !empty(erpAPI::Ioncube_Property('testlizenz'));
      $isCloud = erpAPI::Ioncube_Property('iscloud');
      $isDemo = $isTestlizenz && $isCloud;
      $activateDoubleClick = false;
      /** @var Dataprotection $dataProtectionModule */
      $dataProtectionModule = $this->loadModule('dataprotection');

      if($isCloud
        && $dataProtectionModule !== null
        && $dataProtectionModule->isGoogleAnalyticsActive()
      ){
        $activateDoubleClick = true;
        $this->Tpl->Add(
          'SCRIPTJAVASCRIPT',
          '<!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-1088253-14"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag(\'js\', new Date());
        
          gtag(\'config\', \'UA-1088253-14\');
        </script>');

        $this->Tpl->Add('ADDITIONALCSPHEADER', ' www.googletagmanager.com www.google-analytics.com ssl.google-analytics.com stats.g.doubleclick.net ');
      }
      if($dataProtectionModule !== null && $dataProtectionModule->isHubspotActive()) {
        $activateDoubleClick = true;
        $this->Tpl->Add(
          'SCRIPTJAVASCRIPT',
          '<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/6748263.js"></script>'
        );
        $this->Tpl->Add(
          'ADDITIONALCSPHEADER',
          ' js.hs-scripts.com js.hscollectedforms.net js.hsleadflows.net js.hs-banner.com js.hs-analytics.net api.hubapi.com js.hsadspixel.net '
        );
        $this->Tpl->Add(
          'ADDITIONALCSPHEADER',
          'forms.hubspot.com forms.hsforms.com track.hubspot.com www.google.com www.google.de '
        );
      }
      if($activateDoubleClick) {
        $this->Tpl->Add('ADDITIONALCSPHEADER', ' googleads.g.doubleclick.net ' );
      }
      $hooktpl = 'JSSCRIPTS';
      $this->erp->RunHook('eproosystem_ende', 1, $hooktpl);
    }
  }

    /**
     * @param {String} $path
     * @param {String} $category
     *
     * @return String
     */
    public function getSVG($path, $filename){
        $filename = str_replace(' ', '', strtolower($filename));

        $iconPath = $path . $filename . '.svg';

        return file_get_contents($iconPath);
    }

    protected function getCounterFor(string $type)
    {

    }
    /**
     * creates and appends sidebar navigation
     */
  public function createSidebarNavigation(){
      include dirname(__DIR__).'/version.php';
      $appstore = $this->loadModule('appstore');

      $svgPath = 'themes/new/images/sidebar/';

      $activeModule = $this->Secure->GetGET('module');
      $activeAction = $this->Secure->GetGET('action');

      $navigation = $this->Page->CreateNavigation($this->erp->Navigation(), true, $activeModule, $activeAction);

      $activeCategory = $appstore->GetCategoryByModule($activeModule, $this->Secure->GetGET('id'));

      $appointmentCount = (int)$this->DB->Select(
          sprintf(
              "SELECT COUNT(ke.id) 
        FROM kalender_event AS ke 
        LEFT JOIN kalender_user AS ku ON ku.event=ke.id
        WHERE DATE_FORMAT(ke.von,'%%Y-%%m-%%d')=DATE_FORMAT(NOW(),'%%Y-%%m-%%d') 
            AND (
                ke.adresse=%d
                OR ke.adresseintern=%d 
                OR ku.userid=%d 
        )",
              $this->User->GetAdresse(),$this->User->GetAdresse(), $this->User->GetID()
          )
      );

      if($appointmentCount <=0) {
          $appointmentCount=0;
      }

      if($this->erp->ModulVorhanden('wiedervorlage') && $this->erp->RechteVorhanden('wiedervorlage','list')) {
        $resubmissionCount = (int)$this->DB->Select(
          sprintf(
            "SELECT count(*) 
            FROM `wiedervorlage` AS `w` 
            LEFT JOIN `adresse` AS `a` ON w.adresse = a.id 
            LEFT JOIN `projekt` AS `p` on p.id = a.projekt 
            WHERE w.abgeschlossen = 0 
              AND TIMESTAMP(concat(w.datum_erinnerung,' ',w.zeit_erinnerung)) < TIMESTAMP(now())
               AND (w.adresse_mitarbeiter = %d OR (w.adresse_mitarbeiter=0 AND w.bearbeiter=%d)) ",
            $this->User->getAdresse(),$this->User->getAdresse()
          ).$this->erp->ProjektRechte('w.projekt')
        );
      }


      // Creates user specific items
      $possibleUserItems = [
          'Inbox' => [
              'link' => 'index.php?module=ticket&action=offene',
              'counter' => $this->erp->AnzahlOffeneTickets()
          ],
          'Aufgaben' => [
              'link' => 'index.php?module=aufgaben&action=list',
              'counter' => $this->erp->AnzahlOffeneAufgaben()
          ],
          'Wiedervorlage' => [
              'link' => 'index.php?module=wiedervorlage&action=list',
              'counter' => $resubmissionCount,
          ],
          'Kalender' => [
              'link' => 'index.php?module=kalender&action=list',
              'counter' => $appointmentCount
              ],
      ];
      $possibleUserItems['Apps'] = [
        'link'=> 'index.php?module=appstore&action=list&cmd=allapps'
      ];


      if(!empty(erpAPI::Ioncube_Property('testlizenz')) && $this->User->GetType() === 'admin'){
          $possibleUserItems['Starte hier!'] = [
              'link' => 'index.php?module=learningdashboard&action=list',
              'type' => 'cta'
          ];
      }

      $userItems = '<div class="sidebar-list small-items separator-bottom">';

      foreach($possibleUserItems as $title => $data){
          $classList = '';
          $link = $data['link'];
          $counter = isset($data['counter']) && ((is_int($data['counter']) && $data['counter'] >= 1)
            || (is_string($data['counter']) && $data['counter'] !== ''))
              ? '<div class="item-counter">'. $data['counter'] .'</div>'
              : '';
          $svg = $this->getSVG($svgPath, $title);
          $active = '';

          if(strtolower($title) === strtolower($activeModule)){
              $active = 'current-module';
          }

          if(isset($data['type']) && $data['type'] === 'cta'){
              $classList .= 'button button-secondary';
          }
          $userItems .=
              '<a href="'. $link .'&top=' .base64_encode($title).'" class="list-item '. $active .' '. $classList .'">'
              . $svg
              . '<div class="title">'. $this->Tpl->pruefeuebersetzung($title) .'</div>'
              . $counter
              .'</a>';
      }

      $userItems .= '</div>';

      // Creates main navigation steps
      $naviHtml = '<div class="sidebar-list">';

      foreach($navigation as $key => $listitem){
          if(!empty($listitem)){
              if (isset($listitem['original_title'])) {
                  $svg = $this->getSVG($svgPath, $listitem['original_title']);
              } else {
                  $svg = $this->getSVG($svgPath, $listitem['title']);
              }
              $active = '';
              if($listitem['active']) {
                $active = 'current-module';
              }

              $naviHtml .=
                  '<div class="list-item '. $active .'">'
                        . $svg .
                        '<div class="title">'. $listitem['title'] .'</div>';

              if(isset($listitem["sec"])){
                  $naviHtml .=
                      '<div class="sidebar-submenu">
                            <div>';

                  foreach($listitem["sec"] as $subkey => $subitem){
                      $naviHtml .= '<a href="'. $subitem['link'].'">'. $subitem['title'] .'</a>';
                  }

                  $naviHtml .= '</div>
                        </div>';
              }

              $naviHtml .= '</div>';
          }
      }

      $naviHtml .= '</div>';

    /** @var Dataprotection $obj */
      $obj = $this->loadModule('dataprotection');
      $showChat = method_exists('erpAPI','Ioncube_Property')
      && !empty(erpAPI::Ioncube_Property('chatactive'))
      && !empty(erpAPI::Ioncube_Property('chat'))
      && $obj !== null
      && method_exists($obj, 'isZenDeskActive')
      && $obj->isZenDeskActive();

      $possibleFixedItems = [];
      if(!$showChat) {
        $possibleFixedItems['Hilfe'] = 'id="showinlinehelplink"';
      }

      // Creates fixed bottom navigation items
      $possibleFixedItems['Datenschutz'] = 'index.php?module=dataprotection&action=list';

      $fixedItems = '<div class="sidebar-list bottom">';

      foreach($possibleFixedItems as $title => $link){
          $svg = $this->getSVG($svgPath, $title);
          $active = '';

          if(strtolower($title) === strtolower($activeModule)){
              $active = 'current-module';
          }

          if(strpos($link, 'index.php?') !== false){
              $fixedItems .=
                  '<a href="'. $link .'&top=' .base64_encode($title).'" class="list-item '. $active .'">'
                  . $svg .
                  '<div class="title">'. $this->Tpl->pruefeuebersetzung($title) .'</div>'
                  .'</a>';
          } elseif(strpos($link, 'id="') !== false) {
              $fixedItems .=
                  '<div ' . $link . ' class="list-item">'
                  . $svg .
                  '<div class="title">'. $this->Tpl->pruefeuebersetzung($title) .'</div>'
                  .'</div>';
          }
      }

      $fixedItems .= '</div>';

      $version = '';
      if(isset($version_revision) && $version_revision != '') {
        $version .= '<div class="sidebar-software-version">xentral.com, v. '. $version_revision .'</div>';
      }

      if($userId = $this->User->GetID()){

      /** @var \Xentral\Modules\User\Service\UserConfigService $userConfig */
      $userConfig = $this->Container->get('UserConfigService');
      $sidebarCollapsed = $userConfig->tryGet('sidebar_collapsed', $userId);
      $sidebarClasses = $sidebarCollapsed === true ? 'class="collapsed"' : '';
      }else{
          $sidebarClasses = '';
      }

      // set generated HTML to template
      $this->Tpl->Set('USERITEMS',  $userItems);
      $this->Tpl->Set('NAVIGATIONITEMS',  $naviHtml);
      $this->Tpl->Set('FIXEDITEMS',  $fixedItems);
      $this->Tpl->Set('XENTRALVERSION', $version);
      $this->Tpl->Set('SIDEBAR_CLASSES', $sidebarClasses);
      $isDevelopmentVersion = method_exists('erpAPI','Ioncube_Property')
        && !empty(erpAPI::Ioncube_Property('isdevelopmentversion'));
      if($isDevelopmentVersion) {
        $this->Tpl->Add(
          'SIDEBARLOGO',
          @file_get_contents(__DIR__ . '/themes/new/templates/sidebar_development_version_logo.svg')
        );
        $this->Tpl->Add(
          'SIDEBARLOGO',
          '<img class="development" src="themes/new/templates/development_version_logo.png" alt="logo" />'
        );
      }
      else{
        $this->Tpl->Add('SIDEBARLOGO', @file_get_contents(__DIR__ . '/themes/new/templates/sidebar_logo.svg'));
      }

      $this->Tpl->Parse('SIDEBAR', 'sidebar.tpl');
      $this->Tpl->Parse('PROFILE_MENU', 'profile_menu.tpl');
  }

  /**
   * @return string
   */
  public function CheckUserdata()
  {
    $isSecure = false;
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
      $isSecure = true;
    }
    elseif ((!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')) {
      $isSecure = true;
    }
    $REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
    if(!empty($_SERVER['SCRIPT_URI']))
    {
      $weburl = $_SERVER['SCRIPT_URI'];
    }elseif(!empty($_SERVER['REQUEST_URI']) && !empty($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR']!=='::1' && (empty($_SERVER['SERVER_SOFTWARE']) || strpos($_SERVER['SERVER_SOFTWARE'],'nginx')===false))
    {
      $weburl = (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR']?$REQUEST_PROTOCOL.'://'.$_SERVER['SERVER_ADDR'].(!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443?':'.$_SERVER['SERVER_PORT']:''):'').$_SERVER['REQUEST_URI'];
    } elseif(!empty($_SERVER['SERVER_NAME'])) //MAMP auf macos
    {
      $weburl = str_replace(array('setup/setup.php?step=5','setup/setup.php'),'',$REQUEST_PROTOCOL.'://'.$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'].$_SERVER['SCRIPT_NAME']);
    }else{
      $weburl = '';
    }
    $userdatadir = $this->Conf->WFuserdata;
    $tmpfile = md5(microtime(true)).'.html';
    $ret = '';
    if(!file_put_contents(rtrim($userdatadir,'/').'/'.$tmpfile,'TEST')){
      $ret = 'Das Verzeichnis userdata ist nicht schreibbar (Rechte) oder die Festplatte ist voll';
    }
    if(!empty($weburl) && stripos($weburl, 'http') !== 0)
    {
      if(is_file(rtrim($userdatadir,'/').'/'.$tmpfile)){
        unlink(rtrim($userdatadir,'/').'/'.$tmpfile);
      }
      if(method_exists($this->erp, 'setSystemHealth')) {
        $this->erp->setSystemHealth('server', 'userdata_writeable',!empty($ret)?'warning':'ok', $ret);
      }
      return $ret;
    }
    $pos = strpos($weburl,'index.php');
    if($pos){
      $weburl = rtrim(substr($weburl, 0 , $pos),'/');
    }
    $thisfoldera = explode('/',__DIR__);
    $userdataa = explode('/',$this->Conf->WFuserdata);
    foreach($thisfoldera as $k => $v)
    {
      if(isset($userdataa[$k]) && $userdataa[$k] == $v)
      {
        unset($userdataa[$k], $thisfoldera[$k]);
      }
    }
    $userdata = trim(implode('/', $userdataa),'/');
    $thisfolder = trim(implode('/', $thisfoldera),'/');
    if(substr($weburl, - strlen($thisfolder)) == $thisfolder)
    {
      $userdata = substr($weburl , 0, strlen($weburl) - strlen($thisfolder)).$userdata.'/';
    }else
    {
      if(is_file(rtrim($userdatadir,'/').'/'.$tmpfile)){
        unlink(rtrim($userdatadir,'/').'/'.$tmpfile);
      }
      if(method_exists($this->erp, 'setSystemHealth')) {
        $this->erp->setSystemHealth('server', 'userdata_writeable', 'ok');
      }
      return $ret;
    }
    if(is_dir($userdatadir)) {
      $content = @file_get_contents($userdata);

      if($content != '') {
        if(is_file(rtrim($userdatadir, '/') . '/' . $tmpfile)){
          unlink(rtrim($userdatadir, '/') . '/' . $tmpfile);
        }
        $ret = 'Sicherheitswarnung: Verzeichnis userdata ist von extern einsehbar' . ($ret === '' ? '' : ' und nicht beschreibbar') . '!';
        if(method_exists($this->erp, 'setSystemHealth')){
          $this->erp->setSystemHealth('server', 'userdata_writeable', 'error', $ret);
        }
        return $ret;
      }


      if(file_put_contents(rtrim($userdatadir,'/').'/'.$tmpfile,'TEST')) {
        if(@file_get_contents($userdata . $tmpfile) === 'TEST') {
          unlink(rtrim($userdatadir, '/') . '/' . $tmpfile);
          $ret = 'Sicherheitswarnung: Verzeichnis userdata ist von extern einsehbar' . ($ret === '' ? '' : ' und nicht beschreibbar') . '!';
          if(method_exists($this->erp, 'setSystemHealth')){
            $this->erp->setSystemHealth('server', 'userdata_writeable', 'error', $ret);
          }
          return $ret;
        }

        unlink(rtrim($userdatadir,'/').'/'.$tmpfile);
        if(method_exists($this->erp, 'setSystemHealth')) {
          $this->erp->setSystemHealth('server', 'userdata_writeable', 'ok');
        }
        return '';
      }
    }
    if(is_file(rtrim($userdatadir,'/').'/'.$tmpfile)){
      unlink(rtrim($userdatadir,'/').'/'.$tmpfile);
    }
    $this->erp->setSystemHealth('server', 'userdata_writeable', 'ok');
    return $ret;
  }

  public function calledBeforeFinish()
  {
  }

  public function Laender($module, $action, $id, $lid)
  {
    /*********** select field for projekt ***************/
    $selectid = $this->Secure->GetPOST('projekt');
    if($selectid=='' && $module !== 'projekt') {
      if(!empty($this->Conf->WFdbType) && $this->Conf->WFdbType==='postgre')
      {
        //POSTGRE -->  dringend bei statements wo es die tabelle gibt machen!
        $selectid = $this->DB->Select("SELECT projekt FROM `$module` WHERE id='$id' LIMIT 1");

      } else {
        $selectid = $id > 0?$this->DB->Select("SELECT projekt FROM `$module` WHERE id='$id' LIMIT 1"):NULL;
      }
    }

    $color_selected = '';
    $options = $this->erp->GetProjektSelect($selectid,'');
    $this->Tpl->Set('EPROO_SELECT_PROJEKT',"<select name=\"projekt\"
        style=\"background-color:$color_selected;\"
        onChange=\"this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor\">$options</select>");
    $this->Tpl->Set('EPROO_SELECT_UNTERPROJEKT','<div id="selectunterprojekt">
        <select name="unterprojekt">
        </select>
        </div>');


    $this->Tpl->Set('LESEZEICHEN','<a title="Angebot" href="index.php?module=angebot&action=search">Angebotssuche</a>&nbsp;');
    $this->Tpl->Add('LESEZEICHEN','<a title="Auftrag" href="index.php?module=auftrag&action=search">Auftragssuche</a>&nbsp;');
    $this->Tpl->Add('LESEZEICHEN','<a title="Rechnung" href="index.php?module=rechnung&action=search">Rechnungssuche</a>&nbsp;');
    $this->Tpl->Add('LESEZEICHEN','<a title="Adresse" href="index.php?module=adresse&action=search">Adressensuche</a>&nbsp;');
    $this->Tpl->Add('LESEZEICHEN','<a title="Adresse" href="index.php?module=wareneingang&action=paketannahme">Paket Annahme</a>');

    $this->Tpl->Set('KURZUEBERSCHRIFT',$module);

    if($action==='edit'){
      $this->Tpl->Add('KURZUEBERSCHRIFT1', 'BEARBEITEN');
    }

    $this->Tpl->Set('KURZUEBERSCHRIFTFIRSTUPPER',ucfirst($module));

    /*********** select field for projekt ***************/
    if($this->Secure->GetPOST('land')=='' && $this->Secure->GetGET('land')=='')
    {
      if(in_array($module, array('adresse', 'adresse_import', 'anfrage', 'angebot', 'ansprechpartner', 'arbeitsnachweis', 'auftrag', 'belege', 'belegegesamt', 'belegeregs', 'bestellung', 'bundesstaaten', 'dokumente', 'gutschrift', 'inventur', 'laendersteuersaetze', 'lieferadressen', 'lieferschein', 'preisanfrage', 'produktion', 'proformarechnung', 'projekt', 'rechnung', 'retoure', 'serviceauftrag', 'shopexport_sprachen', 'shopexport_versandarten', 'spedition', 'spedition_packstuecke', 'steuertexte', 'ustprf', 'verpackungen_details')))
      {
        $countryField = 'land';
        if ($module === 'retoure') {
          $countryField = 'lieferland';
        }
        $sqlCountry = sprintf('SELECT %s FROM `%s` WHERE id = %d LIMIT 1', $countryField, $module, $id);
        $selectid = $id ? $this->DB->Select($sqlCountry) : '';
      }else{
        $selectid = '';
      }
      if(empty($selectid)) {
        $selectid = $lid?$this->DB->Select("SELECT land FROM `lieferadressen` WHERE id='$lid' LIMIT 1"):'';
      }
    }
    else if($this->Secure->GetGET('land')!=''){
      $selectid = $this->Secure->GetGET('land');
    }
    else{
      $selectid = $this->Secure->GetPOST('land');
    }


    /*********** select field for projekt ***************/
    if($module==='adresse' && $this->Secure->GetPOST('rechnung_land')=='' && $this->Secure->GetGET('rechnung_land')=='')
    {
      $selectidrechnung = $id?$this->DB->Select("SELECT rechnung_land FROM adresse WHERE id='$id' LIMIT 1"):'';
    }
    else{
      $selectidrechnung = $this->Secure->GetPOST('rechnung_land');
    }

    /*********** select field for projekt ***************/
    $lid = $this->Secure->GetGET('lid');

    if($module==='adresse' && $this->Secure->GetPOST('ansprechpartner_land')=='' && $this->Secure->GetGET('ansprechpartner_land')=='')
    {
      $selectidansprechpartner = $lid?$this->DB->Select("SELECT ansprechpartner_land FROM ansprechpartner WHERE id='$lid' LIMIT 1"):'';
      if(empty($selectidansprechpartner)) {
        $selectidansprechpartner = $this->DB->Select("SELECT land FROM adresse WHERE id='$id' LIMIT 1");
      }
      //if($selectid<=0 && $module=="lieferadressepopup") $this->DB->Select("SELECT land FROM `lieferadressen` WHERE id='$id' LIMIT 1");
    }
    else{
      $selectidansprechpartner = $this->Secure->GetPOST('ansprechpartner_land');
    }

    if($module==='adresse' && $this->Secure->GetPOST('land')=='' && $this->Secure->GetGET('land')=='')
    {
      $selectidlieferadresse = $lid?$this->DB->Select("SELECT land FROM lieferadressen WHERE id='$lid' LIMIT 1"):'';
      if($selectidlieferadresse =='') {
        $selectidlieferadresse = $this->DB->Select("SELECT land FROM adresse WHERE id='$id' LIMIT 1");
      }
    }
    else{
      $selectidlieferadresse = $this->Secure->GetPOST('land');
    }

    if($module==='proformarechnung' && $this->Secure->GetPOST('verzollungland')=='' && $this->Secure->GetGET('verzollungland')=='')
    {
      $selectidverzollung = $this->DB->Select("SELECT verzollungland FROM proformarechnung WHERE id='$id' LIMIT 1");
    }
    else{
      $selectidverzollung = $this->Secure->GetPOST('land');
    }
    $this->uselaendercache = true;
    $this->Tpl->Set('EPROO_SELECT_LAND',"<select name=\"land\" id=\"land\" [COMMONREADONLYSELECT]>".$this->SelectLaenderliste($selectid)."</select>");
    $this->Tpl->Set('EPROO_SELECT_LIEFERLAND',"<select name=\"lieferland\" id=\"lieferland\" [COMMONREADONLYSELECT]>".$this->SelectLaenderliste($selectid)."</select>");
    $this->Tpl->Set('EPROO_SELECT_LAND_RECHNUNG',"<select name=\"rechnung_land\" id=\"rechnung_land\" [COMMONREADONLYSELECT]>".$this->SelectLaenderliste($selectidrechnung)."</select>");
    $this->Tpl->Set('EPROO_SELECT_LAND_ANSPRECHPARTNER',"<select name=\"ansprechpartner_land\" id=\"ansprechpartner_land\" [COMMONREADONLYSELECT]>".$this->SelectLaenderliste($selectidansprechpartner)."</select>");
    $this->Tpl->Set('EPROO_SELECT_LAND_LIEFERADRESSEN',"<select name=\"land\" id=\"land\" [COMMONREADONLYSELECT]>".$this->SelectLaenderliste($selectidlieferadresse)."</select>");
    $this->Tpl->Set('EPROO_SELECT_LAND_VERZOLLUNG',"<select name=\"verzollungland\" id=\"land\" [COMMONREADONLYSELECT]>".$this->SelectLaenderliste($selectidverzollung)."</select>");
    $this->uselaendercache = false;
    if($this->Secure->GetPOST('lieferland')=='')
    {
        if(in_array($module,array('amazon_inboundshipmentplan', 'angebot', 'auftrag', 'bestellung', 'produktion', 'proformarechnung', 'retoure', 'serviceauftrag', 'spedition')))
        {
          $selectid = $id?$this->DB->Select("SELECT lieferland FROM `$module` WHERE id='$id' LIMIT 1"):'';
        }else {
          $selectid = '';
        }
    }
    else{
      $selectid = $this->Secure->GetPOST('lieferland');
    }

    $this->Tpl->Set('EPROO_SELECT_LIEFERLAND','<select name="lieferland" id="lieferland" [COMMONREADONLYSELECT]>'.$this->SelectLaenderliste($selectid).'</select>');

    $this->Tpl->Set('VORGAENGELINK',"<a href=\"#\" onclick=\"var ergebnistext=prompt('Lesezeichen:','".ucfirst($module)."'); if(ergebnistext!='' && ergebnistext!=null) window.location.href='index.php?module=welcome&action=vorgang&titel='+ergebnistext;\">*</a>");


    if($module==='adresse' || $module==='artikel' || $module==='angebot' || $module==='rechnung' || $module==='auftrag' || $module==='gutschrift' || $module==='lieferschein'
        || $module==='onlineshops' || $module==='geschaeftsbrief_vorlagen' ||  $module==='emailbackup' || $module==='ticket_vorlage')
    {
      // module auf richtige tabellen mappen
      if($module==='onlineshops') {
        $this->erp->Standardprojekt('shopexport',$id);
      }
      else {
        $this->erp->Standardprojekt($module,$id);
      }

      $bezeichnungaktionscodes = $this->erp->Firmendaten('bezeichnungaktionscodes');
      if((String)$bezeichnungaktionscodes === ''){
        $bezeichnungaktionscodes = 'Aktionscode';
      }
      $this->Tpl->Set('BEZEICHNUNGAKTIONSCODE', $bezeichnungaktionscodes);
    }
}

  /**
   * @param string $module
   * @param string $action
   * @param int    $id
   */
  public function addPollJs($module, $action, $id)
  {
    $noTimeoutUserEdit = 0;
    $startTime = 3000;
    $repeatTime = 5000;
    $firmendaten_repeattime = 1000*(int)$this->erp->Firmendaten('poll_repeattime');
    if($firmendaten_repeattime > $repeatTime) {
      $repeatTime = $firmendaten_repeattime;
      if($repeatTime > 25000) {
        $repeatTime = 25000;
      }
    }
    $invisibleTime = 25000;
    if(empty($id)
      || (
        in_array($module, ['auftrag','rechnung','gutschrift','angebot','lieferschein'], false)
        &&
        $this->DB->Select(
          sprintf(
            'SELECT schreibschutz FROM `%s` WHERE id = %d',
            $module,
            $id
          )
        )
      )
    ) {
      $noTimeoutUserEdit = 1;
    }


    if($action !== 'positionen'){
      $pollUid = sha1(uniqid('poll', true));

      $this->Tpl->Add('JAVASCRIPT', "
        var logErrorCount = 0;
        var hidden, visibilityChange; 
if (typeof document.hidden !== \"undefined\") { // Opera 12.10 and Firefox 18 and later support 
  hidden = \"hidden\";
  visibilityChange = \"visibilitychange\";
} else if (typeof document.msHidden !== \"undefined\") {
  hidden = \"msHidden\";
  visibilityChange = \"msvisibilitychange\";
} else if (typeof document.webkitHidden !== \"undefined\") {
  hidden = \"webkitHidden\";
  visibilityChange = \"webkitvisibilitychange\";
}

        function showLockScreen(errorMsg) {
          logErrorCount++;
          if (typeof errorMsg !== 'undefined' && errorMsg !== null) {
            console.error('Polling error: ' + errorMsg);
          }
          if (typeof LockScreen === 'undefined') {
            return;
          }
          if(logErrorCount <= 2) {
            return;
          }
          LockScreen.show();
        }
        
        function hideLockScreen() {
          if (typeof LockScreen === 'undefined') { return; }
          LockScreen.hide();
        }
        
        // Benutzer hat Sperrbildschirm per Button geschlossen 
        // => Sperrbildschirm schliessen und Counter zurücksetzen  
        function resetLockScreen() {
          if (typeof LockScreen === 'undefined') { return; }
          LockScreen.hide();
          logErrorCount = 0;
        }

        var isloggedin = true;
        function executeQuery() {
          if(typeof generate == 'undefined'){
            return;
          }    
          $.ajax({
            url: 'index.php?module=welcome&action=poll&smodule=$module&cmd=messages&saction=$action&sid=$id&user=" .
              $this->User->GetID().(!empty($noTimeoutUserEdit)?'&nousertimeout=1':'') . "&uid=".$pollUid."',
            type: 'POST',
            data:{
              invisible : typeof document.hidden != 'undefined'?
              (document.hidden?1:0):
              (typeof document.msHidden !== 'undefined'?
              (document.msHidden?1:0):(typeof document.webkitHidden != 'undefined'?(document.webkitHidden?1:0):2))
            },
            success: function(data) {
              if (data === '') {
                showLockScreen('Polling result is empty.');
                return;
              }
              
              // do something with the return value here if you like
              try {
                var meinelist = JSON.parse(data);
              } catch (err) {
                showLockScreen('JSON parse error (' + err + ')');
                return;
              }
              logErrorCount = 0;
              // Hide lock screen on successful request
              hideLockScreen();

              for(var i=0;i<meinelist.length;i++)
              {
                obj = meinelist[i];
                if (typeof obj.event !== 'undefined') {
                  switch(obj.event)
                  {
                    case 'logout':
                      isloggedin = false;
                      break;
  
                    case 'chatbox':
                      generate('chatbox', obj.message);
                      break;
                      
                    case 'notification':
                      if (typeof Notify === 'undefined') {
                        console.warn('Notify not found.');
                        return;
                      }
                      // Benachrichtigung erstellen
                      Notify.create(obj.type, obj.title, obj.message, obj.priority, obj.options);
                      break;
                  }
                }
              }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
              if (XMLHttpRequest.readyState === 4) {
                // HTTP error
                var statusCode = XMLHttpRequest.status;
                var statusText = XMLHttpRequest.statusText;
                showLockScreen('HTTP error (' + statusCode + ' ' + statusText + ')');
              } else if (XMLHttpRequest.readyState === 0) {
                // Network error (connection refused, connection lost, access denied, ...)
                showLockScreen('Network error.');
              } else {
                // Something weird is happening
                showLockScreen('Unknown request error.');
              }
            }
          });
          if(isloggedin){
              setTimeout(executeQuery, (typeof hidden == 'undefined' || !document[hidden])?".$repeatTime.":".$invisibleTime."); // you could choose not to continue on failure...
          }
          else {
              logErrorCount=3;
              showLockScreen('logged out.');
          }
        }
  
        $(document).ready(function() {
          // run the first time; all subsequent calls will take care of themselves
          setTimeout(executeQuery, ".$startTime.");
          
          // Benutzer kann Sperrbildschirm per Button schliessen
          $(document).on('click', '#lockscreen-close-button', function (e) {
            e.preventDefault();
            resetLockScreen();
          });
        });
      ");
    }
  }

  /**
   * @return string
   */
  protected function getShortenedUsername(){
      $username = $this->User->GetName();
      $usernameArray = explode(' ', $username);
      $usernameWordsLength = count($usernameArray);

      // Replace the last part of the username by it's abbreviation; example "Sepp Maier" => "Sepp M."
      if($usernameWordsLength > 1){
          $lastName = array_pop($usernameArray);
          $lastName = mb_substr($lastName, 0, 1) . '.';
          $username = implode(' ', $usernameArray) . ' ' . $lastName;
      }

      return $username;
  }

  public function calledWhenAuth($type)
  {
    if(!WithGUI()){
      return;
    }
    $id = $this->Secure->GetGET('id');
    $lid = $this->Secure->GetGET('lid');
    $module = $this->Secure->GetGET('module');
    $action  = $this->Secure->GetGET('action');

    // Check Timeout Users
    $this->DB->Update('UPDATE useronline SET login=0 WHERE DATE_ADD(time,INTERVAL '.(int)$this->Conf->WFconf['logintimeout'].' second) < NOW() AND login=1');
    if($this->DB->affected_rows() > 0) {
      $this->User->createCache();
    }

      // userd edit ajax call
    $poll = true;
    if($poll) {
      $this->addPollJs($module, $action, $id);
    }

    /** @var Ajax $ajax */
    $ajax = $this->loadModule('ajax');
    $this->Tpl->Set('PROFILEPICTURE', $ajax->getProfileHtml($this->User->GetID(), 'Profilbild', null, 38));

    $this->Tpl->SetText('USERID',$this->User->GetID());

    $this->Tpl->SetText('USERNAME_SHORTENED',$this->getShortenedUsername());
    $this->Tpl->SetText('BENUTZER',$this->User->GetName());
    $this->Tpl->Set('CALENDERWEEK',date('W'));

    $this->Tpl->Set('CALENDERWEEKMAX',date('W', date(mktime(0, 0, 0, 1, 1, date('Y')+1) - 4*86400)));

    $this->Tpl->Set('VERSIONUNDSTATUS','Server: '.$_SERVER['SERVER_NAME'].'&nbsp;|&nbsp;Client: '.$_SERVER [ 'REMOTE_ADDR' ].'&nbsp;|&nbsp;User: '.$this->User->GetDescription());
    $this->Tpl->Set('SERVERDATE','Serverzeit: '.date('d.m.Y H:i').' Uhr');

    $this->Tpl->SetText('MODUL',ucfirst($module));

    $this->Tpl->Set('HTMLTITLE','{|[MODUL]|} | Xentral ');


    switch($module)
    {
      case 'artikel':
        switch($action) {
          case 'einkaufeditpopup':
            $artikeltmpid = $this->DB->Select("SELECT artikel FROM einkaufspreise WHERE id='$id' LIMIT 1");
          break;
          case 'verkaufeditpopup':
            $artikeltmpid = $this->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$id' LIMIT 1");
          break;
          default: $artikeltmpid = $id;
        }
        $this->Tpl->AddText('HTMLTITLE','| '.$this->DB->Select("SELECT CONCAT(nummer,' ',name_de) FROM artikel WHERE id='$artikeltmpid' LIMIT 1"));
      break;
      case 'angebot':
      case 'auftrag':
      case 'rechnung':
      case 'lieferschein':
      case 'gutschrift':
      case 'bestellung':
      case 'anfrage':
        $this->Tpl->AddText('HTMLTITLE','| '.$this->DB->Select("SELECT CONCAT(if(belegnr!='',belegnr,'ENTWURF'),' ',name) FROM $module WHERE id='$id' lIMIT 1"));
      break;
    }

    $firmenname = $this->erp->Firmendaten('name');

    $firmenfarbe = $this->erp->Firmendaten('firmenfarbe');
    $this->Tpl->SetText('FIRMENNAME',$firmenname);
    $this->Tpl->Set('NBBREITE','275');
    $this->Tpl->Set('NBPROZ','25');

    $class = '';
      $checkkommen = $this->DB->Select("SELECT kommen FROM stechuhr WHERE adresse='".$this->User->GetAdresse()."' ORDER by datum DESC LIMIT 1");
      if($checkkommen!=0)
      {
        $kommen = '<a href="#" onclick="if(confirm(\'Status von Arbeit auf Pause / Freizeit ändern?\')) window.location.href=\'index.php?module=stechuhr&action=change&cmd=pause&smodule='.$module.'&saction='.$action.($id?'&sid='.$id:'').'\';"
            >&nbsp;Arbeit&nbsp;</a>';
      }
      else
      {
        $class = 'red';
        $kommen = '<a href="#" onclick="if(confirm(\'Status von Pause / Freizeit auf Arbeit ändern?\')) window.location.href=\'index.php?module=stechuhr&action=change&cmd=arbeit\';">&nbsp;Pause</a>';
      }
    $this->Tpl->Set('STECHUHRCLASS', $class);
    $this->Tpl->Set('STECHUHR',$kommen);

    $tmpfirmendatenfkt = 'Firmendaten';
    if(method_exists($this->erp,'TplFirmendaten')){
      $tmpfirmendatenfkt = 'TplFirmendaten';
    }
    $firmenfarbe = $this->erp->$tmpfirmendatenfkt('firmenfarbe');
    if($firmenfarbe ==''){
      $firmenfarbe = '#48494b';
    }

    $firmenfarbehell = $this->erp->$tmpfirmendatenfkt('firmenfarbehell');
    if($firmenfarbehell ==''){
      $firmenfarbehell = '#c2e3ea';
    }

    $firmenfarbedunkel = $this->erp->$tmpfirmendatenfkt('firmenfarbedunkel');
    if($firmenfarbedunkel ==''){
      $firmenfarbedunkel = '#53bed0';
    }

    $firmenfarbeganzdunkel = $this->erp->$tmpfirmendatenfkt('firmenfarbeganzdunkel');
    if($firmenfarbeganzdunkel ==''){
      $firmenfarbeganzdunkel = '#018fa3';
    }

    $navigationfarbeschrift = $this->erp->$tmpfirmendatenfkt('navigationfarbeschrift');
    if($navigationfarbeschrift ==''){
      $navigationfarbeschrift = '#c9c9cb';
    }

    $navigationfarbe = $this->erp->$tmpfirmendatenfkt('navigationfarbe');
    if($navigationfarbe ==''){
      $navigationfarbe = $firmenfarbe;
    }

    $navigationfarbeschrift2 = $this->erp->$tmpfirmendatenfkt('navigationfarbeschrift2');
    if($navigationfarbeschrift2 =='')
    {
      $navigationfarbeschrift2 = $navigationfarbe;
    }

    $navigationfarbe2 = $this->erp->$tmpfirmendatenfkt('navigationfarbe2');
    if($navigationfarbe2 ==''){
      $navigationfarbe2 = $navigationfarbeschrift;
    }

    $this->Tpl->Set('COLORCSS','--color1: '.$firmenfarbehell.';'."\r\n");
    $this->Tpl->Add('COLORCSS','--color2: '.$firmenfarbedunkel.';');
    if($this->erp->Firmendaten('firmenhoherformularkontrast'))
    {
      $this->Tpl->Add('COLORCSS','--textfield-border: #666;');
    }else{
      $this->Tpl->Add('COLORCSS','--textfield-border: #d9d9d9;');
    }

    $this->Tpl->Set('COLORCSSFILE','color3.css');


    if(($module!=='welcome' && $action!=='start') && is_file('./themes/new/css/grid_cache.css')){
      $this->Tpl->Add('CSSLINKS', '<link href="./themes/new/css/grid_cache.css" rel="stylesheet" type="text/css" />');
    }
    else{
      $this->Tpl->Add('CSSLINKS', '<link href="./index.php?module=welcome&action=css&file=grid.css" rel="stylesheet" type="text/css" />');
    }

    if(($module!=='welcome' && $action!=='start') && ($module!=='kalender' && $action!=='list') && is_file('./themes/new/css/style_cache.css')){
      $this->Tpl->Add('CSSLINKS', '<link href="./themes/new/css/style_cache.css" rel="stylesheet" type="text/css" />');
    }
    else{
      $this->Tpl->Add('CSSLINKS', '<link href="./index.php?module=welcome&action=css&file=style.css&submodule=[MODULE]&subaction=[ACTION]&v=2.2" rel="stylesheet" type="text/css" />');
    }

    if(($module!=='welcome' && $action!=='start') && ($module!=='kalender' && $action!=='list') && is_file('./themes/new/css/popup_cache.css')){
      $this->Tpl->Set('CSSLINKSPOPUP', '<link href="./themes/new/css/popup_cache.css" rel="stylesheet" type="text/css" />');
    }
    else{
      $this->Tpl->Set('CSSLINKSPOPUP', '<link href="./index.php?module=welcome&action=css&file=popup.css&submodule=[MODULE]&subaction=[ACTION]" rel="stylesheet" type="text/css" />');
    }

    if(is_file('./themes/new/css/custom.css')){
      $this->Tpl->Set('FINALCSSLINKS', '<link href="./themes/new/css/custom.css" rel="stylesheet" type="text/css" />');
    }

    if(is_file('./js/custom.js')){
      $this->Tpl->Add('CSSLINKS', '<script type="text/javascript" language="javascript" src="./js/custom.js"></script>');
    }


    if(is_file('./themes/new/css/custom_popup.css')){
      $this->Tpl->Set('FINALCSSLINKSPOPUP', '<link href="./themes/new/css/custom_popup.css" rel="stylesheet" type="text/css" />');
    }

    if(!empty($this->Conf->WFtestmode) && $this->Conf->WFtestmode==true)
    {
      $this->Tpl->Set('TPLLOGOFIRMA','./themes/new/images/xentral_logo_testmode.png');
    } elseif(is_file('./themes/new/images/logo_cache.png')){
        $this->Tpl->Set('TPLLOGOFIRMA', './themes/new/images/logo_cache.png');
    }elseif($this->erp->Firmendaten('firmenlogoaktiv')!='1')
    {
      $this->Tpl->Set('TPLLOGOFIRMA', './themes/new/images/xentral_logo.svg');
    }else{
      $this->Tpl->Set('TPLLOGOFIRMA', './index.php?module=welcome&action=logo');
    }
    if($this->erp->Firmendaten('iconset_dunkel')!='1')
    {
      $this->Tpl->Set('HAMBURGERICON','menue_hgr.png');
      $this->Tpl->Set('HAMBURGERICONALT','menue_gr.png');
    }else{
      $this->Tpl->Set('HAMBURGERICON','menue_gr.png');
      $this->Tpl->Set('HAMBURGERICONALT','menue_gr.png');
    }
    $this->Tpl->Set('TPLNAVIGATIONFARBE',$navigationfarbe);
    $this->Tpl->Set('TPLNAVIGATIONFARBE2',$navigationfarbe2);
    $this->Tpl->Set('TPLNAVIGATIONFARBESCHRIFT',$navigationfarbeschrift);
    $this->Tpl->Set('TPLNAVIGATIONFARBESCHRIFT2',$navigationfarbeschrift2);
    $this->Tpl->Set('TPLUNTERNAVIGATIONFARBE',$this->erp->$tmpfirmendatenfkt('unternavigationfarbe'));
    $this->Tpl->Set('TPLUNTERNAVIGATIONFARBESCHRIFT',$this->erp->$tmpfirmendatenfkt('unternavigationfarbeschrift'));

    $bordertabnav = 0;
    if($this->erp->Firmendaten('bordertabnav')){
      $bordertabnav = 1;
    }
    $this->Tpl->Set('TPLBORDERTABNAV',$bordertabnav);
  }

  /**
   * Draw Warning and Info-Boxes
   */
  public function HeaderBoxen()
  {
    $themeheader = '';

    if($this->User->GetType() === 'admin') {
      if(method_exists($this->erp,'setSystemHealth')) {
        $letzteraufruf = $this->erp->GetKonfiguration('prozessstarter_letzteraufruf');
        $diff = time() - strtotime($letzteraufruf);
        $lastRunning = date('d.m.Y H:i:s', strtotime($letzteraufruf));
        if($diff > 60 * 5 + 1) // mit sicherheitsabstand :-)
        {
          $status = 'warning';
        }
        else{
          $status = 'ok';
        }
        $this->erp->setSystemHealth(
          'cronjobs', 'lastrunning', $status, 'Letzte Ausf&uuml;hrung: ' . $lastRunning
        );
      }

      if($this->erp->GetKonfiguration('eproosystem_skipcheckuserdata') != '1') {
        $time = microtime(true);
        $this->CheckUserdata();
        if(microtime(true) - $time > 5) {
          $this->erp->SetKonfigurationValue('eproosystem_skipcheckuserdata', '1');
        }
      }
      if(!$this->erp->ServerOK()) {
        $serverlist = $this->erp->GetIoncubeServerList();
        if(method_exists($this->erp, 'setSystemHealth')) {
          $this->erp->setSystemHealth(
            'server',
            'ioncube',
            'error',
            'Die Ioncube-Lizenz ist nur g&uuml;ltig f&uuml;r folgene'.
            (count($serverlist) == 1?'n':'').' Server: '.implode(', ',$serverlist)
          );
        }
      }
      else {
        $expDays = erpAPI::Ioncube_ExpireInDays();
        $testLicence = erpAPI::Ioncube_Property('testlizenz');
        if(!$testLicence && $expDays !== false && $expDays < 14) {
          $this->erp->setSystemHealth(
            'server',
            'ioncube',
            'error',
            sprintf(
              'Die Lizenz am %s aus.',
              erpAPI::Ioncube_ExpireDate()
            )
          );
        }
        else{
          $this->erp->setSystemHealth(
            'server',
            'ioncube',
            'ok',
            ''
          );
        }
      }
      if ($this->ModuleScriptCache->IsCacheDirWritable() === false) {
        $this->erp->setSystemHealth(
          'server',
          'cache',
          'error',
          'Cache-Verzeichnis ist nicht beschreibbar! Bitte <code>/www/cache/</code> beschreibbar machen.</div >'
        );
      }
      else {
        $this->erp->setSystemHealth(
          'server',
          'cache',
          'ok',
          'Cache-Verzeichnis ist beschreibbar.'
        );
      }
    }

    $this->Tpl->Add('THEMEHEADER', $themeheader);
    $doppeltenummerncheckCronjob = $this->DB->Select(
      "SELECT id FROM prozessstarter WHERE parameter = 'doppeltenummerncheck' AND aktiv = 1 LIMIT 1"
    );
    if(!$doppeltenummerncheckCronjob
      && $this->erp->RechteVorhanden('mhdwarning','list') && $this->erp->Firmendaten('modul_mhd')=='1') {
      $checkmhd = $this->DB->SelectArrCache(
        'SELECT ROUND(SUM(menge),0) 
        FROM lager_mindesthaltbarkeitsdatum 
        WHERE DATEDIFF(NOW(),mhddatum) > 0',
        $doppeltenummerncheckCronjob?86400:300,
        'mhdwarning'
      );
      if(!empty($checkmhd)) {
        $checkmhd = reset($checkmhd);
        if(!empty($checkmhd)) {
          $checkmhd = reset($checkmhd);
        }
      }
      $checkmhd = round($checkmhd);

      $checkmhdwarnung = $this->DB->SelectArrCache(
        'SELECT ROUND(SUM(menge),0) 
         FROM lager_mindesthaltbarkeitsdatum 
         WHERE DATEDIFF(NOW(),mhddatum) + '.($this->erp->Firmendaten('mhd_warnung_tage')+1).' > 0',
        $doppeltenummerncheckCronjob?86400:3600,
        'mhdwarning'
      );
      if(!empty($checkmhdwarnung)) {
        $checkmhdwarnung = reset($checkmhdwarnung);
        if(!empty($checkmhdwarnung)) {
          $checkmhdwarnung = reset($checkmhdwarnung);
        }
      }
      $checkmhdwarnung = round($checkmhdwarnung);

      $checkmhdwarnung -= $checkmhd;

      if($checkmhd > 0 || $checkmhdwarnung  > 0) {
        $this->erp->SetKonfigurationValue('eproosystem_mhdwarning', 1);

        $module = $this->Secure->GetGET('module');
        if(!$doppeltenummerncheckCronjob) {
          if($module === 'lager' || $module === 'mhdwarning' || $module === 'lagermobil'){
            if($this->erp->GetKonfiguration('eproosystem_mhdwarning')){
              $this->erp->ClearSqlCache('mhdwarning', 120);
            }else{
              $this->erp->ClearSqlCache('mhdwarning', 600);
            }
          }
        }
        if($checkmhd <=0) {
          $checkmhd=0;
        }
        if($checkmhd==1) {
          $ist = 'ist';
        }
        else {
          $ist='sind';
        }
        $type = 'warning';
        $link = '<a href="index.php?module=mhdwarning&action=list">Pr&uuml;fen</a>';
        if($checkmhdwarnung) {
          $text="$checkmhdwarnung Artikel laufen bald ab.";
        }
        if($checkmhd) {
          $type = 'error';
          $text2="$checkmhd Artikel $ist abgelaufen!";
        }
        if(!empty($text) && !empty($text2)) {
          $text_out = $text.'<br>'.$text2.' '.$link;
        }
        elseif(!empty($text) &&  empty($text2)) {
          $text_out = $text.' '.$link;
        }
        else {
          $text_out = $text2.' '.$link;
        }
        if(method_exists($this->erp,'setSystemHealth')){
          $this->erp->setSystemHealth(
            'bestbeforebatchsn', 'bestbefore', $type,
            '<b>Mindesthaltbarkeitsdatum:</b> ' . $text_out
          );
        }
      }
      else {
        if(method_exists($this->erp,'setSystemHealth')) {
          $this->erp->setSystemHealth('bestbeforebatchsn', 'bestbefore', 'ok');
        }
        $this->erp->SetKonfigurationValue('eproosystem_mhdwarning', 0);
      }
    }

    if($this->erp->Firmendaten('warnung_doppelte_nummern')=='1') {
      if(method_exists($this->erp, 'ClearSqlCache')
        && $this->Secure->GetGET('action') === 'edit'
        && !$doppeltenummerncheckCronjob
      ) {
        $module = $this->Secure->GetGET('module');
        switch($module) {
          case 'artikel':
          case 'rechnung':
          case 'gutschrift':
          case 'adresse':
            if($this->erp->GetKonfiguration('eproosystem_'.$module)) {
              $this->erp->ClearSqlCache($module, 60);
            }
            else {
              $this->erp->ClearSqlCache($module, 120);
            }
          break;
        }
      }

      $link = '<a href="index.php?module=doppelte_nummern&action=list" target="_blank">';

      $belege = '';
      $gutschrift_check = 0;
      $rechnung_check = 0;
      $kundennummer_check = 0;
      if(!$doppeltenummerncheckCronjob){
        $check_double_doppeltekundennummer = $this->DB->SelectArrCache(
          "SELECT adr.kundennummer,count(adr.id) as NumOccurrences 
        FROM adresse adr 
        LEFT JOIN projekt pr ON adr.projekt = pr.id 
        WHERE adr.geloescht = 0 AND (adr.projekt = 0 OR pr.eigenernummernkreis = 0) AND adr.kundennummer <> '' 
        GROUP BY adr.kundennummer 
        HAVING COUNT(adr.kundennummer) > 1 
        LIMIT 100",
          $doppeltenummerncheckCronjob ? 86400 : 300,
          'adresse'
        );
        if($check_double_doppeltekundennummer && count($check_double_doppeltekundennummer)>0) {
          $this->erp->SetKonfigurationValue('eproosystem_adresse', 1);
          $ccheck_double_doppeltekundennummer = count($check_double_doppeltekundennummer);
          for($icheck=0;$icheck<$ccheck_double_doppeltekundennummer;$icheck++) {
            $belege .= ' ' . $check_double_doppeltekundennummer[$icheck]['kundennummer'];
          }

          $gesamt_gutschrift= count($check_double_doppeltekundennummer);
          if(method_exists($this->erp,'setSystemHealth')) {
            $this->erp->setSystemHealth('masterdata', 'double_customernumber', 'error',
              $link.'<b>Achtung: Doppelte Kundennummern!</b> (Gesamt '.
              $gesamt_gutschrift.') <span title="Kundennummern: '.$belege.'">*</span>'.
              ($link?'</a>':'')
            );
          }
          $kundennummer_check=1;
        }
        else {
          $this->erp->SetKonfigurationValue('eproosystem_adresse', 0);
          if(method_exists($this->erp,'setSystemHealth')) {
            $this->erp->setSystemHealth('masterdata', 'double_customernumber', 'ok');
          }
        }
      }
      if(!$doppeltenummerncheckCronjob){
        $check_double_gutschrift = $this->DB->SelectArrCache(
          "SELECT b.belegnr, COUNT(b.belegnr) AS NumOccurrences 
        FROM gutschrift b 
        LEFT JOIN projekt pr ON b.projekt = pr.id 
        WHERE b.status!='angelegt' AND b.belegnr <> '' 
        GROUP BY b.belegnr, if(ifnull(pr.eigenernummernkreis,0) = 0,0,pr.id) 
        HAVING ( COUNT(b.belegnr) > 1 )",
          $doppeltenummerncheckCronjob ? 86400 : 600,
          'gutschrift'
        );

        if($check_double_gutschrift && count($check_double_gutschrift) > 0){
          $this->erp->SetKonfigurationValue('eproosystem_gutschrift', 1);
          $ccheck_double_gutschrift = count($check_double_gutschrift);
          for ($icheck = 0; $icheck < $ccheck_double_gutschrift; $icheck++) {
            $belege .= ' ' . $check_double_gutschrift[$icheck]['belegnr'];
          }

          if(trim($belege) == ''){
            $belege = 'ohne Nummer';
          }

          $gesamt_gutschrift = count($check_double_gutschrift);

          if(method_exists($this->erp, 'setSystemHealth')){
            $this->erp->setSystemHealth(
              'masterdata', 'double_return_order', 'error',
              $link . '<b>Achtung: Doppelte Gutschriftsnummern!</b> (Gesamt ' .
              $gesamt_gutschrift . ') <span title="Belege: ' . $belege . '">*</span>' .
              ($link ? '</a>' : '')
            );
          }
          $gutschrift_check = 1;
        }else{
          if(method_exists($this->erp, 'setSystemHealth')){
            $this->erp->setSystemHealth('masterdata', 'double_return_order', 'ok', '');
          }
          $this->erp->SetKonfigurationValue('eproosystem_gutschrift', 0);
        }
        $check_double_gutschrift = null;

        $check_double_rechnungen = $this->DB->SelectArrCache(
          "SELECT b.belegnr, COUNT(b.belegnr) AS NumOccurrences 
         FROM rechnung b 
         LEFT JOIN projekt pr ON b.projekt = pr.id WHERE b.status!='angelegt' AND b.belegnr <> '' 
         GROUP BY b.belegnr, if(ifnull(pr.eigenernummernkreis,0) = 0,0,pr.id) 
         HAVING ( COUNT(b.belegnr) > 1 )",
          $doppeltenummerncheckCronjob ? 86400 : 120,
          'rechnung'
        );
        if($check_double_rechnungen && count($check_double_rechnungen) > 0){
          $this->erp->SetKonfigurationValue('eproosystem_rechnung', 1);
          $gesamt_rechnungen = count($check_double_rechnungen);
          for ($icheck = 0; $icheck < $gesamt_rechnungen; $icheck++) {
            $belege .= ' ' . $check_double_rechnungen[$icheck]['belegnr'];
          }

          if(trim($belege) === ''){
            $belege = 'ohne Nummer';
          }
          if(method_exists($this->erp, 'setSystemHealth')){
            $this->erp->setSystemHealth(
              'masterdata', 'double_invoice', 'error',
              $link . '<b>Achtung: Doppelte Rechnungsnummern!</b>  (Gesamt ' .
              $gesamt_rechnungen . ') <span title="Belege: ' . $belege . '">*</span></div>' .
              ($link ? '</a>' : '')
            );
          }

          $rechnung_check = 1;
        }else{
          $this->erp->SetKonfigurationValue('eproosystem_rechnung', 0);
          if(method_exists($this->erp, 'setSystemHealth')){
            $this->erp->setSystemHealth('masterdata', 'double_invoice', 'ok', '');
          }
        }
        $check_double_rechnungen = null;

        if($this->DB->SelectArrCache("SELECT id FROM artikel WHERE nummer = '' AND ifnull(geloescht,0) = 0 LIMIT 1", 120, 'artikel')){
          if(method_exists($this->erp, 'setSystemHealth')){
            $this->erp->setSystemHealth('masterdata', 'empty_articlenumber', 'error', 'Achtung Es existieren Artikel ohne Artikelnummer');
          }
        }else{
          if(method_exists($this->erp, 'setSystemHealth')){
            $this->erp->setSystemHealth('masterdata', 'empty_articlenumber', 'ok', '');
          }
        }

        $check_double_artikel = $this->DB->SelectArrCache(
          "SELECT art.nummer, count(art.nummer) as NumOccurrences 
        FROM artikel art 
        LEFT JOIN projekt pr ON art.projekt = pr.id 
        WHERE art.geloescht <> '1' AND art.nummer <> '' AND art.nummer <> 'DEL' 
        GROUP BY art.nummer,if(ifnull(pr.eigenernummernkreis,0) = 0,0,pr.id) 
        HAVING (COUNT(art.nummer) > 1) 
        LIMIT 101",
          $doppeltenummerncheckCronjob ? 86400 : 600,
          'artikel'
        );

        if(!empty($check_double_artikel) && count($check_double_artikel) > 0){
          $this->erp->SetKonfigurationValue('eproosystem_artikel', 1);
          $gesamt_artikel = count($check_double_artikel);
          $gcount = $gesamt_artikel;
          if($gcount > 10){
            $gcount = 10;
          }
          for ($icheck = 0; $icheck < $gcount; $icheck++) {
            $belege .= ' ' . $check_double_artikel[$icheck]['nummer'];
          }
          if($gesamt_artikel > $gcount){
            $belege .= ' ...';
          }

          if(method_exists($this->erp, 'setSystemHealth')){
            $this->erp->setSystemHealth('masterdata', 'double_articlenumber', 'error',
              $link . '<b>Achtung: Doppelte Artikelnummern!</b> (Gesamt ' .
              $gesamt_artikel . ') <span title="Artikelnummern: ' . $belege . '">*</span>' .
              ($link ? '</a>' : '')
            );
          }
        }else{
          $this->erp->SetKonfigurationValue('eproosystem_artikel', 0);
          if(method_exists($this->erp, 'setSystemHealth')){
            $this->erp->setSystemHealth('masterdata', 'double_articlenumber', 'ok', '');
          }
        }
      }
    }
      
    if(!$doppeltenummerncheckCronjob && $this->erp->Firmendaten('warnung_doppelte_seriennummern')=='1') {
      $sql = "SELECT  art.id,art.nummer,
      s.seriennummer, s.anzahl
      FROM artikel art INNER JOIN (
          (
              SELECT artikel, seriennummer, count(id) as anzahl, 0 as lieferschein, '' as belegnr FROM lager_seriennummern WHERE seriennummer <> '' 
              GROUP BY seriennummer, artikel HAVING  count(id) > 1
          )
          UNION ALL 
          (
              SELECT t3.artikel, t3.wert, sum(t3.anzahl) ,max(t3.lieferschein), max(t3.belegnr) FROM 
              (
              (
              SELECT lp.artikel, s.wert , count(s.id) as anzahl, max(l.id) as lieferschein, max(l.belegnr) as belegnr
              FROM `beleg_chargesnmhd` s 
              INNER JOIN lieferschein_position lp ON s.doctype = 'lieferschein' AND s.pos = lp.id AND s.type = 'sn' AND s.wert <> ''
              INNER JOIN lieferschein l ON lp.lieferschein = l.id AND l.status <> 'storniert'
              WHERE l.id NOT IN (SELECT lieferscheinid FROM retoure LIMIT 1)
              GROUP BY s.wert, lp.artikel 
              )
              UNION ALL (


              SELECT lp.artikel, s.seriennummer as wert , count(s.id) as anzahl, max(l.id) as lieferschein, max(l.belegnr) as belegnr
              FROM `seriennummern` s 
              INNER JOIN lieferschein_position lp ON s.lieferscheinpos = lp.id 
              INNER JOIN lieferschein l ON lp.lieferschein = l.id WHERE s.seriennummer <> '' AND l.id NOT IN (SELECT lieferscheinid FROM retoure LIMIT 1)
              GROUP BY s.seriennummer, lp.artikel 


              )) t3 GROUP BY t3.artikel, t3.wert HAVING sum(t3.anzahl) > 1
           )   
      ) s ON art.id = s.artikel  GROUP BY  art.id, s.seriennummer HAVING max(anzahl) > 1  ";
      $doppeltebelege = $this->DB->SelectArrCache($sql,$doppeltenummerncheckCronjob?86400:300,'artikel');

      if(!empty($doppeltebelege)) {
        $this->erp->SetKonfigurationValue('eproosystem_artikel', 1);
        $gesamt_artikel = 0;
        $gcount = 0;

        $gesamt_artikel2 = count($doppeltebelege);
        if($gcount < 10) {
          $gcount2 = $gesamt_artikel2;
          if($gesamt_artikel2 + $gcount > 10){
            $gcount2 = 10 - $gcount;
          }
          for($icheck=0;$icheck<$gcount2;$icheck++) {
            if(!isset($arts)){
              $arts = '';
            }
            $arts .=' '.$doppeltebelege[$icheck]['nummer'].':'.$doppeltebelege[$icheck]['seriennummer'];
          }
        }

        $link = '';
        if($this->erp->RechteVorhanden('doppelte_nummern','list')) {
          $link = '<a href="index.php?module=doppelte_nummern&action=list#tabs-2" target="_blank">';
        }

        if(method_exists($this->erp,'setSystemHealth')){
          $this->erp->setSystemHealth(
            'bestbeforebatchsn', 'sn', 'error',
            $link.'<b>Achtung: Doppelte Seriennummern!</b>  (Gesamt '.
            ($gesamt_artikel+$gesamt_artikel2).')'.($link?'</a>':'')
          );
        }
      }
      else {
        if(method_exists($this->erp,'setSystemHealth')){
          $this->erp->setSystemHealth('bestbeforebatchsn', 'sn', 'ok', '');
        }
      }
    }

    if($this->erp instanceof erpAPICustom
      && $this->User->GetType() === 'admin'
      && method_exists('erpAPICustom', 'AllowedVersion')) {
      try {
        $allowedmethod = new ReflectionMethod('erpAPICustom', 'AllowedVersion');
        if($allowedmethod->isStatic()){
          $allowed = erpAPICustom::AllowedVersion();
          $version_revision = null;
          include dirname(__DIR__) . '/version.php';
          if(isset($version_revision) && ((isset($allowed['max']) && ((float)$allowed['max'] < (float)$version_revision))
              ||
              (isset($allowed['versionen']) && (
                  (is_array($allowed['versionen']) && !in_array($version_revision, $allowed['versionen']))
                  || (!is_array($allowed['versionen']) && $allowed['versionen'] != $version_revision)
                ))

            )){
            $this->Tpl->Add('THEMEHEADER', '<div class="headererror">Die Datei class.erpapi_custom.php auf Ihrem System, ist nicht f&uuml;r Ihre Version geeignet</div>');
          }
        }
      }
      catch (Exception $e) {

      }
    }
    if($this->Secure->GetPOST('deactivate_maintenance')) {
      $tags = json_encode('update');
      $this->DB->Delete("DELETE FROM notification_message WHERE tags = '$tags'");
      $this->erp->SetKonfigurationValue('update_maintenance',0);
    }

    $anznachrichtenboxen = 0;
    $this->erp->RunHook('eproosystem_iconboxes_start', 1, $anznachrichtenboxen);

    if($this->erp->ModulVorhanden('chat') && $this->erp->RechteVorhanden('chat','list')) {
      $userId = $this->User->GetID();
      $registrierDatum = $this->DB->Select("SELECT u.logdatei FROM `user` AS u WHERE u.id='".$userId."'");

      $ungelesenOeffentlich = (int)$this->DB->Select(
        "SELECT COUNT(c.id) 
          FROM chat AS c 
          LEFT JOIN chat_gelesen AS g ON c.id = g.message AND (g.user = '".$userId."' OR g.user = 0)
          WHERE c.user_to='0' AND c.zeitstempel > '".$registrierDatum."' 
          AND g.id IS NULL"
      );
      $ungelesenPrivat = (int)$this->DB->Select(
        "SELECT COUNT(c.id) 
          FROM chat AS c
          INNER JOIN `user` AS u ON c.user_from = u.id 
          LEFT JOIN chat_gelesen AS g ON c.id = g.message  
          WHERE u.activ = 1 AND c.user_to='".$userId."' 
          AND g.id IS NULL"
      );
      $anzchat = $ungelesenOeffentlich + $ungelesenPrivat;
      $this->Tpl->Set('CHATNACHRICHTENBOXCOUNTER',$anzchat > 0?$anzchat:'');

      if($this->DB->Select("SELECT chat_popup FROM `user` WHERE id = '".$userId."' LIMIT 1")) {
        $this->Tpl->Set('CHATLINK','href="index.php?module=chat&action=list" target="_blank" ');
      }
      else {
        $this->Tpl->Set('CHATLINK','href="index.php?module=chat&action=list"');
      }
    }
    else {
      $this->Tpl->Set('VORCHATNACHRICHTENBOX','<!--');
      $this->Tpl->Set('NACHCHATNACHRICHTENBOX','-->');
    }

    if(!empty(erpAPI::Ioncube_Property('testlizenz'))){
        $upgradeButton = '<li id="upgrade-licence"><a href="./index.php?module=appstore&action=buy">'.
            '<svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M4.47287 12.0104C2.04566 9.80074 1.66708 6.11981 3.59372 3.46237C5.52036 0.804943 9.13654 0.0202146 11.9914 1.64005" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M2.21273 11.9649C1.39377 13.3996 1.11966 14.513 1.58214 14.9761C2.2843 15.6776 4.48124 14.6858 7.02522 12.6684" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M9.93719 12.1581L7.52014 9.74109L12.8923 4.3689C13.3305 3.93091 13.8797 3.62049 14.481 3.47095L15.863 3.12392C16.0571 3.07558 16.2623 3.1325 16.4037 3.27392C16.5451 3.41534 16.602 3.62054 16.5537 3.8146L16.208 5.19732C16.0578 5.7984 15.7469 6.34731 15.3087 6.78527L9.93719 12.1581Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M7.51976 9.7409L5.54021 9.08128C5.44619 9.05019 5.37505 8.97252 5.35233 8.87613C5.32961 8.77974 5.35857 8.67847 5.42881 8.60867L6.11882 7.91866C6.7306 7.30697 7.63548 7.09343 8.45619 7.36706L9.53644 7.72625L7.51976 9.7409Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M9.93713 12.1584L10.5968 14.1386C10.6278 14.2326 10.7055 14.3038 10.8019 14.3265C10.8983 14.3492 10.9996 14.3203 11.0694 14.25L11.7594 13.56C12.3711 12.9482 12.5846 12.0434 12.311 11.2226L11.9518 10.1424L9.93713 12.1584Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
'.
            '<span>Upgrade</span></a></li>';

        $this->Tpl->Set('UPGRADELICENCECTA', $upgradeButton);
    }

    if(!$this->erp->ModulVorhanden('aufgaben') || !$this->erp->RechteVorhanden('aufgaben','list')) {
      $this->Tpl->Set('AUFGABENVOR','<!--');
      $this->Tpl->Set('AUFGABENNACH','-->');
    }
    else {
      $anznachrichtenboxen++;
    }
    if($this->erp->ModulVorhanden('ticket') || $this->erp->RechteVorhanden('ticket','offene')) {
      $anznachrichtenboxen++;
    }
    if(!$this->erp->ModulVorhanden('telefonrueckruf') || !$this->erp->RechteVorhanden('telefonrueckruf','list')) {
      $this->Tpl->Set('TELEFONVOR','<!--');
      $this->Tpl->Set('TELEFONNACH','-->');
    }
    else {
      $anznachrichtenboxen++;
    }

    $this->erp->RunHook('eproosystem_iconboxes', 1, $anznachrichtenboxen);

    if($anznachrichtenboxen < 3) {
      if($anznachrichtenboxen == 2) {
        $this->Tpl->Set('NBBREITE','207');
        $this->Tpl->Set('NBPROZ','33');
      }
      elseif($anznachrichtenboxen == 1) {
        $this->Tpl->Set('NBBREITE','139');
        $this->Tpl->Set('NBPROZ','50');
      }
      else {
        $this->Tpl->Set('NBBREITE','71');
        $this->Tpl->Set('NBPROZ','100');
      }
    }

    $anzahltickets = $this->erp->AnzahlOffeneAufgaben();
    if($anzahltickets<=0) {
      $this->Tpl->Set('ANZAHLAUFGABEN','');
    }
    else {
      $this->Tpl->Set('ANZAHLAUFGABEN',$anzahltickets);
    }
  }


  /**
   * @param string $isocode
   * @param string $sprache
   *
   * @return string
   */
  public function GetLandLang($isocode,$sprache='')
  {
    $flipped = array_flip($this->GetLaender($sprache));
    if(isset($flipped[$isocode])){
      return $flipped[$isocode];
    }

    return '';
  }

  public function GetLaender($sprache='deutsch')
  {
    if($sprache!=='deutsch' && $sprache!=='englisch'){
      $sprache = 'deutsch';
    }
  
    if($sprache==='deutsch'){
      if(empty($this->uselaendercache) || empty($this->laendercache['deutsch'])){
        $tmp = $this->DB->SelectArr('SELECT bezeichnung_de,iso FROM laender ORDER by bezeichnung_de');
        $this->laendercache['deutsch'] = $tmp;
      }else{
        $tmp = $this->laendercache['deutsch'];
      }
    }
    elseif(empty($this->uselaendercache) || empty($this->laendercache['englisch'])){
      $tmp = $this->DB->SelectArr('SELECT bezeichnung_en,iso FROM laender ORDER by bezeichnung_en');
      $this->laendercache['englisch'] = $tmp;
    }else{
      $tmp = $this->laendercache['englisch'];
    }
    if(!empty($tmp)){
      $ctmp = count($tmp);
      $laender = [];
      for ($i = 0; $i < $ctmp; $i++) {
        switch ($sprache) {
          case 'deutsch':
            $laender[$tmp[$i]['bezeichnung_de']] = $tmp[$i]['iso'];
            break;
          case 'englisch':
            $laender[$tmp[$i]['bezeichnung_en']] = $tmp[$i]['iso'];
            break;
          default:
            $laender[$tmp[$i]['bezeichnung_de']] = $tmp[$i]['iso'];
        }
      }
      return $laender;
    }
  
    $laender = array(
        'Afghanistan'  => 'AF',
        '&Auml;gypten'  => 'EG',
        'Albanien'  => 'AL',
        'Algerien'  => 'DZ',
        'Amerikanische Jungferninseln' => 'VI',
        'Andorra'  => 'AD',
        'Angola'  => 'AO',
        'Anguilla'  => 'AI',
        'Antarktis'  => 'AQ',
        'Antigua und Barbuda'  => 'AG',
        '&Auml;quatorialguinea'  => 'GQ',
        'Argentinien'  => 'AR',
        'Armenien'  => 'AM',
        'Aruba'  => 'AW',
        'Aserbaidschan'  => 'AZ',
        '&Auml;thiopien'  => 'ET',
        'Australien'  => 'AU',
        'Bahamas'  => 'BS',
        'Bahrain'  => 'BH',
        'Bangladesch'  => 'BD',
        'Barbados'  => 'BB',
        'Belgien'  => 'BE',
        'Belize'  => 'BZ',
        'Benin'  => 'BJ',
        'Bermuda'  => 'BM',
        'Bhutan'  => 'BT',
        'Bolivien'  => 'BO',
        'Bosnien und Herzegowina'  => 'BA',
        'Botswana'  => 'BW',
        'Bouvetinsel'  => 'BV',
        'Brasilien'  => 'BR',
        'Britisch-Indischer Ozean'  => 'IO',
        'Britische Jungferninseln' => 'VG',
        'Brunei Darussalam'  => 'BN',
        'Bulgarien'  => 'BG',
        'Burkina Faso'  => 'BF',
        'Burundi'  => 'BI',
        'Chile'  => 'CL',
        'China'  => 'CN',
        'Cookinseln'  => 'CK',
        'Costa Rica'  => 'CR',
        'D&auml;nemark'  => 'DK',
        'Deutschland'  => 'DE',
        'Dominica'  => 'DM',
        'Dominikanische Republik'  => 'DO',
        'Dschibuti'  => 'DJ',
        'Ecuador'  => 'EC',
        'El Salvador'  => 'SV',
        'Elfenbeink&uuml;ste'  => 'CI',
        'Eritrea'  => 'ER',
        'Estland'  => 'EE',
        'Falklandinseln'  => 'FK',
        'F&auml;r&ouml;er Inseln'  => 'FO',
        'Fidschi'  => 'FJ',
        'Finnland'  => 'FI',
        'Frankreich'  => 'FR',
        'Franz&ouml;sisch-Guayana'  => 'GF',
        'Franz&ouml;sisch-Polynesien'  => 'PF',
        'Franz&ouml;sisches S&uuml;d-Territorium'  => 'TF',
        'Gabun'  => 'GA',
        'Gambia'  => 'GM',
        'Georgien'  => 'GE',
        'Ghana'  => 'GH',
        'Gibraltar'  => 'GI',
        'Grenada'  => 'GD',
        'Griechenland'  => 'GR',
        'Gr&ouml;nland'  => 'GL',
        'Gro&szlig;britannien'  => 'GB',
        'Guadeloupe'  => 'GP',
        'Guam'  => 'GU',
        'Guatemala'  => 'GT',
        'Guinea'  => 'GN',
        'Guinea-Bissau'  => 'GW',
        'Guyana'  => 'GY',
        'Haiti'  => 'HT',
        'Heard und McDonaldinseln'  => 'HM',
        'Honduras'  => 'HN',
        'Hongkong'  => 'HK',
        'Indien'  => 'IN',
        'Indonesien'  => 'ID',
        'Irak'  => 'IQ',
        'Iran'  => 'IR',
        'Irland'  => 'IE',
        'Island'  => 'IS',
        'Israel'  => 'IL',
        'Italien'  => 'IT',
        'Jamaika'  => 'JM',
        'Japan'  => 'JP',
        'Jemen'  => 'YE',
        'Jordanien'  => 'JO',
        'Kaimaninseln'  => 'KY',
        'Kambodscha'  => 'KH',
        'Kamerun'  => 'CM',
        'Kanada'  => 'CA',
        'Kap Verde'  => 'CV',
        'Kasachstan'  => 'KZ',
        'Katar'  => 'QA',
        'Kenia'  => 'KE',
        'Kirgisistan'  => 'KG',
        'Kiribati'  => 'KI',
        'Kokosinseln'  => 'CC',
        'Kolumbien'  => 'CO',
        'Komoren'  => 'KM',
        'Kongo'  => 'CG',
        'Kongo, Demokratische Republik'  => 'CD',
        'Kroatien'  => 'HR',
        'Kuba'  => 'CU',
        'Kuwait'  => 'KW',
        'Laos'  => 'LA',
        'Lesotho'  => 'LS',
        'Lettland'  => 'LV',
        'Libanon'  => 'LB',
        'Liberia'  => 'LR',
        'Libyen'  => 'LY',
        'Liechtenstein'  => 'LI',
        'Litauen'  => 'LT',
        'Luxemburg'  => 'LU',
        'Macau'  => 'MO',
        'Madagaskar'  => 'MG',
        'Malawi'  => 'MW',
        'Malaysia'  => 'MY',
        'Malediven'  => 'MV',
        'Mali'  => 'ML',
        'Malta'  => 'MT',
        'Marianen'  => 'MP',
        'Marokko'  => 'MA',
        'Marshallinseln'  => 'MH',
        'Martinique'  => 'MQ',
        'Mauretanien'  => 'MR',
        'Mauritius'  => 'MU',
        'Mayotte'  => 'YT',
        'Mazedonien'  => 'MK',
        'Mexiko'  => 'MX',
        'Mikronesien'  => 'FM',
        'Moldawien'  => 'MD',
        'Monaco'  => 'MC',
        'Mongolei'  => 'MN',
        'Montenegro'  => 'ME',
        'Montserrat'  => 'MS',
        'Mosambik'  => 'MZ',
        'Myanmar' => 'MM',
        'Namibia'  => 'NA',
        'Nauru'  => 'NR',
        'Nepal'  => 'NP',
        'Neukaledonien'  => 'NC',
        'Neuseeland'  => 'NZ',
        'Nicaragua'  => 'NI',
        'Niederlande'  => 'NL',
        'Niger'  => 'NE',
        'Nigeria'  => 'NG',
        'Niue'  => 'NU',
        'Nordkorea'  => 'KP',
        'Norfolkinsel'  => 'NF',
        'Norwegen'  => 'NO',
        'Oman'  => 'OM',
        '&Ouml;sterreich'  => 'AT',
        'Pakistan'  => 'PK',
        'Pal&auml;stina'  => 'PS',
        'Palau'  => 'PW',
        'Panama'  => 'PA',
        'Papua-Neuguinea'  => 'PG',
        'Paraguay'  => 'PY',
        'Peru'  => 'PE',
        'Philippinen'  => 'PH',
        'Pitcairninseln'  => 'PN',
        'Polen'  => 'PL',
        'Portugal'  => 'PT',
        'Puerto Rico'  => 'PR',
        'Réunion'  => 'RE',
        'Ruanda'  => 'RW',
        'Rum&auml;nien'  => 'RO',
        'Russland'  => 'RU',
        'Salomonen'  => 'SB',
        'Sambia'  => 'ZM',
        'Samoa, amerikanisch'  => 'AS',
        'Samoa'  => 'WS',
        'San Marino'  => 'SM',
        'São Tomé und Príncipe'  => 'ST',
        'Saudi-Arabien'  => 'SA',
        'Schweden'  => 'SE',
        'Schweiz'  => 'CH',
        'Senegal'  => 'SN',
        'Serbien'  => 'RS',
        'Seychellen'  => 'SC',
        'Sierra Leone'  => 'SL',
        'Simbabwe' => 'ZW',
        'Singapur'  => 'SG',
        'Slowakei'  => 'SK',
        'Slowenien'  => 'SI',
        'Somalia'  => 'SO',
        'S&uuml;dgeorgien, s&uuml;dliche Sandwichinseln'  => 'GS',
        'Spanien'  => 'ES',
        'Sri Lanka'  => 'LK',
        'St. Helena'  => 'SH',
        'St. Kitts und Nevis'  => 'KN',
        'St. Lucia'  => 'LC',
        'St. Pierre und Miquelon'  => 'PM',
        'St. Vincent und die Grenadinen'  => 'VC',
        'S&uuml;dkorea'  => 'KR',
        'S&uuml;dafrika'  => 'ZA',
        'Sudan'  => 'SD',
        'Suriname'  => 'SR',
        'Svalbard und Jan Mayen'  => 'SJ',
        'Swasiland'  => 'SZ',
        'Syrien'  => 'SY',
        'Tadschikistan'  => 'TJ',
        'Taiwan'  => 'TW',
        'Tansania'  => 'TZ',
        'Thailand'  => 'TH',
        'Togo'  => 'TG',
        'Tokelau'  => 'TK',
        'Tonga'  => 'TO',
        'Trinidad und Tobago'  => 'TT',
        'Tschad'  => 'TD',
        'Tschechien'  => 'CZ',
        'Tunesien'  => 'TN',
        'T&uuml;rkei'  => 'TR',
        'Turkmenistan'  => 'TM',
        'Turks- und Caicosinseln'  => 'TC',
        'Tuvalu'  => 'TV',
        'Uganda'  => 'UG',
        'Ukraine'  => 'UA',
        'Ungarn'  => 'HU',
        'Uruguay'  => 'UY',
        'Usbekistan'  => 'UZ',
        'Vanuatu'  => 'VU',
        'Vatikanstadt'  => 'VA',
        'Venezuela'  => 'VE',
        'Vereinigte Arabische Emirate'  => 'AE',
        'Vereinigtes Königreich' => 'UK',
        'Vereinigte Staaten von Amerika'  => 'US',
        'Vietnam'  => 'VN',
        'Wallis und Futuna'  => 'WF',
        'Weihnachtsinsel' => 'CX',
        'Wei&szlig;russland'  => 'BY',
        'Westsahara'  => 'EH',
        'Zentralafrikanische Republik'  => 'CF',
        'Zypern'  => 'CY'
          );
    return $laender;
  }


  public function SelectLaenderliste($selected='')
  {
    if(empty($selected)) {
      $selected=$this->erp->Firmendaten('land');
    }
    if(empty($selected)) {
      $selected='DE';
    }
    $options = '';
    $laender = $this->GetLaender();
    foreach ($laender as $land => $kuerzel) {
      $options .= '<option value="'.$kuerzel.'"';
      if ($selected === $kuerzel) {
        $options .= ' selected';
      }
      $options .= '>'.$land."</option>\n";
    }
    return $options;
  }
}

