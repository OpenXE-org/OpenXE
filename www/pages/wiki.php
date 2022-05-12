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
use \Xentral\Modules\SystemNotification\Service\NotificationService;



class Wiki {
  /** @var ApplicationCore $app */
  var $app;

  const MODULE_NAME = 'Wiki';

  /**
   * @param ApplicationCore $app
   * @param bool            $intern
   */
  public function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern){
      return;
    }

    $this->app->ActionHandlerInit($this);
    $this->app->DB->DisableHTMLClearing(true);
    $this->app->ActionHandler("create","WikiCreate");
    $this->app->ActionHandler("edit","WikiEdit");
    $this->app->ActionHandler("delete","WikiDelete");
    $this->app->ActionHandler("rename","WikiRename");
    $this->app->ActionHandler("new","WikiNew");
    $this->app->ActionHandler("alle","WikiAlle");
    $this->app->ActionHandler("dateien","WikiDateien");
    $this->app->ActionHandler("list","WikiList");
    $this->app->ActionHandler("settings","WikiSettings");
    $this->app->ActionHandler("getfile","WikiGetFile");
    $this->app->ActionHandler("faq","WikiFaq");
    $this->app->ActionHandler("changelog","WikiChangelog");
    $this->app->ActionHandler("minidetailsites","WikiMinidetailSites");
    $this->app->ActionHandler("minidetailfaq","WikiMinidetailFaq");
    $this->app->ActionHandler("minidetailchangelog","WikiMinidetailChangelog");

    $this->app->DefaultActionHandler("list");

    $this->app->ActionHandlerListen($app);
  }

  /**
   * @return bool
   */
  public function runInstallFromJson()
  {
    ignore_user_abort(true);
    $this->app->erp->SetKonfigurationValue('wiki_install', 0);
    $workspaces = $this->getAllWorkspaces();
    $foundWorkspace = false;
    if(!empty($workspaces)) {
      foreach($workspaces as $workspace) {
        if($workspace['name'] === 'XentralHandbuch' || $workspace['foldername'] === 'XentralHandbuch') {
          $foundWorkspace = true;
          if($workspace['savein'] !== 'userdata') {
            $this->app->DB->Update(
              sprintf(
                "UPDATE wiki_workspace SET savein = 'userdata' WHERE id =%d",
                $workspace['id']
              )
            );
          }
          break;
        }
      }
    }
    if(!$foundWorkspace) {
      $this->app->DB->Insert(
        "INSERT INTO wiki_workspace (name, description, active, foldername, savein) 
        VALUE ('XentralHandbuch','',1,'XentralHandbuch','userdata')"
      );
    }
    $ret = $this->fromUserData();
    $workspace = $this->getWorkspaceByName('XentralHandbuch');
    if(!empty($workspace['id'])){
      $this->app->DB->Update(
        sprintf(
          "UPDATE `datei_stichwoerter` AS `ds` 
          INNER JOIN `wiki` AS `w` ON `ds`.objekt LIKE 'Wiki' AND ds.parameter = w.id
          SET ds.parameter2 = %d WHERE w.wiki_workspace_id = %d",
          $workspace['id'],$workspace['id']
        )
      );
    }
    return $ret;
  }

  public function Install()
  {
    $this->app->erp->RegisterHook('inline_tooltip','wiki','WikiInlineTooltip');
    $this->app->erp->RegisterHook('appstoreModulelistToShow','wiki','WikiAppstoreModulelistToShow');
    $this->app->erp->RegisterHook('appstoreModuleDetailToShow','wiki','WikiAppstoreModuleDetailToShow');
    $this->app->erp->RegisterHook('welcome_news','wiki','WikiWelcomeNews');
    $this->app->erp->RegisterHook('dateibrowser_tablesearch_list_swhere2','wiki','WikiDateiBrowserTableSearch');
    $this->getAllWorkspaces();
    $this->runInstallFromJson();

    $this->app->erp->SetKonfigurationValue('wiki_install', 1);
  }

  /**
   * @param string $swhere2
   */
  public function WikiDateiBrowserTableSearch(&$swhere2)
  {
    $workspace = $this->getWorkspaceByName('XentralHandbuch');
    if(empty($workspace)) {
      return;
    }

    $swhere2 .= sprintf(" AND (ds.objekt NOT LIKE 'Wiki' OR ds.parameter2 <> '%d') ", $workspace['id']);
  }

  public function WikiWelcomeNews()
  {
    if(!$this->app->erp->RechteVorhanden('wiki', 'list')) {
      return;
    }
    $workspace = $this->getWorkspaceByName('XentralHandbuch');
    if(empty($workspace)) {
      return;
    }
    $version_revision = '';
    include dirname(dirname(__DIR__)).'/version.php';


    $wiki = $this->getArticleByName('changelog'.$version_revision, $workspace['id']);
    if(empty($wiki)) {
      return;
    }
    $this->app->Tpl->Add(
      'UPDATEBUTTONS_HOOK1',
      '<a target="_blank" href="index.php?module=wiki&action=list&workspace='
      .$workspace['id']
      .'&cmd=changelog'.$version_revision.'" class="button button-secondary">{|Änderungen zur Version '
      .$version_revision.'|}</a>'
    );
  }

  /**
   * @param array $module
   */
  public function WikiAppstoreModuleDetailToShow(&$module)
  {
    $workspace = $this->getWorkspaceByName('XentralHandbuch');
    if(empty($workspace)) {
      return;
    }
    $wikis = $this->app->DB->SelectArr(
      sprintf(
        "SELECT w.* 
        FROM `wiki` AS `w`
        WHERE w.`wiki_workspace_id` = %d AND w.`name` = '%s'
        ORDER BY w.`name`, w.`language` = '' DESC, w.language = 'default ' DESC",
        $workspace['id'], $this->app->DB->real_escape_string($module['key'])
      )
    );
    if(empty($wikis)) {
      return;
    }

    $name = '';
    foreach($wikis as $wiki) {
      if($wiki['name'] === $name) {
        continue;
      }
      $name = $wiki['name'];
      $module['helpdesk']
        = 'index.php?module=wiki&action=list&workspace='.$workspace['id'].'&cmd='.$name;
    }
  }

  /**
   * @param array $modules
   */
  public function WikiAppstoreModulelistToShow(&$modules)
  {
    $workspace = $this->getWorkspaceByName('XentralHandbuch');
    if(empty($workspace)) {
      return;
    }

    $modulList = [];
    $nameToModule = [];

    if(!empty($modules['kauf'])) {
      foreach($modules['kauf'] as $key => $module) {
        if(!empty($module['key']) && $module['key'] !== 'appstore_extern') {
          $modulList[] = $this->app->DB->real_escape_string($module['key']);
          $nameToModule[$module['key']] = ['kauf', $key];
        }
      }
    }
    if(!empty($modules['installiert'])) {
      foreach($modules['installiert'] as $key => $module) {
        if(!empty($module['key']) && $module['key'] !== 'appstore_extern') {
          $modulList[] = $this->app->DB->real_escape_string($module['key']);
          $nameToModule[$module['key']] = ['installiert', $key];
        }
      }
    }
    $wikis = $this->app->DB->SelectArr(
      sprintf(
        "SELECT w.* 
        FROM `wiki` AS `w`
        WHERE w.`wiki_workspace_id` = %d AND w.`name` IN ('%s')
        ORDER BY w.`name`, w.`language` = '' DESC, w.language = 'default ' DESC",
        $workspace['id'], implode("','", $modulList)
      )
    );
    if(empty($wikis)) {
      return;
    }

    $name = '';
    foreach($wikis as $wiki) {
      if($wiki['name'] === $name) {
        continue;
      }
      $name = $wiki['name'];
      if(empty($nameToModule[$name])) {
        continue;
      }
      $modules[$nameToModule[$name][0]][$nameToModule[$name][1]]['helpdesk']
        = 'index.php?module=wiki&action=list&workspace='.$workspace['id'].'&cmd='.$name;
    }
  }

  /**
   * @param string $folder
   */
  public function changeUserdataRights($folder = '')
  {
    if(empty($folder)) {
      $folder = $this->getUserDataFolder();
      $workspace = $this->getWorkspaceByName('XentralHandbuch');
      $folder .= $workspace['foldername'];
    }
    if(!is_dir($folder) && !@mkdir($folder,0777,true) && !is_dir($folder)) {
      return;
    }
    @chmod($folder, 0777);
    $handle = @opendir($folder);
    if(!$handle) {
      return;
    }
    $folders = [];
    while($entry = @readdir($handle)) {
      if(strpos($entry,'.') === 0) {
        continue;
      }
      if(is_file($folder.'/'.$entry)) {
        @chmod($folder.'/'.$entry, 0666);
      }
      if(!is_dir($folder.'/'.$entry)) {
        continue;
      }
      $folders[] = $folder.'/'.$entry;
    }
    closedir($handle);
    if(empty($folders)) {
      return;
    }
    foreach($folders as $subfolder) {
      $this->changeUserdataRights($subfolder);
    }
  }

  /**
   * @param array  $arr
   * @param string $prefix
   *
   * @return string
   */
  public function drawMenuFromArray($arr, $prefix = '')
  {
    $html = '';
    if(empty($arr)) {
      return $html;
    }

    $actlvl = 0;
    foreach($arr as $key => $row) {
      $newLvl = $row['lvl'];
      if($newLvl > $actlvl) {
        for($i = $actlvl; $i < $newLvl; $i++) {
          if($i === 0) {
            $html .= '<ul class="firstlvl">';
          }
          else {
            $html .= '<ul>';
          }
          $html .= '<li>';
        }
      }
      elseif($newLvl == $actlvl && $key > 0) {
        $html .= '</li><li>';
      }
      elseif($actlvl > $newLvl) {
        for($i = $newLvl; $i < $actlvl; $i++) {
          $html .= '</li></ul>';
        }
        $html .= '<li>';
      }
      $actlvl = $newLvl;
      $html .= '<a href="#'.($prefix===''?'':$prefix.$key).'" data-lvl="'.$newLvl.'">'.$row['text'].'</a>';
    }
    for($i = $actlvl; $i > 0; $i--) {
      $html .= '</li></ul>';
    }

    return $html.'<hr />';
  }

  /**
   * @param string $html
   * @param int    $maxLvl
   * @param string $prefix
   *
   * @return array
   */
  public function parseMenuFromHtml($html, $maxLvl = 3, $prefix = '')
  {
    $headers = [];
    $html = str_replace(
      [
        '<h1>&nbsp;</h1>',
        '<h2>&nbsp;</h2>',
        '<h3>&nbsp;</h3>',
        '<h1><br />',
        '<h2><br />',
        '<h3><br />',
        '<h1><br/>',
        '<h2><br/>',
        '<h3><br/>',
        '<h1><br>',
        '<h2><br>',
        '<h3><br>',
        '<h1></h1>',
        '<h2></h2>',
        '<h3></h3>',
      ],
      [
        ' ',
        ' ',
        ' ',
        '<h1>',
        '<h2>',
        '<h3>',
        '<h1>',
        '<h2>',
        '<h3>',
        ' ',
        ' ',
        ' ',
      ],
      $html
    );
    if(preg_match_all(
      sprintf(
        '/<h([1-%d]{1})([^>]*)>([^<]+)<\/h([1-%d]{1})>/i', $maxLvl, $maxLvl
      ),$html,$matches,PREG_OFFSET_CAPTURE)) {
      foreach($matches[0] as $i => $match) {
        $headers[] = ['lvl' => $matches[1][$i][0], 'text' => $matches[3][$i][0], 'pos' => $matches[0][$i][1]];
        if($prefix === '') {
          continue;
        }
        if(!empty($matches[2][$i][0]) && preg_match_all('/(.*)(id="([^"]+)")(.*)/', $matches[2][$i][0],$matches2,PREG_OFFSET_CAPTURE)) {
          if(!empty($matches2[2][0][0])) {
            $matches[2][$i][0] = str_replace($matches2[2][0][0], ' ', $matches[2][$i][0]);
          }
        }
        $html = str_replace(
          $match[0],
          '<h'.$matches[1][$i][0].$matches[2][$i][0].' id="'.$prefix.$i.'">'.$matches[3][$i][0].'</h'.$matches[4][$i][0].'>',
          $html
        );
      }
    }

    return [$headers, $html];
  }

  public function WikiInlineTooltip(&$inline, &$tooltip, &$module, &$action, $id, $module2)
  {
    $actModule = !empty($module2)?$module2:$module;
    if(empty($actModule)) {
      return;
    }
    $moduleOld = $module;
    $module = $actModule;
    $extraLinks = [];
    $content = '';
    list($poId, $link, $oldLink) = [null,null,null];


    $oldAction = $action;
    if(empty($inline[$actModule])) {
      $action = 'default';
      if(!empty($link)) {
        $inline[$actModule]['default']['link'] = $link;
      }
      $inline[$actModule][$oldAction]['link'] = $link;
    }
    elseif(!empty($inline[$actModule][$action])) {
      if(empty($inline[$actModule][$action]['link']) && !empty($link)) {
        $inline[$actModule][$action]['link'] = $link;
      }
    }
    elseif(!empty($inline[$actModule]['default'])) {
      $action = 'default';
      $inline[$actModule][$action] = $inline[$actModule]['default'];
      if(!empty($link)) {
        $inline[$actModule]['default']['link'] = $link;
      }
    }
    else {
      $action = 'default';
      if(!empty($link)) {
        $inline[$actModule]['default']['link'] = $link;
        $inline[$actModule][$oldAction]['link'] = $link;
      }
    }
    if(!empty($oldLink)) {
      $extraLinks[$oldLink] = $oldLinkDescription;
    }

    if(!empty($extraLinks)) {
      $inline[$actModule][$action]['extralinks3'] = $extraLinks;
      $inline[$actModule][$oldAction]['extralinks3'] = $extraLinks;
    }
    $linkContent = '';
    if(!empty($content)) {
      $inline[$actModule][$action]['extradescription'] = $content;//  '<h2>Vorschau</h2>'.$content.'<hr />';
      $inline[$actModule][$oldAction]['extradescription'] = $content;//'<h2>Vorschau</h2>'.$content.'<hr />';
      $linkContent = $content;
    }
    /** @var Wiki $wiki */
    //$wiki = $this->app->erp->LoadModul('wiki');
    $workspace = $this->getWorkspaceByName('XentralHandbuch');

    if(!empty($workspace)){

      //$this->checkWiki($actModule, $content, $wiki);
      $inline[$actModule][$action]['handbook'] = '';

      $wiki = $this->getArticleByName($actModule,$workspace['id']);
      $content = $wiki['content'];
      $menu = null;
      if(!empty($content)){
        list($menu, $content) = $this->parseMenuFromHtml($content);
      }
      if(empty($linkContent)) {
        $listLink = '';
        if(!empty($wiki)) {

          $listLink = '&nbsp;<a target="_blank" 
            href="index.php?module=wiki&action=list&workspace='.$workspace['id'].'&cmd='.$wiki['name'].'">'
            .'(Handbuch im neuen Tab &ouml;ffnen)'
            .'</a><br />';

          $linkToWiki = "index.php?module=wiki&action=list&workspace=".$workspace['id'].'&cmd='.$wiki['name']."";
          $this->app->Tpl->Add("LINKTOWIKI", $linkToWiki);
        }
        $inline[$actModule][$action]['handbook'] = '';
        $inline[$actModule][$oldAction]['handbook'] = '';
        if(!empty($menu)) {
          ##$inline[$actModule][$action]['handbook'] .= '<h2 class="inlinetabh2">Inhaltsverzeichnis</h2>'.$listLink.$this->drawMenuFromArray($menu);
          if($action !== $oldAction){
            ##$inline[$actModule][$oldAction]['handbook'] .= '<h2 class="inlinetabh2">Inhaltsverzeichnis</h2>' . $listLink . $this->drawMenuFromArray($menu);
          }
        }
        else {
          $inline[$actModule][$action]['handbook'] .= $listLink;
          if($action !== $oldAction){
            $inline[$actModule][$oldAction]['handbook'] .= $listLink;
          }
        }
        $inline[$actModule][$action]['handbook'] .=  $content . '<hr />';
        $inline[$actModule][$oldAction]['handbook'] .= $content . '<hr />';
      }
      else{
        if(!empty($menu)) {
          ##$inline[$actModule][$action]['handbook'] .= '<h2>Inhaltsverzeichnis</h2>'.$this->drawMenuFromArray($menu);
        }
        $inline[$actModule][$action]['handbook'] .=  $content . '<hr />';
      }
      if(!empty($wiki)) {
        /*$inline[$actModule][$action]['extraheading'] =
        '<div class="rTabs">
			<ul><li class="aktiv"><a class="inlinecontentlink" href="#rinhalt">Inhalt</a></li><li><a class="inlinefaqlink" href="#rfaq">FAQ</a></li></ul></div>';*/
        $inline[$actModule][$action]['extralinks3'] = [
          'index.php?module=wiki&action=edit&workspace='.$workspace['id'].'&cmd='.$wiki['name']
          => 'Wiki'
        ];
        unset($inline[$actModule][$action]['extralinks']);
        $faqs = $this->app->DB->SelectArr(sprintf("SELECT question,answer FROM wiki_faq WHERE wiki_id = %d", $wiki['id']));
        if(!empty($faqs)) {
          $inline[$actModule][$action]['faqs'] = $faqs;
          if($action !== $oldAction && empty($inline[$actModule][$oldAction]['faqs'])) {
            $inline[$actModule][$oldAction]['faqs'] = $faqs;
          }
        }
        $inline[$actModule][$action]['extmenu'] = '<li>
			<a target="_blank" href="index.php?module=wiki&action=edit&workspace='.$workspace['id'].'&cmd='.$wiki['name'].'">Bearbeiten</a>
			</li>';
        if($oldAction !== $action){
          $inline[$actModule][$oldAction]['extmenu'] = $inline[$actModule][$action]['extmenu'];
        }
      }
      else {
      }
      if($oldAction !== $action){
        $inline[$actModule][$oldAction]['handbook'] = $inline[$actModule][$action]['handbook'];
      }
    }

    if(empty($inline[$actModule][$action]['handbook'])){
      $inline[$actModule][$action]['handbook'] = '&nbsp;';
      //$inline[$actModule][$action]['extralinks3'];
      $inline[$actModule][$oldAction]['handbook'] = '&nbsp;';
      //$inline[$actModule][$oldAction]['extralinks3'];
    }

    $action = $oldAction;

    if($module !== $moduleOld) {
      $inline[$moduleOld] = $inline[$module];
    }
    $module = $moduleOld;
  }

  /**
   * @param ApplicationCore $app
   * @param string          $name
   * @param array           $erlaubtevars
   *
   * @return array
   */
  public function TableSearch($app, $name, $erlaubtevars)
  {
    switch($name)
    {
      case 'wiki_changelog':
        $id = $this->app->Secure->GetGET('id');
        $wiki = $this->app->DB->SelectRow(sprintf('SELECT name, id, wiki_workspace_id FROM wiki WHERE id = %d', $id));
        if(!empty($wiki)) {
          $ids = $this->app->DB->SelectFirstCols(
            sprintf(
              "SELECT id FROM wiki WHERE wiki_workspace_id = %d AND name = '%s'",
              $wiki['wiki_workspace_id'], $this->app->DB->real_escape_string($wiki['name'])
            )
          );
        }
        else{
          $ids = $this->app->DB->SelectFirstCols(
            sprintf(
              'SELECT id FROM wiki WHERE parent_id = %d OR id = %d',
              $id, $id
            )
          );
          $ids = $this->app->DB->SelectFirstCols(
            sprintf(
              'SELECT id FROM wiki WHERE parent_id IN (%s) OR id IN (%s)',
              implode(',', $ids), implode(',', $ids)
            )
          );
        }

        $result = [];
        $result['allowed']['wiki'] = ['faq'];
        $result['heading'] = ['','Datum','Bearbeiter','Kommentar',''];
        $result['width'] = ['1%','20%','40','50%', '1%'];
        $result['findcols'] = ['open','wc.created_at','wc.created_by','wc.comment', 'wc.id'];
        $result['searchsql'] = ['wc.comment'];
        $result['menucol'] = 4;
        $result['sql'] = "SELECT wc.id, 
       '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,
            DATE_FORMAT(wc.created_at,'%d.%m.%Y %H:%i:%s'),wc.created_by, wc.comment, wc.id 
         FROM wiki_changelog AS wc
         INNER JOIN wiki AS w ON wc.wiki_id = w.id";
        $result['menu'] = '<table class="nopadding"><tr><td nowrap>'
          .'<a href="#">'
          .'<img class="hidden" data-id="%value%" src="./themes/new/images/edit.svg" alt="zur Seite" />'
          .'</a>'
          .'</td></tr></table>';
        $result['where'] = sprintf(' w.id in (%s)', implode(',', $ids))." AND w.comment <> '' ";
        $result['moreinfo'] = true;
        $result['moreinfoaction'] = 'changelog';
        break;
      case 'wiki_faq':
        $id = $this->app->Secure->GetGET('id');
        $wiki = $this->app->DB->SelectRow(sprintf('SELECT name, id, wiki_workspace_id FROM wiki WHERE id = %d', $id));
        if(!empty($wiki)) {
          $ids = $this->app->DB->SelectFirstCols(
            sprintf(
              "SELECT id FROM wiki WHERE wiki_workspace_id = %d AND name = '%s'",
              $wiki['wiki_workspace_id'], $this->app->DB->real_escape_string($wiki['name'])
            )
          );
        }
        else{
          $ids = $this->app->DB->SelectFirstCols(
            sprintf(
              'SELECT id FROM wiki WHERE parent_id = %d OR id = %d',
              $id, $id
            )
          );
          $ids = $this->app->DB->SelectFirstCols(
            sprintf(
              'SELECT id FROM wiki WHERE parent_id IN (%s) OR id IN (%s)',
              implode(',', $ids), implode(',', $ids)
            )
          );
        }

        $result = [];
        $result['allowed']['wiki'] = ['faq'];
        $result['heading'] = ['','Seite','Sprache','Frage','Men&uuml;'];
        $result['width'] = ['1%','40','20%','50%', '1%'];
        $result['findcols'] = ['open','w.name','w.language','wf.question', 'wf.id'];
        $result['searchsql'] = ['w.name','wf.question'];
        $result['menucol'] = 4;
        $result['sql'] = "SELECT wf.id, 
       '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,
            w.name, w.language, wf.question, wf.id 
         FROM wiki_faq AS wf
         INNER JOIN wiki AS w ON wf.wiki_id = w.id";
        $result['menu'] = '<table class="nopadding"><tr><td nowrap>'
          .'<a href="#">'
          .'<img class="wikifaqedit" data-id="%value%" src="./themes/new/images/edit.svg" alt="ändern" />'
          .'&nbsp;'
          .'<img class="wikifaqdelete" data-id="%value%" src="./themes/new/images/delete.png" alt="löschen" />'
          .'</a>'
          .'</td></tr></table>';
        $result['where'] = sprintf(' w.id in (%s)', implode(',', $ids));
        $result['moreinfo'] = true;
        $result['moreinfoaction'] = 'faq';

        break;
      case 'wiki_sites':
        $workspace = $this->getUserWorkspace();
        $result = [];
        $result['allowed']['wiki'] = ['settings'];
        $result['heading'] = ['','Seite','Sprache','Men&uuml;'];
        $result['width'] = ['1%','40','20%', '1%'];
        $result['findcols'] = ['open','w.name','w.language', 'w.id'];
        $result['searchsql'] = ['w.name'];
        $result['menucol'] = 3;
        $result['sql'] = "SELECT w.id, 
       '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,
            w.name, w.language, w.id 
         FROM wiki AS w";
        $result['menu'] = '<table class="nopadding"><tr><td nowrap>'
          .'<a href="index.php?module=wiki&action=list&id=%value%">'
          .'<img src="./themes/new/images/forward.svg" alt="zur Seite" />'
          .'</a>'
          .'</td></tr></table>';
        $result['where'] = sprintf(' w.wiki_workspace_id =  %d', $workspace);
        $result['moreinfo'] = true;
        $result['moreinfoaction'] = 'sites';
        break;
      case 'wiki_workspaces':
        $result = [];
        $result['allowed']['wiki'] = array('settings');
        $result['heading'] = ['Bezeichnung','Speicherort','Aktiv', 'Men&uuml;'];
        $result['width'] = ['54%','40','5%', '1%'];
        $result['findcols'] = array('ww.name','ww.active','ww.savein', 'ww.id');
        $result['searchsql'] = array('ww.name');
        $result['sql'] =
          "SELECT ww.id, ww.name,IF(ww.savein = 'userdata','Userdata','Datenbank'),
          IF(ww.active,'ja','-'), ww.id
          FROM wiki_workspace AS ww";
        $result['count'] = 'SELECT COUNT(ww.id) FROM  wiki_workspace AS ww';
        $result['menucol'] = 4;
        $result['menu'] = '<table class="nopadding"><tr><td nowrap>'
          . '<img src="./themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/edit.svg" data-id="%value%" class="workspaceedit" alt="Editieren" />'
          . '&nbsp;'
          . '<img src="./themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/overview.png" data-id="%value%" class="workspacesites" alt="&Uuml;bersicht" />'
          . '&nbsp;'
          . '<img src="./themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/delete.svg" data-id="%value%" class="workspacedelete" alt="Löschen" />'
          . '</td></tr></table>';
        $result['where'] = 'ww.id > 0';
        break;
      case 'wiki_files':
        $id = (int)$app->Secure->GetGET('id');
        $workspace = $this->getUserWorkspace();
        $wikiname = (string)$app->Secure->GetGET('cmd');
        if ($wikiname === 'wiki_files') {
          $wikiname = (string)$app->Secure->GetGET('smodule', 'nohtml');
        }

        // ID der Wiki-Page ermitteln
        if($id === 0 && !empty($wikiname)){
          $id = (int)$app->DB->Select(sprintf('SELECT w.id FROM `wiki` AS w WHERE w.name = \'%s\' LIMIT 1',$wikiname));
        }

        $heading = array('', 'Vorschau', 'Titel', 'Stichwort', 'Version', 'Gr&ouml;&szlig;e', 'Ersteller', 'Bemerkung', 'Datum', 'Sortierung', 'Men&uuml;');
        $width = array('1%', '10%', '40%', '15%', '5%', '10%', '15%', '10%', '10%', '15%', '10%', '5%', '1%');
        $findcols = array('open', 'd.id', 'CONCAT(d.titel, \' \', v1.dateiname)', 's.subjekt', 'v1.version', "if(v1.size!='',if(v1.size > 1024*1024,CONCAT(ROUND(v1.size/1024/1024,2),' MB'),CONCAT(ROUND(v1.size/1024,2),' KB')),'')", 'v1.ersteller', 'v1.bemerkung', 'v1.datum', 's.sort', 's.id');
        $searchsql = array('d.titel', 's.subjekt', 'v1.version', "if(v1.size!='',if(v1.size > 1024*1024,CONCAT(ROUND(v1.size/1024/1024,2),' MB'),CONCAT(ROUND(v1.size/1024,2),' KB')),'')", 'v1.ersteller', 'v1.bemerkung', 'v1.dateiname', "DATE_FORMAT(v1.datum, '%d.%m.%Y')");

        $menu  = '<table border="0" cellpadding="0" cellspacing="0"><tr><td nowrap>';
        $menu .= '<a href="#" data-file-id="%value%" class="wiki-select-file-button">';
        $menu .= '<img src="./themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/forward.svg' . '" alt="Bild ausw&auml;hlen" border="0" />';
        $menu .= '</a>';
        $menu .= '&nbsp;';
        $menu .= '<a href="#" data-file-id="%value%" class="wiki-delete-file-button">';
        $menu .= '<img src="./themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/delete.svg' . '" alt="Bild l&ouml;schen" border="0" />';
        $menu .= '</a>';
        $menu .= '</td></tr></table>';
        $menucol = 10;
        $alignright = [5, 6, 10];

        if(!function_exists('imagejpeg')){
          $img = "'<img src=./themes/{$app->Conf->WFconf['defaulttheme']}/images/icon_img_error.png title=\"Keine GD-Erweiterung installiert\" />'";
        }else{
          $img = "concat('<span style=\"width:100px;text-align:center;display:block;\"><a href=\"index.php?module=dateien&action=send&id=',d.id,'\"><img src=\"index.php?module=ajax&action=thumbnail&cmd=wiki&id=',d.id,'\" style=\"border:0;max-width:100px;max-height:100px;\" /></a></span>')";
        }

        // SQL statement
        $sql =
            "SELECT SQL_CALC_FOUND_ROWS 
              d.id, 
              '<img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/details_open.png\" class=\"details\">' AS open,
              {$img}, 
              IF(d.titel != '', CONCAT(d.titel, '<br><i style=color:#999>', v1.dateiname, '</i>'), v1.dateiname), 
              s.subjekt, 
              v1.version, 
              IF(v1.size != '', IF(v1.size > 1024 * 1024, CONCAT(ROUND(v1.size / 1024 / 1024, 2), ' MB'), CONCAT(ROUND(v1.size / 1024, 2),' KB')), '') AS groesse, 
              v1.ersteller, 
              v1.bemerkung, 
              DATE_FORMAT(v1.datum, '%d.%m.%Y'), 
              s.sort,
              d.id 
            FROM datei AS d 
            LEFT JOIN datei_stichwoerter AS s ON d.id = s.datei
            LEFT JOIN (
                SELECT dv.datei, MAX(dv.version) AS version FROM datei_version AS dv GROUP BY dv.datei
            ) AS v2 ON v2.datei = d.id
            LEFT JOIN datei_version AS v1 ON v1.datei = v2.datei AND v1.version = v2.version ";

        $parameter = $id;
        $where = " s.objekt LIKE 'Wiki' AND s.parameter = '{$parameter}' AND d.geloescht = 0 ";

        $count = "
            SELECT COUNT(d.id) AS anzahl
            FROM datei AS d 
            LEFT JOIN datei_stichwoerter AS s ON d.id = s.datei
            LEFT JOIN (
                SELECT dv.datei FROM datei_version AS dv GROUP BY dv.datei
            ) v ON v.datei = d.id 
            WHERE $where";

        $result = [
          'alignright' => $alignright,
          'heading' => $heading,
          'width' => $width,
          'findcols' => $findcols,
          'searchsql' => $searchsql,
          'menu' => $menu,
          'menucol' => $menucol,
          'moreinfo' => true,
          'moreinfomodule' => 'dateien',
          'sql' => $sql,
          'where' => $where,
          'count' => $count,
        ];
        break;
    }

    // Nicht erlaubt Keys aus Result entfernen
    foreach ($result as $key => $value) {
      if (!in_array($key, $erlaubtevars, true)) {
        unset($result[$key]);
      }
    }

    return !empty($result) ? $result : [];
  }

  public function WikiMinidetailChangelog()
  {
    $id = $this->app->Secure->GetGET('id');
    $iframe = $this->app->Secure->GetGET('iframe');
    if(empty($iframe)) {
      echo '<iframe style="border:none;width:100%;" border="0" src="index.php?module=wiki&action=minidetailchangelog&id='.$id.'&iframe=true"></iframe>';
      $this->app->ExitXentral();
    }
    $content = $this->app->DB->Select(sprintf('SELECT content FROM wiki_changelog WHERE id = %d', $id));
    echo $content;
    $this->app->ExitXentral();
  }

  public function WikiMinidetailFaq()
  {
    $id = $this->app->Secure->GetGET('id');
    $iframe = $this->app->Secure->GetGET('iframe');
    if(empty($iframe)) {
      echo '<iframe style="border:none;width:100%;" border="0" src="index.php?module=wiki&action=minidetailfaq&id='.$id.'&iframe=true"></iframe>';
      $this->app->ExitXentral();
    }
    $content = $this->app->DB->Select(sprintf('SELECT answer FROM wiki_faq WHERE id = %d', $id));
    echo $content;
    $this->app->ExitXentral();
  }

  public function WikiMinidetailSites()
  {
    $id = $this->app->Secure->GetGET('id');
    $iframe = $this->app->Secure->GetGET('iframe');
    if(empty($iframe)) {
      echo '<iframe style="border:none;width:100%;" border="0" src="index.php?module=wiki&action=minidetailsites&id='.$id.'&iframe=true"></iframe>';
      $this->app->ExitXentral();
    }
    $content = $this->app->DB->Select(sprintf('SELECT content FROM wiki WHERE id = %d', $id));
    echo $content;
    $this->app->ExitXentral();
  }

  public function WikiFaq()
  {
    $command = $this->app->Secure->GetGET('command');
    if($command === 'deletefaq') {
      $wikiFaqId = $this->app->Secure->GetPOST('wikifaqid');
      $success = 0;
      if($wikiFaqId > 0) {
        $this->deleteFaqInUserdata($wikiFaqId);
        $this->app->DB->Delete(
          sprintf(
            'DELETE FROM wiki_faq WHERE id = %d',
            $wikiFaqId
          )
        );
        if($this->app->DB->affected_rows() > 0) {
          $success = 1;
        }
      }
      header('Content-Type: application/json');
      echo json_encode(['success' => $success]);
      $this->app->ExitXentral();
    }
    if($command === 'savefaq') {
      $id = $this->app->Secure->GetPOST('id');
      $wikiFaqId = $this->app->Secure->GetPOST('wikifaqid');
      $answer = $this->app->Secure->GetPOST('answer');
      $question = $this->app->Secure->GetPOST('question');
      $oldWiki = empty($wikiFaqId)?null:$this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM wiki_faq WHERE id = %d',
          $wikiFaqId
        )
      );
      if(empty($wikiFaqId) && !empty($id)) {
        $this->app->DB->Insert(sprintf("INSERT INTO wiki_faq (wiki_id) VALUES (%d)", $id));
        $wikiFaqId = $this->app->DB->GetInsertID();
      }
      if(!empty($wikiFaqId)) {
        if(!empty($oldWiki['question']) && $this->app->DB->real_escape_string($oldWiki['question']) != $question) {
          $this->deleteFaqInUserdata($wikiFaqId);
        }
        $this->app->DB->Update(
          sprintf(
            "UPDATE wiki_faq 
            SET answer = '%s', question = '%s', created_by = '%s', updated_at = NOW()
            WHERE id = %d",
            $answer, $question, $this->app->DB->real_escape_string($this->app->User->GetName()), $wikiFaqId
          )
        );

        $this->saveFaqInUserdata($wikiFaqId);
      }

      $wikiFaq = $wikiFaqId <= 0?null:$this->app->DB->SelectRow(
        sprintf('SELECT * FROM wiki_faq WHERE id = %d', $wikiFaqId)
      );
      if(empty($wikiFaq)) {
        $wikiFaq = ['id' => 0, 'answer' => '', 'question' => ''];
      }
      header('Content-Type: application/json');
      echo json_encode($wikiFaq);
      $this->app->ExitXentral();
    }
    if($command === 'getfaq') {
      $wikiFaqId = $this->app->Secure->GetPOST('wikifaqid');
      $wikiFaq = $wikiFaqId <= 0?null:$this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM wiki_faq WHERE id = %d',
          $wikiFaqId
        )
      );
      if(empty($wikiFaq)) {
        $wikiFaq = ['id' => 0, 'answer' => '', 'question' => ''];
      }
      header('Content-Type: application/json');
      echo json_encode($wikiFaq);
      $this->app->ExitXentral();
    }
    $this->WikiMenu();
    $workspace =$this->getUserWorkspace();
    $language = $this->getUserLanguage();
    $cmd = $this->app->Secure->GetGET('cmd');
    $id = $this->app->Secure->GetGET('id');
    if(!empty($id)) {
      $this->app->Tpl->Set('ID', $id);
    }
    else {
      $wiki = $this->getArticleByName($cmd, $workspace, $language);
      if(empty($wiki) && !empty($language)){
        $wiki = $this->getArticleByName($cmd, $workspace);
      }
      if(!empty($wiki)){
        $this->app->Tpl->Set('ID', $wiki['id']);
      }
    }
    $this->app->YUI->TableSearch('TAB1', 'wiki_faq','show','','',basename(__FILE__),__CLASS__);
    //$this->app->YUI->CkEditor('popupanswer','wiki',['ckeditor5'=>true,'min-height'=>'250']);
    $this->app->YUI->CkEditor('popupanswer','wiki');
    $this->app->Tpl->Parse('PAGE', 'wiki_faq.tpl');
  }

  public function WikiChangelog()
  {
    $this->WikiMenu();
    $this->app->YUI->TableSearch('TAB1','wiki_changelog','show','','',basename(__FILE__),__CLASS__);
    $this->app->Tpl->Parse('PAGE', 'wiki_changelog.tpl');
  }

  public function WikiGetFileCache()
  {
    $this->WikiGetFile(true);
  }

  public function WikiGetFile($exit = false)
  {
    $workspacefolder = $this->app->Secure->GetGET('workspacefolder', 'nothtml','', true);
    $article = $this->app->Secure->GetGET('article', 'nothtml','', true);
    $fileid = $this->app->Secure->GetGET('fileid', 'nothtml','', true);
    $path = $this->getUserDataFolder().$workspacefolder.'/files/'.$article.'/'.$fileid;
    $name = $article;
    if(is_file($path)) {
      if ( strpos ( $_SERVER [ 'HTTP_USER_AGENT' ], "MSIE" ) > 0 )
      {
        $header_name =  'Content-Disposition: attachment; filename="' . rawurlencode ( $name ) . '"' ;
      }
      else {
        $header_name =   'Content-Disposition: attachment; filename*=UTF-8\'\'' . rawurlencode ( $name ) ;
      }

      $contenttype= $this->app->erp->content_type($name);
      if(empty($contenttype)) {
        $contenttype = mime_content_type($path);
      }

      //required, or it might try to send the serving     //document instead of the file
      header('Cache-Control: public, max-age=14400, s-maxage=14400');
      header('Pragma: ');
      header('Expires: '.date('D, d M Y H:i:s e'), time()+14400);
      header("Content-Type: $contenttype");
      header('Content-Length: ' .(string)(empty($path)?0:@filesize($path)) );
      //header('Content-Disposition: inline; filename="'.$name.'"');
      //$name = $this->Dateinamen($name);
      //header('Content-Disposition: attachment; filename="'.$name.'"');
      header($header_name);

      if(!empty($path) && $file = fopen($path, 'rb')){
        while( (!feof($file)) && (connection_status()==0) ){
          print(fread($file, 1024*8));
          flush();
        }
        fclose($file);
      }
    }
    if($exit) {
      exit;
    }
    $this->app->ExitXentral();
  }

  public function WikiSettings()
  {
    $command = $this->app->Secure->GetGET('command');
    if($command === 'openworkspace') {
      $id = $this->app->Secure->GetPOST('id');
      $workspace = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM wiki_workspace WHERE id = %d',
          $id
        )
      );
      if(empty($workspace)) {
        $workspace = [
          'name'   =>'',
          'id'     => 0,
          'active' => 1,
          'savein' => ''
        ];
      }
      header('Content-Type: application/json');
      echo json_encode($workspace);
      $this->app->ExitXentral();
    }

    if($command === 'saveworkspace') {
      $id = $this->app->Secure->GetPOST('id');
      $active= $this->app->Secure->GetPOST('active');
      $name = $this->app->Secure->GetPOST('name');
      $savein = $this->app->Secure->GetPOST('savein');
      $status = 0;
      if(empty($id)) {
        $this->app->DB->Insert(
          sprintf(
            "INSERT INTO wiki_workspace (id, name, description, active, savein) 
                VALUES (NULL, '%s', '', %d,'%s')",
            $name, $active, $savein
          )
        );
        $status = $this->app->DB->GetInsertID()>0?1:0;
      }
      else {
        $this->app->DB->Update(
          sprintf(
            "UPDATE wiki_workspace SET name = '%s', active = %d, savein = '%s' WHERE id = %d",
            $name, $active, $savein, $id
          )
        );
        if($this->app->DB->error()) {
          $status = 0;
        }
        elseif($this->app->DB->Select(
          sprintf(
            'SELECT id FROM wiki_workspace WHERE id = %d',
            $id
          )
        )) {
          $status = 1;
        }
      }
      header('Content-Type: application/json');
      echo json_encode(['status' => $status]);
      $this->app->ExitXentral();
    }

    if($command === 'deleteworkspace') {
      $id = $this->app->Secure->GetPOST('id');
      $status = 0;
      if($this->app->DB->Select(sprintf('SELECT id FROM wiki_workspace WHERE id = %d', $id))) {
        if($this->app->DB->Select(sprintf('SELECT id FROM wiki WHERE wiki_workspace_id = %d', $id))) {
          $this->app->DB->Update(sprintf('UPDATE wiki_workspace SET active = 0 WHERE id = %d', $id));
        }
        else {
          $this->app->DB->Delete(sprintf('DELETE FROM wiki_workspace WHERE id = %d', $id));
        }
      }
      header('Content-Type: application/json');
      echo json_encode(['status' => $status]);
      $this->app->ExitXentral();
    }

    $this->WikiMenu();
    $this->app->erp->MenuEintrag('#','Neu');
    $this->app->YUI->TableSearch('TAB1','wiki_workspaces','show','','',basename(__FILE__),__CLASS__);
    $this->app->YUI->TableSearch('TABSITES','wiki_sites','show','','',basename(__FILE__),__CLASS__);
    $this->app->Tpl->Parse('PAGE', 'wiki_settings.tpl');
  }

  /**
   * @return string
   */
  public function getUserDataFolder()
  {
    $folder = rtrim($this->app->Conf->WFuserdata).'/wiki/';
    if(!is_dir($folder) && !@mkdir($folder,0777, true) && !is_dir($folder)) {
      return $folder;
    }

    return $folder;
  }

  /**
   * @return bool
   */
  public function fromUserData()
  {
    $folder = $this->getUserDataFolder();

    $workspaces = $this->getFilesAndFolders($folder);
    if(empty($workspaces) || empty($workspaces['folders'])) {
      return false;
    }
    $workspaces = $workspaces['folders'];
    $workspaces = array_flip($workspaces);
    foreach($workspaces as $workspaceKey => $workspace) {
      $workspaces[$workspaceKey] = 0;
    }

    $workSpacesInDb = $this->getAllWorkspaces();
    foreach($workspaces as $workspace => $workspaceId) {
      $found = false;
      if(!empty($workSpacesInDb)) {
        foreach($workSpacesInDb as $workSpaceInDb) {
          if($workSpaceInDb['foldername'] === $workspace) {
            $workspaces[$workspace] = $workSpaceInDb['id'];
            $found = true;
            break;
          }
        }
      }
      if(!$found) {
        $this->app->DB->Insert(
          sprintf(
            "INSERT INTO wiki_workspace (name, foldername, savein, active)
            VALUES ('%s', '%s', 'userdata', 1)",
            $this->app->DB->real_escape_string(ucfirst($workspace)),
            $this->app->DB->real_escape_string($workspace)
          )
        );
        $workspaces[$workspace] = (int)$this->app->DB->GetInsertID();
      }
    }
    $allLanguages = $this->app->erp->GetSelectSprachenListe();
    unset($allLanguages[0]);
    $allLanguages = array_unique(array_merge(['default'], array_keys($allLanguages)));
    foreach($workspaces as $workspace => $workspaceId) {
      if(empty($workspaceId)) {
        continue;
      }
      $workspaceFolders = $this->getFilesAndFolders($folder.$workspace);
      if(empty($workspaceFolders) || empty($workspaceFolders['folders'])) {
        continue;
      }
      $workspaceFolders = $workspaceFolders['folders'];

      $languages = array_intersect($allLanguages, $workspaceFolders);
      if(empty($languages)) {
        continue;
      }
      foreach($languages as $language) {
        $articles = $this->getFilesAndFolders($folder . $workspace . '/' . $language);
        if(empty($articles) || empty($articles['files'])) {
          continue;
        }
        $articles = $articles['files'];
        foreach($articles as $article) {
          @chmod($folder . $workspace . '/' . $language.'/'.$article, 0666);
          $articleContent = file_get_contents($folder . $workspace . '/' . $language.'/'.$article);
          $articleContent = strip_tags(
            $articleContent,
            '<table><tr><td><th><tbody><tfoot><thead><button><br><br /><div><a><img><h1><h2><h3><h4><h5><h6><p><span><ul><li><ol><strong><u><i><iframe><article>'
          );
          $articleContent = str_replace(['<h1>&nbsp;</h1>','<h2>&nbsp;</h2>','<h3>&nbsp;</h3>'],' ', $articleContent);
          foreach(['img','h1','h2','h3','a'] as $tag) {
            while (
            preg_match_all(
              '/(.*)<'.$tag.'([^>]+)((class|alt)="([^"]+)")(.*)>/',
              $articleContent,
              $matches,
              PREG_OFFSET_CAPTURE
            )
            ) {
              if(strlen($matches[3][0][0]) === 0){
                break;
              }
              $articleContent = substr($articleContent, 0, $matches[3][0][1])
                . substr($articleContent, $matches[3][0][1] + strlen($matches[3][0][0]));
            }
          }
          /*$articleContent = str_replace(
            ["<br />","<br>","</p>",'</div>','</ul>','</h1>','</h2>','</h3>','</h4>','</h5>','</h6>'],
            ["<br />\n","<br>\n","</p>\n","</div>\n","</ul>","</h1>\n","</h2>\n","</h3>\n","</h4>\n","</h5>\n","</h6>\n"],
            $articleContent
          );*/
          if(empty($articleContent)) {
            continue;
          }
          $this->saveArticle($article, $articleContent, $workspaceId, $language==='default'?'':$language, true);
        }
      }
      if(is_dir($folder . $workspace . '/faq')) {
        $fileFolders = $this->getFilesAndFolders($folder . $workspace . '/faq');
        if(empty($fileFolders['folders'])) {
          continue;
        }
        $fileFolders = $fileFolders['folders'];
        foreach($fileFolders as $fileFolder) {
          @chmod($folder . $workspace . '/faq/' . $fileFolder, 0777);
          $article = $this->getArticleByName($fileFolder, $workspaceId);
          if(empty($article)) {
            continue;
          }
          $files = $this->getFilesAndFolders($folder . $workspace . '/faq/'.$fileFolder);
          if(empty($files['files'])) {
            continue;
          }
          $files = $files['files'];
          $this->syncFaqs($folder . $workspace . '/faq/'.$fileFolder, $files, $article);
        }
      }
    }

    return true;
  }

  /**
   * @param string $folder
   * @param array  $files
   * @param array  $wiki
   */
  public function syncFaqs($folder, $files, $wiki)
  {
    foreach($files as $file) {
      $fileabsoulte = $folder.'/'.$file;
      if(strpos($file,'question_') !== 0) {
        continue;
      }
      if(!is_file($fileabsoulte)) {
        continue;
      }
      $question = file_get_contents($fileabsoulte);
      if(empty($question)) {
        continue;
      }
      $answerfile = $folder.'/answer_'.substr($file, 9);
      @chmod($answerfile, 0666);
      $answer= is_file($answerfile)?file_get_contents($answerfile):'';
      if(empty($answer)) {
        $this->deleteFaq($wiki, $question);
        continue;
      }
      $this->updateFaq($wiki, $question, $answer);
    }
  }


  /**
   * @param array  $wiki
   * @param string $question
   */
  public function deleteFaq($wiki, $question)
  {
    $this->app->DB->Delete(
      sprintf(
        "DELETE FROM wiki_faq WHERE wiki_id = %d AND question = '%s'",
        $wiki['id'], $this->app->DB->real_escape_string($question)
      )
    );
  }

  /**
   * @param $wiki
   * @param $question
   * @param $answer
   */
  public function updateFaq($wiki, $question, $answer)
  {
    if(empty($question) || empty($wiki['id'])) {
      return;
    }
    $id = $this->app->DB->Select(
      sprintf(
        "SELECT id FROM wiki_faq WHERE wiki_id = %d AND question = '%s' LIMIT 1",
        $wiki['id'], $this->app->DB->real_escape_string($question)
      )
    );
    if($id <= 0) {
      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO wiki_faq (wiki_id, question) VALUES (%d, '%s')",
          $wiki['id'], $this->app->DB->real_escape_string($question)
        )
      );
      $id = $this->app->DB->GetInsertID();
    }
    if($id <= 0) {
      return;
    }
    $this->app->DB->Update(
      sprintf(
        "UPDATE wiki_faq SET answer = '%s' WHERE id = %d",
        $this->app->DB->real_escape_string($answer), $id
      )
    );
  }


  /**
   * @param string $rootFolder
   *
   * @return array
   */
  public function getFilesAndFolders($rootFolder)
  {
    $ret  =[];
    if(!is_dir($rootFolder)) {
      return $ret;
    }
    $rootFolder = rtrim($rootFolder,'/').'/';
    $handle = @opendir($rootFolder);
    if(empty($handle)) {
      return $ret;
    }
    while($entry = @readdir($handle)) {
      if($entry === '.' || $entry === '..') {
        continue;
      }
      if(is_dir($rootFolder.$entry)) {
        $ret['folders'][] = $entry;
        continue;
      }
      if(is_file($rootFolder.$entry)) {
        @chmod($rootFolder.$entry, 0666);
        $ret['files'][] = $entry;
      }
    }
    closedir($handle);

    return $ret;
  }

  /**
   * @return array
   */
  public function getWorkspacesWithUserdata($fullRow = false)
  {
    if($fullRow) {
      $ret = $this->app->DB->SelectArr("SELECT * FROM wiki_workspace WHERE savein = 'userdata'");
      if(empty($ret)) {
        return [];
      }

      return $ret;
    }
    return $this->app->DB->SelectPairs("SELECT id, name FROM wiki_workspace WHERE savein = 'userdata'");
  }

  /**
   * @param string $name
   *
   * @return array|null
   */
  public function getWorkspaceByName($name)
  {
    return $this->app->DB->SelectRow(
      sprintf(
        "SELECT * FROM wiki_workspace WHERE name = '%s' LIMIT 1", $this->app->DB->real_escape_string($name)
      )
    );
  }

  /**
   * @return array
   */
  public function getAllWorkspaces()
  {
    $workspaces = $this->app->DB->SelectArr(
      'SELECT * FROM wiki_workspace'
    );
    if(empty($workspaces)) {
      return [];
    }

    foreach($workspaces AS $key => $workspace) {
      if(empty($workspace['foldername'])) {
        $foldername = $this->app->erp->Dateinamen($workspace['name']);
        if($workspace['savein'] === 'userdata'){
          $this->app->DB->Update(
            sprintf(
              "UPDATE wiki_workspace SET foldername = '%s' WHERE id = %d",
              $this->app->DB->real_escape_string($foldername), $workspace['id']
            )
          );
        }
        $workspaces[$key]['foldername'] = $foldername;
      }
    }

    return $workspaces;
  }

  public function saveAllInUserdata()
  {
    $workspaces = $this->getWorkspacesWithUserdata(true);
    if(empty($workspaces)) {
      return;
    }
    foreach($workspaces as $workspace) {
      $workspaceId = $workspace['id'];
      $articles = $this->getArticlesByWorkspaceId($workspaceId);
      if(empty($articles)) {
        continue;
      }
      foreach($articles as $article) {
        $this->saveArticleInUserdata($article, $workspace);
      }
    }
  }

  /**
   * @param null|int|array $faq
   * @param bool           $isChangeLog
   *
   * @return array|null
   */
  public function getFaqDataForUserdata($faq, $isChangeLog = false)
  {
    if(is_numeric($faq)) {
      if($isChangeLog) {
        $faq = $this->app->DB->SelectRow(sprintf('SELECT * FROM wiki_changelog WHERE id = %d', $faq));
      }
      else{
        $faq = $this->app->DB->SelectRow(sprintf('SELECT * FROM wiki_faq WHERE id = %d', $faq));
      }
    }
    if(empty($faq) || empty($faq['wiki_id'])) {
      return null;
    }
    if(!$isChangeLog && empty($faq['question'])) {
      return null;
    }
    if($isChangeLog && empty($faq['comment'])) {
      return null;
    }

    $wiki = $this->app->DB->SelectRow(sprintf('SELECT wiki_workspace_id, name FROM wiki WHERE id = %d', $faq['wiki_id']));

    if(empty($wiki) || empty($wiki['wiki_workspace_id']) || empty($wiki['name'])
    || !$this->app->DB->Select(
      sprintf(
        "SELECT id FROM wiki_workspace WHERE savein = 'userdata' AND id =%d ",
        $wiki['wiki_workspace_id']
      )
      )
    ) {
      return null;
    }
    $faq['wikifilename'] = $this->app->erp->Dateinamen($wiki['name']);
    $faq['workspace_id'] = $wiki['wiki_workspace_id'];
    $faq['workspace_folder'] = $this->app->DB->Select(
      sprintf(
        'SELECT foldername FROM wiki_workspace WHERE id = %d',
        $faq['workspace_id']
      )
    );
    if(empty($faq['workspace_folder'])) {
      return null;
    }

    $faq['folder'] = $this->getUserDataFolder()
      .$faq['workspace_folder'].'/'.($isChangeLog?'changelog':'faq').'/'.$faq['wikifilename'];

    return $faq;
  }

  /**
   * @param int|array|null $faq
   */
  public function saveFaqInUserdata($faq)
  {
    $faq = $this->getFaqDataForUserdata($faq);

    if(empty($faq)) {
      return;
    }

    if(!is_dir($faq['folder']) && !@mkdir($faq['folder'],0777, true) && !is_dir($faq['folder'])) {
      return;
    }
    list($questionFile, $answerfile) = $this->getFaqFiles($faq['folder'], $faq['question']);
    if(empty($questionFile) || empty($answerfile)) {
      return;
    }
    @file_put_contents($questionFile, $faq['question']);
    @chmod($questionFile,0666);
    @file_put_contents($answerfile, $faq['answer']);
    @chmod($answerfile,0666);
  }

  /**
   * @param int|array|null $changelog
   */
  public function saveChangeLogInUserdata($changelog)
  {
    $changelog = $this->getFaqDataForUserdata($changelog, true);
    if(empty($changelog)) {
      return;
    }
    if(!is_dir($changelog['folder']) && !@mkdir($changelog['folder'],0777, true) && !is_dir($changelog['folder'])) {
      return;
    }
    list($commentFile, $messageFile, $createdAtFile, $createdByFile) =
      $this->getChangelogFiles($changelog['folder'], $changelog['created_at']);
    if(empty($commentFile) || empty($messageFile) || empty($createdAtFile) || empty($createdByFile)) {
      return;
    }
    @file_put_contents($commentFile, $changelog['commment']);
    @file_put_contents($messageFile, $changelog['content']);
    @file_put_contents($createdAtFile, $changelog['created_at']);
    @file_put_contents($createdByFile, $changelog['created_by']);
    @chmod($commentFile,0666);
    @chmod($messageFile,0666);
    @chmod($createdAtFile,0666);
    @chmod($createdByFile,0666);
  }

  /**
   * @param string $folder
   * @param string $question
   *
   * @return array
   */
  public function getFaqFiles($folder, $question)
  {
    $ids = [];
    $files = $this->getFilesAndFolders($folder);
    if(!empty($files['files'])) {
      $files = $files['files'];
      foreach($files as $file) {
        if(strpos($file, 'question_') !== 0) {
          continue;
        }
        if($question === file_get_contents($folder.'/'.$file)) {
          @chmod($folder.'/'.$file, 0666);
          return [$folder.'/'.$file, $folder.'/answer_'.substr($file,9)];
        }
        $ids[] = (int)substr($file,9);
      }
    }
    $id = 1;
    while(in_array($id, $ids)) {
      $id++;
    }

    return [ $folder.'/question_'.$id, $folder.'/answer_'.$id];
  }

  /**
   * @param string $folder
   * @param string $changelog
   *
   * @return array
   */
  public function getChangelogFiles($folder, $changelog)
  {
    $ids = [];
    $files = $this->getFilesAndFolders($folder);
    if(!empty($files['files'])) {
      $files = $files['files'];
      foreach($files as $file) {
        if(strpos($file, 'createdat_') !== 0) {
          continue;
        }
        if($changelog === file_get_contents($folder.'/'.$file)) {
          return [
            $folder.'/comment_'.substr($file,10),
            $folder.'/content_'.substr($file,10),
            $folder.'/'.$file,
            $folder.'/createdby_'.substr($file,10)
          ];
        }
        $ids[] = (int)substr($file,9);
      }
    }
    $id = 1;
    while(in_array($id, $ids)) {
      $id++;
    }

    return [ $folder.'/comment_'.$id, $folder.'/content_'.$id, $folder.'/createdat_'.$id,$folder.'/createdby_'.$id];
  }

  /**
   * @param int|array|null $faq
   */
  public function deleteFaqInUserdata($faq)
  {
    $faq = $this->getFaqDataForUserdata($faq);

    if(empty($faq)) {
      return;
    }

    $faq['answer'] = '';
    $this->saveFaqInUserdata($faq);
  }

  /**
   * @param int $changeLogId
   */
  public function saveCommentInUserdata($changeLogId) {
    $changeLog = $this->app->DB->SelectRow(
      sprintf(
        'SELECT comment,content,wiki_id FROM wiki_changelog WHERE id = %d',
        $changeLogId
      )
    );
    if(empty($changeLog) || empty($changeLog['comment']) || empty($changeLog['wiki_id'])) {
      return;
    }
    $article = $this->app->DB->SelectRow(sprintf('SELECT * FROM wiki WHERE id = %d', $changeLog['wiki_id']));
    if(empty($article)) {
      return;
    }
    $workspace = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM wiki_workspace WHERE id = %d',
        $article['wiki_workspace_id']
      )
    );
    if(empty($workspace) || $workspace['savein'] !== 'userdata') {
      return;
    }
    if(empty($workspace['foldername'])) {
      $workspace['foldername'] = $this->app->erp->Dateinamen($workspace['name']);
    }
    $language = empty($article['language'])?'default':$article['language'];
    $folder = $this->getUserDataFolder().$workspace['foldername'].'/changelog/'.$language;
    if(!is_dir($folder) && !@mkdir($folder, 0777, true) && !is_dir($folder)) {
      return;
    }
    list($new, $content) = $this->getUserdataPicturesFromFiles($changeLog['content'], $article, $workspace);
    if(@file_put_contents($folder.'/'.$article['name'], $content)) {
      if($new && !empty($content)) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE wiki set content = '%s' WHERE id = %d", $this->app->DB->real_escape_string($content),
            $article['id']
          )
        );
      }
      @chmod($folder.'/'.$article['name'], 0666);
    }
  }

  /**
   * @param int|array|null $article
   * @param null|int|array $workspace
   */
  public function saveArticleInUserdata($article, $workspace = null)
  {
    if(empty($article)) {
      return;
    }
    if(is_numeric($article)) {
      $article = $this->app->DB->SelectRow(sprintf('SELECT * FROM wiki WHERE id = %d', $article));
      if(empty($article)) {
        return;
      }
    }
    elseif(empty($article['content'])) {
      $article = $this->app->DB->SelectRow(sprintf('SELECT * FROM wiki WHERE id = %d', $article['id']));
    }
    if(empty($workspace)) {
      $workspace = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM wiki_workspace WHERE id = %d',
          $article['wiki_workspace_id']
        )
      );
      if(empty($workspace) || $workspace['savein'] !== 'userdata') {
        return;
      }
    }
    elseif(is_numeric($workspace)) {
      $workspace = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM wiki_workspace WHERE id = %d',
          $workspace
        )
      );
      if(empty($workspace) || $workspace['savein'] !== 'userdata') {
        return;
      }
    }
    if(empty($workspace['foldername'])) {
      $workspace['foldername'] = $this->app->erp->Dateinamen($workspace['name']);
    }
    $language = empty($article['language'])?'default':$article['language'];
    $folder = $this->getUserDataFolder().$workspace['foldername'].'/'.$language;
    if(!is_dir($folder) && !@mkdir($folder, 0777, true) && !is_dir($folder)) {
      return;
    }
    if(empty($article['name']) || empty($article['content'])) {
      return;
    }
    $content = $article['content'];
    list($new, $content) = $this->getUserdataPicturesFromFiles($content, $article, $workspace);
    /*$new = false;
    while(
      preg_match_all(
        '/(.*)src="\.\/index\.php\?(module=dateien&amp;action=send&amp;id=([0-9]+))"(.*)/',
        $content,
        $matches,
        PREG_OFFSET_CAPTURE
      )
    ) {
      if(empty($matches[3]) || empty($matches[3][0]) || empty($matches[3][0][0])) {
        break;
      }
      $articleFolder = $this->app->erp->Dateinamen($article['name']);
      $newFile = $this->saveFileToUserData(
        $matches[3][0][0],
        $articleFolder,
        $workspace['foldername']
      );
      if(empty($newFile)) {
        break;
      }
      $newContent = str_replace(
        'src="./index.php?module=dateien&amp;action=send&amp;id='.$matches[3][0][0].'"',
        'src="./index.php?module=wiki&amp;action=getfile&amp;workspacefolder='
        .urlencode($workspace['foldername'])
        .'&amp;article='.urlencode($articleFolder)
        .'&amp;fileid='.$matches[3][0][0].'"',
        $content
      );
      if($newContent === $content || empty($newContent)) {
        break;
      }
      $content = $newContent;
      $new = true;
    }*/

    if(@file_put_contents($folder.'/'.$article['name'], $content)) {
      @chmod($folder.'/'.$article['name'],0666);
      @chmod($folder, 0777);
      if($new && !empty($content)) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE wiki set content = '%s' WHERE id = %d", $this->app->DB->real_escape_string($content),
            $article['id']
          )
        );
      }
    }
  }

  /**
   * @param string $content
   * @param array  $article
   * @param array  $workspace
   *
   * @return array
   */
  public function getUserdataPicturesFromFiles($content, $article, $workspace)
  {
    $new = false;
    while(
    preg_match_all(
      '/(.*)src="\.\/index\.php\?(module=dateien&amp;action=send&amp;id=([0-9]+))"(.*)/',
      $content,
      $matches,
      PREG_OFFSET_CAPTURE
    )
    ) {
      if(empty($matches[3]) || empty($matches[3][0]) || empty($matches[3][0][0])) {
        return [$new, $content];
      }
      $articleFolder = $this->app->erp->Dateinamen($article['name']);
      $newFile = $this->saveFileToUserData(
        $matches[3][0][0],
        $articleFolder,
        $workspace['foldername']
      );
      if(empty($newFile)) {
        return [$new, $content];
      }
      $newContent = str_replace(
        'src="./index.php?module=dateien&amp;action=send&amp;id='.$matches[3][0][0].'"',
        'src="./index.php?module=wiki&amp;action=getfile&amp;workspacefolder='
        .urlencode($workspace['foldername'])
        .'&amp;article='.urlencode($articleFolder)
        .'&amp;fileid='.$matches[3][0][0].'"',
        $content
      );
      if($newContent === $content || empty($newContent)) {
        return [$new, $content];
      }

      $content = $newContent;
      $new = true;
    }

    return [$new, $content];
  }

  /**
   * @param int    $fileId
   * @param string $aricleFolder
   * @param string    $workspaceFolder
   *
   * @return string
   */
  public function saveFileToUserData($fileId, $aricleFolder, $workspaceFolder)
  {
    if(empty($fileId) || empty($aricleFolder) || empty($workspaceFolder)) {
      return '';
    }

    $folder = $this->getUserDataFolder().$workspaceFolder.'/files/'.$aricleFolder;
    if(!is_dir($folder) && !@mkdir($folder,0777,true) && !is_dir($folder)) {
      return '';
    }
    $folder .= '/';
    $dmsFolder = $this->app->Conf->WFuserdata.'/dms/'.$this->app->Conf->WFdbname;
    $dmsFile = $this->app->erp->GetDateiPfad($fileId);
    if(!is_file($dmsFile)) {
      return '';
    }
    $file = $folder.$fileId;
    if(is_file($file)) {
      @chmod($file, 0666);
      $md5dms = md5_file($dmsFile);
      $md5 = md5_file($file);
      if($md5dms === $md5) {
        return (String)$fileId;
      }
      $fileId++;
      while(is_file($folder.$fileId)) {
        $md5 = md5_file($folder.$fileId);
        if($md5dms === $md5) {
          return (String)$fileId;
        }
        $fileId++;
      }
    }

    if(@copy($dmsFile, $file)) {
      @chmod($file, 0666);
      @chmod(dirname($file), 0777);
      return (String)$fileId;
    }

    return '';
  }


  /**
   * @param int $workspaceId
   *
   * @return array
   */
  public function getArticlesByWorkspaceId($workspaceId)
  {
    if(empty($workspaceId)) {
      return [];
    }

    $ret = $this->app->DB->SelectArr('SELECT id, language, name FROM wiki WHERE wiki_workspace_id = %d', $workspaceId);
    if(empty($ret)) {
      return $ret;
    }
    return $ret;
  }

  public function WikiDateien()
  {
    $subcmd = $this->app->Secure->GetGET('subcmd');
    if($subcmd === 'delete') {
      $status = 0;
      $fileId = (int)$this->app->Secure->GetPOST('fileid');
      if($fileId > 0) {
        if($this->app->erp->DeleteDatei($fileId)) {
          $status = 1;
        }
      }
      header('Content-Type: application/json');
      echo json_encode(['status' => $status]);
      $this->app->ExitXentral();
    }
    if($subcmd === 'browse'){
      $this->app->BuildNavigation = false;
      $this->app->YUI->TableSearch('DATATABLE', 'wiki_files', 'show', '', '', basename(__FILE__), __CLASS__);
      //$cmd = $this->app->Secure->GetGET('cmd');
      //$id = (int)$this->app->DB->Select(sprintf('SELECT w.id FROM `wiki` AS w WHERE w.name = \'%s\' LIMIT 1', $cmd));
      $this->app->Tpl->Parse('PAGE', 'wiki_files_popup.tpl');
      return;
    }

    $id = $this->app->Secure->GetGET('id');
    $this->WikiMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Dateien)');
    $this->app->YUI->DateiUpload('PAGE','Wiki',$id);
  }


  public function WikiAlle()
  {
    $command = $this->app->Secure->GetGET('command');
    if($command === 'gethtml')
    {
      $html = '';
      $workspace = $this->app->Secure->GetPOST('workspace');
      $this->app->User->SetParameter('wiki_workspace', $workspace);
      $html = $this->getAllHtml($this->app->DB->SelectArr(
        sprintf(
          "SELECT name,id FROM wiki WHERE wiki_workspace_id = %d ORDER by name",
          $workspace
        )
      ));
      $html = $this->app->Tpl->ParseTranslation($html);
      header('Content-Type: application/json');
      echo json_encode(
        [
          'html'      => $html
        ]
      );
      $this->app->ExitXentral();
    }
    $this->app->Tpl->Add('KURZUEBERSCHRIFT2','Hauptseite');
    $this->app->erp->MenuEintrag('index.php?module=wiki&action=list&cmd=Hauptseite','Hauptseite');

    $anzahldateien = $this->app->erp->AnzahlDateien('Wiki','Hauptseite');
    if($anzahldateien > 0) {
      $anzahldateien = ' ('.$anzahldateien.')';
    } else {
      $anzahldateien='';
    }

    $this->app->erp->MenuEintrag('index.php?module=wiki&action=dateien&cmd=Hauptseite','Dateien'.$anzahldateien);
    $this->app->erp->MenuEintrag('index.php?module=wiki&action=new','Neue Seite anlegen');
    $this->app->erp->MenuEintrag('index.php?module=wiki&action=alle','Alle Seiten anzeigen');
    $this->app->erp->MenuEintrag('index.php?module=wiki&action=list&cmd=Hauptseite','Zur&uuml;ck zur &Uuml;bersicht');
    $workspace = $this->getUserWorkspace();
    $alle = $this->app->DB->SelectArr(
      sprintf(
        "SELECT name,id FROM wiki WHERE wiki_workspace_id = %d ORDER by name",
        $workspace
      )
    );

    $this->app->Tpl->Set('TAB1', $this->getAllHtml($alle));

    $this->showWorkspaces();
    $this->app->Tpl->Parse('PAGE','wiki_alle.tpl');
  }

  /**
   * @param array|null $alle
   *
   * @return string
   */
  public function getAllHtml($alle)
  {
    $html = '<h1>{|Alle Seiten nach Alphabet sortiert|}:</h1><ul>';
    if(!empty($alle)){
      foreach ($alle as $row) {
        $html .= '<li><a href="index.php?module=wiki&action=list&id=' . $row['id'] . '&cmd=' .
          $row['name'] . '">' . $row['name'] . '</a></li>';
      }
    }
    $html .= '</ul>';

    return $html;
  }

  function WikiCreateDialog()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    $this->app->Tpl->Set(
      'TAB1',
      "<div class=\"info\">{|Seite|}: <b>{|$cmd|}</b> {|fehlt|}! {|Soll diese jetzt angelegt werden?|} <a href=\"index.php?module=wiki&action=create&cmd=$cmd\">{|Seite jetzt anlegen!|}</a></div>"
    );
  }


  public function WikiDelete()
  {
    session_start();
    $cmd = $this->app->Secure->GetGET('cmd');
    $id = $this->app->Secure->GetGET('id');
    $key = $this->app->Secure->GetGET('key');

    if($key==$_SESSION['deletekey'] && $id > 0 && $key!='')
    { 
      //loeschen
      $_SESSION['deletekey'] = '';
      $this->app->DB->Delete("DELETE FROM wiki WHERE id='$id' LIMIT 1");
      $this->app->DB->Delete("DELETE FROM datei_stichwoerter WHERE parameter='$id' AND objekt='Wiki'");
      $this->app->Location->execute('index.php?module=wiki&action=list');
    }
    if($id > 0)
    { 
      $l=20;
      $c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxwz0123456789";
      $s = '';
      for(;$l > 0;$l--) {
        $s .= $c[rand(0, strlen($c))];
      }
      $key = str_shuffle($s);

      $_SESSION['deletekey'] = $key;

      $name = $this->app->DB->Select("SELECT name FROM wiki WHERE id='$id'");

      $this->app->Tpl->Set(
        'TAB1',
        "<div class=\"error\">Seite: <b>$name</b> wirklich l&ouml;schen? <a href=\"index.php?module=wiki&action=delete&id=$id&key=$key\">Seite jetzt l&ouml;schen!</a></div>"
      );
      $this->app->Tpl->Set('KURZUEBERSCHRIFT2',"$name");
      $this->app->Tpl->Parse('PAGE','tabview.tpl');
    }
  }


  public function WikiNew()
  {
    $workspace = $this->app->Secure->GetGET('workspace');
    $cmd = $this->app->Secure->GetGET('cmd');
    $command = $this->app->Secure->GetGET('command');
    if($command === 'createnew' && !empty($cmd)) {
      $language = $this->app->Secure->GetPOST('language');
      $this->app->User->SetParameter('wiki_language', $language);
      $this->app->User->SetParameter('wiki_workspace', $workspace);
      $wiki = $this->getArticleByName($cmd,$workspace,$language);
      if(empty($wiki)) {
        $content = '';
        $this->saveArticle($cmd, $content, $workspace, $language);
      }
      $this->app->Location->execute('index.php?module=wiki&action=edit&cmd='.$cmd);
    }

    if($command === 'changelanguage') {
      $language = $this->app->Secure->GetPOST('language');
      $this->app->User->SetParameter('wiki_language', $language);
      header('Content-Type: application/json');
      echo json_encode(['success' => 1]);
      $this->app->ExitXentral();
    }
    if($command === 'changeworkspace') {
      $workspace = $this->app->Secure->GetPOST('workspace');
      $this->app->User->SetParameter('wiki_workspace', $workspace);
      header('Content-Type: application/json');
      echo json_encode(['success' => 1]);
      $this->app->ExitXentral();
    }

    if(!empty($workspace)) {
      $this->app->User->SetParameter('wiki_workspace', $workspace);
      $this->app->User->SetParameter('wiki_language','');
      $this->app->Location->execute('index.php?module=wiki&action=new&cmd='.$cmd);
    }


    $submit = $this->app->Secure->GetPOST('submit');
    $newname = $this->app->Secure->GetPOST('newname');
    $this->WikiMenu();

    if($submit!='')
    {
      // pruefen ob name passt
      $workspace = $this->getUserWorkspace();
      $language = $this->getUserLanguage();
      $checkname = $this->app->DB->Select(
        sprintf(
          "SELECT name FROM wiki WHERE name='%s' AND wiki_workspace_id = %d AND language = '%s' LIMIT 1",
          $newname, $workspace, $language
        )
      );

      if($checkname == $newname)
      {
        $this->app->Tpl->Set(
          'MESSAGE',
          "<div class=\"error\">Diesen Namen gibt es bereits. Bitte w&auml;hlen Sie einen anderen Namen.</div>"
        );
      }
      else if($newname=='')
      {
        $this->app->Tpl->Set(
          'MESSAGE',
          "<div class=\"error\">Bitte geben Sie eine Namen an!</div>"
        );
      } else {
        // alle 
        $newname = str_replace(' ','_',$newname);
        $this->app->DB->Insert(
          sprintf(
            "INSERT INTO wiki (name,content, wiki_workspace_id, language) 
            VALUES ('%s','', %d,'%s')",
            $newname, $workspace, $language
          )
        );
        $newId = $this->app->DB->GetInsertID();
        $this->saveArticleInUserdata($newId);
        $this->app->Location->execute('index.php?module=wiki&action=edit&cmd='.$newname);
      }
    }
    $this->app->Tpl->Set(
      'TAB1',
      "<form action=\"\" method=\"post\">Neuer Name: <input type=\"text\" name=\"newname\" value=\"\" size=\"50\">&nbsp;<input type=\"submit\" value=\"anlegen\" name=\"submit\"></form>"
    );
    $this->app->Tpl->Set('TABTEXT','Wiki');
    $this->showWorkspaces();
    $this->showLanguages();
    $this->app->Tpl->Parse('PAGE','wiki_new.tpl');
  }

  function WikiRename()
  {
    $submit = $this->app->Secure->GetPOST('submit');
    $cmd = $this->app->Secure->GetGET('cmd');
    $newname = $this->app->Secure->GetPOST('newname');
    $this->WikiMenu();

    if($submit!='')
    {
      // pruefen ob name passt
      $workspace = $this->getUserWorkspace();
      $language = $this->getUserLanguage();
      $checkname = $this->app->DB->Select(
        sprintf(
          "SELECT name FROM wiki WHERE name='%s' AND wiki_workspace_id = %d AND `language` = '%s' LIMIT 1",
          $newname, $workspace, $language
        )
      );

      if($checkname == $cmd)
      {
        $this->app->Tpl->Set(
          'MESSAGE',
          "<div class=\"error\">Diesen Namen gibt es bereits. Bitte w&auml;hlen Sie einen anderen Namen.</div>"
        );
        $cmd = $newname;
      }
      else if($newname=='')
      {
        $this->app->Tpl->Set(
          'MESSAGE',
          "<div class=\"error\">Bitte geben Sie eine Namen an!</div>"
        );
        $cmd = $newname;
      } else {
        // alle 
        $newname = str_replace(' ','_',$newname);
        $this->app->DB->UPDATE(
          sprintf(
            "UPDATE wiki SET name='%s' WHERE name='%s' AND wiki_workspace_id = %d AND `language` = '%s' LIMIT 1",
            $newname, $cmd,  $workspace, $language
          )
        );
        if(empty($workspace)){
          $this->app->DB->UPDATE(
            "UPDATE datei_stichwoerter 
            SET parameter='$newname' 
            WHERE parameter='$cmd' AND (parameter2 = '' OR parameter2 = '0') AND objekt='Wiki'"
          );
        }
        else {
          $this->app->DB->UPDATE(
            sprintf(
              "UPDATE datei_stichwoerter SET parameter='%s' WHERE parameter='%s' AND parameter2 = '%d' AND objekt='Wiki'",
              $newname, $cmd, $workspace
            )
          );
        }
        $this->app->Location->execute('index.php?module=wiki&action=list&cmd='.$newname);
      }
    }

    $this->app->Tpl->Set(
      'TAB1',
      "<form action=\"\" method=\"post\">Neuer Name: <input type=\"text\" name=\"newname\" value=\"$cmd\" size=\"50\">&nbsp;<input type=\"submit\" value=\"umbenennen\" name=\"submit\"></form>"
    );
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  /**
   * @return string
   */
  public function getUserLanguage()
  {
    $language = $this->app->User->GetParameter('wiki_language');
    if(empty($language)) {
      return '';
    }
    $languages = $this->app->erp->GetSelectSprachenListe();
    unset($languages[0]);
    if(isset($languages[$language])) {
      return $language;
    }

    return '';
  }

  /**
   * @return int
   */
  public function getUserWorkspace()
  {
    $workspace = $this->app->User->GetParameter('wiki_workspace');
    if(empty($workspace)) {
      return 0;
    }
    $workspace  = (int)$this->app->DB->Select(
      sprintf(
        'SELECT id FROM wiki_workspace WHERE id = %d',
        $workspace
      )
    );
    if(empty($workspace)) {
      $this->app->User->SetParameter('wiki_workspace', '');
      return 0;
    }

    return $workspace;
  }


  public function WikiCreate()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd !='') {
      $workspace = $this->getUserWorkspace();
      $wikiname = $this->app->DB->Select(
        sprintf(
          "SELECT name FROM wiki WHERE name='%s' AND wiki_workspace_id = %d LIMIT 1",
          $cmd,
          $workspace
        )
      );

      if($wikiname != $cmd) {
        $this->app->DB->Insert(
          sprintf(
            "INSERT INTO wiki (name,content, wiki_workspace_id) VALUES ('%s','', %d)",
            $cmd, $workspace
          )
        );
        $this->app->Location->execute('index.php?module=wiki&action=edit&cmd='.$cmd);
      }
      $this->app->Location->execute('index.php?module=wiki&action=edit&cmd='.$cmd);
    }
  }

  /**
   * @param string     $site
   * @param string     $content
   * @param string|int $workspace
   * @param string     $language
   * @param $bool      $dontSaveInUserSpace
   *
   * @return int
   */
  public function saveArticle($site, $content, $workspace = '', $language = '', $dontSaveInUserSpace = false)
  {
    if(empty($site)) {
      return 0;
    }
    $workspaceId = $this->getWorkspaceFromName($workspace);
    $article = $this->getArticleByName($site, $workspaceId, $language);
    $parent = null;
    if(empty($article)) {
      $parent = $this->getArticleByName($site, $workspaceId);
      if(!empty($parent)) {
        $article = $this->getArticleByParentId($parent['id'], $workspaceId, $language);
      }
    }
    if(empty($article)) {
      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO wiki (name, parent_id, wiki_workspace_id, language, content)
            VALUES ('%s', %d, %d, '%s', '%s')",
          $this->app->DB->real_escape_string($site),
          empty($parent)?0:$parent['id'],
          $workspaceId,
          $this->app->DB->real_escape_string($language),
          $this->app->DB->real_escape_string($content)
        )
      );
      $id = (int)$this->app->DB->GetInsertID();
      if($dontSaveInUserSpace) {
        return $id;
      }
      if($workspaceId > 0) {
        $this->saveArticleInUserdata($id);
      }
      return $id;
    }

    $this->app->DB->Update(
      sprintf(
        'UPDATE wiki SET lastcontent = content WHERE id = %d',
        $article['id']
      )
    );
    $this->app->DB->Update(
      sprintf(
        "UPDATE wiki SET content = '%s' WHERE id = %d",
        $this->app->DB->real_escape_string($content), $article['id']
      )
    );
    if($dontSaveInUserSpace) {
      return (int)$article['id'];
    }
    if($workspaceId > 0) {
      $this->saveArticleInUserdata((int)$article['id']);
    }

    return (int)$article['id'];
  }

  /**
   * @param string $site
   * @param int    $workspaceId
   * @param string $language
   *
   * @return array|null
   */
  public function getArticleByName($site, $workspaceId = 0, $language = '')
  {
    if($language === 'any') {
      return $this->app->DB->SelectRow(
        sprintf(
          "SELECT * FROM wiki WHERE name = '%s' AND wiki_workspace_id = %d ORDER BY language = '' DESC LIMIT 1",
          $this->app->DB->real_escape_string($site), $workspaceId
        )
      );
    }

    return $this->app->DB->SelectRow(
      sprintf(
        "SELECT * FROM wiki WHERE name = '%s' AND wiki_workspace_id = %d AND language = '%s' LIMIT 1",
        $this->app->DB->real_escape_string($site), $workspaceId, $this->app->DB->real_escape_string($language)
      )
    );
  }

  /**
   * @param int    $parentId
   * @param int    $workspaceId
   * @param string $language
   *
   * @return array|null
   */
  public function getArticleByParentId($parentId, $workspaceId, $language)
  {
    return $this->app->DB->SelectRow(
      sprintf(
        "SELECT * FROM wiki WHERE parent_id = %d AND wiki_workspace_id = %d AND language = '%s' LIMIT 1",
        $parentId, $workspaceId, $this->app->DB->real_escape_string($language)
      )
    );
  }

  /**
   * @param string|int $name
   *
   * @return int
   */
  public function getWorkspaceFromName($name)
  {
    if(empty($name)) {
      return 0;
    }
    if(is_numeric($name) && $this->app->DB->Select(
      sprintf(
        'SELECT id FROM wiki_workspace WHERE id = %d',
        $name
      )
      )
    ) {
      return (int)$name;
    }

    return (int)$this->app->DB->Select(
      sprintf(
        "SELECT id FROM wiki_workspace WHERE name = '%s' LIMIT 1",
        $this->app->DB->real_escape_string($name)
      )
    );
  }

  /**
   * @param int    $articleId
   * @param string $comment
   * @param int    $notify
   *
   * @return int
   */
  public function saveComment($articleId, $comment, $notify = 0)
  {
    if($articleId <= 0) {
      return 0;
    }
    $article = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM wiki WHERE id = %d',
        $articleId
      )
    );
    if(empty($article)) {
      return 0;
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO wiki_changelog (wiki_id, `comment`, content, created_by, notify) VALUES (%d, '%s', '%s','%s', %d)",
        $articleId,
        $comment,
        $this->app->DB->real_escape_string($article['content']),
        $this->app->DB->real_escape_string($this->app->User->GetName()),
        $notify
      )
    );

    return (int)$this->app->DB->GetInsertID();
  }

  /**
   * @param int $articleId
   *
   * @return array
   */
  public function getArticlesById($articleId)
  {
    return array_unique(
      array_merge(
        [$articleId],
        $this->app->DB->SelectFirstCols(
          sprintf('SELECT id FROM wiki WHERE parent_id = %d', $articleId)
        ),
        $this->app->DB->SelectFirstCols(
          sprintf('SELECT parent_id FROM wiki WHERE id = %d AND parent_id > 0', $articleId)
        )
      )
    );
  }

  /**
   * @param int $articleId
   */
  public function notify($articleId)
  {
    $article = $this->app->DB->SelectRow(sprintf('SELECT id, name FROM wiki WHERE id = %d', $articleId));
    $articleIds = $this->getArticlesById($articleId);
    $users = $this->app->DB->SelectFirstCols(
      sprintf(
        'SELECT `user_id` FROM wiki_subscription WHERE wiki_id IN (%s) AND active = 1',
        implode(',', $articleIds)
      )
    );
    if(empty($users)) {
      return;
    }
    /** @var NotificationService $notificationService */
    $notificationService = $this->app->Container->get('NotificationService');
    foreach($users as $user) {
      try {
        $notificationService->create(
          $user,
          NotificationService::TYPE_NOTICE, 'Wikibenachrichtung',
          'Änderung im Artikel ' . $article['name']
        );
      }
      catch(Exception $e) {

      }
    }
  }

  /**
   * @return array
   */
  public function getActiveWorkspaces()
  {
    return $this->app->DB->SelectPairs(sprintf('SELECT id, name FROM wiki_workspace WHERE active = 1 ORDER BY name'));
  }

  /**
   * @param int  $aricleId
   * @param bool $active
   */
  public function subscibe($articleId, $active = true)
  {
    $articleIds = $this->getArticlesById($articleId);
    if($active) {
      $this->app->DB->Insert(
        sprintf(
          'INSERT INTO wiki_subscription 
          (`user_id`, `wiki_id`, `active`)
          SELECT %d as `user_id`, w.`id`, 1 as `active`
          FROM wiki as w
          LEFT JOIN wiki_subscription AS ws ON w.id = ws.wiki_id AND ws.user_id = %d
          WHERE ISNULL(ws.id) AND w.id IN (%s)
           ',
          $this->app->User->GetID(), $this->app->User->GetID(), implode(',', $articleIds)
        )
      );
      $this->app->DB->Update(
        sprintf(
          'UPDATE wiki_subscription SET active = 1 WHERE user_id = %d AND wiki_id IN (%s)',
          $this->app->User->GetID(), implode(',', $articleIds)
        )
      );
      return;
    }
    $this->app->DB->Update(
      sprintf(
        'UPDATE wiki_subscription SET active = 0 WHERE user_id = %d AND wiki_id IN (%s)',
        $this->app->User->GetID(), implode(',', $articleIds)
      )
    );
  }

  public function WikiList()
  {
    $command = $this->app->Secure->GetGET('command');
    if($command === 'install') {
      $this->runInstallFromJson();
      header('Content-Type: application/json');
      echo json_encode(['success' => 1]);
      $this->app->ExitXentral();
    }

    $cmd = $this->app->Secure->GET['cmd'];
    $workspace= $this->app->Secure->GET['workspace'];
    if(!empty($workspace)) {
      $this->app->User->SetParameter('wiki_workspace', $workspace);
      $this->app->User->SetParameter('wiki_language','');
      $this->app->Location->execute('index.php?module=wiki&action=list&cmd='.$cmd);
    }
    $id = $this->app->Secure->GetGET('id');

    if(empty($command) && empty($cmd) && !empty($id)) {
      $wiki = $this->app->DB->SelectRow(
        sprintf(
          'SELECT id, name, wiki_workspace_id, language, parent_id FROM wiki WHERE id = %d',
          $id
        )
      );
      if(!empty($wiki)) {
        $this->app->User->SetParameter('wiki_workspace', $wiki['wiki_workspace_id']);
        $this->app->User->SetParameter('wiki_language',$wiki['language']);
        $this->app->Location->execute('index.php?module=wiki&action=list&cmd='.$wiki['name']);
      }
    }
    if($command === 'subscribe') {
      $site = $this->app->Secure->GetPOST('site');
      $article = $this->getArticleByName($site);
      $this->subscibe($article['id']);
      header('Content-Type: application/json');
      echo json_encode(['success' => !empty($article['id'])?1:0]);
      $this->app->ExitXentral();
    }
    if($cmd === 'unsubscribe') {
      $site = $this->app->Secure->GetPOST('site');
      $article = $this->getArticleByName($site);
      $this->subscibe($article['id'], false);
      header('Content-Type: application/json');
      echo json_encode(['success' => !empty($article['id'])?1:0]);
      $this->app->ExitXentral();
    }

    if($command === 'savearticle') {
      $site = $this->app->Secure->GetPOST('site');
      $workspace = $this->app->Secure->GetPOST('workspace');
      $language = $this->app->Secure->GetPOST('language');
      $content = $this->app->Secure->POST['content'];
      $comment = $this->app->Secure->GetPOST('comment');
      $notify = $this->app->Secure->GetPOST('notify');
      $articleId = $this->saveArticle($site, $content, $workspace, $language);
      if(!empty($comment)) {
        $this->saveComment($articleId, $comment, $notify);
        $this->saveChangeLogInUserdata($this->app->DB->GetInsertID());
      }
      if($notify) {
        $this->notify($articleId);
      }
      $this->saveArticleInUserdata($articleId);
      $url = 'index.php?module=wiki&action=list&cmd='.$site;
      header('Content-Type: application/json');
      echo json_encode(['success' => !empty($articleId)?1:0,'url'=>$url]);
      $this->app->ExitXentral();
    }
    if($command === 'loadarticle') {
      $html = '';
      $site = $this->app->Secure->GetPOST('site');
      $workspace = $this->app->Secure->GetPOST('workspace');
      $this->app->User->SetParameter('wiki_workspace', $workspace);
      $language = $this->app->Secure->GetPOST('language');
      $this->app->User->SetParameter('wiki_language', $language);
      $article = $this->getArticleByName($site, (int)$workspace, $language);
      if(!empty($article)) {
        $content = $article['content'];
        $wikiparser = new WikiParser();
        $content = $wikiparser->parse($content);
        $html = html_entity_decode($content);
      }
      header('Content-Type: application/json');
      echo json_encode(
        [
          'site'      => $site,
          'workspace' => $workspace,
          'language'  => $language,
          'artice_id' => $articleId,
          'html'      => $html
        ]
      );
      $this->app->ExitXentral();
    }
    if($cmd !='')
    {
      $workspace = $this->getUserWorkspace();
      $language = $this->getUserLanguage();
      $wikiname = $this->app->DB->Select(
        sprintf(
          "SELECT name FROM wiki WHERE name='%s' AND wiki_workspace_id = %d LIMIT 1",
          $cmd, $workspace
        )
      );

      if($wikiname == $cmd)
      {
        $content = $this->app->DB->Select(
          sprintf(
            "SELECT content FROM wiki WHERE name='%s' AND wiki_workspace_id = %d ORDER BY language = '%s' DESC LIMIT 1",
            $cmd, $workspace, $language
          )
        );
        $str = $this->app->erp->ReadyForPDF($content);
        $wikiparser = new WikiParser();
        if (preg_match('/(<[^>].*?>)/e', $str))  	
        {
          $str=preg_replace('#(href)="([^:"]*)(?:")#','$1="index.php?module=wiki&action=list&cmd=$2"',$str);
          $content = $str;
        } else {
          $content = $wikiparser->parse($content);
          //$index = $wikiparser->BuildIndex();
        }

        $content = html_entity_decode($content);

        //if($index!==false) {
          //$this->app->Tpl->Set('INDEX', $index);
          //$this->app->Tpl->Parse('WIKIINDEX', 'wiki_index.tpl');
        //}

        //Pruefe ob es die Seite Navigation gibt
        $navigation = $this->app->DB->Select(
          sprintf(
            "SELECT content FROM wiki WHERE name='Navigation' AND wiki_workspace_id = %d LIMIT 1",
            $workspace
          )
        );

        if($navigation!='' && $cmd!=='Navigation')
        {
          $navigation = $this->app->erp->ReadyForPDF($navigation);
          $navigation = str_replace(['https://','http://'],'',$navigation);
          $navigation=preg_replace(
            '#(href)="([^:"]*)(?:")#','$1="index.php?module=wiki&action=list&cmd=$2"',
            $navigation
          );
          $content = "<table width=100%><tr valign=top><td width=200><div id=\"wikinav\"><ul><li style=\"color:white;font-weight:bold;padding-bottom:5px;\">Navigation<br></li></ul>$navigation</div></td><td style=\"padding:0px 15px;\">$content</td></tr></table>";
        }

        $this->app->Tpl->Set('TAB1',$content); // TODO Wiki Parser!!!!
      } else {
        $this->WikiCreateDialog();
      }
      $this->app->Tpl->Set('WIKISITE',$cmd);
    } else {
      // hauptseite
      $this->app->Location->execute('index.php?module=wiki&action=list&cmd=Hauptseite');
    }

    $this->app->Tpl->Add(
      'WIKISUBMENUBEFOREWORKSPACE',
      '<table class="wikilabel" id="tabwikilist"><tr class="labeltr">
													<td><label for="labels">{|Labels|}:</label></td>
													<td></td>
													<td><a href="#" class="label-manager" data-label-column-number="2" data-label-reference-id="[ID]" data-label-reference-table="wiki"><img src="./themes/new/images/label.svg"></a></td>
												</tr></table>
      <label for="language">{|Sprache|}:</label> <td colspan="2">
																<select id="language" name="language">
																		<option value="">{|Default|}</option>
																		'.$this->showLanguages(true).'
																</select>
														</td>'
    );

    if($this->app->erp->RechteVorhanden('wiki','edit')){
      $this->app->Tpl->Add(
        'WIKISUBMENUAFTEREDIT',
        '<a href="index.php?module=wiki&action=edit&cmd='.$cmd.'"><img class="submenusettingsicon" src="./themes/new/images/edit.svg" alt="Einstellungen" /></a>'
      );
      $this->app->Tpl->Add(
        'WIKIICONS',
        "<a href=\"index.php?module=wiki&action=edit&cmd=$cmd\"><img src=\"./themes/new/images/edit.svg\"></a>&nbsp;"
      );
    }
    if($this->app->erp->RechteVorhanden('wiki','rename')){
      $this->app->Tpl->Add(
        'WIKIICONS',
        "<a href=\"index.php?module=wiki&action=rename&cmd=$cmd\"><img src=\"./themes/new/images/forward.svg\"></a>&nbsp;"
      );
    }
    if($this->app->erp->RechteVorhanden('wiki','delete')){
      $this->app->Tpl->Add(
        'WIKIICONS',
        "<a href=\"index.php?module=wiki&action=delete&id=$id\"><img src=\"./themes/new/images/delete.svg\"></a>&nbsp;"
      );
    }

    $addtionalcspheader = ' '.
      str_replace([';','"'],'',$this->app->erp->Firmendaten('additionalcspheader')).' ';
    $this->app->Tpl->Add('ADDITIONALCSPHEADER', $addtionalcspheader);
    $this->WikiMenu();
    $this->showWorkspaces();

    if($this->app->erp->GetKonfiguration('wiki_install')) {
      $this->app->Tpl->Add('TAB1','<input type="hidden" id="wikiinstall" />');
    }
    $this->app->Tpl->Parse('PAGE','wiki_list.tpl');
  }

  /**
   * @param bool $return
   *
   * @return string
   */
  public function showLanguages($return = false)
  {
    $languages = $this->app->erp->GetSelectSprachenListe();
    $selLanguage = $this->app->User->GetParameter('wiki_language');
    unset($languages[0]);
    $html = '';
    foreach($languages as $iso => $language) {
      $html .= '<option value="'.$iso.'"'.($selLanguage === $iso?' selected="selected" ':'').'>'.
        $language
        .'</option>';
    }
    if(!$return) {
      $this->app->Tpl->Add('SELLANGUAGE', $html);
    }

    return $html;
  }

  public function showWorkspaces()
  {
    $this->app->Tpl->Add('SELWORKSPACE', $this->getWorkspaceOptions());
  }

  /**
   * @return string
   */
  public function getWorkspaceOptions()
  {
    $return = '';
    $workspaces = $this->getActiveWorkspaces();
    $selWorkspace = (int)$this->app->User->GetParameter('wiki_workspace');
    foreach($workspaces as $workspaceId => $workspace) {
      $return .=
        '<option value="'.$workspaceId.'"'.($selWorkspace == $workspaceId?' selected="selected" ':'').'>'.
        $workspace .'</option>';
    }

    return $return;
  }

  public function WikiMenu()
  {
    $cmd = $this->app->Secure->GET['cmd'];
    $action = $this->app->Secure->GetGET('action');
    $id = $this->app->Secure->GetGET('id');
    if(in_array($action,['faq','changelog','edit'])){
      $this->app->Tpl->Set('WORSPACEDISABLED', 'disabled="disables" ');
    }
    if($action !== 'dateien'){
      $this->app->Tpl->Add('SELWORKSPACEOPTIONS', $this->getWorkspaceOptions());
      $this->app->Tpl->Parse('WIKISUBMENU', 'wiki_submenu.tpl');
    }
    if($action === 'settings'){
      $this->app->erp->MenuEintrag(
        'index.php?module=wiki&action=settings',
        '{|Workspaces|}'
      );
      $this->app->erp->MenuEintrag(
        'index.php?module=wiki&action=list&cmd=Hauptseite',
        '{|Zur&uuml;ck zur &Uuml;bersicht|}'
      );
      return;
    }
    $workspace = $this->getUserWorkspace();
    $language = $this->getUserLanguage();
    if($cmd!='' && $id<=0) {
      $id = $this->app->DB->Select(
        sprintf(
          "SELECT id 
          FROM wiki 
          WHERE name='%s' 
          ORDER BY wiki_workspace_id = %d DESC, language = '%s' DESC, language = '' DESC
          LIMIT 1",
          $cmd, $workspace, $language
        )
      );
    }

    if(is_numeric($id))
    {
      $wikiname = $this->app->DB->Select("SELECT name FROM wiki WHERE id='$id'");
    } else {
      $wikiname = $this->app->Secure->GET['cmd'];
    }
    $tabName = $wikiname;
    if(in_array($tabName, ['Hauptseite','hauptseite'])) {
      $tabName = 'Startsteite';
    }
    if($tabName !== 'Startseite') {
      $tabName = 'Inhalt';
    }
    if($action==='edit'){
      $this->app->erp->MenuEintrag("index.php?module=wiki&action=edit&cmd=$wikiname", $tabName);
    }
    else{
      $this->app->erp->MenuEintrag("index.php?module=wiki&action=list&cmd=$wikiname", $tabName);
    }


    $this->app->Tpl->Set('KURZUEBERSCHRIFT2',$wikiname);

    $anzahldateien = $this->app->erp->AnzahlDateien('Wiki',$id);
    if($anzahldateien > 0) {
      $anzahldateien = ' ('.$anzahldateien.')';
    } else {
      $anzahldateien='';
    }

    $wiki = $this->getArticleByName($cmd, $workspace, $language);
    if(empty($wiki)) {
      $wiki = $this->getArticleByName($cmd, $workspace,'any');
    }
    $ids = [0];
    if(!empty($wiki['id'])) {
      $ids = $this->app->DB->SelectFirstCols(
        sprintf(
          'SELECT id FROM wiki WHERE parent_id = %d OR id = %d',
          $wiki['id'], $wiki['id']
        )
      );
      $ids = $this->app->DB->SelectFirstCols(
        sprintf(
          'SELECT id FROM wiki WHERE parent_id IN (%s) OR id IN (%s)',
          implode(',', $ids), implode(',', $ids)
        )
      );
    }
    $cmddateiid = $this->app->DB->Select("SELECT id FROM wiki WHERE name='$cmd' AND name!='' LIMIT 1");
    $faqanz = $this->app->DB->Select(
      sprintf(
        'SELECT COUNT(id) FROM wiki_faq WHERE wiki_id IN (%s) AND wiki_id > 0',
        implode(', ', $ids)
      )
    );
    if($faqanz > 0) {
      $faqanz = ' ('.$faqanz.')';
    }else{
      $faqanz = '';
    }
    $changeanz = $this->app->DB->Select(
      sprintf(
        'SELECT COUNT(id) FROM wiki_changelog WHERE  wiki_id IN (%s) AND wiki_id > 0',
        implode(', ', $ids)
      )
    );
    if($changeanz > 0) {
      $changeanz = ' ('.$changeanz.')';
    }else{
      $changeanz = '';
    }
    $this->app->erp->MenuEintrag(
      'index.php?module=wiki&action=faq&cmd='.$wikiname.(empty($wiki)?'':'&id='.$wiki['id']),
      'FAQs'.$faqanz
    );
    $this->app->erp->MenuEintrag(
      'index.php?module=wiki&action=changelog&cmd='.$wikiname.(empty($wiki)?'':'&id='.$wiki['id']),
      'Historie'.$changeanz
    );
    $this->app->erp->MenuEintrag('index.php?module=wiki&action=dateien&id='.$cmddateiid,'Dateien'.$anzahldateien);


//    $this->app->erp->MenuEintrag("index.php?module=wiki&action=edit&name=$cmd","bearbeiten");
//    $this->app->erp->MenuEintrag("index.php?module=wiki&action=rename&name=$cmd","umbenennen");
    $this->app->erp->MenuEintrag("index.php?module=wiki&action=new","{|Neue Seite anlegen|}");
//    $this->app->erp->MenuEintrag("index.php?module=wiki&action=delete&name=$cmd","l&ouml;schen");
    if($tabName === 'Startseite'){
      $this->app->erp->MenuEintrag("index.php?module=wiki&action=alle", "{|Alle Seiten anzeigen|}");
    }

    $this->app->erp->MenuEintrag("index.php?module=wiki&action=list&cmd=Hauptseite","{|Zur&uuml;ck zur &Uuml;bersicht|}");
    if($action === 'list' && !empty($wiki['id'])) {
      $this->app->Tpl->Set('ID', $wiki['id']);
    }
  }

  public function WikiEdit()
  {

    $this->WikiMenu();
    $cmd = $this->app->Secure->GET['cmd'];
    $workspace= $this->app->Secure->GET['workspace'];
    if(!empty($workspace)) {
      $this->app->User->SetParameter('wiki_workspace', $workspace);
      $this->app->User->SetParameter('wiki_language','');
      $this->app->Location->execute('index.php?module=wiki&action=edit&cmd='.$cmd);
    }

    //$content = $this->app->Secure->GetPOST("content");
    $content = $this->app->DB->real_escape_string($this->app->Secure->POST['content']);
    $content = str_replace(array('<','>'),array('&lt;','&gt;'),$content);
    $startseite_link = '';
    $workspace = $this->getUserWorkspace();
    $language = $this->getUserLanguage();
    if($cmd !='')
    {
      // check if is valid page
      $wikiname = $this->app->DB->Select(
        sprintf(
          "SELECT name FROM wiki WHERE name='%s' AND wiki_workspace_id = %d AND language = '%s' LIMIT 1",
          $cmd, $workspace, $language
        )
      );

      if(empty($wikiname) && !empty($language)) {
        $wikiname = $this->app->DB->Select(
          sprintf(
            "SELECT name FROM wiki WHERE name='%s' AND wiki_workspace_id = %d AND language = '%s' LIMIT 1",
            $cmd, $workspace, ''
          )
        );
        if(!empty($wikiname)) {
          $this->app->DB->Insert(
            sprintf(
              "INSERT INTO wiki (name,wiki_workspace_id,language,content)
                VALUES ('%s',%d,'%s','')",
              $cmd, $workspace, $language
            )
          );
          if($this->app->DB->GetInsertID() > 0) {
            $wikiname = $cmd;
          }
        }
      }

      if(!empty($wikiname)) {
        $id = $this->app->DB->Select(
          sprintf(
            "SELECT id FROM wiki WHERE name='%s' AND wiki_workspace_id = %d AND language = '%s' LIMIT 1",
            $cmd, $workspace, ''
          )
        );
        $this->app->Tpl->Set('ID', $id);
      }

      $home_wikis = $this->app->DB->SelectArr("SELECT target FROM accordion");
      $found = false;
      if(!empty($home_wikis)){
        foreach($home_wikis as $home_row) {
          if($home_row['target'] == $wikiname){
            $found = true;
            break;
          }
        }
      }
      if($found) {
        $startseite_link = "<input type=\"submit\" name=\"submitAndGoBack\" value=\"Speichern und zurück zur Startseite\" name=\"submit\">";
      }


      if($wikiname != $cmd)	
      {
        // seite gibt es nicht!!!	
      } else { // wenn es seite gibt speichern
        if($this->app->Secure->GetPOST('submit')!='' || $this->app->Secure->GetPOST('submitAndGoBack')!='')
        {
          $articleId = $this->app->DB->Select(
            sprintf(
              "SELECT id FROM wiki WHERE name = '%s' AND wiki_workspace_id = %d AND `language` = '%s' LIMIT 1",
              $cmd, $workspace, $language
            )
          );
          $this->app->DB->Update(
            sprintf(
              "UPDATE wiki SET lastcontent=content WHERE id = %d LIMIT 1",
              $articleId
            )
          );
          $this->app->DB->Update(
            sprintf(
              "UPDATE wiki SET content='%s' WHERE id = %d LIMIT 1",
              $content, $articleId
            )
          );
          $this->saveArticleInUserdata($articleId);
          if($this->app->Secure->GetPOST('submitAndGoBack')!=''){
            $this->app->Location->execute('index.php?module=welcome&action=start');
          }
          else{
            $this->app->Location->execute('index.php?module=wiki&action=list&cmd='.$cmd);
          }
        }
        $articleId = $this->app->DB->Select(
          sprintf(
            "SELECT id FROM wiki WHERE name = '%s' AND wiki_workspace_id = %d AND `language` = '%s' LIMIT 1",
            $cmd, $workspace, $language
          )
        );
        $content = $this->app->DB->Select(
          sprintf(
            "SELECT content FROM wiki WHERE id = %d LIMIT 1",
            $articleId
          )
        );
        if (!preg_match('/(<[^>].*?>)/e', $str))  	
        {
          $wikiparser = new WikiParser();
          $content = $wikiparser->parse($content);
          //$index = $wikiparser->BuildIndex();
        }


      }
    } else {
      // Seite fehlt!!! soll diese angelegt werden?
      $this->WikiCreateDialog();
    }

    //$this->app->YUI->CkEditor('content','wiki',array('wikiname' => $cmd, 'height'=>'50vh','ckeditor5'=>true));
    $this->app->YUI->CkEditor('content','wiki',array('wikiname' => $cmd, 'height'=>'50vh'));

    $this->app->Tpl->Set(
      'TAB1',
      "<textarea rows=\"25\" cols=\"120\" name=\"content\" id=\"content\">$content</textarea><br>
        $startseite_link"
    );
    $this->app->Tpl->Set('CMD', $cmd);
    $this->app->Tpl->Set('WIKISITE', $cmd);
    $this->app->Tpl->Set('KURZUEBERSCHRIFT2',"$cmd - Seite bearbeiten");
    $languages = $this->app->erp->GetSelectSprachenListe();
    $selLanguage = $this->getUserLanguage();
    $this->app->Tpl->Set('LANUAGEISO', $selLanguage);
    if(empty($selLanguage) || $selLanguage == '0' || !isset($languages[$selLanguage])) {
      $this->app->Tpl->Set('LANGUAGE', 'Default');
    }
    else{
      $this->app->Tpl->Set('LANGUAGE', $languages[$selLanguage]);
    }
    $workspace = $this->getUserWorkspace();
    $this->app->Tpl->Set('WORKSPACEID', $workspace);
    if(empty($workspace)) {
      $this->app->Tpl->Set('WORKSPACE', 'Default');
    }
    else{
      $this->app->Tpl->Set(
        'WORKSPACE',
        $this->app->DB->Select(
          sprintf('SELECT name FROM wiki_workspace WHERE id = %d', $workspace)
        )
      );
    }

    $addtionalcspheader = ' '.str_replace([';','"'],'',$this->app->erp->Firmendaten('additionalcspheader')).' ';
    $this->app->Tpl->Add('ADDITIONALCSPHEADER', $addtionalcspheader);

    $this->app->Tpl->Parse('PAGE','wiki_edit.tpl');
  }


}

