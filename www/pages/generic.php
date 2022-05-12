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

class Generic  {
  var $app;
  
  function __construct(&$app) {
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler("edit","GenericEdit");
    $this->app->ActionHandler("reiter_up","GenericReiterUp");
    $this->app->ActionHandler("reiter_down","GenericReiterDown");
    $this->app->ActionHandler("reiter_delete","GenericReiterDelete");

    $this->app->ActionHandlerListen($app);

    $this->app->Tpl->Set('UEBERSCHRIFT',"Einstellungen");
    $this->app->Tpl->Set('FARBE',"[FARBE5]");
  }
	
	function GenericReiterDown() 
	{
		$id = $this->app->Secure->GetGET('id');

		if(is_numeric($id)) {
			$total = $this->app->DB->Select("SELECT MAX(position) FROM accordion");
	
			$curPos = $this->app->DB->Select("SELECT position FROM accordion WHERE id='$id' LIMIT 1");

			$nextPos = $curPos+1;
			if($nextPos <= $total){
				$cur = $this->app->DB->Select("Select id FROM accordion WHERE position=$curPos LIMIT 1");
				$next = $this->app->DB->Select("Select id FROM accordion WHERE position=$nextPos LIMIT 1");	

				$this->app->DB->Update("UPDATE accordion SET position=position+1 WHERE id='$cur' LIMIT 1");
				$this->app->DB->Update("UPDATE accordion SET position=position-1 WHERE id='$next' LIMIT 1");
			}	
		}

		header("Location: index.php?module=generic&action=edit");
    exit;
	}

	function GenericReiterUp()
  {
    $id = $this->app->Secure->GetGET('id');

    if(is_numeric($id)) {
      $curPos = $this->app->DB->Select("SELECT position FROM accordion WHERE id='$id' LIMIT 1");
      $nextPos = $curPos-1; 
      if($nextPos > 0){ 
        $cur = $this->app->DB->Select("Select id FROM accordion WHERE position=$curPos LIMIT 1");
        $next = $this->app->DB->Select("Select id FROM accordion WHERE position=$nextPos LIMIT 1");

        $this->app->DB->Update("UPDATE accordion SET position=position-1 WHERE id='$cur' LIMIT 1");
        $this->app->DB->Update("UPDATE accordion SET position=position+1 WHERE id='$next' LIMIT 1");
      } 
    }

    header("Location: index.php?module=generic&action=edit");
    exit;
  }
		

	function GenericReiterDelete() 
	{
		$id = $this->app->Secure->GetGET('id');
		if(is_numeric($id)) {
			$curPos = $this->app->DB->Select("SELECT position FROM accordion WHERE id='$id' LIMIT 1");
 			$this->app->DB->Delete("DELETE FROM accordion WHERE id='$id' LIMIT 1");
			$this->app->DB->Update("UPDATE accordion SET position=position-1 WHERE position>$curPos");
		}

		header("Location: index.php?module=generic&action=edit");
		exit;
	}

  function GenericEdit()
  {
    $this->app->Tpl->Add('KURZUEBERSCHRIFT',"Module");
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");

		$submit = $this->app->Secure->GetPOST('submitGeneric');

		/* ********************* Startseitenreiter ******************** */
		$new = $this->app->Secure->GetPOST('newReiter');
		if($new!='') {
			$max = $this->app->DB->Select("SELECT MAX(position) FROM accordion") + 1;
			$this->app->DB->Insert("INSERT INTO accordion (name, position) VALUES('NEU', '$max')");
		}
		
		if($submit!='') {
			$reiter = $this->app->Secure->GetPOST('startseitenreiter');
			foreach($reiter AS $key=>$value) {
				$this->app->DB->Update("UPDATE accordion SET name='{$value['name']}', target='{$value['target']}' WHERE id='$key' LIMIT 1");
			} 
		}

		$this->app->Tpl->Set('REITER', $this->StartseitenreiterTable());
		/* ****************** Startseitenreiter-Ende ****************** */

    $this->app->Tpl->Set('TABTEXT',"Moduleinstellungen");
    $this->app->Tpl->Parse('TAB1',"generic.tpl");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }

	function StartseitenreiterTable()
	{
		$data = $this->app->DB->SelectArr("SELECT * FROM accordion ORDER BY position ASC");

		$out = '';
		for($i=0;$i<count($data);$i++) {
			$color = (($i%2) ? '#e0e0e0' : '#fff');
			$out .= "<tr style=\"background-color:$color\">
								<td class=\"gentable\">{$data[$i]['position']}</td>
								<td class=\"gentable\"><input type=\"text\" name=\"startseitenreiter[{$data[$i]['id']}][name]\" value=\"{$data[$i]['name']}\" style=\"width:96%\"></td>

								<td class=\"gentable\"><input type=\"text\" name=\"startseitenreiter[{$data[$i]['id']}][target]\" value=\"{$data[$i]['target']}\" style=\"width:96%\"></td>
								<td><a href=\"./index.php?module=generic&action=reiter_up&id={$data[$i]['id']}\"><img src=\"./themes/new/images/down.png\"></a> 
										<a href=\"./index.php?module=generic&action=reiter_down&id={$data[$i]['id']}\"><img src=\"./themes/new/images/up.png\"></a> 
									  <a href=\"./index.php?module=generic&action=reiter_delete&id={$data[$i]['id']}\"><img src=\"./themes/new/images/delete.svg\"></a></td>
							 </tr>";
		}
		return $out;
	}
}

?>
