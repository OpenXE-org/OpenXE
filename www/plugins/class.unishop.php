<?php

class unishop
{

  function __construct(&$app)
  {
    $this->app = &$app;
  }


  function CheckOK($auftrag)
  {
    //suche ausweise
    $adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$auftrag' LIMIT 1");

    //pruefen ob ausweise ok
    $check_ok = $this->app->DB->Select("SELECT kundenfreigabe FROM adresse WHERE id='$adresse' LIMIT 1");

    if($check_ok!=1)
      $error++;

    //pruefen ob kunde den artikel bereits bestellt hat

    if($this->MehrfachbestellungVorhanden($auftrag))
      $error++;

    if($error > 0)
      return false;
    else
      return true;
  }


  function CheckDisplay($parsetarget,$auftrag)
  {
    //suche ausweise
    $adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$auftrag' LIMIT 1");

    //pruefen ob ausweise ok
    $check_ok = $this->app->DB->Select("SELECT kundenfreigabe FROM adresse WHERE id='$adresse' LIMIT 1");

    if($check_ok!=1)
      $this->app->Tpl->Set($parsetarget,"Kundenfreigabe: <b>Studentenausweis oder Fragebogen fehlt noch!</b>");


    if($this->MehrfachbestellungVorhanden($auftrag))
      $this->app->Tpl->Add($parsetarget,"<br><br>Mehrfachbestellungs-Check: <b>Studentenausweis oder Fragebogen fehlt noch!</b>");
    else
      $this->app->Tpl->Add($parsetarget,"<br><br>Mehrfachbestellungs-Check: Erstbestellung</b>");

  }

  function MehrfachbestellungVorhanden($auftrag)
  {

    return false;
  }


}


?>
