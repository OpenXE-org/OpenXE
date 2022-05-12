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
use Xentral\Components\Http\JsonResponse;

class Artikelbaum
{
  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public static function TableSearch($app, $name, $erlaubtevars)
  {
    switch($name)
    {
      case 'artikelbaum_artikel':
        //$allowed['denalunion'] = array('auftragsstatus');
        $heading = array('Nummer','Artikel','Projekt','');
        $width = array('10%','80%','10%');
        $findcols = array('a.nummer', 'a.name_de','p.abkuerzung','a.id');
        $searchsql = array('a.nummer', 'a.name_de','p.abkuerzung');
        
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc = 1;

        $id = (int)$app->Secure->GetGET('id');
        $menu = '<table cellpadding=0 cellspacing=0>';
          $menu .= '<tr>';
            $menu .= '<td nowrap>';
              $menu .= '<a href="index.php?module=artikel&action=edit&id=%value%" target="_blank">';
                $menu .= '<img src="themes/'.$app->Conf->WFconf['defaulttheme'].'/images/edit.svg" border="0">';
              $menu .= '</a>';
            $menu .= '</td>';
          $menu .= '</tr>';
        $menu .= '</table>';
        
        //$menucol = 5;
        //$moreinfo = true;
        $sql = 'SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer, a.name_de,p.abkuerzung,a.id
        FROM artikel AS a 
        LEFT JOIN projekt AS p ON p.id = a.projekt 
        LEFT JOIN artikelbaum_artikel AS aba ON aba.artikel = a.id AND aba.kategorie = '.$id.' AND aba.kategorie != 0';
        $where = " (a.typ = '".$id."_kat' OR not isnull(aba.id)) AND a.geloescht = 0 ".$app->erp->ProjektRechte();
        $groupby = "GROUP BY a.id";
        
        $count = 'SELECT COUNT(DISTINCT a.id) 
                  FROM artikel AS a 
                  LEFT JOIN projekt AS p ON p.id = a.projekt 
                  LEFT JOIN artikelbaum_artikel AS aba ON aba.artikel = a.id AND aba.kategorie = '.$id.' AND aba.kategorie != 0 
                  WHERE '.$where;
            
      break;
    }
    
    $erg = [];

    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v)) {
        $erg[$v] = $$v;
      }
    }
    return $erg; 
  }

  /**
   * Artikelbaum constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler("list","ArtikelbaumList");
    $this->app->ActionHandler("change", "ArtikelbaumChange");
    $this->app->ActionHandler("baumajax", "ArtikelbaumBaumajax");
    $this->app->ActionHandler("detail", "ArtikelbaumDetail");
    $this->app->ActionHandler("loeschen", "ArtikelbaumLoeschen");

    $this->app->DefaultActionHandler("list");

    $this->app->erp->Headlines('Artikelbaum');

    $this->app->ActionHandlerListen($app);
  }
  
  public function Install() {
    $this->app->erp->CheckTable('artikelbaum_artikel');
    $this->app->erp->CheckColumn('id','int(11)','artikelbaum_artikel','NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('artikel','INT(11)','artikelbaum_artikel','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('kategorie','INT(11)','artikelbaum_artikel','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('haupt','TINYINT(1)','artikelbaum_artikel','DEFAULT 0 NOT NULL');

    $this->app->erp->CheckIndex('artikelbaum_artikel',['artikel', 'kategorie']);
  }
  
  public function ArtikelbaumLoeschen()
  {
    //Rechte
    $this->app->ExitXentral();
  }
  
  public function ArtikelbaumList()
  {
    $this->app->erp->MenuEintrag('index.php?module=artikelbaum&action=list','&Uuml;bersicht');
    $id = $this->app->Secure->GetGET('id');
    $url = 'index.php?module=artikelbaum&action=baumajax&id='.$id;
    $this->app->Tpl->Set('URL',$url);
    
    if($this->app->Secure->GetPOST("speichern"))
    {
      $katid = (int)$this->app->Secure->GetPOST('kat');
      $bezeichnung = $this->app->Secure->GetPOST('bezeichnung');
      if($bezeichnung && $this->app->erp->RechteVorhanden("artikelbaum","change"))
      {
        if($katid > 0)
        {
          $projekt = $this->app->DB->Select("SELECT projekt FROM artikelkategorien WHERE id = $katid LIMIT 1");
          if(!$projekt || $this->app->erp->UserProjektRecht($projekt))
          {
            $this->app->DB->Update("UPDATE artikelkategorien SET bezeichnung = '$bezeichnung' WHERE id = $katid LIMIT 1");
          }
        }else{
          $this->app->DB->Insert("INSERT INTO artikelkategorien (bezeichnung) VALUES ('$bezeichnung')");
        }
      }
    }
    if($this->app->Secure->GetPOST('loeschen'))
    {
      $katid = (int)$this->app->Secure->GetPOST('kat');
      if($katid > 0)
      {
        $projekt = $this->app->DB->Select("SELECT projekt FROM artikelkategorien WHERE id = $katid LIMIT 1");
        if((!$projekt || $this->app->erp->UserProjektRecht($projekt)) && $this->app->erp->RechteVorhanden('artikelbaum', 'loeschen')) {
          $this->app->DB->Update("UPDATE artikelkategorien SET geloescht = 1 WHERE id = $katid LIMIT 1");
        }
      }
    }
    if($this->app->Secure->GetPOST("speichernunter"))
    {
      $parent = (int)$this->app->Secure->GetPOST('kat');
      $bezeichnungunterkategorie = $this->app->Secure->GetPOST('bezeichnungunterkategorie');
      if(!empty($bezeichnungunterkategorie) && $parent > 0 && $this->app->erp->RechteVorhanden('artikelbaum', 'change'))
      {
        $projekt = $this->app->DB->Select("SELECT projekt FROM artikelkategorien WHERE id = $parent LIMIT 1");
        if(!$projekt || $this->app->erp->UserProjektRecht($projekt))
        {
          $this->app->DB->Insert("INSERT INTO artikelkategorien (bezeichnung, parent, projekt) VALUES 
            ('$bezeichnungunterkategorie', $parent,$projekt)");
        }
      }
    }
    
    $this->app->Tpl->Parse('PAGE','artikelbaum_list.tpl');
  }
  
  public function ArtikelbaumDetail()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    if($id)
    {
      $this->app->Tpl->Set('ID', $id);
      $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM artikelkategorien WHERE id = $id LIMIT 1");
      $this->app->Tpl->Add('BEZEICHNUNG',$bezeichnung);
      $this->app->YUI->TableSearch('TABELLE', 'artikelbaum_artikel', 'show','','',basename(__FILE__), __CLASS__);
    }else{
      $this->app->Tpl->Add('VORLOESCHEN','<!--');
      $this->app->Tpl->Add('NACHLOESCHEN','-->');
      $this->app->Tpl->Add('VORUNTERKATEGORIE','<!--');
      $this->app->Tpl->Add('NACHUNTERKATEGORIE','-->');
    }
    
    echo $this->app->Tpl->Parse('return', 'artikelbaum_detail.tpl',true);

    $this->app->ExitXentral();
  }

  /**
   * @return JsonResponse
   */
  public function ArtikelbaumBaumajax()
  {
    $kategorien = null;
    $this->getKategorien($kategorien, 0);
    if($this->app->Secure->GetGET('cmd') === 'suche' && $kategorien) {
      $kategorien[count($kategorien)-1] = [
        'id'          =>0,
        'bezeichnung' => 'zur&uuml;cksetzen',
        'parent'      => 0
      ];
    }
    $baum = $this->getTreeData($kategorien);
    return new JsonResponse($baum);
  }

  /**
   * @param array $categories
   *
   * @return array
   */
  public function getTreeData($categories, $options = null)
  {
    $maxLvl = 0;
    $tree = [];
    if(empty($categories)) {
      return $tree;
    }
    $withCheckBoxes = false;
    $checkedIds = [];
    if(!empty($options['checkbox'])) {
      $withCheckBoxes = true;
    }

    if($withCheckBoxes){
      $checkedIds = !empty($options['checked_ids']) ? $options['checked_ids'] : [];
    }

    foreach($categories as $categoryKey => $category) {
      $ind[$category['id']] = $categoryKey;

      if($category['parent'] == 0) {
        $categories[$categoryKey]['lvl'] = 0;
        $name = 'node'.$categoryKey;
        $$name = new stdClass();
        $$name->id = $category['id'];
        $$name->label = $category['bezeichnung'];
        $$name->checkbox = $withCheckBoxes;
        $$name->radio = false;

        $$name->inode = false;
        if(in_array($category['id'], $checkedIds)) {
          $$name->checked = true;
        }
        $tree[] = $$name;
        $categories[$categoryKey]['node'] = $$name;
      }
      else{
        if(isset($ind[$category['parent']])) {
          $name = 'node'.$categoryKey;
          $$name = new stdClass();
          $$name->id = $category['id'];
          $$name->label = $category['bezeichnung'];
          $$name->checkbox = $withCheckBoxes;
          $$name->radio = false;

          $$name->inode = false;
          if(in_array($category['id'], $checkedIds)) {
            $$name->checked = true;
          }
          $categories[$categoryKey]['node'] = $$name;
          $categories[$categoryKey]['lvl'] = 1+$categories[$ind[$category['parent']]]['lvl'];
          if($categories[$categoryKey]['lvl'] > $maxLvl) {
            $maxLvl = $categories[$categoryKey]['lvl'];
          }
          $categories[$ind[$category['parent']]]['inode'] = true;
          $categories[$ind[$category['parent']]]['node']->open = true;
          $categories[$ind[$category['parent']]]['node']->branch[] = $$name;
        }
      }
    }

    return $tree;
  }

  /**
   * @param array $kategorien
   * @param int   $parent
   */
  public function getKategorien(&$kategorien, $parent)
  {
    $sql = 'SELECT id, bezeichnung, parent 
      FROM artikelkategorien 
      WHERE geloescht != 1 AND parent = '.(int)$parent.' '.$this->app->erp->ProjektRechte('projekt',true,'',[0]).'
      ORDER by bezeichnung';
    $res = $this->app->DB->SelectArr($sql);
    if($res) {
      foreach($res as $k => $v) {
        $kategorien[] = $v;
        $this->getKategorien($kategorien, $v['id']);
      }
    }
    if($parent == 0) {
      $kategorien[] = array('id'=>0,'bezeichnung'=> 'Neue Kategorie anlegen', 'parent'=> 0);
    }
  }
  
  public function ArtikelbaumChange()
  {

    $this->app->ExitXentral();
  }

  /**
   * @param int|array $categoryId
   * @param array     $categoryIdList
   * @param int       $lvl
   */
  public function getArticleSubtreeIds($categoryId, &$categoryIdList, $lvl = 0)
  {
    if($lvl > 20) {
      return;
    }
    if(!is_array($categoryId)) {
      $categoryId = [(int)$categoryId];
    }
    if(empty($categoryIdList)) {
      $categoryIdList = [];
    }
    $categoryIdList = array_merge($categoryIdList, $categoryId);

    $categories = $this->app->DB->SelectFirstCols(
      sprintf(
        'SELECT id FROM `artikelkategorien` WHERE parent IN (%s) ',
        implode(', ',$categoryId)
      )
    );
    if(empty($categories)) {
      return;
    }

    $this->getArticleSubtreeIds($categories, $categoryIdList, $lvl + 1);
  }
}
